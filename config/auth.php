<?php

return [

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

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'employees',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Next, you may define every authentication guard for your application.
    | Of course, a great default configuration has been defined for you
    | here which uses session storage and the Employee model.
    |
    | All authentication drivers have a user provider. This defines how the
    | users are actually retrieved out of your database or other storage
    | mechanisms used by this application to persist your user's data.
    |
    | Supported: "session"
    |
    */

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'employees',
        ],

        'api' => [
            'driver' => 'sanctum',
            'provider' => 'employees',
            'hash' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | All authentication drivers have a user provider. This defines how the
    | users are actually retrieved out of your database or other storage
    | mechanisms used by this application to persist your user's data.
    |
    | If you have multiple user tables or models you may configure multiple
    | sources which represent each model / table. These sources may then
    | be assigned to any extra authentication guards you have defined.
    |
    | Supported: "database", "eloquent"
    |
    */

    'providers' => [
        'employees' => [
            'driver' => 'eloquent',
            'model' => App\Models\Employee::class,
        ],

        // Keep users provider for backwards compatibility if needed
        // You can remove this if you're not using the default User model
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | These configuration options specify the behavior of Laravel's password
    | reset functionality, including the table utilized for token storage
    | and the user provider that is invoked to actually retrieve users.
    |
    | The expire time is the number of minutes that each reset token will be
    | considered valid. This security feature keeps tokens short-lived so
    | they have less time to be guessed. You may change this as needed.
    |
    | The throttle setting is the number of seconds a user must wait before
    | generating more password reset tokens. This prevents the user from
    | quickly generating a very large amount of password reset tokens.
    |
    */

    'passwords' => [
        'employees' => [
            'provider' => 'employees',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],

        // Keep users password reset for backwards compatibility
        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    |
    | Here you may define the amount of seconds before a password confirmation
    | times out and the user is prompted to re-enter their password via the
    | confirmation screen. By default, the timeout lasts for three hours.
    |
    */

    'password_timeout' => 10800, // 3 hours

    /*
    |--------------------------------------------------------------------------
    | Custom Authentication Settings
    |--------------------------------------------------------------------------
    |
    | Here you can define custom settings for the Employee authentication
    | system that are specific to your IT Management System.
    |
    */

    'employee_settings' => [
        /*
        | Login identifier field
        | You can change this to 'login_email' or 'username' if needed
        */
        'login_field' => 'email',

        /*
        | Remember me duration (in minutes)
        | Default is 2 weeks (20160 minutes)
        */
        'remember_duration' => 20160,

        /*
        | Session lifetime for employees (in minutes)
        | Default is 8 hours (480 minutes)
        */
        'session_lifetime' => 480,

        /*
        | Maximum login attempts before lockout
        */
        'max_login_attempts' => 5,

        /*
        | Lockout duration in minutes
        */
        'lockout_duration' => 30,

        /*
        | Require email verification for new employees
        */
        'require_email_verification' => false,

        /*
        | Auto-logout inactive users (in minutes)
        | Set to 0 to disable auto-logout
        */
        'auto_logout_duration' => 0,

        /*
        | Force password change on first login
        */
        'force_password_change_on_first_login' => false,

        /*
        | Password expiry duration (in days)
        | Set to 0 to disable password expiry
        */
        'password_expiry_days' => 0,

        /*
        | Roles that can bypass certain authentication restrictions
        */
        'admin_roles' => ['super_admin', 'it_admin'],

        /*
        | Default role for new employees
        */
        'default_role' => 'employee',

        /*
        | Allowed domains for employee emails
        */
        'allowed_email_domains' => [
            'bettersystem.co.th',
            'better-groups.com',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Two-Factor Authentication (Future Implementation)
    |--------------------------------------------------------------------------
    |
    | These settings are for future implementation of 2FA.
    | Currently not implemented but prepared for future use.
    |
    */

    'two_factor' => [
        'enabled' => false,
        'required_roles' => ['super_admin', 'it_admin'],
        'grace_period' => 7, // days
        'backup_codes_count' => 8,
    ],

    /*
    |--------------------------------------------------------------------------
    | API Authentication Settings
    |--------------------------------------------------------------------------
    |
    | Settings specific to API authentication using Sanctum tokens.
    |
    */

    'api' => [
        /*
        | Token expiration time (in minutes)
        | Set to null for tokens that don't expire
        */
        'token_expiry' => 1440, // 24 hours

        /*
        | Token name prefix
        */
        'token_name_prefix' => 'ITMS',

        /*
        | Rate limiting for API requests
        */
        'rate_limit' => '60:1', // 60 requests per minute

        /*
        | Abilities/permissions for API tokens
        */
        'token_abilities' => [
            'read' => 'Read access to resources',
            'write' => 'Write access to resources',
            'delete' => 'Delete access to resources',
            'admin' => 'Full administrative access',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Headers and CSRF Protection
    |--------------------------------------------------------------------------
    |
    | Additional security settings for authentication.
    |
    */

    'security' => [
        /*
        | Enable CSRF protection for authentication routes
        */
        'csrf_protection' => true,

        /*
        | Session security settings
        */
        'session_security' => [
            'secure_cookies' => env('SESSION_SECURE_COOKIE', false),
            'same_site_cookies' => 'lax',
            'http_only_cookies' => true,
        ],

        /*
        | IP address validation
        */
        'validate_ip' => false,

        /*
        | User agent validation
        */
        'validate_user_agent' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging Settings
    |--------------------------------------------------------------------------
    |
    | Authentication events that should be logged.
    |
    */

    'logging' => [
        'enabled' => true,
        'events' => [
            'login' => true,
            'logout' => true,
            'failed_login' => true,
            'password_reset' => true,
            'password_change' => true,
            'account_lockout' => true,
        ],
        'log_channel' => 'auth', // Custom log channel for auth events
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Rules
    |--------------------------------------------------------------------------
    |
    | Custom validation rules for employee authentication.
    |
    */

    'validation' => [
        'password_rules' => [
            'min_length' => 6,
            'require_uppercase' => false,
            'require_lowercase' => false,
            'require_numbers' => false,
            'require_symbols' => false,
            'max_length' => 255,
        ],
        
        'username_rules' => [
            'min_length' => 3,
            'max_length' => 100,
            'allowed_characters' => 'alphanumeric_dot_underscore',
        ],
        
        'email_rules' => [
            'max_length' => 255,
            'require_verification' => false,
        ],
    ],

];