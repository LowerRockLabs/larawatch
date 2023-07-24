<?php

namespace Larawatch\Jobs;

use Composer\InstalledVersions;
use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendPackageVersionsToAPI
{
    use Dispatchable;
    use SerializesModels;
    use InteractsWithQueue;
    use Queueable;

    public DateTime $dateTime;

    public array $installedPackages;

    public function __construct()
    {
        $this->dateTime = new DateTime;

    }

    public function handle()
    {
        $composerInstance = new InstalledVersions;

        $dataArray = [
            'event_datetime' => $this->dateTime,
            'installed_packages_rawdata' => $composerInstance->getAllRawData()[0]['versions'],
        ];
        $laraWatch = app('larawatch');
        $laraWatch->logStats('updateinstalledpackages', $dataArray);

    }

    public function retryUntil(): DateTime
    {
        return now()->addMinutes(config('larawatch.lowerrocklabs.retry_job_for_minutes', 5))->toDateTime();
    }
}
