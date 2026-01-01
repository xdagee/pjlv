<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SystemSettingsSeeder extends Seeder
{
    /**
     * Seed the system_settings table with default configuration.
     */
    public function run(): void
    {
        $this->command->info('Seeding system settings...');

        $settings = [
            // Leave Management Settings
            [
                'key' => 'leave.default_annual_days',
                'value' => '21',
                'type' => 'integer',
                'group' => 'leave_management',
                'label' => 'Default Annual Leave Days',
                'description' => 'Default number of annual leave days for new staff',
                'is_public' => false,
            ],
            [
                'key' => 'leave.carry_forward_enabled',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'leave_management',
                'label' => 'Allow Leave Carry Forward',
                'description' => 'Allow unused leave days to be carried to the next year',
                'is_public' => false,
            ],
            [
                'key' => 'leave.max_carry_forward_days',
                'value' => '5',
                'type' => 'integer',
                'group' => 'leave_management',
                'label' => 'Maximum Carry Forward Days',
                'description' => 'Maximum number of days that can be carried forward',
                'is_public' => false,
            ],
            [
                'key' => 'leave.min_notice_days',
                'value' => '3',
                'type' => 'integer',
                'group' => 'leave_management',
                'label' => 'Minimum Notice Days',
                'description' => 'Minimum days in advance a leave request must be made',
                'is_public' => false,
            ],
            [
                'key' => 'leave.require_supervisor_approval',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'leave_management',
                'label' => 'Require Supervisor Approval',
                'description' => 'Leave requests require supervisor approval before HR',
                'is_public' => false,
            ],
            [
                'key' => 'leave.max_consecutive_days',
                'value' => '14',
                'type' => 'integer',
                'group' => 'leave_management',
                'label' => 'Maximum Consecutive Days',
                'description' => 'Maximum consecutive days allowed for a single leave request',
                'is_public' => false,
            ],

            // Email Settings
            [
                'key' => 'email.notifications_enabled',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'email',
                'label' => 'Enable Email Notifications',
                'description' => 'Send email notifications for leave requests and updates',
                'is_public' => false,
            ],
            [
                'key' => 'email.notify_on_request',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'email',
                'label' => 'Notify on New Request',
                'description' => 'Send email when a new leave request is submitted',
                'is_public' => false,
            ],
            [
                'key' => 'email.notify_on_approval',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'email',
                'label' => 'Notify on Approval',
                'description' => 'Send email when a leave request is approved',
                'is_public' => false,
            ],
            [
                'key' => 'email.notify_on_rejection',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'email',
                'label' => 'Notify on Rejection',
                'description' => 'Send email when a leave request is rejected',
                'is_public' => false,
            ],
            [
                'key' => 'email.admin_email',
                'value' => 'admin@example.com',
                'type' => 'string',
                'group' => 'email',
                'label' => 'Admin Email Address',
                'description' => 'Email address for admin notifications',
                'is_public' => false,
            ],
            [
                'key' => 'email.from_name',
                'value' => 'PJLV Leave System',
                'type' => 'string',
                'group' => 'email',
                'label' => 'Email From Name',
                'description' => 'Name displayed in email from field',
                'is_public' => false,
            ],

            // System Settings
            [
                'key' => 'system.app_name',
                'value' => 'PJLV Leave Management',
                'type' => 'string',
                'group' => 'system',
                'label' => 'Application Name',
                'description' => 'Name of the application displayed in the header',
                'is_public' => true,
            ],
            [
                'key' => 'system.timezone',
                'value' => 'Africa/Accra',
                'type' => 'string',
                'group' => 'system',
                'label' => 'System Timezone',
                'description' => 'Default timezone for the application',
                'is_public' => false,
            ],
            [
                'key' => 'system.date_format',
                'value' => 'Y-m-d',
                'type' => 'string',
                'group' => 'system',
                'label' => 'Date Format',
                'description' => 'Format for displaying dates (PHP date format)',
                'is_public' => true,
            ],
            [
                'key' => 'system.maintenance_mode',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'system',
                'label' => 'Maintenance Mode',
                'description' => 'Enable maintenance mode to prevent user access',
                'is_public' => false,
            ],
            [
                'key' => 'system.company_name',
                'value' => 'Your Company',
                'type' => 'string',
                'group' => 'system',
                'label' => 'Company Name',
                'description' => 'Name of your organization',
                'is_public' => true,
            ],

            // Security Settings
            [
                'key' => 'security.password_min_length',
                'value' => '8',
                'type' => 'integer',
                'group' => 'security',
                'label' => 'Minimum Password Length',
                'description' => 'Minimum number of characters for passwords',
                'is_public' => false,
            ],
            [
                'key' => 'security.session_timeout_minutes',
                'value' => '60',
                'type' => 'integer',
                'group' => 'security',
                'label' => 'Session Timeout (Minutes)',
                'description' => 'Minutes of inactivity before auto-logout',
                'is_public' => false,
            ],
            [
                'key' => 'security.max_login_attempts',
                'value' => '5',
                'type' => 'integer',
                'group' => 'security',
                'label' => 'Maximum Login Attempts',
                'description' => 'Number of failed login attempts before lockout',
                'is_public' => false,
            ],
            [
                'key' => 'security.lockout_duration_minutes',
                'value' => '15',
                'type' => 'integer',
                'group' => 'security',
                'label' => 'Lockout Duration (Minutes)',
                'description' => 'Minutes until account is unlocked after lockout',
                'is_public' => false,
            ],

            // ============================================
            // PHASE 1: Display & Personalization Settings
            // ============================================
            [
                'key' => 'display.pagination_size',
                'value' => '15',
                'type' => 'integer',
                'group' => 'display',
                'label' => 'Records Per Page',
                'description' => 'Number of records shown per page in tables (10, 15, 25, 50)',
                'is_public' => true,
            ],
            [
                'key' => 'display.date_format',
                'value' => 'd/m/Y',
                'type' => 'string',
                'group' => 'display',
                'label' => 'Display Date Format',
                'description' => 'Format for displaying dates (d/m/Y, m/d/Y, Y-m-d, d M Y)',
                'is_public' => true,
            ],
            [
                'key' => 'display.time_format',
                'value' => '24',
                'type' => 'string',
                'group' => 'display',
                'label' => 'Time Format',
                'description' => 'Time format: 12 (AM/PM) or 24 (military)',
                'is_public' => true,
            ],
            [
                'key' => 'display.theme_mode',
                'value' => 'light',
                'type' => 'string',
                'group' => 'display',
                'label' => 'Theme Mode',
                'description' => 'Color theme: light, dark, or auto',
                'is_public' => true,
            ],
            [
                'key' => 'display.sidebar_collapsed',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'display',
                'label' => 'Sidebar Collapsed by Default',
                'description' => 'Start with sidebar minimized',
                'is_public' => true,
            ],
            [
                'key' => 'display.show_staff_photos',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'display',
                'label' => 'Show Staff Photos',
                'description' => 'Display staff photos in lists and cards',
                'is_public' => true,
            ],

            // ============================================
            // Calendar & Scheduling Settings
            // ============================================
            [
                'key' => 'calendar.week_start',
                'value' => 'monday',
                'type' => 'string',
                'group' => 'calendar',
                'label' => 'Week Starts On',
                'description' => 'First day of the week: sunday or monday',
                'is_public' => true,
            ],
            [
                'key' => 'calendar.default_view',
                'value' => 'month',
                'type' => 'string',
                'group' => 'calendar',
                'label' => 'Default Calendar View',
                'description' => 'Default view: day, week, month',
                'is_public' => true,
            ],
            [
                'key' => 'calendar.holiday_color',
                'value' => '#4caf50',
                'type' => 'string',
                'group' => 'calendar',
                'label' => 'Holiday Color',
                'description' => 'Background color for public holidays on calendar',
                'is_public' => true,
            ],
            [
                'key' => 'calendar.show_weekends',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'calendar',
                'label' => 'Show Weekends',
                'description' => 'Display Saturday and Sunday on calendar',
                'is_public' => true,
            ],
            [
                'key' => 'calendar.business_hours_start',
                'value' => '08:00',
                'type' => 'string',
                'group' => 'calendar',
                'label' => 'Business Hours Start',
                'description' => 'Start of business hours (HH:MM)',
                'is_public' => true,
            ],
            [
                'key' => 'calendar.business_hours_end',
                'value' => '17:00',
                'type' => 'string',
                'group' => 'calendar',
                'label' => 'Business Hours End',
                'description' => 'End of business hours (HH:MM)',
                'is_public' => true,
            ],

            // ============================================
            // Workflow Settings
            // ============================================
            [
                'key' => 'workflow.approval_levels',
                'value' => '2',
                'type' => 'integer',
                'group' => 'workflow',
                'label' => 'Approval Chain Length',
                'description' => 'Number of approval levels: 1 (HR only), 2 (Supervisor→HR), 3 (Supervisor→Manager→HR)',
                'is_public' => false,
            ],
            [
                'key' => 'workflow.allow_weekend_leave',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'workflow',
                'label' => 'Allow Weekend Leave',
                'description' => 'Count weekends in leave day calculations',
                'is_public' => false,
            ],
            [
                'key' => 'workflow.max_retroactive_days',
                'value' => '7',
                'type' => 'integer',
                'group' => 'workflow',
                'label' => 'Max Retroactive Days',
                'description' => 'Maximum days in the past for backdated leave requests',
                'is_public' => false,
            ],
            [
                'key' => 'workflow.balance_warning_threshold',
                'value' => '20',
                'type' => 'integer',
                'group' => 'workflow',
                'label' => 'Low Balance Warning (%)',
                'description' => 'Show warning when leave balance falls below this percentage',
                'is_public' => true,
            ],
            [
                'key' => 'workflow.require_reason',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'workflow',
                'label' => 'Require Leave Reason',
                'description' => 'Staff must provide reason when applying for leave',
                'is_public' => false,
            ],
            [
                'key' => 'workflow.allow_half_days',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'workflow',
                'label' => 'Allow Half-Day Leave',
                'description' => 'Enable half-day (0.5) leave requests',
                'is_public' => true,
            ],

            // ============================================
            // Export & Report Settings
            // ============================================
            [
                'key' => 'export.csv_delimiter',
                'value' => ',',
                'type' => 'string',
                'group' => 'export',
                'label' => 'CSV Delimiter',
                'description' => 'Field delimiter for CSV exports: comma, semicolon, or tab',
                'is_public' => false,
            ],
            [
                'key' => 'export.pdf_page_size',
                'value' => 'A4',
                'type' => 'string',
                'group' => 'export',
                'label' => 'PDF Page Size',
                'description' => 'Paper size for PDF exports: A4, Letter, Legal',
                'is_public' => false,
            ],
            [
                'key' => 'export.pdf_orientation',
                'value' => 'portrait',
                'type' => 'string',
                'group' => 'export',
                'label' => 'PDF Orientation',
                'description' => 'Page orientation: portrait or landscape',
                'is_public' => false,
            ],
            [
                'key' => 'export.include_headers',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'export',
                'label' => 'Include Column Headers',
                'description' => 'Include header row in CSV exports',
                'is_public' => false,
            ],
            [
                'key' => 'export.report_footer',
                'value' => 'Generated by PJLV Leave Management System',
                'type' => 'string',
                'group' => 'export',
                'label' => 'Report Footer Text',
                'description' => 'Custom text displayed at the bottom of PDF reports',
                'is_public' => false,
            ],

            // ============================================
            // Analytics Dashboard Settings
            // ============================================
            [
                'key' => 'analytics.top_n_takers',
                'value' => '10',
                'type' => 'integer',
                'group' => 'analytics',
                'label' => 'Top Leave Takers to Show',
                'description' => 'Number of top leave takers displayed on dashboard',
                'is_public' => true,
            ],
            [
                'key' => 'analytics.default_period',
                'value' => 'year',
                'type' => 'string',
                'group' => 'analytics',
                'label' => 'Default Report Period',
                'description' => 'Default time range: month, quarter, year',
                'is_public' => true,
            ],
            [
                'key' => 'analytics.show_comparison',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'analytics',
                'label' => 'Show Year Comparison',
                'description' => 'Display year-over-year comparison in charts',
                'is_public' => true,
            ],
            [
                'key' => 'analytics.auto_refresh_minutes',
                'value' => '0',
                'type' => 'integer',
                'group' => 'analytics',
                'label' => 'Auto-Refresh Interval',
                'description' => 'Minutes between dashboard refreshes (0 = disabled)',
                'is_public' => true,
            ],
            [
                'key' => 'analytics.hide_empty_departments',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'analytics',
                'label' => 'Hide Empty Departments',
                'description' => 'Hide departments with no leave data in reports',
                'is_public' => true,
            ],
            [
                'key' => 'analytics.cache_ttl_minutes',
                'value' => '60',
                'type' => 'integer',
                'group' => 'analytics',
                'label' => 'Analytics Cache Duration',
                'description' => 'Minutes to cache analytics data (improves performance)',
                'is_public' => false,
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('system_settings')->updateOrInsert(
                ['key' => $setting['key']],
                array_merge($setting, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        $this->command->info('  ✓ System settings seeded: ' . count($settings) . ' settings');
    }
}
