<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    /**
     * Role constants
     */
    const ROLE_ADMIN = 'admin';
    const ROLE_INTERN = 'intern';

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Check if user is intern
     */
    public function isIntern(): bool
    {
        return $this->role === self::ROLE_INTERN;
    }

    /**
     * Get admin profile
     */
    public function adminProfile(): HasOne
    {
        return $this->hasOne(AdminProfile::class);
    }

    /**
     * Get intern profile
     */
    public function internProfile(): HasOne
    {
        return $this->hasOne(InternProfile::class);
    }

    /**
     * Get the full name based on role
     */
    public function getFullNameAttribute(): string
    {
        if ($this->isAdmin()) {
            return $this->adminProfile?->nama ?? 'Admin';
        }
        return $this->internProfile?->nama_lengkap ?? 'Peserta';
    }

    /**
     * Get initials for avatar
     */
    public function getInitialsAttribute(): string
    {
        $name = $this->full_name;
        $words = explode(' ', $name);
        $initials = array_map(fn($word) => $word[0] ?? '', $words);
        return strtoupper(implode('', array_slice($initials, 0, 2)));
    }
}