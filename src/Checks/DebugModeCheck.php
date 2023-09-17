<?php

namespace Larawatch\Checks;

use function config;

class DebugModeCheck extends BaseCheck
{
    protected bool $expected = false;

    public function expectedToBe(bool $bool): self
    {
        $this->expected = $bool;

        return $this;
    }

    public function run(): CheckResult
    {
        $this->setStartTime(null);

        $actual = config('app.debug');

        $result = CheckResult::make()
            ->startTime($this->getStartTime())
            ->resultData([
                'actual' => $actual,
                'expected' => $this->expected,
            ])
            ->resultMessage($this->convertToWord($actual));
            
        return $result->ok();
    }

    protected function convertToWord(bool $boolean): string
    {
        return $boolean ? 'true' : 'false';
    }
}
