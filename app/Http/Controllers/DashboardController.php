<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function adminDashboard(): View
    {
        $totalProducts = Product::count();
        $totalStock = Product::sum('stock');
        $totalValue = Product::select(DB::raw('SUM(stock * price) as val'))->value('val') ?? 0;
        $criticalStockCount = Product::whereColumn('stock', '<=', 'minimum_stock')->count();
        $totalIn = StockMovement::where('type', 'IN')->sum('quantity');
        $totalOut = StockMovement::where('type', 'OUT')->sum('quantity');

        // Low stock items
        $criticalProducts = Product::with('category')
            ->whereColumn('stock', '<=', 'minimum_stock')
            ->orderBy('stock', 'asc')
            ->take(8)
            ->get();

        // Recent 10 transactions
        $recentTransactions = StockMovement::with(['product.category', 'supplier', 'distributionTarget', 'user'])
            ->latest('date')
            ->latest('id')
            ->take(10)
            ->get();

        // Monthly mutation data for Chart.js
        $monthlyStats = StockMovement::select(
            DB::raw("DATE_FORMAT(date, '%Y-%m') as month"),
            DB::raw("SUM(CASE WHEN type = 'IN' THEN quantity ELSE 0 END) as total_in"),
            DB::raw("SUM(CASE WHEN type = 'OUT' THEN quantity ELSE 0 END) as total_out")
        )
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->take(6)
            ->get();

        $chartLabels = $monthlyStats->pluck('month')->map(function ($m) {
            return date('M Y', strtotime($m . '-01'));
        })->toArray();
        $chartIn = $monthlyStats->pluck('total_in')->toArray();
        $chartOut = $monthlyStats->pluck('total_out')->toArray();

        // Category breakdown
        $categoryStats = Category::withCount('products')
            ->withSum('products', 'stock')
            ->get();

        return view('dashboard.admin', compact(
            'totalProducts',
            'totalStock',
            'totalValue',
            'criticalStockCount',
            'totalIn',
            'totalOut',
            'criticalProducts',
            'recentTransactions',
            'chartLabels',
            'chartIn',
            'chartOut',
            'categoryStats'
        ));
    }

    public function staffDashboard(): View
    {
        $totalProducts = Product::count();
        $totalStock = Product::sum('stock');
        $criticalStockCount = Product::whereColumn('stock', '<=', 'minimum_stock')->count();
        $totalIn = StockMovement::where('type', 'IN')->sum('quantity');
        $totalOut = StockMovement::where('type', 'OUT')->sum('quantity');

        $criticalProducts = Product::with('category')
            ->whereColumn('stock', '<=', 'minimum_stock')
            ->orderBy('stock', 'asc')
            ->take(6)
            ->get();

        $recentTransactions = StockMovement::with(['product.category', 'supplier', 'distributionTarget'])
            ->latest('date')
            ->latest('id')
            ->take(8)
            ->get();

        return view('dashboard.staff', compact(
            'totalProducts',
            'totalStock',
            'criticalStockCount',
            'totalIn',
            'totalOut',
            'criticalProducts',
            'recentTransactions'
        ));
    }
}
