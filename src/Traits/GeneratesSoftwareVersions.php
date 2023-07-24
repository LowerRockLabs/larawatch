<?php

namespace Larawatch\Traits;

trait GeneratesSoftwareVersions
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
        return [
            'event_datetime' => $this->dateTime ?? $this->generateDateTime(),
            'php_extensions' => get_loaded_extensions(),
        ];
    }
}
