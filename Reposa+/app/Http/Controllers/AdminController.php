<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\Category;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalOrders = Order::count();
        $totalRevenue = Order::sum('total_amount');
        $totalProducts = Product::count();
        $recentOrders = Order::with('user')->latest()->take(5)->get();

        return view('admin.dashboard', compact('totalOrders', 'totalRevenue', 'totalProducts', 'recentOrders'));
    }

    public function products()
    {
        $products = Product::with('categories')->get();
        return view('admin.products.index', compact('products'));
    }

    public function createProduct()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function storeProduct(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image_url' => 'nullable|url'
        ]);

        Product::create($request->all());

        return redirect()->route('admin.products')->with('success', 'Producto creado correctamente.');
    }

    public function editProduct(Product $product)
    {
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function updateProduct(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image_url' => 'nullable|url'
        ]);

        $product->update($request->all());

        return redirect()->route('admin.products')->with('success', 'Producto actualizado correctamente.');
    }

    public function deleteProduct(Product $product)
    {
        $product->delete();
        return back()->with('success', 'Producto eliminado.');
    }

    public function orders()
    {
        $orders = Order::with('user', 'orderItems.product')->latest()->get();
        return view('admin.orders.index', compact('orders'));
    }
}
