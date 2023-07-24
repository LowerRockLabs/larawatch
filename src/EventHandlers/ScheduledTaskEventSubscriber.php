<?php

namespace Larawatch\EventHandlers;

use Illuminate\Console\Events\ScheduledTaskFailed;
use Illuminate\Console\Events\ScheduledTaskFinished;
use Illuminate\Console\Events\ScheduledTaskSkipped;
use Illuminate\Console\Events\ScheduledTaskStarting;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\Log;
use Larawatch\Support\Concerns\UsesScheduleMonitoringModels;

class ScheduledTaskEventSubscriber
{
    use UsesScheduleMonitoringModels;

    public function handleScheduledTaskStarting($event): void
    {
        Log::error('handleScheduledTaskStarting');
    }

    public function handleScheduledTaskFinished($event): void
    {
        Log::error('handleScheduledTaskFinished');
    }

    public function handleScheduledTaskFailed($event): void
    {
        Log::error('handleScheduledTaskFailed');

    }

    public function handleScheduledTaskSkipped($event): void
    {
        Log::error('handleScheduledTaskSkipped');

    }

    public function subscribe(Dispatcher $events): array
    {
        return [
            ScheduledTaskStarting::class => 'handleScheduledTaskStarting',
            ScheduledTaskFinished::class => 'handleScheduledTaskFinished',
            ScheduledTaskFailed::class => 'handleScheduledTaskFailed',
            ScheduledTaskSkipped::class => 'handleScheduledTaskSkipped',
        ];
    }
}
