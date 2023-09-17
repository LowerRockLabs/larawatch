<?php

namespace Larawatch\Traits\Commands;


trait ManagesFileSystemChecks
{
    protected function retrieveMonitoredLocalFileSystems(): array
    {
        $localFileSystems = $this->getConfiguredLocalFileSystems();
        $larawatchMonitors = $this->getLarawatchConfiguredFileSystems();

        
    }

    protected function getConfiguredLocalFileSystems(): array
    {
        return collect(config('filesystems.disks'))->filter(function (array $value, string $key) {
            return ($value['driver'] == "local");
        })->toArray();
    }

    protected function getLarawatchConfiguredFileSystems(): array
    {
        return config('larawatch.checks.local_filesystems', []);
    }

}
