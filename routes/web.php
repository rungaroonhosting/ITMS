<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect root to appropriate page
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

// Login routes
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');
    
    Route::post('/login', function (Illuminate\Http\Request $request) {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            return redirect()->intended(route('dashboard'))->with('success', 'เข้าสู่ระบบสำเร็จ');
        }

        return back()->withErrors([
            'email' => 'ข้อมูลการเข้าสู่ระบบไม่ถูกต้อง',
        ])->onlyInput('email');
    })->name('login.store');
});

// Logout routes
Route::post('/logout', function (Illuminate\Http\Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    
    return redirect()->route('login')->with('success', 'ออกจากระบบเรียบร้อยแล้ว');
})->name('logout');

/*
|--------------------------------------------------------------------------
| Protected Routes (ต้อง login ก่อน)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    
    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */
    
    Route::get('/dashboard', function () {
        try {
            $employees = \App\Models\Employee::with('department')->get();
            $departments = \App\Models\Department::all();
            
            return view('dashboard', compact('employees', 'departments'));
        } catch (\Exception $e) {
            // Fallback if database is not ready
            return view('dashboard', [
                'employees' => collect(),
                'departments' => collect()
            ]);
        }
    })->name('dashboard');
    
    /*
    |--------------------------------------------------------------------------
    | Employee Management Routes
    |--------------------------------------------------------------------------
    */
    
    // Special routes ต้องอยู่ก่อน resource routes
    Route::get('/employees/generate-data', [EmployeeController::class, 'generateData'])->name('employees.generate-data');
    Route::get('/employees/export-excel', [EmployeeController::class, 'exportExcel'])->name('employees.exportExcel');
    Route::post('/employees/bulk-action', [EmployeeController::class, 'bulkAction'])->name('employees.bulkAction');
    Route::get('/employees/search', [EmployeeController::class, 'search'])->name('employees.search');
    
    // Employee CRUD routes
    Route::resource('employees', EmployeeController::class);
    
    /*
    |--------------------------------------------------------------------------
    | Profile Management
    |--------------------------------------------------------------------------
    */
    
    Route::get('/profile', function () {
        return view('profile', ['user' => auth()->user()]);
    })->name('profile');
    
    Route::put('/profile', function (Illuminate\Http\Request $request) {
        $user = auth()->user();
        
        $validated = $request->validate([
            'first_name_th' => 'required|string|max:100',
            'last_name_th' => 'required|string|max:100',
            'first_name_en' => 'required|string|max:100',
            'last_name_en' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'nickname' => 'nullable|string|max:50',
        ]);
        
        $user->update($validated);
        
        return back()->with('success', 'อัปเดตข้อมูลส่วนตัวเรียบร้อยแล้ว');
    })->name('profile.update');
    
    Route::put('/profile/password', function (Illuminate\Http\Request $request) {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|confirmed|min:6',
        ]);
        
        $user = auth()->user();
        
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'รหัสผ่านปัจจุบันไม่ถูกต้อง']);
        }
        
        $user->update([
            'password' => Hash::make($request->password)
        ]);
        
        return back()->with('success', 'เปลี่ยนรหัสผ่านเรียบร้อยแล้ว');
    })->name('profile.password.update');
    
});

/*
|--------------------------------------------------------------------------
| API Routes สำหรับ AJAX
|--------------------------------------------------------------------------
*/

Route::prefix('api')->middleware('auth')->group(function () {
    
    // Employee API
    Route::get('/employees/search', [EmployeeController::class, 'apiSearch'])->name('api.employees.search');
    Route::get('/employees/{employee}', [EmployeeController::class, 'apiShow'])->name('api.employees.show');
    
    // Generate data API
    Route::get('/generate-data', [EmployeeController::class, 'generateData'])->name('api.generate-data');
    
    // Department API
    Route::get('/departments', function () {
        try {
            $departments = \App\Models\Department::select('id', 'name')->get();
            return response()->json(['success' => true, 'data' => $departments]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    })->name('api.departments');
    
});

/*
|--------------------------------------------------------------------------
| Test Routes (เฉพาะ Local Environment)
|--------------------------------------------------------------------------
*/

if (app()->environment('local')) {
    
    Route::get('/test-db', function () {
        try {
            $employees = \App\Models\Employee::count();
            $departments = \App\Models\Department::count();
            
            return response()->json([
                'success' => true,
                'message' => 'Database connection successful',
                'data' => [
                    'employees_count' => $employees,
                    'departments_count' => $departments,
                    'timestamp' => now(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Database connection failed: ' . $e->getMessage(),
            ], 500);
        }
    })->name('test.db');
    
    Route::get('/test-generate', function () {
        try {
            $controller = new EmployeeController();
            
            $tests = [
                'employee_code' => $controller->generateEmployeeCode(),
                'keycard_id' => $controller->generateKeycardId(),
                'password' => $controller->generatePassword(),
                'copier_code' => $controller->generateCopierCode(),
            ];
            
            return response()->json([
                'success' => true,
                'message' => 'Generate functions working',
                'data' => $tests
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Generate test failed: ' . $e->getMessage(),
            ], 500);
        }
    })->name('test.generate');
    
    Route::get('/test-auth', function () {
        try {
            $user = auth()->user();
            
            return response()->json([
                'success' => true,
                'message' => 'Authentication working',
                'data' => [
                    'authenticated' => Auth::check(),
                    'user_id' => $user ? $user->id : null,
                    'user_email' => $user ? $user->email : null,
                    'user_role' => $user ? $user->role : null,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Auth test failed: ' . $e->getMessage(),
            ], 500);
        }
    })->name('test.auth');
    
}

/*
|--------------------------------------------------------------------------
| Health Check
|--------------------------------------------------------------------------
*/

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now(),
        'version' => '1.0.0',
        'environment' => app()->environment(),
    ]);
})->name('health');

/*
|--------------------------------------------------------------------------
| Fallback Route (ต้องอยู่ท้ายสุด)
|--------------------------------------------------------------------------
*/

Route::fallback(function () {
    if (Auth::check()) {
        return redirect()->route('dashboard')->with('warning', 'หน้าที่คุณต้องการไม่พบ');
    }
    
    return redirect()->route('login')->with('error', 'หน้าที่คุณต้องการไม่พบ');
});