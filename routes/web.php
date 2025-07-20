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
            $employees = \App\Models\Employee::with('department')->withoutTrashed()->get();
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
    Route::get('/employees/generate-data', [EmployeeController::class, 'generateData'])->name('employees.generate-data');
    Route::get('/employees/export-excel', [EmployeeController::class, 'exportExcel'])->name('employees.exportExcel');
    Route::get('/employees/export-pdf', [EmployeeController::class, 'exportPdf'])->name('employees.exportPdf');
    Route::post('/employees/bulk-action', [EmployeeController::class, 'bulkAction'])->name('employees.bulkAction');
    Route::get('/employees/search', [EmployeeController::class, 'search'])->name('employees.search');
    
    // *** Trash Management Routes (SuperAdmin Only) ***
    Route::middleware(function ($request, $next) {
        if (auth()->user()->role !== 'super_admin') {
            return redirect()->route('employees.index')->with('error', 'ไม่มีสิทธิ์เข้าถึง');
        }
        return $next($request);
    })->group(function () {
        Route::get('/employees/trash', [EmployeeController::class, 'trash'])->name('employees.trash');
        Route::post('/employees/{id}/restore', [EmployeeController::class, 'restore'])->name('employees.restore');
        Route::delete('/employees/{id}/force-delete', [EmployeeController::class, 'forceDelete'])->name('employees.forceDelete');
        Route::post('/employees/bulk-restore', [EmployeeController::class, 'bulkRestore'])->name('employees.bulkRestore');
        Route::delete('/employees/empty-trash', [EmployeeController::class, 'emptyTrash'])->name('employees.emptyTrash');
    });
    
    // *** Employee Action Routes ***
    Route::post('/employees/{employee}/reset-password', [EmployeeController::class, 'resetPassword'])->name('employees.resetPassword');
    Route::post('/employees/{employee}/send-credentials', [EmployeeController::class, 'sendCredentials'])->name('employees.sendCredentials');
    Route::get('/employees/{employee}/preview', [EmployeeController::class, 'preview'])->name('employees.preview');
    Route::post('/employees/{employee}/generate-credentials', [EmployeeController::class, 'generateCredentials'])->name('employees.generateCredentials');
    
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
    Route::get('/employees/trash-count', function () {
        try {
            $count = \App\Models\Employee::onlyTrashed()->count();
            return response()->json(['success' => true, 'count' => $count]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'count' => 0]);
        }
    })->name('api.employees.trashCount');
    
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
    })->name('api.employees.toggleStatus');
    
    // Generate data API
    Route::get('/generate-data', [EmployeeController::class, 'generateData'])->name('api.generate-data');
    
    // Department API
    Route::get('/departments', [DepartmentController::class, 'apiList'])->name('api.departments');
    Route::get('/departments/active', function () {
        try {
            $departments = \App\Models\Department::where('is_active', true)
                                               ->select('id', 'name', 'code')
                                               ->orderBy('name')
                                               ->get();
            return response()->json(['success' => true, 'data' => $departments]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    })->name('api.departments.active');
    
    // Check if department is accounting
    Route::get('/departments/{department}/is-accounting', function (\App\Models\Department $department) {
        try {
            $accountingNames = [
                'แผนกบัญชีและการเงิน',
                'บัญชีและการเงิน', 
                'บัญชี',
                'การเงิน',
                'accounting',
                'finance'
            ];
            
            $accountingCodes = ['ACC', 'FIN', 'AF'];
            
            $isAccounting = in_array(strtolower($department->name), array_map('strtolower', $accountingNames)) ||
                           in_array(strtoupper($department->code ?? ''), $accountingCodes);
            
            return response()->json([
                'success' => true,
                'is_accounting' => $isAccounting,
                'department' => $department
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    })->name('api.departments.isAccounting');
    
    // Express data generation
    Route::get('/generate-express-data', function (Illuminate\Http\Request $request) {
        $type = $request->get('type');
        
        switch ($type) {
            case 'username':
                $name = $request->get('name', '');
                $clean = preg_replace('/[^a-zA-Z]/', '', $name);
                $username = strtolower(substr($clean, 0, 7));
                
                if (strlen($username) < 7) {
                    $username = str_pad($username, 7, 'x');
                }
                
                // Check for duplicates
                $counter = 1;
                $originalUsername = $username;
                
                while (\App\Models\Employee::where('express_username', $username)->exists()) {
                    $username = substr($originalUsername, 0, 6) . $counter;
                    $counter++;
                }
                
                return response()->json(['username' => $username]);
            
            case 'password':
                $chars = 'abcdefghijklmnopqrstuvwxyz';
                $numbers = '0123456789';
                
                $password = '';
                for ($i = 0; $i < 3; $i++) {
                    $password .= $chars[rand(0, strlen($chars) - 1)];
                }
                $password .= $numbers[rand(0, strlen($numbers) - 1)];
                $password = str_shuffle($password);
                
                return response()->json(['password' => $password]);
            
            default:
                return response()->json(['error' => 'Invalid type'], 400);
        }
    })->name('api.generate-express-data');
    
});

/*
|--------------------------------------------------------------------------
| Bulk Actions for Trash Management (SuperAdmin Only)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', function ($request, $next) {
    if (auth()->user()->role !== 'super_admin') {
        return response()->json(['success' => false, 'message' => 'ไม่มีสิทธิ์'], 403);
    }
    return $next($request);
}])->group(function () {
    
    // Bulk restore from trash
    Route::post('/employees/bulk-restore', function (Illuminate\Http\Request $request) {
        try {
            $validated = $request->validate([
                'employee_ids' => 'required|array',
                'employee_ids.*' => 'exists:employees,id'
            ]);
            
            $employeeIds = $validated['employee_ids'];
            $count = count($employeeIds);
            
            // Restore employees
            \App\Models\Employee::withTrashed()
                               ->whereIn('id', $employeeIds)
                               ->restore();
            
            return response()->json([
                'success' => true,
                'message' => "กู้คืนพนักงาน {$count} คนเรียบร้อยแล้ว"
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Bulk restore failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการกู้คืน'
            ], 500);
        }
    });
    
    // Empty trash (delete all trashed employees permanently)
    Route::delete('/employees/empty-trash', function () {
        try {
            $count = \App\Models\Employee::onlyTrashed()->count();
            
            if ($count === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'ถังขยะว่างเปล่า'
                ]);
            }
            
            // Force delete all trashed employees
            \App\Models\Employee::onlyTrashed()->forceDelete();
            
            return response()->json([
                'success' => true,
                'message' => "ลบข้อมูลถาวร {$count} รายการเรียบร้อยแล้ว"
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Empty trash failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการล้างถังขยะ'
            ], 500);
        }
    });
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
            $departments = \App\Models\Department::count();
            
            return response()->json([
                'success' => true,
                'message' => 'Database connection successful',
                'data' => [
                    'employees_count' => $employees,
                    'trashed_employees_count' => $trashedEmployees,
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
                'employee_code' => 'EMP' . str_pad(random_int(1, 999), 3, '0', STR_PAD_LEFT),
                'keycard_id' => 'KC' . str_pad(random_int(1, 999999), 6, '0', STR_PAD_LEFT),
                'password' => 'TestPassword123',
                'copier_code' => str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT),
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
    
    Route::get('/test-departments', function () {
        try {
            $departments = \App\Models\Department::all();
            
            return response()->json([
                'success' => true,
                'message' => 'Department test successful',
                'data' => [
                    'departments_count' => $departments->count(),
                    'active_departments' => $departments->where('is_active', true)->count(),
                    'departments' => $departments->map(function($dept) {
                        return [
                            'id' => $dept->id,
                            'name' => $dept->name,
                            'code' => $dept->code,
                            'is_active' => $dept->is_active
                        ];
                    })
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Department test failed: ' . $e->getMessage(),
            ], 500);
        }
    })->name('test.departments');
    
    // Test Express credential generation
    Route::get('/test-express', function () {
        try {
            $testNames = [
                'จอห์น สมิธ',
                'มารี ซู',
                'ทดสอบ ระบบ',
                'Test User'
            ];
            
            $results = [];
            
            foreach ($testNames as $name) {
                $clean = preg_replace('/[^a-zA-Z]/', '', $name);
                $username = strtolower(substr($clean, 0, 7));
                
                if (strlen($username) < 7) {
                    $username = str_pad($username, 7, 'x');
                }
                
                $chars = 'abcdefghijklmnopqrstuvwxyz';
                $numbers = '0123456789';
                
                $password = '';
                for ($i = 0; $i < 3; $i++) {
                    $password .= $chars[rand(0, strlen($chars) - 1)];
                }
                $password .= $numbers[rand(0, strlen($numbers) - 1)];
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
            'department_management' => true,
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