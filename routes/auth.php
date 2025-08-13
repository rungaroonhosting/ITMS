<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication Routes - Employee Management System v2.1
|--------------------------------------------------------------------------
| Fixed to use proper controllers instead of Livewire Volt
*/

Route::middleware('guest')->group(function () {
    // ✅ Login Routes - Using AuthController
    Route::get('login', [AuthController::class, 'showLoginForm'])
        ->name('login');
    
    Route::post('login', [AuthController::class, 'login'])
        ->name('login.store');

    // ✅ Registration Routes (if needed in future)
    Route::get('register', function () {
        // For now, redirect to login or show coming soon page
        return redirect()->route('login')->with('info', 'การสมัครสมาชิกจะเปิดให้บริการในอนาคต');
    })->name('register');

    // ✅ Password Reset Routes
    Route::get('forgot-password', function () {
        return view('auth.forgot-password');
    })->name('password.request');

    Route::post('forgot-password', [AuthController::class, 'sendResetLink'])
        ->name('password.email');

    Route::get('reset-password/{token}', function ($token) {
        return view('auth.reset-password', ['token' => $token]);
    })->name('password.reset');

    Route::post('reset-password', [AuthController::class, 'resetPassword'])
        ->name('password.update');
});

Route::middleware('auth')->group(function () {
    // ✅ Logout Route
    Route::post('logout', [AuthController::class, 'logout'])
        ->name('logout');

    // ✅ Email Verification Routes
    Route::get('verify-email', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    // ✅ Password Confirmation
    Route::get('confirm-password', function () {
        return view('auth.confirm-password');
    })->name('password.confirm');

    Route::post('confirm-password', [AuthController::class, 'confirmPassword'])
        ->name('password.confirm.store');

    // ✅ Profile Management
    Route::get('profile', [AuthController::class, 'showProfile'])
        ->name('profile');
        
    Route::put('profile', [AuthController::class, 'updateProfile'])
        ->name('profile.update');
});

// ✅ Additional Security Routes
Route::middleware(['auth', 'throttle:3,1'])->group(function () {
    // Two-factor authentication (for future implementation)
    Route::get('two-factor-challenge', function () {
        return view('auth.two-factor-challenge');
    })->name('two-factor.login');
    
    Route::post('two-factor-challenge', [AuthController::class, 'twoFactorChallenge'])
        ->name('two-factor.verify');
});

// ✅ Admin-only auth management routes
Route::middleware(['auth', 'role:super_admin'])->prefix('admin/auth')->group(function () {
    Route::get('users', [AuthController::class, 'manageUsers'])
        ->name('admin.auth.users');
        
    Route::post('users/{user}/suspend', [AuthController::class, 'suspendUser'])
        ->name('admin.auth.suspend');
        
    Route::post('users/{user}/activate', [AuthController::class, 'activateUser'])
        ->name('admin.auth.activate');
        
    Route::post('users/{user}/force-logout', [AuthController::class, 'forceLogout'])
        ->name('admin.auth.force-logout');
});

// ✅ Development/Debug Routes (only in local environment)
if (app()->environment('local')) {
    Route::get('auth/debug', function () {
        return [
            'user' => auth()->user(),
            'guard' => config('auth.defaults.guard'),
            'provider' => config('auth.providers.employees'),
            'session' => session()->all(),
            'routes' => [
                'login' => route('login'),
                'logout' => route('logout'),
                'dashboard' => route('dashboard'),
            ]
        ];
    })->name('auth.debug');
}
