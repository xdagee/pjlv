<?php

namespace App\Listeners;

use App\Events\LeaveUpdated;
use App\Services\AuditService;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;

class HandleLeaveUpdated
{
    /**
     * Create the event listener.
     */
    public function __construct(
        protected AuditService $auditService
    ) {
    }

    /**
     * Handle the event.
     */
    public function handle(LeaveUpdated $event): void
    {
        // Log the update to audit trail
        $this->logAudit($event);

        // Notify supervisor about the update
        $this->notifySupervisor($event);
    }

    /**
     * Log audit entry for the update.
     */
    protected function logAudit(LeaveUpdated $event): void
    {
        try {
            $this->auditService->logLeaveUpdated(
                $event->leave,
                $event->staff->id,
                $event->getChangesSummary()
            );
        } catch (\Exception $e) {
            // AuditService may not have this method yet, log error but continue
            Log::warning("LeaveUpdated audit logging failed: " . $e->getMessage());
        }
    }

    /**
     * Notify supervisor about the leave update.
     */
    protected function notifySupervisor(LeaveUpdated $event): void
    {
        $staff = $event->staff;
        $leave = $event->leave;

        // Notify direct supervisor if exists
        if ($staff->supervisor_id && $staff->supervisor?->user) {
            Notification::notify(
                $staff->supervisor->user->id,
                'Leave Request Updated',
                "{$staff->full_name} has updated their leave request. {$event->getChangesSummary()}",
                'info',
                "/leaves/{$leave->id}"
            );
        }

        // Also notify HR for visibility (role-based delivery)
        $this->notifyHR($event);
    }

    /**
     * Notify HR about the leave update.
     */
    protected function notifyHR(LeaveUpdated $event): void
    {
        $leave = $event->leave;
        $staff = $event->staff;

        // Find HR staff members
        $hrStaff = \App\Models\Staff::where('role_id', \App\Enums\RoleEnum::HR->value)
            ->where('is_active', true)
            ->get();

        foreach ($hrStaff as $hr) {
            // Skip if this is the same person as the applicant's supervisor
            if ($staff->supervisor_id === $hr->id) {
                continue;
            }

            if ($hr->user) {
                Notification::notify(
                    $hr->user->id,
                    'Leave Request Updated',
                    "{$staff->full_name} has updated their leave request.",
                    'info',
                    "/leaves/{$leave->id}"
                );
            }
        }
    }
}
