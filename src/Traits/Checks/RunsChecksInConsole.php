<?php

namespace Larawatch\Traits\Checks;

use Exception;
use Illuminate\Support\Facades\Log;
use Larawatch\Traits\Checks\{ManagesCheckRuns, SetsUpChecklist};

trait RunsChecksInConsole
{
    use ManagesCheckRuns;
    use SetsUpChecklist;

    public function runChecksFromConsole()
    {
        $this->createCheckRun();
        $this->generateChecklist();
        
    }

}
