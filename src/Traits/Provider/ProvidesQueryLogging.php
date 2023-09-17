<?php

namespace Larawatch\Traits\Provider;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Larawatch\Jobs\SendSlowQueryToAPI;

trait ProvidesQueryLogging
{
    protected function setupSlowQueryLogger()
    {
        DB::whenQueryingForLongerThan(config('larawatch.slow_query.threshold'), function ($connection, QueryExecuted $event) {
            SendSlowQueryToAPI::dispatch($event);
        });


    }
}
