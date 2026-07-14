@extends('layouts.app')

@section('title', 'Laporan Harian')
@section('page-title', 'Laporan Harian')
@section('page-subtitle', 'Tinjau laporan harian peserta magang')

@section('content')
<!-- Filters -->
<div class="card" style="padding: 1rem; margin-bottom: 1.5rem;">
    <form method="GET" style="display: flex; align-items: flex-end; gap: 1rem; flex-wrap: wrap;">
        <div>
            <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: var(--slate-600); margin-bottom: 0.375rem;">Bulan</label>
            <select name="month" class="form-input form-select" style="padding: 0.625rem 2rem 0.625rem 0.75rem;" onchange="this.form.submit()">
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                    </option>
                @endfor
            </select>
        </div>
        <div>
            <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: var(--slate-600); margin-bottom: 0.375rem;">Tahun</label>
            <select name="year" class="form-input form-select" style="padding: 0.625rem 2rem 0.625rem 0.75rem;" onchange="this.form.submit()">
                @for($y = now()->year; $y >= now()->year - 2; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
        <button type="submit" class="btn btn-primary" style="padding: 0.625rem 1.25rem;">
            <span class="material-symbols-outlined" style="font-size: 18px;">filter_list</span>
            Tampilkan
        </button>
    </form>
</div>

<!-- Table -->
<div class="card">
    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Peserta</th>
                    <th>Laporan</th>
                    <th>Status</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $report)
                    <tr>
                        <td>
                            <code style="background: var(--slate-100); padding: 0.25rem 0.5rem; border-radius: var(--radius-sm); font-size: 0.8125rem;">
                                {{ $report->report_date->format('d/m/Y') }}
                            </code>
                        </td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <div class="avatar avatar-primary" style="width: 32px; height: 32px; font-size: 0.75rem;">
                                    {{ Str::substr($report->internProfile?->nama_lengkap, 0, 2) ?? 'XX' }}
                                </div>
                                <div>
                                    <p style="font-weight: 600; font-size: 0.875rem;">{{ $report->internProfile?->nama_lengkap ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </td>
                        <td style="max-width: 300px;">
                            @php($isLongReport = mb_strlen($report->description) > 100)
                            <p id="report-short-{{ $report->id }}" style="font-size: 0.8125rem; color: var(--slate-600);">
                                {{ Str::limit($report->description, 100) }}
                            </p>
                            <p id="report-full-{{ $report->id }}" style="display: none; font-size: 0.8125rem; color: var(--slate-600); white-space: pre-line;">
                                {{ $report->description }}
                            </p>
                            <div style="display: flex; align-items: center; gap: 0.75rem; margin-top: 0.25rem;">
                                <p style="font-size: 0.75rem; color: var(--slate-400);">
                                    {{ str_word_count($report->description) }} kata
                                </p>
                                @if($isLongReport)
                                    <button type="button" onclick="toggleReport({{ $report->id }})" class="btn btn-ghost" style="padding: 0.125rem 0.5rem; font-size: 0.75rem; color: var(--primary); display: inline-flex; align-items: center; gap: 0.25rem;">
                                        <span id="report-toggle-text-{{ $report->id }}">Selengkapnya</span>
                                        <span class="material-symbols-outlined" id="report-toggle-icon-{{ $report->id }}" style="font-size: 16px;">expand_more</span>
                                    </button>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="badge {{ $report->getStatusBadgeClass() }}">{{ $report->getStatusLabel() }}</span>
                        </td>
                        <td style="text-align: right;">
                            @if($report->isApproved())
                                <span style="font-size: 0.75rem; color: var(--slate-400);">
                                    <span class="material-symbols-outlined" style="font-size: 14px; vertical-align: middle;">verified</span>
                                    {{ $report->approver?->nama ?? '' }}
                                </span>
                            @else
                                <div style="display: inline-flex; align-items: center; gap: 0.25rem; justify-content: flex-end;">
                                    <form action="{{ route('admin.reports.approve', $report->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-ghost" style="padding: 0.5rem 0.75rem; font-size: 0.8125rem; color: var(--success, #16a34a);">
                                            <span class="material-symbols-outlined" style="font-size: 16px; vertical-align: middle;">check</span>
                                            Setujui
                                        </button>
                                    </form>
                                    @unless($report->isRejected())
                                        <form action="{{ route('admin.reports.reject', $report->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Tolak laporan ini?')">
                                            @csrf
                                            <button type="submit" class="btn btn-ghost" style="padding: 0.5rem 0.75rem; font-size: 0.8125rem; color: var(--danger);">
                                                <span class="material-symbols-outlined" style="font-size: 16px; vertical-align: middle;">close</span>
                                                Tolak
                                            </button>
                                        </form>
                                    @endunless
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 3rem; color: var(--slate-400);">
                            <span class="material-symbols-outlined" style="font-size: 48px; display: block; margin-bottom: 0.5rem;">description</span>
                            Tidak ada laporan untuk periode ini
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($reports->hasPages())
        <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--slate-100);">
            {{ $reports->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    function toggleReport(id) {
        const shortEl = document.getElementById('report-short-' + id);
        const fullEl = document.getElementById('report-full-' + id);
        const textEl = document.getElementById('report-toggle-text-' + id);
        const iconEl = document.getElementById('report-toggle-icon-' + id);
        const isCollapsed = fullEl.style.display === 'none';

        fullEl.style.display = isCollapsed ? 'block' : 'none';
        shortEl.style.display = isCollapsed ? 'none' : 'block';
        textEl.textContent = isCollapsed ? 'Sembunyikan' : 'Selengkapnya';
        iconEl.textContent = isCollapsed ? 'expand_less' : 'expand_more';
    }
</script>
@endpush
