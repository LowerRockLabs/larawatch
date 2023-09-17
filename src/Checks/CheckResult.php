<?php

namespace Larawatch\Checks;

use Larawatch\Traits\Core\{ProvidesAccessData, ProvidesCheckData, ProvidesErrorMessages, ProvidesResultData, ProvidesResultMessage, ProvidesResultStatus, ProvidesTimings};
use Carbon\Carbon;

class CheckResult
{
    use ProvidesAccessData;
    use ProvidesCheckData;
    use ProvidesErrorMessages;
    use ProvidesResultData;
    use ProvidesResultMessage;
    use ProvidesResultStatus;
    use ProvidesTimings;

    public BaseCheck $check;


    public static function make(string $resultMessage = '', Carbon $started_at = null): self
    {
        return new self('ok', $resultMessage, $started_at);
    }

    public function __construct(
        string $resultStatus = '',
        string $resultMessage = '',
        Carbon $started_at = null,
    ) {
        $this->setResultStatus($resultStatus ?? 'ok');
        $this->setStartTime($started_at ?? null);
    }


    public function check(BaseCheck $check): self
    {
        $this->check = $check;

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
        $this->setResultMessage($resultMessage);
        $this->setResultStatus('ok');

        return $this;
    }

    public function pending(): self
    {
        $this->setResultMessage('');
        $this->setResultStatus('pending');

        return $this;
    }

    public function passed(string $resultMessage = ''): self
    {
        $this->setResultMessage($resultMessage);
        $this->setResultStatus('passed');

        return $this;
    }

    public function warning(string $resultMessage = ''): self
    {
        $this->setResultMessage($resultMessage);
        $this->setResultStatus('warning');


        return $this;
    }

    public function failed(string $resultMessage = ''): self
    {
        $this->setResultMessage($resultMessage);
        $this->setResultStatus('failed');
        return $this;
    }

}
