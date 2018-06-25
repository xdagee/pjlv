<?php

namespace App;


class StaffLeave extends Model
{

    public function leaveAction()
    {
        return $this->hasMany(LeaveAction::class, 'leave_id');
    }
}