<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $locale = app()->getLocale();
        $query = Product::query();

        // Search by name, description, materials, firmness and postural/anatomical terms
        if ($request->filled('q')) {
            $search = trim($request->input('q'));
            $lowerSearch = mb_strtolower($search);

            $posturalSynonyms = [
                // Posturas de descanso
                'lado' => ['Media-Alta', 'Alta', 'cervical', 'Ergonómica', 'Medium-High', 'High'],
                'costado' => ['Media-Alta', 'Alta', 'cervical', 'Ergonómica'],
                'side' => ['Medium-High', 'High', 'cervical', 'Ergonomic'],
                'boca arriba' => ['Media', 'Media-Suave', 'lordosis', 'Medium', 'Medium-Soft'],
                'espalda' => ['Media', 'Media-Suave', 'columna', 'Medium', 'Medium-Soft'],
                'back' => ['Medium', 'Medium-Soft'],
                'boca abajo' => ['Suave', 'Soft', 'baja', 'plumas'],
                'estomago' => ['Suave', 'Soft', 'plumas'],
                'estómago' => ['Suave', 'Soft', 'plumas'],
                'stomach' => ['Soft'],

                // Zona cervical y soporte anatómico
                'cuello' => ['cervical', 'ergonómica', 'Alta', 'Media-Alta', 'viaje'],
                'cervical' => ['cervical', 'ergonómica', 'alta'],
                'cervicales' => ['cervical', 'ergonómica', 'alta'],
                'neck' => ['cervical', 'ergonomic', 'travel'],
                'nuca' => ['cervical', 'ergonómica'],
                'hombro' => ['Media-Alta', 'Alta', 'cervical'],
                'hombros' => ['Media-Alta', 'Alta', 'cervical'],

                // Grados y descripciones de firmeza
                'firme' => ['Alta', 'Media-Alta', 'High', 'Medium-High'],
                'firmeza' => ['Alta', 'Media-Alta'],
                'dura' => ['Alta', 'Media-Alta'],
                'duro' => ['Alta', 'Media-Alta'],
                'firm' => ['High', 'Medium-High'],
                'hard' => ['High'],
                'suave' => ['Suave', 'Media-Suave', 'Soft', 'Medium-Soft'],
                'blanda' => ['Suave', 'Media-Suave'],
                'blando' => ['Suave', 'Media-Suave'],
                'soft' => ['Soft', 'Medium-Soft'],
                'media' => ['Media', 'Media-Alta', 'Media-Suave', 'Medium'],
                'medio' => ['Media', 'Medium'],
                'medium' => ['Medium', 'Medium-High', 'Medium-Soft'],

                // Materiales, termorregulación y salud
                'visco' => ['viscoelástica', 'memoria', 'memory foam'],
                'viscoelastica' => ['viscoelástica', 'memoria'],
                'latex' => ['látex', 'latex'],
                'pluma' => ['plumas', 'plumón'],
                'plumas' => ['plumas', 'plumón'],
                'fresca' => ['gel', 'refrescante', 'térmica', 'bambú'],
                'fresco' => ['gel', 'refrescante', 'térmica', 'bambú'],
                'calor' => ['gel', 'refrescante', 'térmica', 'transpirable'],
                'transpirable' => ['bambú', 'gel', 'microfibra'],
                'alergia' => ['antiácaros', 'hipoalergénica'],
                'antiacaros' => ['antiácaros', 'hipoalergénica'],
            ];

            // Colectar términos semánticos y posturales expandidos
            $expandedTerms = [];
            foreach ($posturalSynonyms as $trigger => $terms) {
                if (str_contains($lowerSearch, $trigger)) {
                    foreach ($terms as $t) {
                        $expandedTerms[] = $t;
                    }
                }
            }
            $expandedTerms = array_unique($expandedTerms);

            // Palabras clave individuales significativas (omitiendo artículos y stop words comunes)
            $stopWords = ['para', 'con', 'las', 'los', 'una', 'uno', 'del', 'que', 'the', 'and', 'for', 'with', 'almohada', 'pillow'];
            $keywords = array_filter(
                preg_split('/\s+/', $lowerSearch),
                fn ($word) => mb_strlen($word) >= 3 && ! in_array($word, $stopWords)
            );

            $query->where(function ($q) use ($lowerSearch, $locale, $keywords, $expandedTerms) {
                // 1. Coincidencia directa de la cadena completa en atributos clave y categorías (insensible a mayúsculas)
                $q->whereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(name, '$.{$locale}'))) LIKE ?", ["%{$lowerSearch}%"])
                    ->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(description, '$.{$locale}'))) LIKE ?", ["%{$lowerSearch}%"])
                    ->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(material, '$.{$locale}'))) LIKE ?", ["%{$lowerSearch}%"])
                    ->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(firmness, '$.{$locale}'))) LIKE ?", ["%{$lowerSearch}%"])
                    ->orWhereHas('categories', function ($catQ) use ($lowerSearch, $locale) {
                        $catQ->whereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(name, '$.{$locale}'))) LIKE ?", ["%{$lowerSearch}%"])
                            ->orWhere('slug', 'LIKE', "%{$lowerSearch}%");
                    });

                // 2. Coincidencia por palabras individuales
                foreach ($keywords as $word) {
                    $lowerWord = mb_strtolower($word);
                    $q->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(name, '$.{$locale}'))) LIKE ?", ["%{$lowerWord}%"])
                        ->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(description, '$.{$locale}'))) LIKE ?", ["%{$lowerWord}%"])
                        ->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(material, '$.{$locale}'))) LIKE ?", ["%{$lowerWord}%"])
                        ->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(firmness, '$.{$locale}'))) LIKE ?", ["%{$lowerWord}%"])
                        ->orWhereHas('categories', function ($catQ) use ($lowerWord, $locale) {
                            $catQ->whereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(name, '$.{$locale}'))) LIKE ?", ["%{$lowerWord}%"])
                                ->orWhere('slug', 'LIKE', "%{$lowerWord}%");
                        });
                }

                // 3. Coincidencia por términos posturales / biomecánicos inferidos
                foreach ($expandedTerms as $term) {
                    $lowerTerm = mb_strtolower($term);
                    $q->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(name, '$.{$locale}'))) LIKE ?", ["%{$lowerTerm}%"])
                        ->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(description, '$.{$locale}'))) LIKE ?", ["%{$lowerTerm}%"])
                        ->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(material, '$.{$locale}'))) LIKE ?", ["%{$lowerTerm}%"])
                        ->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(firmness, '$.{$locale}'))) LIKE ?", ["%{$lowerTerm}%"])
                        ->orWhereHas('categories', function ($catQ) use ($lowerTerm, $locale) {
                            $catQ->whereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(name, '$.{$locale}'))) LIKE ?", ["%{$lowerTerm}%"]);
                        });
                }
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filter by material (translatable, case-insensitive)
        if ($request->filled('material')) {
            $query->whereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(material, '$.{$locale}'))) = ?", [mb_strtolower($request->material)]);
        }

        // Filter by firmness (translatable, case-insensitive)
        if ($request->filled('firmness')) {
            $query->whereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(firmness, '$.{$locale}'))) = ?", [mb_strtolower($request->firmness)]);
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
