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
                'sub_check_failed' => $this->subCheckFailed,
            ]); 

        return (!$this->subCheckFailed ? $result->ok() : $result->failed());
    }

    public function basicCheck(): bool
    {
        if ((config('database.connections.'.$this->connectionName)['database'] == null))
        {
            $this->subCheckFailed = true;
            return false;
        }
        try {
            $dbConnection = DB::connection($this->connectionName)->select('SELECT DISTINCT table_schema from information_schema.tables');
        } catch (PDOException $e) {
            $this->subCheckFailed = true;
        } catch (\Illuminate\Database\QueryException $e)  {
            $this->subCheckFailed = true;
        } catch(Exception $e) {
            $this->subCheckFailed = true;
        }

        if ($this->subCheckFailed)
        {
            $this->subCheckFailed = true;
            return false;
        }
        if (!$dbConnection)
        {
            $this->subCheckFailed = true;
            return false;
        } 

        $this->subCheckFailed = false;
        return true;

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
        try {
            $connection = $this->setupConnection();
        } catch (\Illuminate\Database\QueryException $e)  {
            $this->subCheckFailed = true;
        } catch(Exception $e) {
            $this->subCheckFailed = true;
        }

        try {
            $dbinMb = (new DBInfo())->databaseSizeInMb($connection);
        } catch (\Illuminate\Database\QueryException $e)  {
            $this->subCheckFailed = true;
        } catch(Exception $e) {
            $this->subCheckFailed = true;
        }
        return $dbinMb;
    }

    protected function setupConnection()
    {
        try {
            $connection = app(ConnectionResolverInterface::class)->connection($this->connectionName); 
        } catch (\Illuminate\Database\QueryException $e)  {
            $this->subCheckFailed = true;
        } catch(Exception $e) {
            $this->subCheckFailed = true;
        }
        return $connection;
    }

}
