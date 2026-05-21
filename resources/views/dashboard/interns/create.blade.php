@extends('layouts.app')

@section('title', 'Tambah Peserta')
@section('page-title', 'Tambah Peserta Magang')
@section('page-subtitle', 'Daftarkan peserta magang baru')

@section('content')
<div style="max-width: 640px;">
    <form action="{{ route('admin.interns.store') }}" method="POST" style="display: flex; flex-direction: column; gap: 1.5rem;">@csrf

        <!-- Personal Info -->
        <div class="card" style="padding: 1.5rem;">
            <h3 style="margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem; padding-bottom: 1rem; border-bottom: 1px solid var(--slate-100);">
                <span class="material-symbols-outlined" style="color: var(--primary); font-size: 24px;">person</span>
                Informasi Personal
            </h3>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--slate-700); margin-bottom: 0.375rem;">Email *</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-input" style="padding: 0.75rem;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--slate-700); margin-bottom: 0.375rem;">NIM/NIS *</label>
                    <input type="text" name="nim_nis" value="{{ old('nim_nis') }}" class="form-input" style="padding: 0.75rem;">
                </div>
                <div style="grid-column: span 2;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--slate-700); margin-bottom: 0.375rem;">Nama Lengkap *</label>
                    <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}" class="form-input" style="padding: 0.75rem;">
                </div>
                <div style="grid-column: span 2;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--slate-700); margin-bottom: 0.375rem;">Asal Sekolah/Kampus *</label>
                    <input type="text" name="asal_sekolah_kampus" value="{{ old('asal_sekolah_kampus') }}" class="form-input" style="padding: 0.75rem;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--slate-700); margin-bottom: 0.375rem;">No. Telepon</label>
                    <input type="tel" name="no_telp" value="{{ old('no_telp') }}" class="form-input" style="padding: 0.75rem;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--slate-700); margin-bottom: 0.375rem;">Kontak Darurat</label>
                    <input type="tel" name="kontak_darurat" value="{{ old('kontak_darurat') }}" class="form-input" style="padding: 0.75rem;">
                </div>
                <div style="grid-column: span 2;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--slate-700); margin-bottom: 0.375rem;">Alamat</label>
                    <textarea name="alamat" rows="2" class="form-input" style="padding: 0.75rem;">{{ old('alamat') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Internship Info -->
        <div class="card" style="padding: 1.5rem;">
            <h3 style="margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem; padding-bottom: 1rem; border-bottom: 1px solid var(--slate-100);">
                <span class="material-symbols-outlined" style="color: var(--primary); font-size: 24px;">school</span>
                Informasi Magang
            </h3>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--slate-700); margin-bottom: 0.375rem;">Nama Pembimbing *</label>
                    <input type="text" name="nama_pembimbing" value="{{ old('nama_pembimbing') }}" class="form-input" style="padding: 0.75rem;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--slate-700); margin-bottom: 0.375rem;">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}" class="form-input" style="padding: 0.75rem;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--slate-700); margin-bottom: 0.375rem;">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}" class="form-input" style="padding: 0.75rem;">
                </div>
            </div>
        </div>

        <!-- Password -->
        <div class="card" style="padding: 1.5rem;">
            <h3 style="margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem; padding-bottom: 1rem; border-bottom: 1px solid var(--slate-100);">
                <span class="material-symbols-outlined" style="color: var(--primary); font-size: 24px;">lock</span>
                Akun Login
            </h3>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--slate-700); margin-bottom: 0.375rem;">Password *</label>
                    <input type="password" name="password" class="form-input" style="padding: 0.75rem;" placeholder="Min. 6 karakter">
                </div>
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--slate-700); margin-bottom: 0.375rem;">Konfirmasi Password *</label>
                    <input type="password" name="password_confirmation" class="form-input" style="padding: 0.75rem;" placeholder="Ulangi password">
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
            <a href="{{ route('admin.interns.index') }}" class="btn btn-ghost" style="text-decoration: none; padding: 0.75rem 1.5rem;">Batal</a>
            <button type="submit" class="btn btn-primary" style="min-width: 160px;">
                <span class="material-symbols-outlined" style="font-size: 18px;">save</span>
                Simpan Peserta
            </button>
        </div>
    </form>
</div>
@endsection
