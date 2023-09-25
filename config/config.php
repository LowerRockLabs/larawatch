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
        'enabled' => true,

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

    'larawatch_public_key' => 'LS0tLS1CRUdJTiBDRVJUSUZJQ0FURS0tLS0tCk1JSUZxakNDQTVLZ0F3SUJBZ0lDRUFBd0RRWUpLb1pJaHZjTkFRRUxCUUF3Z2NveEN6QUpCZ05WQkFZVEFrZEMKTVJBd0RnWURWUVFJREFkRmJtZHNZVzVrTVJ3d0dnWURWUVFLREJOTWIzZGxjaUJTYjJOcklFeGhZbk1nVEhSawpNVEl3TUFZRFZRUUxEQ2xNYjNkbGNpQlNiMk5ySUV4aFluTWdUSFJrSUVObGNuUnBabWxqWVhSbElFRjFkR2h2CmNtbDBlVEVzTUNvR0ExVUVBd3dqVEc5M1pYSWdVbTlqYXlCTVlXSnpJRXgwWkNCSmJuUmxjbTFsWkdsaGRHVWcKUTBFeEtUQW5CZ2txaGtpRzl3MEJDUUVXR25ObFkzVnlhWFI1UUd4dmQyVnljbTlqYTJ4aFluTXVZMjl0TUI0WApEVEl6TURreE9ERTJNakl5TTFvWERUSTBNRGt5TnpFMk1qSXlNMW93Z1p3eEN6QUpCZ05WQkFZVEFrZENNUkF3CkRnWURWUVFJREFkRmJtZHNZVzVrTVE4d0RRWURWUVFIREFaTWIyNWtiMjR4SERBYUJnTlZCQW9NRTB4dmQyVnkKSUZKdlkyc2dUR0ZpY3lCTWRHUXhFakFRQmdOVkJBc01DVXhoY21GM1lYUmphREVTTUJBR0ExVUVBd3dKVEdGeQpZWGRoZEdOb01TUXdJZ1lKS29aSWh2Y05BUWtCRmhWemRYQndiM0owUUd4aGNtRjNZWFJqYUM1amIyMHdnZ0VpCk1BMEdDU3FHU0liM0RRRUJBUVVBQTRJQkR3QXdnZ0VLQW9JQkFRRFpTYkU5RWdHK3EvSFdQWE9DWElMQWdRS3QKdHVnUVhERE5KUlB5TE9xY3Z5aG52NThFaFE3VG5EOWFnSjNVSnk5SHJzR0Nnc054RkYrT2o3SStzRmtNUEZycQpmM2U3UVBoQkF4dHBIV2dyOWpoVTk1VlpONjVESEVWeVFTUkhiaDBPeE4xcGNWSXBWMmxhSldQUk8wU1JNQXpzCmo2SVlCRlA4aFhFN3ZrYXVsaDd1eXNIV2hHYVBNd0JXZk1NR3VnNk5LNmg5aWtYbWJEaitOZEdBZTdEaG9zQnEKM3d3WHQrWVRtdk9LY1JvZkMyelhuZVo3LzNDSlZGVUk0N0sySUYydStweE5BMENyTlk3RExrTkUzQzhWYTU0VApjT3RoU1Q1M2dneTJOQXNHaTM5bW9GV05kZDdMZlZ1c1ZuQ0JnaUZRQzloQ2NBZ0FxU0MxTHIvTWliK0ZBZ01CCkFBR2pnY1V3Z2NJd0NRWURWUjBUQkFJd0FEQVJCZ2xnaGtnQmh2aENBUUVFQkFNQ0JhQXdNd1lKWUlaSUFZYjQKUWdFTkJDWVdKRTl3Wlc1VFUwd2dSMlZ1WlhKaGRHVmtJRU5zYVdWdWRDQkRaWEowYVdacFkyRjBaVEFkQmdOVgpIUTRFRmdRVUJwbW1ObU5nVDdwRU1kallLWmsxc2N6T2Q0d3dId1lEVlIwakJCZ3dGb0FVcTVPQkR2NWhjOWxpCmFLUGpWdjF5RzBIc0ZNTXdEZ1lEVlIwUEFRSC9CQVFEQWdYZ01CMEdBMVVkSlFRV01CUUdDQ3NHQVFVRkJ3TUMKQmdnckJnRUZCUWNEQkRBTkJna3Foa2lHOXcwQkFRc0ZBQU9DQWdFQUpPWlRJelhQdzB2dk1ueFRWV2cvb216VQpzNlZnQmNVNnV3Z3U0NFN4c0JDZHhraG5EU2xQWWtTYXVTRExCMW5uOEQyb1pMK1FUcG5tbjMzYlBuR1dTajFlCjJvcTJLeXo5Vmk0SmllU2dFQTNadU1JNlFPNmw4bThkd2E4YjJ6dkE1WVBld2RuMVd1cTVMK3VMT1A4ZEJaNkYKckJuME8wbW5RcW53a2tYVDBTcnpjUDh2aHZ5Sk5UTWRqRkVtTGpoaTNhaUQ2OHNhNGt1ZjBkRmRXVWFvYTQxVgoyNTkveW9MOVBKUGZmS3MxRWloNVBqSDZLbUc5djVuZlB6OXI2RVVrbks0R0N1WXh3amJ5QzRkeWY1aUVTdGliCnRtcnJFTEtVS3VMT0NMbEdubHc2L3BtVzNOcVYxai9xVkVlLzkxZ0VRQ2VaclJsLzJ4THJHYXZ3RWE2cURud2UKbTZXcitacXdqazNjcHNHSzMxcHRyK2lmT2hrYzFEVzQ4aUZiczhoRHF0ckhWZDRUaVVCZHNzam9FdHJ4YmoyZQpNNmxKaXJCYUJsWWhpWlZKZFJCcU5ub29YYzJ3Z3Q4THRCeEUvdENWNGdzZnJOUFhHN2MxdVhuSG1LQkNVQ3ljClRGQkRxL1N1MFZqU2ZMekZ4Zmpya3hnZTR5elBGMDh5Y1Q4SG9xWEgvNjZzaTl5d1NQQ3c5UGRNYStIZWhVc3YKYTdzc1ZwSlk4NHFvNHJQR3VTOWtOUmd5Y01ZYWloMk0yYytUZDlKYkxHYldNSGx5RUt2b0RsVGtFRE5EeThKcQpWNkxYdlRRT2RNZG5CMm53TnBkOCs4cnp4SXg3KzlaTkFkWXlPOW1NajJYMUMwQnhLZGlNaEg2YzVUR1NPTWhSCmNIenlZTkJwNlc3OU1Qek5OaEU9Ci0tLS0tRU5EIENFUlRJRklDQVRFLS0tLS0=',
];
