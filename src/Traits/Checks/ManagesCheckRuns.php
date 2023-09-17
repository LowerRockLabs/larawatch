<?php

namespace Larawatch\Traits\Checks;

use Ramsey\Uuid\Uuid;

trait ManagesCheckRuns
{
    protected string $check_run_id = '';
    
    protected function createCheckRun()
    {
        $this->check_run_id = Uuid::uuid7()->toString();

    }

}
