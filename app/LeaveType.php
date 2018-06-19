<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    public function staff()
    {
    	return $this->belongsToMany(Staff::class)->using(StaffLeave::class);
    }
}
