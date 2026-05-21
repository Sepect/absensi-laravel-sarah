<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\InternProfile;
use App\Models\OfficeSetting;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AttendanceService
{
    public function __construct(
        private HaversineService $haversineService,
        private QRCodeService $qrCodeService
    ) {}

    /**
     * Process attendance scan
     *
     * @param array $data
     * @return array
     */
    public function processScan(array $data): array
    {
        // 1. Validate QR code
        $qrValidation = $this->qrCodeService->validatePayload(
            $data['qr_data'],
            $data['qr_timestamp']
        );

        if (!$qrValidation['valid']) {
            return [
                'success' => false,
                'message' => $qrValidation['message'],
                'error_code' => 'INVALID_QR',
            ];
        }

        // 2. Get office settings
        $office = OfficeSetting::getActive();

        // 3. Calculate distance
        $locationCheck = $this->haversineService->isWithinOfficeRadius(
            $data['lat'],
            $data['long'],
            $office
        );

        if (!$locationCheck['within_radius']) {
            return [
                'success' => false,
                'message' => "Anda di luar jangkauan kantor. Jarak: {$this->haversineService->formatDistance($locationCheck['distance'])}. Maksimum: {$this->haversineService->formatDistance($locationCheck['max_radius'])}.",
                'error_code' => 'OUT_OF_RANGE',
                'distance_meters' => $locationCheck['distance'],
            ];
        }

        // 4. Determine attendance status
        $scanType = $data['scan_type'];
        $scanTime = now();
        $status = $this->determineStatus($scanType, $scanTime, $office);

        // 5. Get or create attendance record
        $today = now()->toDateString();
        $attendance = Attendance::firstOrCreate(
            [
                'intern_id' => $data['intern_id'],
                'attendance_date' => $today,
            ]
        );

        // 6. Update based on scan type
        if ($scanType === 'in') {
            $attendance->update([
                'scan_in_time' => $scanTime,
                'distance_in_meters' => $locationCheck['distance'],
                'status' => $status,
                'qr_validated_at' => now(),
                'qr_type' => 'in',
            ]);
        } else {
            $attendance->update([
                'scan_out_time' => $scanTime,
                'distance_in_meters' => $locationCheck['distance'],
                'qr_validated_at' => now(),
                'qr_type' => 'out',
            ]);
        }

        $message = $scanType === 'in'
            ? "Absensi masuk berhasil. Status: {$this->getStatusLabel($status)}."
            : "Absensi pulang berhasil.";

        return [
            'success' => true,
            'message' => $message,
            'data' => [
                'attendance' => $attendance->fresh(),
                'status' => $status,
                'status_label' => $this->getStatusLabel($status),
                'distance_meters' => $locationCheck['distance'],
            ],
        ];
    }

    /**
     * Determine attendance status based on scan type and time
     */
    private function determineStatus(string $scanType, Carbon $scanTime, OfficeSetting $office): string
    {
        if ($scanType !== 'in') {
            return Attendance::STATUS_HADIR;
        }

        $waktuMasuk = Carbon::parse($office->waktu_masuk);
        $toleranceMinutes = 15; // Grace period

        $latestAllowedTime = $waktuMasuk->copy()->addMinutes($toleranceMinutes);

        if ($scanTime->gt($latestAllowedTime)) {
            return Attendance::STATUS_TERLAMBAT;
        }

        return Attendance::STATUS_HADIR;
    }

    /**
     * Get status label
     */
    private function getStatusLabel(string $status): string
    {
        return match($status) {
            Attendance::STATUS_HADIR => 'Hadir',
            Attendance::STATUS_TERLAMBAT => 'Terlambat',
            Attendance::STATUS_ALPHA => 'Absen',
            Attendance::STATUS_IZIN => 'Izin',
            default => 'Unknown',
        };
    }

    /**
     * Get today's attendance summary for admin dashboard
     */
    public function getTodaySummary(): array
    {
        $today = now()->toDateString();
        $totalInterns = InternProfile::where('status', 'aktif')->count();

        $attendances = Attendance::where('attendance_date', $today)->get();

        return [
            'total_interns' => $totalInterns,
            'checked_in' => $attendances->whereNotNull('scan_in_time')->count(),
            'checked_out' => $attendances->whereNotNull('scan_out_time')->count(),
            'hadir' => $attendances->where('status', Attendance::STATUS_HADIR)->count(),
            'terlambat' => $attendances->where('status', Attendance::STATUS_TERLAMBAT)->count(),
            'alpha' => $totalInterns - $attendances->count(),
            'izin' => $attendances->where('status', Attendance::STATUS_IZIN)->count(),
        ];
    }

    /**
     * Get recent attendance records
     */
    public function getRecentAttendances(int $limit = 20): Collection
    {
        return Attendance::with('internProfile')
            ->where('attendance_date', now()->toDateString())
            ->orderBy('scan_in_time', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Generate monthly attendance report
     */
    public function generateMonthlyReport(int $month, int $year): Collection
    {
        return Attendance::with('internProfile')
            ->forMonth($month, $year)
            ->get()
            ->groupBy('intern_id')
            ->map(function ($records, $internId) {
                $intern = $records->first()->internProfile;
                return [
                    'intern_id' => $internId,
                    'nama_lengkap' => $intern->nama_lengkap,
                    'nim_nis' => $intern->nim_nis,
                    'asal' => $intern->asal_sekolah_kampus,
                    'total_hadir' => $records->where('status', Attendance::STATUS_HADIR)->count(),
                    'total_terlambat' => $records->where('status', Attendance::STATUS_TERLAMBAT)->count(),
                    'total_alpha' => $records->where('status', Attendance::STATUS_ALPHA)->count(),
                    'total_izin' => $records->where('status', Attendance::STATUS_IZIN)->count(),
                    'records' => $records,
                ];
            });
    }
}