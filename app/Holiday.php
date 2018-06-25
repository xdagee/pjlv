<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    public static function holidays(){
    	return Holiday::select('date')->get();
    }
}
