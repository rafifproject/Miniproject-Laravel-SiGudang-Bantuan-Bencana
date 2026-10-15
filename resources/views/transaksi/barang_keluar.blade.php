@extends('layouts.admin')

@section('title', 'Penyaluran Bantuan (Barang Keluar)')
@section('page_title', 'Transaksi Penyaluran Logistik Bencana')
@section('page_subtitle', 'Pencatatan distribusi barang bantuan ke posko pengungsian atau titik terdampak bencana')

@section('content')
<div class="row g-4">
  <!-- Kolom Kiri: Form Penyaluran Bantuan -->
  <div class="col-lg-5">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white py-3 border-0">
        <h5 class="fw-bold mb-0 text-danger"><i class="bi bi-box-arrow-up me-2"></i>Formulir Penyaluran ke Posko</h5>
      </div>
      <div class="card-body p-4 pt-0">
        <form method="POST" action="{{ route('barang-keluar.store') }}">
          @csrf

          <div class="mb-3">
            <label class="form-label fw-semibold">Pilih Produk Logistik <span class="text-danger">*</span></label>
            <select name="product_id" id="product_id_out" class="form-select @error('product_id') is-invalid @enderror" required onchange="updateStockDisplay()">
              <option value="">-- Pilih Barang Bantuan --</option>
              @foreach($products as $prod)
                <option value="{{ $prod->id }}" data-unit="{{ $prod->unit }}" data-stock="{{ $prod->stock }}"
                        {{ old('product_id') == $prod->id ? 'selected' : '' }}>
                  {{ $prod->name }} (Sisa: {{ number_format($prod->stock) }} {{ $prod->unit }})
                </option>
              @endforeach
            </select>
            <div id="stockBadgeAlert" class="mt-2 small text-muted"></div>
            @error('product_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Posko / Titik Tujuan Distribusi <span class="text-danger">*</span></label>
            <select name="distribution_target_id" class="form-select @error('distribution_target_id') is-invalid @enderror" required>
              <option value="">-- Pilih Titik Posko Bencana --</option>
              @foreach($targets as $tgt)
                <option value="{{ $tgt->id }}" {{ old('distribution_target_id') == $tgt->id ? 'selected' : '' }}>
                  {{ $tgt->name }} ({{ $tgt->location ?: 'Umum' }})
                </option>
              @endforeach
            </select>
            @error('distribution_target_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label fw-semibold">Jumlah Keluar <span class="text-danger">*</span></label>
              <input type="number" min="1" name="quantity" id="quantity_out" class="form-control @error('quantity') is-invalid @enderror" 
                     value="{{ old('quantity', 1) }}" required>
              @error('quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-6">
              <label class="form-label fw-semibold">Tanggal Penyaluran <span class="text-danger">*</span></label>
              <input type="date" name="date" class="form-control @error('date') is-invalid @enderror" 
                     value="{{ old('date', date('Y-m-d')) }}" required>
              @error('date') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Nomor Surat Jalan / Bukti Serah Terima</label>
            <input type="text" name="reference" class="form-control @error('reference') is-invalid @enderror" 
                   value="{{ old('reference', $suggestedRef) }}" placeholder="Contoh: TRX-2026-001">
            <small class="text-muted">Nomor dokumen serah terima bantuan atau referensi otomatis</small>
            @error('reference') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Catatan / Penerima di Posko</label>
            <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="2" 
                      placeholder="Nama penanggung jawab posko, nomor telepon, armada pengangkut...">{{ old('notes') }}</textarea>
            @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="mb-4 form-check form-switch p-2 ps-5 bg-light rounded-2 border">
            <input class="form-check-input" type="checkbox" name="is_public" id="is_public_out" value="1" 
                   {{ old('is_public', 1) ? 'checked' : '' }}>
            <label class="form-check-label fw-semibold text-dark small" for="is_public_out">
              Publikasikan di Portal Penyaluran Terbuka
            </label>
          </div>

          <button type="submit" class="btn btn-danger w-100 fw-semibold py-2">
            <i class="bi bi-box-arrow-up me-1"></i> Salurkan Bantuan (Kunci Stok & Simpan)
          </button>
        </form>
      </div>
    </div>
  </div>

  <!-- Kolom Kanan: 10 Penyaluran Terkini -->
  <div class="col-lg-7">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history me-2 text-danger"></i>10 Penyaluran Terkini</h5>
        <a href="{{ route('mutasi-stok.index', ['type' => 'OUT']) }}" class="btn btn-outline-danger btn-sm">
          Semua Riwayat
        </a>
      </div>
      <div class="card-body p-4 pt-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle small mb-0">
            <thead class="table-light">
              <tr>
                <th>Tanggal</th>
                <th>Produk</th>
                <th>Jumlah Disalurkan</th>
                <th>Tujuan Posko</th>
                <th>Petugas</th>
              </tr>
            </thead>
            <tbody>
              @forelse($recentOut as $out)
                <tr>
                  <td class="text-muted">{{ $out->date->format('d/m/Y') }}</td>
                  <td>
                    <div class="fw-semibold text-dark">{{ $out->product->name ?? '-' }}</div>
                    <span class="badge bg-secondary-subtle text-secondary font-monospace">{{ $out->reference }}</span>
                  </td>
                  <td>
                    <span class="badge bg-danger-subtle text-danger fs-6 fw-bold">
                      -{{ number_format($out->quantity) }} {{ $out->product->unit ?? '' }}
                    </span>
                  </td>
                  <td>
                    <span class="fw-bold text-dark">{{ $out->distributionTarget->name ?? '-' }}</span>
                    <div class="small text-muted">{{ $out->distributionTarget->location ?? '' }}</div>
                  </td>
                  <td class="text-muted">{{ $out->user->full_name ?: $out->user->username ?? '-' }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center py-4 text-muted">Belum ada transaksi penyaluran barang.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
  function updateStockDisplay() {
    const sel = document.getElementById('product_id_out');
    const badge = document.getElementById('stockBadgeAlert');
    const opt = sel.options[sel.selectedIndex];
    if (sel.value) {
      const stock = parseInt(opt.getAttribute('data-stock') || 0);
      const unit = opt.getAttribute('data-unit') || '';
      if (stock <= 0) {
        badge.innerHTML = '<span class="text-danger fw-bold"><i class="bi bi-x-circle me-1"></i>Stok kosong! Tidak dapat disalurkan.</span>';
      } else {
        badge.innerHTML = '<span class="text-success"><i class="bi bi-check-circle me-1"></i>Sisa persediaan fisik: <strong>' + stock + ' ' + unit + '</strong></span>';
      }
    } else {
      badge.innerHTML = '';
    }
  }
  document.addEventListener('DOMContentLoaded', updateStockDisplay);
</script>
@endpush
@endsection
