<?php

namespace Larawatch\Traits\Provider;

use Larawatch\Traits\Provider\{ManagesSchedulerMacros, ProvidesCommandListener, ProvidesConsoleOptions, ProvidesErrorParsing, ProvidesLarawatchClient,ProvidesQueryLogging};

trait ProvidesProviderTraits
{
    use ManagesSchedulerMacros;
    use ProvidesCommandListener;
    use ProvidesConsoleOptions;
    use ProvidesErrorParsing;
    use ProvidesLarawatchClient;
    use ProvidesQueryLogging;
}
