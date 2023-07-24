<?php

namespace Larawatch\Support\Concerns;

use function app;
use Larawatch\Models\MonitoredScheduledTask;
use Larawatch\Models\MonitoredScheduledTaskLogItem;

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
