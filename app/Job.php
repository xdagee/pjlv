<?php

namespace App;

class Job extends Model
{
	public function staffJob()
	{
		return $this->belongsToMany(Staff::class, 'staff_jobs');
	}
}
