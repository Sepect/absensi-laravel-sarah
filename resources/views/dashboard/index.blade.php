@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', now()->translatedFormat('d M Y'))

@section('content')
<!-- Stats Grid -->
<div class="stats-grid">
    <!-- Total Interns -->
    <div class="stat-card">
        <div>
            <p class="stat-label">Total Peserta</p>
            <p class="stat-value">{{ $summary['total_interns'] }}</p>
            <p style="font-size: 0.8125rem; color: var(--success); margin-top: 0.5rem; display: flex; align-items: center; gap: 0.25rem;">
                <span class="material-symbols-outlined" style="font-size: 14px;">trending_up</span>
                Aktif semua
            </p>
        </div>
        <div class="stat-card-icon stat-card-icon blue">
            <span class="material-symbols-outlined" style="font-size: 24px;">group</span>
        </div>
    </div>

    <!-- Hadir -->
    <div class="stat-card">
        <div>
            <p class="stat-label">Hadir Hari Ini</p>
            <p class="stat-value">{{ $summary['hadir'] }}</p>
            <p style="font-size: 0.8125rem; color: var(--slate-500); margin-top: 0.25rem;">
                @if($summary['total_interns'] > 0)
                    {{ round($summary['hadir'] / $summary['total_interns'] * 100) }}% dari total
                @else
                    0% dari total
                @endif
            </p>
        </div>
        <div class="stat-card-icon stat-card-icon green">
            <span class="material-symbols-outlined" style="font-size: 24px;">how_to_reg</span>
        </div>
    </div>

    <!-- Terlambat -->
    <div class="stat-card">
        <div>
            <p class="stat-label">Terlambat</p>
            <p class="stat-value">{{ $summary['terlambat'] }}</p>
            <p style="font-size: 0.8125rem; color: var(--warning); margin-top: 0.25rem; display: flex; align-items: center; gap: 0.25rem;">
                <span class="material-symbols-outlined" style="font-size: 14px;">schedule</span>
                Perlu perhatian
            </p>
        </div>
        <div class="stat-card-icon stat-card-icon yellow">
            <span class="material-symbols-outlined" style="font-size: 24px;">schedule</span>
        </div>
    </div>

    <!-- Belum Absen -->
    <div class="stat-card">
        <div>
            <p class="stat-label">Belum Absen</p>
            <p class="stat-value">{{ $summary['alpha'] }}</p>
            <p style="font-size: 0.8125rem; color: var(--danger); margin-top: 0.25rem; display: flex; align-items: center; gap: 0.25rem;">
                <span class="material-symbols-outlined" style="font-size: 14px;">warning</span>
                Perlu ditindaklanjuti
            </p>
        </div>
        <div class="stat-card-icon stat-card-icon red">
            <span class="material-symbols-outlined" style="font-size: 24px;">person_off</span>
        </div>
    </div>
</div>

<!-- Main Content Grid -->
<div class="dashboard-grid">
    <!-- Recent Activity Table -->
    <div class="card">
        <div class="card-header">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <span class="material-symbols-outlined" style="font-size: 20px; color: var(--primary);">history</span>
                <h3 class="card-title">Aktivitas Kehadiran Terkini</h3>
            </div>
            <a href="{{ route('admin.attendance.history') }}"
               style="font-size: 0.875rem; color: var(--primary); text-decoration: none; font-weight: 500; display: flex; align-items: center; gap: 0.25rem;">
                Lihat Semua
                <span class="material-symbols-outlined" style="font-size: 18px;">chevron_right</span>
            </a>
        </div>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Peserta</th>
                        <th>Masuk</th>
                        <th>Pulang</th>
                        <th>Jarak</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentAttendances as $attendance)
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div class="avatar avatar-slate">{{ $attendance->internProfile->initials ?? 'XX' }}</div>
                                    <div>
                                        <p style="font-weight: 600;">{{ $attendance->internProfile->nama_lengkap }}</p>
                                        <p style="font-size: 0.75rem; color: var(--slate-400);">{{ $attendance->internProfile->nim_nis }}</p>
                                    </div>
                                </div>
                            </td>
                            <td style="font-family: monospace;">{{ $attendance->formatted_check_in }}</td>
                            <td style="font-family: monospace;">
                                @if($attendance->formatted_check_out && $attendance->formatted_check_out !== '--:--')
                                    {{ $attendance->formatted_check_out }}
                                @else
                                    <span style="color: var(--slate-400);">--:--</span>
                                @endif
                            </td>
                            <td style="font-family: monospace;">
                                @if($attendance->distance_in_meters)
                                    {{ round($attendance->distance_in_meters) }}m
                                @else
                                    <span style="color: var(--slate-400);">-</span>
                                @endif
                            </td>
                            <td>
                                @if($attendance->status === 'hadir')
                                    <span class="badge badge-success">Hadir</span>
                                @elseif($attendance->status === 'terlambat')
                                    <span class="badge badge-warning">Terlambat</span>
                                @elseif($attendance->status === 'alpha')
                                    <span class="badge badge-danger">Alpha</span>
                                @else
                                    <span class="badge badge-info">Izin</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 3rem; color: var(--slate-400);">
                                <span class="material-symbols-outlined" style="font-size: 48px; display: block; margin-bottom: 0.5rem;">inbox</span>
                                Belum ada data absensi hari ini
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="dashboard-sidebar">
        <!-- Alerts -->
        @if($pendingReports > 0 || $pendingIzin > 0)
            <div class="card" style="padding: 1.25rem;">
                <h4 style="font-weight: 700; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <span class="material-symbols-outlined" style="color: var(--warning);">notifications</span>
                    Notifikasi
                </h4>
                @if($pendingReports > 0)
                    <a href="{{ route('admin.reports.index') }}"
                       style="display: flex; align-items: center; gap: 0.75rem; padding: 0.875rem; background: var(--warning-bg); border-radius: var(--radius-lg); text-decoration: none; margin-bottom: 0.75rem;">
                        <span class="material-symbols-outlined" style="color: var(--warning);">description</span>
                        <div>
                            <p style="font-weight: 600; color: var(--slate-800); font-size: 0.875rem;">
                                {{ $pendingReports }} laporan pending
                            </p>
                            <p style="font-size: 0.75rem; color: var(--slate-500);">Menunggu persetujuan</p>
                        </div>
                    </a>
                @endif
                @if($pendingIzin > 0)
                    <a href="{{ route('admin.izin.index') }}"
                       style="display: flex; align-items: center; gap: 0.75rem; padding: 0.875rem; background: var(--info-bg); border-radius: var(--radius-lg); text-decoration: none;">
                        <span class="material-symbols-outlined" style="color: var(--info);">event_available</span>
                        <div>
                            <p style="font-weight: 600; color: var(--slate-800); font-size: 0.875rem;">
                                {{ $pendingIzin }} izin pending
                            </p>
                            <p style="font-size: 0.75rem; color: var(--slate-500);">Perlu ditinjau</p>
                        </div>
                    </a>
                @endif
            </div>
        @endif

        <!-- Quick Actions -->
        <div class="card" style="padding: 1.25rem;">
            <h4 style="font-weight: 700; margin-bottom: 1rem;">Aksi Cepat</h4>
            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <a href="{{ route('admin.attendance.index') }}"
                   style="display: flex; align-items: center; gap: 0.75rem; padding: 0.875rem; background: var(--primary); color: white; border-radius: var(--radius-lg); text-decoration: none; font-weight: 600;">
                    <span class="material-symbols-outlined">qr_code_scanner</span>
                    Generate QR Code
                </a>
                <a href="{{ route('admin.interns.create') }}"
                   style="display: flex; align-items: center; gap: 0.75rem; padding: 0.875rem; background: var(--slate-100); color: var(--slate-700); border-radius: var(--radius-lg); text-decoration: none; font-weight: 500;">
                    <span class="material-symbols-outlined">person_add</span>
                    Tambah Peserta
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
