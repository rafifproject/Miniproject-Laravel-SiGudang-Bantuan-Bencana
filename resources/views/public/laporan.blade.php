@extends('layouts.app')

@section('title', 'Laporan Publik')

@section('content')
<div class="container py-4">
  <div class="mb-4">
    <h1 class="section-heading">Laporan <span>Publik</span></h1>
    <div class="section-divider"></div>
    <p class="text-muted">Data laporan yang diizinkan untuk diakses oleh masyarakat umum.</p>
  </div>

  <form method="GET" action="{{ route('public.laporan') }}" class="filter-bar mb-4">
    <div class="row g-2 align-items-end">
      <div class="col-md-3">
        <label class="form-label small fw-semibold mb-1">Jenis Laporan</label>
        <select name="jenis" class="form-select">
          @foreach($jenisList as $k => $v)
            <option value="{{ $k }}" {{ $jenisLaporan === $k ? 'selected' : '' }}>{{ $v }}</option>
          @endforeach
        </select>
      </div>
      @if($jenisLaporan !== 'stok')
        <div class="col-md-2">
          <label class="form-label small fw-semibold mb-1">Dari</label>
          <input type="date" name="from" class="form-control" value="{{ $dateFrom }}">
        </div>
        <div class="col-md-2">
          <label class="form-label small fw-semibold mb-1">Sampai</label>
          <input type="date" name="to" class="form-control" value="{{ $dateTo }}">
        </div>
      @endif
      <div class="col-md-2 d-flex gap-1">
        <button type="submit" class="btn btn-primary flex-fill"><i class="bi bi-search"></i> Tampilkan</button>
      </div>
    </div>
  </form>

  <div class="card-panel">
    <div class="card-panel-header">
      <span>{{ $jenisList[$jenisLaporan] }}</span>
      <small class="text-muted">{{ count($data) }} baris data</small>
    </div>
    <div class="table-responsive">
      <table class="table-modern w-100">
        <thead>
          <tr>
            @foreach($columns as $col)
              <th>{{ $col }}</th>
            @endforeach
          </tr>
        </thead>
        <tbody>
          @forelse($data as $row)
            <tr>
              @foreach(array_values((array)$row) as $i => $cell)
                <td>
                  @if($columns[$i] === 'Status')
                    <span class="badge-status {{ $cell === 'Kritis' ? 'badge-kritis' : 'badge-aman' }}">{{ $cell }}</span>
                  @elseif($columns[$i] === 'Jumlah')
                    <strong>{{ $cell }}</strong>
                  @else
                    {{ $cell }}
                  @endif
                </td>
              @endforeach
            </tr>
          @empty
            <tr>
              <td colspan="{{ count($columns) }}" class="empty-state">
                <span class="empty-icon">📊</span>Tidak ada data untuk parameter ini
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
