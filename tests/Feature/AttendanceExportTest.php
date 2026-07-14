<?php

namespace Tests\Feature;

use App\Exports\AttendanceExport;
use App\Models\Attendance;
use App\Models\InternProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class AttendanceExportTest extends TestCase
{
    use RefreshDatabase;

    private function makeAttendance(string $date): Attendance
    {
        $user = User::factory()->intern()->create();

        $profile = InternProfile::create([
            'user_id' => $user->id,
            'nim_nis' => '2021001',
            'nama_lengkap' => 'Budi Santoso',
            'asal_sekolah_kampus' => 'Universitas Indonesia',
            'nama_pembimbing' => 'Dr. Andi',
            'status' => 'aktif',
        ]);

        return Attendance::create([
            'intern_id' => $profile->id,
            'attendance_date' => $date,
            'scan_in_time' => $date.' 08:05:00',
            'scan_out_time' => $date.' 17:00:00',
            'distance_in_meters' => 12.34,
            'status' => Attendance::STATUS_TERLAMBAT,
            'catatan' => 'Datang terlambat',
        ]);
    }

    public function test_admin_can_export_attendance_history_as_xlsx(): void
    {
        Excel::fake();

        $admin = User::factory()->admin()->create();
        $this->makeAttendance('2026-07-14');

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.attendance.export', ['date' => '2026-07-14']));

        $response->assertOk();

        Excel::assertDownloaded('riwayat-absensi-2026-07-14.xlsx', function (AttendanceExport $export) {
            $rows = $export->query()->get();

            return $rows->count() === 1
                && $rows->first()->internProfile->nama_lengkap === 'Budi Santoso'
                && $rows->first()->status === Attendance::STATUS_TERLAMBAT;
        });
    }

    public function test_export_only_includes_records_for_the_selected_date(): void
    {
        Excel::fake();

        $admin = User::factory()->admin()->create();
        $this->makeAttendance('2026-07-14');

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.attendance.export', ['date' => '2026-07-13']));

        $response->assertOk();

        Excel::assertDownloaded('riwayat-absensi-2026-07-13.xlsx', function (AttendanceExport $export) {
            return $export->query()->get()->isEmpty();
        });
    }

    public function test_admin_can_export_attendance_history_for_a_date_range(): void
    {
        Excel::fake();

        $admin = User::factory()->admin()->create();
        $this->makeAttendance('2026-07-14');

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.attendance.export', [
                'start_date' => '2026-07-10',
                'end_date' => '2026-07-16',
            ]));

        $response->assertOk();

        Excel::assertDownloaded('riwayat-absensi-2026-07-10_sd_2026-07-16.xlsx', function (AttendanceExport $export) {
            $rows = $export->query()->get();

            return $rows->count() === 1
                && $rows->first()->internProfile->nama_lengkap === 'Budi Santoso';
        });
    }

    public function test_export_range_excludes_records_outside_the_range(): void
    {
        Excel::fake();

        $admin = User::factory()->admin()->create();
        $this->makeAttendance('2026-07-14');

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.attendance.export', [
                'start_date' => '2026-07-01',
                'end_date' => '2026-07-10',
            ]));

        $response->assertOk();

        Excel::assertDownloaded('riwayat-absensi-2026-07-01_sd_2026-07-10.xlsx', function (AttendanceExport $export) {
            return $export->query()->get()->isEmpty();
        });
    }

    public function test_guest_cannot_export_attendance_history(): void
    {
        $response = $this->get(route('admin.attendance.export', ['date' => '2026-07-14']));

        $response->assertRedirect(route('login'));
    }
}
