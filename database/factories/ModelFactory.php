<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

use Faker\Generator as Faker;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker $faker) {
    static $password;

    return [
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});


$factory->define(App\Job::class, function (Faker $faker) {
    static $password;

    return [
        'job_title' => $faker->unique()->jobTitle,
        'job_description' => $faker->realText($maxNbChars = 200, $indexSize = 2),
        'is_multiple_staff' => $faker->boolean($chanceOfGettingTrue = 50),
    ];
});


$factory->define(App\Staff::class, function (Faker $faker) {
    $gender = $faker->boolean($chanceOfGettingTrue = 50);
    $gend = $gender ? 'male': 'female';

    return [
        	'staff_number'		=>	$faker->ean8,
        	'title'				=>	$faker->title($gend),
        	'firstname'			=>	$faker->firstName($gend),
        	'lastname'			=>	$faker->firstName($gend),
        	'othername'			=>	null,
        	'dob'				=>	$faker->date($format = 'Y-m-d', $max = 'now+100'),
        	'mobile_number' 	=>	$faker->unique()->e164PhoneNumber,
        	'gender'			=>	$gender,
        	'is_active'			=>	1,
        	'date_joined'		=>	$faker->date($format = 'Y-m-d', $max = 'now'),
        	'leave_level_id'	=>	$faker->numberBetween($min = 1, $max = 3),
        	'total_leave_days'	=>	10,
        	'role_id'			=>	$faker->numberBetween($min = 2, $max = 5)
    ];
});

$factory->define(App\LeaveAction::class, function (Faker $faker) {

    return [
        	'leave_id'			    =>	App\Job::all()->random()->id,
        	'actionby'				=>	App\Staff::all()->random()->id,
        	'status_id'				=>	App\LeaveStatus::all()->random()->id,
        	'action_date'			=>	$faker->date($format = 'Y-m-d', $max = 'now'),
        	'action_reason'			=>	$faker->realText($maxNbChars = 30, $indexSize = 2)
    ];
});