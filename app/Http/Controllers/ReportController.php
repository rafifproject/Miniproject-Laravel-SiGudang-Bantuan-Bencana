<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\DistributionTarget;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $type = $request->input('type', 'stok_saat_ini');
        $dateStart = $request->input('date_start', date('Y-m-01'));
        $dateEnd = $request->input('date_end', date('Y-m-d'));

        $data = [];
        $summary = [];

        switch ($type) {
            case 'stok_kategori':
                $data = Category::withCount('products')
                    ->with(['products'])
                    ->get()
                    ->map(function ($cat) {
                        $totalStock = $cat->products->sum('stock');
                        $totalValue = $cat->products->sum(fn ($p) => $p->stock * $p->price);
                        $criticalItems = $cat->products->filter(fn ($p) => $p->stock <= $p->minimum_stock)->count();
                        return (object) [
                            'name' => $cat->name,
                            'total_products' => $cat->products_count,
                            'total_stock' => $totalStock,
                            'total_value' => $totalValue,
                            'critical_items' => $criticalItems,
                        ];
                    });
                $summary = [
                    'Total Kategori' => $data->count(),
                    'Total Fisik Unit' => number_format($data->sum('total_stock')),
                    'Total Nilai Kategori' => 'Rp ' . number_format($data->sum('total_value'), 0, ',', '.'),
                ];
                break;

            case 'barang_masuk':
                $data = StockMovement::with(['product.category', 'supplier', 'user'])
                    ->where('type', 'IN')
                    ->whereBetween('date', [$dateStart, $dateEnd])
                    ->orderBy('date', 'desc')
                    ->get();
                $summary = [
                    'Total Transaksi Masuk' => $data->count(),
                    'Total Kuantitas Masuk' => number_format($data->sum('quantity')),
                    'Total Estimasi Nilai' => 'Rp ' . number_format($data->sum(fn ($m) => $m->quantity * ($m->product->price ?? 0)), 0, ',', '.'),
                ];
                break;

            case 'barang_keluar':
                $data = StockMovement::with(['product.category', 'distributionTarget', 'user'])
                    ->where('type', 'OUT')
                    ->whereBetween('date', [$dateStart, $dateEnd])
                    ->orderBy('date', 'desc')
                    ->get();
                $summary = [
                    'Total Penyaluran' => $data->count(),
                    'Total Unit Tersalur' => number_format($data->sum('quantity')),
                    'Total Nilai Penyaluran' => 'Rp ' . number_format($data->sum(fn ($m) => $m->quantity * ($m->product->price ?? 0)), 0, ',', '.'),
                ];
                break;

            case 'mutasi_stok':
                $data = StockMovement::with(['product.category', 'supplier', 'distributionTarget', 'user'])
                    ->whereBetween('date', [$dateStart, $dateEnd])
                    ->orderBy('date', 'desc')
                    ->get();
                $summary = [
                    'Total Arus Mutasi' => $data->count(),
                    'Masuk (IN)' => number_format($data->where('type', 'IN')->sum('quantity')),
                    'Keluar (OUT)' => number_format($data->where('type', 'OUT')->sum('quantity')),
                ];
                break;

            case 'distribusi_tujuan':
                $data = DB::table('stock_movements')
                    ->join('distribution_targets', 'stock_movements.distribution_target_id', '=', 'distribution_targets.id')
                    ->where('stock_movements.type', 'OUT')
                    ->whereBetween('stock_movements.date', [$dateStart, $dateEnd])
                    ->select(
                        'distribution_targets.name as target_name',
                        'distribution_targets.location',
                        DB::raw('COUNT(stock_movements.id) as total_tx'),
                        DB::raw('SUM(stock_movements.quantity) as total_qty')
                    )
                    ->groupBy('distribution_targets.id', 'distribution_targets.name', 'distribution_targets.location')
                    ->get();
                $summary = [
                    'Posko Terdistribusi' => $data->count(),
                    'Total Barang Tersalurkan' => number_format($data->sum('total_qty')),
                ];
                break;

            case 'nilai_persediaan':
                $data = Product::with('category')->orderBy('name')->get()->map(function ($p) {
                    return (object) [
                        'sku' => $p->sku,
                        'name' => $p->name,
                        'category' => $p->category->name ?? '-',
                        'unit' => $p->unit,
                        'stock' => $p->stock,
                        'price' => $p->price,
                        'total_value' => $p->stock * $p->price,
                    ];
                });
                $summary = [
                    'Total Jenis Barang' => $data->count(),
                    'Total Unit Fisik' => number_format($data->sum('stock')),
                    'Total Nilai Inventaris' => 'Rp ' . number_format($data->sum('total_value'), 0, ',', '.'),
                ];
                break;

            case 'aktivitas_user':
                $data = DB::table('stock_movements')
                    ->join('users', 'stock_movements.user_id', '=', 'users.id')
                    ->whereBetween('stock_movements.date', [$dateStart, $dateEnd])
                    ->select(
                        'users.username',
                        'users.full_name',
                        'users.role',
                        DB::raw('COUNT(stock_movements.id) as total_tx'),
                        DB::raw("SUM(CASE WHEN stock_movements.type = 'IN' THEN 1 ELSE 0 END) as total_in"),
                        DB::raw("SUM(CASE WHEN stock_movements.type = 'OUT' THEN 1 ELSE 0 END) as total_out")
                    )
                    ->groupBy('users.id', 'users.username', 'users.full_name', 'users.role')
                    ->get();
                $summary = [
                    'Operator Aktif' => $data->count(),
                    'Total Aksi Transaksi' => number_format($data->sum('total_tx')),
                ];
                break;

            case 'stok_saat_ini':
            default:
                $data = Product::with('category')->orderBy('name')->get();
                $summary = [
                    'Total Jenis Logistik' => $data->count(),
                    'Total Fisik Stok' => number_format($data->sum('stock')),
                    'Stok Kritis' => $data->filter(fn ($p) => $p->stock <= $p->minimum_stock)->count(),
                    'Nilai Persediaan' => 'Rp ' . number_format($data->sum(fn ($p) => $p->stock * $p->price), 0, ',', '.'),
                ];
                break;
        }

        $isPrint = $request->boolean('print');

        return view('reports.index', compact('type', 'dateStart', 'dateEnd', 'data', 'summary', 'isPrint'));
    }
}
