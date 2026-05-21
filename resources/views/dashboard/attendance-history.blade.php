@extends('layouts.app')

@section('title', 'Riwayat Absensi')
@section('page-title', 'Riwayat Absensi')
@section('page-subtitle', 'Riwayat kehadiran peserta magang')

@section('content')
<!-- Date Filter -->
<div class="card" style="padding: 1rem; margin-bottom: 1.5rem;">
    <form method="GET" style="display: flex; align-items: flex-end; gap: 1rem; flex-wrap: wrap;">
        <div>
            <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: var(--slate-600); margin-bottom: 0.375rem;">Tanggal</label>
            <input type="date" name="date" value="{{ $date }}" class="form-input" style="padding: 0.625rem 0.75rem;" onchange="this.form.submit()">
        </div>
        <button type="submit" class="btn btn-primary" style="padding: 0.625rem 1.25rem;">
            <span class="material-symbols-outlined" style="font-size: 18px;">filter_list</span>
            Tampilkan
        </button>
    </form>
</div>

<!-- Attendance Table -->
<div class="card">
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
                @forelse($attendances as $attendance)
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
                        <td style="font-family: monospace;">
                            @if($attendance->scan_in_time)
                                {{ $attendance->scan_in_time->format('H:i:s') }}
                            @else
                                <span style="color: var(--slate-400);">--:--</span>
                            @endif
                        </td>
                        <td style="font-family: monospace;">
                            @if($attendance->scan_out_time)
                                {{ $attendance->scan_out_time->format('H:i:s') }}
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
                            <span class="material-symbols-outlined" style="font-size: 48px; display: block; margin-bottom: 0.5rem;">event_busy</span>
                            Tidak ada data absensi untuk tanggal ini
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($attendances->hasPages())
        <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--slate-100);">
            {{ $attendances->links() }}
        </div>
    @endif
</div>
@endsection