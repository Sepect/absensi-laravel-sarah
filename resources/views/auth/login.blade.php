<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Absensi Magang</title>
    <link rel="stylesheet" href="{{ asset('build/assets/app.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
</head>
<body>
<div class="login-page">
    <div style="flex: 1; display: flex; align-items: center; justify-content: center; padding: 1.5rem;">
        <div class="login-card animate-slide-up">

            <!-- Logo & Branding -->
            <div style="text-align: center; margin-bottom: 2rem;">
                <div class="login-logo" style="margin: 0 auto;">
                    <span class="material-symbols-outlined" style="font-size: 28px; font-variation-settings: 'FILL' 1;">
                        fingerprint
                    </span>
                </div>
                <h1 class="login-title">Absensi Magang</h1>
                <p class="login-subtitle">
                    Sistem Manajemen Kehadiran Peserta Magang
                </p>
            </div>

            <!-- Alert Messages -->
            @if ($errors->any())
                <div class="alert alert-danger" style="margin-bottom: 1.5rem;">
                    <span class="material-symbols-outlined" style="font-size: 20px;">error</span>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger" style="margin-bottom: 1.5rem;">
                    <span class="material-symbols-outlined" style="font-size: 20px;">error</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('admin.login.submit') }}">
                @csrf

                <!-- Email -->
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <div class="input-wrapper">
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-input"
                            placeholder="admin@email.com"
                            value="{{ old('email') }}"
                            required
                            autofocus
                        >
                        <span class="input-icon material-symbols-outlined" style="font-size: 20px;">mail</span>
                    </div>
                    @error('email')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password" class="form-label">Kata Sandi</label>
                    <div class="input-wrapper">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-input"
                            placeholder="••••••••"
                            required
                        >
                        <span class="input-icon material-symbols-outlined" style="font-size: 20px;">lock</span>
                        <button
                            type="button"
                            onclick="togglePassword()"
                            style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--slate-400);"
                        >
                            <span class="material-symbols-outlined" id="eye-icon" style="font-size: 20px;">visibility</span>
                        </button>
                    </div>
                    @error('password')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember & Forgot -->
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--slate-600); cursor: pointer;">
                        <input type="checkbox" name="remember" style="width: 16px; height: 16px; accent-color: var(--primary);">
                        Ingat saya
                    </label>
                    <a href="#" style="font-size: 0.875rem; color: var(--primary); text-decoration: none; font-weight: 500;">
                        Lupa kata sandi?
                    </a>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary">
                    <span class="material-symbols-outlined" style="font-size: 20px;">login</span>
                    Masuk ke Dashboard
                </button>
            </form>

            <!-- Footer -->
            <p style="text-align: center; margin-top: 1.5rem; font-size: 0.8125rem; color: var(--slate-400);">
                Sistem internal terbatas. Akses tidak sah dilarang.
            </p>
        </div>
    </div>
</div>

<script>
    function togglePassword() {
        const input = document.getElementById('password');
        const icon = document.getElementById('eye-icon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.textContent = 'visibility_off';
        } else {
            input.type = 'password';
            icon.textContent = 'visibility';
        }
    }
</script>
</body>
</html>
