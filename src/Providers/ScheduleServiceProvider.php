<?php

namespace Larawatch\Larawatch\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Larawatch\Larawatch\Jobs\SendPackageVersionsToAPI;
use Larawatch\Larawatch\Jobs\SendServerStatsToAPI;

class ScheduleServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->callAfterResolving(Schedule::class, function (Schedule $schedule) {
            $schedule->job(new SendServerStatsToAPI)->everyFifteenMinutes()->storeOutputInDb();
            $schedule->job(new SendPackageVersionsToAPI)->everyFifteenMinutes()->storeOutputInDb();

        });
    }

    public function register()
    {
    }
}
