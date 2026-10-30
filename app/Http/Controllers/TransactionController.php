<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with(['product', 'user'])
            ->latest()
            ->get();

        $products = Product::select('id', 'name', 'stock')->get();

        return Inertia::render('transactions/Index', [
            'transactions' => $transactions,
            'products' => $products,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:in,out',
            'quantity' => 'required|integer|min:1',
            'note' => 'nullable|string|max:255',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        // Validasi: Cegah stok menjadi minus jika barang keluar
        if ($validated['type'] === 'out' && $product->stock < $validated['quantity']) {
            return redirect()->back()->withErrors([
                'quantity' => "Stok produk tidak mencukupi! Stok saat ini: {$product->stock}",
            ]);
        }

        DB::transaction(function () use ($validated, $product, $request) {
            // 1. Simpan Transaksi
            Transaction::create([
                'product_id' => $validated['product_id'],
                'user_id' => $request->user()->id,
                'type' => $validated['type'],
                'quantity' => $validated['quantity'],
                'note' => $validated['note'] ?? null,
            ]);

            // 2. Auto-Update Stok Produk
            if ($validated['type'] === 'in') {
                $product->increment('stock', $validated['quantity']);
            } else {
                $product->decrement('stock', $validated['quantity']);
            }
        });

        return redirect()->back();
    }
}