<?php

namespace Larawatch\Checks\Stores;

use Exception;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;

class FileStore 
{
    protected FilesystemAdapter $disk;

    protected string $diskName;
    protected string $folderPath;
    protected string $fileName;
    protected string $fullPath;

    public function __construct(?string $diskName, ?string $folderPath, ?string $fileName)
    {
        $this->diskName = $diskName ?? config('larawatch.checks.diskName','local');
        $this->folderPath = $folderPath ?? config('larawatch.checks.folderPath','larawatch');
        $this->fileName = $fileName ?? 'larawatch-checks-'.date('Y-m-d').'.json';
        $this->fullPath = rtrim($this->folderPath, '/').'/'.ltrim($this->fileName, '/');
    }

    public function save(Collection $checkResults): void
    {
        $this->disk = Storage::disk($this->diskName);
    

        if ($this->disk->exists($this->fullPath)) {
            $this->disk->append($this->fullPath, trim(collect(Arr::wrap($checkResults))->toJson(), "\n"));
        }
        else {
            $this->disk->write($this->fullPath, trim(collect(Arr::wrap($checkResults))->toJson(), "\n"));   
            
        }
    }

}
