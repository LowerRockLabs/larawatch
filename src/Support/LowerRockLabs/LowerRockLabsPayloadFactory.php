<?php

namespace Larawatch\Support\LowerRockLabs;

use Larawatch\Models\MonitoredScheduledTaskLogItem;
use Larawatch\Support\LowerRockLabs\Payloads\FailedPayload;
use Larawatch\Support\LowerRockLabs\Payloads\FinishedPayload;
use Larawatch\Support\LowerRockLabs\Payloads\Payload;
use Larawatch\Support\LowerRockLabs\Payloads\StartingPayload;

class LowerRockLabsPayloadFactory
{
    public static function createForLogItem(MonitoredScheduledTaskLogItem $logItem): ?Payload
    {
        $payloadClasses = [
            StartingPayload::class,
            FailedPayload::class,
            FinishedPayload::class,
        ];

        $payloadClass = collect($payloadClasses)
            ->first(fn (string $payloadClass) => $payloadClass::canHandle($logItem));

        if (! $payloadClass) {
            return null;
        }

        return new $payloadClass($logItem);
    }
}
