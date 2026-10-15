<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>403 - Akses Ditolak | SIGudang Bencana</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>
<body>
<div class="login-page">
  <div class="login-card text-center" style="max-width: 500px;">
    <div class="mb-3 text-danger" style="font-size: 4rem;">
      <i class="bi bi-shield-x"></i>
    </div>
    <span class="badge bg-danger-subtle text-danger px-3 py-1 rounded-pill mb-2 fw-semibold">Error 403: Forbidden</span>
    <h2 class="fw-bold mb-2">Akses Terlarang</h2>
    <p class="text-muted mb-4">
      {{ $exception->getMessage() ?: 'Maaf, Anda tidak memiliki izin untuk mengakses halaman ini. Halaman ini hanya diperuntukkan bagi pengguna dengan role Administrator.' }}
    </p>

    <div class="d-flex flex-wrap gap-2 justify-content-center">
      @auth
        <a href="{{ Auth::user()->role === 'admin' ? route('admin.dashboard') : route('staff.dashboard') }}" class="btn btn-primary px-4 fw-semibold">
          <i class="bi bi-speedometer2 me-2"></i>Kembali ke Dashboard
        </a>
      @else
        <a href="{{ route('login') }}" class="btn btn-primary px-4 fw-semibold">
          <i class="bi bi-box-arrow-in-right me-2"></i>Login
        </a>
      @endauth
      <a href="{{ route('home') }}" class="btn btn-outline-secondary px-4">
        <i class="bi bi-house me-2"></i>Beranda Publik
      </a>
    </div>

    @auth
      <div class="mt-4 pt-3 border-top text-muted small">
        Masuk sebagai: <strong>{{ Auth::user()->full_name ?: Auth::user()->username }}</strong> 
        (Role: <span class="badge bg-secondary">{{ ucfirst(Auth::user()->role) }}</span>)
      </div>
    @endauth
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
