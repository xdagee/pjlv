<?php

namespace App;

class Role extends Model
{
    public function staff()
    {
    	return $this->hasMany(Staff::class);
	}
	
    // public function users()
    // {
    //   return $this->belongsToMany(User::class);
    // }
}
