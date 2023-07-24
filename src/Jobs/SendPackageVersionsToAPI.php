<?php

namespace Larawatch\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Larawatch\Traits\GeneratesPackageVersions;

class SendPackageVersionsToAPI
{
    use Dispatchable;
    use SerializesModels;
    use InteractsWithQueue;
    use Queueable;
    use GeneratesPackageVersions;

    public function __construct()
    {
        $this->generateData();
    }

    public function handle()
    {
        $laraWatch = app('larawatch');
        $laraWatch->logStats('updateinstalledpackages', $this->dataArray);
    }
}
