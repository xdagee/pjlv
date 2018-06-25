<?php

namespace App;

class LeaveAction extends Model
{

    public $timestamps = false;

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