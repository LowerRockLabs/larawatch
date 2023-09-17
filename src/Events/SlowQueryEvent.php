<?php

namespace Larawatch\Events;

use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\EventMutex;

class SlowQueryEvent extends Event
{
    public string $lrl_id;

    /**
     * Create a new event instance.
     *
     * @param  string  $command
     * @param  \DateTimeZone|string|null  $timezone
     * @return void
     */
    public function __construct(EventMutex $mutex, $command, $timezone = null)
    {
        $this->mutex = $mutex;
        $this->command = $command;
        $this->timezone = $timezone;

        $this->output = $this->getDefaultOutput();
        parent::__construct();
    }
}
