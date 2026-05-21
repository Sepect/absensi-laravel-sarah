<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\IzinRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IzinController extends Controller
{
    /**
     * Get izin requests for current intern
     * GET /api/izin
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $internProfile = $user->internProfile;

        if (!$internProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Profil peserta tidak ditemukan.',
            ], 404);
        }

        $izinRequests = $internProfile->izinRequests()
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $izinRequests->map(fn($req) => $this->formatRequest($req)),
        ]);
    }

    /**
     * Create new izin request
     * POST /api/izin
     */
    public function store(Request $request): JsonResponse
    {
        // Map English keys from React Native client to Laravel validator keys if present
        if ($request->has('type')) {
            $request->merge(['jenis' => $request->input('type')]);
        }
        if ($request->has('start_date')) {
            $request->merge(['tanggal_mulai' => $request->input('start_date')]);
        }
        if ($request->has('end_date')) {
            $request->merge(['tanggal_selesai' => $request->input('end_date')]);
        }
        if ($request->has('reason')) {
            $request->merge(['alasan' => $request->input('reason')]);
        }

        $request->validate([
            'jenis' => 'required|in:sakit,cuti,urgent',
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan' => 'required|string|min:10|max:1000',
            'bukti' => 'nullable|string',
        ]);

        $user = $request->user();
        $internProfile = $user->internProfile;

        if (!$internProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Profil peserta tidak ditemukan.',
            ], 404);
        }

        $izinRequest = IzinRequest::create([
            'intern_id' => $internProfile->id,
            'jenis' => $request->jenis,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'alasan' => $request->alasan,
            'bukti' => $request->bukti,
            'status' => IzinRequest::STATUS_PENDING,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permintaan izin berhasil diajukan.',
            'data' => $this->formatRequest($izinRequest),
        ], 201);
    }

    /**
     * Format izin request for API response
     */
    private function formatRequest(IzinRequest $request): array
    {
        return [
            'id' => $request->id,
            'jenis' => $request->jenis,
            'jenis_label' => $request->jenis_label,
            'tanggal_mulai' => $request->tanggal_mulai->toDateString(),
            'tanggal_selesai' => $request->tanggal_selesai->toDateString(),
            'durasi_hari' => $request->duration_days,
            'alasan' => $request->alasan,
            'bukti' => $request->bukti,
            'status' => $request->status,
            'status_label' => $request->status_label,
            'created_at' => $request->created_at->toIso8601String(),
        ];
    }
}