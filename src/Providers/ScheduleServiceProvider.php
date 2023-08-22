<?php

namespace Larawatch\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Larawatch\Jobs\SendDataToLarawatch;

class ScheduleServiceProvider extends \Illuminate\Foundation\Support\Providers\EventServiceProvider
{
    public function boot()
    {
        $this->callAfterResolving(Schedule::class, function (Schedule $schedule) {
            //$schedule->job(new SendDataToLarawatch)->everyFifteenMinutes();

        });
    }

    public function register()
    {
    }
}
