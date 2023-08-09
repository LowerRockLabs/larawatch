<?php

namespace Larawatch\Support;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\MySqlConnection;
use Illuminate\Database\PostgresConnection;
use Larawatch\Exceptions\UnsupportedItems\UnsupportedDatabaseException;

class DBInfo
{
    public function connectionCount(ConnectionInterface $connection): int
    {
        return match (true) {
            $connection instanceof MySqlConnection => (int) $connection->selectOne('show status where variable_name = "threads_connected"')->Value,
            $connection instanceof PostgresConnection => (int) $connection->selectOne('select count(*) as connections from pg_stat_activity')->connections,
            default => throw UnsupportedDatabaseException::make($connection),
        };
    }

    public function tableSizeInMb(ConnectionInterface $connection, string $table): float
    {
        $sizeInBytes = match (true) {
            $connection instanceof MySqlConnection => $this->getMySQLTableSize($connection, $table),
            $connection instanceof PostgresConnection => $this->getPostgresTableSize($connection, $table),
            default => throw UnsupportedDatabaseException::make($connection),
        };

        return $sizeInBytes / 1024 / 1024;
    }

    public function databaseSizeInMb(ConnectionInterface $connection): float
    {
        return match (true) {
            $connection instanceof MySqlConnection => $this->getMySQlDatabaseSize($connection),
            $connection instanceof PostgresConnection => $this->getPostgresDatabaseSize($connection),
            default => throw UnsupportedDatabaseException::make($connection),
        };
    }

    protected function getMySQLTableSize(ConnectionInterface $connection, string $table): int
    {
        return $connection->selectOne('SELECT (data_length + index_length) AS size FROM information_schema.TABLES WHERE table_schema = ? AND table_name = ?', [
            $connection->getDatabaseName(),
            $table,
        ])->size;
    }

    protected function getPostgresTableSize(ConnectionInterface $connection, string $table): int
    {
        return $connection->selectOne('SELECT pg_total_relation_size(?) AS size;', [
            $table,
        ])->size;
    }

    protected function getMySQLDatabaseSize(ConnectionInterface $connection): int
    {
        try {
            $dbName = $connection->getDatabaseName();
        } catch (Exception $e) {
            report($e);
            return 0;
        }
        try {
            $databaseList = $connection->select('SELECT DISTINCT table_schema from information_schema.tables');
            if(in_array($connection->getDatabaseName(), $databaseList))
            {
                $instance = $connection->select('SELECT size from (SELECT table_schema "name", ROUND(SUM(data_length + index_length) / 1024 / 1024) as size FROM information_schema.tables GROUP BY table_schema) alias_one where name = ?', [$dbName]);
            }
            else
            {
                return 0;
            }
        } catch (\Illuminate\Database\QueryException $e) {
            report($e);
            return 0;
        } catch (Exception $e) {
            report($e);
            return 0;
        } 
        return $instance[0]->size;
    }

    protected function getPostgresDatabaseSize(ConnectionInterface $connection): int
    {
        return $connection->selectOne('SELECT pg_database_size(?) / 1024 / 1024 AS size;', [
            $connection->getDatabaseName(),
        ])->size;
    }
}
