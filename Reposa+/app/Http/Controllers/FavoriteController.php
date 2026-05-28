<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * Toggle favorite status of a product for the authenticated user.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggle(Product $product)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => __('messages.favorites.login_required') ?? 'Debes iniciar sesión para añadir a favoritos.',
                'redirect' => route('login')
            ], 401);
        }

        $user = Auth::user();
        
        // toggle returns an array with 'attached' and 'detached' containing IDs
        $result = $user->favorites()->toggle($product->id);
        $isFavorite = count($result['attached']) > 0;

        return response()->json([
            'success' => true,
            'is_favorite' => $isFavorite,
            'message' => $isFavorite 
                ? (__('messages.favorites.added') ?? 'Añadido a favoritos.')
                : (__('messages.favorites.removed') ?? 'Eliminado de favoritos.')
        ]);
    }
}
