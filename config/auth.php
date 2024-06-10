<?php



    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | This option controls the default authentication "guard" and password
    | reset options for your application. You may change these defaults
    | as required, but they're a perfect start for most applications.
    |
    */

    

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Next, you may define every authentication guard for your application.
    | Of course, a great default configuration has been defined for you
    | here which uses session storage and the Eloquent user provider.
    |
    | All authentication drivers have a user provider. This defines how the
    | users are actually retrieved out of your database or other storage
    | mechanisms used by this application to persist your user's data.
    |
    | Supported: "session"
    |
    */

    return [

        'defaults' => [
            'guard' => 'web',  // Ensure the default guard is set
            'passwords' => 'users',
        ],
    
        'guards' => [
            'web' => [
                'driver' => 'session',
                'provider' => 'users',
            ],
    
                'api' => [
                    'driver' => 'jwt',
                    'provider' => 'users',
                ],
        ],
    
        'providers' => [
            'users' => [
                'driver' => 'eloquent',
                'model' => App\Models\User::class,
            ],
        ],
    
        'passwords' => [
            'users' => [
                'provider' => 'users',
                'table' => 'password_reset_tokens',
                'expire' => 60,
                'throttle' => 60,
            ],
        ],
    
        'password_timeout' => 10800,
    
    ];
       


   
  