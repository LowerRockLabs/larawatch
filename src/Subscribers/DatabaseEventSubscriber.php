<?php

namespace Larawatch\Subscribers;

use Illuminate\Database\Events\DatabaseBusy;
use Illuminate\Events\Dispatcher;
use Larawatch\Jobs\SendDatabaseBusyToAPI;

class DatabaseEventSubscriber
{
    public function subscribe(Dispatcher $events): void
    {
        $events->listen(
            DatabaseBusy::class,
            fn (DatabaseBusy $event) => SendDatabaseBusyToAPI::dispatch()
        );
    }
}
