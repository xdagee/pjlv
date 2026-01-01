<?php

namespace App\Models;

class LeaveType extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'leave_type_name',
        'leave_duration',
    ];

    public $timestamps = false;

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'leave_duration' => 'integer',
    ];

    /**
     * Get all staff who have used this leave type (via pivot).
     */
    public function staff()
    {
        return $this->belongsToMany(Staff::class, 'staff_leaves')
            ->withPivot('start_date', 'end_date', 'leave_days')
            ->withTimestamps();
    }

    /**
     * Get all leave requests for this type.
     */
    public function staffLeaves()
    {
        return $this->hasMany(StaffLeave::class);
    }
}
