<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Beranda') | {{ config('app.name', 'Sistem Informasi Gudang Bantuan Bencana') }}</title>
  <meta name="description" content="Sistem Informasi Gudang Bantuan Bencana — transparansi bantuan tepat sasaran">
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>📦</text></svg>">
  <!-- Bootstrap 5 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
</head>
<body>

@include('layouts.navigation')

@if(session('success'))
  <div class="container mt-3">
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  </div>
@endif

@if(session('error'))
  <div class="container mt-3">
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  </div>
@endif

@yield('content')

<!-- Footer matching original native UI -->
<footer class="site-footer mt-5">
  <div class="container">
    <div class="row g-4">
      <div class="col-md-4">
        <div class="d-flex align-items-center gap-2 mb-2">
          <span style="font-size:1.3rem">📦</span>
          <span style="color:#fff;font-weight:700">SIGudang Bencana</span>
        </div>
        <p style="font-size:0.82rem">Sistem Informasi Gudang Bantuan Bencana — mewujudkan transparansi, akuntabilitas, dan ketepatan penyaluran bantuan kemanusiaan.</p>
      </div>
      <div class="col-md-2">
        <h6 style="color:#fff;font-size:0.85rem;font-weight:700">Navigasi</h6>
        <ul style="list-style:none;padding:0;margin:0">
          <li><a href="{{ route('home') }}">Beranda</a></li>
          <li><a href="{{ route('public.stok') }}">Stok Bantuan</a></li>
          <li><a href="{{ route('public.penyaluran') }}">Penyaluran</a></li>
          <li><a href="{{ route('public.statistik') }}">Statistik</a></li>
        </ul>
      </div>
      <div class="col-md-2">
        <h6 style="color:#fff;font-size:0.85rem;font-weight:700">Informasi</h6>
        <ul style="list-style:none;padding:0;margin:0">
          <li><a href="{{ route('public.laporan') }}">Laporan Publik</a></li>
          <li><a href="{{ route('public.tentang') }}">Tentang Sistem</a></li>
          <li><a href="{{ route('login') }}">Login Admin</a></li>
        </ul>
      </div>
      <div class="col-md-4">
        <h6 style="color:#fff;font-size:0.85rem;font-weight:700">Kontak</h6>
        <p style="font-size:0.82rem">
          <i class="bi bi-envelope me-1"></i> info@sigudang.id<br>
          <i class="bi bi-telephone me-1"></i> (021) 1234-5678<br>
          <i class="bi bi-geo-alt me-1"></i> Jakarta, Indonesia
        </p>
      </div>
    </div>
    <hr style="border-color:rgba(255,255,255,0.15)">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
      <small>&copy; {{ date('Y') }} Sistem Informasi Gudang Bantuan Bencana. Hak cipta dilindungi.</small>
      <small>Transparansi • Akuntabilitas • Ketepatan Penyaluran</small>
    </div>
  </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
