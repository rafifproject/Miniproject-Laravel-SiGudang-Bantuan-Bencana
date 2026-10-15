@extends('layouts.admin')

@section('title', 'Manajemen Pengguna Sistem')
@section('page_title', 'Kontrol Pengguna & Otorisasi RBAC')
@section('page_subtitle', 'Kelola akun operator sistem (Admin & Staff), profil relasional, dan status aktif')

@section('content')
<div class="card border-0 shadow-sm mb-4">
  <div class="card-body p-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
      <div>
        <h5 class="fw-bold mb-1"><i class="bi bi-people me-2 text-primary"></i>Daftar Operator Sistem</h5>
        <small class="text-muted">Total: <strong>{{ $users->total() }}</strong> akun terdaftar</small>
      </div>
      <div>
        <button type="button" class="btn btn-primary fw-semibold" data-bs-toggle="modal" data-bs-target="#addUserModal">
          <i class="bi bi-person-plus me-1"></i> Tambah Pengguna Baru
        </button>
      </div>
    </div>

    <!-- Filter & Search -->
    <form method="GET" action="{{ route('users.index') }}" class="row g-2 mb-4 bg-light p-3 rounded-3 border">
      <div class="col-md-6">
        <input type="text" name="search" class="form-control" placeholder="Cari username, nama lengkap, email..." value="{{ request('search') }}">
      </div>
      <div class="col-md-4">
        <select name="role" class="form-select">
          <option value="">Semua Role</option>
          <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Administrator</option>
          <option value="staff" {{ request('role') === 'staff' ? 'selected' : '' }}>Staff Operasional</option>
        </select>
      </div>
      <div class="col-md-2 d-flex gap-1">
        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-filter me-1"></i>Filter</button>
        @if(request()->anyFilled(['search', 'role']))
          <a href="{{ route('users.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-counterclockwise"></i></a>
        @endif
      </div>
    </form>

    <!-- Table of Users -->
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>Pengguna</th>
            <th>Role</th>
            <th>Kontak & Alamat (Profil 1:1)</th>
            <th class="text-center">Total Transaksi</th>
            <th class="text-center">Status</th>
            <th class="text-end" style="width: 140px;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($users as $user)
            <tr>
              <td>
                <div class="d-flex align-items-center gap-3">
                  <div class="avatar bg-primary-subtle text-primary fw-bold rounded-circle p-2 text-center" style="width: 40px; height: 40px;">
                    {{ strtoupper(substr($user->username, 0, 2)) }}
                  </div>
                  <div>
                    <div class="fw-bold text-dark">{{ $user->full_name ?: $user->username }}</div>
                    <small class="text-muted font-monospace">{{ $user->username }}</small>
                    @if($user->id === Auth::id())
                      <span class="badge bg-primary-subtle text-primary ms-1 small">Anda</span>
                    @endif
                  </div>
                </div>
              </td>
              <td>
                @if($user->role === 'admin')
                  <span class="badge bg-primary px-3 py-1 rounded-pill">
                    <i class="bi bi-shield-check me-1"></i>Admin
                  </span>
                @else
                  <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill">
                    <i class="bi bi-person me-1"></i>Staff
                  </span>
                @endif
              </td>
              <td class="small">
                <div><i class="bi bi-envelope me-1 text-muted"></i>{{ $user->email ?: '-' }}</div>
                <div><i class="bi bi-telephone me-1 text-muted"></i>{{ $user->profile->phone ?? '-' }}</div>
                <div class="text-muted">{{ Str::limit($user->profile->address ?? '', 30, '...') ?: '-' }}</div>
              </td>
              <td class="text-center">
                <span class="badge bg-secondary-subtle text-secondary px-2 py-1">
                  {{ $user->stock_movements_count }} aksi
                </span>
              </td>
              <td class="text-center">
                @if($user->is_active)
                  <span class="badge bg-success-subtle text-success">Aktif</span>
                @else
                  <span class="badge bg-danger-subtle text-danger">Nonaktif</span>
                @endif
              </td>
              <td class="text-end">
                <div class="btn-group btn-group-sm">
                  <button type="button" class="btn btn-outline-warning" title="Edit Profil/Role"
                          data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}">
                    <i class="bi bi-pencil"></i>
                  </button>
                  <button type="button" class="btn btn-outline-info" title="Reset Password"
                          data-bs-toggle="modal" data-bs-target="#resetPasswordModal{{ $user->id }}">
                    <i class="bi bi-key"></i>
                  </button>
                  @if($user->id !== Auth::id())
                    <button type="button" class="btn btn-outline-danger" title="Hapus User"
                            data-bs-toggle="modal" data-bs-target="#deleteUserModal{{ $user->id }}">
                      <i class="bi bi-trash"></i>
                    </button>
                  @endif
                </div>

                <!-- Modal Edit User -->
                <div class="modal fade text-start" id="editUserModal{{ $user->id }}" tabindex="-1">
                  <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                      <form method="POST" action="{{ route('users.update', $user) }}">
                        @csrf
                        @method('PUT')
                        <div class="modal-header border-0 pb-0">
                          <h5 class="modal-title fw-bold text-primary">Edit Pengguna: {{ $user->username }}</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body py-3">
                          <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="full_name" class="form-control" value="{{ old('full_name', $user->full_name) }}" required>
                          </div>
                          <div class="row g-2 mb-3">
                            <div class="col-6">
                              <label class="form-label fw-semibold">Hak Akses (Role) <span class="text-danger">*</span></label>
                              <select name="role" class="form-select" {{ $user->id === Auth::id() ? 'disabled' : '' }} required>
                                <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Administrator</option>
                                <option value="staff" {{ old('role', $user->role) === 'staff' ? 'selected' : '' }}>Staff Operasional</option>
                              </select>
                            </div>
                            <div class="col-6">
                              <label class="form-label fw-semibold">Email</label>
                              <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}">
                            </div>
                          </div>
                          <div class="mb-3">
                            <label class="form-label fw-semibold">No. Telepon / HP</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->profile->phone ?? '') }}">
                          </div>
                          <div class="mb-3">
                            <label class="form-label fw-semibold">Alamat Penugasan / Posko</label>
                            <textarea name="address" class="form-control" rows="2">{{ old('address', $user->profile->address ?? '') }}</textarea>
                          </div>
                          @if($user->id !== Auth::id())
                            <div class="form-check form-switch">
                              <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active_edit{{ $user->id }}" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                              <label class="form-check-label fw-semibold small" for="is_active_edit{{ $user->id }}">Status Akun Aktif</label>
                            </div>
                          @endif
                        </div>
                        <div class="modal-footer border-0 pt-0">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                          <button type="submit" class="btn btn-primary fw-semibold">Perbarui</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>

                <!-- Modal Reset Password -->
                <div class="modal fade text-start" id="resetPasswordModal{{ $user->id }}" tabindex="-1">
                  <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                      <form method="POST" action="{{ route('users.reset-password', $user) }}">
                        @csrf
                        <div class="modal-header border-0 pb-0">
                          <h5 class="modal-title fw-bold text-info"><i class="bi bi-key me-2"></i>Reset Password Pengguna</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body py-3">
                          <p class="small text-muted mb-3">
                            Mengatur ulang kata sandi untuk akun <strong>{{ $user->username }}</strong> ({{ $user->full_name }}).
                          </p>
                          <div class="mb-3">
                            <label class="form-label fw-semibold">Password Baru <span class="text-danger">*</span></label>
                            <input type="password" name="new_password" class="form-control" placeholder="Minimal 6 karakter" required>
                          </div>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                          <button type="submit" class="btn btn-info fw-semibold text-white">Reset Password</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>

                <!-- Modal Hapus User -->
                @if($user->id !== Auth::id())
                  <div class="modal fade text-start" id="deleteUserModal{{ $user->id }}" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                      <div class="modal-content border-0 shadow">
                        <div class="modal-header border-0 pb-0">
                          <h5 class="modal-title fw-bold">Hapus Akun Pengguna</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body py-4 text-center">
                          <div class="text-danger mb-3" style="font-size: 3rem;">
                            <i class="bi bi-person-x"></i>
                          </div>
                          <p class="mb-1">Hapus akun <strong>{{ $user->username }}</strong>?</p>
                          <small class="text-muted">Akun dengan riwayat transaksi tidak dapat dihapus.</small>
                        </div>
                        <div class="modal-footer border-0 pt-0 justify-content-center">
                          <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                          <form method="POST" action="{{ route('users.destroy', $user) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger px-4 fw-semibold">Hapus Akun</button>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center py-4 text-muted">Belum ada akun pengguna.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-4">
      {{ $users->links() }}
    </div>
  </div>
</div>

<!-- Modal Tambah User Baru -->
<div class="modal fade text-start" id="addUserModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <form method="POST" action="{{ route('users.store') }}">
        @csrf
        <div class="modal-header border-0 pb-0">
          <h5 class="modal-title fw-bold text-primary"><i class="bi bi-person-plus me-2"></i>Tambah Operator Baru</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body py-3">
          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
              <input type="text" name="username" class="form-control" placeholder="Contoh: staff3" required>
            </div>
            <div class="col-6">
              <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
              <input type="password" name="password" class="form-control" placeholder="Min. 6 karakter" required>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
            <input type="text" name="full_name" class="form-control" placeholder="Nama lengkap petugas" required>
          </div>
          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
              <select name="role" class="form-select" required>
                <option value="staff">Staff Operasional</option>
                <option value="admin">Administrator</option>
              </select>
            </div>
            <div class="col-6">
              <label class="form-label fw-semibold">Email</label>
              <input type="email" name="email" class="form-control" placeholder="email@sigudang.id">
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">No. Telepon / HP</label>
            <input type="text" name="phone" class="form-control" placeholder="08xxxxxxxxxx">
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Alamat / Posko Penugasan</label>
            <textarea name="address" class="form-control" rows="2" placeholder="Gudang Posko Lapangan..."></textarea>
          </div>
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active_add" checked>
            <label class="form-check-label fw-semibold small" for="is_active_add">Status Akun Aktif</label>
          </div>
        </div>
        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary fw-semibold">Buat Akun</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
