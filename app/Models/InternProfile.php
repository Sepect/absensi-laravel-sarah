<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InternProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nim_nis',
        'nama_lengkap',
        'asal_sekolah_kampus',
        'no_telp',
        'nama_pembimbing',
        'alamat',
        'foto_peserta',
        'kontak_darurat',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    /**
     * Get the user that owns this profile
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all attendances for this intern
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'intern_id');
    }

    /**
     * Get all daily reports for this intern
     */
    public function dailyReports(): HasMany
    {
        return $this->hasMany(DailyReport::class, 'intern_id');
    }

    /**
     * Get all izin requests for this intern
     */
    public function izinRequests(): HasMany
    {
        return $this->hasMany(IzinRequest::class, 'intern_id');
    }

    /**
     * Get today's attendance
     */
    public function todayAttendance()
    {
        return $this->attendances()
            ->where('attendance_date', now()->toDateString())
            ->first();
    }

    /**
     * Get attendance summary for current month
     */
    public function getMonthlySummary(int $month = null, int $year = null): array
    {
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;

        $attendances = $this->attendances()
            ->whereMonth('attendance_date', $month)
            ->whereYear('attendance_date', $year)
            ->get();

        return [
            'hadir' => $attendances->where('status', 'hadir')->count(),
            'terlambat' => $attendances->where('status', 'terlambat')->count(),
            'alpha' => $attendances->where('status', 'alpha')->count(),
            'izin' => $attendances->where('status', 'izin')->count(),
            'total_hari_kerja' => $attendances->count(),
        ];
    }

    /**
     * Get initials for avatar display
     */
    public function getInitialsAttribute(): string
    {
        $words = explode(' ', $this->nama_lengkap);
        $initials = array_map(fn($word) => $word[0] ?? '', $words);
        return strtoupper(implode('', array_slice($initials, 0, 2)));
    }
}