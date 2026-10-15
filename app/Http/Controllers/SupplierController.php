<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function index(Request $request): View
    {
        $query = Supplier::withCount(['products', 'stockMovements'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->input('search');
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('contact', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('type'), function ($q) use ($request) {
                $q->where('type', $request->input('type'));
            });

        $suppliers = $query->orderBy('name')->paginate(10)->withQueryString();
        $types = Supplier::distinct()->pluck('type')->filter()->values();

        return view('suppliers.index', compact('suppliers', 'types'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'type' => ['required', 'string', 'max:100'],
            'contact' => ['nullable', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:150'],
            'address' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_public' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'Nama supplier / lembaga donor wajib diisi.',
            'type.required' => 'Tipe lembaga wajib dipilih.',
        ]);

        $validated['is_public'] = $request->has('is_public');

        Supplier::create($validated);

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier / Donatur baru berhasil ditambahkan.');
    }

    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'type' => ['required', 'string', 'max:100'],
            'contact' => ['nullable', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:150'],
            'address' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_public' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'Nama supplier / lembaga donor wajib diisi.',
            'type.required' => 'Tipe lembaga wajib dipilih.',
        ]);

        $validated['is_public'] = $request->has('is_public');

        $supplier->update($validated);

        return redirect()->route('suppliers.index')
            ->with('success', 'Data supplier / donatur berhasil diperbarui.');
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        if ($supplier->stockMovements()->exists()) {
            return redirect()->route('suppliers.index')
                ->with('error', 'Supplier tidak dapat dihapus karena tercatat dalam histori penerimaan barang masuk.');
        }

        $supplierName = $supplier->name;
        $supplier->products()->detach();
        $supplier->delete();

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier ' . $supplierName . ' berhasil dihapus.');
    }
}
