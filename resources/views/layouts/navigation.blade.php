<nav class="navbar navbar-public navbar-expand-lg">
  <div class="container">
    <a class="navbar-brand" href="{{ route('home') }}">
      <span class="brand-icon">📦</span>
      <span>SIGudang</span>
    </a>
    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navPublic">
      <span style="color:#fff;font-size:1.2rem"><i class="bi bi-list"></i></span>
    </button>
    <div class="collapse navbar-collapse" id="navPublic">
      <ul class="navbar-nav me-auto ms-3 gap-1">
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Beranda</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('public.dashboard') ? 'active' : '' }}" href="{{ route('public.dashboard') }}">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('public.stok') ? 'active' : '' }}" href="{{ route('public.stok') }}">Stok Bantuan</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('public.masuk') ? 'active' : '' }}" href="{{ route('public.masuk') }}">Bantuan Masuk</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('public.penyaluran') ? 'active' : '' }}" href="{{ route('public.penyaluran') }}">Penyaluran</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('public.statistik') ? 'active' : '' }}" href="{{ route('public.statistik') }}">Statistik</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('public.laporan') ? 'active' : '' }}" href="{{ route('public.laporan') }}">Laporan</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('public.tentang') ? 'active' : '' }}" href="{{ route('public.tentang') }}">Tentang</a></li>
      </ul>
      <div class="d-flex align-items-center gap-2">
        @auth
          <a href="{{ Auth::user()->role === 'admin' ? route('admin.dashboard') : route('staff.dashboard') }}" class="btn-login nav-link small">
            <i class="bi bi-speedometer2"></i> Dashboard
          </a>
        @else
          <a href="{{ route('login') }}" class="btn-login nav-link">
            <i class="bi bi-lock-fill"></i> Login Admin
          </a>
        @endauth
      </div>
    </div>
  </div>
</nav>
