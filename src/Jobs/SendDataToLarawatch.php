<?php

namespace Larawatch\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Larawatch\Traits\GeneratesDateTime;

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
        $this->fileName = $fileName;
        $this->dateTime = $this->generateDateTime();

    }

    public function handle()
    {
      //  $laraWatch = app('larawatch');

       // $laraWatch->sendFile($this->fileName.'-test123321321.json');
       
       $laraWatch = app('larawatch');
       $laraWatch->sendFile($this->fileName);

    }
}
