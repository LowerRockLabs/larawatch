<?php

namespace Larawatch\Traits\Checks;


trait FindsFileSystems
{
    protected function getLWLocalFileSystems(): array
    {
        if (!empty(config('larawatch.checks.local_filesystems', [])))
        {
            return config('larawatch.checks.local_filesystems', []);
        }
        else
        {
            return collect(config('filesystems.disks'))->filter(function (array $value, string $key) {
                    return ($value['driver'] == "local");
                })->toArray();
        }
    }

    protected function getLWCloudFileSystems(): array
    {
        if (!empty(config('larawatch.checks.cloud_filesystems', [])))
        {
            return config('larawatch.checks.cloud_filesystems', []);
        }
        else
        {
            return collect(config('filesystems.disks'))->filter(function (array $value, string $key) {
                    return ($value['driver'] != "local");
                })->toArray();
        }
    }

    public function determineFileSystemsToCheck(): array
    {
        return ['local' => $this->getLWLocalFileSystems(), 'cloud' => $this->getLWCloudFileSystems()];
    }

    public function findLocalFileSystemsToCheck()
    {
        $localFileSystems = $this->getLWLocalFileSystems();
        if (!empty($localFileSystems)) {
            foreach ($localFileSystems as $localFileSystemName => $localFileSystemDetails)
            {
                $this->checkList[] = (new \Larawatch\Checks\LocalDiskSpaceCheck(fileSystemName: $localFileSystemName, fileSystemPath: $localFileSystemDetails['root']));    
                $this->checkList[] = (new \Larawatch\Checks\LocalDiskPerformanceCheck(fileSystemName: $localFileSystemName, fileSystemPath: $localFileSystemDetails['root']));                    
            }
        }
    }

    public function findCloudFileSystemsToCheck(): void
    {
        $cloudFileSystems = $this->getLWCloudFileSystems();

        if (!empty($cloudFileSystems)) {
            foreach ($cloudFileSystems as $cloudFileSystemName => $cloudFileSystemDetails)
            {
                $this->checkList[] = (new \Larawatch\Checks\CloudStorageCheck(fileSystemName: $cloudFileSystemName));    
            }
        }
    }

    public function addFileSystemChecks(): void
    {
        $this->findCloudFileSystemsToCheck();
        $this->findLocalFileSystemsToCheck();
    }

}
