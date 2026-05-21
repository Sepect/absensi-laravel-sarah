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
        'approved_by',
    ];

    protected $casts = [
        'report_date' => 'date',
        'is_approved' => 'boolean',
    ];

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
        return $this->is_approved;
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
            ? substr($this->description, 0, $length) . '...'
            : $this->description;
    }

    /**
     * Scope for pending approval
     */
    public function scopePending($query)
    {
        return $query->where('is_approved', false);
    }

    /**
     * Scope for approved reports
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope for specific date range
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('report_date', [$startDate, $endDate]);
    }
}