<?php

namespace Larawatch\Checks;

//use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Process;
use Larawatch\Traits\Checks\ChecksDisks;

class LocalDiskPerformanceCheck extends BaseCheck
{
    use ChecksDisks;

    public function __construct(string $fileSystemName = '', string $fileSystemPath = '')
    {
        $this->fileSystemName = $fileSystemName;
        $this->fileSystemPath = $fileSystemPath;

        if (!isset($this->checkStartTime))
        {
            $this->checkStartTime = \Carbon\Carbon::now();
        }
    }

    public function run(): CheckResult
    {
        $this->setStartTime(null);

        $result = CheckResult::make()
        ->startTime($this->getStartTime())
        ->checkData([
            'fileSystemName' => $this->fileSystemName ?? 'unknown', 
            'fileSystemPath' => $this->fileSystemPath ?? 'unknown',
        ])
        ->checkTarget($this->fileSystemName)
        ->resultData([]);

        if (!isset($this->fileSystemName))
        {
            return $result->failed("No File System Name Specified");
        }
        if (!isset($this->fileSystemPath))
        {
            return $result->failed("No File System Path Specified");
        }

        $result
            ->resultData([
                'disk_speed_test_write' => $this->diskSpeedTestWrite($this->fileSystemPath),
            ])
            ->errorMessages($this->getErrorMessages());
            
        if ($this->crashed) {
            return $result->markAsCrashed("Could Not Run");
        }
    
        if ($this->error) {
            return $result->failed();
        }

        if ($this->warning) {
            return $result->warning();
        }
        
        return $result->ok();
    }

    protected function diskSpeedTestWrite(string $fileSystemPath)
    {
        $fileName = $fileSystemPath . '/tmpfile-' . md5(rand(10,500) . '-' . now()) . '.txt';

        $processToRun = "dd if=/dev/zero of=" . $fileName . " bs=5G count=1 oflag=direct";
        $start = time();
        $process = Process::run($processToRun);
        $end = time();
        $newProcess = Process::run('rm '.$fileName);
        $difference = ($end - $start);

        return round(5120/$difference,2);
    }

    public function getName(): string
    {
        return class_basename(static::class);
    }
    
    public function getTarget(): string
    {
        return $this->fileSystemName;
    }
}
