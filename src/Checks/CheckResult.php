<?php

namespace Larawatch\Checks;

use Carbon\CarbonInterface;
use Carbon\Carbon;

class CheckResult
{
    public array $resultData = [];

    public string $resultMessage = '';
    
    public string $resultStatus;

    public BaseCheck $check;

    public ?CarbonInterface $ended_at;

    public ?Carbon $started_at;

    public static function make(string $resultMessage = '', Carbon $started_at = null): self
    {
        return new self('ok', $resultMessage, $started_at);
    }

    public function __construct(
        string $resultStatus = '',
        string $resultMessage = '',
        Carbon $started_at = null,
    ) {
        $this->resultStatus = $resultStatus ?? 'ok';
        $this->started_at = $started_at ?? Carbon::now();
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
        $resultDetails = collect($this->getResultDetails)
            ->filter(function ($item) {
                return is_scalar($item);
            })->toArray();
        
        return [$this->resultMessage, $resultDetails];
    }

    public function ok(string $resultMessage = ''): self
    {
        $this->resultMessage = $resultMessage;

        $this->resultStatus = 'ok';

        return $this;
    }

    public function warning(string $resultMessage = ''): self
    {
        $this->resultMessage = $resultMessage;

        $this->resultStatus = 'warning';

        return $this;
    }

    public function failed(string $resultMessage = ''): self
    {
        $this->resultMessage = $resultMessage;

        $this->resultStatus = 'failed';

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
