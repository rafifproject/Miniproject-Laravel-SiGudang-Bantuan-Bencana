@extends('layouts.admin')

@section('title', 'Pasokan Produk & Supplier (Many-to-Many)')
@section('page_title', 'Manajemen Pasokan Produk & Lembaga Donor')
@section('page_subtitle', 'Pemetaan relasi Many-to-Many: 1 produk dapat disuplai banyak donor, 1 donor dapat menyuplai banyak produk')

@section('content')
<div class="row g-4">
  <!-- Kolom Kiri: Tabel Relasi -->
  <div class="col-lg-8">
    <div class="card border-0 shadow-sm">
      <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <div>
            <h5 class="fw-bold mb-1"><i class="bi bi-truck me-2 text-primary"></i>Daftar Pemetaan Pasokan</h5>
            <small class="text-muted">Total terhubung: <strong>{{ $mappings->total() }}</strong> relasi produk-supplier</small>
          </div>
          <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Ke Master Produk
          </a>
        </div>

        <!-- Filter -->
        <form method="GET" action="{{ route('product-suppliers.index') }}" class="row g-2 mb-3 bg-light p-3 rounded-3 border">
          <div class="col-md-5">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari produk / supplier..." value="{{ request('search') }}">
          </div>
          <div class="col-md-5">
            <select name="product_id" class="form-select form-select-sm">
              <option value="">Semua Produk</option>
              @foreach($products as $p)
                <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }} ({{ $p->sku }})</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-2 d-flex gap-1">
            <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-filter"></i></button>
            @if(request()->anyFilled(['search', 'product_id', 'supplier_id']))
              <a href="{{ route('product-suppliers.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-x-lg"></i></a>
            @endif
          </div>
        </form>

        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>Produk Logistik</th>
                <th>Supplier / Donor</th>
                <th class="text-end">Harga Pasokan Ref.</th>
                <th class="text-end" style="width: 80px;">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($mappings as $map)
                <tr>
                  <td>
                    <div class="fw-bold text-dark">{{ $map->product_name }}</div>
                    <small class="text-muted font-monospace">{{ $map->product_sku }}</small>
                  </td>
                  <td>
                    <div class="fw-semibold text-dark">{{ $map->supplier_name }}</div>
                    <span class="badge bg-secondary-subtle text-secondary small">{{ $map->supplier_type }}</span>
                  </td>
                  <td class="text-end fw-bold text-primary">
                    Rp {{ number_format($map->supplier_price, 0, ',', '.') }}
                    <small class="text-muted fw-normal">/ {{ $map->product_unit }}</small>
                  </td>
                  <td class="text-end">
                    <form method="POST" action="{{ route('product-suppliers.destroy') }}" onsubmit="return confirm('Lepas hubungan pasokan ini?')">
                      @csrf
                      @method('DELETE')
                      <input type="hidden" name="product_id" value="{{ $map->product_id }}">
                      <input type="hidden" name="supplier_id" value="{{ $map->supplier_id }}">
                      <button type="submit" class="btn btn-outline-danger btn-sm" title="Hapus Relasi">
                        <i class="bi bi-trash"></i>
                      </button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="text-center py-4 text-muted">Belum ada data pemetaan pasokan yang ditemukan.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="mt-4">
          {{ $mappings->links() }}
        </div>
      </div>
    </div>
  </div>

  <!-- Kolom Kanan: Form Tambah/Update Relasi M:N -->
  <div class="col-lg-4">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white py-3 border-0">
        <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-link-45deg me-2"></i>Hubungkan Pasokan</h5>
      </div>
      <div class="card-body p-4 pt-0">
        <form method="POST" action="{{ route('product-suppliers.store') }}">
          @csrf
          <div class="mb-3">
            <label class="form-label fw-semibold">Pilih Produk <span class="text-danger">*</span></label>
            <select name="product_id" class="form-select @error('product_id') is-invalid @enderror" required>
              <option value="">-- Pilih Produk --</option>
              @foreach($products as $prod)
                <option value="{{ $prod->id }}" {{ old('product_id') == $prod->id ? 'selected' : '' }}>
                  {{ $prod->name }} ({{ $prod->sku }})
                </option>
              @endforeach
            </select>
            @error('product_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Pilih Supplier / Lembaga <span class="text-danger">*</span></label>
            <select name="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror" required>
              <option value="">-- Pilih Supplier --</option>
              @foreach($suppliers as $sup)
                <option value="{{ $sup->id }}" {{ old('supplier_id') == $sup->id ? 'selected' : '' }}>
                  {{ $sup->name }} ({{ $sup->type }})
                </option>
              @endforeach
            </select>
            @error('supplier_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="mb-4">
            <label class="form-label fw-semibold">Harga Pasokan / Biaya Satuan (Rp) <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text">Rp</span>
              <input type="number" step="100" min="0" name="supplier_price" class="form-control @error('supplier_price') is-invalid @enderror" 
                     value="{{ old('supplier_price', 0) }}" required>
              @error('supplier_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <small class="text-muted">Nilai donasi/harga referensi pasokan dari lembaga donor terkait.</small>
          </div>

          <button type="submit" class="btn btn-primary w-100 fw-semibold">
            <i class="bi bi-save me-1"></i> Simpan Relasi Pasokan
          </button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
