<?php

namespace Larawatch\Commands;

use Illuminate\Console\Command;
use Larawatch\Support\Concerns\UsesScheduleMonitoringModels;
use Larawatch\Support\ScheduledTasks\ScheduledTasks;
use Larawatch\Support\ScheduledTasks\Tasks\Task;
use function Termwind\render;

class SyncCommand extends Command
{
    use UsesScheduleMonitoringModels;

    public $signature = 'larawatch:sync';

    public $description = 'Sync the schedule of the app with the schedule monitor';

    public function handle()
    {
        render(view('larawatch::alert', [
            'message' => 'Start syncing schedule...',
            'class' => 'text-green',
        ]));

        $this->syncScheduledTasksWithDatabase();

        $monitoredScheduledTasksCount = $this->getMonitoredScheduleTaskModel()->count();

        render(view('larawatch::sync', [
            'monitoredScheduledTasksCount' => $monitoredScheduledTasksCount,
        ]));
    }

    protected function syncScheduledTasksWithDatabase(): self
    {
        render(view('larawatch::alert', [
            'message' => 'Start syncing schedule with database...',
        ]));

        $monitoredScheduledTasks = ScheduledTasks::createForSchedule()
            ->uniqueTasks()
            ->map(function (Task $task) {
                return $this->getMonitoredScheduleTaskModel()->updateOrCreate(
                    ['name' => $task->name()],
                    [
                        'type' => $task->type(),
                        'cron_expression' => $task->cronExpression(),
                        'timezone' => $task->timezone(),
                        'grace_time_in_minutes' => $task->graceTimeInMinutes(),
                    ]
                );
            });

        $this->getMonitoredScheduleTaskModel()->query()
            ->whereNotIn('id', $monitoredScheduledTasks->pluck('id'))
            ->delete();

        return $this;
    }
}
