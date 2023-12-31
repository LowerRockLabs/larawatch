<?php

namespace Larawatch\Larawatch\EventHandlers;

use Illuminate\Console\Events\CommandStarting;
use Illuminate\Console\Events\ScheduledTaskFinished;
use Illuminate\Console\Scheduling\Schedule;
use Larawatch\Larawatch\Events\SchedulerEvent as Event;
use Larawatch\Larawatch\Support\Concerns\UsesScheduleMonitoringModels;

class BackgroundCommandListener
{
    use UsesScheduleMonitoringModels;

    public function handle(CommandStarting $event)
    {
        if ($event->command !== 'schedule:finish') {
            return;
        }

        collect(app(Schedule::class)->events())
            ->filter(fn (Event $task) => $task->runInBackground)
            ->each(function (Event $task) {
                $task
                    ->then(
                        function () use ($task) {
                            if (! $monitoredTask = $this->getMonitoredScheduleTaskModel()->findForTask($task)) {
                                return;
                            }

                            $event = new ScheduledTaskFinished(
                                $task,
                                0
                            );

                            $monitoredTask->markAsFinished($event);
                        }
                    );
            });
    }
}
