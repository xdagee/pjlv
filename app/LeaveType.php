<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    protected $fillable = ['leave_type_name', 'leave_duration'];

    public function staff()
    {
        return $this->belongsToMany(Staff::class, 'staff_leaves')->withPivot('start_date', 'end_date', 'leave_days')->withTimestamps();
    }

    public function staffLeaves()
    {
        return $this->hasMany(StaffLeave::class);
    }
}
