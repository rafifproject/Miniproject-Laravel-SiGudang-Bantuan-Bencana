<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>404 - Halaman Tidak Ditemukan | SIGudang Bencana</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>
<body>
<div class="login-page">
  <div class="login-card text-center" style="max-width: 500px;">
    <div class="mb-3 text-warning" style="font-size: 4rem;">
      <i class="bi bi-exclamation-triangle"></i>
    </div>
    <span class="badge bg-warning-subtle text-warning-emphasis px-3 py-1 rounded-pill mb-2 fw-semibold">Error 404</span>
    <h2 class="fw-bold mb-2">Halaman Tidak Ditemukan</h2>
    <p class="text-muted mb-4">
      Maaf, tautan atau halaman yang Anda cari tidak tersedia atau mungkin sudah dipindahkan.
    </p>

    <div class="d-flex flex-wrap gap-2 justify-content-center">
      @auth
        <a href="{{ Auth::user()->role === 'admin' ? route('admin.dashboard') : route('staff.dashboard') }}" class="btn btn-primary px-4 fw-semibold">
          <i class="bi bi-speedometer2 me-2"></i>Dashboard
        </a>
      @endauth
      <a href="{{ route('home') }}" class="btn btn-outline-primary px-4">
        <i class="bi bi-house me-2"></i>Kembali ke Beranda
      </a>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
