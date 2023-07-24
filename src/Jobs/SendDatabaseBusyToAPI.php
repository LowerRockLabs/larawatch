<?php

namespace Larawatch\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Larawatch\Traits\GeneratesDateTime;

class SendDatabaseBusyToAPI
{
    use Dispatchable;
    use SerializesModels;
    use InteractsWithQueue;
    use Queueable;
    use GeneratesDateTime;

    public function __construct()
    {
        $this->dateTime = $this->generateDateTime();
    }

    public function handle()
    {
        $dataArray = [
            'event_datetime' => $this->dateTime ?? $this->generateDateTime(),
        ];

        $laraWatch = app('larawatch');
        $laraWatch->logStats('dbstatsupdate', $dataArray);

    }
}
