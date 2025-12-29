<?php

namespace App;


class StaffLeave extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'start_date',
        'end_date',
        'leave_days',
        'leave_type_id',
        'staff_id',
    ];

    /**
     * Get the staff member who requested this leave.
     */
    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    /**
     * Get the leave type for this leave request.
     */
    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    /**
     * Get the actions taken on this leave request.
     */
    public function leaveAction()
    {
        return $this->hasMany(LeaveAction::class, 'leave_id');
    }
}