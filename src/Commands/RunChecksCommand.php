<?php

namespace Larawatch\Commands;

use Illuminate\Console\Command;
use Larawatch\Jobs\SendPackageVersionsToAPI;
use Larawatch\Checks\DatabaseCheck;
use Larawatch\Checks\DebugModeCheck;
use Larawatch\Checks\CheckResult;
use Larawatch\Checks\BaseCheck;
use Larawatch\Checks\Stores\FileStore;
use Larawatch\Exceptions\Checks\CheckDidNotCompleteException;
use Illuminate\Support\Facades\Log;

class RunChecksCommand extends Command
{
    public $signature = 'larawatch:runchecks';

    public $description = 'Send details of the currently installed packages';

    /** @var array<int, Exception> */
    protected array $thrownExceptions = [];

    public function handle()
    {
        $checkList[] = (new DatabaseCheck());
        $checkList[] = (new DebugModeCheck());
        $checks = collect($checkList)->map(function (BaseCheck $check): array {
            return [$check->getName() => $check->shouldRun()
                ? $this->runCheck($check)
                : (new CheckResult('skipped'))->check($check)->endedAt(now())];
        });
        $fileStore = new FileStore('local', 'larawatch-checks.json');
        $fileStore->save($checks);

        
    }

    public function runCheck(BaseCheck $check): CheckResult
    {
      // event(new CheckStartingEvent($check));

        try {
            $this->line('');
            $this->line("Running check: {$check->getName()}...");
            $result = $check->run();
        } catch (Exception $exception) {
            $exception = CheckDidNotComplete::make($check, $exception);
            report($exception);

            $this->thrownExceptions[] = $exception;

            $result = $check->markAsCrashed();
        }

        $result
            ->check($check)
            ->endedAt(now());

        $this->outputResult($result, $exception ?? null);

       // event(new CheckEndedEvent($check, $result));

        return $result;
    }

    protected function outputResult(CheckResult $result, Exception $exception = null): void
    {
        $status = ucfirst((string) $result->status);

        $okMessage = $status;

        if (! empty($result->resultMessage)) {
            $okMessage .= ": {$result->resultMessage}";
        }

    }


}
