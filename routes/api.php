<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public API routes
Route::prefix('v1')->group(function () {
    // Authentication
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/forgot-password', [AuthController::class, 'requestPasswordReset']);
    Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);
    
    // Protected API routes
    Route::middleware(['auth:sanctum', 'session.management'])->group(function () {
        // Authentication
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/user', [AuthController::class, 'user']);
        Route::post('/auth/refresh', [AuthController::class, 'refresh']);
        Route::post('/auth/change-password', [AuthController::class, 'changePassword']);
        
        // Employee Module API (ถ้ามี)
        Route::prefix('employees')->middleware('permission:employees.view')->group(function () {
            Route::get('/', function() {
                return response()->json(['message' => 'Employee list endpoint']);
            });
            Route::post('/', function() {
                return response()->json(['message' => 'Create employee endpoint']);
            })->middleware('permission:employees.create');
            Route::get('/{id}', function($id) {
                return response()->json(['message' => "Employee {$id} details endpoint"]);
            });
            Route::put('/{id}', function($id) {
                return response()->json(['message' => "Update employee {$id} endpoint"]);
            })->middleware('permission:employees.update');
            Route::delete('/{id}', function($id) {
                return response()->json(['message' => "Delete employee {$id} endpoint"]);
            })->middleware('permission:employees.delete');
        });
        
        // Asset Module API (ถ้ามี)
        Route::prefix('assets')->middleware('permission:assets.view')->group(function () {
            Route::get('/', function() {
                return response()->json(['message' => 'Asset list endpoint']);
            });
            Route::post('/', function() {
                return response()->json(['message' => 'Create asset endpoint']);
            })->middleware('permission:assets.create');
            Route::get('/{id}', function($id) {
                return response()->json(['message' => "Asset {$id} details endpoint"]);
            });
            Route::put('/{id}', function($id) {
                return response()->json(['message' => "Update asset {$id} endpoint"]);
            })->middleware('permission:assets.update');
            Route::delete('/{id}', function($id) {
                return response()->json(['message' => "Delete asset {$id} endpoint"]);
            })->middleware('permission:assets.delete');
            Route::get('/{id}/qrcode', function($id) {
                return response()->json(['message' => "Asset {$id} QR code endpoint"]);
            });
        });
        
        // Incident Module API (ถ้ามี)
        Route::prefix('incidents')->middleware('permission:incidents.view')->group(function () {
            Route::get('/', function() {
                return response()->json(['message' => 'Incident list endpoint']);
            });
            Route::post('/', function() {
                return response()->json(['message' => 'Create incident endpoint']);
            })->middleware('permission:incidents.create');
            Route::get('/{id}', function($id) {
                return response()->json(['message' => "Incident {$id} details endpoint"]);
            });
            Route::put('/{id}', function($id) {
                return response()->json(['message' => "Update incident {$id} endpoint"]);
            })->middleware('permission:incidents.update');
            Route::delete('/{id}', function($id) {
                return response()->json(['message' => "Delete incident {$id} endpoint"]);
            })->middleware('permission:incidents.delete');
        });
        
        // Service Request Module API (ถ้ามี)
        Route::prefix('service-requests')->middleware('permission:service_requests.view')->group(function () {
            Route::get('/', function() {
                return response()->json(['message' => 'Service request list endpoint']);
            });
            Route::post('/', function() {
                return response()->json(['message' => 'Create service request endpoint']);
            })->middleware('permission:service_requests.create');
            Route::get('/{id}', function($id) {
                return response()->json(['message' => "Service request {$id} details endpoint"]);
            });
            Route::put('/{id}', function($id) {
                return response()->json(['message' => "Update service request {$id} endpoint"]);
            })->middleware('permission:service_requests.update');
            Route::delete('/{id}', function($id) {
                return response()->json(['message' => "Delete service request {$id} endpoint"]);
            })->middleware('permission:service_requests.delete');
        });
        
        // Agreement Module API (ถ้ามี)
        Route::prefix('agreements')->middleware('permission:agreements.view')->group(function () {
            Route::get('/', function() {
                return response()->json(['message' => 'Agreement list endpoint']);
            });
            Route::post('/', function() {
                return response()->json(['message' => 'Create agreement endpoint']);
            })->middleware('permission:agreements.create');
            Route::get('/{id}', function($id) {
                return response()->json(['message' => "Agreement {$id} details endpoint"]);
            });
            Route::put('/{id}', function($id) {
                return response()->json(['message' => "Update agreement {$id} endpoint"]);
            })->middleware('permission:agreements.update');
            Route::delete('/{id}', function($id) {
                return response()->json(['message' => "Delete agreement {$id} endpoint"]);
            })->middleware('permission:agreements.delete');
        });
        
        // Admin only routes
        Route::middleware('admin')->prefix('admin')->group(function () {
            Route::get('/users', function () {
                return response()->json(['message' => 'Admin users endpoint']);
            });
            
            Route::get('/settings', function () {
                return response()->json(['message' => 'Admin settings endpoint']);
            });
            
            Route::get('/reports', function () {
                return response()->json(['message' => 'Admin reports endpoint']);
            });
        });
        
        // Super Admin only routes
        Route::middleware('role:super_admin')->prefix('super-admin')->group(function () {
            Route::get('/system-logs', function () {
                return response()->json(['message' => 'System logs endpoint']);
            });
            
            Route::get('/backup', function () {
                return response()->json(['message' => 'System backup endpoint']);
            });
        });
    });
});
