<?php

namespace Larawatch\Larawatch;

use Illuminate\Console\Events\CommandStarting;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Larawatch\Larawatch\Commands\ListCommand;
use Larawatch\Larawatch\Commands\SendPackageDetailsCommand;
use Larawatch\Larawatch\Commands\SyncCommand;
use Larawatch\Larawatch\EventHandlers\BackgroundCommandListener;
use Larawatch\Larawatch\Events\SchedulerEvent;
use Larawatch\Larawatch\Jobs\SendSlowQueryToAPI;
use Larawatch\Larawatch\Models\MonitoredScheduledTask;
use Larawatch\Larawatch\Models\MonitoredScheduledTaskLogItem;
use Larawatch\Larawatch\Providers\EventServiceProvider;
use Larawatch\Larawatch\Providers\ScheduleServiceProvider;

class LarawatchServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function boot()
    {

        /*
         * Optional methods to load package assets
         */
        $this->mergeConfigFrom(
            __DIR__.'/../config/config.php', 'larawatch'
        );

        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        // $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'laravel-advanced-authentication');
        AboutCommand::add('Larawatch', fn () => ['Version' => '1.0.0']);

        if (config('larawatch.db_monitoring_enabled')) {
            if (config('larawatch.db_slowquery_enabled')) {
                DB::whenQueryingForLongerThan(config('larawatch.db_slowquery_threshold'), function ($connection, QueryExecuted $event) {
                    SendSlowQueryToAPI::dispatch($event);
                });
            }
        }

        SchedulerEvent::macro('monitorName', function (string $monitorName) {
            $this->monitorName = $monitorName;

            return $this;
        });

        SchedulerEvent::macro('graceTimeInMinutes', function (int $graceTimeInMinutes) {
            $this->graceTimeInMinutes = $graceTimeInMinutes;

            return $this;
        });

        SchedulerEvent::macro('doNotMonitor', function (bool $bool = true) {
            $this->doNotMonitor = $bool;

            return $this;
        });

        SchedulerEvent::macro('storeOutputInDb', function () {
            $this->storeOutputInDb = true;
            $this->testUuid = '123475812';

            /** @psalm-suppress UndefinedMethod */
            $this->ensureOutputIsBeingCaptured();

            return $this;
        });

        if ($this->app->runningInConsole()) {

            $this->commands([
                ListCommand::class,
                SyncCommand::class,
                SendPackageDetailsCommand::class,
            ]);

            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('larawatch.php'),
            ], 'larawatch-config');

        }
    }

    /**
     * @return void
     */
    public function register()
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(ScheduleServiceProvider::class);

        Event::listen(CommandStarting::class, BackgroundCommandListener::class);
        $this->app->singleton(MonitoredScheduledTask::class);
        $this->app->singleton(MonitoredScheduledTaskLogItem::class);

        //$this->app->register(EventServiceProvider::class);
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'larawatch');
    }
}
