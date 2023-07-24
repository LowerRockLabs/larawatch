<?php

namespace Larawatch\Larawatch\Support\ScheduledTasks;

use Illuminate\Console\Events\ScheduledTaskStarting;
use Illuminate\Console\Scheduling\Event;
use Larawatch\Larawatch\Events\SchedulerEvent;
use Larawatch\Larawatch\Support\ScheduledTasks\Tasks\ClosureTask;
use Larawatch\Larawatch\Support\ScheduledTasks\Tasks\CommandTask;
use Larawatch\Larawatch\Support\ScheduledTasks\Tasks\JobTask;
use Larawatch\Larawatch\Support\ScheduledTasks\Tasks\ShellTask;
use Larawatch\Larawatch\Support\ScheduledTasks\Tasks\Task;

class ScheduledTaskFactory
{
    public static function createForEvent(ScheduledTaskStarting|SchedulerEvent|Event $event): Task
    {
        $taskClass = collect([
            ClosureTask::class,
            JobTask::class,
            CommandTask::class,
            ShellTask::class,
        ])
            ->first(fn (string $taskClass) => $taskClass::canHandleEvent($event));

        return new $taskClass($event);
    }
}
