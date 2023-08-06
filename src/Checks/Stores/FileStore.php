<?php

namespace Larawatch\Checks\Stores;

use Exception;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class FileStore 
{
    protected FilesystemAdapter $disk;

    protected string $path;

    public function __construct(string $disk, string $path)
    {
        $this->disk = Storage::disk($disk);

        $this->path = $path;
    }

    public function save(Collection $checkResults): void
    {
        if ($this->disk->exists($this->path)) {
            $this->disk->delete($this->path);
        }
        $this->disk->write($this->path, $checkResults->toJson());
    }

}
