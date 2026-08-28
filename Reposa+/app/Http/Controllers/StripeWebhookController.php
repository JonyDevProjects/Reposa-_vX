<?php

namespace App\Http\Controllers;

use App\Mail\OrderConfirmed;
use App\Models\Order;
use App\Models\Product;
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
}
