<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        // Query Produk dengan Pencarian (Nama Produk atau Nama Kategori)
        $products = Product::with('category')
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhereHas('category', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
            ->latest()
            ->get();

        $categories = Category::all();

        // Data Produk dengan Stok Menipis (<= 5) untuk Notifikasi
        $lowStockProducts = Product::with('category')
            ->where('stock', '<=', 5)
            ->get();

        $stats = [
            'total_products' => Product::count(),
            'total_stock' => (int) Product::sum('stock'),
            'total_in' => (int) Transaction::where('type', 'in')->sum('quantity'),
            'low_stock_count' => $lowStockProducts->count(),
        ];

        return Inertia::render('products/Index', [
            'products' => $products,
            'categories' => $categories,
            'stats' => $stats,
            'filters' => [
                'search' => $search,
            ],
            'lowStockProducts' => $lowStockProducts,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
        ]);

        Product::create($validated);

        return redirect()->back();
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
        ]);

        $product->update($validated);

        return redirect()->back();
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->back();
    }
}