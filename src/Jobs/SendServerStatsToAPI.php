<?php

namespace Larawatch\Larawatch\Jobs;

use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Process;

class SendServerStatsToAPI
{
    use Dispatchable;
    use SerializesModels;
    use InteractsWithQueue;
    use Queueable;

    public DateTime $dateTime;

    public array $dataArray;

    public string $cpu_usage;

    public string $memory_usage;

    public function __construct()
    {
        $this->dateTime = new DateTime;

    }

    public function handle()
    {
        $totalMemory = intval(rtrim(Process::run("awk '{ if (/MemTotal:/) {print $2} }' < /proc/meminfo")->output(), "\n"));
        $freeMemory = intval(rtrim(Process::run("awk '{ if (/MemFree:/) {print $2} }' </proc/meminfo")->output(), "\n"));
        $cpuPercent = Process::run("grep 'cpu ' /proc/stat | awk '{usage=($2+$4)*100/($2+$4+$5)} END {print usage}'")->output();

        $this->dataArray = [
            'project_key' => config('larawatch.project_key'),
            'event_datetime' => $this->dateTime,
            'cpu_usage' => $cpuPercent,
            'memory_usage' => ($freeMemory / $totalMemory),
        ];
        $response = Http::withToken(config('larawatch.destination_token'))->retry(3, 10 * 1000)->post(config('larawatch.base_url').'serverstats', $this->dataArray);
    }

    public function retryUntil(): DateTime
    {
        return now()->addMinutes(config('larawatch.lowerrocklabs.retry_job_for_minutes', 5))->toDateTime();
    }
}
