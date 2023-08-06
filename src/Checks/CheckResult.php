<?php

namespace Larawatch\Checks;

use Carbon\CarbonInterface;

class CheckResult
{
    public array $resultData = [];

    public string $resultMessage = '';
    
    public string $resultStatus;

    public BaseCheck $check;

    public ?CarbonInterface $ended_at;

    public static function make(string $resultMessage = ''): self
    {
        return new self('ok', $resultMessage);
    }

    public function __construct(
        string $resultStatus = '',
        string $resultMessage = '',
    ) {
    }


    public function check(BaseCheck $check): self
    {
        $this->check = $check;

        return $this;
    }

    public function resultMessage(string $resultMessage): self
    {
        $this->resultMessage = $resultMessage;

        return $this;
    }

    public function getResultDetails(): array
    {
        $getResultDetails = collect($this->getResultDetails)
            ->filter(function ($item) {
                return is_scalar($item);
            })->toArray();

        return [$this->resultMessage, $getResultDetails];
    }

    public function ok(string $resultMessage = ''): self
    {
        $this->resultMessage = $resultMessage;

        $this->status = 'ok';

        return $this;
    }

    public function warning(string $resultMessage = ''): self
    {
        $this->resultMessage = $resultMessage;

        $this->status = 'warning';

        return $this;
    }

    public function failed(string $resultMessage = ''): self
    {
        $this->resultMessage = $resultMessage;

        $this->status = 'failed';

        return $this;
    }

    public function resultData(array $resultData): self
    {
        $this->resultData = $resultData;

        return $this;
    }

    public function endedAt(CarbonInterface $carbon): self
    {
        $this->ended_at = $carbon;

        return $this;
    }
}
