<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     * Demonstrates Eloquent Query Builder, eager loading with('category'),
     * search, category filtering, stock status filter, dynamic sorting, and pagination.
     */
    public function index(Request $request): View
    {
        $categories = Category::orderBy('name')->get();

        $query = Product::with('category')
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->input('search');
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('category_id'), function ($q) use ($request) {
                $q->where('category_id', $request->input('category_id'));
            })
            ->when($request->filled('status'), function ($q) use ($request) {
                $status = $request->input('status');
                if ($status === 'kritis') {
                    $q->whereColumn('stock', '<=', 'minimum_stock');
                } elseif ($status === 'aman') {
                    $q->whereColumn('stock', '>', 'minimum_stock');
                }
            });

        // Allowed sorting columns
        $sort = in_array($request->input('sort'), ['name', 'sku', 'stock', 'price', 'created_at'])
            ? $request->input('sort')
            : 'name';
        $order = strtoupper($request->input('order')) === 'DESC' ? 'desc' : 'asc';

        $products = $query->orderBy($sort, $order)
            ->paginate(10)
            ->withQueryString();

        return view('products.index', compact('products', 'categories', 'sort', 'order'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        return view('products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'sku' => ['required', 'string', 'max:50', 'unique:products,sku'],
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:200'],
            'unit' => ['required', 'string', 'max:30'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_public' => ['nullable', 'boolean'],
        ], [
            'sku.required' => 'SKU / Kode Produk wajib diisi.',
            'sku.unique' => 'SKU ini sudah digunakan oleh produk lain.',
            'category_id.required' => 'Kategori produk wajib dipilih.',
            'name.required' => 'Nama produk wajib diisi.',
            'unit.required' => 'Satuan produk wajib diisi.',
            'price.required' => 'Estimasi harga satuan wajib diisi.',
            'stock.required' => 'Stok awal wajib diisi.',
            'minimum_stock.required' => 'Batas stok minimum wajib diisi.',
        ]);

        $validated['is_public'] = $request->has('is_public');

        Product::create($validated);

        return redirect()->route('products.index')
            ->with('success', 'Produk logistik baru berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product): View
    {
        $product->load(['category', 'suppliers', 'stockMovements' => function ($q) {
            $q->with(['user', 'supplier', 'distributionTarget'])->latest('date')->take(10);
        }]);

        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product): View
    {
        $categories = Category::orderBy('name')->get();
        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'sku' => ['required', 'string', 'max:50', Rule::unique('products')->ignore($product->id)],
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:200'],
            'unit' => ['required', 'string', 'max:30'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_public' => ['nullable', 'boolean'],
        ], [
            'sku.required' => 'SKU / Kode Produk wajib diisi.',
            'sku.unique' => 'SKU ini sudah digunakan oleh produk lain.',
            'category_id.required' => 'Kategori produk wajib dipilih.',
            'name.required' => 'Nama produk wajib diisi.',
            'unit.required' => 'Satuan produk wajib diisi.',
            'price.required' => 'Estimasi harga satuan wajib diisi.',
            'stock.required' => 'Jumlah stok wajib diisi.',
            'minimum_stock.required' => 'Batas stok minimum wajib diisi.',
        ]);

        $validated['is_public'] = $request->has('is_public');

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', 'Data produk ' . $product->name . ' berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        if ($product->stockMovements()->exists()) {
            return redirect()->route('products.index')
                ->with('error', 'Produk tidak dapat dihapus karena telah memiliki riwayat transaksi/mutasi stok.');
        }

        $productName = $product->name;
        $product->suppliers()->detach();
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Produk ' . $productName . ' berhasil dihapus.');
    }
}
