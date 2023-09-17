<?php

namespace Larawatch\Traits\Core;


trait ProvidesErrorMessages
{
    public array $errorMessages = [];

    public function errorMessage(string|array $errorMessage): self
    {
        $this->setErrorMessage($errorMessage);

        return $this;
    }

    public function errorMessages(array $errorMessages = []): self
    {
        $this->setErrorMessages($errorMessages);

        return $this;
    }

    public function setErrorMessages(array $errorMessages = []): void
    {
        $this->errorMessages = [...$this->errorMessages, ...$errorMessages];
    }

    
    public function setErrorMessage(string|array $errorMessage): void
    {
        if (is_array($errorMessage))
        {
            $this->errorMessages = [...$this->errorMessages, ...$errorMessage];
        }
        else
        {
            $this->addErrorMessage($errorMessage);
        }
    }

    public function addErrorMessage(string $errorMessage = ''): void
    {
        $this->errorMessages[] = $errorMessage;
    }

    public function getErrorMessages(): array
    {
        return $this->errorMessages ?? [];
    }

}
