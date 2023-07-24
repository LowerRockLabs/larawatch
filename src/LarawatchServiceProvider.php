<?php

namespace Larawatch;

use Illuminate\Console\Events\CommandStarting;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Larawatch\Commands\ListCommand;
use Larawatch\Commands\SendPackageDetailsCommand;
use Larawatch\Commands\SyncCommand;
use Larawatch\Commands\TestCommand;
use Larawatch\EventHandlers\BackgroundCommandListener;
use Larawatch\Events\SchedulerEvent;
use Larawatch\Jobs\SendSlowQueryToAPI;
use Larawatch\Models\MonitoredScheduledTask;
use Larawatch\Models\MonitoredScheduledTaskLogItem;
use Larawatch\Providers\EventServiceProvider;
use Larawatch\Providers\ScheduleServiceProvider;
use Monolog\Logger;

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

        if (class_exists(\Illuminate\Foundation\AliasLoader::class)) {
            $loader = \Illuminate\Foundation\AliasLoader::getInstance();
            $loader->alias('larawatch', 'Larawatch\Facade');
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
                TestCommand::class,
            ]);

            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('larawatch.php'),
            ], 'larawatch-config');

            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'larawatch-migrations');

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
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'larawatch');

        $this->app->singleton('larawatch', function ($app) {
            return new Larawatch(new \Larawatch\Http\Client(
                config('larawatch.destination_token', 'destination_token'),
                config('larawatch.project_key', 'project_key')
            ));
        });

        if ($this->app['log'] instanceof \Illuminate\Log\LogManager) {
            $this->app['log']->extend('larawatch', function ($app, $config) {
                $handler = new \Larawatch\Logger\LarawatchHandler(
                    $app['larawatch']
                );

                return new Logger('larawatch', [$handler]);
            });
        }

        //$this->app->register(EventServiceProvider::class);
    }
}
