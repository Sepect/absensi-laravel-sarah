# Sistem Absensi Magang - Laravel Backend & Admin Panel

## Overview
Aplikasi full-stack untuk sistem absensi magang dengan fitur:
- **Mobile App** (React Native): Absensi QR + GPS untuk peserta magang
- **Admin Panel** (Laravel Blade): Dashboard management untuk admin

## Tech Stack
- **Backend**: Laravel 11
- **Frontend Web**: Blade Templates + Tailwind CSS
- **Mobile API**: Laravel Sanctum (token-based auth)
- **Database**: MySQL

## Directory Structure
```
absensi-laravel/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/           # API Controllers (for mobile app)
│   │   │   │   ├── AuthController.php
│   │   │   │   ├── AttendanceController.php
│   │   │   │   ├── IzinController.php
│   │   │   │   ├── ReportController.php
│   │   │   │   └── SettingsController.php
│   │   │   └── Web/           # Web Controllers (for admin panel)
│   │   │       ├── AuthController.php
│   │   │       └── DashboardController.php
│   ├── Models/
│   │   ├── Admin.php
│   │   ├── Attendance.php
│   │   ├── DailyReport.php
│   │   ├── Intern.php
│   │   ├── IzinRequest.php
│   │   └── OfficeSetting.php
│   └── Services/
│       ├── AttendanceService.php
│       ├── HaversineService.php
│       └── QRCodeService.php
├── database/
│   └── migrations/
│       └── 2024_01_01_000001_create_sistem_absensi_tables.php
├── resources/
│   ├── css/
│   │   └── app.css           # Tailwind CSS
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php # Admin panel layout
│       ├── auth/
│       │   └── login.blade.php
│       └── dashboard/
│           ├── index.blade.php        # Dashboard
│           ├── attendance.blade.php   # QR Generator
│           ├── settings/index.blade.php
│           ├── interns/
│           │   ├── index.blade.php
│           │   ├── create.blade.php
│           │   └── edit.blade.php
│           ├── reports/index.blade.php
│           └── izin/index.blade.php
├── routes/
│   ├── api.php   # API routes
│   └── web.php   # Web routes
└── tailwind.config.js
```

## Features

### Admin Panel (Web)
- [x] Login dengan autentikasi
- [x] Dashboard dengan ringkasan harian
- [x] Generator QR Code Dinamis (auto-refresh 5 menit)
- [x] Manajemen Peserta Magang (CRUD)
- [x] Laporan Harian (approve/reject)
- [x] Permintaan Izin (approve/reject)
- [x] Pengaturan Lokasi & Jadwal
- [x] Export laporan (PDF/Excel placeholder)

### Mobile API
- [x] Login intern
- [x] Scan QR attendance
- [x] Haversine distance validation
- [x] Attendance history
- [x] Daily reports CRUD
- [x] Izin requests
- [x] Office settings

## API Endpoints

### Authentication
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/login` | Login intern |
| POST | `/api/admin/login` | Login admin |
| GET | `/api/profile` | Get current user |
| POST | `/api/logout` | Logout |

### Attendance
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/absensi` | Scan QR (absensi masuk/pulang) |
| GET | `/api/absensi/riwayat` | Get attendance history |
| GET | `/api/absensi/summary` | Get monthly summary |
| GET | `/api/absensi/today` | Get today's attendance |

### Reports
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/laporan-harian` | List reports |
| GET | `/api/laporan-harian/{date}` | Get report by date |
| POST | `/api/laporan-harian` | Create report |
| PUT | `/api/laporan-harian/{id}` | Update report |

### Settings
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/office-settings` | Get office settings |

### Izin
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/izin` | List izin requests |
| POST | `/api/izin` | Create izin request |

## Installation

1. Clone repository
2. Install dependencies:
   ```bash
   composer install
   npm install
   ```

3. Setup environment:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Configure database in `.env`:
   ```
   DB_DATABASE=absensi_magang
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. Run migrations & seeders:
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. Build assets:
   ```bash
   npm run build
   ```

7. Start server:
   ```bash
   php artisan serve
   ```

## Database Schema

### Tables
- **admins** - Admin users
- **interns** - Intern data
- **attendances** - Attendance records
- **daily_reports** - Daily reports
- **izin_requests** - Leave requests
- **office_settings** - Office location/schedule config
- **personal_access_tokens** - Sanctum tokens

## Security Features

### QR Code Validation
- Timestamp-based (5 minute window)
- Server-side validation only
- Cannot be spoofed from client

### Location Validation
- Haversine formula for distance calculation
- Server-side GPS validation
- Configurable radius

## Design System

Uses Material Design 3 inspired design with custom color palette:

- Primary: `#003d9b`
- Primary Container: `#0052cc`
- Status Present: `#36B37E`
- Status Late: `#FFAB00`
- Status Absent: `#FF5630`

See `tailwind.config.js` for full color system.

## Demo Data

Run seeder for demo data:
```bash
php artisan db:seed --class=AdminSeeder
php artisan db:seed --class=InternSeeder
```

### Demo Credentials
- **Admin**: admin@anchorprecision.com / password
- **Intern**: 1234567890 / password