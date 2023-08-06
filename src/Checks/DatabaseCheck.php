<?php

namespace Larawatch\Checks;

use Illuminate\Database\ConnectionResolverInterface;
use Larawatch\Support\DBInfo;

class DatabaseCheck extends BaseCheck
{
    protected ?string $connectionName = null;

    public function connectionName(string $connectionName): self
    {
        $this->connectionName = $connectionName;

        return $this;
    }


    public function run(): CheckResult
    {
        $databaseSizeInGb = $this->getDatabaseSizeInGb();

        $result = CheckResult::make()
            ->resultData([
                'database_size' => $databaseSizeInGb,
            ]);

        return $result->ok();
    }

    protected function getDefaultConnectionName(): string
    {
        return config('database.default');
    }

    protected function getDatabaseSizeInGb(): float
    {
        $connectionName = $this->connectionName ?? $this->getDefaultConnectionName();

        $connection = app(ConnectionResolverInterface::class)->connection($connectionName);

        return round((new DBInfo())->databaseSizeInMb($connection) / 1000, 2);
    }
}
