<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Generator - Absensi</title>
    <link rel="stylesheet" href="{{ asset('build/assets/app.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DM Sans', system-ui, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #1e40af 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .container {
            width: 100%;
            max-width: 600px;
        }

        .card {
            background: white;
            border-radius: 24px;
            padding: 3rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #1e40af, #3b82f6);
            border-radius: 16px;
            margin-bottom: 1rem;
        }

        .logo-icon {
            color: white;
            font-size: 32px;
        }

        .company-name {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 1.5rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 0.25rem;
        }

        .subtitle {
            color: #64748b;
            font-size: 0.875rem;
        }

        .qr-type-switcher {
            display: flex;
            gap: 0.75rem;
            margin-bottom: 2rem;
        }

        .qr-type-btn {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 1rem;
            border-radius: 12px;
            background: #f1f5f9;
            color: #475569;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .qr-type-btn:hover {
            background: #e2e8f0;
        }

        .qr-type-btn.active {
            background: linear-gradient(135deg, #1e40af, #3b82f6);
            color: white;
            box-shadow: 0 4px 15px rgba(30, 64, 175, 0.3);
        }

        .qr-type-btn .material-symbols-outlined {
            font-size: 24px;
        }

        .qr-frame {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background: white;
            border-radius: 20px;
            border: 4px solid #1e40af;
            margin-bottom: 2rem;
        }

        .qr-image {
            width: 280px;
            height: 280px;
        }

        .qr-overlay {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
        }

        .qr-center {
            background: white;
            padding: 12px;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .qr-center-icon {
            color: #1e40af;
            font-size: 36px;
        }

        .timer {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            background: #f1f5f9;
            padding: 1rem 2rem;
            border-radius: 9999px;
            margin-bottom: 2rem;
        }

        .timer-icon {
            color: #1e40af;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .timer-label {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 0.05em;
        }

        .timer-value {
            font-size: 2rem;
            font-weight: 800;
            font-family: 'Courier New', monospace;
            color: #1e40af;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .info-box {
            background: #f8fafc;
            border-radius: 12px;
            padding: 1rem;
            text-align: center;
        }

        .info-value {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1e293b;
        }

        .info-label {
            font-size: 0.75rem;
            color: #64748b;
            margin-top: 0.25rem;
        }

        .info-box.success .info-value { color: #059669; }
        .info-box.warning .info-value { color: #d97706; }
        .info-box.danger .info-value { color: #dc2626; }
        .info-box.primary .info-value { color: #1e40af; }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 2rem;
            color: #64748b;
            text-decoration: none;
            font-size: 0.875rem;
            transition: color 0.2s;
        }

        .back-link:hover {
            color: #1e40af;
        }

        .server-clock {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-bottom: 2rem;
            background: #f1f5f9;
            padding: 0.75rem 1.5rem;
            border-radius: 16px;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.06);
            border: 1px solid #e2e8f0;
        }

        .clock-time {
            font-size: 2.25rem;
            font-weight: 800;
            font-family: 'Courier New', monospace;
            color: #1e293b;
            letter-spacing: 0.05em;
        }

        .clock-timezone {
            font-size: 0.875rem;
            font-weight: 700;
            color: #3b82f6;
            background: #dbeafe;
            padding: 0.125rem 0.5rem;
            border-radius: 6px;
            margin-left: 0.25rem;
        }

        @media (max-width: 480px) {
            .card {
                padding: 1.5rem;
            }

            .qr-image {
                width: 200px;
                height: 200px;
            }

            .info-grid {
                grid-template-columns: 1fr 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <!-- Header -->
            <div class="header">
                <div class="logo">
                    <span class="material-symbols-outlined logo-icon">fingerprint</span>
                </div>
                <h1 class="company-name">{{ $instansi->nama_instansi }}</h1>
                <p class="subtitle">Scan QR Code untuk Absensi Masuk/Pulang</p>
            </div>

            <!-- Server Clock -->
            <div class="server-clock">
                <span class="material-symbols-outlined" style="color: #3b82f6; font-size: 28px;">schedule</span>
                <span class="clock-time" id="server-time">--:--:--</span>
                <span class="clock-timezone">{{ $timezoneAbbr }}</span>
            </div>



            <!-- QR Code Display -->
            <div class="qr-frame">
                <img src="{{ $qrImage }}" alt="QR Code" class="qr-image">
                <div class="qr-overlay">
                    <div class="qr-center">
                        <span class="material-symbols-outlined qr-center-icon">fingerprint</span>
                    </div>
                </div>
            </div>

            <!-- Timer -->
            <div class="timer">
                <span class="material-symbols-outlined timer-icon">timer</span>
                <div>
                    <div class="timer-label">QR Code refresh dalam</div>
                    <div id="countdown" class="timer-value">--:--</div>
                </div>
            </div>

            <!-- Stats -->
            <div class="info-grid">
                <div class="info-box success">
                    <div class="info-value">{{ $summary['checked_in'] }}</div>
                    <div class="info-label">Hadir Masuk</div>
                </div>
                <div class="info-box primary">
                    <div class="info-value">{{ $summary['checked_out'] }}</div>
                    <div class="info-label">Sudah Pulang</div>
                </div>
                <div class="info-box warning">
                    <div class="info-value">{{ $summary['terlambat'] }}</div>
                    <div class="info-label">Terlambat</div>
                </div>
                <div class="info-box danger">
                    <div class="info-value">{{ $summary['alpha'] }}</div>
                    <div class="info-label">Belum Absen</div>
                </div>
            </div>

            <!-- Back Link -->
            <a href="/" class="back-link">
                <span class="material-symbols-outlined" style="vertical-align: middle;">arrow_back</span>
                Kembali ke Dashboard
            </a>
        </div>
    </div>

    <script>
        let remaining = {{ $timeRemaining }};
        const qrExpiryMinutes = {{ $qrExpiryMinutes }};
        const defaultFallbackSeconds = qrExpiryMinutes * 60;
        let timerId = null;

        // Server clock sync
        let serverTimestamp = {{ now()->timestamp }} * 1000;
        let localStartTime = Date.now();

        function updateServerClock() {
            const elapsed = Date.now() - localStartTime;
            const currentServerTime = new Date(serverTimestamp + elapsed);

            const hours = String(currentServerTime.getHours()).padStart(2, '0');
            const minutes = String(currentServerTime.getMinutes()).padStart(2, '0');
            const seconds = String(currentServerTime.getSeconds()).padStart(2, '0');

            const timeStr = `${hours}:${minutes}:${seconds}`;
            const serverTimeEl = document.getElementById('server-time');
            if (serverTimeEl) {
                serverTimeEl.textContent = timeStr;
            }
        }

        setInterval(updateServerClock, 1000);
        updateServerClock();

        function updateCountdown() {
            const minutes = Math.floor(remaining / 60);
            const seconds = remaining % 60;
            document.getElementById('countdown').textContent =
                String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');

            if (remaining > 0) {
                remaining--;
                timerId = setTimeout(updateCountdown, 1000);
            } else {
                refreshQRCode();
            }
        }

        async function refreshQRCode() {
            // Hentikan timer yang sedang berjalan untuk menghindari penumpukan
            if (timerId) {
                clearTimeout(timerId);
            }

            document.getElementById('countdown').textContent = "--:--";

            try {
                // Ambil data QR Code dan statistik terbaru secara asynchronous (AJAX)
                const response = await fetch(window.location.href, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    throw new Error('Gagal memuat QR Code baru');
                }

                const data = await response.json();

                // 1. Update gambar QR Code
                document.querySelector('.qr-image').src = data.qrImage;

                // Sync server clock timestamp to prevent drift
                if (data.serverTimestamp) {
                    serverTimestamp = data.serverTimestamp;
                    localStartTime = Date.now();
                    updateServerClock();
                }

                // 2. Update statistik absensi hari ini
                updateStatsUI(data.summary);

                // 3. Reset countdown timer
                remaining = data.timeRemaining > 0 ? data.timeRemaining : (data.qrExpiryMinutes ? data.qrExpiryMinutes * 60 : defaultFallbackSeconds);
                updateCountdown();

            } catch (error) {
                console.error(error);
                document.getElementById('countdown').textContent = "Retrying...";
                // Coba lagi dalam 3 detik jika gagal
                setTimeout(refreshQRCode, 3000);
            }
        }

        function updateStatsUI(stats) {
            const successVal = document.querySelector('.info-box.success .info-value');
            if (successVal) successVal.textContent = stats.checked_in;

            const primaryVal = document.querySelector('.info-box.primary .info-value');
            if (primaryVal) primaryVal.textContent = stats.checked_out;

            const warningVal = document.querySelector('.info-box.warning .info-value');
            if (warningVal) warningVal.textContent = stats.terlambat;

            const dangerVal = document.querySelector('.info-box.danger .info-value');
            if (dangerVal) dangerVal.textContent = stats.alpha;
        }

        // Sinkronisasi data statistik di background setiap 10 detik tanpa mengganggu timer QR
        setInterval(async () => {
            try {
                const response = await fetch(window.location.href, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                if (response.ok) {
                    const data = await response.json();
                    updateStatsUI(data.summary);
                }
            } catch (e) {
                console.error("Gagal sinkronisasi data statistik di background", e);
            }
        }, 10000);

        updateCountdown();
    </script>
</body>
</html>
