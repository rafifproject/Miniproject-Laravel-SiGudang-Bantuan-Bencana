<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\DistributionTarget;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SigudangMigrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * Acceptance Criteria 1: Aplikasi dapat dijalankan & portal publik dapat diakses.
     */
    public function test_public_pages_are_accessible(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Transparansi Bantuan');

        $response = $this->get('/publik/dashboard');
        $response->assertStatus(200);

        $response = $this->get('/publik/stok');
        $response->assertStatus(200);
        $response->assertSee('Beras Premium 5kg');

        $response = $this->get('/publik/bantuan-masuk');
        $response->assertStatus(200);

        $response = $this->get('/publik/penyaluran');
        $response->assertStatus(200);

        $response = $this->get('/publik/statistik');
        $response->assertStatus(200);

        $response = $this->get('/publik/laporan');
        $response->assertStatus(200);

        $response = $this->get('/publik/tentang');
        $response->assertStatus(200);
    }

    /**
     * Acceptance Criteria 2: Form Login & Autentikasi Pengguna
     */
    public function test_login_and_redirection(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);

        // Login as admin
        $response = $this->post('/login', [
            'username' => 'admin',
            'password' => 'admin123',
        ]);
        $response->assertRedirect('/admin/dashboard');

        // Login as staff
        $this->post('/logout');
        $response = $this->post('/login', [
            'username' => 'staff1',
            'password' => 'staff123',
        ]);
        $response->assertRedirect('/staff/dashboard');
    }

    /**
     * Acceptance Criteria 3: Otorisasi Role-Based Access Control (RBAC) & HTTP 403
     */
    public function test_staff_cannot_access_admin_user_management(): void
    {
        $staff = User::where('username', 'staff1')->first();
        $response = $this->actingAs($staff)->get('/admin/users');
        $response->assertStatus(403);
    }

    /**
     * Acceptance Criteria 4: Eloquent Relationship Category-Product & Resource Controller Product
     */
    public function test_product_crud_and_relationships(): void
    {
        $admin = User::where('username', 'admin')->first();
        $category = Category::first();

        // 1. Index
        $response = $this->actingAs($admin)->get('/products');
        $response->assertStatus(200);
        $response->assertSee('Beras Premium 5kg');

        // 2. Create form
        $response = $this->actingAs($admin)->get('/products/create');
        $response->assertStatus(200);

        // 3. Store (Demonstrasikan form -> validasi -> simpan -> redirect -> tampilkan data)
        $response = $this->actingAs($admin)->post('/products', [
            'sku' => 'PRD999',
            'category_id' => $category->id,
            'name' => 'Biskuit Balita Darurat',
            'unit' => 'karton',
            'price' => 50000,
            'stock' => 100,
            'minimum_stock' => 20,
            'description' => 'Biskuit gizi darurat untuk balita pengungsi',
            'is_public' => 1,
        ]);
        $response->assertRedirect('/products');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('products', [
            'sku' => 'PRD999',
            'name' => 'Biskuit Balita Darurat',
        ]);

        $newProduct = Product::where('sku', 'PRD999')->first();

        // 4. Show
        $response = $this->actingAs($admin)->get('/products/' . $newProduct->id);
        $response->assertStatus(200);
        $response->assertSee('Biskuit Balita Darurat');

        // 5. Update
        $response = $this->actingAs($admin)->put('/products/' . $newProduct->id, [
            'sku' => 'PRD999',
            'category_id' => $category->id,
            'name' => 'Biskuit Balita Darurat Spesial',
            'unit' => 'karton',
            'price' => 55000,
            'stock' => 120,
            'minimum_stock' => 25,
            'description' => 'Diperbarui',
            'is_public' => 1,
        ]);
        $response->assertRedirect('/products');

        $this->assertDatabaseHas('products', [
            'sku' => 'PRD999',
            'name' => 'Biskuit Balita Darurat Spesial',
        ]);

        // 6. Delete
        $response = $this->actingAs($admin)->delete('/products/' . $newProduct->id);
        $response->assertRedirect('/products');
        $this->assertDatabaseMissing('products', ['sku' => 'PRD999']);
    }

    /**
     * Acceptance Criteria 5: Form Validation Failure
     */
    public function test_product_store_validation(): void
    {
        $admin = User::where('username', 'admin')->first();

        $response = $this->actingAs($admin)->post('/products', [
            'sku' => '', // Required
            'name' => '', // Required
        ]);

        $response->assertSessionHasErrors(['sku', 'category_id', 'name', 'unit', 'price', 'stock', 'minimum_stock']);
    }

    /**
     * Acceptance Criteria 6: Business Rule Stok P4 (Transaksi Masuk Atomik & Penambahan Stok)
     */
    public function test_stock_movement_in_increases_stock(): void
    {
        $admin = User::where('username', 'admin')->first();
        $product = Product::first();
        $initialStock = $product->stock;
        $supplier = Supplier::first();

        $response = $this->actingAs($admin)->post('/transaksi/barang-masuk', [
            'product_id' => $product->id,
            'supplier_id' => $supplier->id,
            'quantity' => 50,
            'date' => date('Y-m-d'),
            'reference' => 'REF-TEST-001',
            'notes' => 'Donasi uji coba',
            'is_public' => 1,
        ]);

        $response->assertRedirect('/transaksi/barang-masuk');
        $response->assertSessionHas('success');

        $product->refresh();
        $this->assertEquals($initialStock + 50, $product->stock);
    }

    /**
     * Acceptance Criteria 7: Business Rule Stok P4 (Transaksi Keluar & Validasi Stok Tidak Negatif)
     */
    public function test_stock_movement_out_prevents_negative_stock(): void
    {
        $staff = User::where('username', 'staff1')->first();
        $product = Product::first();
        $initialStock = $product->stock;
        $target = DistributionTarget::first();

        // 1. Sukses jika stok mencukupi
        $response = $this->actingAs($staff)->post('/transaksi/barang-keluar', [
            'product_id' => $product->id,
            'distribution_target_id' => $target->id,
            'quantity' => 10,
            'date' => date('Y-m-d'),
            'reference' => 'TRX-TEST-001',
            'is_public' => 1,
        ]);
        $response->assertRedirect('/transaksi/barang-keluar');
        $product->refresh();
        $this->assertEquals($initialStock - 10, $product->stock);

        // 2. Gagal dan tidak mengurangi stok jika jumlah melebihi stok yang ada
        $excessQuantity = $product->stock + 99999;
        $response = $this->actingAs($staff)->post('/transaksi/barang-keluar', [
            'product_id' => $product->id,
            'distribution_target_id' => $target->id,
            'quantity' => $excessQuantity,
            'date' => date('Y-m-d'),
            'reference' => 'TRX-TEST-FAIL',
            'is_public' => 1,
        ]);
        $response->assertSessionHasErrors(['quantity']);
        $product->refresh();
        $this->assertGreaterThanOrEqual(0, $product->stock);
    }
}
