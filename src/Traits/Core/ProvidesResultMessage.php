<?php

namespace Larawatch\Traits\Core;


trait ProvidesResultMessage
{
    public string $result_message = '';

    public function resultMessage(string $result_message = ''): self
    {
        $this->setResultMessage($result_message);

        return $this;
    }

    public function setResultMessage(string $result_message = ''): void
    {
        $this->result_message = $result_message;
    }

    public function getResultMessage(): string
    {
        return $this->result_message ?? '';
    }

}
