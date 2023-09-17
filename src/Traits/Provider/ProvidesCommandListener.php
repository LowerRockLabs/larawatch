<?php

namespace Larawatch\Traits\Provider;

use Illuminate\Console\Events\CommandStarting;
use Illuminate\Support\Facades\Event;
use Larawatch\Subscribers\CommandEventSubscriber;

trait ProvidesCommandListener
{
    protected function setupCommandListener()
    {
        Event::listen(CommandStarting::class, CommandEventSubscriber::class);

    }

}
