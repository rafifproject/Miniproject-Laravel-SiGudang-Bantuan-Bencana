@extends('layouts.admin')

@section('title', 'Manajemen Supplier & Donatur')
@section('page_title', 'Master Data Supplier & Lembaga Donor')
@section('page_subtitle', 'Pengelolaan mitra penyedia logistik kemanusiaan (PMI, BNPB, Yayasan, CSR Perusahaan)')

@section('content')
<div class="card border-0 shadow-sm mb-4">
  <div class="card-body p-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
      <div>
        <h5 class="fw-bold mb-1"><i class="bi bi-building me-2 text-primary"></i>Daftar Lembaga Donor & Supplier</h5>
        <p class="text-muted small mb-0">Total: <strong>{{ $suppliers->total() }}</strong> entitas mitra bantuan</p>
      </div>
      <div>
        <button type="button" class="btn btn-primary fw-semibold" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
          <i class="bi bi-plus-lg me-1"></i> Tambah Mitra / Donatur
        </button>
      </div>
    </div>

    <!-- Search & Filter -->
    <form method="GET" action="{{ route('suppliers.index') }}" class="row g-2 mb-4 bg-light p-3 rounded-3 border">
      <div class="col-md-6">
        <div class="input-group">
          <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
          <input type="text" name="search" class="form-control" placeholder="Cari nama mitra, kontak, email, alamat..." value="{{ request('search') }}">
        </div>
      </div>
      <div class="col-md-4">
        <select name="type" class="form-select">
          <option value="">Semua Tipe Lembaga</option>
          @foreach($types as $t)
            <option value="{{ $t }}" {{ request('type') === $t ? 'selected' : '' }}>{{ $t }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2 d-flex gap-1">
        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-filter me-1"></i>Filter</button>
        @if(request()->anyFilled(['search', 'type']))
          <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-counterclockwise"></i></a>
        @endif
      </div>
    </form>

    <!-- Table of Suppliers -->
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>Nama Lembaga / Donatur</th>
            <th>Tipe</th>
            <th>Kontak & Email</th>
            <th>Alamat</th>
            <th class="text-center">Pasokan Produk</th>
            <th class="text-center">Trans. Masuk</th>
            <th class="text-center">Publik</th>
            <th class="text-end" style="width: 120px;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($suppliers as $sup)
            <tr>
              <td>
                <div class="fw-bold text-dark">{{ $sup->name }}</div>
                <small class="text-muted">{{ Str::limit($sup->description, 40, '...') }}</small>
              </td>
              <td>
                <span class="badge bg-secondary-subtle text-secondary px-2 py-1">{{ $sup->type }}</span>
              </td>
              <td class="small">
                <div><i class="bi bi-telephone me-1 text-muted"></i>{{ $sup->contact ?: '-' }}</div>
                <div><i class="bi bi-envelope me-1 text-muted"></i>{{ $sup->email ?: '-' }}</div>
              </td>
              <td class="small text-muted">{{ Str::limit($sup->address, 35, '...') ?: '-' }}</td>
              <td class="text-center">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                  {{ $sup->products_count }} item
                </span>
              </td>
              <td class="text-center">
                <span class="badge bg-success-subtle text-success px-2 py-1">
                  {{ $sup->stock_movements_count }} kali
                </span>
              </td>
              <td class="text-center">
                @if($sup->is_public)
                  <span class="badge bg-success-subtle text-success"><i class="bi bi-check-lg"></i> Ya</span>
                @else
                  <span class="badge bg-secondary-subtle text-muted"><i class="bi bi-x-lg"></i> Tidak</span>
                @endif
              </td>
              <td class="text-end">
                <div class="btn-group btn-group-sm">
                  <button type="button" class="btn btn-outline-warning" title="Edit Supplier"
                          data-bs-toggle="modal" data-bs-target="#editSupplierModal{{ $sup->id }}">
                    <i class="bi bi-pencil"></i>
                  </button>
                  <button type="button" class="btn btn-outline-danger" title="Hapus Supplier"
                          data-bs-toggle="modal" data-bs-target="#deleteSupplierModal{{ $sup->id }}">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>

                <!-- Modal Edit Supplier -->
                <div class="modal fade text-start" id="editSupplierModal{{ $sup->id }}" tabindex="-1">
                  <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                      <form method="POST" action="{{ route('suppliers.update', $sup) }}">
                        @csrf
                        @method('PUT')
                        <div class="modal-header border-0 pb-0">
                          <h5 class="modal-title fw-bold text-primary">Edit Data Mitra</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body py-3">
                          <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Lembaga <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $sup->name) }}" required>
                          </div>
                          <div class="mb-3">
                            <label class="form-label fw-semibold">Tipe Lembaga <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" required>
                              <option value="Lembaga" {{ old('type', $sup->type) === 'Lembaga' ? 'selected' : '' }}>Lembaga</option>
                              <option value="Pemerintah" {{ old('type', $sup->type) === 'Pemerintah' ? 'selected' : '' }}>Pemerintah</option>
                              <option value="Perusahaan" {{ old('type', $sup->type) === 'Perusahaan' ? 'selected' : '' }}>Perusahaan</option>
                              <option value="Yayasan" {{ old('type', $sup->type) === 'Yayasan' ? 'selected' : '' }}>Yayasan</option>
                              <option value="NGO" {{ old('type', $sup->type) === 'NGO' ? 'selected' : '' }}>NGO</option>
                            </select>
                          </div>
                          <div class="row g-2 mb-3">
                            <div class="col-6">
                              <label class="form-label fw-semibold">No. Kontak / Telepon</label>
                              <input type="text" name="contact" class="form-control" value="{{ old('contact', $sup->contact) }}">
                            </div>
                            <div class="col-6">
                              <label class="form-label fw-semibold">Email</label>
                              <input type="email" name="email" class="form-control" value="{{ old('email', $sup->email) }}">
                            </div>
                          </div>
                          <div class="mb-3">
                            <label class="form-label fw-semibold">Alamat</label>
                            <textarea name="address" class="form-control" rows="2">{{ old('address', $sup->address) }}</textarea>
                          </div>
                          <div class="mb-3">
                            <label class="form-label fw-semibold">Deskripsi</label>
                            <textarea name="description" class="form-control" rows="2">{{ old('description', $sup->description) }}</textarea>
                          </div>
                          <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_public" value="1" id="is_public_edit{{ $sup->id }}" {{ old('is_public', $sup->is_public) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold small" for="is_public_edit{{ $sup->id }}">Tampilkan di Portal Publik</label>
                          </div>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                          <button type="submit" class="btn btn-primary fw-semibold">Simpan Perubahan</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>

                <!-- Modal Hapus Supplier -->
                <div class="modal fade text-start" id="deleteSupplierModal{{ $sup->id }}" tabindex="-1">
                  <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                      <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold">Hapus Supplier / Donatur</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>
                      <div class="modal-body py-4 text-center">
                        <div class="text-danger mb-3" style="font-size: 3rem;">
                          <i class="bi bi-exclamation-circle"></i>
                        </div>
                        <p class="mb-1">Hapus supplier <strong>{{ $sup->name }}</strong>?</p>
                        <small class="text-muted">Supplier yang tercatat dalam transaksi barang masuk tidak dapat dihapus.</small>
                      </div>
                      <div class="modal-footer border-0 pt-0 justify-content-center">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                        <form method="POST" action="{{ route('suppliers.destroy', $sup) }}">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-danger px-4 fw-semibold">Hapus</button>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="text-center py-4 text-muted">Belum ada data supplier / lembaga donor.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-4">
      {{ $suppliers->links() }}
    </div>
  </div>
</div>

<!-- Modal Tambah Supplier -->
<div class="modal fade text-start" id="addSupplierModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <form method="POST" action="{{ route('suppliers.store') }}">
        @csrf
        <div class="modal-header border-0 pb-0">
          <h5 class="modal-title fw-bold text-primary"><i class="bi bi-building me-2"></i>Tambah Mitra Donatur Baru</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body py-3">
          <div class="mb-3">
            <label class="form-label fw-semibold">Nama Lembaga <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" placeholder="Contoh: PMI, BNPB, PT..." required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Tipe Lembaga <span class="text-danger">*</span></label>
            <select name="type" class="form-select" required>
              <option value="Lembaga">Lembaga</option>
              <option value="Pemerintah">Pemerintah</option>
              <option value="Perusahaan">Perusahaan</option>
              <option value="Yayasan">Yayasan</option>
              <option value="NGO">NGO</option>
            </select>
          </div>
          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label fw-semibold">No. Telepon / Kontak</label>
              <input type="text" name="contact" class="form-control" placeholder="021-xxxxxxx">
            </div>
            <div class="col-6">
              <label class="form-label fw-semibold">Email</label>
              <input type="email" name="email" class="form-control" placeholder="info@lembaga.org">
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Alamat</label>
            <textarea name="address" class="form-control" rows="2" placeholder="Kota / Kantor cabang..."></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Deskripsi</label>
            <textarea name="description" class="form-control" rows="2" placeholder="Catatan profil donatur..."></textarea>
          </div>
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="is_public" value="1" id="is_public_add" checked>
            <label class="form-check-label fw-semibold small" for="is_public_add">Tampilkan di Portal Publik</label>
          </div>
        </div>
        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary fw-semibold">Simpan Mitra</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
