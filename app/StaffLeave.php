<?php

namespace App;


use Illuminate\Database\Eloquent\Relations\Pivot;


class StaffLeave extends Pivot
{

    public function leaveAction()
    {
        return $this->hasMany(LeaveAction::class, 'leave_id');
    }

    public static function getAll(){
    	$staff = Staff::all();
    	$leaves = array();
    	foreach ($staff as $s) {
    		array_push($leaves,$s->leaveTypes);
    	}
    	return $leaves;
    }
}