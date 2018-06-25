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

    /*
    *Function to apply for a leave
    *@param leaveType   -> name of the leave type applied for
    *@param start_date  -> start date of the applied leave
    *@param end_date	-> end date of the applied leave
    *@param duration	-> duration of the applied leave
    */
	public function applyLeave($leaveType, $start_date, $end_date, $duration){
		$leaveTypeId = LeaveType::select('id')->where('leave_type_name', '=', $leaveType)->get();
		
		static::leaveTypes()->attach($leaveTypeId,[
			'start_date'	=>	$start_date,
			'end_date'		=>	$end_date,
			'leave_days'	=>	$duration]
		);
		

		//get the staff leave id
		$staffLeaveId = StaffLeave::select('id')->latest()->first();
		$staffLeaveId = $staffLeaveId['id'];
		
		$this->leaveAction()->create(['leave_id' => $staffLeaveId]);
	}

	/*
    *Function to approve a leave
    *@param staffLeaveId   -> id of the staff leave to approve
    */
	public function approveLeave($staffLeaveId){
		$this->leaveAction()->create([
			'leave_id' => $staffLeaveId,
			'status_id'	=> 2
		]);	
	}

	/*
    *Function to disapprove a leave
    *@param staffLeaveId   -> id of the staff leave to disapprove
    */
	public function disapproveLeave($staffLeaveId){
		$this->leaveAction()->create([
			'leave_id' => $staffLeaveId,
			'status_id'	=> 3
		]);	
	}

	/*
    *Function to recommend a leave
    *@param staffLeaveId   -> id of the staff leave to recommend
    */
	public function recommendLeave($staffLeaveId){
		$this->leaveAction()->create([
			'leave_id' => $staffLeaveId,
			'status_id'	=> 4
		]);	
	}

	/*
    *Function to reject a leave
    *@param staffLeaveId   -> id of the staff leave to reject
    */
	public function rejectLeave($staffLeaveId){
		$this->leaveAction()->create([
			'leave_id' => $staffLeaveId,
			'status_id'	=> 5
		]);	
	}

	/*
    *Function to cancel a leave
    *@param staffLeaveId   -> id of the staff leave to cancel
    */
	public function cancelLeave($staffLeaveId){
		$this->leaveAction()->create([
			'leave_id' => $staffLeaveId,
			'status_id'	=> 6
		]);	
	}
    
}

