<?php

namespace Larawatch\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Larawatch\Jobs\SendPackageVersionsToAPI;
use Larawatch\Jobs\SendServerStatsToAPI;

class ScheduleServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->callAfterResolving(Schedule::class, function (Schedule $schedule) {
            $schedule->job(new SendServerStatsToAPI)->everyHour()->storeOutputInDb();
            $schedule->job(new SendPackageVersionsToAPI)->everyHour()->storeOutputInDb();

        });
    }

    public function register()
    {
    }
}
