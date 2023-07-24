<?php

namespace Larawatch\Commands;

use Illuminate\Console\Command;
use Larawatch\Jobs\SendPackageVersionsToAPI;

class SendPackageDetailsCommand extends Command
{
    public $signature = 'larawatch:updatepackages';

    public $description = 'Send details of the currently installed packages';

    public function handle()
    {
        SendPackageVersionsToAPI::dispatch();
    }
}
