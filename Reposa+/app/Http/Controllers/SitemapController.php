<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Genera el mapa del sitio XML conforme a la especificación estándar sitemaps.org 0.9.
     */
    public function index(): Response
    {
        $products = Product::orderBy('id', 'asc')->get();

        return response()
            ->view('sitemap', [
                'products' => $products,
            ])
            ->header('Content-Type', 'text/xml; charset=utf-8');
    }
}
