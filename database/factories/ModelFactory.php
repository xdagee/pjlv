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


$factory->define(App\Staff::class, function (Faker $faker) {

    return [
        'staff_number'		=>	$faker->ean8,
        	'title'				=>	$faker->title($gender = 1|0),
        	'firstname'			=>	$faker->firstName($gender = 1|0),
        	'lastname'			=>	$faker->firstName($gender = 1|0),
        	'othername'			=>	null,
        	'dob'				=>	$faker->date($format = 'Y-m-d', $max = 'now'),
        	'mobile_number' 	=>	$faker->unique()->e164PhoneNumber,
        	'gender'			=>	$faker->boolean($chanceOfGettingTrue = 50),
        	'is_active'			=>	1,
        	'date_joined'		=>	$faker->date($format = 'Y-m-d', $max = 'now'),
        	'leave_level_id'	=>	$faker->numberBetween($min = 1, $max = 3),
        	'total_leave_days'	=>	10,
        	'role_id'			=>	$faker->numberBetween($min = 2, $max = 5),
    ];
});
