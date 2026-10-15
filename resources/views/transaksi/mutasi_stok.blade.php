@extends('layouts.admin')

@section('title', 'Riwayat Mutasi Stok')
@section('page_title', 'Audit Trail Mutasi Stok Logistik')
@section('page_subtitle', 'Seluruh rekaman historis pergerakan keluar-masuk barang bantuan bencana secara transparan')

@section('content')
<div class="card border-0 shadow-sm mb-4">
  <div class="card-body p-4">
    <!-- Header Card -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
      <div>
        <h5 class="fw-bold mb-1"><i class="bi bi-arrow-left-right me-2 text-primary"></i>Lembar Audit Mutasi Inventaris</h5>
        <small class="text-muted">Total catatan: <strong>{{ $movements->total() }}</strong> entri transaksi</small>
      </div>
      <div class="d-flex gap-2">
        <a href="{{ route('barang-masuk.index') }}" class="btn btn-success btn-sm fw-semibold">
          <i class="bi bi-box-arrow-in-down me-1"></i> Catat Masuk
        </a>
        <a href="{{ route('barang-keluar.index') }}" class="btn btn-danger btn-sm fw-semibold">
          <i class="bi bi-box-arrow-up me-1"></i> Penyaluran
        </a>
      </div>
    </div>

    <!-- Filter & Search Form -->
    <form method="GET" action="{{ route('mutasi-stok.index') }}" class="row g-2 mb-4 bg-light p-3 rounded-3 border">
      <div class="col-lg-3 col-md-6">
        <label class="form-label small fw-semibold text-muted mb-1">Cari Referensi / Catatan</label>
        <div class="input-group input-group-sm">
          <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
          <input type="text" name="search" class="form-control" placeholder="No. ref / catatan..." value="{{ request('search') }}">
        </div>
      </div>
      <div class="col-lg-2 col-md-6">
        <label class="form-label small fw-semibold text-muted mb-1">Arah Mutasi</label>
        <select name="type" class="form-select form-select-sm">
          <option value="">Semua (IN & OUT)</option>
          <option value="IN" {{ request('type') === 'IN' ? 'selected' : '' }}>📥 Masuk (IN)</option>
          <option value="OUT" {{ request('type') === 'OUT' ? 'selected' : '' }}>📤 Keluar (OUT)</option>
        </select>
      </div>
      <div class="col-lg-3 col-md-6">
        <label class="form-label small fw-semibold text-muted mb-1">Produk Logistik</label>
        <select name="product_id" class="form-select form-select-sm">
          <option value="">Semua Produk</option>
          @foreach($products as $p)
            <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>
              {{ $p->name }}
            </option>
          @endforeach
        </select>
      </div>
      <div class="col-lg-3 col-md-6">
        <label class="form-label small fw-semibold text-muted mb-1">Rentang Tanggal</label>
        <div class="input-group input-group-sm">
          <input type="date" name="date_start" class="form-control" value="{{ request('date_start') }}">
          <span class="input-group-text">s/d</span>
          <input type="date" name="date_end" class="form-control" value="{{ request('date_end') }}">
        </div>
      </div>
      <div class="col-lg-1 col-md-12 d-flex align-items-end gap-1">
        <button type="submit" class="btn btn-primary btn-sm w-100" title="Filter"><i class="bi bi-filter"></i></button>
        @if(request()->anyFilled(['search', 'type', 'product_id', 'date_start', 'date_end']))
          <a href="{{ route('mutasi-stok.index') }}" class="btn btn-outline-secondary btn-sm" title="Reset"><i class="bi bi-arrow-counterclockwise"></i></a>
        @endif
      </div>
    </form>

    <!-- Audit Trail Table -->
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th style="width: 100px;">Tanggal</th>
            <th style="width: 130px;">No. Referensi</th>
            <th>Produk Bantuan</th>
            <th class="text-center" style="width: 110px;">Arus</th>
            <th class="text-end">Jumlah</th>
            <th>Pihak Terkait (Sumber / Tujuan)</th>
            <th>Petugas Operator</th>
            <th class="text-center">Publik</th>
          </tr>
        </thead>
        <tbody>
          @forelse($movements as $mov)
            <tr>
              <td class="text-muted small">{{ $mov->date->format('d/m/Y') }}</td>
              <td>
                <span class="badge bg-secondary-subtle text-secondary font-monospace">{{ $mov->reference ?? '-' }}</span>
              </td>
              <td>
                <div class="fw-bold text-dark">{{ $mov->product->name ?? 'Produk Dihapus' }}</div>
                <small class="text-muted">{{ $mov->product->category->name ?? '-' }} ({{ $mov->product->sku ?? '-' }})</small>
              </td>
              <td class="text-center">
                @if($mov->type === 'IN')
                  <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill">
                    <i class="bi bi-arrow-down-left me-1"></i>MASUK
                  </span>
                @else
                  <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1 rounded-pill">
                    <i class="bi bi-arrow-up-right me-1"></i>KELUAR
                  </span>
                @endif
              </td>
              <td class="text-end fw-bold {{ $mov->type === 'IN' ? 'text-success' : 'text-danger' }}">
                {{ $mov->type === 'IN' ? '+' : '-' }}{{ number_format($mov->quantity) }} 
                <small class="text-muted fw-normal">{{ $mov->product->unit ?? 'unit' }}</small>
              </td>
              <td class="small">
                @if($mov->type === 'IN')
                  <span class="fw-semibold text-dark"><i class="bi bi-building me-1 text-muted"></i>{{ $mov->supplier->name ?? 'Donasi Langsung/Umum' }}</span>
                @else
                  <span class="fw-semibold text-dark"><i class="bi bi-geo-alt me-1 text-muted"></i>{{ $mov->distributionTarget->name ?? 'Posko Lapangan' }}</span>
                  <div class="text-muted">{{ $mov->distributionTarget->location ?? '' }}</div>
                @endif
                @if($mov->notes)
                  <div class="text-muted fst-italic mt-1"><i class="bi bi-chat-left-text me-1"></i>{{ $mov->notes }}</div>
                @endif
              </td>
              <td class="small text-muted">
                {{ $mov->user->full_name ?: $mov->user->username ?? '-' }}
              </td>
              <td class="text-center">
                @if($mov->is_public)
                  <i class="bi bi-eye-fill text-primary" title="Terbuka untuk publik"></i>
                @else
                  <i class="bi bi-eye-slash-fill text-muted" title="Hanya internal"></i>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="text-center py-5 text-muted">
                <i class="bi bi-clock-history fs-1 d-block mb-2 text-muted"></i>
                Tidak ada data mutasi yang sesuai dengan kriteria filter.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
      <div class="text-muted small">
        Menampilkan <strong>{{ $movements->firstItem() ?? 0 }}</strong> - <strong>{{ $movements->lastItem() ?? 0 }}</strong> dari <strong>{{ $movements->total() }}</strong> baris mutasi
      </div>
      <div>
        {{ $movements->links() }}
      </div>
    </div>
  </div>
</div>
@endsection
