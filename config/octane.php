<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Octane Server
    |--------------------------------------------------------------------------
    |
    | This value determines the default server that will be used by Octane
    | when starting, restarting, or stopping your server via the given
    | commands. You may change this value at any time.
    |
    | Supported: "roadrunner", "swoole"
    |
    */

    'server' => env('OCTANE_SERVER', 'frankenphp'),

    /*
    |--------------------------------------------------------------------------
    | Force HTTPS
    |--------------------------------------------------------------------------
    |
    | When this configuration value is set to "true", Octane will inform the
    | framework that all absolute links must be generated using the HTTPS
    | protocol. Otherwise, your links may be generated using plain HTTP.
    |
    */

    'https' => env('OCTANE_HTTPS', false),

    /*
    |--------------------------------------------------------------------------
    | Octane Listeners
    |--------------------------------------------------------------------------
    |
    | All of the event listeners for Octane's events are defined below. These
    | listeners are responsible for resetting your application's state for
    | each request. You may even add your own listeners to the list here.
    |
    */

    'listeners' => [
        //
    ],

    /*
    |--------------------------------------------------------------------------
    | Octane Cache
    |--------------------------------------------------------------------------
    |
    | This configuration option allows you to configure a cache store that is
    | used to store data specific to Octane. This cache is used to store
    | information such as the public assets that should be served by Octane.
    |
    */

    'cache' => [
        'store' => env('OCTANE_CACHE_STORE', 'octane'),
        'ttl' => 3600,
    ],

    /*
    |--------------------------------------------------------------------------
    | Octane Tables
    |--------------------------------------------------------------------------
    |
    | The following array lists the Octane tables that should be available via
    | the `Octane::table` method. These tables are defined with a name and
    | a maximum number of rows. The max rows may be an integer or "unlimited".
    |
    */

    'tables' => [
        'example:1000',
        'another_example:unlimited',
    ],

    /*
    |--------------------------------------------------------------------------
    | Octane Server-Specific Options
    |--------------------------------------------------------------------------
    |
    | Here you may configure any server-specific options that might be required
    | by your server. These values will be passed directly to the server via
    | the command line when starting the server. You should consult your
    | server's documentation to learn more about the available options.
    |
    */

    'roadrunner' => [
        //
    ],

    'swoole' => [
        //
    ],
];
