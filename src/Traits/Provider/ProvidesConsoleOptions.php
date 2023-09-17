<?php

namespace Larawatch\Traits\Provider;

use Larawatch\Commands\{RunChecksCommand,RunConsoleChecksCommand,SendChecksToLarawatchCommand,ListCommand,SendPackageDetailsCommand,SyncCommand,TestCommand};

trait ProvidesConsoleOptions
{

    protected function returnConsoleOptions()
    {
        $this->commands([
            RunChecksCommand::class,
            RunConsoleChecksCommand:: class,
            ListCommand::class,
            SyncCommand::class,
            SendPackageDetailsCommand::class,
            TestCommand::class,
            SendChecksToLarawatchCommand::class,
        ]);

        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('larawatch.php'),
        ], 'larawatch-config');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'larawatch-migrations');

    }
}
