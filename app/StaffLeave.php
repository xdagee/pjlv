<?php

namespace App;

<<<<<<< HEAD
use Illuminate\Database\Eloquent\Relations\Pivot;

class StaffLeave extends Pivot
{

    public function leaveAction()
    {
        return $this->hasMany(LeaveAction::class, 'leave_id');
    }

    // public function leaveStatus()
    // {
    // 	return $this->belongsTo(LeaveStatus::class);
    // }
=======
class StaffLeave extends Model
{
    //
>>>>>>> c380fb589c0ec27f25f4962c2c90243f5fef6602
}
