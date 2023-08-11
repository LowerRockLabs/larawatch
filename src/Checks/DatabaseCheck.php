<?php

namespace Larawatch\Checks;

use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Support\Str;
use Larawatch\Support\DBInfo;
use Illuminate\Support\Facades\DB;

class DatabaseCheck extends BaseCheck
{
    protected string $connectionName;

    protected array $connectionsToCheck = [];

    protected array $temporaryResults = [];

    protected bool $subCheckFailed = false;

    public function __construct(array $connectionsToCheck)
    {
        $this->connectionsToCheck($connectionsToCheck);
        if (!isset($this->checkStartTime))
        {
            $this->checkStartTime = \Carbon\Carbon::now();
        }

    }

    public function getName(): string
    {
        return class_basename(static::class);

    }

    public function connectionsToCheck(array $connectionsToCheck): self
    {
        $this->connectionsToCheck = $connectionsToCheck;

        return $this;
    }


    public function connectionName(string $connectionName): self
    {
        $this->connectionName = $connectionName;

        return $this;
    }

    public function run(): CheckResult
    {

        foreach ($this->connectionsToCheck as $connectionName => $connectionData)
        {
            $dbConnections = 0;
            $dbSize = 0;    
            $this->connectionName = $connectionName;
            $basicCheck = $this->basicCheck();
            if ($basicCheck)
            {
                $dbConnections = $this->getDatabaseConnections();
                $dbSize = $this->getDatabaseSizeInGb();
            }
            else
            {
                $this->subCheckFailed = true;
            }
    
            $this->temporaryResults[] = [
                'connection_name' => $this->connectionName,
                'basic_check' => $basicCheck,
                'connections' => $dbConnections,
                'database_size' => $dbSize,
                'sub_check_failed' => $this->subCheckFailed,
            ];
        }
        $result = CheckResult::make(started_at: $this->checkStartTime)
            ->resultData($this->temporaryResults); 

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
