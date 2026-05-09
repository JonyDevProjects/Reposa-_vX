<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Models\CartItem;

class MergeCartOnLogin
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $user = $event->user;
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
    }
}
