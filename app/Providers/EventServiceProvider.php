<?php

namespace App\Providers;

use App\Events\LeaveSubmitted;
use App\Events\LeaveStatusChanged;
use App\Events\LeaveUpdated;
use App\Listeners\HandleLeaveSubmitted;
use App\Listeners\HandleLeaveStatusChanged;
use App\Listeners\HandleLeaveUpdated;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        LeaveSubmitted::class => [
            HandleLeaveSubmitted::class,
        ],
        LeaveStatusChanged::class => [
            HandleLeaveStatusChanged::class,
        ],
        LeaveUpdated::class => [
            HandleLeaveUpdated::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
