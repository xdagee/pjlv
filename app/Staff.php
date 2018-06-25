<?php

namespace App;

class Staff extends Model
{

	public function user(){
		return $this->hasOne(User::class,'id');
	}

	public function leaveTypes()
	{
		return $this->belongsToMany(LeaveType::class, 'staff_leaves')->withPivot('start_date','end_date','leave_days')->withTimestamps();
	}

	public function leaveAction()
	{
		return $this->hasMany(LeaveAction::class, 'actionby');
	}

	public function jobs()
	{
		return $this->belongsToMany(Job::class, 'staff_jobs');
	}

    public function role()
    {
    	return $this->belongsTo(Role::class);
    }

    public function leaveLevel(){
    	return $this->belongsTo(LeaveLevel::class);
    }

    
}

