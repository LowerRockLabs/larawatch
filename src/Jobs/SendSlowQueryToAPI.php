<?php

namespace Larawatch\Jobs;

use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSlowQueryToAPI
{
    use Dispatchable;
    use SerializesModels;
    use InteractsWithQueue;
    use Queueable;

    public DateTime $dateTime;

    public QueryExecuted $slowQueryEvent;

    public function __construct(QueryExecuted $slowQueryEvent)
    {
        $this->dateTime = new DateTime;
        $this->slowQueryEvent = $slowQueryEvent;

    }

    public function handle()
    {

        $dataArray = [
            'event_datetime' => $this->dateTime,
            'slow_query_event' => $this->slowQueryEvent
        ];
        $laraWatch = app('larawatch');
        $laraWatch->logStats('slowquery', $dataArray);
    }

    public function retryUntil(): DateTime
    {
        return now()->addMinutes(config('larawatch.lowerrocklabs.retry_job_for_minutes', 10))->toDateTime();
    }
}
