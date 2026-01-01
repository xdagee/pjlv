<?php

namespace App\Enums;

/**
 * Enum representing user roles in the system.
 * These map to the roles table IDs and role names.
 */
enum RoleEnum: int
{
    case ADMIN = 1;
    case HR = 2;
    case CEO = 3;
    case OPS = 4;
    case HOD = 5;
    case NORMAL = 6;


    /**
     * Get the human-readable label for this role.
     */
    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Super Administrator',
            self::HR => 'Human Resource Manager',
            self::CEO => 'Chief Executive Officer',
            self::OPS => 'Operations Manager',
            self::HOD => 'Head of Department',
            self::NORMAL => 'Normal',
        };
    }

    /**
     * Get the short name for this role (as stored in DB).
     */
    public function shortName(): string
    {
        return match ($this) {
            self::ADMIN => 'ADMIN',
            self::HR => 'HR',
            self::CEO => 'CEO',
            self::OPS => 'OPS',
            self::HOD => 'HOD',
            self::NORMAL => 'Normal',
        };
    }

    /**
     * Get the hierarchy level (higher = more permissions).
     */
    public function level(): int
    {
        return match ($this) {
            self::ADMIN => 1,
            self::HR => 2,
            self::CEO => 3,
            self::OPS => 4,
            self::HOD => 5,
            self::NORMAL => 6,
        };
    }

    /**
     * Check if this role can approve leaves.
     */
    public function canApproveLeaves(): bool
    {
        return in_array($this, [
            self::ADMIN,
            self::HR,
            self::CEO,
            self::OPS,
            self::HOD,
        ]);
    }

    /**
     * Check if this role can manage staff.
     */
    public function canManageStaff(): bool
    {
        return in_array($this, [self::ADMIN, self::HR, self::CEO, self::OPS, self::HOD]);
    }

    /**
     * Check if this role can view reports.
     */
    public function canViewReports(): bool
    {
        return in_array($this, [self::ADMIN, self::HR, self::CEO, self::OPS, self::HOD, self::NORMAL]);
    }

    /**
     * Check if this role can manage system settings.
     */
    public function canManageSettings(): bool
    {
        return in_array($this, [self::ADMIN, self::HR]);
    }

    /**
     * Get all roles that can approve leaves.
     */
    public static function approverRoles(): array
    {
        return [self::ADMIN, self::HR, self::CEO, self::OPS, self::HOD];
    }

    /**
     * Get all roles that can manage staff.
     */
    public static function staffManagerRoles(): array
    {
        return [self::ADMIN, self::HR];
    }

    /**
     * Create from database role name.
     */
    public static function fromName(string $name): ?self
    {
        return match (strtolower($name)) {
            'admin', 'super administrator' => self::ADMIN,
            'hr', 'human resource manager', 'human resources manager' => self::HR,
            'ceo', 'chief executive officer' => self::CEO,
            'ops', 'operations manager' => self::OPS,
            'hod', 'head of department' => self::HOD,
            'normal', 'employee' => self::NORMAL,
            default => null,
        };
    }

    /**
     * Check if a role ID is at least as privileged as the given role.
     */
    public static function hasLevel(int $roleId, self $requiredRole): bool
    {
        $role = self::tryFrom($roleId);
        if (!$role) {
            return false;
        }
        return $role->level() <= $requiredRole->level();
    }
}
