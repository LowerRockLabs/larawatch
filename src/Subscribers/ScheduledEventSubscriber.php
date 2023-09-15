<?php

namespace Larawatch\Subscribers;

use Illuminate\Console\Events\ScheduledTaskFailed;
use Illuminate\Console\Events\ScheduledTaskFinished;
use Illuminate\Console\Events\ScheduledTaskSkipped;
use Illuminate\Console\Events\ScheduledTaskStarting;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\Log;
use Illuminate\Console\Scheduling\Event;

class ScheduledEventSubscriber
{
    public function handleScheduledTaskStarting(ScheduledTaskStarting $event): void
    {
        Log::error('handleScheduledTaskStarting');
        Log::error("Scheduled Task Starting: ".($event->task->description ?? 'No Description'). ' Mutex:'.($event->task->mutexName() ?? 'NoMutexname'));
                //Log::error("EventName: ".$event->name ?? 'NoEventName');
        //Log::error("TaskName: ".$event->task->name ?? 'NoTaskName');

        //Log::error(serialize($event->task->output));
    }

    public function handleScheduledTaskFinished(ScheduledTaskFinished $event): void
    {
        Log::error('handleScheduledTaskFinished');
        Log::error("Scheduled Task Finished: ".($event->task->description ?? 'No Description'). ' Mutex:'.($event->task->mutexName() ?? 'NoMutexname'));
       //Log::error(serialize($event->task->output));

    }

    public function handleScheduledTaskFailed(ScheduledTaskFailed $event): void
    {
        Log::error('handleScheduledTaskFailed');
        Log::error("Scheduled Task Failed: ".($event->task->description ?? 'No Description'). ' Mutex:'.($event->task->mutexName() ?? 'NoMutexname'));

       // Log::error(serialize($event->task->output));

    }

    public function handleScheduledTaskSkipped(ScheduledTaskSkipped $event): void
    {
        Log::error('handleScheduledTaskSkipped');
        Log::error("Scheduled Task Skipped: ".($event->task->description ?? 'No Description'). ' Mutex:'.($event->task->mutexName() ?? 'NoMutexname'));

       // Log::error(serialize($event->task->output));

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
