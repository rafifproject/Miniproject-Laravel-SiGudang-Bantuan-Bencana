@extends('layouts.app')

@section('title', 'Riwayat Bantuan Masuk')

@section('content')
<div class="container py-4">
  <div class="mb-4">
    <h1 class="section-heading">Riwayat <span>Bantuan Masuk</span></h1>
    <div class="section-divider"></div>
    <p class="text-muted">Catatan penerimaan bantuan dari supplier dan donatur.</p>
  </div>

  <form method="GET" action="{{ route('public.masuk') }}" class="filter-bar mb-4">
    <div class="row g-2 align-items-end">
      <div class="col-md-2">
        <label class="form-label small fw-semibold mb-1">Dari</label>
        <input type="date" name="from" class="form-control" value="{{ $dateFrom }}">
      </div>
      <div class="col-md-2">
        <label class="form-label small fw-semibold mb-1">Sampai</label>
        <input type="date" name="to" class="form-control" value="{{ $dateTo }}">
      </div>
      <div class="col-md-3">
        <select name="cat" class="form-select">
          <option value="">Semua Kategori</option>
          @foreach($cats as $c)
            <option value="{{ $c->id }}" {{ $catId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <select name="supplier" class="form-select">
          <option value="">Semua Donatur</option>
          @foreach($sups as $s)
            <option value="{{ $s->id }}" {{ $supId == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2 d-flex gap-1">
        <button type="submit" class="btn btn-primary flex-fill"><i class="bi bi-funnel"></i> Filter</button>
        <a href="{{ route('public.masuk') }}" class="btn btn-outline-secondary"><i class="bi bi-x"></i></a>
      </div>
    </div>
  </form>

  <div class="card-panel">
    <div class="card-panel-header">
      <span>Data Bantuan Masuk</span>
      <small class="text-muted">{{ $rows->total() }} transaksi</small>
    </div>
    <div class="table-responsive">
      <table class="table-modern w-100">
        <thead>
          <tr>
            <th>Tanggal</th>
            <th>Nama Barang</th>
            <th>Kategori</th>
            <th>Jumlah</th>
            <th>Satuan</th>
            <th>Donatur</th>
            <th>Keterangan</th>
          </tr>
        </thead>
        <tbody>
          @forelse($rows as $r)
            <tr>
              <td>{{ $r->date->format('d M Y') }}</td>
              <td><strong>{{ $r->product->name ?? '-' }}</strong></td>
              <td>{{ $r->product->category->name ?? '-' }}</td>
              <td class="fw-bold text-success">+{{ number_format($r->quantity) }}</td>
              <td>{{ $r->product->unit ?? '-' }}</td>
              <td>{{ $r->supplier->name ?? '-' }}</td>
              <td class="text-muted small">{{ $r->notes ?: '-' }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="empty-state">
                <span class="empty-icon">📥</span>Tidak ada data
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
