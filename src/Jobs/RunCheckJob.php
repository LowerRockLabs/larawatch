<?php

namespace Larawatch\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Larawatch\Traits\Checks\{ManagesCheckRuns, RunsConsoleChecks, SetsUpChecklist, StoresResultsInDatabase};
use Larawatch\Checks\BaseCheck;

class RunCheckJob 
{
    protected $check;

    public function __construct(BaseCheck $check)
    {
        $this->check = $check;

    }

    public function handle()
    {
        $this->executeCheck($this->check);

    }


}
