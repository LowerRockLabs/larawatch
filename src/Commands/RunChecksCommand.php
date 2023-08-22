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
        if (count(config('larawatch.checks.databases_active')) > 0)
        {
            $databasesToCheck = config('larawatch.checks.databases_active');
        }
        else
        {
            $databasesToCheck = config('database.connections');
        }

        if (count(config('larawatch.checks.databases_ignore')) > 0)
        {
            foreach ($databasesToCheck as $dbCheckIndex => $dbCheckName)
            {
                if (in_array($dbCheckIndex, config('larawatch.checks.databases_ignore')))
                {
                    unset($databasesToCheck[$dbCheckIndex]);
                }
            }

        }


        foreach ($databasesToCheck as $connectionName => $connectionData)
        {
            if (!in_array($connectionData['driver'], ['mysql', 'pgsql']))
            {
                unset($databasesToCheck[$connectionName]);
            }
        }


        $checkList[] = (new \Larawatch\Checks\AppOptimizedCheck());
        $checkList[] = (new \Larawatch\Checks\CacheCheck());
        $checkList[] = (new \Larawatch\Checks\DebugModeCheck());
        $checkList[] = (new \Larawatch\Checks\DatabaseCheck(connectionsToCheck: $databasesToCheck));
        $checkList[] = (new \Larawatch\Checks\EnvironmentCheck());
        $checkList[] = (new \Larawatch\Checks\InstalledPackageCheck());
        $checkList[] = (new \Larawatch\Checks\InstalledSoftwareCheck());
        $checkList[] = (new \Larawatch\Checks\RepoVersionCheck());
    
        $fileSystemsToCheck = $this->determineFileSystemsToCheck();

        /**
         * Configure File Systems To Check
         */
        if (is_array($fileSystemsToCheck['cloud'])) {
            $checkList[] = (new \Larawatch\Checks\CloudStorageCheck($fileSystemsToCheck['cloud']));    
        }
        if (is_array($fileSystemsToCheck['local'])) {
            $checkList[] = (new \Larawatch\Checks\DiskSpaceCheck(fileSystemsToCheck: $fileSystemsToCheck['local']));    
        }

 

        $checks = collect($checkList)->map(function ($check): array {
            return ($check->shouldRun() ? [$check->getName() => [$this->runCheck($check)]] : [$check->getName() => [$check->markAsSkipped()]]);
        });
        $fileStore = new \Larawatch\Checks\Stores\FileStore(config('larawatch.checks.diskName', 'local'), config('larawatch.checks.folderPath','larawatch'));
        $fileStore->save($checks);

        
    }

    public function determineFileSystemsToCheck(): array
    {
        $fileSystemsToCheck = ['local' => [], 'cloud' => []];
        if (!empty(config('larawatch.checks.local_filesystems')))
        {
            $fileSystemsToCheck['local'] = config('larawatch.checks.local_filesystems');
        }
        if (!empty(config('larawatch.checks.cloud_filesystems')))
        {
            $fileSystemsToCheck['cloud'] = config('larawatch.checks.cloud_filesystems');
        }

        if (!empty($fileSystemsToCheck['local']) && !empty($fileSystemsToCheck['cloud']))
        {
            return $fileSystemsToCheck;
        }

        // Need to Get All Disks Into Collection
        $allConfiguredFileSystems =  $localConfiguredFileSystems = $cloudConfiguredFileSystems = collect(config('filesystems.disks'));

        // Filter Local

        $localConfiguredFileSystems = $localConfiguredFileSystems->filter(function (array $value, string $key) {

            return ($value['driver'] == "local");
        })->toArray();

        $cloudConfiguredFileSystems = $cloudConfiguredFileSystems->filter(function (array $value, string $key) {

            return ($value['driver'] != "local");
        })->toArray();



        $fileSystemsToCheck['local'] = $localConfiguredFileSystems;

        $fileSystemsToCheck['cloud'] = $cloudConfiguredFileSystems;


        return $fileSystemsToCheck;

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
