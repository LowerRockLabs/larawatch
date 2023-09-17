<?php

namespace Larawatch\Traits\Core;


trait ProvidesResultData
{
    public $result_data = [];
    
    public function resultData(array $result_data = []): self
    {
        $this->setResultData($result_data);

        return $this;
    }

    public function setResultData(array $result_data = []): void
    {
        $this->result_data = $result_data;
    }

    public function getResultData()
    {
        return $this->result_data ?? [];
    }

}
