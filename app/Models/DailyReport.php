<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'intern_id',
        'report_date',
        'description',
        'is_approved',
        'status',
        'approved_by',
    ];

    protected $casts = [
        'report_date' => 'date',
        'is_approved' => 'boolean',
    ];

    /**
     * @var array<string, string>
     */
    protected $attributes = [
        'status' => self::STATUS_PENDING,
    ];

    /**
     * Status constants
     */
    const STATUS_PENDING = 'pending';

    const STATUS_DISETUJUI = 'disetujui';

    const STATUS_DITOLAK = 'ditolak';

    /**
     * Get the intern profile who wrote this report
     */
    public function internProfile(): BelongsTo
    {
        return $this->belongsTo(InternProfile::class, 'intern_id');
    }

    /**
     * Get the admin profile who approved this report
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(AdminProfile::class, 'approved_by');
    }

    /**
     * Check if report is approved
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_DISETUJUI;
    }

    /**
     * Check if report is pending review
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if report is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_DITOLAK;
    }

    /**
     * Get the Indonesian status label
     */
    public function getStatusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_DISETUJUI => 'Disetujui',
            self::STATUS_DITOLAK => 'Ditolak',
            default => 'Menunggu',
        };
    }

    /**
     * Get the badge CSS class for the current status
     */
    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_DISETUJUI => 'badge-success',
            self::STATUS_DITOLAK => 'badge-danger',
            default => 'badge-warning',
        };
    }

    /**
     * Get word count
     */
    public function getWordCountAttribute(): int
    {
        return str_word_count($this->description);
    }

    /**
     * Get character count
     */
    public function getCharCountAttribute(): int
    {
        return strlen($this->description);
    }

    /**
     * Get truncated description
     */
    public function getTruncatedDescription(int $length = 100): string
    {
        return strlen($this->description) > $length
            ? substr($this->description, 0, $length).'...'
            : $this->description;
    }

    /**
     * Scope for pending approval
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for approved reports
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_DISETUJUI);
    }

    /**
     * Scope for rejected reports
     */
    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_DITOLAK);
    }

    /**
     * Scope for specific date range
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('report_date', [$startDate, $endDate]);
    }
}
