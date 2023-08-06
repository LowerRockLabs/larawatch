<?php

namespace Larawatch\Exceptions\Checks;

use Exception;
use Larawatch\Checks\BaseCheck;

class CheckDidNotCompleteException extends Exception
{
    public static function make(BaseCheck $check, Exception $exception): self
    {
        return new self(
            message: "The check named `{$check->getName()}` did not complete. An exception was thrown with this message: `".get_class($exception).": {$exception->getMessage()}`",
            previous: $exception,
        );
    }
}
