<?php

return [
     /*-------------------------------------------------------------------------
     | The application's global HTTP middleware stack.
     |--------------------------------------------------------------------------
     |
     | These middleware are run during every request to your application.
     */
    'global' => [],

    /*-------------------------------------------------------------------------
     | The application's middleware
     |--------------------------------------------------------------------------
     |
     | These have to be assigned to a route
     */
    'middleware' => [
        'auth' => \app\framework\Component\Auth\Authenticate::class
    ],

    /*-------------------------------------------------------------------------
     | The application's route middleware groups.
     |--------------------------------------------------------------------------
     |
     | Sometimes you may want to group several middleware under a
     | single key to make them easier to assign to routes.
     */
    'groups' => []
];
