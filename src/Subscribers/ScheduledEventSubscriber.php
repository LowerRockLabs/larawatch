<?php

namespace Larawatch\Subscribers;

use Illuminate\Events\Dispatcher;
use Larawatch\Support\Concerns\UsesScheduleMonitoringModels;

class ScheduledEventSubscriber
{
    use UsesScheduleMonitoringModels;

    public function subscribe(Dispatcher $events): void
    {

    }
}
