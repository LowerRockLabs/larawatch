<?php

namespace Larawatch\Traits\Provider;

use Larawatch\Events\SchedulerEvent;

trait ManagesSchedulerMacros
{
    public function addSchedulerMacros()
    {
        SchedulerEvent::macro('monitorName', function (string $monitorName) {
            $this->monitorName = $monitorName;

            return $this;
        });

        SchedulerEvent::macro('graceTimeInMinutes', function (int $graceTimeInMinutes) {
            $this->graceTimeInMinutes = $graceTimeInMinutes;

            return $this;
        });

        SchedulerEvent::macro('doNotMonitor', function (bool $bool = true) {
            $this->doNotMonitor = $bool;

            return $this;
        });

        SchedulerEvent::macro('storeOutputInDb', function () {
            $this->storeOutputInDb = true;
            $this->testUuid = '123475812';

            /** @psalm-suppress UndefinedMethod */
            $this->ensureOutputIsBeingCaptured();

            return $this;
        });
    }
}
