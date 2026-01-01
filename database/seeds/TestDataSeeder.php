<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Staff;
use App\Models\Role;
use App\Models\Department;
use App\Models\LeaveType;
use App\Models\LeaveLevel;
use App\Models\LeaveStatus;
use App\Models\StaffLeave;
use App\Models\LeaveAction;
use App\Models\Notification;
use Carbon\Carbon;
use Faker\Factory as Faker;

class TestDataSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Clear existing data (careful in production!)
        $this->command->info('Clearing existing test data...');
        Notification::truncate();
        LeaveAction::truncate();
        StaffLeave::truncate();
        // Don't truncate Staff/User as they may have dependencies

        // 1. Seed Roles (if not exists)
        $this->seedRoles();

        // 2. Seed Departments
        $this->seedDepartments();

        // 3. Seed Leave Types
        $this->seedLeaveTypes();

        // 4. Seed Leave Statuses
        $this->seedLeaveStatuses();

        // 5. Create Test Users for each Role
        $this->seedTestUsers($faker);

        // 6. Create Leave Requests with various statuses
        $this->seedLeaveRequests($faker);

        // 7. Create Notifications
        $this->seedNotifications();

        $this->command->info('Test data seeded successfully!');
        $this->command->info('');
        $this->command->info('=== LOGIN CREDENTIALS ===');
        $this->command->info('Admin:  admin@pjlv.test / password');
        $this->command->info('HR:     hr@pjlv.test / password');
        $this->command->info('Staff:  john.doe@pjlv.test / password');
        $this->command->info('Staff:  jane.smith@pjlv.test / password');
    }

    private function seedRoles()
    {
        $roles = [
            ['id' => 1, 'role_name' => 'Admin', 'role_status' => 1],
            ['id' => 2, 'role_name' => 'HR', 'role_status' => 1],
            ['id' => 3, 'role_name' => 'DG', 'role_status' => 1],
            ['id' => 4, 'role_name' => 'Director', 'role_status' => 1],
            ['id' => 5, 'role_name' => 'Normal', 'role_status' => 1],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['id' => $role['id']], $role);
        }
        $this->command->info('Roles seeded.');
    }

    private function seedDepartments()
    {
        $departments = [
            ['name' => 'Information Technology', 'description' => 'IT and Software Development'],
            ['name' => 'Human Resources', 'description' => 'HR and People Operations'],
            ['name' => 'Finance', 'description' => 'Financial Operations and Accounting'],
            ['name' => 'Operations', 'description' => 'Business Operations'],
            ['name' => 'Marketing', 'description' => 'Marketing and Communications'],
        ];

        foreach ($departments as $dept) {
            Department::updateOrCreate(['name' => $dept['name']], $dept);
        }
        $this->command->info('Departments seeded.');
    }

    private function seedLeaveTypes()
    {
        $leaveTypes = [
            ['leave_type_name' => 'Annual Leave', 'description' => 'Regular annual vacation', 'leave_duration' => 20],
            ['leave_type_name' => 'Sick Leave', 'description' => 'Medical illness leave', 'leave_duration' => 10],
            ['leave_type_name' => 'Maternity Leave', 'description' => 'Maternity/Paternity leave', 'leave_duration' => 90],
            ['leave_type_name' => 'Compassionate Leave', 'description' => 'Bereavement or family emergency', 'leave_duration' => 5],
            ['leave_type_name' => 'Study Leave', 'description' => 'Educational pursuits', 'leave_duration' => 10],
        ];

        foreach ($leaveTypes as $lt) {
            LeaveType::updateOrCreate(['leave_type_name' => $lt['leave_type_name']], $lt);
        }
        $this->command->info('Leave Types seeded.');
    }

    private function seedLeaveStatuses()
    {
        $statuses = [
            ['id' => 1, 'status_name' => 'Unattended'],
            ['id' => 2, 'status_name' => 'Approved'],
            ['id' => 3, 'status_name' => 'Disapproved'],
            ['id' => 4, 'status_name' => 'Recommended'],
            ['id' => 5, 'status_name' => 'Rejected'],
            ['id' => 6, 'status_name' => 'Cancelled'],
        ];

        foreach ($statuses as $status) {
            LeaveStatus::updateOrCreate(['id' => $status['id']], $status);
        }
        $this->command->info('Leave Statuses seeded.');
    }

    private function seedTestUsers($faker)
    {
        $itDept = Department::where('name', 'Information Technology')->first();
        $hrDept = Department::where('name', 'Human Resources')->first();
        $finDept = Department::where('name', 'Finance')->first();

        // Admin User
        $adminUser = User::updateOrCreate(
            ['email' => 'admin@pjlv.test'],
            ['name' => 'System Admin', 'password' => Hash::make('password')]
        );
        Staff::updateOrCreate(
            ['id' => $adminUser->id],
            [
                'firstname' => 'System',
                'lastname' => 'Administrator',
                'role_id' => 1,
                'department_id' => $itDept?->id,
                'is_active' => 1,
                'gender' => 'Male',
                'phoneno' => '0700000001',
                'date_employed' => '2020-01-01',
            ]
        );

        // HR User
        $hrUser = User::updateOrCreate(
            ['email' => 'hr@pjlv.test'],
            ['name' => 'HR Manager', 'password' => Hash::make('password')]
        );
        Staff::updateOrCreate(
            ['id' => $hrUser->id],
            [
                'firstname' => 'Grace',
                'lastname' => 'HR Manager',
                'role_id' => 2,
                'department_id' => $hrDept?->id,
                'is_active' => 1,
                'gender' => 'Female',
                'phoneno' => '0700000002',
                'date_employed' => '2021-03-15',
            ]
        );

        // Normal Staff Users
        $staff1User = User::updateOrCreate(
            ['email' => 'john.doe@pjlv.test'],
            ['name' => 'John Doe', 'password' => Hash::make('password')]
        );
        Staff::updateOrCreate(
            ['id' => $staff1User->id],
            [
                'firstname' => 'John',
                'lastname' => 'Doe',
                'role_id' => 5,
                'department_id' => $itDept?->id,
                'supervisor_id' => $hrUser->id,
                'is_active' => 1,
                'gender' => 'Male',
                'phoneno' => '0700000003',
                'date_employed' => '2022-06-01',
            ]
        );

        $staff2User = User::updateOrCreate(
            ['email' => 'jane.smith@pjlv.test'],
            ['name' => 'Jane Smith', 'password' => Hash::make('password')]
        );
        Staff::updateOrCreate(
            ['id' => $staff2User->id],
            [
                'firstname' => 'Jane',
                'lastname' => 'Smith',
                'role_id' => 5,
                'department_id' => $finDept?->id,
                'supervisor_id' => $hrUser->id,
                'is_active' => 1,
                'gender' => 'Female',
                'phoneno' => '0700000004',
                'date_employed' => '2023-01-10',
            ]
        );

        // Create more random staff
        for ($i = 0; $i < 5; $i++) {
            $user = User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => Hash::make('password'),
            ]);

            Staff::create([
                'id' => $user->id,
                'firstname' => $faker->firstName,
                'lastname' => $faker->lastName,
                'role_id' => 5,
                'department_id' => Department::inRandomOrder()->first()?->id,
                'supervisor_id' => $hrUser->id,
                'is_active' => 1,
                'gender' => $faker->randomElement(['Male', 'Female']),
                'phoneno' => $faker->phoneNumber,
                'date_employed' => $faker->dateTimeBetween('-3 years', '-6 months')->format('Y-m-d'),
            ]);
        }

        $this->command->info('Test users seeded.');
    }

    private function seedLeaveRequests($faker)
    {
        $staffMembers = Staff::where('role_id', 5)->get();
        $leaveTypes = LeaveType::all();
        $hrStaff = Staff::where('role_id', 2)->first();

        foreach ($staffMembers as $staff) {
            // Create 2-4 leave requests per staff member
            $numRequests = rand(2, 4);

            for ($i = 0; $i < $numRequests; $i++) {
                $startDate = $faker->dateTimeBetween('-6 months', '+2 months');
                $leaveDays = rand(1, 5);
                $endDate = (clone $startDate)->modify("+{$leaveDays} days");

                $leave = StaffLeave::create([
                    'staff_id' => $staff->id,
                    'leave_type_id' => $leaveTypes->random()->id,
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate->format('Y-m-d'),
                    'leave_days' => $leaveDays,
                ]);

                // Assign random status
                $statusId = $faker->randomElement([1, 2, 2, 2, 5]); // Weighted towards Approved

                LeaveAction::create([
                    'leave_id' => $leave->id,
                    'status_id' => $statusId,
                    'actionby' => $statusId == 1 ? $staff->id : ($hrStaff?->id ?? $staff->id),
                    'action_reason' => $statusId == 2 ? 'Approved. Enjoy your leave!' : ($statusId == 5 ? 'Insufficient balance or overlapping period.' : null),
                ]);
            }
        }

        $this->command->info('Leave requests seeded.');
    }

    private function seedNotifications()
    {
        $users = User::all();

        foreach ($users as $user) {
            // Create 1-3 notifications per user
            $numNotifications = rand(1, 3);

            for ($i = 0; $i < $numNotifications; $i++) {
                Notification::create([
                    'user_id' => $user->id,
                    'title' => collect(['New Leave Request', 'Leave Approved', 'Leave Rejected', 'System Update'])->random(),
                    'message' => 'This is a sample notification message for testing purposes.',
                    'type' => collect(['info', 'success', 'warning'])->random(),
                    'link' => '/dashboard',
                ]);
            }
        }

        $this->command->info('Notifications seeded.');
    }
}
