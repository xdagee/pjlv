<?php

namespace App;


use Illuminate\Database\Eloquent\Relations\Pivot;


class StaffLeave extends Pivot
{

    public function leaveAction()
    {
        return $this->hasMany(LeaveAction::class, 'leave_id');
    }
}