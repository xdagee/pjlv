<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'job_title',
        'job_description',
        'is_multiple_staff',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_multiple_staff' => 'boolean',
    ];

    /**
     * Get all staff holding this job/position.
     */
    public function staff()
    {
        return $this->belongsToMany(Staff::class, 'staff_jobs');
    }
}
