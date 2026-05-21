@extends('layouts.app')

@section('title', 'Pengaturan Kantor')
@section('page-title', 'Pengaturan Lokasi & Jadwal')
@section('page-subtitle', 'Atur koordinat dan jadwal operasional kantor')

@section('content')
<div style="max-width: 600px;">
    <form action="{{ route('admin.settings.update') }}" method="POST" style="display: flex; flex-direction: column; gap: 1.5rem;">
        @csrf
        @method('PUT')

        <!-- Location Settings -->
        <div class="card" style="padding: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem; padding-bottom: 1rem; border-bottom: 1px solid var(--slate-100);">
                <span class="material-symbols-outlined" style="font-size: 24px; color: var(--primary);">location_on</span>
                <h3 style="font-size: 1rem; font-weight: 700;">Lokasi Kantor</h3>
            </div>

            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--slate-700); margin-bottom: 0.375rem;">Nama Instansi</label>
                    <input type="text" name="nama_instansi" value="{{ old('nama_instansi', $settings->nama_instansi) }}"
                           class="form-input" style="padding: 0.75rem;"
                           placeholder="PT. Anchor Precision Indonesia">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--slate-700); margin-bottom: 0.375rem;">Latitude</label>
                        <input type="text" name="latitude" value="{{ old('latitude', $settings->latitude) }}"
                               class="form-input" style="padding: 0.75rem; font-family: monospace;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--slate-700); margin-bottom: 0.375rem;">Longitude</label>
                        <input type="text" name="longitude" value="{{ old('longitude', $settings->longitude) }}"
                               class="form-input" style="padding: 0.75rem; font-family: monospace;">
                    </div>
                </div>

                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--slate-700); margin-bottom: 0.375rem;">
                        Radius Maksimum
                        <span style="font-weight: 400; color: var(--slate-400);">(meter)</span>
                    </label>
                    <input type="number" name="radius_meters" value="{{ old('radius_meters', $settings->radius_meters) }}"
                           class="form-input" style="padding: 0.75rem; max-width: 150px;">
                    <p style="font-size: 0.75rem; color: var(--slate-400); margin-top: 0.25rem;">
                        Jarak maksimal peserta dari titik koordinat kantor
                    </p>
                </div>
            </div>
        </div>

        <!-- Schedule Settings -->
        <div class="card" style="padding: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem; padding-bottom: 1rem; border-bottom: 1px solid var(--slate-100);">
                <span class="material-symbols-outlined" style="font-size: 24px; color: var(--primary);">schedule</span>
                <h3 style="font-size: 1rem; font-weight: 700;">Jadwal Operasional</h3>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--slate-700); margin-bottom: 0.375rem;">Waktu Masuk</label>
                    <input type="time" name="waktu_masuk"
                           value="{{ old('waktu_masuk', $settings->waktu_masuk) }}"
                           class="form-input" style="padding: 0.75rem;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--slate-700); margin-bottom: 0.375rem;">Waktu Pulang</label>
                    <input type="time" name="waktu_pulang"
                           value="{{ old('waktu_pulang', $settings->waktu_pulang) }}"
                           class="form-input" style="padding: 0.75rem;">
                </div>
            </div>

            <div style="margin-top: 1rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--slate-700); margin-bottom: 0.375rem;">
                    Durasi QR Code <span style="font-weight: 400; color: var(--slate-400);">(menit)</span>
                </label>
                <input type="number" name="qr_expiry_minutes"
                       value="{{ old('qr_expiry_minutes', $settings->qr_expiry_minutes) }}"
                       class="form-input" style="padding: 0.75rem; max-width: 100px;">
                <p style="font-size: 0.75rem; color: var(--slate-400); margin-top: 0.25rem;">
                    QR Code otomatis berganti setiap {{ $settings->qr_expiry_minutes }} menit
                </p>
            </div>
        </div>

        <!-- Actions -->
        <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-ghost" style="text-decoration: none;">
                Batal
            </a>
            <button type="submit" class="btn btn-primary" style="min-width: 150px;">
                <span class="material-symbols-outlined" style="font-size: 18px;">save</span>
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
