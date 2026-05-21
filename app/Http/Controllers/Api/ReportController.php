<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DailyReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Get all daily reports for current intern
     * GET /api/laporan-harian
     */
    public function index(Request $request): JsonResponse
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

        $query = $internProfile->dailyReports();

        if ($request->month && $request->year) {
            $query->whereMonth('report_date', $request->month)
                ->whereYear('report_date', $request->year);
        } else {
            $query->whereMonth('report_date', now()->month)
                ->whereYear('report_date', now()->year);
        }

        $reports = $query->orderBy('report_date', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $reports->map(fn($report) => $this->formatReport($report)),
        ]);
    }

    /**
     * Get daily report by date
     * GET /api/laporan-harian/{date}
     */
    public function show(Request $request, string $date): JsonResponse
    {
        $user = $request->user();
        $internProfile = $user->internProfile;

        if (!$internProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Profil peserta tidak ditemukan.',
            ], 404);
        }

        $report = $internProfile->dailyReports()
            ->where('report_date', $date)
            ->first();

        if (!$report) {
            return response()->json([
                'success' => false,
                'message' => 'Laporan tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $this->formatReport($report),
        ]);
    }

    /**
     * Create new daily report
     * POST /api/laporan-harian
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'report_date' => 'required|date_format:Y-m-d',
            'description' => 'required|string|min:20|max:5000',
        ]);

        $user = $request->user();
        $internProfile = $user->internProfile;

        if (!$internProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Profil peserta tidak ditemukan.',
            ], 404);
        }

        // Check if report already exists for this date
        $existingReport = DailyReport::where('intern_id', $internProfile->id)
            ->where('report_date', $request->report_date)
            ->first();

        if ($existingReport) {
            return response()->json([
                'success' => false,
                'message' => 'Laporan untuk tanggal ini sudah ada. Gunakan fitur edit untuk mengubah.',
            ], 400);
        }

        $report = DailyReport::create([
            'intern_id' => $internProfile->id,
            'report_date' => $request->report_date,
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Laporan harian berhasil dikirim.',
            'data' => $this->formatReport($report),
        ], 201);
    }

    /**
     * Update existing daily report
     * PUT /api/laporan-harian/{id}
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'description' => 'required|string|min:20|max:5000',
        ]);

        $user = $request->user();
        $internProfile = $user->internProfile;

        if (!$internProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Profil peserta tidak ditemukan.',
            ], 404);
        }

        $report = DailyReport::where('intern_id', $internProfile->id)
            ->where('id', $id)
            ->first();

        if (!$report) {
            return response()->json([
                'success' => false,
                'message' => 'Laporan tidak ditemukan.',
            ], 404);
        }

        // Cannot edit approved reports
        if ($report->is_approved) {
            return response()->json([
                'success' => false,
                'message' => 'Laporan yang sudah disetujui tidak dapat diubah.',
            ], 400);
        }

        $report->update([
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Laporan berhasil diperbarui.',
            'data' => $this->formatReport($report->fresh()),
        ]);
    }

    /**
     * Format report for API response
     */
    private function formatReport(DailyReport $report): array
    {
        return [
            'id' => $report->id,
            'report_date' => $report->report_date->toDateString(),
            'description' => $report->description,
            'is_approved' => $report->is_approved,
            'created_at' => $report->created_at->toIso8601String(),
            'updated_at' => $report->updated_at->toIso8601String(),
        ];
    }
}