@extends('layouts.app')

@section('title', 'Statistik Distribusi')

@section('content')
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
    <div>
      <h1 class="section-heading">Statistik <span>Distribusi</span></h1>
      <div class="section-divider"></div>
    </div>
    <form method="GET" action="{{ route('public.statistik') }}" class="d-flex gap-2 align-items-end flex-wrap">
      <div>
        <label class="form-label small fw-semibold mb-1">Dari</label>
        <input type="date" name="from" class="form-control form-control-sm" value="{{ $dateFrom }}">
      </div>
      <div>
        <label class="form-label small fw-semibold mb-1">Sampai</label>
        <input type="date" name="to" class="form-control form-control-sm" value="{{ $dateTo }}">
      </div>
      <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-funnel"></i> Terapkan</button>
      <a href="{{ route('public.statistik') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
    </form>
  </div>

  <!-- Summary -->
  <div class="row g-3 mb-4">
    <div class="col-md-6">
      <div class="stat-card">
        <div class="stat-icon green"><i class="bi bi-arrow-down-circle"></i></div>
        <div class="stat-body">
          <div class="stat-value">{{ number_format($sumRow->total_masuk ?? 0) }}</div>
          <div class="stat-label">Total Masuk pada Periode</div>
          <div class="stat-sub">{{ $dateFrom }} s/d {{ $dateTo }}</div>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="stat-card">
        <div class="stat-icon orange"><i class="bi bi-arrow-up-circle"></i></div>
        <div class="stat-body">
          <div class="stat-value">{{ number_format($sumRow->total_keluar ?? 0) }}</div>
          <div class="stat-label">Total Tersalur pada Periode</div>
          <div class="stat-sub">{{ $dateFrom }} s/d {{ $dateTo }}</div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4 mb-4">
    <div class="col-lg-6">
      <div class="card-panel h-100">
        <div class="card-panel-header">Distribusi Berdasarkan Tujuan</div>
        <div class="card-panel-body chart-container" style="height:280px">
          <canvas id="chartDest"></canvas>
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="card-panel h-100">
        <div class="card-panel-header">Distribusi Berdasarkan Kategori</div>
        <div class="card-panel-body chart-container" style="height:280px">
          <canvas id="chartCat"></canvas>
        </div>
      </div>
    </div>
  </div>

  <div class="card-panel">
    <div class="card-panel-header">Tren Barang Masuk & Keluar</div>
    <div class="card-panel-body chart-container" style="height:280px">
      <canvas id="chartTrend"></canvas>
    </div>
  </div>
</div>

@push('scripts')
<script>
const COLORS = ['#1a3a6b','#2456a4','#e85d04','#0fa86a','#7c3aed','#b45309','#dc3545','#0dcaf0'];

new Chart(document.getElementById('chartDest'), {
  type: 'bar',
  data: {
    labels: {!! json_encode($destRows->pluck('name')) !!},
    datasets: [{
      label: 'Tersalur',
      data: {!! json_encode($destRows->pluck('total')->map(fn($v)=>(int)$v)) !!},
      backgroundColor: 'rgba(36,86,164,0.75)',
      borderRadius: 4
    }]
  },
  options: {
    indexAxis: 'y',
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: { x: { beginAtZero: true } }
  }
});

new Chart(document.getElementById('chartCat'), {
  type: 'doughnut',
  data: {
    labels: {!! json_encode($catRows->pluck('name')) !!},
    datasets: [{
      data: {!! json_encode($catRows->pluck('total')->map(fn($v)=>(int)$v)) !!},
      backgroundColor: COLORS
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { position: 'bottom' } }
  }
});

new Chart(document.getElementById('chartTrend'), {
  type: 'line',
  data: {
    labels: {!! json_encode($tsRows->pluck('bulan')) !!},
    datasets: [
      {
        label: 'Masuk',
        data: {!! json_encode($tsRows->pluck('masuk')->map(fn($v)=>(int)$v)) !!},
        borderColor: '#0fa86a',
        backgroundColor: 'rgba(15,168,106,0.1)',
        fill: true,
        tension: 0.4
      },
      {
        label: 'Keluar',
        data: {!! json_encode($tsRows->pluck('keluar')->map(fn($v)=>(int)$v)) !!},
        borderColor: '#e85d04',
        backgroundColor: 'rgba(232,93,4,0.1)',
        fill: true,
        tension: 0.4
      }
    ]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { position: 'bottom' } },
    scales: { y: { beginAtZero: true } }
  }
});
</script>
@endpush
@endsection
