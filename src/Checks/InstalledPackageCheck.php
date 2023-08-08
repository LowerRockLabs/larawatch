<?php

namespace Larawatch\Checks;

use Composer\InstalledVersions;

class InstalledPackageCheck extends BaseCheck
{
    protected string $expression = '* * * * *';

    public function run(): CheckResult
    {      

        $result = CheckResult::make(started_at: $this->checkStartTime)
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
