<?php

namespace Larawatch\Commands;

use Illuminate\Console\Command;
use Larawatch\Jobs\SendPackageVersionsToAPI;
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
        $checkList[] = (new \Larawatch\Checks\DatabaseCheck());
        $checkList[] = (new \Larawatch\Checks\DebugModeCheck());
        $checkList[] = (new \Larawatch\Checks\CacheCheck());

        $checks = collect($checkList)->map(function (\Larawatch\Checks\BaseCheck $check): array {
            return [$check->getName() => [$check->shouldRun()
                ? $this->runCheck($check)
                : (new \Larawatch\Checks\CheckResult('skipped'))->check($check)->endedAt(now())]];
        });
        $fileStore = new \Larawatch\Checks\Stores\FileStore(config('larawatch.checks.diskName', 'local'), config('larawatch.checks.folderPath','larawatch'));
        $fileStore->save($checks);

        
    }

    public function runCheck(\Larawatch\Checks\BaseCheck $check): \Larawatch\Checks\CheckResult
    {
      // event(new CheckStartingEvent($check));

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

        $result
            ->check($check)
            ->endedAt(now());

        $this->outputResult($result, $exception ?? null);

       // event(new CheckEndedEvent($check, $result));

        return $result;
    }

    protected function outputResult(\Larawatch\Checks\CheckResult $result, Exception $exception = null): void
    {
        $status = ucfirst((string) $result->status);

        $okMessage = $status;

        if (! empty($result->resultMessage)) {
            $okMessage .= ": {$result->resultMessage}";
        }

    }


}
