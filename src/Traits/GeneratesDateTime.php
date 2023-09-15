<?php

namespace Larawatch\Traits;

use DateTime;

trait GeneratesDateTime
{
    public DateTime $dateTime;

    public function generateDateTime(): DateTime
    {
        return new DateTime;
    }

    public function retryUntil(): DateTime
    {
        return now()->addMinutes(config('larawatch.lowerrocklabs.retry_job_for_minutes', 5))->toDateTime();
    }
}
