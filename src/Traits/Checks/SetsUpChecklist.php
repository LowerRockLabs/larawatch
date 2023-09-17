<?php

namespace Larawatch\Traits\Checks;

use Larawatch\Traits\Checks\{FindsDatabases, FindsFileSystems};

trait SetsUpChecklist
{
    use FindsFileSystems;
    use FindsDatabases;

    protected array $checkList = [];

    protected function generateChecklist()
    {
        /**
         * Configure File Systems To Check
         */
        $this->addFileSystemChecks();

        $this->checkList[] = (new \Larawatch\Checks\AppOptimizedCheck());
        $this->checkList[] = (new \Larawatch\Checks\CacheCheck());
        $this->checkList[] = (new \Larawatch\Checks\DebugModeCheck());
        $this->checkList[] = (new \Larawatch\Checks\DatabaseCheck(connectionsToCheck: $this->determineDatabasesToCheck()));
        $this->checkList[] = (new \Larawatch\Checks\EnvironmentCheck());
        $this->checkList[] = (new \Larawatch\Checks\InstalledPackageCheck());
        $this->checkList[] = (new \Larawatch\Checks\InstalledSoftwareCheck());
        $this->checkList[] = (new \Larawatch\Checks\RepoVersionCheck());
    }

    protected function removeSkippedChecks()
    {
        foreach ($this->checkList as $index => $check)
        {
            if (!$check->shouldRun())
            {
                $this->dbStore->setCheckName($check->getName() ?? 'unknown');
                $this->dbStore->storeCheck($check->markAsSkipped());
                unset($this->checkList[$index]);
            }
        }
    }
    
}
