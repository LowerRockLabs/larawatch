<?php

namespace Larawatch\Traits\Core;


trait ProvidesAccessData
{
    public array $accessData = [];

    public function accessData(array $accessData = []): self
    {
        $this->setAccessData($accessData);

        return $this;
    }

    public function setAccessData(array $accessData = []): void
    {
        $this->accessData = $accessData;
    }

    public function getAccessData(): array
    {
        if (empty($this->accessData))
        {
            $this->setAccessData([
                'project_key' => config('larawatch.project_key', []),
                'server_key' => config('larawatch.server_key', []),
            ]);
        }
        return $this->accessData ?? [];
    }

}
