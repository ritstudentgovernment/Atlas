<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Views / Endpoints
    |--------------------------------------------------------------------------
    |
    | Set your login page, or login routes, here. If you provide a view,
    | that will be rendered. Otherwise, it will redirect to a route.
    |
     */

    'idp_login'     => '/Shibboleth.sso/Login',
    'idp_logout'    => '/Shibboleth.sso/Logout',
    'authenticated' => '/',

    /*
    |--------------------------------------------------------------------------
    | Emulate an IdP
    |--------------------------------------------------------------------------
    |
    | In case you do not have access to your Shibboleth environment on
    | homestead or your own Vagrant box, you can emulate a Shibboleth
    | environment with the help of Shibalike.
    |
    | The password is the same as the username.
    |
    | Do not use this in production for literally any reason.
    |
     */

    'emulate_idp'       => true,
    'emulate_idp_users' => [
        'admin' => [
            'emplId'    => 'admin',
            'cn'        => 'Admin User',
            'givenName' => 'Admin',
            'sn'        => 'User',
            'mail'      => 'admin@rit.edu',
        ],
        'staff' => [
            'emplId'    => 'staff',
            'cn'        => 'Staff User',
            'givenName' => 'Staff',
            'sn'        => 'User',
            'mail'      => 'staff@rit.edu',
        ],
        'user' => [
            'emplId'    => 'user',
            'cn'        => 'User User',
            'givenName' => 'User',
            'sn'        => 'User',
            'mail'      => 'user@rit.edu',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Server Variable Mapping
    |--------------------------------------------------------------------------
    |
    | Change these to the proper values for your IdP.
    |
     */

    'entitlement' => 'isMemberOf',

    'user' => [
        // fillable user model attribute => server variable
        'name'       => 'cn',
        'email'      => 'mail',
        'first_name' => 'givenName',
        'last_name'  => 'sn',
    ],

    /*
    |--------------------------------------------------------------------------
    | User Creation and Groups Settings
    |--------------------------------------------------------------------------
    |
    | Allows you to change if / how new users are added
    |
     */

    'add_new_users' => true, // Should new users be added automatically if they do not exist?

    /*
    |--------------------------------------------------------------------------
    | JWT Auth
    |--------------------------------------------------------------------------
    |
    | JWTs are for the front end to know it's logged in
    |
    | https://github.com/tymondesigns/jwt-auth
    | https://github.com/StudentAffairsUWM/Laravel-Shibboleth-Service-Provider/issues/24
    |
     */

    'jwtauth' => env('JWTAUTH', false),
];
