<?php

return [

    /*
    |--------------------------------------------------------------------------
    | View Storage Paths
    |--------------------------------------------------------------------------
    |
    | Most templating systems load templates from disk. Here you may specify
    | an array of paths that should be checked for your views. Of course
    | the usual Laravel view path has already been registered for you.
    |
    */

    'paths' => [
        resource_path('views'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Compiled View Path
    |--------------------------------------------------------------------------
    |
    | This option determines where all the compiled Blade templates will be
    | stored for your application. Typically, this is within the storage
    | directory. However, as usual, you are free to change this value.
    |
    */

    'compiled' => env(
        'VIEW_COMPILED_PATH',
        realpath(storage_path('framework/views'))
    ),

    /*
    |--------------------------------------------------------------------------
    | View Cache
    |--------------------------------------------------------------------------
    |
    | This option determines if views should be cached when the environment
    | is production. When this option is set to true, views will be cached
    | for better performance.
    |
    */

    'cache' => env('VIEW_CACHE', true),

    /*
    |--------------------------------------------------------------------------
    | View Hash
    |--------------------------------------------------------------------------
    |
    | This option determines if the view cache should be hashed to prevent
    | long file names and potential collisions on some operating systems.
    |
    */

    'hash' => env('VIEW_HASH', false),

    /*
    |--------------------------------------------------------------------------
    | Relative Cache Path
    |--------------------------------------------------------------------------
    |
    | This option determines if the view cache path should be relative to
    | the application base path. This is useful for some deployment scenarios.
    |
    */

    'relative_cache_path' => env('VIEW_RELATIVE_CACHE_PATH', false),

];
