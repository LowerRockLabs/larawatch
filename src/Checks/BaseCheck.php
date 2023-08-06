<?php

namespace Larawatch\Checks;

use Cron\CronExpression;
use Illuminate\Console\Scheduling\ManagesFrequencies;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;

abstract class BaseCheck
{
    use ManagesFrequencies;
    use Macroable;
    use Conditionable {
        unless as doUnless;
    }

    protected string $expression = '* * * * *';

    protected ?string $name = null;

    /**
     * @var array<bool|callable(): bool>
     */
    protected array $shouldRun = [];

    public function __construct()
    {
    }

    public static function new(): static
    {
        $instance = app(static::class);

        $instance->everyMinute();

        return $instance;
    }

    public function name(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        if ($this->name) {
            return $this->name;
        }

        $baseName = class_basename(static::class);

        return Str::of($baseName)->beforeLast('Check');
    }

    public function setExpression(string $expression): static    
    {
        $this->expression = $expression;
        
        return $this;
    }

    public function shouldRun(): bool
    {
        foreach ($this->shouldRun as $shouldRun) {
            $shouldRun = is_callable($shouldRun) ? $shouldRun() : $shouldRun;

            if (! $shouldRun) {
                return false;
            }
        }

        $date = Date::now();

        return (new CronExpression($this->expression))->isDue($date->toDateTimeString());
    }

    public function if(bool|callable $condition)
    {
        $this->shouldRun[] = $condition;

        return $this;
    }

    public function unless(bool|callable $condition)
    {
        $this->shouldRun[] = is_callable($condition) ?
            fn () => ! $condition() :
            ! $condition;

        return $this;
    }

    abstract public function run(): CheckResult;
    
    public function markAsCrashed(): CheckResult
    {
        return new CheckResult(status: 'crashed');
    }

    public function onTerminate(mixed $request, mixed $response): void
    {
    }
}
