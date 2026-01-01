<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'date',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Scope for upcoming holidays.
     */
    public function scopeUpcoming($query, int $days = 30)
    {
        return $query->where('date', '>=', now())
            ->where('date', '<=', now()->addDays($days))
            ->orderBy('date');
    }

    /**
     * Scope for holidays in a date range.
     */
    public function scopeInRange($query, string $start, string $end)
    {
        return $query->whereBetween('date', [$start, $end]);
    }
}
