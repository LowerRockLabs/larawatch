<?php

namespace Larawatch\Checks;

use function app;

class EnvironmentCheck extends BaseCheck
{
    public function run(): CheckResult
    {
        $this->setStartTime(null);

        $result = CheckResult::make()
            ->startTime($this->getStartTime())
            ->resultData([
                'actual' => (string) app()->environment(),
            ]);
            
        return $result->ok();
    }
}
