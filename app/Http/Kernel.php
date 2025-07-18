<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class,
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's middleware aliases.
     *
     * Aliases may be used instead of class names to conveniently assign middleware to routes and groups.
     *
     * @var array<string, class-string|string>
     */
    protected $middlewareAliases = [
        // Laravel Default Middleware
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'precognitive' => \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
        'signed' => \App\Http\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        
        // ITMS Core Middleware (ระบบหลัก)
        'role' => \App\Http\Middleware\RoleMiddleware::class,
        'permission' => \App\Http\Middleware\PermissionMiddleware::class,
        'admin' => \App\Http\Middleware\AdminMiddleware::class,
        'session.management' => \App\Http\Middleware\SessionManagement::class,
        
        // ITMS Role-Based Middleware (ตามสิทธิ์)
        'super.admin' => \App\Http\Middleware\SuperAdminMiddleware::class,
        'it.admin' => \App\Http\Middleware\ITAdminMiddleware::class,
        'manager' => \App\Http\Middleware\ManagerMiddleware::class,
        'employee' => \App\Http\Middleware\EmployeeMiddleware::class,
        
        // ITMS Feature Middleware (ตามฟีเจอร์)
        'active.user' => \App\Http\Middleware\CheckActiveUser::class,
        'log.activity' => \App\Http\Middleware\LogUserActivity::class,
        'api.auth' => \App\Http\Middleware\ApiAuthenticate::class,
        
        // ITMS Employee Management Middleware (จัดการพนักงาน)
        'can.view.passwords' => \App\Http\Middleware\CanViewPasswordsMiddleware::class,
        'can.manage.employees' => \App\Http\Middleware\CanManageEmployeesMiddleware::class,
        'can.manage.departments' => \App\Http\Middleware\CanManageDepartmentsMiddleware::class,
        'can.export.data' => \App\Http\Middleware\CanExportDataMiddleware::class,
        
        // ITMS Security Middleware (ความปลอดภัย)
        'ip.whitelist' => \App\Http\Middleware\IPWhitelistMiddleware::class,
        'two.factor' => \App\Http\Middleware\TwoFactorMiddleware::class,
        'password.expired' => \App\Http\Middleware\PasswordExpiredMiddleware::class,
        
        // ITMS Utility Middleware (เสริม)
        'maintenance.bypass' => \App\Http\Middleware\MaintenanceBypassMiddleware::class,
        'force.https' => \App\Http\Middleware\ForceHttpsMiddleware::class,
        'locale' => \App\Http\Middleware\LocaleMiddleware::class,
    ];
}
