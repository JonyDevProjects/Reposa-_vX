<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\Category;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalOrders = Order::count();
        $totalRevenue = Order::sum('total_amount');
        $totalProducts = Product::count();
        $recentOrders = Order::with('user')->latest()->take(5)->get();
        
        // Analítica de demanda: Top Almohadas con mayores expectativas de compra
        $topExpectedProducts = Product::withCount('favoritedBy')
                                      ->having('favorited_by_count', '>', 0)
                                      ->orderBy('favorited_by_count', 'desc')
                                      ->take(5)
                                      ->get();

        return view('admin.dashboard', compact('totalOrders', 'totalRevenue', 'totalProducts', 'recentOrders', 'topExpectedProducts'));
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
            'image_url' => 'nullable|url',
            'categories' => 'array',
            'categories.*' => 'exists:categories,id'
        ]);

        $product = Product::create($request->except('categories'));
        
        if ($request->has('categories')) {
            $product->categories()->attach($request->categories);
        }

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
            'image_url' => 'nullable|url',
            'categories' => 'array',
            'categories.*' => 'exists:categories,id'
        ]);

        $product->update($request->except('categories'));
        
        if ($request->has('categories')) {
            $product->categories()->sync($request->categories);
        } else {
            $product->categories()->detach();
        }

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

    // Gestión de Categorías
    public function categories()
    {
        $categories = Category::withCount('products')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function createCategory()
    {
        return view('admin.categories.create');
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()->route('admin.categories')->with('success', 'Categoría creada correctamente.');
    }

    public function editCategory(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function updateCategory(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()->route('admin.categories')->with('success', 'Categoría actualizada correctamente.');
    }

    public function deleteCategory(Category $category)
    {
        $category->delete();
        return back()->with('success', 'Categoría eliminada.');
    }
}
