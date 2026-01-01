<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'role_name',
        'role_description',
        'role_status',
    ];

    /**
     * Get all staff members with this role.
     */
    public function staff()
    {
        return $this->hasMany(Staff::class);
    }
}
