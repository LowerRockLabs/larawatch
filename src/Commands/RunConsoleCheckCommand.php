<?php

namespace Larawatch\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Larawatch\Traits\Checks\RunsChecks;
use Exception;
use Illuminate\Support\Facades\Log;
use Larawatch\Traits\Checks\{ManagesCheckRuns, SetsUpChecklist, RunsChecks};


class RunConsoleCheckCommand extends Command
{
    use ManagesCheckRuns;
    use SetsUpChecklist;
    use RunsChecks;
    use RunsConsoleChecks;

    public $signature = 'larawatch:runchecks';

    public $description = 'Runs Larawatch Checks';

    public function handle()
    {
        $this->createCheckRun();
        $this->generateChecklist();
        $this->executeChecks();
        
    }

}
