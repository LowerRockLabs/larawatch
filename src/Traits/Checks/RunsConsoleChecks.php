<?php

namespace Larawatch\Traits\Checks;

use Exception;
use Illuminate\Support\Facades\Log;
use Larawatch\Exceptions\Checks\CheckDidNotCompleteException;
use Ramsey\Uuid\Uuid;

trait RunsConsoleChecks
{
    /** @var array<int, Exception> */
    protected array $thrownExceptions = [];


    protected function executeCheck(\Larawatch\Checks\BaseCheck $check)
    {
        $this->dbStore->setCheckName($check->getName() ?? 'unknown');

        try {
            $runCheck = $this->runCheck($check);
            $this->dbStore->storeCheck($runCheck);
        } 
        catch (\Exception $exception)
        {
            report($exception);
            $this->dbStore->storeCheck($check->markAsCrashed());
        }
    }

    public function runCheck(\Larawatch\Checks\BaseCheck $check): \Larawatch\Checks\CheckResult
    {
      // event(new CheckStartingEvent($check));
        sleep(rand(0,5));
        try {
            $this->line('');
            $this->line("Running check: {$check->getName()}...");
            $result = $check->run();

        } catch (Exception $exception) {
            $exception = CheckDidNotCompleteException::make($check, $exception);
            report($exception);

            $this->thrownExceptions[] = $exception;

            $result = $check->markAsCrashed();
        }

        $result->endTime(now())
            ->check($check);

        $this->outputResult($result, $exception ?? null);

       // event(new CheckEndedEvent($check, $result));

        return $result;
    }

    protected function outputResult(\Larawatch\Checks\CheckResult $result, Exception $exception = null): void
    {
        $resultStatus = ucfirst((string) $result->getResultStatus());

        $okMessage = $resultStatus;

        if (! empty($result->getResultMessage())) {
            $okMessage .= ": {$result->getResultMessage()}";
        }

    }

}
