<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

/**
 * AdminAccountSeeder - Ensures the Super Admin account is always available.
 * 
 * This seeder is designed to be run safely at any time, even on existing databases.
 * It will create or update the Super Admin account without affecting other data.
 * 
 * Default Credentials:
 * - Email: admin@admin.com
 * - Password: adminpass
 * 
 * IMPORTANT: Change these credentials immediately after first login!
 */
class AdminAccountSeeder extends Seeder
{
    /**
     * Default admin credentials - configurable via environment variables.
     */
    private const DEFAULT_EMAIL = 'admin@admin.com';
    private const DEFAULT_PASSWORD = 'adminpass';
    private const DEFAULT_NAME = 'System Administrator';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('');
        $this->command->info('╔══════════════════════════════════════════════════════════════╗');
        $this->command->info('║           PJLV Leave Management System - Setup               ║');
        $this->command->info('╚══════════════════════════════════════════════════════════════╝');
        $this->command->info('');

        // Get credentials from environment or use defaults
        $email = env('ADMIN_EMAIL', self::DEFAULT_EMAIL);
        $password = env('ADMIN_PASSWORD', self::DEFAULT_PASSWORD);
        $name = env('ADMIN_NAME', self::DEFAULT_NAME);

        try {
            DB::beginTransaction();

            // Create or update the Super Admin user
            $user = $this->createOrUpdateAdminUser($email, $password);

            // Create or update the Admin profile
            $admin = $this->createOrUpdateAdminProfile($user, $name, $email);

            DB::commit();

            $this->displaySuccessMessage($email, $password);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Failed to create admin account: ' . $e->getMessage());
            Log::error('AdminAccountSeeder failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Ensure minimum required roles exist.
     */
    private function ensureRolesExist(): void
    {
        $this->command->info('→ Checking required roles...');

        $roles = [
            ['id' => 1, 'role_name' => 'Super Administrator', 'role_description' => 'Full system access', 'role_status' => 1],
            ['id' => 2, 'role_name' => 'Human Resource Manager', 'role_description' => 'HR Management', 'role_status' => 1],
            ['id' => 6, 'role_name' => 'Normal', 'role_description' => 'Regular Employee', 'role_status' => 1],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['id' => $role['id']],
                array_merge($role, ['updated_at' => now(), 'created_at' => now()])
            );
        }

        $this->command->info('  ✓ Required roles verified');
    }

    /**
     * Create or update the system admin user account.
     * Note: The system admin is NOT a staff member - it's a standalone system account.
     * We temporarily disable FK checks since users.id references staff.id.
     */
    private function createOrUpdateAdminUser(string $email, string $password): User
    {
        $this->command->info('→ Setting up System Admin user...');

        // Check if user with ID 1 exists
        $existingUser = User::find(1);

        if ($existingUser) {
            // Update existing user
            DB::table('users')->where('id', 1)->update([
                'email' => $email,
                'updated_at' => now(),
            ]);
            $this->command->info('  ✓ Existing system admin user updated');
            return User::find(1);
        }

        // Temporarily disable FK checks for system admin bootstrap
        // This is necessary because users.id has FK to staff.id, but the 
        // system admin is NOT a staff member - it's a standalone account.
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        try {
            DB::table('users')->insertOrIgnore([
                'id' => 1,
                'email' => $email,
                'password' => Hash::make($password),
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } finally {
            // Always re-enable FK checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        $this->command->info('  ✓ System admin user created');
        return User::find(1);
    }

    /**
     * Create or update the admin profile.
     */
    private function createOrUpdateAdminProfile(User $user, string $name, string $email): Admin
    {
        $this->command->info('→ Setting up Admin profile...');

        $admin = Admin::updateOrCreate(
            ['user_id' => $user->id],
            [
                'name' => $name,
                'email' => $email,
                'phone' => env('ADMIN_PHONE', ''),
                'updated_at' => now(),
            ]
        );

        $this->command->info('  ✓ Admin profile configured');
        return $admin;
    }

    /**
     * Display success message with login credentials.
     */
    private function displaySuccessMessage(string $email, string $password): void
    {
        $this->command->info('');
        $this->command->info('╔══════════════════════════════════════════════════════════════╗');
        $this->command->info('║                    ✓ SETUP COMPLETE!                         ║');
        $this->command->info('╠══════════════════════════════════════════════════════════════╣');
        $this->command->info('║                                                              ║');
        $this->command->info('║  Your Super Admin account has been created successfully.     ║');
        $this->command->info('║                                                              ║');
        $this->command->info('║  Login Credentials:                                          ║');
        $this->command->info('║  ─────────────────                                           ║');
        $this->command->info("║    Email:    {$this->padRight($email, 40)}  ║");
        $this->command->info("║    Password: {$this->padRight($password, 40)}  ║");
        $this->command->info('║                                                              ║');
        $this->command->warn('║  ⚠  SECURITY WARNING                                         ║');
        $this->command->warn('║  Change your password immediately after first login!         ║');
        $this->command->info('║                                                              ║');
        $this->command->info('║  Quick Start:                                                ║');
        $this->command->info('║  ────────────                                                ║');
        $this->command->info('║  1. Run: php artisan serve                                   ║');
        $this->command->info('║  2. Visit: http://127.0.0.1:8000/login                       ║');
        $this->command->info('║  3. Login with the credentials above                         ║');
        $this->command->info('║  4. Navigate to Admin Dashboard to configure the system      ║');
        $this->command->info('║                                                              ║');
        $this->command->info('║  Admin Panel: http://127.0.0.1:8000/admin/dashboard          ║');
        $this->command->info('║                                                              ║');
        $this->command->info('╚══════════════════════════════════════════════════════════════╝');
        $this->command->info('');
    }

    /**
     * Pad string to the right for aligned output.
     */
    private function padRight(string $text, int $length): string
    {
        return str_pad($text, $length, ' ', STR_PAD_RIGHT);
    }
}
