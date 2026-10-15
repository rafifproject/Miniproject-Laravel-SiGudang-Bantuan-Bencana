@extends('layouts.admin')

@section('title', 'Dashboard Staff Lapangan')
@section('page_title', 'Dashboard Operasional Gudang Lapangan')
@section('page_subtitle', 'Antarmuka kerja staf logistik bencana: input cepat barang masuk, catat penyaluran posko, dan pantau persediaan')

@section('content')
<!-- Tombol Aksi Cepat Operasional -->
<div class="row g-3 mb-4">
  <div class="col-md-4">
    <div class="card border-0 shadow-sm bg-primary text-white p-4 h-100 position-relative overflow-hidden">
      <div class="position-relative z-1">
        <span class="badge bg-white text-primary mb-2 fw-semibold">Penerimaan</span>
        <h4 class="fw-bold mb-2">Catat Barang Masuk</h4>
        <p class="small text-white-50 mb-3">Terima donasi logistik dari BNPB, PMI, atau lembaga donor.</p>
        <a href="{{ route('barang-masuk.index') }}" class="btn btn-light fw-bold text-primary px-4">
          <i class="bi bi-box-arrow-in-down me-1"></i> Buka Form Masuk
        </a>
      </div>
      <i class="bi bi-box-seam position-absolute end-0 bottom-0 text-white-50 fs-1 m-3 opacity-25" style="font-size: 5rem !important;"></i>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card border-0 shadow-sm bg-danger text-white p-4 h-100 position-relative overflow-hidden">
      <div class="position-relative z-1">
        <span class="badge bg-white text-danger mb-2 fw-semibold">Penyaluran</span>
        <h4 class="fw-bold mb-2">Catat Penyaluran Posko</h4>
        <p class="small text-white-50 mb-3">Keluarkan logistik ke posko pengungsian atau titik bencana.</p>
        <a href="{{ route('barang-keluar.index') }}" class="btn btn-light fw-bold text-danger px-4">
          <i class="bi bi-box-arrow-up me-1"></i> Buka Form Keluar
        </a>
      </div>
      <i class="bi bi-truck position-absolute end-0 bottom-0 text-white-50 fs-1 m-3 opacity-25" style="font-size: 5rem !important;"></i>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card border-0 shadow-sm bg-dark text-white p-4 h-100 position-relative overflow-hidden">
      <div class="position-relative z-1">
        <span class="badge bg-secondary mb-2 fw-semibold">Pemeriksaan</span>
        <h4 class="fw-bold mb-2">Riwayat Audit Mutasi</h4>
        <p class="small text-white-50 mb-3">Periksa histori pergerakan stok barang dan nomor surat jalan.</p>
        <a href="{{ route('mutasi-stok.index') }}" class="btn btn-outline-light fw-semibold px-4">
          <i class="bi bi-arrow-left-right me-1"></i> Cek Mutasi
        </a>
      </div>
      <i class="bi bi-card-checklist position-absolute end-0 bottom-0 text-white-50 fs-1 m-3 opacity-25" style="font-size: 5rem !important;"></i>
    </div>
  </div>
</div>

<!-- Ringkasan Metrik Staff -->
<div class="row g-3 mb-4">
  <div class="col-md-3 col-6">
    <div class="p-3 bg-white rounded-3 border shadow-sm text-center">
      <small class="text-muted fw-semibold">Total Jenis Bantuan</small>
      <h3 class="fw-bold text-dark mb-0 mt-1">{{ number_format($totalProducts) }}</h3>
    </div>
  </div>
  <div class="col-md-3 col-6">
    <div class="p-3 bg-white rounded-3 border shadow-sm text-center">
      <small class="text-muted fw-semibold">Stok Fisik di Gudang</small>
      <h3 class="fw-bold text-primary mb-0 mt-1">{{ number_format($totalStock) }}</h3>
    </div>
  </div>
  <div class="col-md-3 col-6">
    <div class="p-3 bg-white rounded-3 border shadow-sm text-center">
      <small class="text-muted fw-semibold">Total Diterima (Masuk)</small>
      <h3 class="fw-bold text-success mb-0 mt-1">+{{ number_format($totalIn) }}</h3>
    </div>
  </div>
  <div class="col-md-3 col-6">
    <div class="p-3 bg-white rounded-3 border shadow-sm text-center">
      <small class="text-muted fw-semibold">Total Tersalur (Keluar)</small>
      <h3 class="fw-bold text-danger mb-0 mt-1">-{{ number_format($totalOut) }}</h3>
    </div>
  </div>
</div>

<div class="row g-4">
  <!-- Peringatan Stok Kritis untuk Staf -->
  <div class="col-lg-6">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0 text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Barang Menipis / Kritis</h5>
        <span class="badge bg-danger">{{ $criticalProducts->count() }} Item</span>
      </div>
      <div class="card-body p-4 pt-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle small mb-0">
            <thead class="table-light">
              <tr>
                <th>Nama Barang</th>
                <th class="text-center">Sisa Stok</th>
                <th class="text-center">Batas Min</th>
                <th class="text-end">Aksi Cepat</th>
              </tr>
            </thead>
            <tbody>
              @forelse($criticalProducts as $cp)
                <tr>
                  <td>
                    <div class="fw-bold text-dark">{{ $cp->name }}</div>
                    <small class="text-muted">{{ $cp->category->name ?? '-' }} ({{ $cp->sku }})</small>
                  </td>
                  <td class="text-center fw-bold text-danger">{{ number_format($cp->stock) }} {{ $cp->unit }}</td>
                  <td class="text-center text-muted">{{ number_format($cp->minimum_stock) }}</td>
                  <td class="text-end">
                    <a href="{{ route('barang-masuk.index') }}" class="btn btn-sm btn-success py-1 px-2" title="Tambah Donasi Masuk">
                      <i class="bi bi-plus-lg me-1"></i>Terima
                    </a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="text-center py-4 text-muted">
                    Semua stok logistik dalam kondisi aman.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Transaksi Terkini -->
  <div class="col-lg-6">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history me-2 text-primary"></i>Aktivitas Gudang Terkini</h5>
        <a href="{{ route('mutasi-stok.index') }}" class="btn btn-outline-primary btn-sm">Lihat Semua</a>
      </div>
      <div class="card-body p-4 pt-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle small mb-0">
            <thead class="table-light">
              <tr>
                <th>Tanggal</th>
                <th>Barang</th>
                <th class="text-center">Arus</th>
                <th class="text-end">Jumlah</th>
                <th>Tujuan/Sumber</th>
              </tr>
            </thead>
            <tbody>
              @forelse($recentTransactions as $tx)
                <tr>
                  <td class="text-muted">{{ $tx->date->format('d/m') }}</td>
                  <td class="fw-semibold text-dark">{{ Str::limit($tx->product->name ?? '-', 20) }}</td>
                  <td class="text-center">
                    @if($tx->type === 'IN')
                      <span class="badge bg-success-subtle text-success">IN</span>
                    @else
                      <span class="badge bg-warning-subtle text-warning-emphasis">OUT</span>
                    @endif
                  </td>
                  <td class="text-end fw-bold {{ $tx->type === 'IN' ? 'text-success' : 'text-danger' }}">
                    {{ $tx->type === 'IN' ? '+' : '-' }}{{ number_format($tx->quantity) }}
                  </td>
                  <td class="text-muted small">
                    {{ $tx->type === 'IN' ? ($tx->supplier->name ?? 'Donasi') : ($tx->distributionTarget->name ?? 'Posko') }}
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center py-4 text-muted">Belum ada transaksi.</td>
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
