<?php

namespace Larawatch\Checks;

use Symfony\Component\Process\Process;

class InstalledSoftwareCheck extends BaseCheck
{
    protected string $expression = '0 * * * *';

    public function run(): CheckResult
    {      

        $result = CheckResult::make(started_at: $this->checkStartTime)
            ->resultData([
                'installed_software' => $this->getInstalledSoftware(),
            ]);

        return $result->ok();
    }

    protected function getRetrievalMethod(): string
    {
        return 'dpkg-query -l --no-pager';
    }

    protected function getInstalledSoftware(): array
    {
        $method = $this->getRetrievalMethod();

        $runProcess = Process::run($method);
        $softwareList =  explode("\n", $runProcess->output());
        $newSoftwareList = [];
        foreach ($softwareList as $softwareListIndex => $installedSoftware)
        {
            $newArray = explode("||", preg_replace("/\s\s+/", "||", $installedSoftware)); 
            if (isset($newArray[1]) && isset($newArray[2]))
            {
                $newSoftwareList[] = ['softwareName' => $newArray[1], 'softwareVersion' => $newArray[2]];
            }

        }
        return $newSoftwareList;
    }

}
