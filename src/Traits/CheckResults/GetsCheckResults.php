<?php

namespace Larawatch\Traits\CheckResults;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Larawatch\Models\LarawatchCheck;

trait GetsCheckResults
{
    public array $locallyStoredChecks = [];
    public array $unsubmittedRunIDs = [];
    public array $runResults = [];

    public function getUnsubmittedRunIDs(): bool
    {
        try {
            $this->unsubmittedRunIDs = LarawatchCheck::select('check_run_id')->groupby('check_run_id')->get()->pluck('check_run_id')->toArray();
        }
        catch (ModelNotFoundException $exception)
        {
            report($exception);
            return false;
        }
        catch (Exception $exception)
        {
            report($exception);
            return false;
        }
        return count($this->unsubmittedRunIDs) > 0;
    }

    public function getResultsByRunId(string $check_run_id = ''): bool
    {
        try {
            $this->runResults = LarawatchCheck::where('check_run_id', $check_run_id)->get()->toArray();
        }
        catch (ModelNotFoundException $exception)
        {
            report($exception);
            return false;
        }
        catch (Exception $exception)
        {
            report($exception);
            return false;
        }
        

        return count($this->runResults) > 0;
    }

    public function getResultsFromLocalDB(): bool
    {
        try {
            $this->locallyStoredChecks = LarawatchCheck::get()->toArray();
        }
        catch (ModelNotFoundException $exception)
        {
            report($exception);
            return false;
        }
        catch (Exception $exception)
        {
            report($exception);
            return false;
        }
        

        return count($this->locallyStoredChecks) > 0;
    }
}
