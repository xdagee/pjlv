<?php

namespace App\Services;

use App\Models\Staff;
use App\Models\StaffLeave;
use App\Models\Notification;
use App\Enums\RoleEnum;
use App\Mail\LeaveRequestSubmitted;
use App\Mail\LeaveRequestApproved;
use App\Mail\LeaveRequestRejected;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Notify Supervisor about a new leave request.
     */
    public function notifySupervisor(StaffLeave $leave): void
    {
        $applicant = $leave->staff;
        if (!$applicant)
            return;

        $supervisor = $applicant->supervisor;

        // If no supervisor, maybe notify HR directly? 
        // For now, only notify if supervisor exists and has a user account
        if ($supervisor && $supervisor->user) {
            $this->sendNotification(
                $supervisor->user->id,
                'New Leave Request',
                "{$applicant->fullname} has submitted a leave request.",
                'info',
                '/leaveactions/' . $leave->id // Link to review page
            );

            $this->sendEmail($supervisor->user->email, new LeaveRequestSubmitted($leave, $applicant));
        }
    }

    /**
     * Notify HR about a recommended leave request (or new request if no supervisor).
     */
    public function notifyHR(StaffLeave $leave, string $message = ''): void
    {
        // Find HR Staff (Role ID 2)
        // Note: Could be multiple HRs, keeping it simple to first found for now
        // Refactoring to get ALL HRs would be better but requires loop
        $descriptors = [RoleEnum::HR, RoleEnum::ADMIN]; // Notify Admin too? Strategy says HR.

        // Let's filter by RoleEnum::HR
        $hrStaffMembers = Staff::where('role_id', RoleEnum::HR->value)->where('is_active', 1)->get();

        foreach ($hrStaffMembers as $hr) {
            if ($hr->user) {
                $this->sendNotification(
                    $hr->user->id,
                    'Leave Request Pending Approval',
                    $message ?: "A leave request requires your approval.",
                    'warning',
                    '/leaveactions/' . $leave->id
                );

                // Assuming HR uses same email template 'LeaveRequestSubmitted' as it serves 'Review' purpose
                // Or create a specific 'LeaveRequestRecommended' email if needed.
                // Re-using Submitted for now.
                $this->sendEmail($hr->user->email, new LeaveRequestSubmitted($leave, $leave->staff));
            }
        }
    }

    /**
     * Notify Staff about the status of their request.
     */
    public function notifyStaff(StaffLeave $leave, string $status): void
    {
        $staff = $leave->staff;
        if (!$staff || !$staff->user)
            return;

        $title = "Leave Request Update";
        $message = "Your leave request has been {$status}.";
        $type = match (strtolower($status)) {
            'approved' => 'success',
            'rejected', 'disapproved', 'cancelled' => 'danger',
            'recommended' => 'info',
            default => 'info'
        };

        $this->sendNotification(
            $staff->user->id,
            $title,
            $message,
            $type,
            '/leaves' // Link to their list
        );

        // Send Email
        $statusKey = strtolower($status);
        $action = $leave->leaveAction()->latest('created_at')->first();

        Log::info("NotifyStaff Debug: Status={$statusKey}, LeaveID={$leave->id}, ActionID=" . ($action ? $action->id : 'null'));

        // Fallback to HR/Admin if no action recorded (should not happen in normal flow)
        $actionBy = $action && $action->actionby ? Staff::find($action->actionby) : Staff::where('role_id', RoleEnum::ADMIN->value)->first();

        Log::info("NotifyStaff Debug: ActionBy=" . ($actionBy ? $actionBy->id : 'null'));

        $reason = $action ? $action->action_reason : '';

        if ($statusKey === 'approved') {
            if ($actionBy) {
                try {
                    $this->sendEmail($staff->user->email, new LeaveRequestApproved($leave, $staff, $actionBy));
                    Log::info("NotifyStaff Debug: Email sent to {$staff->user->email}");
                } catch (\Exception $e) {
                    Log::error("NotifyStaff Debug: Email FAILED " . $e->getMessage());
                }
            } else {
                Log::info("NotifyStaff Debug: No ActionBy found, skipping email.");
            }
        } elseif (in_array($statusKey, ['rejected', 'disapproved'])) {
            if ($actionBy) {
                $this->sendEmail($staff->user->email, new LeaveRequestRejected($leave, $staff, $actionBy, $reason));
            }
        }
    }

    /**
     * Helper to create in-app notification.
     */
    protected function sendNotification(int $userId, string $title, string $message, string $type, string $link): void
    {
        Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'link' => $link
        ]);
    }

    /**
     * Helper to send email safely.
     */
    protected function sendEmail(string $email, $mailable): void
    {
        if (empty($email))
            return;

        try {
            Mail::to($email)->send($mailable);
        } catch (\Exception $e) {
            Log::error("Failed to send notification email to {$email}: " . $e->getMessage());
        }
    }

    /**
     * Notify supervisor and HR when a leave request is cancelled.
     */
    public function notifyLeaveCancelled(StaffLeave $leave): void
    {
        $applicant = $leave->staff;
        if (!$applicant) {
            return;
        }

        // Notify supervisor
        $supervisor = $applicant->supervisor;
        if ($supervisor && $supervisor->user) {
            $this->sendNotification(
                $supervisor->user->id,
                'Leave Request Cancelled',
                "{$applicant->fullname} has cancelled their leave request.",
                'warning',
                '/leaves/' . $leave->id
            );
        }

        // Notify HR staff
        $hrStaffMembers = Staff::where('role_id', RoleEnum::HR->value)
            ->where('is_active', 1)
            ->get();

        foreach ($hrStaffMembers as $hr) {
            // Skip if HR is the supervisor (already notified)
            if ($supervisor && $hr->id === $supervisor->id) {
                continue;
            }

            if ($hr->user) {
                $this->sendNotification(
                    $hr->user->id,
                    'Leave Request Cancelled',
                    "{$applicant->fullname} has cancelled their leave request.",
                    'warning',
                    '/leaves/' . $leave->id
                );
            }
        }
    }

    /**
     * Notify all admins and managers about a leave event.
     */
    public function notifyAdminsAndManagers(StaffLeave $leave, string $title, string $message): void
    {
        // Get all admin and HR staff
        $managers = Staff::whereIn('role_id', [RoleEnum::ADMIN->value, RoleEnum::HR->value])
            ->where('is_active', 1)
            ->get();

        foreach ($managers as $manager) {
            if ($manager->user) {
                $this->sendNotification(
                    $manager->user->id,
                    $title,
                    $message,
                    'info',
                    '/leaves/' . $leave->id
                );
            }
        }
    }
}
