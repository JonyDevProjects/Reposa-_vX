<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $user->load(['profile', 'addresses', 'orders', 'orderSummary', 'favorites.categories']);
        
        return view('profile.index', compact('user'));
    }

    public function toggleFavorite(Product $product)
    {
        $user = auth()->user();

        if ($user->favorites()->where('product_id', $product->id)->exists()) {
            $user->favorites()->detach($product->id);
            $isFavorite = false;
        } else {
            $user->favorites()->attach($product->id);
            $isFavorite = true;
        }

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'is_favorite' => $isFavorite,
            ]);
        }

        return back()->with('success', $isFavorite
            ? 'Producto añadido a tus favoritos.'
            : 'Producto eliminado de tus favoritos.');
    }

    public function removeFavorite(Product $product)
    {
        $user = auth()->user();
        $user->favorites()->detach($product->id);

        return redirect('/profile#favorites')->with('success', 'Producto eliminado de tus favoritos.');
    }

    public function storeAddress(Request $request)
    {
        $validated = $request->validate([
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'zip_code' => 'required|string|max:20',
            'is_main' => 'boolean',
        ]);

        auth()->user()->addresses()->create($validated);

        return back()->with('success', 'Dirección añadida correctamente.');
    }

    public function destroyAddress(\App\Models\Address $address)
    {
        if ($address->user_id !== auth()->id()) {
            abort(403);
        }

        $address->delete();

        return back()->with('success', 'Dirección eliminada.');
    }

    public function updateAddress(Request $request, \App\Models\Address $address)
    {
        if ($address->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'zip_code' => 'required|string|max:20',
            'is_main' => 'boolean',
        ]);

        $address->update($validated);

        return back()->with('success', 'Dirección actualizada correctamente.');
    }
}
