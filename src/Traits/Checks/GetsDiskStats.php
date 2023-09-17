<?php

namespace Larawatch\Traits\Checks;


trait GetsDiskStats
{

    protected array $diskStats = [];

    protected function getDiskStats(string $fileSystemPath): array
    {
        $free_space = $this->getDiskFreeSpace(fileSystemPath: $fileSystemPath);
        $total_space = $this->getDiskTotalSpace(fileSystemPath: $fileSystemPath);
        $used_space = $this->getDiskUsedSpace(total_space: $total_space, free_space: $free_space);

        // Space is in MB
        return [
            'free_space' => $free_space,
            'total_space' => $total_space,
            'used_space' => $used_space,
            'free_percentage' => round(($free_space / $total_space) * 100,1),
            'used_percentage' => round(($used_space / $total_space) * 100,1),
        ];
    }

    public function setDiskStats(string $fileSystemPath)
    {
        $this->diskStats = $this->getDiskStats(fileSystemPath: $fileSystemPath);
    }

    protected function getDiskUsedSpace(int $total_space = 0, int $free_space = 0): int
    {
        return round($total_space - $free_space);
    }

    protected function getDiskFreeSpace(string $fileSystemPath): int
    {
        return round(disk_free_space($fileSystemPath ?: '.')/1048576);
    }

    protected function getDiskTotalSpace(string $fileSystemPath): int
    {
        
        return round(disk_total_space($fileSystemPath ?: '.')/1048576);
    }

    
}
