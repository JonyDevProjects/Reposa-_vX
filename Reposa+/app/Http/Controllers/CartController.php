<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use App\Mail\OrderConfirmed;
use Laravel\Cashier\Cashier;
use Dompdf\Dompdf;
use Dompdf\Options;

class CartController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            $cartItems = CartItem::where('user_id', Auth::id())->with('product')->get();
        } else {
            $sessionCart = session()->get('cart', []);
            $cartItems = collect($sessionCart)->map(function ($item, $productId) {
                return (object) [
                    'id' => $productId,
                    'product_id' => $productId,
                    'quantity' => $item['quantity'],
                    'product' => Product::find($productId)
                ];
            });
        }

        $total = $cartItems->sum(function($item) {
            return $item->product->price * $item->quantity;
        });

        return view('cart.index', compact('cartItems', 'total'));
    }

    public function add(Product $product)
    {
        $quantity = (int) request('quantity', 1);

        if ($product->stock <= 0) {
            if (request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => __('messages.cart.stock_unavailable')], 422);
            }
            return back()->with('error', __('messages.cart.stock_unavailable'));
        }

        if (Auth::check()) {
            $cartItem = CartItem::where('user_id', Auth::id())
                                ->where('product_id', $product->id)
                                ->first();

            $currentQty = $cartItem ? $cartItem->quantity : 0;
            if ($currentQty + $quantity > $product->stock) {
                $msg = __('messages.cart.insufficient_stock', ['available' => $product->stock, 'cart_qty' => $currentQty]);
                if (request()->wantsJson()) {
                    return response()->json(['success' => false, 'message' => $msg], 422);
                }
                return back()->with('error', $msg);
            }

            if ($cartItem) {
                $cartItem->increment('quantity', $quantity);
            } else {
                CartItem::create([
                    'user_id' => Auth::id(),
                    'product_id' => $product->id,
                    'quantity' => $quantity
                ]);
            }
        } else {
            $cart = session()->get('cart', []);
            $currentQty = isset($cart[$product->id]) ? $cart[$product->id]['quantity'] : 0;
            if ($currentQty + $quantity > $product->stock) {
                $msg = __('messages.cart.insufficient_stock', ['available' => $product->stock, 'cart_qty' => $currentQty]);
                return back()->with('error', $msg);
            }
            if (isset($cart[$product->id])) {
                $cart[$product->id]['quantity'] += $quantity;
            } else {
                $cart[$product->id] = [
                    'quantity' => $quantity
                ];
            }
            session()->put('cart', $cart);
        }

        if (request()->wantsJson()) {
            $cartCount = Auth::check() 
                ? \App\Models\CartItem::where('user_id', Auth::id())->sum('quantity')
                : collect(session()->get('cart', []))->sum('quantity');

            return response()->json([
                'success' => true,
                'message' => __('messages.cart.added'),
                'cartCount' => $cartCount
            ]);
        }

        return back()->with('success', __('messages.cart.added'));
    }

    public function update(Request $request, $id)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);

        if (Auth::check()) {
            $cartItem = CartItem::findOrFail($id);
            $product = Product::find($cartItem->product_id);

            if ($product && $request->quantity > $product->stock) {
                return back()->with('error', __('messages.cart.only_left', ['count' => $product->stock, 'name' => $product->name]));
            }

            $cartItem->update(['quantity' => $request->quantity]);
        } else {
            $cart = session()->get('cart', []);
            if (isset($cart[$id])) {
                $product = Product::find($id);
                if ($product && $request->quantity > $product->stock) {
                    return back()->with('error', __('messages.cart.only_left', ['count' => $product->stock, 'name' => $product->name]));
                }
                $cart[$id]['quantity'] = $request->quantity;
                session()->put('cart', $cart);
            }
        }

        return back()->with('success', __('messages.cart.updated'));
    }

    public function remove($id)
    {
        if (Auth::check()) {
            $cartItem = CartItem::findOrFail($id);
            $cartItem->delete();
        } else {
            $cart = session()->get('cart', []);
            if (isset($cart[$id])) {
                unset($cart[$id]);
                session()->put('cart', $cart);
            }
        }
        return back()->with('success', __('messages.cart.removed'));
    }

    public function checkout()
    {
        $user = Auth::user();
        
        // Sincronizar carrito de sesión si existe antes de proceder
        $sessionCart = session()->get('cart', []);
        if (!empty($sessionCart)) {
            foreach ($sessionCart as $productId => $item) {
                $cartItem = CartItem::where('user_id', $user->id)
                                    ->where('product_id', $productId)
                                    ->first();
                if ($cartItem) {
                    $cartItem->increment('quantity', $item['quantity']);
                } else {
                    CartItem::create([
                        'user_id' => $user->id,
                        'product_id' => $productId,
                        'quantity' => $item['quantity']
                    ]);
                }
            }
            session()->forget('cart');
        }

        $cartItems = CartItem::where('user_id', $user->id)->with('product')->get();

        if ($cartItems->isEmpty()) {
            return back()->with('error', __('messages.cart.empty'));
        }

        $total = $cartItems->sum(function($item) {
            return $item->product->price * $item->quantity;
        });

        // Crear el pedido dentro de una transacción
        try {
            $order = DB::transaction(function () use ($user, $total, $cartItems) {
                $order = Order::create([
                    'user_id' => $user->id,
                    'total_amount' => $total,
                    'status' => 'pending'
                ]);

                // Mover items del carrito a order_items, verificando y decrementando stock
                foreach ($cartItems as $item) {
                    $product = Product::lockForUpdate()->find($item->product_id);

                    if ($product->stock < $item->quantity) {
                        throw new \Exception(__('messages.cart.stock_insufficient', ['name' => $product->name, 'available' => $product->stock, 'requested' => $item->quantity]));
                    }

                    $product->decrement('stock', $item->quantity);

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'price_at_purchase' => $item->product->price
                    ]);
                    $item->delete(); // Vaciar el carrito
                }

                return $order;
            });
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        // Enviar el correo de confirmación
        try {
            Mail::to($user->email)->send(new OrderConfirmed($order));
        } catch (\Exception $e) {
            // Si falla el envío del correo, el pedido sigue siendo válido
            return redirect('/profile#orders')->with('success', __('messages.cart.order_success_no_email'));
        }

        return redirect('/profile#orders')->with('success', __('messages.cart.order_success'));
    }

    public function orders()
    {
        return redirect('/profile#orders');
    }

    public function requireLogin()
    {
        session()->put('url.intended', route('cart.index'));
        return redirect()->route('login');
    }

    public function showOrder(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->load('orderItems.product');

        return view('orders.show', compact('order'));
    }

    public function stripeCheckout()
    {
        $user = Auth::user();

        // Sincronizar carrito de sesión si existe
        $sessionCart = session()->get('cart', []);
        if (!empty($sessionCart)) {
            foreach ($sessionCart as $productId => $item) {
                $cartItem = CartItem::where('user_id', $user->id)
                                    ->where('product_id', $productId)
                                    ->first();
                if ($cartItem) {
                    $cartItem->increment('quantity', $item['quantity']);
                } else {
                    CartItem::create([
                        'user_id' => $user->id,
                        'product_id' => $productId,
                        'quantity' => $item['quantity']
                    ]);
                }
            }
            session()->forget('cart');
        }

        $cartItems = CartItem::where('user_id', $user->id)->with('product')->get();

        if ($cartItems->isEmpty()) {
            return back()->with('error', __('messages.cart.empty'));
        }

        // Verify stock availability before creating the order
        try {
            $order = DB::transaction(function () use ($user, $cartItems) {
                foreach ($cartItems as $item) {
                    $product = Product::lockForUpdate()->find($item->product_id);

                    if (! $product || $product->stock < $item->quantity) {
                        $name = $product?->name ?? __('messages.admin.products.name') . ' #' . $item->product_id;
                        $available = $product?->stock ?? 0;
                        throw new \Exception(
                            __('messages.cart.stock_insufficient', ['name' => $name, 'available' => $available, 'requested' => $item->quantity])
                        );
                    }
                }

                $order = Order::create([
                    'user_id' => $user->id,
                    'total_amount' => $cartItems->sum(fn($item) => $item->product->price * $item->quantity),
                    'status' => 'pending',
                ]);

                foreach ($cartItems as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'price_at_purchase' => $item->product->price,
                    ]);
                }

                return $order;
            });
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        // Construir line items para Stripe Checkout
        $lineItems = $order->orderItems->map(function ($item) {
            return [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => (int) ($item->price_at_purchase * 100),
                    'product_data' => [
                        'name' => $item->product->name,
                    ],
                ],
                'quantity' => $item->quantity,
            ];
        })->toArray();

        // Crear cliente Stripe si no existe
        $stripeCustomer = $user->createOrGetStripeCustomer();

        // Crear sesión de Checkout usando la API de Stripe directamente
        $session = $user->stripe()->checkout->sessions->create([
            'customer' => $stripeCustomer->id,
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => route('stripe.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('stripe.cancel'),
            'metadata' => ['order_id' => $order->id],
            'managed_payments' => ['enabled' => false],
        ]);

        $order->update([
            'stripe_session_id' => $session->id,
            'payment_intent_id' => $session->payment_intent,
        ]);

        return redirect($session->url);
    }

    public function stripeSuccess(Request $request)
    {
        $sessionId = $request->get('session_id');

        if ($sessionId === null) {
            return redirect('/')->with('error', __('messages.cart.session_not_found'));
        }

        $session = Cashier::stripe()->checkout->sessions->retrieve($sessionId);

        if ($session->payment_status !== 'paid') {
            return redirect('/profile#orders')->with('error', __('messages.cart.payment_not_completed'));
        }

        $orderId = $session->metadata['order_id'] ?? null;
        $order = Order::findOrFail($orderId);

        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        if ($order->status === 'pending') {
            DB::transaction(function () use ($order) {
                foreach ($order->orderItems as $item) {
                    $product = Product::lockForUpdate()->find($item->product_id);
                    $product->decrement('stock', $item->quantity);
                }
                $order->update(['status' => 'completed']);
            });

            // Vaciar carrito solo después de confirmar el pago
            CartItem::where('user_id', auth()->id())->delete();

            // Enviar correo de confirmación
            try {
                Mail::to($order->user->email)->send(new OrderConfirmed($order));
            } catch (\Exception $e) {
                // El pago ya está registrado aunque falle el correo
            }
        }

        return redirect('/profile#orders')->with('success', __('messages.cart.payment_success'));
    }

    public function stripeCancel()
    {
        // Cancelar la orden pending más reciente del usuario
        $order = Order::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->whereNotNull('stripe_session_id')
            ->latest()
            ->first();

        if ($order) {
            $order->update(['status' => 'cancelled']);
        }

        return redirect('/cart')->with('error', __('messages.cart.payment_cancelled'));
    }

    public function downloadInvoice(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->load(['orderItems.product', 'user.addresses']);

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml(view('invoices.invoice', compact('order'))->render());
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = __('messages.invoice.title') . '_Reposa+' . '_' . str_pad($order->id, 6, '0', STR_PAD_LEFT) . '.pdf';

        $pdf = $dompdf->output();

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Content-Length' => strlen($pdf),
        ]);
    }
}
