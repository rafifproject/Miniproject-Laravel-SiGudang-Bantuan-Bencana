@extends('layouts.admin')

@section('title', 'Manajemen Kategori')
@section('page_title', 'Master Data Kategori Logistik')
@section('page_subtitle', 'Pengelompokan logistik bantuan bencana (Pangan, Sandang, Kesehatan, Peralatan, Higienitas)')

@section('content')
<div class="row g-4">
  <!-- Kolom Kiri: Tabel Kategori -->
  <div class="col-lg-8">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-tags me-2"></i>Daftar Kategori Bantuan</h5>
        <span class="badge bg-primary rounded-pill">{{ $categories->count() }} Kategori</span>
      </div>
      <div class="card-body p-4 pt-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th style="width: 50px;">#</th>
                <th>Nama Kategori</th>
                <th>Deskripsi</th>
                <th class="text-center">Jumlah Produk</th>
                <th class="text-end" style="width: 120px;">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($categories as $index => $cat)
                <tr>
                  <td class="text-muted">{{ $index + 1 }}</td>
                  <td>
                    <span class="fw-bold text-dark">{{ $cat->name }}</span>
                  </td>
                  <td class="text-muted small">{{ $cat->description ?: '-' }}</td>
                  <td class="text-center">
                    <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-1 rounded-pill">
                      {{ $cat->products_count }} item
                    </span>
                  </td>
                  <td class="text-end">
                    <div class="btn-group btn-group-sm">
                      <button type="button" class="btn btn-outline-warning" title="Edit Kategori"
                              data-bs-toggle="modal" data-bs-target="#editCategoryModal{{ $cat->id }}">
                        <i class="bi bi-pencil"></i>
                      </button>
                      <button type="button" class="btn btn-outline-danger" title="Hapus Kategori"
                              data-bs-toggle="modal" data-bs-target="#deleteCategoryModal{{ $cat->id }}">
                        <i class="bi bi-trash"></i>
                      </button>
                    </div>

                    <!-- Modal Edit Kategori -->
                    <div class="modal fade text-start" id="editCategoryModal{{ $cat->id }}" tabindex="-1">
                      <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow">
                          <form method="POST" action="{{ route('categories.update', $cat) }}">
                            @csrf
                            @method('PUT')
                            <div class="modal-header border-0 pb-0">
                              <h5 class="modal-title fw-bold text-primary">Edit Kategori</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body py-3">
                              <div class="mb-3">
                                <label class="form-label fw-semibold">Nama Kategori <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $cat->name) }}" required>
                              </div>
                              <div class="mb-3">
                                <label class="form-label fw-semibold">Deskripsi</label>
                                <textarea name="description" class="form-control" rows="3">{{ old('description', $cat->description) }}</textarea>
                              </div>
                            </div>
                            <div class="modal-footer border-0 pt-0">
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                              <button type="submit" class="btn btn-primary fw-semibold">Perbarui Kategori</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>

                    <!-- Modal Hapus Kategori -->
                    <div class="modal fade text-start" id="deleteCategoryModal{{ $cat->id }}" tabindex="-1">
                      <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow">
                          <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title fw-bold">Hapus Kategori</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                          </div>
                          <div class="modal-body py-4 text-center">
                            <div class="text-danger mb-3" style="font-size: 3rem;">
                              <i class="bi bi-exclamation-circle"></i>
                            </div>
                            <p class="mb-1">Hapus kategori <strong>{{ $cat->name }}</strong>?</p>
                            <small class="text-muted">Kategori yang masih memiliki produk tidak dapat dihapus.</small>
                          </div>
                          <div class="modal-footer border-0 pt-0 justify-content-center">
                            <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                            <form method="POST" action="{{ route('categories.destroy', $cat) }}">
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
                  <td colspan="5" class="text-center py-4 text-muted">Belum ada kategori terdaftar.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Kolom Kanan: Form Tambah Kategori -->
  <div class="col-lg-4">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white py-3 border-0">
        <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-plus-circle me-2"></i>Tambah Kategori Baru</h5>
      </div>
      <div class="card-body p-4 pt-0">
        <form method="POST" action="{{ route('categories.store') }}">
          @csrf
          <div class="mb-3">
            <label for="name" class="form-label fw-semibold">Nama Kategori <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" 
                   value="{{ old('name') }}" placeholder="Contoh: Logistik Darurat, Pangan..." required>
            @error('name')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="mb-4">
            <label for="description" class="form-label fw-semibold">Deskripsi</label>
            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" 
                      placeholder="Penjelasan kelompok kategori...">{{ old('description') }}</textarea>
            @error('description')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <button type="submit" class="btn btn-primary w-100 fw-semibold">
            <i class="bi bi-save me-1"></i> Simpan Kategori
          </button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
