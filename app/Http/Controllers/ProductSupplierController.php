<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProductSupplierController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();

        $query = DB::table('product_suppliers')
            ->join('products', 'product_suppliers.product_id', '=', 'products.id')
            ->join('suppliers', 'product_suppliers.supplier_id', '=', 'suppliers.id')
            ->select(
                'product_suppliers.*',
                'products.name as product_name',
                'products.sku as product_sku',
                'products.unit as product_unit',
                'suppliers.name as supplier_name',
                'suppliers.type as supplier_type'
            );

        if ($request->filled('product_id')) {
            $query->where('product_suppliers.product_id', $request->input('product_id'));
        }

        if ($request->filled('supplier_id')) {
            $query->where('product_suppliers.supplier_id', $request->input('supplier_id'));
        }

        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where(function ($q) use ($s) {
                $q->where('products.name', 'like', "%{$s}%")
                    ->orWhere('suppliers.name', 'like', "%{$s}%")
                    ->orWhere('products.sku', 'like', "%{$s}%");
            });
        }

        $mappings = $query->orderBy('products.name')->paginate(10)->withQueryString();

        return view('product_suppliers.index', compact('mappings', 'products', 'suppliers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'supplier_price' => ['required', 'numeric', 'min:0'],
        ], [
            'product_id.required' => 'Produk wajib dipilih.',
            'supplier_id.required' => 'Supplier / Lembaga wajib dipilih.',
            'supplier_price.required' => 'Harga/nilai pasokan referensi wajib diisi.',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $product->suppliers()->syncWithoutDetaching([
            $validated['supplier_id'] => ['supplier_price' => $validated['supplier_price']]
        ]);

        return redirect()->route('product-suppliers.index')
            ->with('success', 'Relasi pasokan produk dan supplier berhasil disimpan/diperbarui.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'supplier_id' => ['required', 'exists:suppliers,id'],
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $product->suppliers()->detach($validated['supplier_id']);

        return redirect()->route('product-suppliers.index')
            ->with('success', 'Hubungan pasokan produk dan supplier berhasil dilepas.');
    }
}
