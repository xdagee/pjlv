<?php

namespace App;

class LeaveType extends Model
{
    public function staff()
    {
    	return $this->belongsToMany(Staff::class, 'staff_leaves')->withPivot('start_date','end_date','leave_days')->withTimestamps();
    }
}
