<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IzinRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'intern_id',
        'jenis',
        'tanggal_mulai',
        'tanggal_selesai',
        'alasan',
        'bukti',
        'status',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'reviewed_at' => 'datetime',
    ];

    /**
     * Status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_DISETUJUI = 'disetujui';
    const STATUS_DITOLAK = 'ditolak';

    /**
     * Type constants
     */
    const JENIS_SAKIT = 'sakit';
    const JENIS_CUTI = 'cuti';
    const JENIS_URGENT = 'urgent';

    /**
     * Get the intern profile who made this request
     */
    public function internProfile(): BelongsTo
    {
        return $this->belongsTo(InternProfile::class, 'intern_id');
    }

    /**
     * Get the admin profile who reviewed this request
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(AdminProfile::class, 'reviewed_by');
    }

    /**
     * Check if request is pending
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if request is approved
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_DISETUJUI;
    }

    /**
     * Check if request is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_DITOLAK;
    }

    /**
     * Get duration in days
     */
    public function getDurationDaysAttribute(): int
    {
        return $this->tanggal_mulai->diffInDays($this->tanggal_selesai) + 1;
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            self::STATUS_DISETUJUI => 'badge-present',
            self::STATUS_DITOLAK => 'badge-absent',
            default => 'badge-late',
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Menunggu',
            self::STATUS_DISETUJUI => 'Disetujui',
            self::STATUS_DITOLAK => 'Ditolak',
            default => 'Unknown',
        };
    }

    /**
     * Get jenis label
     */
    public function getJenisLabelAttribute(): string
    {
        return match($this->jenis) {
            self::JENIS_SAKIT => 'Sakit',
            self::JENIS_CUTI => 'Cuti',
            self::JENIS_URGENT => 'Urgent',
            default => 'Unknown',
        };
    }

    /**
     * Scope for pending requests
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for approved requests
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_DISETUJUI);
    }
}