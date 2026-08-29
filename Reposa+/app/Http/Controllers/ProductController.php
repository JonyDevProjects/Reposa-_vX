<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $locale = app()->getLocale();
        $query = Product::query();

        // Search by name and description (translatable JSON columns)
        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search, $locale) {
                $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.{$locale}')) LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(description, '$.{$locale}')) LIKE ?", ["%{$search}%"]);
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filter by material (translatable)
        if ($request->filled('material')) {
            $query->whereRaw("JSON_EXTRACT(material, '$.{$locale}') = ?", [$request->material]);
        }

        // Filter by firmness (translatable)
        if ($request->filled('firmness')) {
            $query->whereRaw("JSON_EXTRACT(firmness, '$.{$locale}') = ?", [$request->firmness]);
        }

        // Filter by price range
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->input('min_price', 0));
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->input('max_price', 999));
        }

        // Sorting
        $sort = $request->input('sort', 'newest');
        match ($sort) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'name_asc' => $query->orderByRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.{$locale}')) ASC"),
            'name_desc' => $query->orderByRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.{$locale}')) DESC"),
            default => $query->latest(),
        };

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::all();
        $favoriteIds = auth()->check() ? auth()->user()->favorites()->pluck('product_id')->toArray() : [];

        // Get distinct values for filter dropdowns (from current locale)
        $materials = Product::selectRaw("JSON_UNQUOTE(JSON_EXTRACT(material, '$.{$locale}')) as material")
            ->distinct()->pluck('material')->filter()->sort()->values();
        $firmnesses = Product::selectRaw("JSON_UNQUOTE(JSON_EXTRACT(firmness, '$.{$locale}')) as firmness")
            ->distinct()->pluck('firmness')->filter()->sort()->values();

        return view('catalog.index', compact(
            'products', 'categories', 'favoriteIds',
            'materials', 'firmnesses'
        ));
    }

    public function show(Product $product)
    {
        $isFavorite = auth()->check() && auth()->user()->favorites()->where('product_id', $product->id)->exists();

        return view('catalog.show', compact('product', 'isFavorite'));
    }
}
