<?php

namespace Larawatch\Support\LowerRockLabs\Payloads;

use Larawatch\Models\MonitoredScheduledTaskLogItem;

abstract class Payload
{
    protected MonitoredScheduledTaskLogItem $logItem;

    protected string $taskname;

    protected ?string $tasklogitem_id = null;

    abstract public static function canHandle(MonitoredScheduledTaskLogItem $logItem): bool;

    public function __construct(MonitoredScheduledTaskLogItem $logItem)
    {
        $this->logItem = $logItem;
        $this->taskname = (! is_null($logItem->monitoredScheduledTask) ? $logItem->monitoredScheduledTask->name : 'Unknown');
        $this->tasklogitem_id = (! is_null($this->logItem->lrl_id) ? $this->logItem->lrl_id : null);

    }

    abstract public function url();

    abstract public function data();

    protected function baseUrl(): string
    {
        return $this->logItem->monitoredScheduledTask->ping_url;
    }
}
