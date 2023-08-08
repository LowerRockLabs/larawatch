<?php

namespace Larawatch\Checks;

use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Support\Facades\Process;

class InstalledSoftwareCheck extends BaseCheck
{
    protected string $expression = '0 * * * *';

    public function run(): CheckResult
    {      

        $result = CheckResult::make(started_at: $this->checkStartTime)
            ->resultData([
                'installed_packages' => $this->getInstalledPackages(),
            ]);

        return $result->ok();
    }

    protected function getRetrievalMethod(): string
    {
        return 'dpkg-query -l --no-pager';
    }

    protected function getInstalledPackages(): array
    {
        $method = $this->getRetrievalMethod();

        $runProcess = Process::run($method);
        $packageList =  explode("\n", $runProcess->output());
        $newPackageList = [];
        foreach ($packageList as $packageListIndex => $installedPackage)
        {
            $installedPackage = preg_replace("/\s\s+/", "||", $installedPackage); 
            $newArray = explode("||", $installedPackage);
            if (isset($newArray[1]) && isset($newArray[2]))
            {
                $newPackageList[] = ['packageName' => $newArray[1], 'packageVersion' => $newArray[2]];
            }

        }
        return $newPackageList;
    }

}
