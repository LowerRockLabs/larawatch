<?php

namespace Larawatch\Checks;

use Symfony\Component\Process\Process;

class DiskSpaceCheck extends BaseCheck
{
    protected int $warningThreshold = 70;

    protected int $errorThreshold = 90;

    protected ?string $fileSystemName = null;

    protected array $fileSystemsToCheck = [];

    protected array $temporaryResults = [];

    public function __construct(array $fileSystemsToCheck = [])
    {
        $this->fileSystemsToCheck($fileSystemsToCheck);
        if (!isset($this->checkStartTime))
        {
            $this->checkStartTime = \Carbon\Carbon::now();
        }

    }

    public function fileSystemsToCheck(array $fileSystemsToCheck): self
    {
        $this->fileSystemsToCheck = $fileSystemsToCheck;

        return $this;
    }

    public function fileSystemName(string $fileSystemName): self
    {
        $this->fileSystemName = $fileSystemName;

        return $this;
    }

    public function run(): CheckResult
    {
        $warning = $error = false;
        foreach ($this->fileSystemsToCheck as $fileSystemName => $fileSystemData)
        {
            $this->fileSystemName = $fileSystemName;
            $diskSpaceUsedPercentage = $this->getDiskUsagePercentage();
            $this->temporaryResults[] = [
                'fileSystemName' => $this->fileSystemName, 
                'diskSpaceUsedPercentage' => $diskSpaceUsedPercentage,
                'warning' => ($diskSpaceUsedPercentage > $this->warningThreshold) ? true : false,
                'error' => ($diskSpaceUsedPercentage > $this->errorThreshold) ? true : false,
            ];
            if ($warning == false) $warning = ($diskSpaceUsedPercentage > $this->warningThreshold) ? true : false;
            if ($error == false) $warning = ($diskSpaceUsedPercentage > $this->errorThreshold) ? true : false;
        }

        $result = CheckResult::make()
            ->resultData($this->temporaryResults);

        if ($error) {
            return $result->failed("The disk is almost full - {$this->fileSystemName} - ({$diskSpaceUsedPercentage}% used).");
        }

        if ($warning) {
            return $result->warning("The disk is almost full  - {$this->fileSystemName} - ({$diskSpaceUsedPercentage}% used).");
        }

        return $result->ok();
    }

    protected function getDiskUsagePercentage(): int
    {
        $process = Process::fromShellCommandline('df -P '.($this->fileSystemName ?: '.'));

        $process->run();

        $output = $process->getOutput();

        return (int) preg_match('/(\d*)%/', $output);
    }
}
