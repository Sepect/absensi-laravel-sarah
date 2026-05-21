<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\InternProfile;
use App\Services\AttendanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request as HttpRequest;

class AttendanceController extends Controller
{
    public function __construct(
        private AttendanceService $attendanceService
    ) {}

    /**
     * Process QR code scan for attendance
     * POST /api/absensi
     */
    public function scan(HttpRequest $request): JsonResponse
    {
        $request->validate([
            'qr_data' => 'required|string',
            'qr_timestamp' => 'required',
            'lat' => 'required|numeric',
            'long' => 'required|numeric',
            'scan_type' => 'required|in:in,out',
        ]);

        // Get intern profile from authenticated user
        $user = $request->user();
        $internProfile = $user->internProfile;

        if (!$internProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Profil peserta tidak ditemukan.',
            ], 404);
        }

        $result = $this->attendanceService->processScan([
            'qr_data' => $request->qr_data,
            'qr_timestamp' => $request->qr_timestamp,
            'lat' => $request->lat,
            'long' => $request->long,
            'scan_type' => $request->scan_type,
            'intern_id' => $internProfile->id,
        ]);

        $statusCode = match($result['error_code'] ?? null) {
            'INVALID_QR' => 400,
            'OUT_OF_RANGE' => 403,
            default => $result['success'] ? 200 : 500,
        };

        return response()->json($result, $statusCode);
    }

    /**
     * Get attendance history for current intern
     * GET /api/absensi/riwayat
     */
    public function history(HttpRequest $request): JsonResponse
    {
        $request->validate([
            'month' => 'nullable|integer|between:1,12',
            'year' => 'nullable|integer|between:2020,2100',
        ]);

        $user = $request->user();
        $internProfile = $user->internProfile;

        if (!$internProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Profil peserta tidak ditemukan.',
            ], 404);
        }

        $query = $internProfile->attendances();

        if ($request->month && $request->year) {
            $query->forMonth($request->month, $request->year);
        } else {
            $query->forMonth(now()->month, now()->year);
        }

        $attendances = $query->orderBy('attendance_date', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $attendances->map(fn($att) => $this->formatAttendance($att)),
        ]);
    }

    /**
     * Get attendance summary for current intern
     * GET /api/absensi/summary
     */
    public function summary(HttpRequest $request): JsonResponse
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $user = $request->user();
        $internProfile = $user->internProfile;

        if (!$internProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Profil peserta tidak ditemukan.',
            ], 404);
        }

        $summary = $internProfile->getMonthlySummary((int) $month, (int) $year);

        return response()->json([
            'success' => true,
            'data' => $summary,
        ]);
    }

    /**
     * Get today's attendance status for current intern
     * GET /api/absensi/today
     */
    public function today(HttpRequest $request): JsonResponse
    {
        $user = $request->user();
        $internProfile = $user->internProfile;

        if (!$internProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Profil peserta tidak ditemukan.',
            ], 404);
        }

        $attendance = $internProfile->todayAttendance();

        return response()->json([
            'success' => true,
            'data' => $attendance ? $this->formatAttendance($attendance) : null,
        ]);
    }

    /**
     * Format attendance for API response
     */
    private function formatAttendance(Attendance $attendance): array
    {
        return [
            'id' => $attendance->id,
            'attendance_date' => \Carbon\Carbon::parse($attendance->attendance_date)->toDateString(),
            'scan_in_time' => $attendance->scan_in_time?->toIso8601String(),
            'scan_out_time' => $attendance->scan_out_time?->toIso8601String(),
            'distance_meters' => $attendance->distance_in_meters,
            'status' => $attendance->status,
            'qr_type' => $attendance->qr_type,
            'created_at' => $attendance->created_at->toIso8601String(),
        ];
    }
}