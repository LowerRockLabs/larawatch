<?php

namespace Larawatch\Support\ScheduledTasks\Tasks;

use Illuminate\Console\Events\ScheduledTaskStarting;
use Illuminate\Console\Scheduling\CallbackEvent;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Support\Str;
use Larawatch\Events\SchedulerEvent;

class ShellTask extends Task
{
    public static function canHandleEvent(ScheduledTaskStarting|SchedulerEvent|Event $event): bool
    {
        if ($event instanceof CallbackEvent) {
            return true;
        }

        return true;
    }

    public function defaultName(): ?string
    {
        return Str::limit($this->event->command, 255);
    }

    public function type(): string
    {
        return 'shell';
    }
}
