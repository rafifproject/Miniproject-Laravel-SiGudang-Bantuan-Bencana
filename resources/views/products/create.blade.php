@extends('layouts.admin')

@section('title', 'Tambah Produk Baru')
@section('page_title', 'Tambah Produk Bantuan')
@section('page_subtitle', 'Daftarkan jenis logistik baru ke dalam master data gudang')

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-8">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white py-3 border-0">
        <div class="d-flex justify-content-between align-items-center">
          <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-box-seam me-2"></i>Formulir Data Produk</h5>
          <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Kembali
          </a>
        </div>
      </div>
      <div class="card-body p-4 pt-2">
        <form method="POST" action="{{ route('products.store') }}">
          @csrf

          <div class="row g-3">
            <!-- SKU -->
            <div class="col-md-6">
              <label for="sku" class="form-label fw-semibold">Kode SKU <span class="text-danger">*</span></label>
              <input type="text" class="form-control @error('sku') is-invalid @enderror" id="sku" name="sku" 
                     value="{{ old('sku') }}" placeholder="Contoh: PRD016 atau SKU-101" required>
              @error('sku')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <small class="text-muted">Kode unik pengenal produk (SKU)</small>
            </div>

            <!-- Kategori -->
            <div class="col-md-6">
              <label for="category_id" class="form-label fw-semibold">Kategori Logistik <span class="text-danger">*</span></label>
              <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                <option value="">-- Pilih Kategori --</option>
                @foreach($categories as $cat)
                  <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                    {{ $cat->name }}
                  </option>
                @endforeach
              </select>
              @error('category_id')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <!-- Nama Produk -->
            <div class="col-12">
              <label for="name" class="form-label fw-semibold">Nama Produk Bantuan <span class="text-danger">*</span></label>
              <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" 
                     value="{{ old('name') }}" placeholder="Contoh: Beras Premium 5kg, Selimut Tebal..." required>
              @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <!-- Satuan -->
            <div class="col-md-6">
              <label for="unit" class="form-label fw-semibold">Satuan Kemasan <span class="text-danger">*</span></label>
              <input type="text" class="form-control @error('unit') is-invalid @enderror" id="unit" name="unit" 
                     value="{{ old('unit', 'pcs') }}" placeholder="Contoh: pcs, karton, box, karung..." required>
              @error('unit')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <!-- Harga / Nilai Satuan -->
            <div class="col-md-6">
              <label for="price" class="form-label fw-semibold">Estimasi Harga Satuan (Rp) <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text">Rp</span>
                <input type="number" step="100" min="0" class="form-control @error('price') is-invalid @enderror" 
                       id="price" name="price" value="{{ old('price', 0) }}" required>
                @error('price')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <small class="text-muted">Untuk penilaian valuasi logistik bencana</small>
            </div>

            <!-- Stok Awal -->
            <div class="col-md-6">
              <label for="stock" class="form-label fw-semibold">Stok Awal Fisik <span class="text-danger">*</span></label>
              <input type="number" min="0" class="form-control @error('stock') is-invalid @enderror" 
                     id="stock" name="stock" value="{{ old('stock', 0) }}" required>
              @error('stock')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <small class="text-muted">Jumlah unit fisik yang tersedia saat ini</small>
            </div>

            <!-- Batas Minimum Stok -->
            <div class="col-md-6">
              <label for="minimum_stock" class="form-label fw-semibold">Batas Stok Minimum <span class="text-danger">*</span></label>
              <input type="number" min="0" class="form-control @error('minimum_stock') is-invalid @enderror" 
                     id="minimum_stock" name="minimum_stock" value="{{ old('minimum_stock', 10) }}" required>
              @error('minimum_stock')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <small class="text-muted">Pemicu alert otomatis saat stok menipis</small>
            </div>

            <!-- Keterangan -->
            <div class="col-12">
              <label for="description" class="form-label fw-semibold">Spesifikasi / Deskripsi</label>
              <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" 
                        placeholder="Catatan tambahan spesifikasi bantuan...">{{ old('description') }}</textarea>
              @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <!-- Visibilitas Publik -->
            <div class="col-12">
              <div class="form-check form-switch p-2 ps-5 bg-light rounded-2 border">
                <input class="form-check-input" type="checkbox" role="switch" id="is_public" name="is_public" value="1" 
                       {{ old('is_public', 1) ? 'checked' : '' }}>
                <label class="form-check-label fw-semibold text-dark" for="is_public">
                  Tampilkan pada Portal Publik
                </label>
                <div class="small text-muted">Bila dicentang, ketersediaan produk ini akan dipublikasikan secara transparan ke publik.</div>
              </div>
            </div>

            <!-- Submit Buttons -->
            <div class="col-12 d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
              <a href="{{ route('products.index') }}" class="btn btn-secondary px-4">Batal</a>
              <button type="submit" class="btn btn-primary px-4 fw-semibold">
                <i class="bi bi-save me-1"></i> Simpan Produk
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
