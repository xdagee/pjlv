<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use Faker\Factory as Faker;
use Carbon\Carbon;

class StaffTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('staff')->insert([
        	'staff_number'		=>	'11111111',
        	'title'				=>	'Mr',
        	'firstname'			=>	'admin',
        	'lastname'			=>	'system',
        	'othername'			=>	'administrator',
        	'dob'				=>	'1995-06-10',
        	'mobile_number' 	=>	'+233202996677',
        	'gender'			=>	1,
        	'is_active'			=>	1,
        	'date_joined'		=>	'2018-06-14',
        	'leave_level_id'	=>	1,
        	'total_leave_days'	=>	10,
        	'role_id'			=>	1
        ]);


        factory(App\Staff::class, 10)->create()->each(function ($s) {
            $s->user()->save(factory(App\User::class)->make());
            //$s->leaveTypes()->attach(App\LeaveType::all()->random()->id);

            $s->jobs()->attach(App\Job::all()->random()->id);

            $faker = Faker::create();
            $startDate = $faker->date($format = 'Y-m-d', $max = 'now');
            $duration =  $faker->numberBetween($min = 1, $max = 36);
            $endDate = Carbon::parse($startDate)->addDays($duration)->toDateString();
            $createdAt = Carbon::parse($startDate)->subDays(18)->toDateString();
            
            $s->leaveTypes()->attach(App\LeaveType::all()->random()->id, [
                    'start_date'                =>  $startDate,
                    'end_date'                  =>  $endDate,
                    'leave_days'                =>  $duration,
                    'created_at'                =>  $createdAt,
                    'updated_at'                =>  $createdAt
                ]);

        });
    }
}
