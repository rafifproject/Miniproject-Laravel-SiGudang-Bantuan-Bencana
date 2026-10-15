<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Dashboard') | SIGudang Bencana</title>
  <meta name="description" content="Dashboard Manajemen Gudang Bantuan Bencana">
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>📦</text></svg>">
  
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body>
<div class="app-wrapper">

  <!-- ═══════ SIDEBAR ═══════ -->
  <aside class="sidebar" id="appSidebar">
    <div class="sidebar-header">
      <div class="logo-icon">📦</div>
      <div class="logo-text">
        SIGudang
        <span>Bantuan Bencana</span>
      </div>
    </div>

    <nav class="sidebar-nav">
      <div class="nav-section-label">Utama</div>
      <div class="nav-item">
        <a href="{{ Auth::user()->role === 'admin' ? route('admin.dashboard') : route('staff.dashboard') }}" class="nav-link {{ request()->routeIs('*.dashboard') ? 'active' : '' }}">
          <i class="bi bi-speedometer2 nav-icon"></i> Dashboard
        </a>
      </div>

      <div class="nav-section-label">Transaksi Logistik</div>
      <div class="nav-item">
        <a href="{{ route('barang-masuk.index') }}" class="nav-link {{ request()->routeIs('barang-masuk.*') ? 'active' : '' }}">
          <i class="bi bi-box-arrow-in-down nav-icon"></i> Barang Masuk
        </a>
      </div>
      <div class="nav-item">
        <a href="{{ route('barang-keluar.index') }}" class="nav-link {{ request()->routeIs('barang-keluar.*') ? 'active' : '' }}">
          <i class="bi bi-box-arrow-up nav-icon"></i> Barang Keluar
        </a>
      </div>
      <div class="nav-item">
        <a href="{{ route('mutasi-stok.index') }}" class="nav-link {{ request()->routeIs('mutasi-stok.*') ? 'active' : '' }}">
          <i class="bi bi-arrow-left-right nav-icon"></i> Riwayat Mutasi
        </a>
      </div>

      @if(Auth::user()->isAdmin())
      <div class="nav-section-label">Master Data</div>
      <div class="nav-item">
        <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
          <i class="bi bi-box-seam nav-icon"></i> Produk
        </a>
      </div>
      <div class="nav-item">
        <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
          <i class="bi bi-tags nav-icon"></i> Kategori
        </a>
      </div>
      <div class="nav-item">
        <a href="{{ route('suppliers.index') }}" class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
          <i class="bi bi-building nav-icon"></i> Supplier / Donatur
        </a>
      </div>
      <div class="nav-item">
        <a href="{{ route('product-suppliers.index') }}" class="nav-link {{ request()->routeIs('product-suppliers.*') ? 'active' : '' }}">
          <i class="bi bi-truck nav-icon"></i> Pasokan Supplier
        </a>
      </div>

      <div class="nav-section-label">Laporan & Audit</div>
      <div class="nav-item">
        <a href="{{ route('admin.laporan') }}" class="nav-link {{ request()->routeIs('admin.laporan*') ? 'active' : '' }}">
          <i class="bi bi-file-earmark-bar-graph nav-icon"></i> Laporan Admin
        </a>
      </div>

      <div class="nav-section-label">Pengguna & Hak Akses</div>
      <div class="nav-item">
        <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
          <i class="bi bi-people nav-icon"></i> Manajemen Pengguna
        </a>
      </div>
      @endif

      <div class="nav-section-label">Akun</div>
      <div class="nav-item">
        <a href="{{ route('profile.index') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
          <i class="bi bi-person-circle nav-icon"></i> Profil Saya
        </a>
      </div>
      <div class="nav-item">
        <a href="{{ route('home') }}" class="nav-link">
          <i class="bi bi-globe nav-icon"></i> Portal Publik
        </a>
      </div>
      <div class="nav-item mt-3">
        <button type="button" class="nav-link text-danger border-0 bg-transparent w-100 text-start" data-bs-toggle="modal" data-bs-target="#logoutModal">
          <i class="bi bi-box-arrow-right nav-icon"></i> Keluar Sistem
        </button>
      </div>
    </nav>
  </aside>

  <!-- ═══════ MAIN CONTENT AREA ═══════ -->
  <div class="main-content">
    <!-- Topbar -->
    <header class="topbar">
      <div class="d-flex align-items-center gap-3">
        <button class="btn btn-sm btn-outline-secondary d-lg-none" id="sidebarToggle" type="button">
          <i class="bi bi-list fs-5"></i>
        </button>
        <div class="page-title-box">
          <h1 class="h5 mb-0 fw-bold">@yield('page_title', 'Dashboard')</h1>
          <small class="text-muted">@yield('page_subtitle', 'Sistem Informasi Manajemen Gudang Bencana')</small>
        </div>
      </div>

      <div class="d-flex align-items-center gap-3">
        <div class="text-end d-none d-sm-block">
          <div class="fw-semibold text-dark">{{ Auth::user()->full_name ?: Auth::user()->username }}</div>
          <span class="badge {{ Auth::user()->role === 'admin' ? 'bg-primary' : 'bg-success' }}">
            {{ ucfirst(Auth::user()->role) }}
          </span>
        </div>
        <div class="dropdown">
          <button class="btn btn-light rounded-circle p-2 shadow-sm border" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-person-circle fs-5 text-primary"></i>
          </button>
          <ul class="dropdown-menu dropdown-menu-end shadow border-0">
            <li><h6 class="dropdown-header">{{ Auth::user()->full_name ?: Auth::user()->username }}</h6></li>
            <li><a class="dropdown-item" href="{{ route('profile.index') }}"><i class="bi bi-person me-2"></i>Profil Saya</a></li>
            <li><a class="dropdown-item" href="{{ route('home') }}"><i class="bi bi-globe me-2"></i>Lihat Portal Publik</a></li>
            <li><hr class="dropdown-divider"></li>
            <li>
              <button class="dropdown-item text-danger" type="button" data-bs-toggle="modal" data-bs-target="#logoutModal">
                <i class="bi bi-box-arrow-right me-2"></i>Keluar
              </button>
            </li>
          </ul>
        </div>
      </div>
    </header>

    <!-- Content Body -->
    <main class="content-body p-4">
      @if(session('success'))
        <x-alert type="success">
          {{ session('success') }}
        </x-alert>
      @endif

      @if(session('error'))
        <x-alert type="danger">
          {{ session('error') }}
        </x-alert>
      @endif

      @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <strong>Perhatian:</strong> Terdapat beberapa kesalahan dalam input Anda.
          <ul class="mb-0 mt-2 small">
            @foreach($errors->all() as $err)
              <li>{{ $err }}</li>
            @endforeach
          </ul>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif

      @yield('content')
    </main>

    <footer class="text-center py-3 text-muted small border-top bg-white">
      &copy; {{ date('Y') }} <strong>SIGudang</strong> — Sistem Informasi Gudang Bantuan Bencana (Laravel MVC Edition).
    </footer>
  </div>
</div>

<!-- Logout Confirmation Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold" id="logoutModalLabel">Konfirmasi Keluar</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body py-4 text-center">
        <div class="text-danger mb-3" style="font-size: 3rem;">
          <i class="bi bi-question-circle"></i>
        </div>
        <p class="mb-0 text-muted">Apakah Anda yakin ingin keluar dari sesi kerja sistem gudang ini?</p>
      </div>
      <div class="modal-footer border-0 pt-0 justify-content-center">
        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="btn btn-danger px-4 fw-semibold">Ya, Keluar</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Mobile sidebar toggle
  document.getElementById('sidebarToggle')?.addEventListener('click', function() {
    document.getElementById('appSidebar')?.classList.toggle('show');
  });
</script>
@stack('scripts')
</body>
</html>
