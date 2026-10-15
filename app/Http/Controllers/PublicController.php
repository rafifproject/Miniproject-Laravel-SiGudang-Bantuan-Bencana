<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\DistributionTarget;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PublicController extends Controller
{
    /**
     * Halaman 1: Beranda Publik (index.php)
     */
    public function home(): View
    {
        $totalProducts = Product::where('is_public', true)->count();
        $totalStock = Product::where('is_public', true)->sum('stock');
        $totalIn = StockMovement::where('type', 'IN')->where('is_public', true)->sum('quantity');
        $totalOut = StockMovement::where('type', 'OUT')->where('is_public', true)->sum('quantity');

        $criticalItems = Product::with('category')
            ->where('is_public', true)
            ->whereColumn('stock', '<=', 'minimum_stock')
            ->orderBy('stock', 'asc')
            ->take(4)
            ->get();

        $recentActivities = StockMovement::with(['product.category', 'supplier', 'distributionTarget'])
            ->where('is_public', true)
            ->latest('date')
            ->latest('id')
            ->take(5)
            ->get();

        return view('public.home', compact(
            'totalProducts',
            'totalStock',
            'totalIn',
            'totalOut',
            'criticalItems',
            'recentActivities'
        ));
    }

    /**
     * Halaman 2: Dashboard Publik (public/dashboard.php)
     */
    public function dashboard(Request $request): View
    {
        $dateFrom = $request->input('from', date('Y-01-01'));
        $dateTo = $request->input('to', date('Y-m-d'));

        $stats = [
            'total_jenis' => Product::where('is_public', true)->count(),
            'total_stok' => Product::where('is_public', true)->sum('stock'),
            'total_masuk' => StockMovement::where('type', 'IN')->where('is_public', true)->sum('quantity'),
            'total_keluar' => StockMovement::where('type', 'OUT')->where('is_public', true)->sum('quantity'),
            'total_supplier' => Supplier::where('is_public', true)->count(),
        ];

        // Grafik bulanan IN/OUT
        $monthlyData = StockMovement::where('is_public', true)
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->select(
                DB::raw("DATE_FORMAT(date, '%Y-%m') as bulan"),
                DB::raw("SUM(CASE WHEN type='IN' THEN quantity ELSE 0 END) as masuk"),
                DB::raw("SUM(CASE WHEN type='OUT' THEN quantity ELSE 0 END) as keluar")
            )
            ->groupBy('bulan')
            ->orderBy('bulan', 'asc')
            ->get();

        // Komposisi kategori
        $categoryData = Category::select('categories.name', DB::raw('COALESCE(SUM(products.stock), 0) as total'))
            ->leftJoin('products', function ($join) {
                $join->on('products.category_id', '=', 'categories.id')
                    ->where('products.is_public', true);
            })
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('total', 'desc')
            ->get();

        // Distribusi per tujuan
        $distRows = DistributionTarget::select('distribution_targets.name', DB::raw('COALESCE(SUM(stock_movements.quantity), 0) as total'))
            ->leftJoin('stock_movements', function ($join) use ($dateFrom, $dateTo) {
                $join->on('stock_movements.distribution_target_id', '=', 'distribution_targets.id')
                    ->where('stock_movements.type', 'OUT')
                    ->where('stock_movements.is_public', true)
                    ->whereBetween('stock_movements.date', [$dateFrom, $dateTo]);
            })
            ->groupBy('distribution_targets.id', 'distribution_targets.name')
            ->orderBy('total', 'desc')
            ->take(8)
            ->get();

        return view('public.dashboard', compact(
            'dateFrom',
            'dateTo',
            'stats',
            'monthlyData',
            'categoryData',
            'distRows'
        ));
    }

    /**
     * Halaman 3: Stok Bantuan Publik (public/stok.php)
     */
    public function stok(Request $request): View
    {
        $search = $request->input('q', '');
        $catId = (int) $request->input('cat', 0);
        $status = $request->input('status', '');
        $sortCol = in_array($request->input('sort'), ['name', 'sku', 'stock', 'kategori']) ? $request->input('sort') : 'name';
        $sortDir = strtoupper($request->input('dir')) === 'DESC' ? 'DESC' : 'ASC';

        $cats = Category::orderBy('name')->get();

        $query = Product::with('category')->where('is_public', true);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($catId) {
            $query->where('category_id', $catId);
        }

        if ($status === 'kritis') {
            $query->whereColumn('stock', '<=', 'minimum_stock');
        } elseif ($status === 'aman') {
            $query->whereColumn('stock', '>', 'minimum_stock');
        }

        if ($sortCol === 'kategori') {
            $query->join('categories', 'products.category_id', '=', 'categories.id')
                ->orderBy('categories.name', $sortDir)
                ->select('products.*');
        } else {
            $query->orderBy($sortCol, $sortDir);
        }

        $rows = $query->paginate(10)->withQueryString();

        $params = [
            'q' => $search,
            'cat' => $catId ?: null,
            'status' => $status ?: null,
            'sort' => $sortCol,
            'dir' => $sortDir,
        ];

        return view('public.stok', compact('rows', 'cats', 'search', 'catId', 'status', 'sortCol', 'sortDir', 'params'));
    }

    /**
     * Halaman 4: Riwayat Bantuan Masuk Publik (public/bantuan_masuk.php)
     */
    public function masuk(Request $request): View
    {
        $dateFrom = $request->input('from', '');
        $dateTo = $request->input('to', '');
        $catId = (int) $request->input('cat', 0);
        $supId = (int) $request->input('supplier', 0);

        $cats = Category::orderBy('name')->get();
        $sups = Supplier::where('is_public', true)->orderBy('name')->get();

        $query = StockMovement::with(['product.category', 'supplier'])
            ->where('type', 'IN')
            ->where('is_public', true);

        if ($dateFrom) {
            $query->whereDate('date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('date', '<=', $dateTo);
        }
        if ($catId) {
            $query->whereHas('product', fn ($p) => $p->where('category_id', $catId));
        }
        if ($supId) {
            $query->where('supplier_id', $supId);
        }

        $rows = $query->orderBy('date', 'desc')->orderBy('id', 'desc')->paginate(10)->withQueryString();

        return view('public.masuk', compact('rows', 'cats', 'sups', 'dateFrom', 'dateTo', 'catId', 'supId'));
    }

    /**
     * Halaman 5: Riwayat Penyaluran Bantuan Publik (public/penyaluran.php)
     */
    public function penyaluran(Request $request): View
    {
        $dateFrom = $request->input('from', '');
        $dateTo = $request->input('to', '');
        $catId = (int) $request->input('cat', 0);
        $distId = (int) $request->input('tujuan', 0);

        $cats = Category::orderBy('name')->get();
        $dists = DistributionTarget::orderBy('name')->get();

        $query = StockMovement::with(['product.category', 'distributionTarget'])
            ->where('type', 'OUT')
            ->where('is_public', true);

        if ($dateFrom) {
            $query->whereDate('date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('date', '<=', $dateTo);
        }
        if ($catId) {
            $query->whereHas('product', fn ($p) => $p->where('category_id', $catId));
        }
        if ($distId) {
            $query->where('distribution_target_id', $distId);
        }

        $rows = $query->orderBy('date', 'desc')->orderBy('id', 'desc')->paginate(10)->withQueryString();

        return view('public.penyaluran', compact('rows', 'cats', 'dists', 'dateFrom', 'dateTo', 'catId', 'distId'));
    }

    /**
     * Halaman 6: Statistik Distribusi Publik (public/statistik.php)
     */
    public function statistik(Request $request): View
    {
        $dateFrom = $request->input('from', date('Y-01-01'));
        $dateTo = $request->input('to', date('Y-m-d'));

        $sumRow = StockMovement::where('is_public', true)
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->select(
                DB::raw("COALESCE(SUM(CASE WHEN type='IN' THEN quantity ELSE 0 END), 0) as total_masuk"),
                DB::raw("COALESCE(SUM(CASE WHEN type='OUT' THEN quantity ELSE 0 END), 0) as total_keluar")
            )
            ->first();

        // Dest
        $destRows = DistributionTarget::select('distribution_targets.name', DB::raw('COALESCE(SUM(stock_movements.quantity),0) as total'))
            ->leftJoin('stock_movements', function ($join) use ($dateFrom, $dateTo) {
                $join->on('stock_movements.distribution_target_id', '=', 'distribution_targets.id')
                    ->where('stock_movements.type', 'OUT')
                    ->where('stock_movements.is_public', true)
                    ->whereBetween('stock_movements.date', [$dateFrom, $dateTo]);
            })
            ->groupBy('distribution_targets.id', 'distribution_targets.name')
            ->orderBy('total', 'desc')
            ->get();

        // Cat
        $catRows = Category::select('categories.name', DB::raw('COALESCE(SUM(stock_movements.quantity),0) as total'))
            ->leftJoin('products', function ($join) {
                $join->on('products.category_id', '=', 'categories.id')
                    ->where('products.is_public', true);
            })
            ->leftJoin('stock_movements', function ($join) use ($dateFrom, $dateTo) {
                $join->on('stock_movements.product_id', '=', 'products.id')
                    ->where('stock_movements.type', 'OUT')
                    ->where('stock_movements.is_public', true)
                    ->whereBetween('stock_movements.date', [$dateFrom, $dateTo]);
            })
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('total', 'desc')
            ->get();

        // Time series
        $tsRows = StockMovement::where('is_public', true)
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->select(
                DB::raw("DATE_FORMAT(date, '%Y-%m') as bulan"),
                DB::raw("SUM(CASE WHEN type='IN' THEN quantity ELSE 0 END) as masuk"),
                DB::raw("SUM(CASE WHEN type='OUT' THEN quantity ELSE 0 END) as keluar")
            )
            ->groupBy('bulan')
            ->orderBy('bulan', 'asc')
            ->get();

        return view('public.statistik', compact('dateFrom', 'dateTo', 'sumRow', 'destRows', 'catRows', 'tsRows'));
    }

    /**
     * Halaman 7: Laporan Publik (public/laporan_publik.php)
     */
    public function laporan(Request $request): View
    {
        $jenisLaporan = $request->input('jenis', 'stok');
        $dateFrom = $request->input('from', date('Y-01-01'));
        $dateTo = $request->input('to', date('Y-m-d'));

        $jenisList = [
            'stok' => 'Laporan Stok Saat Ini',
            'distribusi' => 'Laporan Distribusi',
            'masuk' => 'Laporan Bantuan Masuk',
        ];

        $data = [];
        $columns = [];

        if ($jenisLaporan === 'stok') {
            $data = Product::with('category')->where('is_public', true)->orderBy('name')->get()->map(function ($p) {
                return [
                    $p->sku,
                    $p->name,
                    $p->category->name ?? '-',
                    number_format($p->stock),
                    $p->unit,
                    number_format($p->minimum_stock),
                    $p->stock <= $p->minimum_stock ? 'Kritis' : 'Aman',
                ];
            });
            $columns = ['SKU', 'Produk', 'Kategori', 'Stok', 'Satuan', 'Min. Stok', 'Status'];
        } elseif ($jenisLaporan === 'distribusi') {
            $data = StockMovement::with(['product.category', 'distributionTarget'])
                ->where('type', 'OUT')
                ->where('is_public', true)
                ->whereBetween('date', [$dateFrom, $dateTo])
                ->orderBy('date', 'desc')
                ->get()
                ->map(function ($m) {
                    return [
                        $m->date->format('d M Y'),
                        $m->product->name ?? '-',
                        $m->product->category->name ?? '-',
                        number_format($m->quantity),
                        $m->product->unit ?? 'pcs',
                        $m->distributionTarget->name ?? 'Posko Lapangan',
                    ];
                });
            $columns = ['Tanggal', 'Produk', 'Kategori', 'Jumlah', 'Satuan', 'Tujuan'];
        } elseif ($jenisLaporan === 'masuk') {
            $data = StockMovement::with(['product.category', 'supplier'])
                ->where('type', 'IN')
                ->where('is_public', true)
                ->whereBetween('date', [$dateFrom, $dateTo])
                ->orderBy('date', 'desc')
                ->get()
                ->map(function ($m) {
                    return [
                        $m->date->format('d M Y'),
                        $m->product->name ?? '-',
                        $m->product->category->name ?? '-',
                        number_format($m->quantity),
                        $m->product->unit ?? 'pcs',
                        $m->supplier->name ?? 'Donasi Umum',
                        $m->reference ?? '-',
                    ];
                });
            $columns = ['Tanggal', 'Produk', 'Kategori', 'Jumlah', 'Satuan', 'Donatur', 'Referensi'];
        }

        return view('public.laporan', compact('jenisLaporan', 'jenisList', 'dateFrom', 'dateTo', 'data', 'columns'));
    }

    /**
     * Halaman 8: Tentang Sistem (public/tentang.php)
     */
    public function tentang(): View
    {
        return view('public.tentang');
    }
}
