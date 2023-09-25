<?php

namespace Larawatch\Commands;

use Illuminate\Console\Command;
use Exception;
use Illuminate\Support\Facades\Log;
use Larawatch\Models\LarawatchCheck;
use Larawatch\Traits\CheckResults\GetsCheckResults;

class SendChecksToLarawatchCommand extends Command
{
    use GetsCheckResults;

    public $signature = 'larawatch:sendtolarawatch';

    public $description = 'Sends DB Results to Larawatch';

    public function handle()
    {
        if ($this->getResultsFromLocalDB())
        {
            $laraWatch = app('larawatch');
            $laraWatch->logCheckArray('processinbounddbcheck', $this->locallyStoredChecks);
        }
    }


}
