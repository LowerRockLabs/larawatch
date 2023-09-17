<?php

namespace Larawatch\Commands;

use Illuminate\Console\Command;
use Exception;
use Illuminate\Support\Facades\Log;
use Larawatch\Models\LarawatchCheck;

class SendChecksToLarawatchCommand extends Command
{
    public $signature = 'larawatch:sendtolarawatch';

    public $description = 'Sends DB Results to Larawatch';

    public function handle()
    {
        $locallyStoredChecks = LarawatchCheck::get()->toArray();

        $laraWatch = app('larawatch');
        $laraWatch->logCheckArray('processinbounddbcheck', $locallyStoredChecks);

        

    }


}
