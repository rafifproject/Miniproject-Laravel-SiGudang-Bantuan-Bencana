@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
<!-- ═══ HERO ═══════════════════════════════════════════════════ -->
<section class="hero-section" style="background-image: linear-gradient(90deg, #1b3876 0%, rgba(27, 56, 118, 0.94) 40%, rgba(27, 56, 118, 0.70) 65%, rgba(27, 56, 118, 0.20) 100%), url('{{ asset('assets/images/hero-bg.png') }}');">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-6">
        <span class="badge-hero">🏛 Sistem Informasi Publik</span>
        <h1>Transparansi Bantuan, <span>Tepat Sasaran</span></h1>
        <p>Sistem informasi pengelolaan logistik bantuan bencana secara transparan, akuntabel, dan mudah diakses masyarakat.</p>
        <div class="d-flex flex-row align-items-center gap-3 hero-action-buttons">
          <a href="{{ route('public.stok') }}" class="btn btn-warning fw-bold">
            <i class="bi bi-box-seam me-2"></i>Lihat Stok Bantuan
          </a>
          <a href="{{ route('public.penyaluran') }}" class="btn btn-outline-light fw-semibold">
            <i class="bi bi-arrow-up-right-circle me-2"></i>Riwayat Penyaluran
          </a>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="hero-stats">
          <div class="hero-stat-card">
            <div class="stat-num">{{ number_format($totalProducts) }}</div>
            <div class="stat-lbl">Jenis Bantuan</div>
          </div>
          <div class="hero-stat-card">
            <div class="stat-num">{{ number_format($totalStock) }}</div>
            <div class="stat-lbl">Total Stok</div>
          </div>
          <div class="hero-stat-card">
            <div class="stat-num">{{ number_format($totalIn) }}</div>
            <div class="stat-lbl">Total Masuk</div>
          </div>
          <div class="hero-stat-card">
            <div class="stat-num">{{ number_format($totalOut) }}</div>
            <div class="stat-lbl">Total Tersalur</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ═══ STATISTIK CEPAT ═══════════════════════════════════════ -->
<section class="py-5">
  <div class="container">
    <div class="row g-4 mb-5">
      <div class="col-6 col-md-3">
        <div class="stat-card">
          <div class="stat-icon blue"><i class="bi bi-box-seam"></i></div>
          <div class="stat-body">
            <div class="stat-value">{{ number_format($totalProducts) }}</div>
            <div class="stat-label">Jenis Logistik</div>
          </div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-card">
          <div class="stat-icon green"><i class="bi bi-stack"></i></div>
          <div class="stat-body">
            <div class="stat-value">{{ number_format($totalStock) }}</div>
            <div class="stat-label">Total Stok Tersedia</div>
          </div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-card">
          <div class="stat-icon orange"><i class="bi bi-arrow-down-circle"></i></div>
          <div class="stat-body">
            <div class="stat-value">{{ number_format($totalIn) }}</div>
            <div class="stat-label">Total Masuk</div>
          </div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-card">
          <div class="stat-icon purple"><i class="bi bi-arrow-up-circle"></i></div>
          <div class="stat-body">
            <div class="stat-value">{{ number_format($totalOut) }}</div>
            <div class="stat-label">Total Tersalur</div>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-4">
      <!-- Stok Kritis -->
      <div class="col-lg-6">
        <div class="card-panel h-100">
          <div class="card-panel-header">
            <span><i class="bi bi-exclamation-triangle text-warning me-2"></i>Perhatian: Stok Mendekati Minimum</span>
            <a href="{{ route('public.stok', ['status' => 'kritis']) }}" class="btn-sm-action btn-view">Lihat Semua</a>
          </div>
          <div class="card-panel-body">
            @if($criticalItems->isEmpty())
              <div class="empty-state"><span class="empty-icon">✅</span>Semua stok dalam kondisi aman</div>
            @else
              @foreach($criticalItems as $k)
                <div class="alert-stock-low mb-2">
                  <i class="bi bi-exclamation-circle text-warning"></i>
                  <div class="flex-grow-1">
                    <strong>{{ $k->name }}</strong>
                    <div class="text-muted small">{{ $k->category->name ?? '-' }}</div>
                  </div>
                  <span class="badge-status badge-kritis">Stok: {{ number_format($k->stock) }}</span>
                </div>
              @endforeach
            @endif
          </div>
        </div>
      </div>

      <!-- Transaksi Terbaru -->
      <div class="col-lg-6">
        <div class="card-panel h-100">
          <div class="card-panel-header">
            <span><i class="bi bi-clock-history me-2"></i>Aktivitas Terbaru</span>
            <a href="{{ route('public.masuk') }}" class="btn-sm-action btn-view">Lihat Semua</a>
          </div>
          <div class="card-panel-body p-0">
            <table class="table-modern w-100">
              <thead>
                <tr>
                  <th>Tanggal</th>
                  <th>Barang</th>
                  <th>Tipe</th>
                  <th>Jumlah</th>
                </tr>
              </thead>
              <tbody>
                @forelse($recentActivities as $r)
                  <tr>
                    <td>{{ $r->date->format('d M Y') }}</td>
                    <td>{{ $r->product->name ?? '-' }}</td>
                    <td>
                      <span class="badge-status {{ $r->type === 'IN' ? 'badge-in' : 'badge-out' }}">
                        {{ $r->type === 'IN' ? 'Masuk' : 'Keluar' }}
                      </span>
                    </td>
                    <td>{{ number_format($r->quantity) }} {{ $r->product->unit ?? 'pcs' }}</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="4" class="empty-state">Belum ada transaksi</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- CTA Section -->
    <div class="row mt-5">
      <div class="col-12">
        <div class="card-panel text-center p-5" style="background:linear-gradient(135deg,var(--primary),var(--primary-light));color:#fff">
          <h2 class="fw-bold mb-2">Bersama Kita Pulih</h2>
          <p class="mb-4" style="color:rgba(255,255,255,0.85)">Pantau transparansi distribusi bantuan bencana secara real-time melalui sistem informasi ini.</p>
          <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="{{ route('public.dashboard') }}" class="btn btn-warning fw-bold">
              <i class="bi bi-bar-chart-line me-2"></i>Dashboard Publik
            </a>
            <a href="{{ route('public.statistik') }}" class="btn btn-outline-light">
              <i class="bi bi-graph-up me-2"></i>Lihat Statistik
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
