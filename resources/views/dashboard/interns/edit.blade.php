@extends('layouts.app')

@section('title', 'Edit Peserta')
@section('page-title', 'Edit Peserta')
@section('page-subtitle', $intern->nama_lengkap)

@section('content')
<div style="max-width: 640px;">
    <form action="{{ route('admin.interns.update', $intern->id) }}" method="POST" style="display: flex; flex-direction: column; gap: 1.5rem;">@csrf
        @method('PUT')

        <!-- Personal Info -->
        <div class="card" style="padding: 1.5rem;">
            <h3 style="margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem; padding-bottom: 1rem; border-bottom: 1px solid var(--slate-100);">
                <span class="material-symbols-outlined" style="color: var(--primary); font-size: 24px;">person</span>
                Informasi Personal
            </h3>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--slate-700); margin-bottom: 0.375rem;">Email</label>
                    <input type="email" name="email" value="{{ $intern->user->email }}" class="form-input" style="padding: 0.75rem; opacity: 0.7;" readonly>
                </div>
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--slate-700); margin-bottom: 0.375rem;">NIM/NIS</label>
                    <input type="text" name="nis" value="{{ $intern->nim_nis }}" class="form-input" style="padding: 0.75rem; opacity: 0.7;" readonly>
                </div>
                <div style="grid-column: span 2;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--slate-700); margin-bottom: 0.375rem;">Nama Lengkap *</label>
                    <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $intern->nama_lengkap) }}" class="form-input" style="padding: 0.75rem;">
                </div>
                <div style="grid-column: span 2;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--slate-700); margin-bottom: 0.375rem;">Asal Sekolah/Kampus *</label>
                    <input type="text" name="asal_sekolah_kampus" value="{{ old('asal_sekolah_kampus', $intern->asal_sekolah_kampus) }}" class="form-input" style="padding: 0.75rem;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--slate-700); margin-bottom: 0.375rem;">No. Telepon</label>
                    <input type="tel" name="no_telp" value="{{ old('no_telp', $intern->no_telp) }}" class="form-input" style="padding: 0.75rem;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--slate-700); margin-bottom: 0.375rem;">Kontak Darurat</label>
                    <input type="tel" name="kontak_darurat" value="{{ old('kontak_darurat', $intern->kontak_darurat) }}" class="form-input" style="padding: 0.75rem;">
                </div>
                <div style="grid-column: span 2;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--slate-700); margin-bottom: 0.375rem;">Alamat</label>
                    <textarea name="alamat" rows="2" class="form-input" style="padding: 0.75rem;">{{ old('alamat', $intern->alamat) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Internship Info -->
        <div class="card" style="padding: 1.5rem;">
            <h3 style="margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem; padding-bottom: 1rem; border-bottom: 1px solid var(--slate-100);">
                <span class="material-symbols-outlined" style="color: var(--primary); font-size: 24px;">school</span>
                Informasi Magang & Status
            </h3>

            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--slate-700); margin-bottom: 0.375rem;">Pembimbing</label>
                    <input type="text" name="nama_pembimbing" value="{{ old('nama_pembimbing', $intern->nama_pembimbing) }}" class="form-input" style="padding: 0.75rem;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--slate-700); margin-bottom: 0.375rem;">Status</label>
                    <select name="status" class="form-input form-select" style="padding: 0.75rem;">
                        <option value="aktif" {{ $intern->status === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="selesai" {{ $intern->status === 'selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Password Reset -->
        <div class="card" style="padding: 1.5rem;">
            <h3 style="margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <span class="material-symbols-outlined" style="color: var(--primary); font-size: 24px;">lock_reset</span>
                Reset Password
            </h3>
            <p style="font-size: 0.875rem; color: var(--slate-500); margin-bottom: 1rem;">
                Kosongkan jika tidak ingin mengubah password.
            </p>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--slate-700); margin-bottom: 0.375rem;">Password Baru</label>
                    <input type="password" name="password" class="form-input" style="padding: 0.75rem;" placeholder="Min. 6 karakter">
                </div>
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--slate-700); margin-bottom: 0.375rem;">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="form-input" style="padding: 0.75rem;" placeholder="Ulangi password">
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
            <a href="{{ route('admin.interns.index') }}" class="btn btn-ghost" style="text-decoration: none; padding: 0.75rem 1.5rem;">Batal</a>
            <button type="submit" class="btn btn-primary" style="min-width: 160px;">
                <span class="material-symbols-outlined" style="font-size: 18px;">save</span>
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
