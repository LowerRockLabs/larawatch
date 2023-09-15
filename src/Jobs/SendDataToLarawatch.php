<?php

namespace Larawatch\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Larawatch\Traits\GeneratesDateTime;
use Illuminate\Support\Facades\Log;

class SendDataToLarawatch
{
    use Dispatchable;
    use SerializesModels;
    use InteractsWithQueue;
    use Queueable;
    use GeneratesDateTime;

    public string $fileName;

    public function __construct($fileName)
    {
        Log::error('SendDataToLarawatch Constructing');
        $this->fileName = $fileName;
        $this->dateTime = $this->generateDateTime();

    }

    public function handle()
    {
      //  $laraWatch = app('larawatch');

       // $laraWatch->sendFile($this->fileName.'-test123321321.json');
       
       $laraWatch = app('larawatch');
       Log::error('laraWatch Constructing');
       Log::error("Larawatch FileName:".$this->fileName);
       $laraWatch->sendFile($this->fileName);
       Log::error('laraWatch sendFile');
       

    }
}
