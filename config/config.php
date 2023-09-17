<?php

// config for Larawatch
return [

    /*
     * The base URL to use when sending reports.  Most functionality requires Larawatch endpoints
     */
    'base_url' => env('LARAWATCH_BASE_URL') ?? 'https://dev.larawatch.com/api/',

    /*
     * The unique project key, available via the Dashboard. Should be unique to a project
     */
    'project_key' => env('LARAWATCH_PROJECT_KEY') ?? 'unknown',

    /*
     * The unique server key, available via the Dashboard.  Should be unique to a server
     */
    'server_key' => env('LARAWATCH_SERVER_KEY') ?? 'unknown',

    /*
     * The unique NEL Logging key, available via the Dashboard.  Should be unique to your project, and will be publicly exposed.
     */
    'nel_key' => env('LARAWATCH_NEL_KEY') ?? 'unknown',

    /*
     * Your unique authentication token, available via the Dashboard.
     */
    'destination_token' => env('LARAWATCH_TOKEN'),

    /*
    * Server to submit bugs to
    */
    'bug_server' => env('LARAWATCH_BUG_SERVER', 'dev.larawatch.com'),

    /*
    * Server to submit other stuff to
    */
    'server' => env('LARAWATCH_SERVER', 'https://dev.larawatch.com/api/log'),

    /*
    * Whether to verify SSL (Recommended)
    */
    'verify_ssl' => env('LARAWATCH_VERIFY_SSL', true),


    /*
     * The date format used for all dates displayed on the output of commands
     * provided by this package.
     */
    'date_format' => 'Y-m-d H:i:s',
    

    // Used if using routes to run checks
    'routes' => [
        // Enable Routes for Larawatch
        'enable' => false,

        // Route Prefix
        'route_prefix' => 'larawatch',

        // Route Name Prefix
        'route_name_prefix' => 'larawatch',
    ],

    'checks' => [
        'diskName' => 'local',
        'folderPath' => 'larawatch',
        'storage' => 'database',
        'databases_active' => [],
        'databases_ignore' => ['pgsql'],

        /* File System Checks */
        'local_filesystems' => [],

        'cloud_filesystems' => [],
    ],

    'checks_filesystems' => [

        'local_filesystems' => [],

        'local_filesystems_defaults' => [
            'usage_checks' => true, 
            'performance_checks' => true,
        ],

        'cloud_filesystems' => [],

        'cloud_filesystems_defaults' => [
            'usage_checks' => true, 
            'performance_checks' => true,
        ],

    ],

    'models' => [
        /*
         * The model you want to use as a MonitoredScheduledTask model
         */
        'monitored_scheduled_task' => Larawatch\Models\MonitoredScheduledTask::class,

        /*
         * The model you want to use as a MonitoredScheduledTaskLogItem model
         */
        'monitored_scheduled_log_item' => Larawatch\Models\MonitoredScheduledTaskLogItem::class,
    ],

    'lowerrocklabs' => [
        'queue' => 'general-queue',
        'retry_job_for_minutes' => 10,
        'send_starting_ping' => true,
    ],

    'environments' => [
        'production',
    ],

    'project_version' => null,

    'lines_count' => 25,
    'sleep' => 5,
    'except' => [
        'Symfony\Component\HttpKernel\Exception\NotFoundHttpException',
    ],

    'blacklist' => [
        '*authorization*',
        '*password*',
        '*token*',
        '*auth*',
        '*verification*',
        '*credit_card*',
        'cardToken', // mollie card token
        '*cvv*',
        '*iban*',
        '*name*',
        '*email*',
    ],
    // 'release' => trim(exec('git --git-dir ' . base_path('.git') . ' log --pretty="%h" -n1 HEAD')),



    /**
     * Feature Configuration
     * 
     * Here you may enable/disable features here
     */

    'enable_performance_metrics' => env('LARAWATCH_ENABLE_PERFORMANCE_METRICS') ?? false,

    'database_busy' => [
        'enabled' => env('LARAWATCH_DB_MAXCONNECTIONS_ENABLED') ?? false, // Enable or Disable the Max Connections Monitor
        'threshold' => env('LARAWATCH_DB_MAXCONNECTIONS_THRESHOLD') ?? 500, // Specify a threshold for reporting on Max Connections
    ],

    'slow_query' => [
        'enabled' => env('LARAWATCH_SLOWQUERY_ENABLED') ?? false, // Enable or Disable the Slow Query Monitor
        'threshold' => env('LARAWATCH_DB_MAXCONNECTIONS_THRESHOLD') ?? 500, // Specify a threshold for reporting on Slow Query
    ],

    'server_stats' => [
        /**
         *  You should enable either the web or job
         */
        'web_enabled' =>  env('LARAWATCH_SERVER_STATS_WEB_ENABLED') ?? false, // Enable or Disable the Server Stats Web API
        'job_enabled' => env('LARAWATCH_SERVER_STATS_JOB_ENABLED') ?? false, // Enable or Disable the Server Stats Job

        'queue' => env('LARAWATCH_SERVER_STATS_QUEUE') ?? null, // Set the Queue to use for the Server Stats job
        'connection' => env('LARAWATCH_SERVER_STATS_CONNECTION') ?? null, // Set the Queue Connection to use for the Server Stats job
    ],
];
