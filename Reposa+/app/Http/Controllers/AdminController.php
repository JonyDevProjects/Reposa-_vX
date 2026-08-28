<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\Refund;
use App\Models\Category;
use App\Mail\OrderRefunded;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laravel\Cashier\Cashier;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalOrders = Order::count();
        $totalRevenue = Order::where('status', 'completed')->sum('total_amount');
        $totalProducts = Product::count();
        $recentOrders = Order::with('user')->latest()->take(5)->get();

        // Orders by status
        $ordersByStatus = Order::select('status', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        // Monthly sales for the last 6 months (Chart.js)
        $monthlySales = Order::where('status', 'completed')
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, SUM(total_amount) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $chartLabels = collect();
        $chartData = collect();
        for ($i = 5; $i >= 0; $i--) {
            $key = now()->subMonths($i)->format('Y-m');
            $chartLabels->push(now()->subMonths($i)->format('M Y'));
            $chartData->push((float) ($monthlySales[$key] ?? 0));
        }

        // Top selling products
        $topSellingProducts = \App\Models\OrderItem::select('product_id', \Illuminate\Support\Facades\DB::raw('SUM(quantity) as total_sold'))
            ->whereHas('order', fn($q) => $q->where('status', 'completed'))
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->with('product')
            ->take(5)
            ->get();

        // Top favorited products
        $topExpectedProducts = \App\Models\TopFavoritedProduct::orderBy('favorited_by_count', 'desc')
            ->where('favorited_by_count', '>', 0)
            ->take(5)
            ->get();

        // Recent completed orders for reference
        $recentCompleted = Order::where('status', 'completed')
            ->with('user')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalOrders', 'totalRevenue', 'totalProducts', 'recentOrders',
            'ordersByStatus', 'chartLabels', 'chartData',
            'topSellingProducts', 'topExpectedProducts', 'recentCompleted'
        ));
    }

    public function products()
    {
        $products = Product::with('categories')->paginate(10);
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
        $orders = Order::with('user', 'orderItems.product')->latest()->paginate(15);
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

    public function updateOrderStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', array_keys(Order::STATUSES)),
        ]);

        $newStatus = $request->status;

        if (! Order::canTransition($order->status, $newStatus)) {
            $current = Order::getStatusLabel($order->status);
            $target = Order::getStatusLabel($newStatus);
            return back()->with('error', "No se puede cambiar de \"{$current}\" a \"{$target}\".");
        }

        $order->update(['status' => $newStatus]);

        return back()->with('success', 'Estado del pedido actualizado a "' . Order::getStatusLabel($newStatus) . '".');
    }

    public function refundOrder(Request $request, Order $order)
    {
        if (! in_array($order->status, [Order::STATUS_COMPLETED, Order::STATUS_DELIVERED])) {
            return back()->with('error', 'Solo se pueden reembolsar pedidos completados o entregados.');
        }

        if (! $order->payment_intent_id) {
            return back()->with('error', 'Este pedido no tiene un pago de Stripe asociado para reembolsar.');
        }

        if ($order->refunds()->where('status', 'succeeded')->exists()) {
            return back()->with('error', 'Este pedido ya ha sido reembolsado.');
        }

        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        try {
            $stripeRefund = Cashier::stripe()->paymentIntents->refund(
                $order->payment_intent_id,
                []
            );

            $refund = Refund::create([
                'order_id' => $order->id,
                'amount' => $order->total_amount,
                'reason' => $request->input('reason', 'Reembolso solicitado por administrador'),
                'stripe_refund_id' => $stripeRefund->id,
                'status' => $stripeRefund->status,
            ]);

            if ($stripeRefund->status === 'succeeded') {
                DB::transaction(function () use ($order) {
                    foreach ($order->orderItems as $item) {
                        $item->product->increment('stock', $item->quantity);
                    }
                    $order->update(['status' => Order::STATUS_REFUNDED]);
                });
            }

            try {
                Mail::to($order->user->email)->send(new OrderRefunded($order, $refund));
            } catch (\Exception $e) {
                Log::error("Failed to send refund email for order {$order->id}", [
                    'error' => $e->getMessage(),
                ]);
            }

            return back()->with('success', 'Reembolso procesado correctamente. Importe: ' . number_format($order->total_amount, 2) . '€');
        } catch (\Exception $e) {
            Log::error("Refund failed for order {$order->id}", [
                'error' => $e->getMessage(),
            ]);
            return back()->with('error', 'Error al procesar el reembolso: ' . $e->getMessage());
        }
    }
}
