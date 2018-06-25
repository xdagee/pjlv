<?php

namespace App;

class Job extends Model
{
	public function staff()
	{
		return $this->belongsToMany(Staff::class, 'staff_jobs');
	}
}
