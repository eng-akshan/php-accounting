<?php

namespace App\Http\Controllers;

use App\Models\InventoryTransaction;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with('creator')
            ->when($request->search, function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('sku', 'like', "%{$request->search}%");
            })
            ->when($request->category, function($q) use ($request) {
                $q->where('category', $request->category);
            })
            ->orderBy('name')
            ->paginate(15);

        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'unit' => 'nullable|string|max:20',
            'price' => 'required|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'quantity' => 'nullable|integer|min:0',
            'min_stock' => 'nullable|integer|min:0',
        ]);

        $data['created_by'] = auth()->id();
        $data['quantity'] = $data['quantity'] ?? 0;
        $data['cost'] = $data['cost'] ?? 0;
        $data['is_active'] = true;

        $product = Product::create($data);

        if ($data['quantity'] > 0) {
            InventoryTransaction::create([
                'product_id' => $product->id,
                'type' => 'in',
                'quantity' => $data['quantity'],
                'description' => 'Initial stock',
                'date' => now(),
                'created_by' => auth()->id(),
            ]);
        }

        return redirect()->route('products.index')->with('success', 'Product created successfully');
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku,' . $product->id,
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'unit' => 'nullable|string|max:20',
            'price' => 'required|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Product updated successfully');
    }

    public function destroy(Product $product)
    {
        if ($product->invoiceItems()->exists()) {
            return back()->with('error', 'Cannot delete product with invoice records');
        }

        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted');
    }

    public function addStock(Request $request, Product $product)
    {
        $data = $request->validate([
            'quantity' => 'required|integer|min:1',
            'description' => 'nullable|string',
        ]);

        $product->addStock($data['quantity']);

        InventoryTransaction::create([
            'product_id' => $product->id,
            'type' => 'in',
            'quantity' => $data['quantity'],
            'description' => $data['description'] ?? 'Manual stock addition',
            'date' => now(),
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'Stock added successfully');
    }

    public function stockMovement(Product $product)
    {
        $transactions = $product->transactions()->orderByDesc('date')->paginate(20);
        return view('products.stock-movement', compact('product', 'transactions'));
    }
}