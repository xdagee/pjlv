<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SystemSetting;

class SettingsTableSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Leave Management Settings
            [
                'key' => 'leave.default_annual_days',
                'value' => '21',
                'type' => 'integer',
                'group' => 'leave_management',
                'label' => 'Default Annual Leave Days',
                'description' => 'Default number of annual leave days for new staff (can be overridden by Leave Level)',
            ],
            [
                'key' => 'leave.require_medical_certificate_days',
                'value' => '3',
                'type' => 'integer',
                'group' => 'leave_management',
                'label' => 'Medical Certificate Required After (Days)',
                'description' => 'Number of sick leave days before medical certificate is required',
            ],
            [
                'key' => 'leave.allow_negative_balance',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'leave_management',
                'label' => 'Allow Negative Leave Balance',
                'description' => 'Allow staff to apply for leave even if it results in negative balance',
            ],
            [
                'key' => 'leave.carry_over_enabled',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'leave_management',
                'label' => 'Enable Leave Carry Over',
                'description' => 'Allow unused leave days to carry over to next year (Ghana Labour Act discourages this)',
            ],
            [
                'key' => 'leave.max_carry_over_days',
                'value' => '5',
                'type' => 'integer',
                'group' => 'leave_management',
                'label' => 'Maximum Carry Over Days',
                'description' => 'Maximum number of days that can be carried over (if enabled)',
            ],

            // Email Settings
            [
                'key' => 'email.from_name',
                'value' => 'PJLV Leave System',
                'type' => 'string',
                'group' => 'email',
                'label' => 'Email From Name',
                'description' => 'Name that appears in outgoing emails',
            ],
            [
                'key' => 'email.notification_enabled',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'email',
                'label' => 'Enable Email Notifications',
                'description' => 'Send email notifications for leave requests and approvals',
            ],
            [
                'key' => 'email.notify_on_request',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'email',
                'label' => 'Notify on Leave Request',
                'description' => 'Send email when new leave request is submitted',
            ],
            [
                'key' => 'email.notify_on_approval',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'email',
                'label' => 'Notify on Approval/Rejection',
                'description' => 'Send email when leave is approved or rejected',
            ],

            // System Settings
            [
                'key' => 'system.app_name',
                'value' => 'PJLV Leave Management',
                'type' => 'string',
                'group' => 'system',
                'label' => 'Application Name',
                'description' => 'Name displayed in the application header',
                'is_public' => true,
            ],
            [
                'key' => 'system.timezone',
                'value' => 'Africa/Accra',
                'type' => 'string',
                'group' => 'system',
                'label' => 'System Timezone',
                'description' => 'Default timezone for the application',
                'is_public' => true,
            ],
            [
                'key' => 'system.maintenance_mode',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'system',
                'label' => 'Maintenance Mode',
                'description' => 'Put the system in maintenance mode',
            ],
            [
                'key' => 'system.session_timeout',
                'value' => '120',
                'type' => 'integer',
                'group' => 'system',
                'label' => 'Session Timeout (Minutes)',
                'description' => 'Automatic logout after inactivity period',
            ],

            // Security Settings
            [
                'key' => 'security.password_min_length',
                'value' => '8',
                'type' => 'integer',
                'group' => 'security',
                'label' => 'Minimum Password Length',
                'description' => 'Minimum number of characters for passwords',
            ],
            [
                'key' => 'security.require_password_change_days',
                'value' => '90',
                'type' => 'integer',
                'group' => 'security',
                'label' => 'Password Change Period (Days)',
                'description' => 'Require password change after this many days (0 to disable)',
            ],
            [
                'key' => 'security.max_login_attempts',
                'value' => '5',
                'type' => 'integer',
                'group' => 'security',
                'label' => 'Maximum Login Attempts',
                'description' => 'Lock account after this many failed login attempts',
            ],
        ];

        foreach ($settings as $setting) {
            SystemSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
