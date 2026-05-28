<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\Product::query();

        if ($request->has('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        $products = $query->paginate(12);
        $categories = \App\Models\Category::all();
        $favoriteIds = auth()->check() ? auth()->user()->favorites()->pluck('product_id')->toArray() : [];

        return view('catalog.index', compact('products', 'categories', 'favoriteIds'));
    }

    public function show(\App\Models\Product $product)
    {
        $isFavorite = auth()->check() && auth()->user()->favorites()->where('product_id', $product->id)->exists();

        return view('catalog.show', compact('product', 'isFavorite'));
    }
}
