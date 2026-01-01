<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Base Model class with global configuration.
 */
class Model extends Eloquent
{
    /**
     * Guard only the primary key by default.
     * Models should define their own $fillable arrays.
     */
    protected $guarded = ['id'];
}
