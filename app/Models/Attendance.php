<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'intern_id',
        'attendance_date',
        'scan_in_time',
        'scan_out_time',
        'distance_in_meters',
        'status',
        'qr_validated_at',
        'qr_type',
        'catatan',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'scan_in_time' => 'datetime',
        'scan_out_time' => 'datetime',
        'distance_in_meters' => 'decimal:2',
    ];

    /**
     * Status constants
     */
    const STATUS_HADIR = 'hadir';
    const STATUS_TERLAMBAT = 'terlambat';
    const STATUS_ALPHA = 'alpha';
    const STATUS_IZIN = 'izin';

    /**
     * Get the intern profile for this attendance
     */
    public function internProfile(): BelongsTo
    {
        return $this->belongsTo(InternProfile::class, 'intern_id');
    }

    /**
     * Alias for internProfile (for backward compatibility)
     */
    public function getInternAttribute()
    {
        return $this->internProfile;
    }

    /**
     * Check if this is a check-in record
     */
    public function isCheckIn(): bool
    {
        return $this->qr_type === 'in';
    }

    /**
     * Check if this is a check-out record
     */
    public function isCheckOut(): bool
    {
        return $this->qr_type === 'out';
    }

    /**
     * Get formatted check-in time
     */
    public function getFormattedCheckInAttribute(): string
    {
        return $this->scan_in_time?->format('H:i') ?? '--:--';
    }

    /**
     * Get formatted check-out time
     */
    public function getFormattedCheckOutAttribute(): string
    {
        return $this->scan_out_time?->format('H:i') ?? '--:--';
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            self::STATUS_HADIR => 'badge-present',
            self::STATUS_TERLAMBAT => 'badge-late',
            self::STATUS_ALPHA => 'badge-absent',
            self::STATUS_IZIN => 'badge-info',
            default => 'badge-info',
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_HADIR => 'Hadir',
            self::STATUS_TERLAMBAT => 'Terlambat',
            self::STATUS_ALPHA => 'Absen',
            self::STATUS_IZIN => 'Izin',
            default => 'Unknown',
        };
    }

    /**
     * Scope for today's attendance
     */
    public function scopeToday($query)
    {
        return $query->where('attendance_date', now()->toDateString());
    }

    /**
     * Scope for specific date
     */
    public function scopeForDate($query, $date)
    {
        return $query->where('attendance_date', $date);
    }

    /**
     * Scope for specific month
     */
    public function scopeForMonth($query, int $month, int $year)
    {
        return $query->whereMonth('attendance_date', $month)
            ->whereYear('attendance_date', $year);
    }
}
