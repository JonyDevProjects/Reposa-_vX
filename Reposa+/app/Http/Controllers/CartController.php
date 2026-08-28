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
use App\Mail\OrderConfirmed;
use Laravel\Cashier\Cashier;

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

        if (Auth::check()) {
            $cartItem = CartItem::where('user_id', Auth::id())
                                ->where('product_id', $product->id)
                                ->first();

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
                'message' => 'Producto añadido al carrito',
                'cartCount' => $cartCount
            ]);
        }

        return back()->with('success', 'Producto añadido al carrito');
    }

    public function update(Request $request, $id)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);

        if (Auth::check()) {
            $cartItem = CartItem::findOrFail($id);
            $cartItem->update(['quantity' => $request->quantity]);
        } else {
            $cart = session()->get('cart', []);
            if (isset($cart[$id])) {
                $cart[$id]['quantity'] = $request->quantity;
                session()->put('cart', $cart);
            }
        }

        return back()->with('success', 'Carrito actualizado');
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
        return back()->with('success', 'Producto eliminado del carrito');
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
            return back()->with('error', 'El carrito está vacío');
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
                        throw new \Exception("Stock insuficiente para \"{$product->name}\". Disponible: {$product->stock}, solicitado: {$item->quantity}.");
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
            return redirect('/profile#orders')->with('success', '¡Pedido realizado con éxito! No se pudo enviar el correo de confirmación, pero tu pedido ha sido procesado.');
        }

        return redirect('/profile#orders')->with('success', '¡Pedido realizado con éxito! Se ha enviado un ticket a tu correo.');
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
            return back()->with('error', 'El carrito está vacío');
        }

        // Crear order pendiente con los items del carrito
        $order = Order::create([
            'user_id' => $user->id,
            'total_amount' => $cartItems->sum(fn($item) => $item->product->price * $item->quantity),
            'status' => 'pending',
        ]);

        // Crear order items (sin decrementar stock aún — se hará al confirmar pago)
        foreach ($cartItems as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price_at_purchase' => $item->product->price,
            ]);
        }

        // Vaciar carrito
        CartItem::where('user_id', $user->id)->delete();

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

        $order->update(['stripe_session_id' => $session->id]);

        return redirect($session->url);
    }

    public function stripeSuccess(Request $request)
    {
        $sessionId = $request->get('session_id');

        if ($sessionId === null) {
            return redirect('/')->with('error', 'No se encontró la sesión de pago.');
        }

        $session = Cashier::stripe()->checkout->sessions->retrieve($sessionId);

        if ($session->payment_status !== 'paid') {
            return redirect('/profile#orders')->with('error', 'El pago no fue completado.');
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

            // Enviar correo de confirmación
            try {
                Mail::to($order->user->email)->send(new OrderConfirmed($order));
            } catch (\Exception $e) {
                // El pago ya está registrado aunque falle el correo
            }
        }

        return redirect('/profile#orders')->with('success', '¡Pago realizado con éxito! Tu pedido ha sido confirmado.');
    }

    public function stripeCancel()
    {
        return redirect('/cart')->with('error', 'El pago fue cancelado. Tu carrito se mantiene intacto.');
    }
}
