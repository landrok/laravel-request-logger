<?php

return [

    /*
     * Determine if the request logger middleware should be enabled.
     */
    'enabled' => env('REQUEST_LOGGER_ENABLED', true),

    /*
     * The middleware will be applied on the following groups
     */
    'groups' => [
        'api',
        'web',
    ],

    /*
     * The database connection
     */    
    'connection' => config('database.default'), 

    /*
     * The table name
     */
    'tablename' => 'request_logs',

    /*
     * The table name
     */
    'user_model' => config('auth.providers.users.model'),

    /*
     * The fields that will be logged
     */
    'fields' => [

        // User_
        "session_id" => true,
        "user_id" => true,
        "ip" => true,
        "route" => true,
        "route_params" => false,

        // Performances__
        "duration" => true,
        "mem_alloc" => true,

        // HTTP stuff
        "method" => true,
        "status_code" => true,
        "url" => true,
        "referer" => true,
        "referer_host" => true,
        "request_headers" => false,
        "response_headers" => false,

        // Device    
        "device" => true,
        "os" => true,
        "os_version" => true,
        "browser" => true,
        "browser_version" => true,
        "is_desktop" => true,
        "is_mobile" => true,
        "is_tablet" => true,
        "is_phone" => true,
        "is_robot" => true,
        "robot_name" => true,
        "user_agent" => true,

        // Miscellaneous
        "meta" => false, // Used for custom logging
        "created_at" => true,
    ],
];
