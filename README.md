# Sistem Informasi Gudang Bantuan Bencana (SIGudang) — Laravel Edition

> **Tugas Pertemuan 5: Laravel Fundamental**  
> *Mengubah Inventory Management System P4 (PHP Native) Menjadi Aplikasi Laravel Berbasis Arsitektur MVC Lengkap*

---

## 📋 Daftar Isi
1. [Tentang Sistem & Tujuan Migrasi](#-tentang-sistem--tujuan-migrasi)
2. [Arsitektur MVC & Alur Request–Response](#-arsitektur-mvc--alur-requestresponse)
3. [Teknologi & Stack yang Digunakan](#-teknologi--stack-yang-digunakan)
4. [Skema Database, Migrasi, & Relasi Eloquent](#-skema-database-migrasi--relasi-eloquent)
5. [Akun Pengguna Default](#-akun-pengguna-default)
6. [Panduan Instalasi & Menjalankan](#-panduan-instalasi--menjalankan)
7. [Daftar Rute (Routes List)](#-daftar-rute-routes-list)
8. [Demonstrasi Alur Lengkap (Form → Validasi → Simpan → Redirect → Tampil)](#-demonstrasi-alur-lengkap)
9. [Integritas Aturan Bisnis P4 yang Dipertahankan](#-integritas-aturan-bisnis-p4-yang-dipertahankan)
10. [Hasil Pengujian Otomatis (Feature Tests)](#-hasil-pengujian-otomatis-feature-tests)

---

## 📦 Tentang Sistem & Tujuan Migrasi

**SIGudang Bantuan Bencana** adalah sistem informasi manajemen inventaris dan rantai pasok logistik kemanusiaan darurat. Sistem ini dirancang untuk:
1. **Transparansi Publik**: Masyarakat, donatur, dan relawan dapat memantau posisi persediaan sembako, obat-obatan, tenda pengungsian, dan riwayat penyaluran secara langsung tanpa perlu autentikasi.
2. **Integritas Transaksi Atomik**: Pencatatan barang masuk (donasi) dan barang keluar (penyaluran ke posko pengungsian) dilakukan dalam transaksi database terisolasi dengan *Row-Level Locking*, menjamin stok fisik akurat dan tidak akan pernah bernilai negatif.
3. **Kontrol Akses Berbasis Peran (RBAC)**: Memisahkan hak wewenang antara **Administrator** (pengelola master data, pasokan, laporan analitis, dan manajemen pengguna) dengan **Staff Operasional** (pencatat arus mutasi barang posko).

### Perubahan Arsitektur dari P4 (PHP Native) ke P5 (Laravel MVC):
| Aspek | P4 PHP Native | P5 Laravel MVC |
|---|---|---|
| **Struktur Database** | `database/schema.sql` manual | `database/migrations` via Artisan |
| **Data Pengujian** | `database/seed.sql` manual | `database/seeders` & Eloquent Factories |
| **Kueri Data** | PDO Prepared Statements manual | Eloquent ORM & Query Builder |
| **Alur Controller** | File skrip procedural terpisah | Resource Controllers (`ProductController`, dll) |
| **Tampilan / View** | HTML + PHP include tags | Blade Template Engine (`@extends`, `@section`, components) |
| **Keamanan Form** | Token manual di session | Token `@csrf` otomatis & validasi Request |
| **Autentikasi & Otorisasi** | Variabel `$_SESSION` manual | Laravel Auth Guard + Role Middleware (`role:admin`, `role:staff`) |

---

## 🏛 Arsitektur MVC & Alur Request–Response

Aplikasi ini dibangun murni mengikuti pola desain **Model-View-Controller (MVC)** yang diinstruksikan pada Pertemuan 5:

```text
               HTTP Request (Browser / Client)
                             │
                             ▼
                    routes/web.php (Route)
                             │
                             ▼
              app/Http/Controllers/ (Controller)
              [Validasi Input $request->validate()]
                             │
            ┌────────────────┴────────────────┐
            ▼                                 ▼
   app/Models/ (Model)              resources/views/ (Blade)
   [Eloquent ORM & Relations]       [Layout, Komponen, Escaping]
            │                                 │
            ▼                                 │
     MySQL Database                           │
            │                                 │
            └────────────────┬────────────────┘
                             │
                             ▼
               HTTP Response (HTML / Redirect)
```

1. **Request**: Klien mengirim permintaan HTTP (misal: `POST /products` atau `GET /products`).
2. **Route**: Rute di `routes/web.php` meneruskan permintaan ke Controller yang sesuai (misal: `ProductController@store` atau `ProductController@index`).
3. **Controller**: Menerima request, menjalankan validasi `$request->validate()`, memanggil Model/Service untuk manipulasi data, dan memilih bentuk Response.
4. **Model**: Merepresentasikan entitas bisnis, relasi tabel database (1:1, 1:N, M:N), aturan *mass-assignment* (`$fillable`), dan casting tipe data.
5. **Database**: MySQL menyimpan data persediaan secara permanen dengan *Foreign Key Constraints* dan transaksi ACID.
6. **Response**: Controller mengembalikan tampilan view Blade atau pengalihan rute (`redirect()->route(...)`) dengan *Flash Message*.

---

## 🛠 Teknologi & Stack yang Digunakan

- **Framework**: Laravel 10.x
- **Bahasa Pemrograman**: PHP 8.1+
- **Database**: MySQL 8.x / MariaDB (Database: `inventory_db`)
- **Frontend / Styling**: Vanilla CSS Modern kustom (`assets/css/style.css`), Bootstrap 5.3, Bootstrap Icons, Google Fonts (Inter)
- **Visualisasi Data**: Chart.js 4.4 (Tren Arus Bantuan Bulanan, Komposisi Kategori, Volume per Posko)

---

## 🗄 Skema Database, Migrasi, & Relasi Eloquent

### Skema Relasional Tabel:
1. `users` (id, username, password, role ['admin', 'staff'], full_name, email, is_active, timestamps)
2. `user_profiles` (id, user_id [FK UNIQUE], full_name, phone, address, timestamps) — **Relasi 1 : 1** dengan `users`.
3. `categories` (id, name [UNIQUE], description, timestamps) — **Relasi 1 : N** dengan `products`.
4. `products` (id, category_id [FK], sku [UNIQUE], name, unit, price, stock, minimum_stock, description, is_public, timestamps).
5. `suppliers` (id, name, type, contact, email, address, description, is_public, timestamps).
6. `product_suppliers` (product_id [FK], supplier_id [FK], supplier_price, timestamps) — **Relasi Many-to-Many (M : N)** antara `products` dan `suppliers`.
7. `distribution_targets` (id, name, location, description, timestamps).
8. `stock_movements` (id, product_id [FK], user_id [FK], supplier_id [FK NULL], distribution_target_id [FK NULL], type ['IN', 'OUT'], quantity, date, reference, notes, is_public, timestamps).

### Implementasi Relasi Eloquent:
- `Product` $\leftrightarrow$ `Category`: `$product->category()` (`belongsTo`), `$category->products()` (`hasMany`).
- `Product` $\leftrightarrow$ `Supplier`: `$product->suppliers()` (`belongsToMany('product_suppliers')->withPivot('supplier_price')`).
- `Product` $\leftrightarrow$ `StockMovement`: `$product->stockMovements()` (`hasMany`).
- `User` $\leftrightarrow$ `UserProfile`: `$user->profile()` (`hasOne`), `$profile->user()` (`belongsTo`).
- `StockMovement` $\leftrightarrow$ `DistributionTarget`: `$movement->distributionTarget()` (`belongsTo`).

---

## 👤 Akun Pengguna Default

Database Seeder secara otomatis menginisialisasi 3 akun operator dengan hak akses RBAC:

| Peran (Role) | Username | Password | Deskripsi Wewenang |
|---|---|---|---|
| **Administrator** | `admin` | `admin123` | Akses penuh: Dashboard, Master Produk, Kategori, Supplier, Pasokan M:N, Transaksi Masuk/Keluar, Laporan, & Manajemen Pengguna. |
| **Staff Lapangan 1** | `staff1` | `staff123` | Akses operasional: Dashboard Staff, Catat Barang Masuk, Catat Penyaluran Posko, Riwayat Mutasi. |
| **Staff Lapangan 2** | `staff2` | `staff123` | Akses operasional posko lapangan 2. |

> *Catatan: Halaman login juga dilengkapi tombol klik-cepat untuk mengisi kredensial demo secara otomatis.*

---

## 🚀 Panduan Instalasi & Menjalankan

### 1. Prasyarat:
- PHP >= 8.1 dengan ekstensi `pdo_mysql`, `mbstring`, `openssl`.
- Composer terpasang di sistem.
- MySQL Server aktif pada port `3306`.

### 2. Konfigurasi Environment (`.env`):
Pastikan konfigurasi koneksi database di berkas `.env` sudah sesuai:
```env
APP_NAME="SIGudang Bencana"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=inventory_db
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Migrasi & Seed Database Otomatis:
Jalankan perintah berikut pada terminal:
```bash
php artisan migrate:fresh --seed
```
*Perintah ini akan membuat 11 tabel migrasi dan mengisi data awal (users, profiles, categories, suppliers, products, product_suppliers, targets, & stock_movements).*

### 4. Menjalankan Server Lokal:
```bash
php artisan serve
```
Aplikasi dapat dibuka melalui browser pada alamat: **`http://127.0.0.1:8000`**

---

## 🛣 Daftar Rute (Routes List)

Semua rute diatur menggunakan rute bernama (*named routes*), pengelompokan (*grouping*), dan *resource controller*:

```bash
php artisan route:list
```

### Ringkasan Rute Utama:
- **Portal Publik**:
  - `GET /` $\rightarrow$ `home`
  - `GET /publik/dashboard` $\rightarrow$ `public.dashboard`
  - `GET /publik/stok` $\rightarrow$ `public.stok`
  - `GET /publik/bantuan-masuk` $\rightarrow$ `public.masuk`
  - `GET /publik/penyaluran` $\rightarrow$ `public.penyaluran`
  - `GET /publik/statistik` $\rightarrow$ `public.statistik`
  - `GET /publik/laporan` $\rightarrow$ `public.laporan`
  - `GET /publik/tentang` $\rightarrow$ `public.tentang`
- **Autentikasi & Akun**:
  - `GET /login`, `POST /login`, `POST /logout`
  - `GET /profil`, `PUT /profil`, `PUT /profil/password`
- **Operasional Bersama (Admin & Staff)**:
  - `GET /transaksi/barang-masuk`, `POST /transaksi/barang-masuk`
  - `GET /transaksi/barang-keluar`, `POST /transaksi/barang-keluar`
  - `GET /transaksi/mutasi-stok`
  - `GET /staff/dashboard`
- **Administrasi (Admin Only)**:
  - `GET /admin/dashboard`
  - `Route::resource('products', ProductController::class)` (CRUD 7 methods)
  - `Route::resource('categories', CategoryController::class)`
  - `Route::resource('suppliers', SupplierController::class)`
  - `GET|POST|DELETE /admin/product-suppliers`
  - `GET /admin/laporan`
  - `Route::resource('users', UserController::class)`

---

## 🔁 Demonstrasi Alur Lengkap

### Skenario: Form Tambah Produk → Validasi → Simpan → Redirect → Tampilkan Data

1. **Membuka Form**:
   - Operator membuka URL `http://127.0.0.1:8000/products/create` (melalui tombol *Tambah Produk Baru* di halaman produk).
   - `ProductController@create` memanggil view `resources/views/products/create.blade.php`.
2. **Validasi Request Server-Side**:
   - Pengguna mengirimkan form via metode `POST` ke `/products`.
   - `ProductController@store` mengeksekusi:
     ```php
     $validated = $request->validate([
         'sku' => ['required', 'string', 'max:50', 'unique:products,sku'],
         'category_id' => ['required', 'exists:categories,id'],
         'name' => ['required', 'string', 'max:200'],
         'unit' => ['required', 'string', 'max:30'],
         'price' => ['required', 'numeric', 'min:0'],
         'stock' => ['required', 'integer', 'min:0'],
         'minimum_stock' => ['required', 'integer', 'min:0'],
         'description' => ['nullable', 'string', 'max:1000'],
     ]);
     ```
   - Jika validasi gagal (misal SKU kosong atau duplikat), Laravel otomatis mengembalikan user kembali ke form dengan error message (`@error('sku')`) dan data input lama (`old('sku')`).
3. **Penyimpanan Data Eloquent**:
   - Jika valid, model `Product::create($validated)` mengeksekusi kueri `INSERT` ke tabel `products`.
4. **Redirect & Flash Message**:
   - Controller melakukan pengalihan:
     ```php
     return redirect()->route('products.index')
         ->with('success', 'Produk logistik baru berhasil ditambahkan.');
     ```
5. **Menampilkan Data**:
   - Browser menerima redirect dan memuat rute `products.index`.
   - Komponen alert `<x-alert type="success">` menampilkan pesan sukses berwarna hijau.
   - Produk baru langsung muncul pada tabel katalog persediaan dan dapat dicari/difilter.

---

## 🔒 Integritas Aturan Bisnis P4 yang Dipertahankan

1. **Transaksi Atomik Barang Masuk**:
   Setiap pencatatan donasi masuk dibungkus dalam blok `DB::transaction(...)`. Penambahan stok fisik (`$product->increment('stock', $qty)`) dan pencatatan riwayat audit `stock_movements` terjadi secara simultan tanpa risiko *partial insert*.
2. **Row-Level Locking Penyaluran (Barang Keluar)**:
   Pencatatan pengeluaran bantuan menggunakan `Product::lockForUpdate()->findOrFail($id)`. Sistem memvalidasi secara ketat apakah `$product->stock >= $quantity`. Jika kuantitas keluar melebihi stok fisik, sistem melempar `ValidationException` dan transaksi otomatis di-*rollback*, sehingga **stok fisik mustahil bernilai negatif**.
3. **Integritas Penghapusan (Foreign Key Safety)**:
   Produk yang sudah memiliki rekam jejak mutasi stok tidak dapat dihapus (`restrictOnDelete` dan validasi controller) guna melindungi integritas jejak audit (*audit trail*). Hal yang sama berlaku pada kategori dan akun pengguna yang memiliki histori transaksi.
4. **Proteksi Otorisasi 403 Forbidden**:
   Jika pengguna dengan peran `staff` mencoba mengakses modul master data pengguna di `/admin/users`, permintaan langsung dicegat oleh `RoleMiddleware` dan menghasilkan respons HTTP 403 (*Akses Terlarang*) yang ramah pengguna.

---

## ✅ Hasil Pengujian Otomatis (Feature Tests)

Unit & Feature Test telah disiapkan pada `tests/Feature/SigudangMigrationTest.php` untuk memvalidasi seluruh kriteria penerimaan (*Acceptance Criteria*):

```text
PASS  Tests\Feature\SigudangMigrationTest
✓ public pages are accessible
✓ login and redirection
✓ staff cannot access admin user management (HTTP 403)
✓ product crud and relationships
✓ product store validation
✓ stock movement in increases stock (Atomic Transaction)
✓ stock movement out prevents negative stock (Row-Level Locking)

Tests:    7 passed (50 assertions)
Duration: 2.23s
```

Semua kriteria penerimaan Pertemuan 5 berhasil terpenuhi secara sempurna! 🚀
