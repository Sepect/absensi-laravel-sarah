<?php

namespace App\Exports;

use App\Models\Attendance;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttendanceExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    /**
     * @var array<string, string>
     */
    private array $statusLabels = [
        Attendance::STATUS_HADIR => 'Hadir',
        Attendance::STATUS_TERLAMBAT => 'Terlambat',
        Attendance::STATUS_ALPHA => 'Alpha',
        Attendance::STATUS_IZIN => 'Izin',
    ];

    public function __construct(private string $startDate, private string $endDate) {}

    /**
     * @return Builder<Attendance>
     */
    public function query(): Builder
    {
        return Attendance::query()
            ->with('internProfile')
            ->whereDate('attendance_date', '>=', $this->startDate)
            ->whereDate('attendance_date', '<=', $this->endDate)
            ->orderBy('attendance_date', 'desc')
            ->orderBy('scan_in_time', 'desc');
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return ['Nama', 'NIM/NIS', 'Tanggal', 'Masuk', 'Pulang', 'Jarak (m)', 'Status', 'Catatan'];
    }

    /**
     * @param  Attendance  $attendance
     * @return array<int, string|int|null>
     */
    public function map($attendance): array
    {
        return [
            $attendance->internProfile->nama_lengkap ?? '-',
            $attendance->internProfile->nim_nis ?? '-',
            $attendance->attendance_date?->format('Y-m-d'),
            $attendance->scan_in_time?->format('H:i:s'),
            $attendance->scan_out_time?->format('H:i:s'),
            $attendance->distance_in_meters !== null ? (int) round($attendance->distance_in_meters) : null,
            $this->statusLabels[$attendance->status] ?? $attendance->status,
            $attendance->catatan,
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
