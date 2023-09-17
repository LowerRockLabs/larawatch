<?php

namespace Larawatch\Traits\Core;

use Carbon\CarbonInterface;
use Carbon\Carbon;

trait ProvidesTimings
{

    public ?Carbon $started_at;
    public ?CarbonInterface $ended_at;

    public function startedAt(Carbon $started_at = null): self
    {
        $this->setStartTime($started_at);

        return $this;
    }

    public function startTime(?Carbon  $started_at = null): self
    {
        $this->setStartTime($started_at);

        return $this;
    }

    public function setStartTime(?Carbon $started_at = null): void
    {
        $this->started_at = $started_at ?? Carbon::now();
    }

    public function getStartTime(): Carbon
    {
        return $this->started_at ?? Carbon::now();
    }

    public function endedAt(CarbonInterface $ended_at): self
    {
        $this->setEndTime($ended_at);

        return $this;
    }

    public function endTime(CarbonInterface $ended_at): self
    {
        $this->setEndTime($ended_at);

        return $this;
    }

    public function setEndTime(CarbonInterface $ended_at): void
    {
        $this->ended_at = $ended_at;
    }

    public function getEndTime(): CarbonInterface|Carbon
    {
        return $this->ended_at ?? Carbon::now();
    }



}
