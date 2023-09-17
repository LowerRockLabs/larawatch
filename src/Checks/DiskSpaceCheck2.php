<?php

namespace Larawatch\Checks;

//use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Process;


class DiskSpaceCheck2 extends BaseCheck
{
    protected int $warningThreshold = 70;

    protected int $errorThreshold = 90;

    protected string $diskSpaceUsedPercentage = '1210';

    protected string $fileSystemName = '';
    protected string $fileSystemPath = '';

    protected array $diskStats = [];
    protected array $thresholds = [];

    protected bool $error = false;
    protected bool $warning = false;
    protected bool $crashed = false;



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

    public function fileSystemPath(string $fileSystemPath): self
    {
        $this->fileSystemPath = $fileSystemPath;

        return $this;
    }

    public function fileSystemName(string $fileSystemName): self
    {
        $this->fileSystemName = $fileSystemName;

        return $this;
    }

    public function warningThreshold(int $warningThreshold): self
    {
        $this->warningThreshold = $warningThreshold;

        return $this;
    }

    public function errorThreshold(int $errorThreshold): self
    {
        $this->errorThreshold = $errorThreshold;

        return $this;
    }



    public function run(): CheckResult
    {
        $this->setStartTime(null);

        $result = CheckResult::make()
        ->startTime($this->getStartTime())
        ->checkData([
            'fileSystemName' => $this->fileSystemName, 
            'fileSystemPath' => $this->fileSystemPath,
        ])
        ->resultData([]);

        if (!isset($this->fileSystemName))
        {
            return $result->failed("No File System Specified");
        }
        
        $this->calcDiskUsagePercentage();

        $result
            ->resultData([
                'fileSystemName' => $this->fileSystemName, 
                'fileSystemPath' => $this->fileSystemPath,
                'free_percentage' => $this->diskStats['free_percentage'],
                'used_percentage' => $this->diskStats['used_percentage'],
                'total_space' => $this->diskStats['total_space'],
                'used_space' => $this->diskStats['used_space'],
                'free_space' => $this->diskStats['free_space'],
                'speed_test_5g' => $this->diskStats['speed_test_5g'],
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

    protected function getDiskUsedSpace(int $total_space = 0, int $free_space = 0): int
    {
        return round($total_space - $free_space);
    }


    protected function getDiskFreeSpace(string $fileSystemPath): int
    {
        return round(disk_free_space($fileSystemPath ?: '.')/1048576);
    }

    protected function getDiskTotalSpace(string $fileSystemPath): int
    {
        
        return round(disk_total_space($fileSystemPath ?: '.')/1048576);
    }

    protected function runSpeedTest5G(string $fileSystemPath)
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

    protected function getDiskStats(string $fileSystemPath): array
    {
        $free_space = $this->getDiskFreeSpace(fileSystemPath: $fileSystemPath);
        $total_space = $this->getDiskTotalSpace(fileSystemPath: $fileSystemPath);
        $used_space = $this->getDiskUsedSpace(total_space: $total_space, free_space: $free_space);

        // Space is in MB
        return [
            'free_space' => $free_space,
            'total_space' => $total_space,
            'used_space' => $used_space,
            'free_percentage' => round(($free_space / $total_space) * 100,1),
            'used_percentage' => round(($used_space / $total_space) * 100,1),
            'speed_test_5g' => $this->runSpeedTest5G($fileSystemPath)
        ];
    }

    public function setDiskStats()
    {
        $this->diskStats = $this->getDiskStats($this->fileSystemPath);
    }

    public function runSubChecks()
    {
        $this->setDiskStats();

        $this->checkDiskFreePercentage();

    }

    protected function checkDiskFreePercentage(): bool
    {
        return $this->diskStats['free_percentage'] < $this->thresholds['free_percentage']['warning'];
    }

    public function calcDiskUsagePercentage(): void
    {
        $this->setDiskStats();

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

    public function calcDiskUsagePercentage2(): void
    {
        try {
            $process = Process::fromShellCommandline('df -P '.($this->fileSystemPath ?: '.'));
            $process->run();
            $output = $process->getOutput();  
            $this->diskSpaceUsedPercentage = trim(substr($output,-7,4)) ?? 100;
        }
        catch (\Exception $exception)
        {
            report($exception);
            $this->addErrorMessage($exception->getMessage());
            $this->crashed = true;
        }

       // $this->addErrorMessage("OUTPUT: ".$output);


        if (intval($this->diskSpaceUsedPercentage) > $this->errorThreshold)
        {
            $this->addErrorMessage("The disk is almost full - {$this->fileSystemName} - ({$this->diskSpaceUsedPercentage}% used).");
            $this->error = true;

        }    
        else if (intval($this->diskSpaceUsedPercentage) > $this->warningThreshold)
        {
            $this->addErrorMessage("The disk is almost full - {$this->fileSystemName} - ({$this->diskSpaceUsedPercentage}% used).");
            $this->warning = true;
        }    

    }


    public function calcUsedDiskAmount(): int
    {
        try {
            $process = Process::fromShellCommandline('du -shb '.($this->fileSystemPath ?: '.'));
            $process->run();
            $output = $process->getOutput();  
        }
        catch (\Exception $exception)
        {
            report($exception);
            $this->addErrorMessage($exception->getMessage());
            $this->crashed = true;
        }
        //$this->addErrorMessage("calcUsedDiskAmount:".$output);

        return intval(substr($output,0,strpos($output, 't'))) ?? 0;
        //return intval(trim(str_replace($output, '.', ''))) ?? 0;
    }

}
