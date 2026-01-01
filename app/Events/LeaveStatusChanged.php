<?php

namespace App\Events;

use App\Models\StaffLeave;
use App\Models\Staff;
use App\Enums\LeaveStatusEnum;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LeaveStatusChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The leave request.
     */
    public StaffLeave $leave;

    /**
     * The staff member who changed the status.
     */
    public Staff $actor;

    /**
     * The new status.
     */
    public LeaveStatusEnum $newStatus;

    /**
     * The old status (if any).
     */
    public ?LeaveStatusEnum $oldStatus;

    /**
     * Optional reason for the status change.
     */
    public ?string $reason;

    /**
     * Create a new event instance.
     */
    public function __construct(
        StaffLeave $leave,
        Staff $actor,
        LeaveStatusEnum $newStatus,
        ?LeaveStatusEnum $oldStatus = null,
        ?string $reason = null
    ) {
        $this->leave = $leave;
        $this->actor = $actor;
        $this->newStatus = $newStatus;
        $this->oldStatus = $oldStatus;
        $this->reason = $reason;
    }

    /**
     * Check if this is an approval.
     */
    public function isApproval(): bool
    {
        return $this->newStatus === LeaveStatusEnum::APPROVED;
    }

    /**
     * Check if this is a rejection.
     */
    public function isRejection(): bool
    {
        return $this->newStatus === LeaveStatusEnum::REJECTED
            || $this->newStatus === LeaveStatusEnum::DISAPPROVED;
    }

    /**
     * Check if this is a cancellation.
     */
    public function isCancellation(): bool
    {
        return $this->newStatus === LeaveStatusEnum::CANCELLED;
    }
}
