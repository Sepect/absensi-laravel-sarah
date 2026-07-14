@extends('layouts.app')

@section('title', 'Peserta Magang')
@section('page-title', 'Peserta Magang')
@section('page-subtitle', 'Kelola data peserta magang')

@section('content')
<!-- Header Actions -->
<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <p style="color: var(--slate-500); font-size: 0.875rem;">
            {{ $interns->total() }} peserta terdaftar
        </p>
    </div>
    <a href="{{ route('admin.interns.create') }}"
       class="btn btn-primary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.25rem;">
        <span class="material-symbols-outlined" style="font-size: 18px;">add</span>
        Tambah Peserta
    </a>
</div>

<!-- Filters -->
<div class="card" style="padding: 1rem; margin-bottom: 1.5rem;">
    <form method="GET" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
        <div style="flex: 1; min-width: 200px;">
            <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: var(--slate-600); margin-bottom: 0.375rem;">Cari</label>
            <div style="position: relative;">
                <span class="material-symbols-outlined" style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: var(--slate-400); font-size: 20px;">search</span>
                <input type="text" name="search" value="{{ $search }}" placeholder="Nama atau NIM..." style="width: 100%; padding: 0.625rem 0.75rem 0.625rem 2.5rem; border: 1.5px solid var(--slate-200); border-radius: var(--radius-md); font-size: 0.875rem;">
            </div>
        </div>
        <div style="min-width: 140px;">
            <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: var(--slate-600); margin-bottom: 0.375rem;">Status</label>
            <select name="status" style="width: 100%; padding: 0.625rem 2.5rem 0.625rem 0.75rem; border: 1.5px solid var(--slate-200); border-radius: var(--radius-md); font-size: 0.875rem; background: white; cursor: pointer;">
                <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Semua</option>
                <option value="aktif" {{ $status === 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="selesai" {{ $status === 'selesai' ? 'selected' : '' }}>Selesai</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary" style="padding: 0.625rem 1.25rem; text-decoration: none;">
            <span class="material-symbols-outlined" style="font-size: 18px;">filter_list</span>
            Filter
        </button>
    </form>
</div>

<!-- Table -->
<div class="card">
    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Peserta</th>
                    <th>NIM/NIS</th>
                    <th>Asal</th>
                    <th>Pembimbing</th>
                    <th>Status</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($interns as $intern)
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <div class="avatar avatar-primary" style="width: 36px; height: 36px; font-size: 0.75rem;">
                                    {{ $intern->initials }}
                                </div>
                                <div>
                                    <p style="font-weight: 600; color: var(--slate-800);">
                                        {{ $intern->nama_lengkap }}
                                    </p>
                                    <p style="font-size: 0.75rem; color: var(--slate-400);">
                                        {{ $intern->user->email }}
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <code style="background: var(--slate-100); padding: 0.25rem 0.5rem; border-radius: var(--radius-sm); font-size: 0.8125rem;">
                                {{ $intern->nim_nis }}
                            </code>
                        </td>
                        <td style="font-size: 0.875rem;">{{ $intern->asal_sekolah_kampus }}</td>
                        <td style="font-size: 0.875rem;">{{ $intern->nama_pembimbing }}</td>
                        <td>
                            @if($intern->status === 'aktif')
                                <span class="badge badge-success">Aktif</span>
                            @elseif($intern->status === 'nonaktif')
                                <span class="badge badge-danger">Nonaktif</span>
                            @else
                                <span class="badge badge-info">Selesai</span>
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                                <a href="{{ route('admin.interns.edit', $intern->id) }}"
                                   style="padding: 0.5rem; border-radius: var(--radius-md); color: var(--slate-500); transition: all 0.2s;">
                                    <span class="material-symbols-outlined" style="font-size: 20px;">edit</span>
                                </a>
                                <form method="POST" action="{{ route('admin.interns.destroy', $intern->id) }}" style="display: contents;" onsubmit="return confirm('Hapus {{ $intern->nama_lengkap }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="padding: 0.5rem; border-radius: var(--radius-md); background: none; border: none; cursor: pointer; color: var(--danger); transition: all 0.2s;">
                                        <span class="material-symbols-outlined" style="font-size: 20px;">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 3rem; color: var(--slate-400);">
                            <span class="material-symbols-outlined" style="font-size: 48px; display: block; margin-bottom: 0.5rem;">person_search</span>
                            <p>Tidak ada data peserta ditemukan</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($interns->hasPages())
        <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--slate-100); display: flex; justify-content: space-between; align-items: center;">
            <p style="font-size: 0.875rem; color: var(--slate-500);">
                Menampilkan {{ $interns->firstItem() }} - {{ $interns->lastItem() }} dari {{ $interns->total() }}
            </p>
            <div style="display: flex; gap: 0.5rem;">
                @if($interns->onFirstPage())
                    <span style="padding: 0.5rem 1rem; color: var(--slate-400); cursor: not-allowed;">
                        <span class="material-symbols-outlined" style="font-size: 20px; vertical-align: middle;">chevron_left</span>
                    </span>
                @else
                    <a href="{{ $interns->previousPageUrl() }}" style="padding: 0.5rem 1rem; border-radius: var(--radius-md); background: var(--slate-100); color: var(--slate-600);">
                        <span class="material-symbols-outlined" style="font-size: 20px; vertical-align: middle;">chevron_left</span>
                    </a>
                @endif

                @foreach($interns->getUrlRange(max(1, $interns->currentPage() - 2), min($interns->lastPage(), $interns->currentPage() + 2)) as $page)
                    @if($page == $interns->currentPage())
                        <span style="padding: 0.5rem 1rem; border-radius: var(--radius-md); background: var(--primary); color: white; font-weight: 600;">{{ $page }}</span>
                    @else
                        <a href="{{ $interns->url($page) }}" style="padding: 0.5rem 1rem; border-radius: var(--radius-md); background: var(--slate-100); color: var(--slate-600);">{{ $page }}</a>
                    @endif
                @endforeach

                @if($interns->hasMorePages())
                    <a href="{{ $interns->nextPageUrl() }}" style="padding: 0.5rem 1rem; border-radius: var(--radius-md); background: var(--slate-100); color: var(--slate-600);">
                        <span class="material-symbols-outlined" style="font-size: 20px; vertical-align: middle;">chevron_right</span>
                    </a>
                @else
                    <span style="padding: 0.5rem 1rem; color: var(--slate-400); cursor: not-allowed;">
                        <span class="material-symbols-outlined" style="font-size: 20px; vertical-align: middle;">chevron_right</span>
                    </span>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection
