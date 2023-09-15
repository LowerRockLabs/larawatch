<?php

namespace Larawatch\Checks;

use function app;

class EnvironmentCheck extends BaseCheck
{
    public function run(): CheckResult
    {

        $result = CheckResult::make(started_at: $this->checkStartTime)
            ->resultData([
                'actual' => (string) app()->environment(),
            ]);
            
        return $result->ok();
    }
}
