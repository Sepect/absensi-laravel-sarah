<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama',
        'no_telp',
    ];

    /**
     * Get the user that owns this profile
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get initials for avatar display
     */
    public function getInitialsAttribute(): string
    {
        $words = explode(' ', $this->nama);
        $initials = array_map(fn($word) => $word[0] ?? '', $words);
        return strtoupper(implode('', array_slice($initials, 0, 2)));
    }
}