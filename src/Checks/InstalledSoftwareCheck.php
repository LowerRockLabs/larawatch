<?php

namespace Larawatch\Checks;

use Illuminate\Support\Facades\Process;

class InstalledSoftwareCheck extends BaseCheck
{
    protected string $expression = '* * * * *';

    public function run(): CheckResult
    {      
        $this->setStartTime(null);

        $result = CheckResult::make()
            ->startTime($this->getStartTime())
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
        $newSoftwareList = [];

        try {
            $softwareList = explode("\n", ((Process::run($method))->output()));
        } 
        catch (\Exception $exception)
        {
            $this->addErrorMessage($exception->getMessage() ?? 'Unknown Error');
            report($exception);
        }
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
