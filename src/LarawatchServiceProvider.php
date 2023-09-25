<?php

namespace Larawatch;

use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\ServiceProvider;
use Larawatch\Models\{MonitoredScheduledTask,MonitoredScheduledTaskLogItem};
use Larawatch\Providers\{EventServiceProvider,ScheduleServiceProvider};
use Larawatch\Traits\Provider\ProvidesProviderTraits;

class LarawatchServiceProvider extends ServiceProvider
{
    use ProvidesProviderTraits;
    
    /**
     * @return void
     */
    public function boot()
    {
        // Setup About Command
        AboutCommand::add('Larawatch', fn () => ['Version' => '1.0.0']);

        // Merge Config
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'larawatch');


        // Setup Facade
        if (class_exists(\Illuminate\Foundation\AliasLoader::class)) {
            $loader = \Illuminate\Foundation\AliasLoader::getInstance();
            $loader->alias('larawatch', 'Larawatch\Facade');
        }

        // Load Routes if Enabled
        if (config('larawatch.routes.enabled', false))
        {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');    
        }

        // Setup Slow Query Log If Enabled
        if (config('larawatch.slow_query.enabled')) {
            $this->setupSlowQueryLogger();
        }

        // Add Scheduler Monitor Macros
        $this->addSchedulerMacros();

        // Add Console Options
        if ($this->app->runningInConsole()) {
            $this->returnConsoleOptions();
        }
    }

    /**
     * @return void
     */
    public function register()
    {
        $this->app->register(EventServiceProvider::class);
        //$this->app->register(ScheduleServiceProvider::class);
       // Event::subscribe(\Larawatch\Subscribers\ScheduledEventSubscriber::class);

        // Setup Command Listener
        $this->setupCommandListener();

        $this->app->singleton(MonitoredScheduledTask::class);
        $this->app->singleton(MonitoredScheduledTaskLogItem::class);

        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'larawatch');

        // Setup Larawatch Client
        $this->setupLarawatchSingleton();

        // Setup Log Parsing
        $this->setupLogParser();
        

        //$this->app->register(EventServiceProvider::class);
    }
}
