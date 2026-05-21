@extends('layouts.app')

@section('title', 'Absensi')
@section('page-title', 'Absensi & QR Code')
@section('page-subtitle', 'Generate QR Code untuk absensi masuk/pulang')

@section('content')
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; max-width: 900px; margin: 0 auto;">

    <!-- QR Code Card -->
    <div class="card" style="text-align: center; padding: 2rem;">
        <div style="margin-bottom: 1.5rem;">
            <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.25rem;">
                {{ $type === 'in' ? 'QR Masuk' : 'QR Pulang' }}
            </h3>
            <p style="color: var(--slate-500); font-size: 0.875rem;">
                Scan melalui aplikasi mobile peserta magang
            </p>
        </div>

        <!-- QR Type Switcher -->
        <div style="display: flex; gap: 0.5rem; margin-bottom: 1.5rem; justify-content: center;">
            <a href="?type=in"
               class="{{ $type === 'in' ? 'btn' : '' }}"
               style="{{ $type === 'in' ? 'background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%); color: white; text-decoration: none; padding: 0.5rem 1rem; border-radius: var(--radius-md); font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem;' : 'padding: 0.5rem 1rem; background: var(--slate-100); color: var(--slate-600); border-radius: var(--radius-md); text-decoration: none; font-weight: 500;' }}">
                <span class="material-symbols-outlined" style="font-size: 18px;">
                    {{ $type === 'in' ? 'login' : 'login' }}
                </span>
                Masuk
            </a>
            <a href="?type=out"
               class="{{ $type === 'out' ? 'btn' : '' }}"
               style="{{ $type === 'out' ? 'background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%); color: white; text-decoration: none; padding: 0.5rem 1rem; border-radius: var(--radius-md); font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem;' : 'padding: 0.5rem 1rem; background: var(--slate-100); color: var(--slate-600); border-radius: var(--radius-md); text-decoration: none; font-weight: 500;' }}">
                <span class="material-symbols-outlined" style="font-size: 18px;">logout</span>
                Pulang
            </a>
        </div>

        <!-- QR Code Display -->
        <div style="background: white; padding: 1.5rem; border-radius: var(--radius-xl); display: inline-block; margin-bottom: 1rem;">
            <div style="position: relative; padding: 1rem;">
                <!-- Corner decorations -->
                <div style="position: absolute; top: 0; left: 0; width: 24px; height: 24px; border-top: 3px solid var(--primary); border-left: 3px solid var(--primary); border-top-left-radius: var(--radius-lg);"></div>
                <div style="position: absolute; top: 0; right: 0; width: 24px; height: 24px; border-top: 3px solid var(--primary); border-right: 3px solid var(--primary); border-top-right-radius: var(--radius-lg);"></div>
                <div style="position: absolute; bottom: 0; left: 0; width: 24px; height: 24px; border-bottom: 3px solid var(--primary); border-left: 3px solid var(--primary); border-bottom-left-radius: var(--radius-lg);"></div>
                <div style="position: absolute; bottom: 0; right: 0; width: 24px; height: 24px; border-bottom: 3px solid var(--primary); border-right: 3px solid var(--primary); border-bottom-right-radius: var(--radius-lg);"></div>
                <img src="{{ $qrImage }}" alt="QR Code" style="width: 200px; height: 200px; display: block;">
                <div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; pointer-events: none;">
                    <div style="background: white; padding: 0.5rem; border-radius: var(--radius-md);">
                        <span class="material-symbols-outlined" style="font-size: 24px; color: var(--primary);">fingerprint</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Timer -->
        <div style="display: inline-flex; align-items: center; gap: 0.75rem; background: var(--slate-100); padding: 0.75rem 1.25rem; border-radius: var(--radius-full);">
            <span class="material-symbols-outlined" style="color: var(--primary); animation: pulse 2s infinite;">timer</span>
            <div style="text-align: left;">
                <p style="font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; color: var(--slate-400); letter-spacing: 0.05em;">Refresh dalam</p>
                <p id="countdown" style="font-size: 1.25rem; font-weight: 700; font-family: monospace; color: var(--slate-800);">--:--</p>
            </div>
        </div>
    </div>

    <!-- Stats Sidebar -->
    <div style="display: flex; flex-direction: column; gap: 1rem;">
        <div class="card" style="padding: 1.25rem;">
            <h4 style="font-weight: 700; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <span class="material-symbols-outlined" style="color: var(--primary);">insights</span>
                Statistik Hari Ini
            </h4>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                <div style="background: var(--slate-50); padding: 1rem; border-radius: var(--radius-lg); text-align: center;">
                    <p style="font-size: 1.5rem; font-weight: 800; color: var(--success);">{{ $summary['checked_in'] }}</p>
                    <p style="font-size: 0.75rem; color: var(--slate-500);">Sudah Absen Masuk</p>
                </div>
                <div style="background: var(--slate-50); padding: 1rem; border-radius: var(--radius-lg); text-align: center;">
                    <p style="font-size: 1.5rem; font-weight: 800; color: var(--primary);">{{ $summary['checked_out'] }}</p>
                    <p style="font-size: 0.75rem; color: var(--slate-500);">Sudah Absen Pulang</p>
                </div>
                <div style="background: var(--warning-bg); padding: 1rem; border-radius: var(--radius-lg); text-align: center;">
                    <p style="font-size: 1.5rem; font-weight: 800; color: var(--warning);">{{ $summary['terlambat'] }}</p>
                    <p style="font-size: 0.75rem; color: var(--slate-500);">Terlambat</p>
                </div>
                <div style="background: var(--danger-bg); padding: 1rem; border-radius: var(--radius-lg); text-align: center;">
                    <p style="font-size: 1.5rem; font-weight: 800; color: var(--danger);">{{ $summary['alpha'] }}</p>
                    <p style="font-size: 0.75rem; color: var(--slate-500);">Belum Absen</p>
                </div>
            </div>
        </div>

        <div class="card" style="padding: 1.25rem;">
            <h4 style="font-weight: 700; margin-bottom: 0.75rem;">Info Kantor</h4>
            <div style="font-size: 0.875rem; color: var(--slate-600); line-height: 1.8;">
                <p style="display: flex; align-items: center; gap: 0.5rem;">
                    <span class="material-symbols-outlined" style="font-size: 16px; color: var(--slate-400);">location_on</span>
                    PT. Anchor Precision Indonesia
                </p>
                <p style="display: flex; align-items: center; gap: 0.5rem;">
                    <span class="material-symbols-outlined" style="font-size: 16px; color: var(--slate-400);">schedule</span>
                    08:00 - 17:00 WIB
                </p>
                <p style="display: flex; align-items: center; gap: 0.5rem;">
                    <span class="material-symbols-outlined" style="font-size: 16px; color: var(--slate-400);">my_location</span>
                    Radius 100 meter
                </p>
            </div>
        </div>

        <div class="card" style="padding: 1.25rem; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%); border: none;">
            <p style="color: white; font-size: 0.875rem; margin-bottom: 0.5rem; opacity: 0.9;">
                <span class="material-symbols-outlined" style="vertical-align: middle; margin-right: 0.25rem;">info</span>
                QR Code otomatis berganti setiap 5 menit untuk keamanan
            </p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let remaining = {{ $timeRemaining }};

    function updateCountdown() {
        const minutes = Math.floor(remaining / 60);
        const seconds = remaining % 60;
        document.getElementById('countdown').textContent =
            String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');

        if (remaining > 0) {
            remaining--;
            setTimeout(updateCountdown, 1000);
        } else {
            location.reload();
        }
    }

    updateCountdown();
</script>
@endpush
