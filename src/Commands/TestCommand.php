<?php

namespace Larawatch\Commands;

use Exception;
use Illuminate\Console\Command;
use Larawatch\Larawatch;

class TestCommand extends Command
{
    protected $signature = 'larawatch:test {exception?}';

    protected $description = 'Generate a test exception and send it to larawatch';

    public function handle()
    {
        try {
            /** @var Larawatch $larawatch */
            $larawatch = app('larawatch');

            if (config('larawatch.login_key')) {
                $this->info('✓ [Larawatch] Found login key');
            } else {
                $this->error('✗ [Larawatch] Could not find your login key, set this in your .env');
            }

            if (config('larawatch.project_key')) {
                $this->info('✓ [Larawatch] Found project key');
            } else {
                $this->error('✗ [Larawatch] Could not find your project key, set this in your .env');
            }

            if (in_array(config('app.env'), config('larawatch.environments'))) {
                $this->info('✓ [Larawatch] Correct environment found ('.config('app.env').')');
            } else {
                $this->error('✗ [Larawatch] Environment ('.config('app.env').') not allowed to send errors to Larawatch, set this in your config');
            }

            $response = $larawatch->handle(
                $this->generateException()
            );

            if (isset($response->id)) {
                $this->info('✓ [Larawatch] Sent exception to Larawatch with ID: '.$response->id);
            } elseif (is_null($response)) {
                $this->info('✓ [Larawatch] Sent exception to Larawatch!');
            } else {
                $this->error('✗ [Larawatch] Failed to send exception to Larawatch');
            }
        } catch (\Exception $ex) {
            $this->error("✗ [Larawatch] {$ex->getMessage()}");
        }
    }

    public function generateException(): ?Exception
    {
        try {
            throw new Exception($this->argument('exception') ?? 'This is a test exception from the Larawatch console');
        } catch (Exception $ex) {
            return $ex;
        }
    }
}
