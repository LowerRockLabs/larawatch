<?php

namespace Larawatch\Checks;

//use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Process;
use Larawatch\Traits\Checks\{ChecksDisks,GetsDiskStats};


class LocalDiskSpaceCheck extends BaseCheck
{
    use ChecksDisks;
    use GetsDiskStats;


    public function __construct(string $fileSystemName = '', string $fileSystemPath = '')
    {
        $this->fileSystemName = $fileSystemName;
        $this->fileSystemPath = $fileSystemPath;

        if (!isset($this->checkStartTime))
        {
            $this->checkStartTime = \Carbon\Carbon::now();
        }
        $this->thresholds['free_percentage'] = ['warning' => 30, 'error' => 10];
        $this->thresholds['used_percentage'] = ['warning' => 70, 'error' => 90];
        
    }

    public function getName(): string
    {
        return class_basename(static::class);
    }

    public function getTarget(): string
    {
        return $this->fileSystemName;
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

        $this->calcDiskUsagePercentage();

        $result
            ->resultData([
                'disk_free_percentage' => $this->diskStats['free_percentage'],
                'disk_used_percentage' => $this->diskStats['used_percentage'],
                'disk_total_space' => $this->diskStats['total_space'],
                'disk_used_space' => $this->diskStats['used_space'],
                'disk_free_space' => $this->diskStats['free_space'],
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

    public function calcDiskUsagePercentage(): void
    {
        $this->setDiskStats($this->fileSystemPath);

        if ($this->diskStats['used_percentage'] > $this->thresholds['used_percentage']['error'])
        {
            $this->addErrorMessage("The disk is almost full - {$this->fileSystemName} - ({$this->diskStats['used_percentage']}% used).");
            $this->error = true;
        }    
        else if ($this->diskStats['used_percentage'] > $this->thresholds['used_percentage']['warning'])
        {
            $this->addErrorMessage("The disk is almost full - {$this->fileSystemName} - ({$this->diskStats['used_percentage']}% used).");
            $this->warning = true;
        }    
    }

}
