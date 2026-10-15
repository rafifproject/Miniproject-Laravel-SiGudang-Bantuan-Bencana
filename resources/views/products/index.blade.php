@extends('layouts.admin')

@section('title', 'Manajemen Produk Logistik')
@section('page_title', 'Master Data Produk Logistik')
@section('page_subtitle', 'Kelola persediaan barang bantuan bencana, batas stok minimum, dan rantai pasokan')

@section('content')
<div class="card border-0 shadow-sm mb-4">
  <div class="card-body p-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
      <div>
        <h5 class="fw-bold mb-1"><i class="bi bi-box-seam me-2 text-primary"></i>Katalog Persediaan Bantuan</h5>
        <p class="text-muted small mb-0">Total terdaftar: <strong>{{ $products->total() }}</strong> barang logistik bencana</p>
      </div>
      <div class="d-flex gap-2">
        <a href="{{ route('product-suppliers.index') }}" class="btn btn-outline-primary fw-semibold">
          <i class="bi bi-truck me-1"></i> Pasokan Supplier
        </a>
        <a href="{{ route('products.create') }}" class="btn btn-primary fw-semibold">
          <i class="bi bi-plus-lg me-1"></i> Tambah Produk Baru
        </a>
      </div>
    </div>

    <!-- Filter & Search Bar -->
    <form method="GET" action="{{ route('products.index') }}" class="row g-2 mb-4 bg-light p-3 rounded-3 border">
      <div class="col-lg-4 col-md-6">
        <label class="form-label small fw-semibold text-muted mb-1">Pencarian</label>
        <div class="input-group">
          <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
          <input type="text" name="search" class="form-control" placeholder="Cari nama, SKU..." value="{{ request('search') }}">
        </div>
      </div>
      <div class="col-lg-3 col-md-6">
        <label class="form-label small fw-semibold text-muted mb-1">Kategori</label>
        <select name="category_id" class="form-select">
          <option value="">Semua Kategori</option>
          @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
              {{ $cat->name }}
            </option>
          @endforeach
        </select>
      </div>
      <div class="col-lg-2 col-md-4">
        <label class="form-label small fw-semibold text-muted mb-1">Status Stok</label>
        <select name="status" class="form-select">
          <option value="">Semua Status</option>
          <option value="kritis" {{ request('status') === 'kritis' ? 'selected' : '' }}>⚠️ Kritis / Menipis</option>
          <option value="aman" {{ request('status') === 'aman' ? 'selected' : '' }}>✅ Stok Aman</option>
        </select>
      </div>
      <div class="col-lg-2 col-md-4">
        <label class="form-label small fw-semibold text-muted mb-1">Urutkan</label>
        <select name="sort" class="form-select">
          <option value="name" {{ ($sort ?? '') === 'name' ? 'selected' : '' }}>Nama Produk</option>
          <option value="sku" {{ ($sort ?? '') === 'sku' ? 'selected' : '' }}>Kode SKU</option>
          <option value="stock" {{ ($sort ?? '') === 'stock' ? 'selected' : '' }}>Jumlah Stok</option>
          <option value="price" {{ ($sort ?? '') === 'price' ? 'selected' : '' }}>Estimasi Harga</option>
        </select>
      </div>
      <div class="col-lg-1 col-md-4 d-flex align-items-end gap-1">
        <button type="submit" class="btn btn-primary w-100" title="Terapkan Filter">
          <i class="bi bi-filter"></i>
        </button>
        @if(request()->anyFilled(['search', 'category_id', 'status', 'sort']))
          <a href="{{ route('products.index') }}" class="btn btn-outline-secondary" title="Reset">
            <i class="bi bi-arrow-counterclockwise"></i>
          </a>
        @endif
      </div>
    </form>

    <!-- Table of Products -->
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th style="width: 110px;">SKU</th>
            <th>Nama Produk Bantuan</th>
            <th>Kategori</th>
            <th>Harga Satuan</th>
            <th class="text-center">Stok Fisik</th>
            <th class="text-center">Min. Stok</th>
            <th class="text-center">Status</th>
            <th class="text-end" style="width: 160px;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($products as $product)
            <tr>
              <td>
                <span class="badge bg-secondary-subtle text-secondary font-monospace">{{ $product->sku }}</span>
              </td>
              <td>
                <div class="fw-bold text-dark">{{ $product->name }}</div>
                <small class="text-muted">{{ Str::limit($product->description, 50, '...') }}</small>
              </td>
              <td>
                <span class="badge bg-info-subtle text-info border border-info-subtle">
                  {{ $product->category->name ?? '-' }}
                </span>
              </td>
              <td>
                <span class="fw-semibold">{{ $product->formatted_price }}</span>
                <span class="text-muted small">/ {{ $product->unit }}</span>
              </td>
              <td class="text-center">
                <span class="fw-bold {{ $product->isLowStock() ? 'text-danger' : 'text-dark' }}">
                  {{ number_format($product->stock) }}
                </span>
                <span class="text-muted small">{{ $product->unit }}</span>
              </td>
              <td class="text-center text-muted">
                {{ number_format($product->minimum_stock) }} {{ $product->unit }}
              </td>
              <td class="text-center">
                @if($product->isLowStock())
                  <span class="badge bg-danger-subtle text-danger px-2 py-1 rounded-pill">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>Menipis
                  </span>
                @else
                  <span class="badge bg-success-subtle text-success px-2 py-1 rounded-pill">
                    <i class="bi bi-check-circle-fill me-1"></i>Aman
                  </span>
                @endif
              </td>
              <td class="text-end">
                <div class="btn-group btn-group-sm">
                  <a href="{{ route('products.show', $product) }}" class="btn btn-outline-info" title="Detail Produk">
                    <i class="bi bi-eye"></i>
                  </a>
                  <a href="{{ route('products.edit', $product) }}" class="btn btn-outline-warning" title="Edit Produk">
                    <i class="bi bi-pencil"></i>
                  </a>
                  <button type="button" class="btn btn-outline-danger" title="Hapus Produk" 
                          data-bs-toggle="modal" data-bs-target="#deleteProductModal{{ $product->id }}">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>

                <!-- Delete Confirmation Modal -->
                <div class="modal fade text-start" id="deleteProductModal{{ $product->id }}" tabindex="-1">
                  <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                      <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold">Hapus Produk</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>
                      <div class="modal-body py-4 text-center">
                        <div class="text-danger mb-3" style="font-size: 3rem;">
                          <i class="bi bi-exclamation-circle"></i>
                        </div>
                        <p class="mb-1">Apakah Anda yakin ingin menghapus produk ini?</p>
                        <h6 class="fw-bold text-dark">{{ $product->name }} ({{ $product->sku }})</h6>
                        <small class="text-muted">Produk yang memiliki histori transaksi stok tidak dapat dihapus.</small>
                      </div>
                      <div class="modal-footer border-0 pt-0 justify-content-center">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                        <form method="POST" action="{{ route('products.destroy', $product) }}">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-danger px-4 fw-semibold">Ya, Hapus</button>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-2 text-muted"></i>
                Belum ada data produk logistik yang sesuai dengan pencarian / filter.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Pagination links (Slide 24 & 39) -->
    <div class="mt-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
      <div class="text-muted small">
        Menampilkan <strong>{{ $products->firstItem() ?? 0 }}</strong> - <strong>{{ $products->lastItem() ?? 0 }}</strong> dari <strong>{{ $products->total() }}</strong> produk
      </div>
      <div>
        {{ $products->links() }}
      </div>
    </div>
  </div>
</div>
@endsection
