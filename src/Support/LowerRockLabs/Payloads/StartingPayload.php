<?php

namespace Larawatch\Support\LowerRockLabs\Payloads;

use Larawatch\Models\MonitoredScheduledTaskLogItem;

class StartingPayload extends Payload
{
    public static function canHandle(MonitoredScheduledTaskLogItem $logItem): bool
    {

        return $logItem->type === MonitoredScheduledTaskLogItem::TYPE_STARTING;
    }

    public function url()
    {
        return "{$this->baseUrl()}/starting";
    }

    public function data(): array
    {
        return [
            'taskname' => $this->taskname ?? 'Unknown',
            'memory' => $this->logItem->meta['memory'],
            'tasklogitem_id' => $this->tasklogitem_id ?? null,

        ];
    }
}
