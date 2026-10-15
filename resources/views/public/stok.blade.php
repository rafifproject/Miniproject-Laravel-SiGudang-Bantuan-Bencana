@extends('layouts.app')

@section('title', 'Stok Bantuan')

@section('content')
<div class="container py-4">
  <div class="mb-4">
    <h1 class="section-heading">Stok <span>Bantuan Tersedia</span></h1>
    <div class="section-divider"></div>
    <p class="text-muted">Data stok logistik bantuan bencana yang tersedia di gudang.</p>
  </div>

  <!-- Filter Bar -->
  <form method="GET" action="{{ route('public.stok') }}" class="filter-bar mb-4">
    <div class="row g-2 align-items-end">
      <div class="col-md-4">
        <input type="text" name="q" class="form-control" placeholder="🔍 Cari nama barang atau SKU..." value="{{ $search }}">
      </div>
      <div class="col-md-2">
        <select name="cat" class="form-select">
          <option value="">Semua Kategori</option>
          @foreach($cats as $c)
            <option value="{{ $c->id }}" {{ $catId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <select name="status" class="form-select">
          <option value="">Semua Status</option>
          <option value="aman" {{ $status === 'aman' ? 'selected' : '' }}>Aman</option>
          <option value="kritis" {{ $status === 'kritis' ? 'selected' : '' }}>Kritis</option>
        </select>
      </div>
      <div class="col-md-2">
        <select name="sort" class="form-select">
          <option value="name" {{ $sortCol === 'name' ? 'selected' : '' }}>Nama</option>
          <option value="sku" {{ $sortCol === 'sku' ? 'selected' : '' }}>SKU</option>
          <option value="stock" {{ $sortCol === 'stock' ? 'selected' : '' }}>Stok</option>
          <option value="kategori" {{ $sortCol === 'kategori' ? 'selected' : '' }}>Kategori</option>
        </select>
      </div>
      <div class="col-md-1">
        <select name="dir" class="form-select">
          <option value="ASC" {{ $sortDir === 'ASC' ? 'selected' : '' }}>↑</option>
          <option value="DESC" {{ $sortDir === 'DESC' ? 'selected' : '' }}>↓</option>
        </select>
      </div>
      <div class="col-md-1 d-flex gap-1">
        <button type="submit" class="btn btn-primary flex-fill"><i class="bi bi-search"></i></button>
        <a href="{{ route('public.stok') }}" class="btn btn-outline-secondary flex-fill"><i class="bi bi-x"></i></a>
      </div>
    </div>
  </form>

  <div class="card-panel">
    <div class="card-panel-header">
      <span>Daftar Stok Bantuan</span>
      <small class="text-muted">Total: {{ $rows->total() }} barang</small>
    </div>
    <div class="table-responsive">
      <table class="table-modern w-100">
        <thead>
          <tr>
            <th>SKU</th>
            <th>
              <a href="{{ route('public.stok', array_merge($params, ['sort' => 'name', 'dir' => $sortDir === 'ASC' ? 'DESC' : 'ASC', 'page' => 1])) }}" class="sort-link">
                Nama Barang {{ $sortCol === 'name' ? ($sortDir === 'ASC' ? '↑' : '↓') : '' }}
              </a>
            </th>
            <th>Kategori</th>
            <th>
              <a href="{{ route('public.stok', array_merge($params, ['sort' => 'stock', 'dir' => $sortDir === 'ASC' ? 'DESC' : 'ASC', 'page' => 1])) }}" class="sort-link">
                Stok {{ $sortCol === 'stock' ? ($sortDir === 'ASC' ? '↑' : '↓') : '' }}
              </a>
            </th>
            <th>Satuan</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse($rows as $r)
            @php $isKritis = $r->stock <= $r->minimum_stock; @endphp
            <tr>
              <td><code>{{ $r->sku }}</code></td>
              <td><strong>{{ $r->name }}</strong></td>
              <td>{{ $r->category->name ?? '-' }}</td>
              <td>
                {{ number_format($r->stock) }}
                @if($r->minimum_stock > 0)
                  <div class="stock-progress">
                    @php $pct = min(100, round($r->stock / $r->minimum_stock * 100)); @endphp
                    <div class="stock-progress-bar" style="width: {{ $pct }}%; background: {{ $isKritis ? '#dc3545' : '#0fa86a' }};"></div>
                  </div>
                @endif
              </td>
              <td>{{ $r->unit }}</td>
              <td>
                <span class="badge-status {{ $isKritis ? 'badge-kritis' : 'badge-aman' }}">
                  {{ $isKritis ? 'Kritis' : 'Aman' }}
                </span>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="empty-state">
                <span class="empty-icon">📦</span>Tidak ada data ditemukan
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-panel-body d-flex justify-content-between align-items-center flex-wrap gap-2 border-top">
      <small class="text-muted">
        Menampilkan {{ $rows->firstItem() ?? 0 }}–{{ $rows->lastItem() ?? 0 }} dari {{ $rows->total() }} data
      </small>
      <div>
        {{ $rows->links() }}
      </div>
    </div>
  </div>
</div>
@endsection
