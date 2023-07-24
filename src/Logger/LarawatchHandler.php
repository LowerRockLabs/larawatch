<?php

namespace Larawatch\Logger;

use Larawatch\Larawatch;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Throwable;

class LarawatchHandler extends AbstractProcessingHandler
{
    /** @var Larawatch */
    protected $laraWatch;

    /**
     * @param  int  $level
     */
    public function __construct(Larawatch $laraWatch, $level = Logger::ERROR, bool $bubble = true)
    {
        $this->laraWatch = $laraWatch;

        parent::__construct($level, $bubble);
    }

    /**
     * @param  array  $record
     */
    protected function write($record): void
    {
        if (isset($record['context']['exception']) && $record['context']['exception'] instanceof Throwable) {
            $this->laraWatch->handle(
                $record['context']['exception']
            );

            return;
        }
    }
}
