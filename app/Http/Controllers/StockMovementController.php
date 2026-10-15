<?php

namespace App\Http\Controllers;

use App\Models\DistributionTarget;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class StockMovementController extends Controller
{
    /**
     * Generate sequential reference string like REF-YYYY-XXX or TRX-YYYY-XXX
     */
    protected function generateReference(string $type): string
    {
        $year = date('Y');
        $prefix = ($type === 'IN') ? "REF-{$year}-" : "TRX-{$year}-";

        $latestRef = StockMovement::where('reference', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->value('reference');

        $nextNum = 1;
        if ($latestRef && preg_match('#^' . preg_quote($prefix, '#') . '(\d+)$#', $latestRef, $m)) {
            $nextNum = ((int) $m[1]) + 1;
        }

        return $prefix . str_pad((string) $nextNum, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Halaman Transaksi Barang Masuk (Form + 10 transaksi terakhir)
     */
    public function barangMasukIndex(): View
    {
        $products = Product::with('category')->orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();
        $recentIn = StockMovement::with(['product.category', 'supplier', 'user'])
            ->where('type', 'IN')
            ->latest('date')
            ->latest('id')
            ->take(10)
            ->get();

        $suggestedRef = $this->generateReference('IN');

        return view('transaksi.barang_masuk', compact('products', 'suppliers', 'recentIn', 'suggestedRef'));
    }

    /**
     * Simpan Transaksi Barang Masuk secara Atomik
     */
    public function barangMasukStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'date' => ['required', 'date'],
            'reference' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:500'],
            'is_public' => ['nullable', 'boolean'],
        ], [
            'product_id.required' => 'Produk logistik wajib dipilih.',
            'quantity.required' => 'Jumlah barang masuk wajib diisi.',
            'quantity.min' => 'Jumlah barang masuk minimal 1.',
            'date.required' => 'Tanggal penerimaan wajib diisi.',
        ]);

        $reference = !empty($validated['reference']) ? $validated['reference'] : $this->generateReference('IN');
        $isPublic = $request->has('is_public');

        DB::transaction(function () use ($validated, $reference, $isPublic) {
            $product = Product::lockForUpdate()->findOrFail($validated['product_id']);

            // Simpan movement
            StockMovement::create([
                'product_id' => $product->id,
                'user_id' => Auth::id(),
                'supplier_id' => $validated['supplier_id'] ?? null,
                'distribution_target_id' => null,
                'type' => 'IN',
                'quantity' => $validated['quantity'],
                'date' => $validated['date'],
                'reference' => $reference,
                'notes' => $validated['notes'] ?? null,
                'is_public' => $isPublic,
            ]);

            // Update stok fisik produk
            $product->increment('stock', $validated['quantity']);
        });

        return redirect()->route('barang-masuk.index')
            ->with('success', 'Transaksi Barang Masuk berhasil dicatat. Stok ' . Product::find($validated['product_id'])->name . ' bertambah ' . number_format($validated['quantity']) . '.');
    }

    /**
     * Halaman Transaksi Barang Keluar (Form + 10 penyaluran terakhir)
     */
    public function barangKeluarIndex(): View
    {
        $products = Product::with('category')->orderBy('name')->get();
        $targets = DistributionTarget::orderBy('name')->get();
        $recentOut = StockMovement::with(['product.category', 'distributionTarget', 'user'])
            ->where('type', 'OUT')
            ->latest('date')
            ->latest('id')
            ->take(10)
            ->get();

        $suggestedRef = $this->generateReference('OUT');

        return view('transaksi.barang_keluar', compact('products', 'targets', 'recentOut', 'suggestedRef'));
    }

    /**
     * Simpan Transaksi Barang Keluar secara Atomik dengan Row-Level Locking
     * Mencegah stok menjadi negatif!
     */
    public function barangKeluarStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'distribution_target_id' => ['required', 'exists:distribution_targets,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'date' => ['required', 'date'],
            'reference' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:500'],
            'is_public' => ['nullable', 'boolean'],
        ], [
            'product_id.required' => 'Produk logistik wajib dipilih.',
            'distribution_target_id.required' => 'Titik posko tujuan distribusi wajib dipilih.',
            'quantity.required' => 'Jumlah barang keluar wajib diisi.',
            'quantity.min' => 'Jumlah barang keluar minimal 1.',
            'date.required' => 'Tanggal penyaluran wajib diisi.',
        ]);

        $reference = !empty($validated['reference']) ? $validated['reference'] : $this->generateReference('OUT');
        $isPublic = $request->has('is_public');

        try {
            DB::transaction(function () use ($validated, $reference, $isPublic) {
                // Lock row product untuk mencegah race condition
                $product = Product::lockForUpdate()->findOrFail($validated['product_id']);

                if ($product->stock < $validated['quantity']) {
                    throw ValidationException::withMessages([
                        'quantity' => "Stok fisik tidak mencukupi! Sisa stok untuk '{$product->name}' hanya {$product->stock} {$product->unit}, namun diminta {$validated['quantity']} {$product->unit}."
                    ]);
                }

                // Catat mutasi keluar
                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => Auth::id(),
                    'supplier_id' => null,
                    'distribution_target_id' => $validated['distribution_target_id'],
                    'type' => 'OUT',
                    'quantity' => $validated['quantity'],
                    'date' => $validated['date'],
                    'reference' => $reference,
                    'notes' => $validated['notes'] ?? null,
                    'is_public' => $isPublic,
                ]);

                // Kurangi stok produk
                $product->decrement('stock', $validated['quantity']);
            });
        } catch (ValidationException $e) {
            return redirect()->back()->withInput()->withErrors($e->errors());
        }

        return redirect()->route('barang-keluar.index')
            ->with('success', 'Transaksi Penyaluran Bencana berhasil dicatat. Stok berkurang ' . number_format($validated['quantity']) . '.');
    }

    /**
     * Riwayat Mutasi Stok / Audit Trail Lengkap
     */
    public function mutasiStokIndex(Request $request): View
    {
        $products = Product::orderBy('name')->get();

        $query = StockMovement::with(['product.category', 'supplier', 'distributionTarget', 'user'])
            ->when($request->filled('type'), function ($q) use ($request) {
                $q->where('type', $request->input('type'));
            })
            ->when($request->filled('product_id'), function ($q) use ($request) {
                $q->where('product_id', $request->input('product_id'));
            })
            ->when($request->filled('date_start'), function ($q) use ($request) {
                $q->whereDate('date', '>=', $request->input('date_start'));
            })
            ->when($request->filled('date_end'), function ($q) use ($request) {
                $q->whereDate('date', '<=', $request->input('date_end'));
            })
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->input('search');
                $q->where(function ($sub) use ($search) {
                    $sub->where('reference', 'like', "%{$search}%")
                        ->orWhere('notes', 'like', "%{$search}%");
                });
            });

        $movements = $query->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('transaksi.mutasi_stok', compact('movements', 'products'));
    }
}
