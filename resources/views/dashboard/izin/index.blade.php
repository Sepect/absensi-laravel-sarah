@extends('layouts.app')

@section('title', 'Permintaan Izin')
@section('page-title', 'Permintaan Izin')
@section('page-subtitle', 'Kelola permintaan izin peserta magang')

@section('content')
<!-- Filters -->
<div class="card" style="padding: 1rem; margin-bottom: 1.5rem;">
    <form method="GET" style="display: flex; gap: 1rem; align-items: flex-end;">
        <select name="status" class="form-input form-select" style="padding: 0.625rem 2rem 0.625rem 0.75rem;" onchange="this.form.submit()">
            <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Menunggu</option>
            <option value="disetujui" {{ $status === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
            <option value="ditolak" {{ $status === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
            <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Semua</option>
        </select>
    </form>
</div>

<!-- Table -->
<div class="card">
    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Peserta</th>
                    <th>Jenis</th>
                    <th>Periode</th>
                    <th>Alasan</th>
                    <th>Status</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $request)
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <div class="avatar avatar-primary" style="width: 32px; height: 32px; font-size: 0.75rem;">
                                    {{ Str::substr($request->internProfile?->nama_lengkap, 0, 2) ?? 'XX' }}
                                </div>
                                <div>
                                    <p style="font-weight: 600;">{{ $request->internProfile?->nama_lengkap ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge {{ $request->jenis === 'sakit' ? 'badge-danger' : ($request->jenis === 'cuti' ? 'badge-info' : 'badge-warning') }}">
                                {{ ucfirst($request->jenis) }}
                            </span>
                        </td>
                        <td>
                            <code style="background: var(--slate-100); padding: 0.25rem 0.5rem; border-radius: var(--radius-sm); font-size: 0.8125rem;">
                                {{ $request->tanggal_mulai->format('d/m') }} - {{ $request->tanggal_selesai->format('d/m/Y') }}
                            </code>
                            <p style="font-size: 0.75rem; color: var(--slate-400); margin-top: 0.25rem;">
                                {{ $request->duration_days }} hari
                            </p>
                        </td>
                        <td style="max-width: 250px;">
                            <p style="font-size: 0.875rem; color: var(--slate-600); line-clamp: 2; line-clamp: 2; -webkit-line-clamp: 2; overflow: hidden; display: -webkit-box; -webkit-box-orient: vertical;">
                                {{ Str::limit($request->alasan, 80) }}
                            </p>
                        </td>
                        <td>
                            <span class="badge {{ $request->status === 'disetujui' ? 'badge-success' : ($request->status === 'ditolak' ? 'badge-danger' : 'badge-warning') }}">
                                @if($request->status === 'disetujui')
                                    Disetujui
                                @elseif($request->status === 'ditolak')
                                    Ditolak
                                @else
                                    Menunggu
                                @endif
                            </span>
                        </td>
                        <td style="text-align: right;">
                            @if($request->status === 'pending')
                                <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                                    <form action="{{ route('admin.izin.review', $request->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <input type="hidden" name="action" value="approve">
                                        <button type="submit" style="background: var(--success-bg); border: none; padding: 0.5rem; border-radius: var(--radius-md); cursor: pointer; color: var(--success);" title="Setujui">
                                            <span class="material-symbols-outlined" style="font-size: 20px;">check</span>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.izin.review', $request->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <input type="hidden" name="action" value="reject">
                                        <button type="submit" style="background: var(--danger-bg); border: none; padding: 0.5rem; border-radius: var(--radius-md); cursor: pointer; color: var(--danger);" title="Tolak">
                                            <span class="material-symbols-outlined" style="font-size: 20px;">close</span>
                                        </button>
                                    </form>
                                </div>
                            @else
                                <span style="font-size: 0.75rem; color: var(--slate-400);">
                                {{ $request->reviewer?->nama ?? '-' }}
                            </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 3rem; color: var(--slate-400);">
                            <span class="material-symbols-outlined" style="font-size: 48px; display: block; margin-bottom: 0.5rem;">event_available</span>
                            Tidak ada permintaan izin
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($requests->hasPages())
        <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--slate-100);">
            {{ $requests->links() }}
        </div>
    @endif
</div>
@endsection
