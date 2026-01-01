<?php

namespace App\Listeners;

use App\Events\LeaveStatusChanged;
use App\Services\AuditService;
use App\Services\LeaveBalanceService;
use App\Models\Notification;
use App\Mail\LeaveRequestApproved;
use App\Mail\LeaveRequestRejected;
use Illuminate\Support\Facades\Mail;

class HandleLeaveStatusChanged
{
    /**
     * Create the event listener.
     */
    public function __construct(
        protected AuditService $auditService,
        protected LeaveBalanceService $leaveBalanceService
    ) {
    }

    /**
     * Handle the event.
     */
    public function handle(LeaveStatusChanged $event): void
    {
        // Log the status change to audit trail
        $this->logAudit($event);

        // Clear cached balance if approved/cancelled
        if ($event->isApproval() || $event->isCancellation()) {
            $this->leaveBalanceService->clearCache($event->leave->staff_id);
        }

        // Create in-app notification
        $this->createNotification($event);

        // Send email notification
        $this->sendEmail($event);
    }

    /**
     * Log audit entry for the status change.
     */
    protected function logAudit(LeaveStatusChanged $event): void
    {
        $oldStatusId = $event->oldStatus?->value;
        $newStatusId = $event->newStatus->value;

        if ($event->isApproval()) {
            $this->auditService->logLeaveApproved(
                $event->leave,
                $event->actor->id,
                $oldStatusId
            );
        } elseif ($event->isRejection()) {
            $this->auditService->logLeaveRejected(
                $event->leave,
                $event->actor->id,
                $event->reason,
                $oldStatusId
            );
        } elseif ($event->isCancellation()) {
            $this->auditService->logLeaveCancelled(
                $event->leave,
                $event->actor->id,
                $oldStatusId
            );
        }
    }

    /**
     * Create in-app notification for the applicant.
     */
    protected function createNotification(LeaveStatusChanged $event): void
    {
        $leave = $event->leave;
        $applicant = $leave->staff;

        if (!$applicant?->user) {
            return;
        }

        $statusLabel = $event->newStatus->label();
        $message = "Your leave request has been {$statusLabel}.";

        if ($event->reason) {
            $message .= " Reason: {$event->reason}";
        }

        Notification::notify(
            $applicant->user->id,
            "Leave Request {$statusLabel}",
            $message,
            $event->isApproval() ? 'success' : ($event->isRejection() ? 'warning' : 'info'),
            "/leaves/{$leave->id}"
        );
    }

    /**
     * Send email notification.
     */
    protected function sendEmail(LeaveStatusChanged $event): void
    {
        $leave = $event->leave;
        $applicant = $leave->staff;

        if (!$applicant?->user?->email) {
            return;
        }

        if ($event->isApproval()) {
            Mail::to($applicant->user->email)
                ->send(new LeaveRequestApproved($leave, $applicant, $event->actor));
        } elseif ($event->isRejection()) {
            Mail::to($applicant->user->email)
                ->send(new LeaveRequestRejected($leave, $applicant, $event->actor, $event->reason ?? ''));
        }
    }
}
