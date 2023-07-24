<?php

namespace Larawatch\Larawatch\Commands;

use Illuminate\Console\Command;
use Larawatch\Larawatch\Support\ScheduledTasks\ScheduledTasks;
use function Termwind\render;
use function Termwind\style;

class ListCommand extends Command
{
    public $signature = 'larawatch:list';

    public $description = 'Display monitored scheduled tasks';

    public function handle()
    {
        $dateFormat = config('larawatch.date_format');
        style('date-width')->apply('w-'.strlen(date($dateFormat)));

        render(view('larawatch::list', [
            'monitoredTasks' => ScheduledTasks::createForSchedule()->monitoredTasks(),
            'readyForMonitoringTasks' => ScheduledTasks::createForSchedule()->readyForMonitoringTasks(),
            'unnamedTasks' => ScheduledTasks::createForSchedule()->unnamedTasks(),
            'duplicateTasks' => ScheduledTasks::createForSchedule()->duplicateTasks(),
            'dateFormat' => $dateFormat,
        ]));
    }

}
