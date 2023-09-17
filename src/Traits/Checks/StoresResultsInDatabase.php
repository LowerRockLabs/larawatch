<?php

namespace Larawatch\Traits\Checks;

use Larawatch\Checks\Stores\DatabaseStore;
use Ramsey\Uuid\Uuid;

trait StoresResultsInDatabase
{
    protected DatabaseStore $dbStore;

    protected function createsDatabaseStoreInstance()
    {
        $this->dbStore = new \Larawatch\Checks\Stores\DatabaseStore();
        $this->dbStore->checkRunID($this->check_run_id);
    }

}
