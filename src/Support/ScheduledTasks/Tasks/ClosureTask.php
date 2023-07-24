<?php

namespace Larawatch\Larawatch\Support\ScheduledTasks\Tasks;

use Illuminate\Console\Events\ScheduledTaskStarting;
use Illuminate\Console\Scheduling\CallbackEvent;
use Illuminate\Console\Scheduling\Event;
use Larawatch\Larawatch\Events\SchedulerEvent;

class ClosureTask extends Task
{
    public static function canHandleEvent(ScheduledTaskStarting|SchedulerEvent|Event $event): bool
    {
        if (! $event instanceof CallbackEvent) {
            return false;
        }

        return in_array($event->getSummaryForDisplay(), ['Closure', 'Callback']);
    }

    public function type(): string
    {
        return 'closure';
    }

    public function defaultName(): ?string
    {
        return null;
    }
}
