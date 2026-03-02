<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Impersonation Redirection
    |--------------------------------------------------------------------------
    |
    | This option controls where users are redirected after impersonation.
    |
    */

    'redirect_to' => '/admin',

    /*
    |--------------------------------------------------------------------------
    | Leave Impersonation Redirection
    |--------------------------------------------------------------------------
    |
    | This option controls where admins are redirected after leaving impersonation.
    |
    */

    'leave_redirect_to' => '/admin/users',

    /*
    |--------------------------------------------------------------------------
    | Impersonation Enabled
    |--------------------------------------------------------------------------
    |
    | This option controls whether the impersonation feature is enabled.
    |
    */

    'enabled' => true,

    /*
    |--------------------------------------------------------------------------
    | Impersonation Field
    |--------------------------------------------------------------------------
    |
    | This option controls which field is used to identify users.
    |
    */

    'field' => 'id',
];