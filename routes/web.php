<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductSupplierController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistem Informasi Gudang Bantuan Bencana (SIGudang)
|--------------------------------------------------------------------------
*/

// ── 1. Portal Publik (Tanpa Autentikasi) ──────────────────────────────────
Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/publik/dashboard', [PublicController::class, 'dashboard'])->name('public.dashboard');
Route::get('/publik/stok', [PublicController::class, 'stok'])->name('public.stok');
Route::get('/publik/bantuan-masuk', [PublicController::class, 'masuk'])->name('public.masuk');
Route::get('/publik/penyaluran', [PublicController::class, 'penyaluran'])->name('public.penyaluran');
Route::get('/publik/statistik', [PublicController::class, 'statistik'])->name('public.statistik');
Route::get('/publik/laporan', [PublicController::class, 'laporan'])->name('public.laporan');
Route::get('/publik/tentang', [PublicController::class, 'tentang'])->name('public.tentang');

// ── 2. Autentikasi Pengguna ─────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ── 3. Area Bersama (Admin & Staff) ──────────────────────────────────────
Route::middleware(['auth'])->group(function () {
    // Profil Pengguna
    Route::get('/profil', [AuthController::class, 'profile'])->name('profile.index');
    Route::put('/profil', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profil/password', [AuthController::class, 'updatePassword'])->name('profile.password');

    // Dashboard Staff
    Route::get('/staff/dashboard', [DashboardController::class, 'staffDashboard'])
        ->name('staff.dashboard')
        ->middleware('role:staff,admin');

    // Transaksi Operasional Gudang (Barang Masuk, Barang Keluar, Mutasi Stok)
    Route::prefix('transaksi')->group(function () {
        Route::get('/barang-masuk', [StockMovementController::class, 'barangMasukIndex'])->name('barang-masuk.index');
        Route::post('/barang-masuk', [StockMovementController::class, 'barangMasukStore'])->name('barang-masuk.store');

        Route::get('/barang-keluar', [StockMovementController::class, 'barangKeluarIndex'])->name('barang-keluar.index');
        Route::post('/barang-keluar', [StockMovementController::class, 'barangKeluarStore'])->name('barang-keluar.store');

        Route::get('/mutasi-stok', [StockMovementController::class, 'mutasiStokIndex'])->name('mutasi-stok.index');
    });
});

// ── 4. Area Khusus Administrator ─────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Dashboard Admin
    Route::get('/admin/dashboard', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');

    // Resource Controllers (Slide 18)
    Route::resource('products', ProductController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('suppliers', SupplierController::class);

    // Relasi Many-to-Many Pasokan Supplier
    Route::prefix('admin')->group(function () {
        Route::get('/product-suppliers', [ProductSupplierController::class, 'index'])->name('product-suppliers.index');
        Route::post('/product-suppliers', [ProductSupplierController::class, 'store'])->name('product-suppliers.store');
        Route::delete('/product-suppliers', [ProductSupplierController::class, 'destroy'])->name('product-suppliers.destroy');

        // Laporan Mendalam & Cetak
        Route::get('/laporan', [ReportController::class, 'index'])->name('admin.laporan');

        // Manajemen Pengguna & Hak Akses
        Route::resource('users', UserController::class)->except(['create', 'show', 'edit']);
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    });
});
