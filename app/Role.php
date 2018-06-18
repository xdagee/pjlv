<?php

namespace App;

class Role extends Model
{
<<<<<<< HEAD
    public function staffs()
    {
    	return $this->hasMany(Staff::class);
=======
    //
    public function users()
    {
      return $this->belongsToMany(User::class);
>>>>>>> c380fb589c0ec27f25f4962c2c90243f5fef6602
    }
}
