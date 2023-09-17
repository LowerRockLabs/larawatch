<?php

namespace Larawatch\Traits\Checks;


trait FindsDatabases
{
    protected function determineDatabasesToCheck(): array
    {
        if (count(config('larawatch.checks.databases_active')) > 0)
        {
            $databasesToCheck = config('larawatch.checks.databases_active');
        }
        else
        {
            $databasesToCheck = config('database.connections');
        }

        if (count(config('larawatch.checks.databases_ignore')) > 0)
        {
            foreach ($databasesToCheck as $dbCheckIndex => $dbCheckName)
            {
                if (in_array($dbCheckIndex, config('larawatch.checks.databases_ignore')))
                {
                    unset($databasesToCheck[$dbCheckIndex]);
                }
            }

        }
        
        foreach ($databasesToCheck as $connectionName => $connectionData)
        {
            if (!in_array($connectionData['driver'], ['mysql', 'pgsql']))
            {
                unset($databasesToCheck[$connectionName]);
            }
        }
        
        return $databasesToCheck;
    }
}
