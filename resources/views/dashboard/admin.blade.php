@extends('layouts.admin')

@section('title', 'Dashboard Administrator')
@section('page_title', 'Dashboard Admin')
@section('page_subtitle', 'Ringkasan posisi persediaan, arus donasi masuk, penyaluran posko, dan peringatan stok')

@section('content')
<!-- Stat Cards: 2 baris x 3 kartu (Sesuai Desain Halaman 10 Asli) -->
<div class="row g-3 mb-4">
  <div class="col-12 col-sm-6 col-xl-4">
    <div class="stat-card">
      <div class="stat-icon blue"><i class="bi bi-box-seam"></i></div>
      <div class="stat-body">
        <div class="stat-value">{{ number_format($totalProducts) }}</div>
        <div class="stat-label">Jenis Barang</div>
      </div>
    </div>
  </div>

  <div class="col-12 col-sm-6 col-xl-4">
    <div class="stat-card">
      <div class="stat-icon green"><i class="bi bi-stack"></i></div>
      <div class="stat-body">
        <div class="stat-value">{{ number_format($totalStock) }}</div>
        <div class="stat-label">Total Stok Tersedia</div>
      </div>
    </div>
  </div>

  <div class="col-12 col-sm-6 col-xl-4">
    <div class="stat-card">
      <div class="stat-icon purple"><i class="bi bi-cash-coin"></i></div>
      <div class="stat-body">
        <div class="stat-value" style="font-size:1.35rem">Rp {{ number_format($totalValue, 0, ',', '.') }}</div>
        <div class="stat-label">Estimasi Nilai Persediaan</div>
      </div>
    </div>
  </div>

  <div class="col-12 col-sm-6 col-xl-4">
    <div class="stat-card">
      <div class="stat-icon red"><i class="bi bi-exclamation-triangle"></i></div>
      <div class="stat-body">
        <div class="stat-value {{ $criticalStockCount > 0 ? 'text-danger' : 'text-success' }}">{{ number_format($criticalStockCount) }}</div>
        <div class="stat-label">Stok Minimum / Kritis</div>
      </div>
    </div>
  </div>

  <div class="col-12 col-sm-6 col-xl-4">
    <div class="stat-card">
      <div class="stat-icon orange"><i class="bi bi-arrow-down-circle"></i></div>
      <div class="stat-body">
        <div class="stat-value text-success">+{{ number_format($totalIn) }}</div>
        <div class="stat-label">Total Barang Masuk</div>
      </div>
    </div>
  </div>

  <div class="col-12 col-sm-6 col-xl-4">
    <div class="stat-card">
      <div class="stat-icon yellow"><i class="bi bi-arrow-up-circle"></i></div>
      <div class="stat-body">
        <div class="stat-value text-danger">-{{ number_format($totalOut) }}</div>
        <div class="stat-label">Total Barang Keluar</div>
      </div>
    </div>
  </div>
</div>

<div class="row g-4 mb-4">
  <!-- Chart Mutasi -->
  <div class="col-lg-7">
    <div class="card-panel h-100">
      <div class="card-panel-header">Mutasi Stok (6 Bulan Terakhir)</div>
      <div class="card-panel-body chart-container" style="height:260px">
        <canvas id="chartMutasi"></canvas>
      </div>
    </div>
  </div>

  <!-- Ringkasan Kategori -->
  <div class="col-lg-5">
    <div class="card-panel h-100">
      <div class="card-panel-header">Ringkasan Kategori</div>
      <div class="card-panel-body p-0">
        <table class="table-modern w-100">
          <thead>
            <tr>
              <th>Kategori</th>
              <th class="text-end">Produk</th>
              <th class="text-end">Total Stok</th>
            </tr>
          </thead>
          <tbody>
            @forelse($categoryStats as $k)
            <tr>
              <td>{{ $k->name }}</td>
              <td class="text-end">{{ $k->products_count }}</td>
              <td class="text-end fw-bold">{{ number_format($k->products_sum_stock ?? 0) }}</td>
            </tr>
            @empty
            <tr><td colspan="3" class="text-center py-3 text-muted">Belum ada data kategori</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<div class="row g-4 mb-4">
  <!-- Transaksi Terbaru -->
  <div class="col-lg-8">
    <div class="card-panel h-100">
      <div class="card-panel-header">
        <span>Transaksi Terbaru</span>
        <a href="{{ route('mutasi-stok.index') }}" class="btn-sm-action btn-view">Lihat Semua</a>
      </div>
      <div class="table-responsive">
        <table class="table-modern w-100">
          <thead>
            <tr>
              <th>Tgl</th>
              <th>Tipe</th>
              <th>Produk</th>
              <th>Jumlah</th>
              <th>Oleh</th>
            </tr>
          </thead>
          <tbody>
            @forelse($recentTransactions as $r)
            <tr>
              <td class="text-muted small">{{ $r->date->format('d M Y') }}</td>
              <td><span class="badge-status {{ $r->type === 'IN' ? 'badge-in' : 'badge-out' }}">{{ $r->type }}</span></td>
              <td>{{ $r->product->name ?? '-' }}</td>
              <td class="{{ $r->type === 'IN' ? 'text-success' : 'text-danger' }} fw-bold">
                {{ $r->type === 'IN' ? '+' : '-' }}{{ number_format($r->quantity) }}
              </td>
              <td class="text-muted small">{{ $r->user->full_name ?: $r->user->username ?? '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada mutasi transaksi.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Stok Kritis -->
  <div class="col-lg-4">
    <div class="card-panel h-100">
      <div class="card-panel-header">
        <span><i class="bi bi-exclamation-triangle text-warning me-1"></i>Peringatan Stok</span>
        <a href="{{ route('products.index', ['status' => 'kritis']) }}" class="btn-sm-action btn-edit">Detail</a>
      </div>
      <div class="card-panel-body p-0">
        @if($criticalProducts->isEmpty())
          <div class="empty-state text-center py-4"><span class="empty-icon fs-2 d-block">✅</span>Semua stok aman</div>
        @else
          @foreach($criticalProducts as $k)
          <div class="alert-stock-low border-bottom d-flex align-items-center justify-content-between p-2">
            <div class="flex-grow-1">
              <div class="fw-semibold small">{{ $k->name }}</div>
              <div class="text-muted" style="font-size:0.72rem">{{ $k->category->name ?? '-' }} • SKU: {{ $k->sku }}</div>
            </div>
            <span class="badge-status badge-kritis">
              {{ number_format($k->stock) }}/{{ number_format($k->minimum_stock) }}
            </span>
          </div>
          @endforeach
        @endif
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
new Chart(document.getElementById('chartMutasi'), {
  type: 'bar',
  data: {
    labels: {!! json_encode($chartLabels) !!},
    datasets: [
      { label:'Masuk',  data: {!! json_encode($chartIn) !!},  backgroundColor:'rgba(15,168,106,0.7)',  borderRadius:4 },
      { label:'Keluar', data: {!! json_encode($chartOut) !!}, backgroundColor:'rgba(232,93,4,0.7)', borderRadius:4 }
    ]
  },
  options: { responsive:true, maintainAspectRatio:false, plugins:{legend:{position:'bottom'}}, scales:{y:{beginAtZero:true}} }
});
</script>
@endpush
@endsection
