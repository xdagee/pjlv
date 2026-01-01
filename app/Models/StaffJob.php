<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class StaffJob extends Pivot
{
    /**
     * The table associated with the model.
     */
    protected $table = 'staff_jobs';

    /**
     * Indicates if the IDs are auto-incrementing.
     */
    public $incrementing = true;
}
