<?php

namespace Larawatch\Traits;

use Illuminate\Support\Facades\Process;

trait GeneratesServerStats
{
    use GeneratesDateTime;

    public array $dataArray;

    public function generateData(): void
    {
        $this->dateTime = $this->generateDateTime();
        $this->dataArray = $this->getDataArray();
    }

    public function getDataArray(): array
    {

        $totalMemory = intval(rtrim(Process::run("awk '{ if (/MemTotal:/) {print $2} }' < /proc/meminfo")->output(), "\n"));
        $freeMemory = intval(rtrim(Process::run("awk '{ if (/MemFree:/) {print $2} }' </proc/meminfo")->output(), "\n"));
        $cpuPercent = Process::run("grep 'cpu ' /proc/stat | awk '{usage=($2+$4)*100/($2+$4+$5)} END {print usage}'")->output();

        return [
            'event_datetime' => $this->dateTime ?? $this->generateDateTime(),
            'cpu_usage' => $cpuPercent,
            'memory_usage' => ($freeMemory / $totalMemory),
        ];
    }
}
