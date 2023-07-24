<?php

namespace Larawatch\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Larawatch\Subscribers\DatabaseEventSubscriber;
use Larawatch\Subscribers\ScheduledEventSubscriber;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
        ScheduledEventSubscriber::class,
        DatabaseEventSubscriber::class,
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
