<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveLevel extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'level_name',
        'annual_leave_days',
    ];

    /**
     * Get all staff at this leave level.
     */
    public function staff()
    {
        return $this->hasMany(Staff::class);
    }
}
