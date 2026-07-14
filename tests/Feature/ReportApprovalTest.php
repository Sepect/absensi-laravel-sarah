<?php

namespace Tests\Feature;

use App\Models\DailyReport;
use App\Models\InternProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportApprovalTest extends TestCase
{
    use RefreshDatabase;

    private int $internCounter = 0;

    private function makeReport(): DailyReport
    {
        $user = User::factory()->intern()->create();

        $profile = InternProfile::create([
            'user_id' => $user->id,
            'nim_nis' => '20210'.(++$this->internCounter),
            'nama_lengkap' => 'Budi Santoso',
            'asal_sekolah_kampus' => 'Universitas Indonesia',
            'nama_pembimbing' => 'Dr. Andi',
            'status' => 'aktif',
        ]);

        return DailyReport::create([
            'intern_id' => $profile->id,
            'report_date' => '2026-07-14',
            'description' => 'Mengerjakan modul absensi harian.',
        ]);
    }

    public function test_new_report_defaults_to_pending_status(): void
    {
        $report = $this->makeReport();

        $this->assertSame(DailyReport::STATUS_PENDING, $report->fresh()->status);
        $this->assertTrue($report->isPending());
    }

    public function test_admin_can_approve_a_report(): void
    {
        $admin = User::factory()->admin()->create();
        $report = $this->makeReport();

        $response = $this->actingAs($admin, 'admin')
            ->post(route('admin.reports.approve', $report->id));

        $response->assertRedirect();

        $report->refresh();
        $this->assertSame(DailyReport::STATUS_DISETUJUI, $report->status);
        $this->assertTrue($report->is_approved);
    }

    public function test_admin_can_reject_a_report(): void
    {
        $admin = User::factory()->admin()->create();
        $report = $this->makeReport();

        $response = $this->actingAs($admin, 'admin')
            ->post(route('admin.reports.reject', $report->id));

        $response->assertRedirect();

        $report->refresh();
        $this->assertSame(DailyReport::STATUS_DITOLAK, $report->status);
        $this->assertFalse($report->is_approved);
    }

    public function test_guest_cannot_reject_a_report(): void
    {
        $report = $this->makeReport();

        $response = $this->post(route('admin.reports.reject', $report->id));

        $response->assertRedirect(route('login'));
        $this->assertSame(DailyReport::STATUS_PENDING, $report->fresh()->status);
    }

    public function test_pending_scope_excludes_rejected_reports(): void
    {
        $pending = $this->makeReport();
        $rejected = $this->makeReport();
        $rejected->update(['status' => DailyReport::STATUS_DITOLAK]);

        $pendingIds = DailyReport::pending()->pluck('id');

        $this->assertTrue($pendingIds->contains($pending->id));
        $this->assertFalse($pendingIds->contains($rejected->id));
    }
}
