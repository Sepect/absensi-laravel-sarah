<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Absensi Magang</title>
    <link rel="stylesheet" href="{{ asset('build/assets/app.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
</head>
<body>

<!-- Mobile Sidebar Overlay -->
<div id="sidebarOverlay" class="fixed inset-0 bg-black/50 z-50 hidden lg:hidden" onclick="toggleSidebar()"></div>

<!-- Sidebar -->
<aside id="sidebar" class="sidebar">
    <!-- Logo & Brand -->
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <div class="sidebar-logo-icon">A</div>
            <div>
                <h1 style="font-size: 1rem; font-weight: 700; color: var(--slate-800);">Absensi</h1>
                <p style="font-size: 0.6875rem; color: var(--slate-400);">Admin Panel</p>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="sidebar-nav">
        <div class="sidebar-section">
            <p class="sidebar-section-title">Menu Utama</p>
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <span class="material-symbols-outlined" style="font-size: 20px;">dashboard</span>
                Dashboard
            </a>
            <a href="{{ route('admin.attendance.qrcode') }}" class="nav-link {{ request()->routeIs('admin.attendance.qrcode') ? 'active' : '' }}" target="_blank">
                <span class="material-symbols-outlined" style="font-size: 20px;">qr_code_scanner</span>
                QR Code
            </a>
            <a href="{{ route('admin.attendance.index') }}" class="nav-link {{ request()->routeIs('admin.attendance.*') ? 'active' : '' }}">
                <span class="material-symbols-outlined" style="font-size: 20px;">history</span>
                Riwayat Absensi
            </a>
            <a href="{{ route('admin.interns.index') }}" class="nav-link {{ request()->routeIs('admin.interns.*') ? 'active' : '' }}">
                <span class="material-symbols-outlined" style="font-size: 20px;">group</span>
                Peserta Magang
            </a>
            <a href="{{ route('admin.reports.index') }}" class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                <span class="material-symbols-outlined" style="font-size: 20px;">description</span>
                Laporan Harian
            </a>
            <a href="{{ route('admin.izin.index') }}" class="nav-link {{ request()->routeIs('admin.izin.*') ? 'active' : '' }}">
                <span class="material-symbols-outlined" style="font-size: 20px;">event_available</span>
                Permintaan Izin
            </a>
        </div>

        <div class="sidebar-section">
            <p class="sidebar-section-title">Pengaturan</p>
            <a href="{{ route('admin.settings') }}" class="nav-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                <span class="material-symbols-outlined" style="font-size: 20px;">settings</span>
                Pengaturan Kantor
            </a>
        </div>
    </nav>

    <!-- User & Logout -->
    <div style="padding: 1rem; border-top: 1px solid var(--slate-100);">
        <div class="user-menu" style="margin-bottom: 0.5rem;">
            <div class="avatar avatar-primary">
                {{ substr(auth()->user()->nama ?? 'A', 0, 2) }}
            </div>
            <div style="flex: 1; min-width: 0;">
                <p style="font-weight: 600; font-size: 0.875rem; color: var(--slate-800);" class="truncate">
                    {{ auth()->user()->nama ?? 'Administrator' }}
                </p>
                <p style="font-size: 0.75rem; color: var(--slate-400);" class="truncate">
                    {{ auth()->user()->email ?? '' }}
                </p>
            </div>
        </div>
        <form action="{{ route('admin.logout') }}" method="POST" style="display: contents;">
            @csrf
            <button type="submit" class="nav-link" style="width: 100%; color: var(--danger);">
                <span class="material-symbols-outlined" style="font-size: 20px;">logout</span>
                Keluar
            </button>
        </form>
    </div>
</aside>

<!-- Main Content -->
<main class="main-content">
    <!-- Top Bar -->
    <header class="topbar">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <button onclick="toggleSidebar()" class="mobile-menu-btn hidden" style="background: none; border: none; cursor: pointer; padding: 0.5rem; border-radius: var(--radius-md); color: var(--slate-600);">
                <span class="material-symbols-outlined" style="font-size: 24px;">menu</span>
            </button>
            <div>
                <h2 class="page-title">@yield('page-title', 'Dashboard')</h2>
                @hasSection('page-subtitle')
                    <p class="page-subtitle">@yield('page-subtitle')</p>
                @endif
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <!-- Notifications -->
            <button style="position: relative; background: none; border: none; cursor: pointer; padding: 0.5rem; border-radius: var(--radius-md); color: var(--slate-500);">
                <span class="material-symbols-outlined" style="font-size: 22px;">notifications</span>
                <span style="position: absolute; top: 6px; right: 6px; width: 8px; height: 8px; background: var(--danger); border-radius: 50%; border: 2px solid var(--surface);"></span>
            </button>

            <!-- User Avatar -->
            <div class="avatar avatar-primary" style="cursor: pointer;">
                {{ substr(auth()->user()->nama ?? 'A', 0, 2) }}
            </div>
        </div>
    </header>

    <!-- Page Content -->
    <div class="page-content">
        @yield('content')
    </div>
</main>

<!-- Toast Notifications -->
@if(session('success'))
    <div id="toast" class="fixed bottom-6 right-6 z-50 animate-slide-up" style="animation: slideUp 0.3s ease-out;">
        <div style="display: flex; align-items: center; gap: 0.75rem; padding: 1rem 1.5rem; background: var(--surface); border-radius: var(--radius-lg); box-shadow: var(--shadow-lg); border-left: 4px solid var(--success);">
            <span class="material-symbols-outlined" style="color: var(--success); font-size: 20px;">check_circle</span>
            <span style="font-weight: 500;">{{ session('success') }}</span>
        </div>
    </div>
    <script>setTimeout(() => document.getElementById('toast')?.remove(), 3000);</script>
@endif

@if(session('error'))
    <div id="toast" class="fixed bottom-6 right-6 z-50 animate-slide-up" style="animation: slideUp 0.3s ease-out;">
        <div style="display: flex; align-items: center; gap: 0.75rem; padding: 1rem 1.5rem; background: var(--surface); border-radius: var(--radius-lg); box-shadow: var(--shadow-lg); border-left: 4px solid var(--danger);">
            <span class="material-symbols-outlined" style="color: var(--danger); font-size: 20px;">error</span>
            <span style="font-weight: 500;">{{ session('error') }}</span>
        </div>
    </div>
    <script>setTimeout(() => document.getElementById('toast')?.remove(), 3000);</script>
@endif

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        sidebar.classList.toggle('open');
        overlay.classList.toggle('hidden');
    }
</script>

@stack('scripts')
</body>
</html>
