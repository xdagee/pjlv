<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeaveStatus extends Model
{
    public function staffLeaves()
    {
    	return $this->hasMany(StaffLeave::class);
    }

    public function leaveAction()
    {
    	return $this->hasMany(LeaveAction::class, 'status_id');
    }
}
