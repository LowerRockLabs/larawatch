<?php

namespace Larawatch\Support\ScheduledTasks\Tasks;

use Illuminate\Console\Events\ScheduledTaskStarting;
use Illuminate\Console\Scheduling\CallbackEvent;
use Illuminate\Console\Scheduling\Event;
use Larawatch\Events\SchedulerEvent;

class JobTask extends Task
{
    protected SchedulerEvent|Event $task;

    public static function canHandleEvent(ScheduledTaskStarting|SchedulerEvent|Event $event): bool
    {
        if (! $event instanceof CallbackEvent) {
            return false;
        }

        if (! is_null($event->command)) {
            return false;
        }

        if (empty($event->description)) {
            return false;
        }

        return class_exists($event->description);
    }

    public function defaultName(): ?string
    {
        return $this->event->description;
    }

    public function type(): string
    {
        return 'job';
    }
}
