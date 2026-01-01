<?php

namespace App\Models;

use App\Enums\LeaveStatusEnum;

class LeaveAction extends Model
{
    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'leave_id',
        'actionby',
        'status_id',
        'action_reason',
    ];

    /**
     * Get the leave request this action belongs to.
     */
    public function staffLeave()
    {
        return $this->belongsTo(StaffLeave::class, 'leave_id');
    }

    /**
     * Alias for staffLeave().
     */
    public function leave()
    {
        return $this->staffLeave();
    }

    /**
     * Get the status for this action.
     */
    public function leaveStatus()
    {
        return $this->belongsTo(LeaveStatus::class, 'status_id');
    }

    /**
     * Get the staff member who performed this action.
     */
    public function actionBy()
    {
        return $this->belongsTo(Staff::class, 'actionby');
    }

    /**
     * Alias for actionBy() for consistency.
     */
    public function staff()
    {
        return $this->actionBy();
    }

    /**
     * Get the status enum for this action.
     */
    public function getStatusEnum(): ?LeaveStatusEnum
    {
        return LeaveStatusEnum::tryFrom($this->status_id);
    }

    /**
     * Check if this action represents approval.
     */
    public function isApproval(): bool
    {
        return $this->status_id === LeaveStatusEnum::APPROVED->value;
    }

    /**
     * Check if this action represents rejection.
     */
    public function isRejection(): bool
    {
        $status = $this->getStatusEnum();
        return $status ? $status->isRejected() : false;
    }
}
