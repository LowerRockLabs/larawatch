<?php

namespace Larawatch\Traits;

use Composer\InstalledVersions;

trait GeneratesPackageVersions
{
    use GeneratesDateTime;

    public array $dataArray;

    public array $packageVersions;

    public function generateData(): void
    {
        $this->dateTime = $this->generateDateTime();
        $this->packageVersions = $this->generatePackageList();
        $this->dataArray = $this->getDataArray();
    }

    public function generatePackageList(): array
    {
        $composerInstance = new InstalledVersions;

        return $composerInstance->getAllRawData()[0]['versions'];
    }

    public function getDataArray(): array
    {
        return [
            'event_datetime' => $this->dateTime ?? $this->generateDateTime(),
            'installed_packages_rawdata' => $this->packageVersions,
        ];
    }
}
