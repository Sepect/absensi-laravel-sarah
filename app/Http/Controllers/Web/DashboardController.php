<?php

namespace App\Http\Controllers\Web;

use App\Exports\AttendanceExport;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\DailyReport;
use App\Models\InternProfile;
use App\Models\IzinRequest;
use App\Models\OfficeSetting;
use App\Models\User;
use App\Services\AttendanceService;
use App\Services\QRCodeService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DashboardController extends Controller
{
    public function __construct(
        private AttendanceService $attendanceService,
        private QRCodeService $qrCodeService
    ) {}

    /**
     * Main dashboard
     */
    public function index(Request $request)
    {
        $summary = $this->attendanceService->getTodaySummary();
        $recentAttendances = $this->attendanceService->getRecentAttendances(10);

        // Get pending reports count
        $pendingReports = DailyReport::pending()->count();
        $pendingIzin = IzinRequest::pending()->count();

        return view('dashboard.index', compact(
            'summary',
            'recentAttendances',
            'pendingReports',
            'pendingIzin'
        ));
    }

    /**
     * Attendance history
     */
    public function attendanceHistory(Request $request)
    {
        $date = $request->get('date', now()->toDateString());

        $attendances = Attendance::with('internProfile')
            ->where('attendance_date', $date)
            ->orderBy('scan_in_time', 'desc')
            ->paginate(20);

        return view('dashboard.attendance-history', compact('attendances', 'date'));
    }

    /**
     * Export attendance history to an Excel (.xlsx) file.
     */
    public function exportAttendance(Request $request): BinaryFileResponse
    {
        $startDate = $request->get('start_date', $request->get('date', now()->toDateString()));
        $endDate = $request->get('end_date', $startDate);

        // Ensure the range is ordered chronologically even if the inputs are swapped.
        if ($startDate > $endDate) {
            [$startDate, $endDate] = [$endDate, $startDate];
        }

        $filename = $startDate === $endDate
            ? 'riwayat-absensi-'.$startDate.'.xlsx'
            : 'riwayat-absensi-'.$startDate.'_sd_'.$endDate.'.xlsx';

        return Excel::download(new AttendanceExport($startDate, $endDate), $filename);
    }

    /**
     * QR Code Generator (Full Screen)
     */
    public function qrcode(Request $request)
    {
        $type = $request->get('type', 'in');
        $instansi = OfficeSetting::first();

        $qrData = $this->qrCodeService->getActiveQRCode($type);
        $qrImage = $this->qrCodeService->generateImage($qrData['data']);
        $timeRemaining = $this->qrCodeService->getTimeRemaining($type);

        $summary = $this->attendanceService->getTodaySummary();
        $office = OfficeSetting::getActive();
        $qrExpiryMinutes = $office->qr_expiry_minutes ?? 5;

        $timezone = config('app.timezone', 'Asia/Jakarta');
        $timezoneAbbr = match ($timezone) {
            'Asia/Jakarta' => 'WIB',
            'Asia/Makassar' => 'WITA',
            'Asia/Jayapura' => 'WIT',
            default => now()->format('T'),
        };

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'qrImage' => $qrImage,
                'timeRemaining' => $timeRemaining,
                'summary' => $summary,
                'qrExpiryMinutes' => $qrExpiryMinutes,
                'serverTimestamp' => now()->timestamp * 1000,
            ]);
        }

        return view('dashboard.qrcode', compact(
            'qrImage',
            'timeRemaining',
            'type',
            'summary',
            'qrExpiryMinutes',
            'timezoneAbbr', 'instansi'
        ));
    }

    /**
     * Interns management
     */
    public function interns(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status', 'all');

        $query = InternProfile::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('nim_nis', 'like', "%{$search}%");
            });
        }

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $interns = $query->with('user')->orderBy('nama_lengkap')->paginate(15);

        return view('dashboard.interns.index', compact('interns', 'search', 'status'));
    }

    /**
     * Create intern form
     */
    public function createIntern()
    {
        return view('dashboard.interns.create');
    }

    /**
     * Store new intern
     */
    public function storeIntern(Request $request)
    {
        $validated = $request->validate([
            'nim_nis' => 'required|string|unique:intern_profiles,nim_nis',
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'asal_sekolah_kampus' => 'required|string|max:255',
            'no_telp' => 'nullable|string|max:20',
            'nama_pembimbing' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'kontak_darurat' => 'nullable|string|max:20',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
        ]);

        $validated['password'] = bcrypt($validated['password']);

        // Create user first
        $user = User::create([
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => User::ROLE_INTERN,
        ]);

        // Then create intern profile
        InternProfile::create([
            'user_id' => $user->id,
            'nim_nis' => $validated['nim_nis'],
            'nama_lengkap' => $validated['nama_lengkap'],
            'asal_sekolah_kampus' => $validated['asal_sekolah_kampus'],
            'no_telp' => $validated['no_telp'] ?? null,
            'nama_pembimbing' => $validated['nama_pembimbing'],
            'alamat' => $validated['alamat'] ?? null,
            'kontak_darurat' => $validated['kontak_darurat'] ?? null,
            'tanggal_mulai' => $validated['tanggal_mulai'] ?? null,
            'tanggal_selesai' => $validated['tanggal_selesai'] ?? null,
            'status' => 'aktif',
        ]);

        return redirect()
            ->route('admin.interns.index')
            ->with('success', 'Data peserta magang berhasil ditambahkan.');
    }

    /**
     * Edit intern form
     */
    public function editIntern($id)
    {
        $intern = InternProfile::with('user')->findOrFail($id);

        return view('dashboard.interns.edit', compact('intern'));
    }

    /**
     * Update intern
     */
    public function updateIntern(Request $request, $id)
    {
        $intern = InternProfile::with('user')->findOrFail($id);

        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email,'.$intern->user_id,
            'password' => 'nullable|string|min:6',
            'asal_sekolah_kampus' => 'required|string|max:255',
            'no_telp' => 'nullable|string|max:20',
            'nama_pembimbing' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'kontak_darurat' => 'nullable|string|max:20',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'status' => 'required|in:aktif,selesai',
        ]);

        // Update user email/password if provided
        if (isset($validated['password'])) {
            $intern->user->update([
                'email' => $validated['email'],
                'password' => bcrypt($validated['password']),
            ]);
        } else {
            $intern->user->update(['email' => $validated['email']]);
        }

        // Update intern profile
        $intern->update([
            'nama_lengkap' => $validated['nama_lengkap'],
            'asal_sekolah_kampus' => $validated['asal_sekolah_kampus'],
            'no_telp' => $validated['no_telp'],
            'nama_pembimbing' => $validated['nama_pembimbing'],
            'alamat' => $validated['alamat'],
            'kontak_darurat' => $validated['kontak_darurat'],
            // 'tanggal_mulai' => $validated['tanggal_mulai'],
            // 'tanggal_selesai' => $validated['tanggal_selesai'],
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('admin.interns.index')
            ->with('success', 'Data peserta magang berhasil diperbarui.');
    }

    /**
     * Destroy intern
     */
    public function destroyIntern($id)
    {
        $intern = InternProfile::with('user')->findOrFail($id);

        // Delete the user (cascades to intern profile)
        $intern->user->delete();

        return redirect()
            ->route('admin.interns.index')
            ->with('success', 'Data peserta magang berhasil dihapus.');
    }

    /**
     * Reports list
     */
    public function reports(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $reports = DailyReport::with('internProfile')
            ->whereMonth('report_date', $month)
            ->whereYear('report_date', $year)
            ->orderBy('report_date', 'desc')
            ->paginate(20);

        return view('dashboard.reports.index', compact('reports', 'month', 'year'));
    }

    /**
     * Approve report
     */
    public function approveReport($id)
    {
        $report = DailyReport::findOrFail($id);
        $report->update([
            'is_approved' => true,
            'status' => DailyReport::STATUS_DISETUJUI,
            'approved_by' => auth()->user()->adminProfile?->id,
        ]);

        return back()->with('success', 'Laporan berhasil disetujui.');
    }

    /**
     * Reject report
     */
    public function rejectReport($id)
    {
        $report = DailyReport::findOrFail($id);
        $report->update([
            'is_approved' => false,
            'status' => DailyReport::STATUS_DITOLAK,
            'approved_by' => auth()->user()->adminProfile?->id,
        ]);

        return back()->with('success', 'Laporan berhasil ditolak.');
    }

    /**
     * Izin requests
     */
    public function izinRequests(Request $request)
    {
        $status = $request->get('status', 'pending');

        $query = IzinRequest::with('internProfile');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('dashboard.izin.index', compact('requests', 'status'));
    }

    /**
     * Review izin request
     */
    public function reviewIzin(Request $request, $id)
    {
        $izinRequest = IzinRequest::findOrFail($id);

        $validated = $request->validate([
            'action' => 'required|in:approve,reject',
        ]);

        $status = $validated['action'] === 'approve' ? 'disetujui' : 'ditolak';

        $izinRequest->update([
            'status' => $status,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        // If approved, automatically create/update attendance records with status 'izin'
        if ($status === 'disetujui') {
            $startDate = Carbon::parse($izinRequest->tanggal_mulai);
            $endDate = Carbon::parse($izinRequest->tanggal_selesai);

            $currentDate = $startDate->copy();
            while ($currentDate->lte($endDate)) {
                $dateStr = $currentDate->toDateString();

                Attendance::updateOrCreate(
                    [
                        'intern_id' => $izinRequest->intern_id,
                        'attendance_date' => $dateStr,
                    ],
                    [
                        'status' => 'izin',
                    ]
                );

                $currentDate->addDay();
            }
        }

        return back()->with('success', 'Permintaan izin berhasil diproses.');
    }

    /**
     * Settings page
     */
    public function settings()
    {
        $settings = OfficeSetting::getActive();

        return view('dashboard.settings.index', compact('settings'));
    }

    /**
     * Update settings
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'nama_instansi' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius_meters' => 'required|integer|min:10|max:1000',
            'waktu_masuk' => 'required',
            'waktu_pulang' => 'required|after:waktu_masuk',
            'qr_expiry_minutes' => 'required|integer|min:1|max:60',
        ]);

        $settings = OfficeSetting::getActive();
        $settings->update($validated);

        // Force refresh QR codes
        $this->qrCodeService->forceRefresh();

        return back()->with('success', 'Pengaturan berhasil diperbarui.');
    }

    /**
     * Export reports
     */
    public function exportReports(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        // Implementation for Excel/PDF export
        // This would use Laravel Excel or DomPDF
    }
}
