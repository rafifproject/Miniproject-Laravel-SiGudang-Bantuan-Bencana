<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Sistem | SIGudang Bantuan Bencana</title>
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>📦</text></svg>">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>
<body>
<div class="login-page">
  <div class="login-card" style="max-width: 440px;">
    <!-- Logo & Title -->
    <div class="login-logo text-center mb-4">
      <div class="brand-icon mx-auto mb-2" style="width: 56px; height: 56px; font-size: 1.8rem; background: var(--accent); border-radius: 12px; display: flex; align-items: center; justify-content: center;">📦</div>
      <h3 class="fw-bold text-dark mb-1">SIGudang Bencana</h3>
      <p class="text-muted small mb-0">Masuk ke Sistem Manajemen Logistik Bencana</p>
    </div>

    @if(session('success'))
      <x-alert type="success">
        {{ session('success') }}
      </x-alert>
    @endif

    @if($errors->any())
      <x-alert type="danger">
        <ul class="mb-0 ps-3">
          @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
          @endforeach
        </ul>
      </x-alert>
    @endif

    <form method="POST" action="{{ route('login') }}" class="mb-3">
      @csrf

      <div class="mb-3">
        <label for="username" class="form-label fw-semibold">Username Operator</label>
        <div class="input-group">
          <span class="input-group-text bg-light"><i class="bi bi-person text-muted"></i></span>
          <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" 
                 value="{{ old('username') }}" placeholder="Masukkan username" required autofocus>
        </div>
      </div>

      <div class="mb-3">
        <label for="password" class="form-label fw-semibold">Kata Sandi</label>
        <div class="input-group">
          <span class="input-group-text bg-light"><i class="bi bi-lock text-muted"></i></span>
          <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" 
                 placeholder="Masukkan password" required>
          <button class="btn btn-outline-secondary" type="button" id="togglePassword">
            <i class="bi bi-eye" id="toggleIcon"></i>
          </button>
        </div>
      </div>

      <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="remember" id="remember">
          <label class="form-check-label small text-muted" for="remember">Ingat Saya</label>
        </div>
        <a href="{{ route('home') }}" class="small text-decoration-none text-muted hover-primary">
          <i class="bi bi-globe me-1"></i>Portal Publik
        </a>
      </div>

      <button type="submit" class="btn btn-primary w-100 fw-semibold py-2">
        <i class="bi bi-box-arrow-in-right me-1"></i> Masuk ke Sistem
      </button>
    </form>

    <!-- Demo Credentials Box -->
    <div class="mt-4 p-3 bg-light rounded-3 border">
      <div class="d-flex align-items-center gap-2 mb-2">
        <i class="bi bi-info-circle-fill text-primary"></i>
        <small class="fw-bold text-dark text-uppercase">Kredensial Demo Cepat</small>
      </div>
      <div class="row g-2 small">
        <div class="col-6">
          <div class="p-2 bg-white rounded border cursor-pointer hover-shadow" onclick="fillCreds('admin', 'admin123')">
            <span class="badge bg-primary mb-1">Administrator</span>
            <div class="text-muted">User: <code>admin</code></div>
            <div class="text-muted">Pass: <code>admin123</code></div>
          </div>
        </div>
        <div class="col-6">
          <div class="p-2 bg-white rounded border cursor-pointer hover-shadow" onclick="fillCreds('staff1', 'staff123')">
            <span class="badge bg-success mb-1">Staff Lapangan</span>
            <div class="text-muted">User: <code>staff1</code></div>
            <div class="text-muted">Pass: <code>staff123</code></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  const toggleBtn = document.getElementById('togglePassword');
  const passInput = document.getElementById('password');
  const toggleIcon = document.getElementById('toggleIcon');

  toggleBtn.addEventListener('click', () => {
    const isPass = passInput.type === 'password';
    passInput.type = isPass ? 'text' : 'password';
    toggleIcon.className = isPass ? 'bi bi-eye-slash' : 'bi bi-eye';
  });

  function fillCreds(u, p) {
    document.getElementById('username').value = u;
    document.getElementById('password').value = p;
  }
</script>
</body>
</html>
