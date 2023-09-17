<?php

namespace Larawatch\Traits\Core;


trait ProvidesCheckData
{
    public string $check_name = '';
    protected array $check_data = [];
    protected string $check_run_id = '';
    

    public function checkName(string $check_name = ''): self
    {
        $this->setCheckName($check_name);

        return $this;
    }

    public function setCheckName(string $check_name = ''): void
    {
        $this->check_name = $check_name;
    }

    public function getCheckName(): string
    {
        return $this->check_name ?? 'unknown';
    }

    public function checkData(array $check_data = []): self
    {
        $this->setCheckData($check_data);

        return $this;
    }

    public function setCheckData(array $check_data = []): void
    {
        $this->check_data = $check_data;
    }

    public function getCheckData(): array
    {
        return $this->check_data ?? [];
    }


    public function checkRunID(string $check_run_id = ''): self
    {
        $this->setCheckRunID($check_run_id);

        return $this;
    }

    public function setCheckRunID(string $check_run_id = ''): void
    {
        $this->check_run_id = $check_run_id;
    }

    public function getCheckRunID(): string
    {
        return $this->check_run_id ?? '';
    }

}
