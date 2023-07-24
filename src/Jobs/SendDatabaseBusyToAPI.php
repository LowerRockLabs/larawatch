<?php

namespace Larawatch\Jobs;

use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendDatabaseBusyToAPI
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
        ];

        $laraWatch = app('larawatch');
        $laraWatch->logStats('dbstatsupdate', $dataArray);

    }

    public function retryUntil(): DateTime
    {
        return now()->addMinutes(config('larawatch.lowerrocklabs.retry_job_for_minutes', 10))->toDateTime();
    }
}
