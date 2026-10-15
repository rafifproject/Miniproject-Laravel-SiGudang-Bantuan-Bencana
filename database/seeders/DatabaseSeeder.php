<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\DistributionTarget;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Users & Profiles
        $admin = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'full_name' => 'Administrator Sistem',
                'email' => 'admin@sigudang.id',
                'is_active' => true,
            ]
        );

        UserProfile::firstOrCreate(
            ['user_id' => $admin->id],
            [
                'full_name' => 'Administrator Sistem',
                'phone' => '081234567890',
                'address' => 'Kantor BPBD / Gudang Pusat Logistik',
            ]
        );

        $staff1 = User::firstOrCreate(
            ['username' => 'staff1'],
            [
                'password' => Hash::make('staff123'),
                'role' => 'staff',
                'full_name' => 'Budi Santoso',
                'email' => 'budi@sigudang.id',
                'is_active' => true,
            ]
        );

        UserProfile::firstOrCreate(
            ['user_id' => $staff1->id],
            [
                'full_name' => 'Budi Santoso',
                'phone' => '081234567891',
                'address' => 'Gudang Posko Lapangan 1',
            ]
        );

        $staff2 = User::firstOrCreate(
            ['username' => 'staff2'],
            [
                'password' => Hash::make('staff123'),
                'role' => 'staff',
                'full_name' => 'Siti Rahayu',
                'email' => 'siti@sigudang.id',
                'is_active' => true,
            ]
        );

        UserProfile::firstOrCreate(
            ['user_id' => $staff2->id],
            [
                'full_name' => 'Siti Rahayu',
                'phone' => '081234567892',
                'address' => 'Gudang Posko Lapangan 2',
            ]
        );

        // 2. Categories
        $categoriesData = [
            ['name' => 'Pangan', 'description' => 'Bahan makanan dan minuman untuk korban bencana'],
            ['name' => 'Sandang', 'description' => 'Pakaian dan perlengkapan berpakaian'],
            ['name' => 'Kesehatan', 'description' => 'Obat-obatan dan alat kesehatan'],
            ['name' => 'Peralatan', 'description' => 'Peralatan rumah tangga dan tenda pengungsian'],
            ['name' => 'Higienitas', 'description' => 'Sabun, sampo, dan perlengkapan kebersihan diri'],
        ];

        $categories = [];
        foreach ($categoriesData as $cat) {
            $categories[$cat['name']] = Category::firstOrCreate(['name' => $cat['name']], $cat);
        }

        // 3. Suppliers
        $suppliersData = [
            1 => ['name' => 'Palang Merah Indonesia', 'type' => 'NGO', 'contact' => '021-7992325', 'email' => 'info@pmi.or.id', 'address' => 'Jakarta Selatan', 'description' => 'Organisasi kemanusiaan nasional', 'is_public' => true],
            2 => ['name' => 'BNPB - Badan Nasional', 'type' => 'Pemerintah', 'contact' => '021-5703867', 'email' => 'info@bnpb.go.id', 'address' => 'Jakarta Timur', 'description' => 'Badan Nasional Penanggulangan Bencana', 'is_public' => true],
            3 => ['name' => 'PT. Indah Karya', 'type' => 'Perusahaan', 'contact' => '021-4545678', 'email' => 'csr@indahkarya.co.id', 'address' => 'Bandung', 'description' => 'Donasi CSR perusahaan manufaktur', 'is_public' => true],
            4 => ['name' => 'Yayasan Peduli Sesama', 'type' => 'Yayasan', 'contact' => '021-8891234', 'email' => 'info@yps.or.id', 'address' => 'Yogyakarta', 'description' => 'Yayasan sosial kemanusiaan', 'is_public' => true],
            5 => ['name' => 'Bank Mandiri (CSR)', 'type' => 'Perusahaan', 'contact' => '021-5299700', 'email' => 'csr@bankmandiri.co.id', 'address' => 'Jakarta Pusat', 'description' => 'Program CSR Bank Mandiri', 'is_public' => false],
        ];

        $suppliers = [];
        foreach ($suppliersData as $idx => $sup) {
            $suppliers[$idx] = Supplier::firstOrCreate(['name' => $sup['name']], $sup);
        }

        // 4. Distribution Targets
        $targetsData = [
            1 => ['name' => 'Posko Cianjur', 'location' => 'Cianjur, Jawa Barat', 'description' => 'Posko bantuan gempa Cianjur'],
            2 => ['name' => 'Pengungsian Palu', 'location' => 'Palu, Sulawesi Tengah', 'description' => 'Pengungsian pasca gempa Palu'],
            3 => ['name' => 'Pos Banjir Bekasi', 'location' => 'Bekasi, Jawa Barat', 'description' => 'Posko banjir Bekasi'],
            4 => ['name' => 'Pengungsian Lombok', 'location' => 'Lombok, NTB', 'description' => 'Pengungsian gempa Lombok'],
            5 => ['name' => 'Pos Gunung Semeru', 'location' => 'Lumajang, Jawa Timur', 'description' => 'Posko erupsi Gunung Semeru'],
        ];

        $targets = [];
        foreach ($targetsData as $idx => $target) {
            $targets[$idx] = DistributionTarget::firstOrCreate(['name' => $target['name']], $target);
        }

        // 5. Products
        $productsData = [
            1 => ['category_id' => $categories['Pangan']->id, 'sku' => 'PRD001', 'name' => 'Beras Premium 5kg', 'unit' => 'karung', 'price' => 75000, 'stock' => 1250, 'minimum_stock' => 200, 'description' => 'Beras kualitas premium untuk pengungsi', 'is_public' => true],
            2 => ['category_id' => $categories['Pangan']->id, 'sku' => 'PRD002', 'name' => 'Mi Instan (karton 40pcs)', 'unit' => 'karton', 'price' => 95000, 'stock' => 480, 'minimum_stock' => 100, 'description' => 'Mi instan berbagai rasa', 'is_public' => true],
            3 => ['category_id' => $categories['Pangan']->id, 'sku' => 'PRD003', 'name' => 'Air Mineral 600ml (karton)', 'unit' => 'karton', 'price' => 45000, 'stock' => 2300, 'minimum_stock' => 300, 'description' => 'Air mineral dalam kemasan', 'is_public' => true],
            4 => ['category_id' => $categories['Pangan']->id, 'sku' => 'PRD004', 'name' => 'Minyak Goreng 2L', 'unit' => 'botol', 'price' => 35000, 'stock' => 380, 'minimum_stock' => 80, 'description' => 'Minyak goreng kemasan', 'is_public' => true],
            5 => ['category_id' => $categories['Pangan']->id, 'sku' => 'PRD005', 'name' => 'Telur Ayam (tray 30butir)', 'unit' => 'tray', 'price' => 62000, 'stock' => 150, 'minimum_stock' => 50, 'description' => 'Telur ayam segar', 'is_public' => true],
            6 => ['category_id' => $categories['Sandang']->id, 'sku' => 'PRD006', 'name' => 'Selimut Tebal', 'unit' => 'lembar', 'price' => 85000, 'stock' => 430, 'minimum_stock' => 100, 'description' => 'Selimut untuk pengungsi', 'is_public' => true],
            7 => ['category_id' => $categories['Sandang']->id, 'sku' => 'PRD007', 'name' => 'Pakaian Dewasa Campur', 'unit' => 'set', 'price' => 55000, 'stock' => 300, 'minimum_stock' => 80, 'description' => 'Paket pakaian dewasa layak pakai', 'is_public' => true],
            8 => ['category_id' => $categories['Sandang']->id, 'sku' => 'PRD008', 'name' => 'Pakaian Anak', 'unit' => 'set', 'price' => 45000, 'stock' => 220, 'minimum_stock' => 60, 'description' => 'Paket pakaian anak', 'is_public' => true],
            9 => ['category_id' => $categories['Kesehatan']->id, 'sku' => 'PRD009', 'name' => 'Obat P3K Kit', 'unit' => 'kotak', 'price' => 125000, 'stock' => 180, 'minimum_stock' => 50, 'description' => 'Kotak P3K lengkap', 'is_public' => true],
            10 => ['category_id' => $categories['Kesehatan']->id, 'sku' => 'PRD010', 'name' => 'Parasetamol 500mg (box)', 'unit' => 'box', 'price' => 28000, 'stock' => 420, 'minimum_stock' => 100, 'description' => 'Parasetamol kemasan box', 'is_public' => true],
            11 => ['category_id' => $categories['Kesehatan']->id, 'sku' => 'PRD011', 'name' => 'Masker Medis (box 50pcs)', 'unit' => 'box', 'price' => 35000, 'stock' => 650, 'minimum_stock' => 150, 'description' => 'Masker medis 3-ply', 'is_public' => true],
            12 => ['category_id' => $categories['Peralatan']->id, 'sku' => 'PRD012', 'name' => 'Tenda Pengungsian', 'unit' => 'unit', 'price' => 1500000, 'stock' => 45, 'minimum_stock' => 10, 'description' => 'Tenda kapasitas 6 orang', 'is_public' => true],
            13 => ['category_id' => $categories['Peralatan']->id, 'sku' => 'PRD013', 'name' => 'Terpal 4x6m', 'unit' => 'lembar', 'price' => 75000, 'stock' => 120, 'minimum_stock' => 30, 'description' => 'Terpal tebal warna biru', 'is_public' => true],
            14 => ['category_id' => $categories['Higienitas']->id, 'sku' => 'PRD014', 'name' => 'Sabun Mandi (lusin)', 'unit' => 'lusin', 'price' => 48000, 'stock' => 280, 'minimum_stock' => 80, 'description' => 'Sabun mandi batang', 'is_public' => true],
            15 => ['category_id' => $categories['Higienitas']->id, 'sku' => 'PRD015', 'name' => 'Pasta Gigi', 'unit' => 'buah', 'price' => 15000, 'stock' => 550, 'minimum_stock' => 100, 'description' => 'Pasta gigi 120gr', 'is_public' => true],
        ];

        $products = [];
        foreach ($productsData as $idx => $prod) {
            $products[$idx] = Product::firstOrCreate(['sku' => $prod['sku']], $prod);
        }

        // 6. Product-Supplier Pivot
        $pivotData = [
            [1, 1, 72000], [1, 2, 70000], [2, 1, 92000], [3, 3, 43000], [4, 3, 33000],
            [6, 1, 82000], [6, 4, 80000], [9, 1, 120000], [9, 2, 118000], [12, 2, 1450000],
            [13, 3, 72000], [14, 4, 46000], [15, 4, 14000], [10, 1, 26000], [11, 1, 33000],
        ];

        foreach ($pivotData as [$pIdx, $sIdx, $price]) {
            if (isset($products[$pIdx], $suppliers[$sIdx])) {
                DB::table('product_suppliers')->updateOrInsert(
                    ['product_id' => $products[$pIdx]->id, 'supplier_id' => $suppliers[$sIdx]->id],
                    ['supplier_price' => $price, 'created_at' => now(), 'updated_at' => now()]
                );
            }
        }

        // 7. Stock Movements
        $movementsData = [
            [1, $admin->id, 1, null, 'IN', 500, '2026-01-05', 'REF-2026-001', 'Donasi awal masuk gudang'],
            [1, $admin->id, 2, null, 'IN', 300, '2026-01-10', 'REF-2026-002', 'Pengiriman kedua dari PMI'],
            [1, $staff1->id, null, 1, 'OUT', 200, '2026-01-15', 'TRX-2026-001', 'Distribusi ke Posko Cianjur'],
            [2, $admin->id, 1, null, 'IN', 200, '2026-01-05', 'REF-2026-003', 'Donasi mi instan'],
            [2, $staff1->id, null, 2, 'OUT', 80, '2026-01-20', 'TRX-2026-002', 'Distribusi ke Pengungsian Palu'],
            [3, $admin->id, 3, null, 'IN', 1000, '2026-01-05', 'REF-2026-004', 'Donasi air mineral'],
            [3, $staff1->id, null, 1, 'OUT', 500, '2026-01-18', 'TRX-2026-003', 'Air mineral ke Cianjur'],
            [6, $admin->id, 1, null, 'IN', 200, '2026-01-08', 'REF-2026-005', 'Selimut dari PMI'],
            [6, $staff1->id, null, 3, 'OUT', 100, '2026-01-25', 'TRX-2026-004', 'Selimut ke Bekasi'],
            [9, $admin->id, 1, null, 'IN', 100, '2026-02-01', 'REF-2026-006', 'P3K kit dari PMI'],
            [9, $staff1->id, null, 4, 'OUT', 50, '2026-02-10', 'TRX-2026-005', 'P3K ke Lombok'],
            [12, $admin->id, 2, null, 'IN', 30, '2026-02-05', 'REF-2026-007', 'Tenda dari BNPB'],
            [12, $staff1->id, null, 5, 'OUT', 15, '2026-02-15', 'TRX-2026-006', 'Tenda ke Semeru'],
            [10, $admin->id, 1, null, 'IN', 200, '2026-02-10', 'REF-2026-008', 'Obat dari PMI'],
            [11, $admin->id, 1, null, 'IN', 300, '2026-02-12', 'REF-2026-009', 'Masker dari PMI'],
            [11, $staff1->id, null, 1, 'OUT', 100, '2026-03-01', 'TRX-2026-007', 'Masker ke Cianjur'],
            [4, $admin->id, 3, null, 'IN', 150, '2026-03-05', 'REF-2026-010', 'Minyak goreng'],
            [4, $staff1->id, null, 2, 'OUT', 80, '2026-03-10', 'TRX-2026-008', 'Minyak ke Palu'],
            [14, $admin->id, 4, null, 'IN', 150, '2026-03-08', 'REF-2026-011', 'Sabun dari Yayasan'],
            [14, $staff1->id, null, 3, 'OUT', 70, '2026-03-15', 'TRX-2026-009', 'Sabun ke Bekasi'],
            [7, $admin->id, 4, null, 'IN', 200, '2026-03-10', 'REF-2026-012', 'Pakaian dewasa'],
            [7, $staff1->id, null, 4, 'OUT', 100, '2026-03-20', 'TRX-2026-010', 'Pakaian ke Lombok'],
            [8, $admin->id, 4, null, 'IN', 150, '2026-03-12', 'REF-2026-013', 'Pakaian anak'],
            [8, $staff1->id, null, 5, 'OUT', 70, '2026-03-22', 'TRX-2026-011', 'Pakaian ke Semeru'],
            [1, $staff1->id, null, 4, 'OUT', 150, '2026-04-01', 'TRX-2026-012', 'Beras ke Lombok'],
            [2, $staff1->id, null, 5, 'OUT', 60, '2026-04-05', 'TRX-2026-013', 'Mi ke Semeru'],
            [5, $admin->id, 3, null, 'IN', 100, '2026-04-08', 'REF-2026-014', 'Telur donasi'],
            [5, $staff1->id, null, 1, 'OUT', 60, '2026-04-12', 'TRX-2026-014', 'Telur ke Cianjur'],
            [13, $admin->id, 3, null, 'IN', 80, '2026-04-15', 'REF-2026-015', 'Terpal dari perusahaan'],
            [13, $staff1->id, null, 2, 'OUT', 40, '2026-04-20', 'TRX-2026-015', 'Terpal ke Palu'],
        ];

        foreach ($movementsData as [$pIdx, $userId, $sIdx, $tIdx, $type, $qty, $date, $ref, $notes]) {
            if (isset($products[$pIdx])) {
                StockMovement::firstOrCreate(
                    ['reference' => $ref],
                    [
                        'product_id' => $products[$pIdx]->id,
                        'user_id' => $userId,
                        'supplier_id' => $sIdx ? ($suppliers[$sIdx]->id ?? null) : null,
                        'distribution_target_id' => $tIdx ? ($targets[$tIdx]->id ?? null) : null,
                        'type' => $type,
                        'quantity' => $qty,
                        'date' => $date,
                        'reference' => $ref,
                        'notes' => $notes,
                        'is_public' => true,
                    ]
                );
            }
        }
    }
}
