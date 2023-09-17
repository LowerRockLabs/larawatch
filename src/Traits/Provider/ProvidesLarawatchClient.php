<?php

namespace Larawatch\Traits\Provider;

use Larawatch\Larawatch;
use Larawatch\Http\Client;

trait ProvidesLarawatchClient
{
    protected function setupLarawatchSingleton()
    {
        $this->app->singleton('larawatch', function ($app) {
            return new Larawatch(new Client(
                config('larawatch.destination_token', 'destination_token'),
                config('larawatch.project_key', 'project_key')
            ));
        });

    }
}
