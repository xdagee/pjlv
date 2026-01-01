<?php

namespace App\Observers;

use App\Models\Staff;
use App\Models\Role;

class StaffObserver
{
    /**
     * Handle the Staff "created" event.
     */
    public function created(Staff $staff): void
    {
        $this->updateRoleStatus($staff->role_id);
    }

    /**
     * Handle the Staff "updated" event.
     */
    public function updated(Staff $staff): void
    {
        // Check if role_id changed
        if ($staff->isDirty('role_id')) {
            $oldRoleId = $staff->getOriginal('role_id');
            $newRoleId = $staff->role_id;

            $this->updateRoleStatus($oldRoleId);
            $this->updateRoleStatus($newRoleId);
        }
    }

    /**
     * Handle the Staff "deleted" event.
     */
    public function deleted(Staff $staff): void
    {
        $this->updateRoleStatus($staff->role_id);
    }

    /**
     * Update role status based on assignment count.
     *
     * @param int|null $roleId
     * @return void
     */
    protected function updateRoleStatus(?int $roleId): void
    {
        if (!$roleId) {
            return;
        }

        // Do not touch Super Admin (ID 1)
        if ($roleId == 1) {
            return;
        }

        $count = Staff::where('role_id', $roleId)->count();
        $role = Role::find($roleId);

        if ($role) {
            // Rule: Active only if assigned to at least one Staff
            $newStatus = $count > 0 ? 1 : 0;

            if ($role->role_status != $newStatus) {
                // Use quiet update to avoid potential loops if Role also has observers (not currently, but good practice)
                $role->role_status = $newStatus;
                $role->saveQuietly();
            }
        }
    }
}
