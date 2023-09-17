<?php

namespace Larawatch\Checks;

class AppOptimizedCheck extends BaseCheck
{
    public bool $subCheckFailed = false;

    public function run(): CheckResult
    {
        $this->setStartTime(null);

        $result = CheckResult::make()
            ->startTime($this->getStartTime())
            ->accessData([
                'project_key' => config('larawatch.project_key', []),
                'server_key' => config('larawatch.server_key', []),
            ])
            ->resultData([
                'config_cached' => $this->checkConfigCache(),
                'routes_cached' => $this->checkRouteCache(),
                'events_cached' => $this->checkEventCache(),
            ]);

        return (!$this->subCheckFailed) ? $result->ok() : $result->failed();
    }
    
    protected function checkConfigCache(): string
    {
        if (app()->configurationIsCached())
        {
            return 'yes';
        }
        else
        {
            $this->subCheckFailed = true;
            return 'no';
        }
    }

    protected function checkRouteCache(): string
    {
        if (app()->routesAreCached())
        {
            return 'yes';
        }
        else
        {
            $this->subCheckFailed = true;
            return 'no';
        }

    }

    protected function checkEventCache(): string
    {
        if (app()->eventsAreCached())
        {
            return 'yes';
        }
        else
        {
            $this->subCheckFailed = true;
            return 'no';
        }
    }

}

