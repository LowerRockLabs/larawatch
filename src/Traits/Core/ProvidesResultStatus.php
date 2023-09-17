<?php

namespace Larawatch\Traits\Core;


trait ProvidesResultStatus
{
    public string $result_status = '';
    
    public function resultStatus(string $result_status = ''): self
    {
        $this->setResultStatus($result_status);

        return $this;
    }

    public function setResultStatus(string $result_status = ''): void
    {
        $this->result_status = $result_status;
    }

    public function getResultStatus(): string
    {
        return $this->result_status ?? 'unknown';
    }


}
