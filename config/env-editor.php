<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Files Config
    |--------------------------------------------------------------------------
    */
    'paths' => [
        // .env file directory
        'env' => base_path(),
        //backup files directory
        'backupDirectory' => 'env-editor',
    ],
    // .env file name
    'envFileName' => '.env',

    /*
    |--------------------------------------------------------------------------
    | Routes group config
    |--------------------------------------------------------------------------
    |
    */
    'route' => [
        // Prefix url for route Group
        'prefix' => env('SETTING', 'configuration'),
        // Routes base name
        'name' => env('SETTING', 'configuration'),
        // Middleware(s) applied on route Group
        // 'middleware' => ['web', 'auth'],
        'middleware' => ['web', 'auth'],
    ],

    /* ------------------------------------------------------------------------------------------------
    |  Time Format for Views and parsed backups
    | ------------------------------------------------------------------------------------------------
    */
    'timeFormat' => 'd/m/Y H:i:s',

    /* ------------------------------------------------------------------------------------------------
     | Set Views options
     | ------------------------------------------------------------------------------------------------
     | Here you can set The "extends" blade of index.blade.php
    */
    'layout' => 'env-editor::layout',

];
