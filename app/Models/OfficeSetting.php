<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OfficeSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_instansi',
        'latitude',
        'longitude',
        'radius_meters',
        'waktu_masuk',
        'waktu_pulang',
        'qr_expiry_minutes',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'radius_meters' => 'integer',
        'qr_expiry_minutes' => 'integer',
    ];

    /**
     * Get the default office settings (singleton)
     */
    public static function getActive(): self
    {
        return static::firstOrCreate(
            ['id' => 1],
            [
                'nama_instansi' => 'Kantor Pusat',
                'latitude' => -6.2088,
                'longitude' => 106.8456,
                'radius_meters' => 100,
                'waktu_masuk' => '08:00:00',
                'waktu_pulang' => '17:00:00',
                'qr_expiry_minutes' => 5,
            ]
        );
    }

    /**
     * Get formatted coordinates
     */
    public function getFormattedCoordinatesAttribute(): string
    {
        return "{$this->latitude}, {$this->longitude}";
    }

    /**
     * Get formatted schedule
     */
    public function getFormattedScheduleAttribute(): string
    {
        return "{$this->waktu_masuk} - {$this->waktu_pulang}";
    }

    /**
     * Check if current time is within office hours
     */
    public function isWithinOfficeHours(): bool
    {
        $now = now()->format('H:i:s');
        return $now >= $this->waktu_masuk && $now <= $this->waktu_pulang;
    }

    /**
     * Get attendance records using these settings as reference
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}