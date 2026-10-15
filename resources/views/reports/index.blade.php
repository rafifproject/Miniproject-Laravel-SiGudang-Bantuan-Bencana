@extends('layouts.admin')

@section('title', 'Laporan Gudang & Audit')
@section('page_title', 'Laporan & Rekapitulasi Inventaris')
@section('page_subtitle', 'Analitik stok, evaluasi nilai persediaan, dan rekapitulasi distribusi bantuan bencana')

@section('content')
<div class="card border-0 shadow-sm mb-4">
  <div class="card-body p-4">
    <!-- Filter Laporan -->
    <form method="GET" action="{{ route('admin.laporan') }}" class="row g-3 mb-4 bg-light p-3 rounded-3 border align-items-end">
      <div class="col-lg-4 col-md-6">
        <label class="form-label small fw-semibold text-muted mb-1">Tipe Laporan</label>
        <select name="type" class="form-select" onchange="this.form.submit()">
          <option value="stok_saat_ini" {{ $type === 'stok_saat_ini' ? 'selected' : '' }}>📦 Status Stok Logistik Saat Ini</option>
          <option value="stok_kategori" {{ $type === 'stok_kategori' ? 'selected' : '' }}>🏷️ Rekapitulasi Stok per Kategori</option>
          <option value="barang_masuk" {{ $type === 'barang_masuk' ? 'selected' : '' }}>📥 Laporan Penerimaan Donasi Masuk</option>
          <option value="barang_keluar" {{ $type === 'barang_keluar' ? 'selected' : '' }}>📤 Laporan Penyaluran ke Posko Bencana</option>
          <option value="mutasi_stok" {{ $type === 'mutasi_stok' ? 'selected' : '' }}>🔄 Rekapitulasi Mutasi Stok (IN & OUT)</option>
          <option value="distribusi_tujuan" {{ $type === 'distribusi_tujuan' ? 'selected' : '' }}>📍 Akumulasi Distribusi per Titik Posko</option>
          <option value="nilai_persediaan" {{ $type === 'nilai_persediaan' ? 'selected' : '' }}>💰 Valuasi / Nilai Finansial Persediaan</option>
          <option value="aktivitas_user" {{ $type === 'aktivitas_user' ? 'selected' : '' }}>👤 Rekapitulasi Aktivitas Operator Staff</option>
        </select>
      </div>

      @if(in_array($type, ['barang_masuk', 'barang_keluar', 'mutasi_stok', 'distribusi_tujuan', 'aktivitas_user']))
        <div class="col-lg-3 col-md-3">
          <label class="form-label small fw-semibold text-muted mb-1">Dari Tanggal</label>
          <input type="date" name="date_start" class="form-control" value="{{ $dateStart }}">
        </div>
        <div class="col-lg-3 col-md-3">
          <label class="form-label small fw-semibold text-muted mb-1">Sampai Tanggal</label>
          <input type="date" name="date_end" class="form-control" value="{{ $dateEnd }}">
        </div>
      @else
        <div class="col-lg-6 col-md-6"></div>
      @endif

      <div class="col-lg-2 col-md-12 d-flex gap-2">
        <button type="submit" class="btn btn-primary w-100 fw-semibold">
          <i class="bi bi-filter me-1"></i> Tampilkan
        </button>
        <button type="button" class="btn btn-outline-dark" onclick="window.print()" title="Cetak Laporan">
          <i class="bi bi-printer"></i>
        </button>
      </div>
    </form>

    <!-- Ringkasan Agregat Laporan -->
    @if(!empty($summary))
      <div class="row g-3 mb-4">
        @foreach($summary as $label => $val)
          <div class="col-md">
            <div class="p-3 bg-white rounded-3 border shadow-sm text-center">
              <small class="text-muted text-uppercase fw-semibold">{{ $label }}</small>
              <h4 class="fw-bold text-primary mt-1 mb-0">{{ $val }}</h4>
            </div>
          </div>
        @endforeach
      </div>
    @endif

    <!-- Data Table Sesuai Tipe Laporan -->
    <div class="table-responsive">
      @if($type === 'stok_kategori')
        <table class="table table-bordered table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Nama Kategori</th>
              <th class="text-center">Jenis Item</th>
              <th class="text-end">Total Fisik Stok</th>
              <th class="text-end">Estimasi Nilai Total</th>
              <th class="text-center">Item Menipis (Kritis)</th>
            </tr>
          </thead>
          <tbody>
            @foreach($data as $row)
              <tr>
                <td class="fw-bold text-dark">{{ $row->name }}</td>
                <td class="text-center">{{ $row->total_products }} produk</td>
                <td class="text-end fw-semibold">{{ number_format($row->total_stock) }} unit</td>
                <td class="text-end fw-bold text-success">Rp {{ number_format($row->total_value, 0, ',', '.') }}</td>
                <td class="text-center">
                  @if($row->critical_items > 0)
                    <span class="badge bg-danger">{{ $row->critical_items }} item kritis</span>
                  @else
                    <span class="badge bg-success">Aman</span>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>

      @elseif($type === 'barang_masuk')
        <table class="table table-bordered table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Tanggal</th>
              <th>No. Referensi</th>
              <th>Barang Bantuan</th>
              <th>Kategori</th>
              <th class="text-end">Jumlah</th>
              <th>Sumber / Donatur</th>
              <th>Operator</th>
            </tr>
          </thead>
          <tbody>
            @foreach($data as $m)
              <tr>
                <td>{{ $m->date->format('d/m/Y') }}</td>
                <td class="font-monospace text-muted">{{ $m->reference ?? '-' }}</td>
                <td class="fw-bold text-dark">{{ $m->product->name ?? '-' }}</td>
                <td><span class="badge bg-info-subtle text-info">{{ $m->product->category->name ?? '-' }}</span></td>
                <td class="text-end fw-bold text-success">+{{ number_format($m->quantity) }} {{ $m->product->unit ?? '' }}</td>
                <td>{{ $m->supplier->name ?? 'Donasi Umum' }}</td>
                <td class="small text-muted">{{ $m->user->full_name ?: $m->user->username ?? '-' }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>

      @elseif($type === 'barang_keluar')
        <table class="table table-bordered table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Tanggal</th>
              <th>No. Dokumen</th>
              <th>Barang Bantuan</th>
              <th class="text-end">Jumlah Disalurkan</th>
              <th>Tujuan Posko / Wilayah</th>
              <th>Operator</th>
            </tr>
          </thead>
          <tbody>
            @foreach($data as $m)
              <tr>
                <td>{{ $m->date->format('d/m/Y') }}</td>
                <td class="font-monospace text-muted">{{ $m->reference ?? '-' }}</td>
                <td class="fw-bold text-dark">{{ $m->product->name ?? '-' }}</td>
                <td class="text-end fw-bold text-danger">-{{ number_format($m->quantity) }} {{ $m->product->unit ?? '' }}</td>
                <td>
                  <strong>{{ $m->distributionTarget->name ?? '-' }}</strong>
                  <div class="small text-muted">{{ $m->distributionTarget->location ?? '' }}</div>
                </td>
                <td class="small text-muted">{{ $m->user->full_name ?: $m->user->username ?? '-' }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>

      @elseif($type === 'distribusi_tujuan')
        <table class="table table-bordered table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Titik Posko / Lokasi Bencana</th>
              <th>Wilayah Kabupaten / Kota</th>
              <th class="text-center">Frekuensi Distribusi</th>
              <th class="text-end">Total Barang Diterima</th>
            </tr>
          </thead>
          <tbody>
            @foreach($data as $row)
              <tr>
                <td class="fw-bold text-dark">{{ $row->target_name }}</td>
                <td>{{ $row->location ?: '-' }}</td>
                <td class="text-center">{{ $row->total_tx }} kali</td>
                <td class="text-end fw-bold text-primary">{{ number_format($row->total_qty) }} unit</td>
              </tr>
            @endforeach
          </tbody>
        </table>

      @elseif($type === 'nilai_persediaan')
        <table class="table table-bordered table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>SKU</th>
              <th>Nama Produk</th>
              <th>Kategori</th>
              <th class="text-end">Sisa Stok</th>
              <th class="text-end">Estimasi Harga Satuan</th>
              <th class="text-end">Total Nilai Fisik</th>
            </tr>
          </thead>
          <tbody>
            @foreach($data as $row)
              <tr>
                <td class="font-monospace text-muted">{{ $row->sku }}</td>
                <td class="fw-bold text-dark">{{ $row->name }}</td>
                <td>{{ $row->category }}</td>
                <td class="text-end fw-semibold">{{ number_format($row->stock) }} {{ $row->unit }}</td>
                <td class="text-end">Rp {{ number_format($row->price, 0, ',', '.') }}</td>
                <td class="text-end fw-bold text-success">Rp {{ number_format($row->total_value, 0, ',', '.') }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>

      @elseif($type === 'aktivitas_user')
        <table class="table table-bordered table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Username</th>
              <th>Nama Lengkap</th>
              <th>Hak Akses (Role)</th>
              <th class="text-center">Total Transaksi</th>
              <th class="text-center">Barang Masuk</th>
              <th class="text-center">Penyaluran Keluar</th>
            </tr>
          </thead>
          <tbody>
            @foreach($data as $row)
              <tr>
                <td class="font-monospace fw-semibold">{{ $row->username }}</td>
                <td>{{ $row->full_name }}</td>
                <td><span class="badge bg-secondary">{{ ucfirst($row->role) }}</span></td>
                <td class="text-center fw-bold">{{ $row->total_tx }} aksi</td>
                <td class="text-center text-success fw-semibold">{{ $row->total_in }}</td>
                <td class="text-center text-danger fw-semibold">{{ $row->total_out }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>

      @else {{-- stok_saat_ini --}}
        <table class="table table-bordered table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>SKU</th>
              <th>Nama Barang Bantuan</th>
              <th>Kategori</th>
              <th class="text-end">Stok Fisik</th>
              <th class="text-end">Batas Min.</th>
              <th class="text-center">Status</th>
              <th class="text-end">Estimasi Harga</th>
              <th class="text-end">Total Nilai</th>
            </tr>
          </thead>
          <tbody>
            @foreach($data as $p)
              <tr>
                <td class="font-monospace text-muted">{{ $p->sku }}</td>
                <td class="fw-bold text-dark">{{ $p->name }}</td>
                <td><span class="badge bg-info-subtle text-info">{{ $p->category->name ?? '-' }}</span></td>
                <td class="text-end fw-bold {{ $p->isLowStock() ? 'text-danger' : 'text-dark' }}">
                  {{ number_format($p->stock) }} {{ $p->unit }}
                </td>
                <td class="text-end text-muted">{{ number_format($p->minimum_stock) }} {{ $p->unit }}</td>
                <td class="text-center">
                  @if($p->isLowStock())
                    <span class="badge bg-danger">Kritis</span>
                  @else
                    <span class="badge bg-success">Aman</span>
                  @endif
                </td>
                <td class="text-end">{{ $p->formatted_price }}</td>
                <td class="text-end fw-bold text-success">
                  Rp {{ number_format($p->stock * $p->price, 0, ',', '.') }}
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      @endif
    </div>
  </div>
</div>
@endsection
