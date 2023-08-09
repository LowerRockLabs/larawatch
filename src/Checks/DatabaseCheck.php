<?php

namespace Larawatch\Checks;

use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Support\Str;
use Larawatch\Support\DBInfo;
use Illuminate\Support\Facades\DB;

class DatabaseCheck extends BaseCheck
{
    protected string $connectionName;

    protected bool $subCheckFailed = false;

    public function __construct(string $connectionName)
    {
        $this->connectionName($connectionName);
        if (!isset($this->checkStartTime))
        {
            $this->checkStartTime = \Carbon\Carbon::now();
        }

    }

    public function getName(): string
    {
        return class_basename(static::class) ."-".$this->connectionName;

    }


    public function connectionName(string $connectionName): self
    {
        $this->connectionName = $connectionName;

        return $this;
    }

    public function run(): CheckResult
    {
        $basicCheck = $this->basicCheck();
        $dbConnections = 0;
        $dbSize = 0;
        if ($basicCheck)
        {
            $dbConnections = $this->getDatabaseConnections();
            $dbSize = $this->getDatabaseSizeInGb();
        }
        else
        {
            $this->subCheckFailed = true;
        }
        $result = CheckResult::make(started_at: $this->checkStartTime)
            ->resultData([
                'connection_name' => $this->connectionName,
                'basic_check' => $basicCheck,
                'connections' => $dbConnections,
                'database_size' => $dbSize,
            ]); 

        return (!$this->subCheckFailed ? $result->ok() : $result->failed());
    }

    public function basicCheck()
    {
        try {
            $dbname = DB::connection($this->connectionName)->getDatabaseName();
        } catch (\Illuminate\Database\QueryException $e)  {
            report ($e);
            echo 'QueryException Thrown';
            return false;
        } catch(Exception $e) {
            report ($e);
            echo 'Exception Thrown';
            return false;
        }
        if ($dbname)
        {
           echo 'Returning True: '. $dbname;
            return true;
        } 
        echo 'Returning False';
        return false;

    }

    protected function getDefaultConnectionName(): string
    {
        return config('database.default');
    }

    protected function getDatabaseSizeInGb(): float
    {
        $connection = $this->setupConnection();
        if ($connection)
        {
            return round((new DBInfo())->databaseSizeInMb($connection) / 1000, 2);
        }
        $this->subCheckFailed = true;

        return 0.00;

    }

    protected function getDatabaseConnections(): int
    {
        $connection = $this->setupConnection();
        if ($connection)
        {
            return (new DBInfo())->databaseSizeInMb($connection);
        }
        $this->subCheckFailed = true;

        return 0;

    }

    protected function setupConnection()
    {
        try {
            $connection = app(ConnectionResolverInterface::class)->connection($this->connectionName); 
        }
        catch (Exception $e)
        {
            $this->subCheckFailed = true;
            return false;
        }
        return $connection ?? false;
    }

}
