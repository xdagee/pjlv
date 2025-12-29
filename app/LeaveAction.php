<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeaveAction extends Model
{
    public $timestamps = false;

    protected $fillable = ['leave_id', 'actionby', 'status_id', 'action_reason'];

    public function staffLeave()
    {
        return $this->belongsTo(StaffLeave::class, 'leave_id');
    }

    public function leaveStatus()
    {
        return $this->belongsTo(LeaveStatus::class, 'status_id');
    }

    public function actionBy()
    {
        return $this->belongsTo(Staff::class, 'actionby');
    }

    // Alias for consistency
    public function staff()
    {
        return $this->belongsTo(Staff::class, 'actionby');
    }
}