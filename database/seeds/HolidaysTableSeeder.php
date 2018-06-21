<?php

use Illuminate\Database\Seeder;

use Carbon\Carbon;

class HolidaysTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        function myHoliday($date)
        {
            return  $date->isWeekend() ? $date->next(Carbon::MONDAY)->toDateString() : $date->toDateString();
        }

        //$req = http_get("https://holidayapi.com/v1/holidays?key=447ea863-2f6e-4db4-a007-c7cd26947588&country=Tunisia&year=".Carbon::now()->year, array("timeout"=>1));

        
        $myHolidays = [

        	[
        		'description'	=>	"New Year's Day",
        		'date'			=>	myHoliday(Carbon::parse('first day of January'))
        	],

        	[
                'description'   =>  'Independence Day',
                'date'          =>  myHoliday(Carbon::createFromDate(null, 3, 6))
            ],

       		[
                'description'   =>  'Good Friday',
                'date'          =>  myHoliday(Carbon::parse(date("Y-M-d", easter_date()))->subDays(2))
            ],

       		[
                'description'   =>  'Easter Monday',
                'date'          =>  myHoliday(Carbon::parse(date("Y-M-d", easter_date()))->addDay())
            ],

       		[
                'description'   =>  'May Day',
                'date'          =>  myHoliday(Carbon::parse('first day of May'))
            ],

            [
                'description'   =>  'African Unity Day',
                'date'          =>  myHoliday(Carbon::createFromDate(null, 5, 25))
            ],

            // [
            //     'description'   =>  'Eid al-Fitr',
            //     'date'          =>  ''
            // ],

            [
                'description'   =>  'Republic Day',
                'date'          =>  myHoliday(Carbon::parse('first day of July'))
            ],

            // [
            //     'description'   =>  'Eid al-Adha',
            //     'date'          =>  ''
            // ],

            [
                'description'   =>  "Founders Day",
                'date'          =>  myHoliday(Carbon::createFromDate(null, 9, 21))
            ],

            [
                'description'   =>  "Farmer's Day",
                'date'          =>  myHoliday(Carbon::createFromDate(null, 12, 7))
            ],

            [
                'description'   =>  'Christmas Day',
                'date'          =>  myHoliday(Carbon::createFromDate(null, 12, 25))
            ],

            [
                'description'   =>  'Boxing Day',
                'date'          =>  myHoliday(Carbon::createFromDate(null, 12, 26))
            ]

        ];

        DB::table('holidays')->insert($myHolidays);
    }

}
