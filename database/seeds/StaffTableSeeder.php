<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use Faker\Factory as Faker;

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


        factory(App\Staff::class, 50)->create()->each(function ($s) {
            $s->user()->save(factory(App\User::class)->make());
            //$s->leaveTypes()->attach(App\LeaveType::all()->random()->id);

            $s->jobs()->attach(App\Job::all()->random()->id);

            $faker = Faker::create();
            $s->leaveTypes()->attach(App\LeaveType::all()->random()->id, [
                    'start_date'                =>  $faker->date($format = 'Y-m-d', $max = 'now+100'),
                    'end_date'                  =>  $faker->date($format = 'Y-m-d', $max = 'now+200'),
                    'leave_days'                =>  $faker->numberBetween($min = 1, $max = 36),
                    'created_at'                =>  $faker->date($format = 'Y-m-d', $max = 'now+50')
                ]);

        });
    }
}
