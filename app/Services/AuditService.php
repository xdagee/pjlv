<?php

namespace App\Services;

use App\Models\LeaveAudit;
use App\Models\StaffLeave;
use App\Enums\LeaveStatusEnum;
use Illuminate\Http\Request;

class AuditService
{
    /**
     * Log a leave submission.
     */
    public function logLeaveSubmitted(StaffLeave $leave, int $actorId, ?Request $request = null): LeaveAudit
    {
        return $this->log(
            $leave,
            $actorId,
            'submitted',
            null,
            LeaveStatusEnum::UNATTENDED->value,
            null,
            $request
        );
    }

    /**
     * Log a leave approval.
     */
    public function logLeaveApproved(StaffLeave $leave, int $actorId, ?int $oldStatusId = null, ?Request $request = null): LeaveAudit
    {
        return $this->log(
            $leave,
            $actorId,
            'approved',
            $oldStatusId,
            LeaveStatusEnum::APPROVED->value,
            null,
            $request
        );
    }

    /**
     * Log a leave rejection.
     */
    public function logLeaveRejected(StaffLeave $leave, int $actorId, ?string $reason = null, ?int $oldStatusId = null, ?Request $request = null): LeaveAudit
    {
        return $this->log(
            $leave,
            $actorId,
            'rejected',
            $oldStatusId,
            LeaveStatusEnum::REJECTED->value,
            $reason,
            $request
        );
    }

    /**
     * Log a leave recommendation.
     */
    public function logLeaveRecommended(StaffLeave $leave, int $actorId, ?int $oldStatusId = null, ?Request $request = null): LeaveAudit
    {
        return $this->log(
            $leave,
            $actorId,
            'recommended',
            $oldStatusId,
            LeaveStatusEnum::RECOMMENDED->value,
            null,
            $request
        );
    }

    /**
     * Log a leave cancellation.
     */
    public function logLeaveCancelled(StaffLeave $leave, int $actorId, ?int $oldStatusId = null, ?Request $request = null): LeaveAudit
    {
        return $this->log(
            $leave,
            $actorId,
            'cancelled',
            $oldStatusId,
            LeaveStatusEnum::CANCELLED->value,
            null,
            $request
        );
    }

    /**
     * Log a leave update/modification.
     */
    public function logLeaveUpdated(StaffLeave $leave, int $actorId, ?string $changesSummary = null, ?Request $request = null): LeaveAudit
    {
        return $this->log(
            $leave,
            $actorId,
            'updated',
            null,
            null,
            $changesSummary,
            $request
        );
    }

    /**
     * Log a leave view action.
     */
    public function logLeaveViewed(StaffLeave $leave, int $actorId, ?Request $request = null): LeaveAudit
    {
        return $this->log(
            $leave,
            $actorId,
            'viewed',
            null,
            null,
            null,
            $request
        );
    }

    /**
     * Generic log method.
     */
    protected function log(
        StaffLeave $leave,
        int $actorId,
        string $action,
        ?int $oldStatusId = null,
        ?int $newStatusId = null,
        ?string $notes = null,
        ?Request $request = null,
        ?array $changes = null
    ): LeaveAudit {
        return LeaveAudit::create([
            'leave_id' => $leave->id,
            'actor_id' => $actorId,
            'action' => $action,
            'old_status_id' => $oldStatusId,
            'new_status_id' => $newStatusId,
            'changes' => $changes,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'notes' => $notes,
        ]);
    }

    /**
     * Get audit trail for a leave request.
     */
    public function getAuditTrail(int $leaveId): \Illuminate\Database\Eloquent\Collection
    {
        return LeaveAudit::forLeave($leaveId)
            ->with('actor')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get recent audits by actor.
     */
    public function getActorActivity(int $actorId, int $limit = 20): \Illuminate\Database\Eloquent\Collection
    {
        return LeaveAudit::byActor($actorId)
            ->with('leave')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
