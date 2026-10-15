@extends('layouts.admin')

@section('title', 'Profil Pengguna')
@section('page_title', 'Profil Pengguna & Keamanan')
@section('page_subtitle', 'Kelola informasi identitas pribadi, kontak, dan kata sandi akun Anda')

@section('content')
<div class="row g-4">
  <!-- Kolom Kiri: Profil & Relasi UserProfile -->
  <div class="col-lg-6">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white py-3 border-0">
        <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-person-circle me-2"></i>Informasi Akun</h5>
      </div>
      <div class="card-body p-4 pt-0">
        <div class="d-flex align-items-center gap-3 p-3 mb-4 bg-light rounded-3 border">
          <div class="avatar bg-primary text-white fs-3 fw-bold rounded-circle d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
            {{ strtoupper(substr($user->username, 0, 2)) }}
          </div>
          <div>
            <h5 class="fw-bold mb-0 text-dark">{{ $user->full_name ?: $user->username }}</h5>
            <small class="text-muted font-monospace">{{ $user->username }}</small>
            <div class="mt-1">
              <span class="badge {{ $user->role === 'admin' ? 'bg-primary' : 'bg-success' }}">
                {{ ucfirst($user->role) }}
              </span>
            </div>
          </div>
        </div>

        <form method="POST" action="{{ route('profile.update') }}">
          @csrf
          @method('PUT')

          <div class="mb-3">
            <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
            <input type="text" name="full_name" class="form-control @error('full_name') is-invalid @enderror" 
                   value="{{ old('full_name', $user->full_name) }}" required>
            @error('full_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Alamat Email</label>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                   value="{{ old('email', $user->email) }}">
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Nomor Telepon / WhatsApp</label>
            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                   value="{{ old('phone', $user->profile->phone ?? '') }}" placeholder="08xxxxxxxxxx">
            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="mb-4">
            <label class="form-label fw-semibold">Alamat Domisili / Posko Penugasan</label>
            <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="3" 
                      placeholder="Alamat kantor / gudang penugasan...">{{ old('address', $user->profile->address ?? '') }}</textarea>
            @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <button type="submit" class="btn btn-primary fw-semibold px-4">
            <i class="bi bi-save me-1"></i> Simpan Pembaruan Profil
          </button>
        </form>
      </div>
    </div>
  </div>

  <!-- Kolom Kanan: Form Ubah Password -->
  <div class="col-lg-6">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white py-3 border-0">
        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-shield-lock me-2 text-primary"></i>Ganti Kata Sandi</h5>
      </div>
      <div class="card-body p-4 pt-0">
        <form method="POST" action="{{ route('profile.password') }}">
          @csrf
          @method('PUT')

          <div class="mb-3">
            <label class="form-label fw-semibold">Kata Sandi Saat Ini <span class="text-danger">*</span></label>
            <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
            @error('current_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Kata Sandi Baru <span class="text-danger">*</span></label>
            <input type="password" name="new_password" class="form-control @error('new_password') is-invalid @enderror" 
                   placeholder="Minimal 6 karakter" required>
            @error('new_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="mb-4">
            <label class="form-label fw-semibold">Konfirmasi Kata Sandi Baru <span class="text-danger">*</span></label>
            <input type="password" name="new_password_confirmation" class="form-control" 
                   placeholder="Ketik ulang kata sandi baru" required>
          </div>

          <button type="submit" class="btn btn-warning fw-semibold px-4 text-dark">
            <i class="bi bi-key me-1"></i> Perbarui Kata Sandi
          </button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
