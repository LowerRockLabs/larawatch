<?php

namespace Larawatch\Checks;

use Composer\InstalledVersions;

class InstalledPackageCheck extends BaseCheck
{
    protected string $expression = '* * * * *';

    public function run(): CheckResult
    {      
        $this->setStartTime(null);

        $result = CheckResult::make()
            ->startTime($this->getStartTime())
            ->resultData([
                'installed_packages' => $this->getInstalledPackages(),
            ]);

        return $result->ok();
    }

    protected function getInstalledPackages(): array
    {
        $composerInstance = new InstalledVersions;

        return $composerInstance->getAllRawData()[0]['versions'];

    }

}
