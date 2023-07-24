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

    public function __construct(StatsClient $statsClient = null)
    {
        $this->statsClient = $statsClient ?? new StatsClient(config('larawatch.destination_token', 'destination_token'), config('larawatch.project_key', 'project_key'));
    }

    public function logStats(string $destination, array $data)
    {
        return $this->statsClient->sendRawData($destination, $data);

    }


}
