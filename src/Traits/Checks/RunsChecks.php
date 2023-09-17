<?php

namespace Larawatch\Traits\Checks;

use Exception;
use Illuminate\Support\Facades\Log;
use Larawatch\Exceptions\Checks\CheckDidNotCompleteException;
use Larawatch\Traits\Checks\{FindsDatabases, FindsFileSystems};
use Larawatch\Jobs\SendPackageVersionsToAPI;
use Larawatch\Jobs\SendDataToLarawatch;
use Ramsey\Uuid\Uuid;

trait RunsChecks
{
    use FindsFileSystems;
    use FindsDatabases;

    protected array $checkList = [];


    /** @var array<int, Exception> */
    protected array $thrownExceptions = [];
    
    protected $dbStore;

    protected string $check_run_id = '';

    protected function generateChecklist()
    {
        $this->check_run_id = Uuid::uuid7()->toString();
        /** 
         *  Get Databases To Check
        */
        $databasesToCheck = $this->determineDatabasesToCheck();

        /**
         * Configure File Systems To Check
         */
        $this->addFileSystemChecks();

       /* if (is_array($fileSystemsToCheck['cloud'])) {
            foreach ($fileSystemsToCheck['cloud'] as $fileSystemName => $fileSystemDetails)
            {
                $checkList[] = (new \Larawatch\Checks\CloudStorageCheck(fileSystemName: $fileSystemName));    
            }
        }

        if (is_array($fileSystemsToCheck['local'])) {
            foreach ($fileSystemsToCheck['local'] as $fileSystemName => $fileSystemDetails)
            {
                $checkList[] = (new \Larawatch\Checks\LocalDiskSpaceCheck(fileSystemName: $fileSystemName, fileSystemPath: $fileSystemDetails['root']));    
                $checkList[] = (new \Larawatch\Checks\LocalDiskPerformanceCheck(fileSystemName: $fileSystemName, fileSystemPath: $fileSystemDetails['root']));                    
            }
        }*/


        $this->checkList[] = (new \Larawatch\Checks\AppOptimizedCheck());
        $this->checkList[] = (new \Larawatch\Checks\CacheCheck());
        $this->checkList[] = (new \Larawatch\Checks\DebugModeCheck());
        $this->checkList[] = (new \Larawatch\Checks\DatabaseCheck(connectionsToCheck: $databasesToCheck));
        $this->checkList[] = (new \Larawatch\Checks\EnvironmentCheck());
        $this->checkList[] = (new \Larawatch\Checks\InstalledPackageCheck());
        $this->checkList[] = (new \Larawatch\Checks\InstalledSoftwareCheck());
        $this->checkList[] = (new \Larawatch\Checks\RepoVersionCheck());
    

    }

    protected function executeChecks()
    {

        if(config('larawatch.checks.storage') == 'file')
        {
            $checks = collect($this->checkList)->map(function ($check): array {
                return ($check->shouldRun() ? [$check->getName() => [$this->runCheck($check)]] : [$check->getName() => [$check->markAsSkipped()]]);
            });
    
    
            $fileStore = new \Larawatch\Checks\Stores\FileStore(config('larawatch.checks.diskName', 'local'), config('larawatch.checks.folderPath','larawatch'));
            $newFileName = $fileStore->save($checks);
            SendDataToLarawatch::dispatch($newFileName);
            Log::error('NewFileName:'.$newFileName);
        }
        else if(config('larawatch.checks.storage') == 'database')
        {
            $dbStore = new \Larawatch\Checks\Stores\DatabaseStore();

            $dbStore->checkRunID($this->check_run_id);

            foreach ($this->checkList as $check)
            {
                if ($check->shouldRun())
                {
                    $dbStore->setCheckName($check->getName() ?? 'unknown');

                    try {
                        $dbStore->storeCheck($this->runCheck($check));
                    } 
                    catch (\Exception $exception)
                    {
                        report($exception);
                        $dbStore->storeCheck($check->markAsCrashed());
                    }
                }                     
                else
                {
                    $dbStore->storeCheck($check->markAsSkipped());

                 }

            }
            //$storedResults = $dbStore->saveCheck($checks);
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
