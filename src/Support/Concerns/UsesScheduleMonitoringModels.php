<?php

namespace Larawatch\Larawatch\Support\Concerns;

use function app;
use Larawatch\Larawatch\Models\MonitoredScheduledTask;
use Larawatch\Larawatch\Models\MonitoredScheduledTaskLogItem;

trait UsesScheduleMonitoringModels
{
    public function getMonitoredScheduleTaskModel(): MonitoredScheduledTask
    {
        return app(MonitoredScheduledTask::class);
    }

    public function getMonitoredScheduleTaskLogItemModel(): MonitoredScheduledTaskLogItem
    {
        return app(MonitoredScheduledTaskLogItem::class);
    }
}
