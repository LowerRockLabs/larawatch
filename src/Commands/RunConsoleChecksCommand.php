<?php

namespace Larawatch\Commands;

use Illuminate\Console\Command;
use Exception;
use Illuminate\Support\Facades\Log;
use Larawatch\Traits\Checks\{ManagesCheckRuns, RunsConsoleChecks, SetsUpChecklist, StoresResultsInDatabase};


class RunConsoleChecksCommand extends Command
{
    use ManagesCheckRuns;
    use SetsUpChecklist;
    use RunsConsoleChecks;
    use StoresResultsInDatabase;

    public $signature = 'larawatch:runconsolechecks';

    public $description = 'Runs Larawatch Console Checks';

    public function handle()
    {
        $this->createCheckRun();
        $this->generateChecklist();
        $this->createsDatabaseStoreInstance();
        $this->removeSkippedChecks();
        
        foreach ($this->checkList as $check)
        {
            $this->executeCheck($check);
        }
        
        
    }

}
