<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveStatus extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'status_name',
    ];

    /**
     * Get all leave requests with this status.
     */
    public function staffLeaves()
    {
        return $this->hasMany(StaffLeave::class);
    }

    /**
     * Get all leave actions with this status.
     */
    public function leaveActions()
    {
        return $this->hasMany(LeaveAction::class, 'status_id');
    }
}
