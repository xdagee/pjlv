<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\LeaveStatusEnum;

class LeaveAudit extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'leave_id',
        'actor_id',
        'action',
        'old_status_id',
        'new_status_id',
        'changes',
        'ip_address',
        'user_agent',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'changes' => 'array',
    ];

    /**
     * Get the leave request this audit belongs to.
     */
    public function leave()
    {
        return $this->belongsTo(StaffLeave::class, 'leave_id');
    }

    /**
     * Get the staff member who performed the action.
     */
    public function actor()
    {
        return $this->belongsTo(Staff::class, 'actor_id');
    }

    /**
     * Get the old status enum.
     */
    public function getOldStatusEnum(): ?LeaveStatusEnum
    {
        return $this->old_status_id ? LeaveStatusEnum::tryFrom($this->old_status_id) : null;
    }

    /**
     * Get the new status enum.
     */
    public function getNewStatusEnum(): ?LeaveStatusEnum
    {
        return $this->new_status_id ? LeaveStatusEnum::tryFrom($this->new_status_id) : null;
    }

    /**
     * Scope for audits by action type.
     */
    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope for audits by leave.
     */
    public function scopeForLeave($query, int $leaveId)
    {
        return $query->where('leave_id', $leaveId);
    }

    /**
     * Scope for audits by actor.
     */
    public function scopeByActor($query, int $actorId)
    {
        return $query->where('actor_id', $actorId);
    }
}
