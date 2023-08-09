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
        foreach (config('database.connections') as $connectionName => $connectionData)
        {
            $this->line('');
            $this->line($connectionName);

            if (in_array($connectionData['driver'], ['mysql', 'pgsql']))
            {
                $this->line('Should Run: '.$connectionData['driver']);
                $checkList[] = new \Larawatch\Checks\DatabaseCheck(connectionName: $connectionName);

            }
        }
        
        $checkList[] = (new \Larawatch\Checks\AppOptimizedCheck());
        $checkList[] = (new \Larawatch\Checks\InstalledPackageCheck());
        $checkList[] = (new \Larawatch\Checks\InstalledSoftwareCheck());
        $checkList[] = (new \Larawatch\Checks\DebugModeCheck());
        $checkList[] = (new \Larawatch\Checks\CacheCheck());

        $checks = collect($checkList)->map(function ($check): array {
            return ($check->shouldRun() ? [$check->getName() => [$this->runCheck($check)]] : [$check->getName() => [$check->markAsSkipped()]]);
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
        $resultStatus = ucfirst((string) $result->resultStatus);

        $okMessage = $resultStatus;

        if (! empty($result->resultMessage)) {
            $okMessage .= ": {$result->resultMessage}";
        }

    }


}
