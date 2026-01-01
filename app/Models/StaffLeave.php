<?php

namespace App\Models;

use App\Enums\LeaveStatusEnum;
use Database\Factories\StaffLeaveFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StaffLeave extends Model
{
    use HasFactory;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return StaffLeaveFactory::new();
    }

    /**
     * The table associated with the model.
     */
    protected $table = 'staff_leaves';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'start_date',
        'end_date',
        'leave_days',
        'leave_type_id',
        'staff_id',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'leave_days' => 'integer',
    ];

    /**
     * Get the staff member who requested this leave.
     */
    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    /**
     * Get the leave type for this leave request.
     */
    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    /**
     * Get all actions taken on this leave request.
     */
    public function leaveAction()
    {
        return $this->hasMany(LeaveAction::class, 'leave_id');
    }

    /**
     * Alias for leaveAction() for clarity.
     */
    public function actions()
    {
        return $this->leaveAction();
    }

    /**
     * Get the latest/current status of this leave.
     */
    public function latestAction()
    {
        return $this->hasOne(LeaveAction::class, 'leave_id')->latestOfMany();
    }

    /**
     * Get the current status enum.
     */
    public function getCurrentStatus(): ?LeaveStatusEnum
    {
        $latestAction = $this->latestAction;
        if (!$latestAction) {
            return LeaveStatusEnum::UNATTENDED;
        }
        return LeaveStatusEnum::tryFrom($latestAction->status_id);
    }

    /**
     * Check if leave is approved.
     */
    public function isApproved(): bool
    {
        return $this->getCurrentStatus() === LeaveStatusEnum::APPROVED;
    }

    /**
     * Check if leave is pending (not yet finalized).
     */
    public function isPending(): bool
    {
        $status = $this->getCurrentStatus();
        return $status ? $status->isPending() : true;
    }

    /**
     * Check if leave request can be cancelled.
     */
    public function canBeCancelled(): bool
    {
        $status = $this->getCurrentStatus();
        return $status && !$status->isFinal();
    }

    /**
     * Scope for approved leaves.
     */
    public function scopeApproved($query)
    {
        return $query->whereHas('leaveAction', function ($q) {
            $q->where('status_id', LeaveStatusEnum::APPROVED->value);
        });
    }

    /**
     * Scope for pending leaves.
     */
    public function scopePending($query)
    {
        return $query->whereDoesntHave('leaveAction', function ($q) {
            $q->whereIn('status_id', [
                LeaveStatusEnum::APPROVED->value,
                LeaveStatusEnum::DISAPPROVED->value,
                LeaveStatusEnum::REJECTED->value,
                LeaveStatusEnum::CANCELLED->value,
            ]);
        });
    }

    /**
     * Scope for leaves in a date range.
     */
    public function scopeInDateRange($query, string $start, string $end)
    {
        return $query->where(function ($q) use ($start, $end) {
            $q->whereBetween('start_date', [$start, $end])
                ->orWhereBetween('end_date', [$start, $end])
                ->orWhere(function ($q2) use ($start, $end) {
                    $q2->where('start_date', '<=', $start)
                        ->where('end_date', '>=', $end);
                });
        });
    }

    /**
     * Scope for leaves on a specific date.
     */
    public function scopeOnDate($query, string $date)
    {
        return $query->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date);
    }
}
