<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Staff;
use App\Models\StaffLeave;
use App\Enums\RoleEnum;
use Illuminate\Auth\Access\HandlesAuthorization;

class StaffLeavePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any leave requests.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view leave requests
        return true;
    }

    /**
     * Determine whether the user can view the leave request.
     */
    public function view(User $user, StaffLeave $leave): bool
    {
        $staff = $user->staff;

        if (!$staff) {
            return false;
        }

        // Owner can always view their own requests
        if ($leave->staff_id === $staff->id) {
            return true;
        }

        // Approvers can view any request
        return $staff->canApproveLeaves();
    }

    /**
     * Determine whether the user can create leave requests.
     */
    public function create(User $user): bool
    {
        // All staff can apply for leave
        return $user->staff !== null;
    }

    /**
     * Determine whether the user can update/approve the leave request.
     */
    public function update(User $user, StaffLeave $leave): bool
    {
        $staff = $user->staff;

        if (!$staff) {
            return false;
        }

        // Only approvers can update/approve leaves
        return $staff->canApproveLeaves();
    }

    /**
     * Determine whether the user can approve the leave request.
     */
    public function approve(User $user, StaffLeave $leave): bool
    {
        $staff = $user->staff;

        if (!$staff) {
            return false;
        }

        // Cannot approve own request
        if ($leave->staff_id === $staff->id) {
            return false;
        }

        // Must have approval permissions
        if (!$staff->canApproveLeaves()) {
            return false;
        }

        // Leave must still be pending
        return $leave->isPending();
    }

    /**
     * Determine whether the user can cancel the leave request.
     */
    public function cancel(User $user, StaffLeave $leave): bool
    {
        $staff = $user->staff;

        if (!$staff) {
            return false;
        }

        // Only owner can cancel their own request
        if ($leave->staff_id !== $staff->id) {
            return false;
        }

        // Can only cancel if not yet finalized
        return $leave->canBeCancelled();
    }

    /**
     * Determine whether the user can delete the leave request.
     */
    public function delete(User $user, StaffLeave $leave): bool
    {
        $staff = $user->staff;

        if (!$staff) {
            return false;
        }

        // Admin and HR can delete any request
        if ($staff->hasRole([RoleEnum::ADMIN, RoleEnum::HR])) {
            return true;
        }

        // Owner can delete their own pending requests
        if ($leave->staff_id === $staff->id && $leave->isPending()) {
            return true;
        }

        return false;
    }
}
