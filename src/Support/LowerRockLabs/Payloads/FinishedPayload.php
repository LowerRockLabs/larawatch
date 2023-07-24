<?php

namespace Larawatch\Support\LowerRockLabs\Payloads;

use Larawatch\Models\MonitoredScheduledTaskLogItem;

class FinishedPayload extends Payload
{
    public static function canHandle(MonitoredScheduledTaskLogItem $logItem): bool
    {
        return $logItem->type === MonitoredScheduledTaskLogItem::TYPE_FINISHED;
    }

    public function url()
    {
        return "{$this->baseUrl()}/finished";
    }

    public function data(): array
    {
        return [
            'runtime' => $this->logItem->meta['runtime'],
            'exit_code' => $this->logItem->meta['exit_code'],
            'memory' => $this->logItem->meta['memory'],
            'taskname' => $this->taskname ?? 'Unknown',
            'tasklogitem_id' => $this->tasklogitem_id ?? null,
        ];

    }
}
