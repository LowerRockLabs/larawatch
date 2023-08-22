<?php

namespace Larawatch\Checks;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class CloudStorageCheck extends BaseCheck
{
    protected array $cloudStorageSystemsToCheck = [];
    protected array $temporaryResults = [];
    public array $errorMessages = [];

    public function __construct(array $cloudStorageSystemsToCheck = [])
    {
        $this->cloudStorageSystemsToCheck($cloudStorageSystemsToCheck);
        if (!isset($this->checkStartTime))
        {
            $this->checkStartTime = \Carbon\Carbon::now();
        }

    }


    public function cloudStorageSystemsToCheck(array $cloudStorageSystemsToCheck): self
    {
        $this->cloudStorageSystemsToCheck = $cloudStorageSystemsToCheck;

        return $this;
    }

    public function fileSystemName(string $fileSystemName): self
    {
        $this->fileSystemName = $fileSystemName;

        return $this;
    }


    public function run(): CheckResult
    {
        $success = true;
        $messages = [];
        foreach ($this->cloudStorageSystemsToCheck as $cloudStorageName => $cloudStorageData)
        {
            $this->temporaryResults[] = $this->checkCloudStorageProvider($cloudStorageName);

        }

        $result = CheckResult::make(started_at: $this->checkStartTime)
            ->resultData($this->temporaryResults);
        return ($success == true) ? $result->ok() : $result->failed()->resultMessage('Errors: '.implode(",", $this->errorMessages));
    }

    protected function checkCloudStorageProvider($cloudStorageName): array
    {
        $fileName = 'test-file-'.$cloudStorageName."-".date('Y-m-d H:i:s').Str::random(12).".txt";
        $storedContents =  Str::random(64);
        try {
            Storage::disk($cloudStorageName)->put($fileName,$storedContents);

            $contents = Storage::disk($cloudStorageName)->get($fileName);

            //Storage::disk($cloudStorageName)->delete($fileName);
            return [
                "cloudStorageName" => $cloudStorageName,
                "status" => ($contents === $storedContents ?? false),
            ];
        } catch (\Exception $exception) {
           // report($exception);
           $this->errorMessages[] = $exception->getMessage();
           return [
            "cloudStorageName" => $cloudStorageName,
            "status" => false,
            "error_messages" => serialize($exception),
            ];

        }
        return [
            "cloudStorageName" => $cloudStorageName,
            "status" => true,
        ];

    }
}
