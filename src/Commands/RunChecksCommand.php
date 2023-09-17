<?php

namespace Larawatch\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Larawatch\Traits\Checks\RunsChecks;

class RunChecksCommand extends Command
{
    use RunsChecks;

    public $signature = 'larawatch:runchecks';

    public $description = 'Runs Larawatch Checks';

    public function handle()
    {
       
        $this->generateChecklist();
        $this->executeChecks();
        
    }

}
