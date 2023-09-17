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
    public string $fileSystemName = '';

    public function __construct(string $fileSystemName = '')
    {
        $this->fileSystemName = $fileSystemName;
        if (!isset($this->checkStartTime))
        {
            $this->checkStartTime = \Carbon\Carbon::now();
        }

    }


    public function fileSystemName(string $fileSystemName): self
    {
        $this->fileSystemName = $fileSystemName;

        return $this;
    }


    public function run(): CheckResult
    {
        $this->setStartTime(null);

        $success = true;
        $messages = [];

        $result = CheckResult::make()
        ->startTime($this->getStartTime())
        ->resultData($this->checkCloudStorageProvider())
        ->errorMessages($this->getErrorMessages());

        try {
            throw new \Exception("Test");
        }
        catch (\Exception $exception){
            $this->addErrorMessage($exception->getMessage());
            report($exception);
        }
        finally
        {
            $success = false;
        }
        return ($success == true) ? $result->ok() : $result->failed()->resultMessage('Errors: '.implode(",", $this->getErrorMessages()));
    }

    protected function checkCloudStorageProvider(): array
    {
        $fileName = 'test-file-'.$this->fileSystemName."-".date('Y-m-d H:i:s').Str::random(12).".txt";
        $storedContents =  Str::random(64);
        try {
            Storage::disk($this->fileSystemName)->put($fileName,$storedContents);

            $contents = Storage::disk($this->fileSystemName)->get($fileName);

            //Storage::disk($cloudStorageName)->delete($fileName);
            return [
                "cloudStorageName" => $this->fileSystemName,
                "status" => ($contents === $storedContents ?? false),
            ];
        } catch (\Exception $exception) {
           // report($exception);
           $this->addErrorMessage($exception->getMessage());
           return [
            "cloudStorageName" => $this->fileSystemName,
            "status" => false,
            "error_messages" => serialize($exception),
            ];

        }
        return [
            "cloudStorageName" => $this->fileSystemName,
            "status" => true,
        ];

    }
}
