<?php

namespace Larawatch\Jobs;

use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Larawatch\Models\MonitoredScheduledTaskLogItem;
use Larawatch\Support\Concerns\UsesScheduleMonitoringModels;
use Larawatch\Support\LowerRockLabs\LowerRockLabsPayloadFactory;

class SendScheduledJobUpdateToAPI
{
    public $deleteWhenMissingModels = true;

    use Dispatchable;
    use SerializesModels;
    use InteractsWithQueue;
    use Queueable;
    use UsesScheduleMonitoringModels;

    public MonitoredScheduledTaskLogItem $logItem;

    public function __construct(MonitoredScheduledTaskLogItem $logItem)
    {
        $this->logItem = $logItem;

    }

    public function handle()
    {
        if (! $payload = LowerRockLabsPayloadFactory::createForLogItem($this->logItem)) {
            return;
        }

        $response = Http::withToken(config('larawatch.destination_token'))->retry(3, 10 * 1000)->post(config('larawatch.base_url').'logscheduledtasks', $payload->data())->throw()->json();

        $this->logItem->lrl_id = $response['tasklogitem_id'] ?? null;
        $this->logItem->save();

        $this->logItem->monitoredScheduledTask->update(['last_pinged_at' => now()]);
    }

    public function retryUntil(): DateTime
    {
        return now()->addMinutes(config('larawatch.lowerrocklabs.retry_job_for_minutes', 10))->toDateTime();
    }
}
