<?php

namespace App\Events;

use App\Models\StaffLeave;
use App\Models\Staff;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LeaveSubmitted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The leave request that was submitted.
     */
    public StaffLeave $leave;

    /**
     * The staff member who submitted the leave.
     */
    public Staff $staff;

    /**
     * Create a new event instance.
     */
    public function __construct(StaffLeave $leave, Staff $staff)
    {
        $this->leave = $leave;
        $this->staff = $staff;
    }
}
