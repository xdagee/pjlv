<?php

namespace App;

class LeaveType extends Model
{
    public function staff()
    {
    	return $this->belongsToMany(Staff::class, 'staff_leaves');
    }
}
