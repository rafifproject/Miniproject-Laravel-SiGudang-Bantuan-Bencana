@extends('layouts.admin')

@section('title', 'Catat Barang Masuk (Donasi/Penerimaan)')
@section('page_title', 'Transaksi Barang Masuk / Donasi')
@section('page_subtitle', 'Pencatatan resmi penerimaan logistik bantuan bencana ke dalam gudang fisik secara atomik')

@section('content')
<div class="row g-4">
  <!-- Kolom Kiri: Form Penerimaan Bantuan -->
  <div class="col-lg-5">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white py-3 border-0">
        <h5 class="fw-bold mb-0 text-success"><i class="bi bi-box-arrow-in-down me-2"></i>Formulir Penerimaan Donasi</h5>
      </div>
      <div class="card-body p-4 pt-0">
        <form method="POST" action="{{ route('barang-masuk.store') }}">
          @csrf

          <div class="mb-3">
            <label class="form-label fw-semibold">Pilih Produk Logistik <span class="text-danger">*</span></label>
            <select name="product_id" id="product_id" class="form-select @error('product_id') is-invalid @enderror" required>
              <option value="">-- Pilih Barang Bantuan --</option>
              @foreach($products as $prod)
                <option value="{{ $prod->id }}" data-unit="{{ $prod->unit }}" data-stock="{{ $prod->stock }}"
                        {{ old('product_id') == $prod->id ? 'selected' : '' }}>
                  {{ $prod->name }} (Sisa: {{ $prod->stock }} {{ $prod->unit }})
                </option>
              @endforeach
            </select>
            @error('product_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Lembaga Donor / Supplier</label>
            <select name="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror">
              <option value="">-- Donasi Mandiri / Umum --</option>
              @foreach($suppliers as $sup)
                <option value="{{ $sup->id }}" {{ old('supplier_id') == $sup->id ? 'selected' : '' }}>
                  {{ $sup->name }} ({{ $sup->type }})
                </option>
              @endforeach
            </select>
            @error('supplier_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label fw-semibold">Jumlah Masuk <span class="text-danger">*</span></label>
              <input type="number" min="1" name="quantity" class="form-control @error('quantity') is-invalid @enderror" 
                     value="{{ old('quantity', 1) }}" required>
              @error('quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-6">
              <label class="form-label fw-semibold">Tanggal Penerimaan <span class="text-danger">*</span></label>
              <input type="date" name="date" class="form-control @error('date') is-invalid @enderror" 
                     value="{{ old('date', date('Y-m-d')) }}" required>
              @error('date') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Nomor Referensi / Surat Jalan</label>
            <input type="text" name="reference" class="form-control @error('reference') is-invalid @enderror" 
                   value="{{ old('reference', $suggestedRef) }}" placeholder="Contoh: REF-2026-001">
            <small class="text-muted">Nomor surat jalan donasi atau referensi otomatis</small>
            @error('reference') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Catatan / Keterangan Masuk</label>
            <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="2" 
                      placeholder="Kondisi barang, armada pengirim, dll...">{{ old('notes') }}</textarea>
            @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="mb-4 form-check form-switch p-2 ps-5 bg-light rounded-2 border">
            <input class="form-check-input" type="checkbox" name="is_public" id="is_public" value="1" 
                   {{ old('is_public', 1) ? 'checked' : '' }}>
            <label class="form-check-label fw-semibold text-dark small" for="is_public">
              Publikasikan di Portal Donasi Terbuka
            </label>
          </div>

          <button type="submit" class="btn btn-success w-100 fw-semibold py-2">
            <i class="bi bi-box-arrow-in-down me-1"></i> Simpan Transaksi Masuk (Atomik)
          </button>
        </form>
      </div>
    </div>
  </div>

  <!-- Kolom Kanan: 10 Transaksi Penerimaan Terkini -->
  <div class="col-lg-7">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history me-2 text-primary"></i>10 Penerimaan Bantuan Terkini</h5>
        <a href="{{ route('mutasi-stok.index', ['type' => 'IN']) }}" class="btn btn-outline-primary btn-sm">
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
                <th>Jumlah</th>
                <th>Sumber / Donatur</th>
                <th>Petugas</th>
              </tr>
            </thead>
            <tbody>
              @forelse($recentIn as $in)
                <tr>
                  <td class="text-muted">{{ $in->date->format('d/m/Y') }}</td>
                  <td>
                    <div class="fw-semibold text-dark">{{ $in->product->name ?? '-' }}</div>
                    <span class="badge bg-secondary-subtle text-secondary font-monospace">{{ $in->reference }}</span>
                  </td>
                  <td>
                    <span class="badge bg-success-subtle text-success fs-6 fw-bold">
                      +{{ number_format($in->quantity) }} {{ $in->product->unit ?? '' }}
                    </span>
                  </td>
                  <td>{{ $in->supplier->name ?? 'Donasi Umum' }}</td>
                  <td class="text-muted">{{ $in->user->full_name ?: $in->user->username ?? '-' }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center py-4 text-muted">Belum ada transaksi barang masuk.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
