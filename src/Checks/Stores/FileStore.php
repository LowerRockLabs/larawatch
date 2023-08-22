<?php

namespace Larawatch\Checks\Stores;

use Exception;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;

class FileStore 
{
    protected FilesystemAdapter $disk;

    protected string $diskName;
    protected string $folderPath;
    protected string $fileName;
    protected string $fullPath;

    public function __construct(?string $diskName, ?string $folderPath)
    {
        $currentMinute = intval(date('i'));
        $pinpoint = 4;
        if ($currentMinute < 15)
        {
            $pinpoint = 1;
        }
        else if ($currentMinute < 30)
        {
            $pinpoint = 2;
        }
        else if ($currentMinute < 45)
        {
            $pinpoint = 3;
        }

        $this->diskName = $diskName ?? config('larawatch.checks.diskName','local');
        $this->folderPath = $folderPath ?? config('larawatch.checks.folderPath','larawatch');
        $this->fileName = $fileName ?? 'larawatch-checks-'.date('Y-m-d H-').$pinpoint.'.json';
        $this->fullPath = rtrim($this->folderPath, '/').'/'.ltrim($this->fileName, '/');
    }

    public function save(Collection $checkResults): void
    {
        $this->disk = Storage::disk($this->diskName);
    
        if ($this->disk->exists($this->fullPath)) 
        {
            $existingData = json_decode($this->disk->get($this->fullPath),true);
            foreach ($checkResults->toArray() as $key => $data)
            {
                foreach ($data as $key1 => $data1)
                {

                    if (is_array($data1) && isset($data1[0]) && $data1[0] instanceof \Larawatch\Checks\CheckResult)
                    {
                        $existingData[$key1][] = $data1[0];
                    }
                    else
                    {
                        $existingData[$key1][] = ['status' => 'skipped'];
                    }
                    
                }
            }
        }
        else 
        {
            foreach ($checkResults->toArray() as $key => $data)
            {
                foreach ($data as $key1 => $data1)
                {

                    if (is_array($data1) && isset($data1[0]) && $data1[0] instanceof \Larawatch\Checks\CheckResult)
                    {
                        $existingData[$key1][] = $data1[0];
                    }
                    else
                    {
                        $existingData[$key1][] = ['status' => 'skipped'];
                    }
                    
                }

            }
        }
        $this->disk->write($this->fullPath, json_encode($existingData));
    }

}
