<?php

namespace App\Models;

use App\Enums\RoleEnum;
use App\Enums\LeaveStatusEnum;
use Database\Factories\StaffFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Staff extends Model
{
    use HasFactory;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return StaffFactory::new();
    }
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'id',
        'staff_number',
        'title',
        'firstname',
        'lastname',
        'othername',
        'dob',
        'mobile_number',
        'gender',
        'picture',
        'is_active',
        'date_joined',
        'leave_level_id',
        'total_leave_days',
        'supervisor_id',
        'role_id',
        'department_id',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'dob' => 'date',
        'date_joined' => 'date',
        'is_active' => 'boolean',
        'gender' => 'boolean',
    ];

    /**
     * Get the user account for this staff member.
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id');
    }

    /**
     * Get the department that this staff belongs to.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get all leave types this staff has used (via pivot).
     */
    public function leaveTypes()
    {
        return $this->belongsToMany(LeaveType::class, 'staff_leaves')
            ->withPivot('start_date', 'end_date', 'leave_days')
            ->withTimestamps();
    }

    /**
     * Get all leave actions performed by this staff member.
     */
    public function leaveActions()
    {
        return $this->hasMany(LeaveAction::class, 'actionby');
    }

    /**
     * Get all leave requests for this staff member.
     */
    public function staffLeaves()
    {
        return $this->hasMany(StaffLeave::class);
    }

    /**
     * Get all jobs/positions held by this staff member.
     */
    public function jobs()
    {
        return $this->belongsToMany(Job::class, 'staff_jobs');
    }

    /**
     * Get the role of this staff member.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the leave level for this staff member.
     */
    public function leaveLevel()
    {
        return $this->belongsTo(LeaveLevel::class);
    }

    /**
     * Get the supervisor for this staff member.
     */
    public function supervisor()
    {
        return $this->belongsTo(Staff::class, 'supervisor_id');
    }

    /**
     * Get staff members supervised by this staff.
     */
    public function subordinates()
    {
        return $this->hasMany(Staff::class, 'supervisor_id');
    }

    // ========================================
    // Role Helper Methods
    // ========================================

    /**
     * Check if staff has a specific role.
     */
    public function hasRole(string|array|RoleEnum $roles): bool
    {
        if (!$this->role_id) {
            return false;
        }

        $roles = is_array($roles) ? $roles : [$roles];
        $roleIds = [];

        foreach ($roles as $role) {
            if ($role instanceof RoleEnum) {
                $roleIds[] = $role->value;
            } elseif (is_string($role)) {
                $enum = RoleEnum::fromName($role);
                if ($enum) {
                    $roleIds[] = $enum->value;
                }
            } elseif (is_int($role)) {
                $roleIds[] = $role;
            }
        }

        return in_array($this->role_id, $roleIds);
    }

    /**
     * Get the role enum for this staff member.
     */
    public function getRoleEnum(): ?RoleEnum
    {
        return RoleEnum::tryFrom($this->role_id);
    }

    /**
     * Check if staff can approve leaves.
     */
    public function canApproveLeaves(): bool
    {
        $roleEnum = $this->getRoleEnum();
        return $roleEnum ? $roleEnum->canApproveLeaves() : false;
    }

    /**
     * Check if staff can manage other staff.
     */
    public function canManageStaff(): bool
    {
        $roleEnum = $this->getRoleEnum();
        return $roleEnum ? $roleEnum->canManageStaff() : false;
    }

    /**
     * Check if staff can view reports.
     */
    public function canViewReports(): bool
    {
        $roleEnum = $this->getRoleEnum();
        return $roleEnum ? $roleEnum->canViewReports() : false;
    }

    /**
     * Get the full name of the staff member.
     */
    public function getFullNameAttribute(): string
    {
        return trim("{$this->firstname} {$this->lastname}");
    }

    // ========================================
    // Leave Action Methods
    // ========================================

    /**
     * Apply for a leave.
     */
    public function applyLeave(int $leaveTypeId, string $startDate, string $endDate, int $duration): StaffLeave
    {
        $leave = StaffLeave::create([
            'staff_id' => $this->id,
            'leave_type_id' => $leaveTypeId,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'leave_days' => $duration,
        ]);

        LeaveAction::create([
            'leave_id' => $leave->id,
            'actionby' => $this->id,
            'status_id' => LeaveStatusEnum::UNATTENDED->value,
        ]);

        return $leave;
    }

    /**
     * Approve a leave request.
     */
    public function approveLeave(int $staffLeaveId, ?string $reason = null): LeaveAction
    {
        return LeaveAction::create([
            'leave_id' => $staffLeaveId,
            'actionby' => $this->id,
            'status_id' => LeaveStatusEnum::APPROVED->value,
            'action_reason' => $reason,
        ]);
    }

    /**
     * Disapprove a leave request.
     */
    public function disapproveLeave(int $staffLeaveId, ?string $reason = null): LeaveAction
    {
        return LeaveAction::create([
            'leave_id' => $staffLeaveId,
            'actionby' => $this->id,
            'status_id' => LeaveStatusEnum::DISAPPROVED->value,
            'action_reason' => $reason,
        ]);
    }

    /**
     * Recommend a leave request.
     */
    public function recommendLeave(int $staffLeaveId, ?string $reason = null): LeaveAction
    {
        return LeaveAction::create([
            'leave_id' => $staffLeaveId,
            'actionby' => $this->id,
            'status_id' => LeaveStatusEnum::RECOMMENDED->value,
            'action_reason' => $reason,
        ]);
    }

    /**
     * Reject a leave request.
     */
    public function rejectLeave(int $staffLeaveId, ?string $reason = null): LeaveAction
    {
        return LeaveAction::create([
            'leave_id' => $staffLeaveId,
            'actionby' => $this->id,
            'status_id' => LeaveStatusEnum::REJECTED->value,
            'action_reason' => $reason,
        ]);
    }

    /**
     * Cancel a leave request.
     */
    public function cancelLeave(int $staffLeaveId): LeaveAction
    {
        return LeaveAction::create([
            'leave_id' => $staffLeaveId,
            'actionby' => $this->id,
            'status_id' => LeaveStatusEnum::CANCELLED->value,
        ]);
    }
}
