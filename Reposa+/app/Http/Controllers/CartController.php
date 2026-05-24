<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderConfirmed;

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

        // Crear el pedido
        $order = Order::create([
            'user_id' => $user->id,
            'total_amount' => $total,
            'status' => 'pending'
        ]);

        // Mover items del carrito a order_items
        foreach ($cartItems as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price_at_purchase' => $item->product->price
            ]);
            $item->delete(); // Vaciar el carrito
        }

        // Enviar el correo de confirmación
        Mail::to($user->email)->send(new OrderConfirmed($order));

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
}
