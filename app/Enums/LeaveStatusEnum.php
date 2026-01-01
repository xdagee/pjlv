<?php

namespace App\Enums;

/**
 * Enum representing leave request statuses.
 * These map to the leave_statuses table IDs and status names.
 */
enum LeaveStatusEnum: int
{
    case UNATTENDED = 1;
    case APPROVED = 2;
    case DISAPPROVED = 3;
    case RECOMMENDED = 4;
    case REJECTED = 5;
    case CANCELLED = 6;

    /**
     * Get the human-readable label for this status.
     */
    public function label(): string
    {
        return match ($this) {
            self::UNATTENDED => 'Unattended',
            self::APPROVED => 'Approved',
            self::DISAPPROVED => 'Disapproved',
            self::RECOMMENDED => 'Recommended',
            self::REJECTED => 'Rejected',
            self::CANCELLED => 'Cancelled',
        };
    }

    /**
     * Get the CSS class for displaying this status.
     */
    public function cssClass(): string
    {
        return match ($this) {
            self::UNATTENDED => 'badge-warning',
            self::APPROVED => 'badge-success',
            self::DISAPPROVED => 'badge-danger',
            self::RECOMMENDED => 'badge-info',
            self::REJECTED => 'badge-danger',
            self::CANCELLED => 'badge-secondary',
        };
    }

    /**
     * Check if this is a terminal (final) status.
     */
    public function isFinal(): bool
    {
        return in_array($this, [
            self::APPROVED,
            self::DISAPPROVED,
            self::REJECTED,
            self::CANCELLED,
        ]);
    }

    /**
     * Check if this status indicates approval.
     */
    public function isApproved(): bool
    {
        return $this === self::APPROVED;
    }

    /**
     * Check if this status indicates rejection.
     */
    public function isRejected(): bool
    {
        return in_array($this, [self::DISAPPROVED, self::REJECTED]);
    }

    /**
     * Check if this status allows further action.
     */
    public function isPending(): bool
    {
        return in_array($this, [self::UNATTENDED, self::RECOMMENDED]);
    }

    /**
     * Get statuses that count as "in progress" (not final).
     */
    public static function pendingStatuses(): array
    {
        return [self::UNATTENDED, self::RECOMMENDED];
    }

    /**
     * Get statuses that count as approval-related.
     */
    public static function approvalStatuses(): array
    {
        return [self::APPROVED, self::RECOMMENDED];
    }

    /**
     * Get statuses that count as rejection-related.
     */
    public static function rejectionStatuses(): array
    {
        return [self::DISAPPROVED, self::REJECTED];
    }

    /**
     * Create from database status name.
     */
    public static function fromName(string $name): ?self
    {
        return match (strtolower($name)) {
            'unattended' => self::UNATTENDED,
            'approved' => self::APPROVED,
            'disapproved' => self::DISAPPROVED,
            'recommended' => self::RECOMMENDED,
            'rejected' => self::REJECTED,
            'cancelled' => self::CANCELLED,
            default => null,
        };
    }
}
