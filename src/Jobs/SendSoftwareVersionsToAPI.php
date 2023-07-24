<?php

namespace Larawatch\Jobs;

use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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

        $dataArray = [
            'event_datetime' => $this->dateTime,
            'php_extensions' => get_loaded_extensions(),
        ];
        $laraWatch = app('larawatch');
        $laraWatch->logStats('', $dataArray);
    }

    public function retryUntil(): DateTime
    {
        return now()->addMinutes(config('larawatch.lowerrocklabs.retry_job_for_minutes', 5))->toDateTime();
    }
}
