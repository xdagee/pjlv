<?php

namespace App\Events;

use App\Models\StaffLeave;
use App\Models\Staff;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LeaveUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The leave request that was updated.
     */
    public StaffLeave $leave;

    /**
     * The staff member who updated the leave.
     */
    public Staff $staff;

    /**
     * The changes made to the leave request.
     */
    public array $changes;

    /**
     * Create a new event instance.
     */
    public function __construct(StaffLeave $leave, Staff $staff, array $changes = [])
    {
        $this->leave = $leave;
        $this->staff = $staff;
        $this->changes = $changes;
    }

    /**
     * Get a human-readable summary of changes.
     */
    public function getChangesSummary(): string
    {
        if (empty($this->changes)) {
            return 'Leave request was updated.';
        }

        $parts = [];
        foreach ($this->changes as $field => $change) {
            $fieldName = ucfirst(str_replace('_', ' ', $field));
            $parts[] = "{$fieldName}: {$change['old']} → {$change['new']}";
        }

        return implode(', ', $parts);
    }
}
