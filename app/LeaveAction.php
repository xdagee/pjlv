<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeaveAction extends Model
{
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
}
