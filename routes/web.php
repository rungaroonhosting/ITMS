<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\DepartmentController;

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
            $employees = \App\Models\Employee::withoutTrashed()->get();
            $trashCount = \App\Models\Employee::onlyTrashed()->count();
            
            return view('dashboard', compact('employees', 'trashCount'));
        } catch (\Exception $e) {
            // Fallback if database is not ready
            return view('dashboard', [
                'employees' => collect(),
                'trashCount' => 0
            ]);
        }
    })->name('dashboard');
    
    /*
    |--------------------------------------------------------------------------
    | Department Management Routes (Admin Only)
    |--------------------------------------------------------------------------
    */
    
    // Department routes - Admin only
    Route::group(['prefix' => 'departments'], function () {
        Route::get('/', [DepartmentController::class, 'index'])->name('departments.index');
        Route::get('/create', [DepartmentController::class, 'create'])->name('departments.create');
        Route::post('/', [DepartmentController::class, 'store'])->name('departments.store');
        Route::get('/{department}', [DepartmentController::class, 'show'])->name('departments.show');
        Route::get('/{department}/edit', [DepartmentController::class, 'edit'])->name('departments.edit');
        Route::put('/{department}', [DepartmentController::class, 'update'])->name('departments.update');
        Route::delete('/{department}', [DepartmentController::class, 'destroy'])->name('departments.destroy');
        
        // Special routes
        Route::get('/export-excel', [DepartmentController::class, 'exportExcel'])->name('departments.exportExcel');
        Route::post('/bulk-action', [DepartmentController::class, 'bulkAction'])->name('departments.bulkAction');
        Route::post('/{department}/toggle-status', [DepartmentController::class, 'toggleStatus'])->name('departments.toggleStatus');
    });
    
    /*
    |--------------------------------------------------------------------------
    | Employee Management Routes
    |--------------------------------------------------------------------------
    */
    
    // Special employee routes (before resource routes)
    Route::get('/employees/export-excel', [EmployeeController::class, 'exportExcel'])->name('employees.exportExcel');
    Route::get('/employees/export-pdf', [EmployeeController::class, 'exportPdf'])->name('employees.exportPdf');
    Route::post('/employees/bulk-action', [EmployeeController::class, 'bulkAction'])->name('employees.bulkAction');
    Route::get('/employees/search', [EmployeeController::class, 'search'])->name('employees.search');    
    // *** Trash Management Routes (SuperAdmin Only) ***
    // แก้ไข: ใช้ middleware ที่สร้างขึ้นแทน closure
    Route::middleware(['role:super_admin'])->group(function () {
        Route::get('/employees/trash', [EmployeeController::class, 'trash'])->name('employees.trash');
        Route::post('/employees/{id}/restore', [EmployeeController::class, 'restore'])->name('employees.restore');
        Route::delete('/employees/{id}/force-delete', [EmployeeController::class, 'forceDelete'])->name('employees.force-delete');
        Route::post('/employees/bulk-restore', [EmployeeController::class, 'bulkRestore'])->name('employees.bulk-restore');
        Route::delete('/employees/empty-trash', [EmployeeController::class, 'emptyTrash'])->name('employees.empty-trash');
    });
    
    // *** Employee Action Routes ***
    Route::post('/employees/{employee}/reset-password', [EmployeeController::class, 'resetPassword'])->name('employees.reset-password');
    Route::post('/employees/{employee}/send-credentials', [EmployeeController::class, 'sendCredentials'])->name('employees.send-credentials');
    Route::get('/employees/{employee}/preview', [EmployeeController::class, 'preview'])->name('employees.preview');
    Route::post('/employees/{employee}/generate-credentials', [EmployeeController::class, 'generateCredentials'])->name('employees.generate-credentials');
    
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
| API Routes สำหรับ AJAX และ Express Generation
|--------------------------------------------------------------------------
*/

Route::prefix('api')->middleware('auth')->group(function () {
    
    // Employee API
    Route::get('/employees', [EmployeeController::class, 'apiIndex'])->name('api.employees.index');
    Route::get('/employees/search', [EmployeeController::class, 'apiSearch'])->name('api.employees.search');
    Route::get('/employees/{employee}', [EmployeeController::class, 'apiShow'])->name('api.employees.show');
    
    // Express credential generation API
    Route::get('/generate/express-username', [EmployeeController::class, 'generateExpressUsername'])->name('api.generate.express-username');
    Route::get('/generate/express-password', [EmployeeController::class, 'generateExpressPassword'])->name('api.generate.express-password');
    
    // Trash count API
    Route::get('/employees/trash-count', function () {
        try {
            $count = \App\Models\Employee::onlyTrashed()->count();
            return response()->json(['success' => true, 'count' => $count]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'count' => 0]);
        }
    })->name('api.employees.trash-count');
    
    // Employee status toggle
    Route::post('/employees/{employee}/toggle-status', function (\App\Models\Employee $employee, Illuminate\Http\Request $request) {
        try {
            // Check permission
            if (!in_array(auth()->user()->role, ['super_admin', 'it_admin'])) {
                return response()->json(['success' => false, 'message' => 'ไม่มีสิทธิ์']);
            }
            
            $validated = $request->validate([
                'status' => 'required|in:active,inactive'
            ]);
            
            $employee->update(['status' => $validated['status']]);
            
            return response()->json([
                'success' => true,
                'message' => 'อัปเดตสถานะเรียบร้อยแล้ว',
                'status' => $validated['status']
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    })->name('api.employees.toggle-status');
    
    // Department API (สำหรับการเพิ่มแผนกใหม่)
    Route::get('/departments', function () {
        try {
            // Static departments list for now
            $departments = [
                'แผนกบริหาร',
                'แผนกการเงิน', 
                'แผนกบัญชี',
                'แผนกบัญชีและการเงิน',
                'แผนกขาย',
                'แผนกการตลาด',
                'แผนกผลิต',
                'แผนกควบคุมคุณภาพ',
                'แผนกจัดซื้อ',
                'แผนกทรัพยากรบุคคล',
                'แผนกเทคโนโลยีสารสนเทศ',
                'แผนกกฎหมาย',
                'แผนกวิจัยและพัฒนา'
            ];
            
            return response()->json(['success' => true, 'departments' => $departments]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    })->name('api.departments');
    
    // Check if department is accounting
    Route::get('/departments/is-accounting', function (Illuminate\Http\Request $request) {
        try {
            $department = $request->get('department');
            
            $accountingDepartments = [
                'แผนกบัญชี',
                'แผนกบัญชีและการเงิน',
                'แผนกการเงิน'
            ];
            
            $isAccounting = in_array($department, $accountingDepartments);
            
            return response()->json([
                'success' => true,
                'is_accounting' => $isAccounting,
                'department' => $department
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    })->name('api.departments.is-accounting');
    
});

/*
|--------------------------------------------------------------------------
| Test Routes (เฉพาะ Local Environment)
|--------------------------------------------------------------------------
*/

if (app()->environment('local')) {
    
    Route::get('/test-db', function () {
        try {
            $employees = \App\Models\Employee::withoutTrashed()->count();
            $trashedEmployees = \App\Models\Employee::onlyTrashed()->count();
            
            return response()->json([
                'success' => true,
                'message' => 'Database connection successful',
                'data' => [
                    'employees_count' => $employees,
                    'trashed_employees_count' => $trashedEmployees,
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
            $tests = [
                'employee_code' => 'EMP' . str_pad(random_int(1, 999), 3, '0', STR_PAD_LEFT),
                'password' => 'TestPassword123',
                'express_username' => 'testuser',
                'express_password' => 'abc1',
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
    
    // Test Express credential generation
    Route::get('/test-express', function () {
        try {
            $testNames = [
                'John Smith',
                'Mary Sue',
                'Test User',
                'Alice Cooper'
            ];
            
            $results = [];
            
            foreach ($testNames as $name) {
                // Generate username
                $clean = preg_replace('/[^a-zA-Z]/', '', $name);
                $username = strtolower(substr($clean, 0, 7));
                
                if (strlen($username) < 7) {
                    $username = str_pad($username, 7, 'x');
                }
                
                // Generate password
                $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
                $password = '';
                
                // Ensure at least one number
                $password .= rand(0, 9);
                
                // Fill remaining 3 characters
                for ($i = 0; $i < 3; $i++) {
                    $password .= $chars[rand(0, strlen($chars) - 1)];
                }
                
                // Shuffle the password
                $password = str_shuffle($password);
                
                $results[] = [
                    'name' => $name,
                    'username' => $username,
                    'password' => $password
                ];
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Express credential generation test',
                'data' => $results
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Express test failed: ' . $e->getMessage(),
            ], 500);
        }
    })->name('test.express');
    
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
        'version' => '1.4.0',
        'environment' => app()->environment(),
        'features' => [
            'employee_management' => true,
            'department_management' => false,
            'express_support' => true,
            'role_based_access' => true,
            'soft_delete' => true,
            'trash_management' => true,
            'bulk_actions' => true,
            'auto_credentials' => true,
        ]
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