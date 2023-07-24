<?php

namespace Larawatch\Larawatch\Jobs;

use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class SendSoftwareVersionsToAPI
{
    use Dispatchable;
    use SerializesModels;
    use InteractsWithQueue;
    use Queueable;

    public DateTime $dateTime;

    public function __construct()
    {
        $this->dateTime = new DateTime;

    }

    public function handle()
    {

        $this->dataArray = [
            'project_key' => config('larawatch.project_key'),
            'server_key' => config('larawatch.server_key'),
            'event_datetime' => $this->dateTime,
            'php_extensions' => get_loaded_extensions(),
        ];

        $response = Http::withToken(config('larawatch.destination_token'))->retry(3, 10 * 1000)->post(config('larawatch.base_url').'', $this->dataArray);
    }

    public function retryUntil(): DateTime
    {
        return now()->addMinutes(config('larawatch.lowerrocklabs.retry_job_for_minutes', 5))->toDateTime();
    }
}
