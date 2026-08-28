<?php

namespace App\Http\Controllers;

use App\Mail\OrderConfirmed;
use App\Mail\OrderRefunded;
use App\Models\Order;
use App\Models\Product;
use App\Models\Refund;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierController;
use Symfony\Component\HttpFoundation\Response;

class StripeWebhookController extends CashierController
{
    /**
     * Handle the checkout.session.completed event.
     * Acts as a safety net — if the user closes the browser before the
     * success redirect fires, this webhook confirms the payment.
     */
    protected function handleCheckoutSessionCompleted(array $payload): Response
    {
        $session = $payload['data']['object'];
        $orderId = $session['metadata']['order_id'] ?? null;

        if (! $orderId) {
            Log::warning('Stripe webhook: checkout.session.completed without order_id in metadata');
            return $this->successMethod();
        }

        $order = Order::find($orderId);

        if (! $order) {
            Log::warning("Stripe webhook: order {$orderId} not found");
            return $this->successMethod();
        }

        if ($order->status === 'completed') {
            Log::info("Stripe webhook: order {$orderId} already completed, skipping");
            return $this->successMethod();
        }

        if ($session['payment_status'] !== 'paid') {
            Log::info("Stripe webhook: order {$orderId} payment_status is '{$session['payment_status']}', skipping");
            return $this->successMethod();
        }

        DB::transaction(function () use ($order) {
            foreach ($order->orderItems as $item) {
                $product = Product::lockForUpdate()->find($item->product_id);

                if ($product && $product->stock >= $item->quantity) {
                    $product->decrement('stock', $item->quantity);
                } else {
                    Log::warning("Stripe webhook: insufficient stock for product {$item->product_id} in order {$order->id}");
                }
            }

            $order->update(['status' => 'completed']);
        });

        // Send confirmation email if not already sent
        try {
            Mail::to($order->user->email)->send(new OrderConfirmed($order));
        } catch (\Exception $e) {
            Log::error("Stripe webhook: failed to send confirmation email for order {$order->id}", [
                'error' => $e->getMessage(),
            ]);
        }

        Log::info("Stripe webhook: order {$order->id} confirmed via webhook");

        return $this->successMethod();
    }

    /**
     * Handle the payment_intent.payment_failed event.
     */
    protected function handlePaymentIntentPaymentFailed(array $payload): Response
    {
        $paymentIntent = $payload['data']['object'];
        $customerId = $paymentIntent['customer'] ?? null;

        Log::warning('Stripe webhook: payment_intent.payment_failed', [
            'payment_intent_id' => $paymentIntent['id'],
            'customer' => $customerId,
            'error' => $paymentIntent['last_payment_error']['message'] ?? 'Unknown error',
        ]);

        if ($customerId) {
            $user = $this->getUserByStripeId($customerId);

            if ($user) {
                // Find the most recent pending order for this user
                $order = Order::where('user_id', $user->id)
                    ->where('status', 'pending')
                    ->latest()
                    ->first();

                if ($order) {
                    try {
                        Mail::to($user->email)->send(
                            new \App\Mail\PaymentFailed($order, $paymentIntent['last_payment_error']['message'] ?? 'El pago no pudo ser procesado')
                        );
                    } catch (\Exception $e) {
                        Log::error("Stripe webhook: failed to send payment failure email for order {$order->id}", [
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            }
        }

        return $this->successMethod();
    }

    /**
     * Handle the charge.refunded event.
     * Safety net — if the refund is initiated from Stripe dashboard or
     * the admin action webhook didn't complete, this syncs the state.
     */
    protected function handleChargeRefunded(array $payload): Response
    {
        $charge = $payload['data']['object'];
        $paymentIntentId = $charge['payment_intent'] ?? null;

        if (! $paymentIntentId) {
            Log::warning('Stripe webhook: charge.refunded without payment_intent');
            return $this->successMethod();
        }

        $order = Order::where('payment_intent_id', $paymentIntentId)->first();

        if (! $order) {
            Log::warning("Stripe webhook: charge.refunded — no order found for payment_intent {$paymentIntentId}");
            return $this->successMethod();
        }

        if ($order->status === Order::STATUS_REFUNDED) {
            Log::info("Stripe webhook: order {$order->id} already refunded, skipping");
            return $this->successMethod();
        }

        $refundId = $charge['id'] ?? null;

        if ($refundId && ! $order->refunds()->where('stripe_refund_id', $refundId)->exists()) {
            Refund::create([
                'order_id' => $order->id,
                'amount' => ($charge['amount_refunded'] ?? $order->total_amount) / 100,
                'reason' => 'Reembolsado vía Stripe dashboard',
                'stripe_refund_id' => $refundId,
                'status' => 'succeeded',
            ]);
        }

        if ($order->status !== Order::STATUS_REFUNDED) {
            DB::transaction(function () use ($order) {
                foreach ($order->orderItems as $item) {
                    $item->product->increment('stock', $item->quantity);
                }
                $order->update(['status' => Order::STATUS_REFUNDED]);
            });

            try {
                $refund = $order->refunds()->latest()->first();
                if ($refund) {
                    Mail::to($order->user->email)->send(new OrderRefunded($order, $refund));
                }
            } catch (\Exception $e) {
                Log::error("Stripe webhook: failed to send refund email for order {$order->id}", [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info("Stripe webhook: order {$order->id} refunded via charge.refunded webhook");

        return $this->successMethod();
    }
}
