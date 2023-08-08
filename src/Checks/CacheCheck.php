<?php

namespace Larawatch\Checks;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CacheCheck extends BaseCheck
{
    protected ?string $driver = null;

    protected string $expression = '*/5 * * * *';


    public function driver(string $driver): self
    {
        $this->driver = $driver;

        return $this;
    }

    public function run(): CheckResult
    {
        $driver = $this->driver ?? $this->defaultDriver();

        $result = CheckResult::make(started_at: $this->checkStartTime)->resultData([
            'driver' => $driver,
        ]);

        try {
            return $this->canWriteValuesToCache($driver)
                ? $result->ok()
                : $result->failed('Could not set or retrieve an application cache value.');
        } catch (Exception $exception) {
            return $result->failed("An exception occurred with the application cache: `{$exception->getMessage()}`");
        }
    }

    protected function defaultDriver(): string
    {
        return config('cache.default', 'file');
    }

    protected function canWriteValuesToCache(string $driver): bool
    {
        $expectedValue = Str::random(5);

        Cache::driver($driver)->put('larawatch:cache-check', $expectedValue, 10);

        $actualValue = Cache::driver($driver)->get('larawatch:cache-check');

        return $actualValue === $expectedValue;
    }
}
