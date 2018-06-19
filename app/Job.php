<?php

namespace App;

class Job extends Model
{
	public function staffJob()
	{
		return $this->belongsToMany(Staff::class)->using(StaffJob::class);
	}
}
