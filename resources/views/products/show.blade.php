@extends('layouts.admin')

@section('title', 'Detail Produk - ' . $product->name)
@section('page_title', 'Rincian Produk Bantuan')
@section('page_subtitle', $product->name . ' (' . $product->sku . ')')

@section('content')
<div class="row g-4">
  <!-- Kolom Kiri: Info Produk -->
  <div class="col-lg-5">
    <div class="card border-0 shadow-sm mb-4">
      <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-info-circle me-2"></i>Informasi Logistik</h5>
        <a href="{{ route('products.edit', $product) }}" class="btn btn-warning btn-sm fw-semibold">
          <i class="bi bi-pencil me-1"></i> Edit
        </a>
      </div>
      <div class="card-body p-4 pt-0">
        <div class="mb-4 text-center p-3 bg-light rounded-3 border">
          <span class="badge bg-secondary mb-2 font-monospace">{{ $product->sku }}</span>
          <h4 class="fw-bold text-dark mb-1">{{ $product->name }}</h4>
          <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-1">
            {{ $product->category->name ?? '-' }}
          </span>
        </div>

        <ul class="list-group list-group-flush small">
          <li class="list-group-item d-flex justify-content-between px-0 py-2">
            <span class="text-muted">Status Persediaan:</span>
            <span>
              @if($product->isLowStock())
                <span class="badge bg-danger-subtle text-danger px-2 py-1 rounded-pill">
                  <i class="bi bi-exclamation-triangle-fill me-1"></i>Menipis (Kritis)
                </span>
              @else
                <span class="badge bg-success-subtle text-success px-2 py-1 rounded-pill">
                  <i class="bi bi-check-circle-fill me-1"></i>Aman
                </span>
              @endif
            </span>
          </li>
          <li class="list-group-item d-flex justify-content-between px-0 py-2">
            <span class="text-muted">Stok Fisik Tersedia:</span>
            <span class="fw-bold fs-6 {{ $product->isLowStock() ? 'text-danger' : 'text-primary' }}">
              {{ number_format($product->stock) }} {{ $product->unit }}
            </span>
          </li>
          <li class="list-group-item d-flex justify-content-between px-0 py-2">
            <span class="text-muted">Batas Minimum Stok:</span>
            <span class="fw-semibold">{{ number_format($product->minimum_stock) }} {{ $product->unit }}</span>
          </li>
          <li class="list-group-item d-flex justify-content-between px-0 py-2">
            <span class="text-muted">Estimasi Harga Satuan:</span>
            <span class="fw-semibold">{{ $product->formatted_price }} / {{ $product->unit }}</span>
          </li>
          <li class="list-group-item d-flex justify-content-between px-0 py-2">
            <span class="text-muted">Total Nilai Stok:</span>
            <span class="fw-bold text-success">
              Rp {{ number_format($product->stock * $product->price, 0, ',', '.') }}
            </span>
          </li>
          <li class="list-group-item d-flex justify-content-between px-0 py-2">
            <span class="text-muted">Visibilitas Publik:</span>
            <span>
              @if($product->is_public)
                <span class="badge bg-primary-subtle text-primary">Publik (Terbuka)</span>
              @else
                <span class="badge bg-secondary-subtle text-secondary">Internal Khusus</span>
              @endif
            </span>
          </li>
        </ul>

        @if($product->description)
          <div class="mt-3 p-3 bg-light rounded-2 border">
            <small class="text-muted d-block fw-semibold mb-1">Keterangan / Catatan Tambahan:</small>
            <p class="small text-secondary mb-0">{{ $product->description }}</p>
          </div>
        @endif
      </div>
    </div>

    <!-- Lembaga Donor / Pasokan Supplier Terkait -->
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-truck me-2 text-primary"></i>Lembaga Donor & Supplier</h6>
        <a href="{{ route('product-suppliers.index', ['product_id' => $product->id]) }}" class="btn btn-outline-primary btn-sm">
          Kelola
        </a>
      </div>
      <div class="card-body p-4 pt-0">
        @if($product->suppliers->isNotEmpty())
          <ul class="list-group list-group-flush small">
            @foreach($product->suppliers as $sup)
              <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                <div>
                  <div class="fw-semibold text-dark">{{ $sup->name }}</div>
                  <small class="text-muted">{{ $sup->type }}</small>
                </div>
                <div class="text-end">
                  <span class="fw-bold text-primary">Rp {{ number_format($sup->pivot->supplier_price, 0, ',', '.') }}</span>
                </div>
              </li>
            @endforeach
          </ul>
        @else
          <div class="text-center py-3 text-muted small">
            Belum ada supplier/lembaga donor yang dipetakan untuk produk ini.
          </div>
        @endif
      </div>
    </div>
  </div>

  <!-- Kolom Kanan: Histori Mutasi Transaksi Produk -->
  <div class="col-lg-7">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-clock-history me-2"></i>Histori Transaksi Produk</h5>
        <div class="d-flex gap-2">
          <a href="{{ route('barang-masuk.index') }}" class="btn btn-success btn-sm">
            <i class="bi bi-plus-circle me-1"></i>Catat Masuk
          </a>
          <a href="{{ route('barang-keluar.index') }}" class="btn btn-danger btn-sm">
            <i class="bi bi-dash-circle me-1"></i>Penyaluran
          </a>
        </div>
      </div>
      <div class="card-body p-4 pt-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle small mb-0">
            <thead class="table-light">
              <tr>
                <th>Tanggal</th>
                <th>Tipe</th>
                <th>Jumlah</th>
                <th>Referensi</th>
                <th>Asal / Tujuan</th>
              </tr>
            </thead>
            <tbody>
              @forelse($product->stockMovements as $mov)
                <tr>
                  <td class="text-muted">{{ $mov->date->format('d/m/Y') }}</td>
                  <td>
                    @if($mov->type === 'IN')
                      <span class="badge bg-success-subtle text-success">
                        <i class="bi bi-arrow-down-left me-1"></i>Masuk
                      </span>
                    @else
                      <span class="badge bg-warning-subtle text-warning-emphasis">
                        <i class="bi bi-arrow-up-right me-1"></i>Keluar
                      </span>
                    @endif
                  </td>
                  <td class="fw-bold {{ $mov->type === 'IN' ? 'text-success' : 'text-danger' }}">
                    {{ $mov->type === 'IN' ? '+' : '-' }}{{ number_format($mov->quantity) }} {{ $product->unit }}
                  </td>
                  <td class="font-monospace text-muted">{{ $mov->reference ?? '-' }}</td>
                  <td>
                    @if($mov->type === 'IN')
                      <span class="text-dark">{{ $mov->supplier->name ?? 'Donasi Langsung' }}</span>
                    @else
                      <span class="text-dark">{{ $mov->distributionTarget->name ?? 'Posko Lapangan' }}</span>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center py-4 text-muted">
                    Belum ada riwayat pergerakan stok untuk produk ini.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
