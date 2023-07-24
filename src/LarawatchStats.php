<?php

namespace Larawatch;

use Throwable;
use Larawatch\Http\StatsClient;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;

class LarawatchStats
{
    /** @var StatsClient */
    private $statsClient;

    /**
     * @param StatsClient $statsClient
     */
    public function __construct($statsClient)
    {
        $this->statsClient = $statsClient;
    }

    public function logStats(string $destination, array $data)
    {
        return $this->statsClient->sendRawData($destination, $data);

    }


}
