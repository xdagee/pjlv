<?php

namespace App\Listeners;

use App\Events\LeaveSubmitted;
use App\Services\AuditService;
use App\Models\Notification;
use App\Mail\LeaveRequestSubmitted;
use Illuminate\Support\Facades\Mail;

class HandleLeaveSubmitted
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
    public function handle(LeaveSubmitted $event): void
    {
        // Log the submission
        $this->auditService->logLeaveSubmitted(
            $event->leave,
            $event->staff->id
        );

        // Create in-app notification for supervisors
        $this->notifySupervisors($event);

        // Send email notification (already queued)
        $this->sendEmailNotification($event);
    }

    /**
     * Notify supervisors about the leave request.
     */
    protected function notifySupervisors(LeaveSubmitted $event): void
    {
        $staff = $event->staff;
        $leave = $event->leave;

        // Notify direct supervisor if exists
        if ($staff->supervisor_id && $staff->supervisor?->user) {
            Notification::notify(
                $staff->supervisor->user->id,
                'New Leave Request',
                "{$staff->full_name} has submitted a leave request for {$leave->leave_days} days.",
                'info',
                "/leaves/{$leave->id}"
            );
        }
    }

    /**
     * Send email notification.
     */
    protected function sendEmailNotification(LeaveSubmitted $event): void
    {
        $staff = $event->staff;
        $leave = $event->leave;

        // Notify supervisor via email
        if ($staff->supervisor?->user?->email) {
            Mail::to($staff->supervisor->user->email)
                ->send(new LeaveRequestSubmitted($leave, $staff));
        }
    }
}
