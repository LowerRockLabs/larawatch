<?php

namespace Larawatch\Traits\Checks;


trait ChecksDisks
{
    protected string $fileSystemName = '';
    protected string $fileSystemPath = '';

    protected bool $error = false;
    protected bool $warning = false;
    protected bool $crashed = false;

    protected array $thresholds = [];

    protected int $warningThreshold = 70;
    protected int $errorThreshold = 90;

    public function fileSystemPath(string $fileSystemPath): self
    {
        $this->fileSystemPath = $fileSystemPath;

        return $this;
    }

    public function fileSystemName(string $fileSystemName): self
    {
        $this->fileSystemName = $fileSystemName;

        return $this;
    }

    public function warningThreshold(int $warningThreshold): self
    {
        $this->warningThreshold = $warningThreshold;

        return $this;
    }

    public function errorThreshold(int $errorThreshold): self
    {
        $this->errorThreshold = $errorThreshold;

        return $this;
    }
    
}
