<?php

namespace Larawatch\Traits\Provider;

use Illuminate\Log\LogManager;
use Larawatch\Logger\LarawatchHandler;
use Monolog\Logger;

trait ProvidesErrorParsing
{

    protected function setupLogParser()
    {
        if ($this->app['log'] instanceof LogManager) {
            $this->app['log']->extend('larawatch', function ($app, $config) {
                $handler = new LarawatchHandler(
                    $app['larawatch']
                );

                return new Logger('larawatch', [$handler]);
            });
        }

    }

}
