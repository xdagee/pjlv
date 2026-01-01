<?php

namespace App\Models;

use App\Enums\RoleEnum;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return UserFactory::new();
    }

    /**
     * The attributes that are mass assignable.
     * Note: 'id' is fillable because users.id = staff.id by design (foreign key).
     */
    protected $fillable = [
        'id',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the staff profile associated with this user.
     */
    public function staff()
    {
        return $this->belongsTo(Staff::class, 'id');
    }

    /**
     * Get the admin profile associated with the user.
     */
    public function admin()
    {
        return $this->hasOne(Admin::class);
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole(string|array $roles): bool
    {
        // Admins have all roles implicitly
        if ($this->admin) {
            return true;
        }

        if (!$this->staff || !$this->staff->role_id) {
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

        return in_array($this->staff->role_id, $roleIds);
    }
}
