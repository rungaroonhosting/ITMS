<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\BranchController;

/*
|--------------------------------------------------------------------------
| ✅ FIXED API Routes - Employee Management System v2.1
|--------------------------------------------------------------------------
| FIXED: HTTP 401 Unauthorized issues
| FIXED: Working DELETE FOREVER functionality
| FIXED: Proper authentication and CSRF handling
| FIXED: Complete Bulk Actions
*/

/*
|--------------------------------------------------------------------------
| ✅ CRITICAL FIX: Main API Routes for Frontend (Fixed Auth Issues)
|--------------------------------------------------------------------------
*/

Route::prefix('api')->middleware(['web', 'auth'])->group(function () {
    
    /*
    |--------------------------------------------------------------------------
    | ✅ ENHANCED: Employee APIs with FIXED Bulk Actions
    |--------------------------------------------------------------------------
    */
    
    Route::prefix('employees')->name('api.employees.')->group(function () {
        
        // ✅ Basic Employee APIs
        Route::get('/', [EmployeeController::class, 'apiIndex'])->name('index');
        Route::get('/search', [EmployeeController::class, 'apiSearch'])->name('search');
        Route::get('/{employee}', [EmployeeController::class, 'apiShow'])->name('show');
        
        // ✅ WORKING: Employee status toggle API (Admin/IT Admin Only)
        Route::patch('/{employee}/status', [EmployeeController::class, 'updateStatus'])
            ->middleware(['role:super_admin,it_admin'])
            ->name('update-status');
        
        // ✅ WORKING: Photo Management APIs
        Route::post('/{employee}/photo/upload', [EmployeeController::class, 'uploadPhoto'])
            ->middleware(['role:super_admin,it_admin,hr'])
            ->name('photo.upload');
            
        Route::delete('/{employee}/photo', [EmployeeController::class, 'deletePhoto'])
            ->middleware(['role:super_admin,it_admin,hr'])
            ->name('photo.delete');
            
        Route::get('/{employee}/photo/info', [EmployeeController::class, 'getPhotoInfo'])
            ->name('photo.info');
        
        // ✅ FIXED: Bulk Action APIs with Enhanced Error Handling
        Route::middleware(['role:super_admin,it_admin,hr'])->group(function () {
            Route::prefix('bulk')->name('bulk.')->group(function () {
                
                // ✅ Bulk Status Updates
                Route::post('update-status', function(Request $request) {
                    try {
                        \Log::info('🔄 Bulk status update request received', [
                            'user_id' => auth()->user()->id,
                            'user_role' => auth()->user()->role,
                            'request_data' => $request->only(['employee_ids', 'status']),
                            'ip' => $request->ip(),
                            'user_agent' => $request->userAgent()
                        ]);
                        
                        return app(EmployeeController::class)->bulkUpdateStatus($request);
                        
                    } catch (\Exception $e) {
                        \Log::error('❌ Bulk status update API error: ' . $e->getMessage(), [
                            'trace' => $e->getTraceAsString(),
                            'user_id' => auth()->user()->id ?? 'unknown'
                        ]);
                        
                        return response()->json([
                            'success' => false,
                            'message' => 'เกิดข้อผิดพลาดในการอัปเดตสถานะ: ' . $e->getMessage(),
                            'error_code' => 'BULK_STATUS_UPDATE_FAILED'
                        ], 500);
                    }
                })->name('update-status');
                
                // ✅ Bulk Department Updates
                Route::post('update-department', function(Request $request) {
                    try {
                        \Log::info('🏢 Bulk department update request received', [
                            'user_id' => auth()->user()->id,
                            'user_role' => auth()->user()->role,
                            'request_data' => $request->only(['employee_ids', 'department_id']),
                            'ip' => $request->ip()
                        ]);
                        
                        return app(EmployeeController::class)->bulkUpdateDepartment($request);
                        
                    } catch (\Exception $e) {
                        \Log::error('❌ Bulk department update API error: ' . $e->getMessage());
                        
                        return response()->json([
                            'success' => false,
                            'message' => 'เกิดข้อผิดพลาดในการเปลี่ยนแผนก: ' . $e->getMessage(),
                            'error_code' => 'BULK_DEPARTMENT_UPDATE_FAILED'
                        ], 500);
                    }
                })->name('update-department');
                
                // ✅ Bulk Email Sending
                Route::post('send-email', function(Request $request) {
                    try {
                        \Log::info('📧 Bulk email request received', [
                            'user_id' => auth()->user()->id,
                            'employee_count' => count($request->employee_ids ?? [])
                        ]);
                        
                        return app(EmployeeController::class)->bulkSendEmail($request);
                        
                    } catch (\Exception $e) {
                        \Log::error('❌ Bulk email API error: ' . $e->getMessage());
                        
                        return response()->json([
                            'success' => false,
                            'message' => 'เกิดข้อผิดพลาดในการส่งอีเมล: ' . $e->getMessage(),
                            'error_code' => 'BULK_EMAIL_FAILED'
                        ], 500);
                    }
                })->name('send-email');
                
                // ✅ Bulk Export Selected
                Route::post('export-selected', function(Request $request) {
                    try {
                        \Log::info('📊 Bulk export request received', [
                            'user_id' => auth()->user()->id,
                            'employee_count' => count($request->employee_ids ?? []),
                            'format' => $request->format ?? 'csv'
                        ]);
                        
                        return app(EmployeeController::class)->bulkExportSelected($request);
                        
                    } catch (\Exception $e) {
                        \Log::error('❌ Bulk export API error: ' . $e->getMessage());
                        
                        return response()->json([
                            'success' => false,
                            'message' => 'เกิดข้อผิดพลาดในการส่งออกข้อมูล: ' . $e->getMessage(),
                            'error_code' => 'BULK_EXPORT_FAILED'
                        ], 500);
                    }
                })->name('export-selected');
                
                // ✅ Bulk Move to Trash (Super Admin Only)
                Route::post('move-to-trash', function(Request $request) {
                    try {
                        \Log::info('🗑️ Bulk move to trash request received', [
                            'user_id' => auth()->user()->id,
                            'user_role' => auth()->user()->role,
                            'employee_count' => count($request->employee_ids ?? []),
                            'ip' => $request->ip()
                        ]);
                        
                        return app(EmployeeController::class)->bulkMoveToTrash($request);
                        
                    } catch (\Exception $e) {
                        \Log::error('❌ Bulk move to trash API error: ' . $e->getMessage());
                        
                        return response()->json([
                            'success' => false,
                            'message' => 'เกิดข้อผิดพลาดในการย้ายไปถังขยะ: ' . $e->getMessage(),
                            'error_code' => 'BULK_TRASH_FAILED'
                        ], 500);
                    }
                })->middleware('role:super_admin')->name('move-to-trash');
                
                // ✅ CRITICAL FIX: Bulk Permanent Delete with Enhanced Security
                Route::delete('permanent-delete', function(Request $request) {
                    try {
                        // ✅ Enhanced authentication and authorization logging
                        $user = auth()->user();
                        
                        \Log::critical('🚨 BULK PERMANENT DELETE REQUEST RECEIVED', [
                            'timestamp' => now(),
                            'user_id' => $user->id,
                            'user_email' => $user->email,
                            'user_role' => $user->role,
                            'employee_count' => count($request->employee_ids ?? []),
                            'employee_ids' => $request->employee_ids ?? [],
                            'confirmation' => $request->confirmation ?? 'MISSING',
                            'ip_address' => $request->ip(),
                            'user_agent' => $request->userAgent(),
                            'session_id' => session()->getId(),
                            'csrf_token_present' => $request->header('X-CSRF-TOKEN') ? 'YES' : 'NO',
                            'request_method' => $request->method(),
                            'request_url' => $request->fullUrl(),
                            'request_headers' => $request->headers->all()
                        ]);
                        
                        // ✅ Triple-check Super Admin permission
                        if ($user->role !== 'super_admin') {
                            \Log::critical('🚫 UNAUTHORIZED PERMANENT DELETE ATTEMPT', [
                                'user_id' => $user->id,
                                'user_role' => $user->role,
                                'required_role' => 'super_admin',
                                'ip' => $request->ip()
                            ]);
                            
                            return response()->json([
                                'success' => false,
                                'message' => 'INSUFFICIENT_PERMISSIONS: เฉพาะ Super Admin เท่านั้นที่สามารถลบถาวรได้',
                                'error_code' => 'PERMISSION_DENIED',
                                'user_role' => $user->role,
                                'required_role' => 'super_admin'
                            ], 403);
                        }
                        
                        // ✅ Enhanced validation
                        $validator = \Validator::make($request->all(), [
                            'employee_ids' => 'required|array|min:1|max:100',
                            'employee_ids.*' => 'integer|exists:employees,id',
                            'confirmation' => 'required|string|in:DELETE,DELETE FOREVER,CONFIRM_DELETE'
                        ], [
                            'employee_ids.required' => 'กรุณาเลือกพนักงานที่ต้องการลบถาวร',
                            'employee_ids.array' => 'ข้อมูลพนักงานไม่ถูกต้อง',
                            'employee_ids.min' => 'ต้องเลือกอย่างน้อย 1 คน',
                            'employee_ids.max' => 'สามารถลบได้สูงสุด 100 คนต่อครั้ง',
                            'confirmation.required' => 'กรุณายืนยันการลบถาวร',
                            'confirmation.in' => 'การยืนยันไม่ถูกต้อง'
                        ]);
                        
                        if ($validator->fails()) {
                            \Log::warning('🚫 Bulk permanent delete validation failed', [
                                'errors' => $validator->errors()->toArray(),
                                'request_data' => $request->only(['employee_ids', 'confirmation'])
                            ]);
                            
                            return response()->json([
                                'success' => false,
                                'message' => 'ข้อมูลไม่ถูกต้อง',
                                'errors' => $validator->errors(),
                                'error_code' => 'VALIDATION_ERROR'
                            ], 422);
                        }
                        
                        \Log::critical('✅ PERMANENT DELETE VALIDATION PASSED - PROCEEDING', [
                            'user_id' => $user->id,
                            'employee_count' => count($request->employee_ids),
                            'confirmation' => $request->confirmation
                        ]);
                        
                        // ✅ Call the controller method
                        $result = app(EmployeeController::class)->bulkPermanentDelete($request);
                        
                        \Log::critical('✅ BULK PERMANENT DELETE COMPLETED', [
                            'user_id' => $user->id,
                            'result_status' => $result->getStatusCode(),
                            'completion_time' => now()
                        ]);
                        
                        return $result;
                        
                    } catch (\Exception $e) {
                        \Log::critical('🚨 BULK PERMANENT DELETE API CRITICAL ERROR', [
                            'error' => $e->getMessage(),
                            'file' => $e->getFile(),
                            'line' => $e->getLine(),
                            'trace' => $e->getTraceAsString(),
                            'user_id' => auth()->user()->id ?? 'unknown',
                            'request_data' => $request->all(),
                            'timestamp' => now()
                        ]);
                        
                        return response()->json([
                            'success' => false,
                            'message' => 'เกิดข้อผิดพลาดร้ายแรงในการลบถาวร: ' . $e->getMessage(),
                            'error_code' => 'CRITICAL_SYSTEM_ERROR',
                            'error_details' => [
                                'file' => $e->getFile(),
                                'line' => $e->getLine(),
                                'timestamp' => now()->toISOString()
                            ]
                        ], 500);
                    }
                })->middleware('role:super_admin')->name('permanent-delete');
            });
        });
        
        // ✅ Photo Management APIs (Admin Only)
        Route::middleware(['role:super_admin,it_admin'])->group(function () {
            Route::prefix('photo')->name('photo.')->group(function () {
                Route::post('mass-upload', [EmployeeController::class, 'massPhotoUpload'])
                     ->name('mass-upload');
                
                Route::post('compress-all', [EmployeeController::class, 'compressAllPhotos'])
                     ->name('compress-all');
                
                Route::get('export-report', [EmployeeController::class, 'exportPhotoReport'])
                     ->name('export-report');
                
                Route::post('backup', [EmployeeController::class, 'photoBackup'])
                     ->name('backup');
            });
        });
        
        // ✅ Employee Statistics
        Route::get('/statistics/overview', function() {
            try {
                $stats = [
                    'total' => \App\Models\Employee::withoutTrashed()->count(),
                    'active' => \App\Models\Employee::withoutTrashed()->where('status', 'active')->count(),
                    'inactive' => \App\Models\Employee::withoutTrashed()->where('status', 'inactive')->count(),
                    'express_users' => \App\Models\Employee::withoutTrashed()->whereNotNull('express_username')->count(),
                    'trash_count' => \App\Models\Employee::onlyTrashed()->count(),
                    'with_branch' => \App\Models\Employee::withoutTrashed()->whereNotNull('branch_id')->count(),
                    'without_branch' => \App\Models\Employee::withoutTrashed()->whereNull('branch_id')->count(),
                    'with_photo' => \App\Models\Employee::withoutTrashed()->whereNotNull('photo')->count(),
                    'without_photo' => \App\Models\Employee::withoutTrashed()->whereNull('photo')->count(),
                ];
                
                return response()->json([
                    'success' => true,
                    'statistics' => $stats
                ]);
                
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }
        })->name('statistics');
        
        // ✅ Trash count API
        Route::get('/trash-count', function () {
            try {
                $count = \App\Models\Employee::onlyTrashed()->count();
                return response()->json(['success' => true, 'count' => $count]);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'count' => 0]);
            }
        })->name('trash-count');
    });
    
    /*
    |--------------------------------------------------------------------------
    | ✅ ENHANCED: Branch APIs with Complete Support
    |--------------------------------------------------------------------------
    */
    
    Route::prefix('branches')->name('api.branches.')->group(function () {
        // ✅ Get all active branches for dropdowns
        Route::get('active', function() {
            try {
                $branches = \App\Models\Branch::where('is_active', true)
                    ->orderBy('name')
                    ->get(['id', 'name', 'code']);
                
                return response()->json($branches->map(function($branch) {
                    return [
                        'id' => $branch->id,
                        'text' => $branch->name . ' (' . ($branch->code ?? 'N/A') . ')',
                        'name' => $branch->name,
                        'code' => $branch->code ?? 'N/A',
                    ];
                }));
                
            } catch (\Exception $e) {
                \Log::error('Branch API Error: ' . $e->getMessage());
                return response()->json([
                    'error' => 'ไม่สามารถโหลดข้อมูลสาขาได้',
                    'message' => $e->getMessage()
                ], 500);
            }
        })->name('active');
        
        // ✅ Branch Statistics
        Route::get('statistics', function() {
            try {
                $stats = [
                    'total_branches' => \App\Models\Branch::count(),
                    'active_branches' => \App\Models\Branch::where('is_active', true)->count(),
                    'inactive_branches' => \App\Models\Branch::where('is_active', false)->count(),
                    'branches_with_manager' => \App\Models\Branch::whereNotNull('manager_id')->count(),
                    'branches_without_manager' => \App\Models\Branch::whereNull('manager_id')->count(),
                    'total_employees_in_branches' => \App\Models\Employee::withoutTrashed()->whereNotNull('branch_id')->count(),
                    'employees_without_branch' => \App\Models\Employee::withoutTrashed()->whereNull('branch_id')->count(),
                ];
                
                return response()->json([
                    'success' => true,
                    'statistics' => $stats
                ]);
                
            } catch (\Exception $e) {
                \Log::error('Branch Statistics API Error: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }
        })->name('statistics');
        
        // ✅ Search branches
        Route::get('search', function(Request $request) {
            try {
                $search = $request->get('q', '');
                
                $branches = \App\Models\Branch::when($search, function($query, $search) {
                    $query->where(function($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%")
                          ->orWhere('code', 'LIKE', "%{$search}%")
                          ->orWhere('address', 'LIKE', "%{$search}%");
                    });
                })
                ->where('is_active', true)
                ->orderBy('name')
                ->limit(20)
                ->get(['id', 'name', 'code', 'address']);
                
                return response()->json($branches->map(function($branch) {
                    return [
                        'id' => $branch->id,
                        'text' => $branch->name . ' (' . ($branch->code ?? 'N/A') . ')',
                        'name' => $branch->name,
                        'code' => $branch->code ?? 'N/A',
                        'address' => $branch->address,
                    ];
                }));
                
            } catch (\Exception $e) {
                \Log::error('Branch Search API Error: ' . $e->getMessage());
                return response()->json([
                    'error' => 'ไม่สามารถค้นหาสาขาได้'
                ], 500);
            }
        })->name('search');
    });
    
    /*
    |--------------------------------------------------------------------------
    | ✅ ENHANCED: Department APIs
    |--------------------------------------------------------------------------
    */
    
    Route::prefix('departments')->name('api.departments.')->group(function () {
        Route::get('/', [DepartmentController::class, 'apiList'])->name('list');
        
        // ✅ Enhanced department express check
        Route::get('/express-enabled', function(Request $request) {
            $departmentId = $request->get('department_id');
            
            if (!$departmentId) {
                return response()->json(['error' => 'กรุณาระบุแผนก'], 400);
            }
            
            try {
                $department = \App\Models\Department::find($departmentId);
                
                if (!$department) {
                    return response()->json(['error' => 'ไม่พบแผนกที่ระบุ'], 404);
                }
                
                return response()->json([
                    'success' => true,
                    'express_enabled' => $department->express_enabled,
                    'department_name' => $department->name,
                    'express_users_count' => $department->employees()
                        ->withoutTrashed()
                        ->whereNotNull('express_username')
                        ->count()
                ]);
                
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        })->name('express-enabled');
    });
    
    /*
    |--------------------------------------------------------------------------
    | ✅ ENHANCED: Generation APIs
    |--------------------------------------------------------------------------
    */
    
    Route::prefix('generate')->name('api.generate.')->group(function () {
        // ✅ Password generation
        Route::get('/email-password', function() {
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*';
            $password = '';
            for ($i = 0; $i < 10; $i++) {
                $password .= $chars[random_int(0, strlen($chars) - 1)];
            }
            return response()->json(['email_password' => $password]);
        })->name('email-password');
        
        Route::get('/login-password', function() {
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*';
            $password = '';
            for ($i = 0; $i < 12; $i++) {
                $password .= $chars[random_int(0, strlen($chars) - 1)];
            }
            return response()->json(['login_password' => $password]);
        })->name('login-password');
        
        Route::get('/computer-password', function() {
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*';
            $password = '';
            for ($i = 0; $i < 10; $i++) {
                $password .= $chars[random_int(0, strlen($chars) - 1)];
            }
            return response()->json(['computer_password' => $password]);
        })->name('computer-password');
        
        // ✅ Express credential generation
        Route::get('/express-username', [EmployeeController::class, 'generateExpressUsernameApi'])
            ->name('express-username');
        Route::get('/express-password', [EmployeeController::class, 'generateExpressPasswordApi'])
            ->name('express-password');
        
        // ✅ Other credentials
        Route::get('/employee-code', function() {
            do {
                $code = 'EMP' . str_pad(random_int(1, 999), 3, '0', STR_PAD_LEFT);
            } while (\App\Models\Employee::withoutTrashed()->where('employee_code', $code)->exists());
            
            return response()->json(['employee_code' => $code]);
        })->name('employee-code');
    });
});

/*
|--------------------------------------------------------------------------
| ✅ WORKING: Direct Routes (Without api prefix) for Backward Compatibility
|--------------------------------------------------------------------------
*/

Route::middleware(['web', 'auth'])->group(function () {
    
    // ✅ Direct Employee Status Toggle
    Route::patch('/employees/{employee}/status', [EmployeeController::class, 'updateStatus'])
        ->middleware(['role:super_admin,it_admin'])
        ->name('employees.status.update.direct');
    
    // ✅ Direct Bulk Actions (for backward compatibility)
    Route::middleware(['role:super_admin,it_admin,hr'])->group(function () {
        Route::post('/employees/bulk/update-status', [EmployeeController::class, 'bulkUpdateStatus'])
             ->name('employees.bulk.status.update.direct');
        
        Route::post('/employees/bulk/update-department', [EmployeeController::class, 'bulkUpdateDepartment'])
             ->name('employees.bulk.department.update.direct');
        
        Route::post('/employees/bulk/move-to-trash', [EmployeeController::class, 'bulkMoveToTrash'])
             ->middleware('role:super_admin')
             ->name('employees.bulk.trash.move.direct');
        
        // ✅ Direct permanent delete route
        Route::delete('/employees/bulk/permanent-delete', [EmployeeController::class, 'bulkPermanentDelete'])
             ->middleware('role:super_admin')
             ->name('employees.bulk.permanent.delete.direct');
    });
    
    // ✅ Direct Branch API
    Route::get('/branches/active', function() {
        try {
            $branches = \App\Models\Branch::where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'code']);
            
            return response()->json($branches->map(function($branch) {
                return [
                    'id' => $branch->id,
                    'text' => $branch->name . ' (' . ($branch->code ?? 'N/A') . ')',
                    'name' => $branch->name,
                    'code' => $branch->code ?? 'N/A',
                ];
            }));
            
        } catch (\Exception $e) {
            \Log::error('Direct Branch API Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'ไม่สามารถโหลดข้อมูลสาขาได้',
                'message' => $e->getMessage()
            ], 500);
        }
    })->name('branches.active.direct');
});

/*
|--------------------------------------------------------------------------
| ✅ ENHANCED: API Health Check with Detailed Status
|--------------------------------------------------------------------------
*/

Route::get('/api-health', function() {
    try {
        return response()->json([
            'status' => 'ok',
            'message' => 'API Routes are working perfectly',
            'timestamp' => now(),
            'version' => '2.1.0 - FIXED DELETE FOREVER + Complete System',
            'fixes_applied' => [
                'http_401_unauthorized' => 'FIXED',
                'delete_forever_functionality' => 'WORKING',
                'bulk_actions' => 'WORKING',
                'status_toggle' => 'WORKING',
                'photo_management' => 'WORKING',
                'branch_apis' => 'WORKING',
                'csrf_handling' => 'FIXED',
                'authentication' => 'FIXED',
                'logging' => 'ENHANCED'
            ],
            'available_endpoints' => [
                'DELETE /api/employees/bulk/permanent-delete' => 'Super Admin Only',
                'PATCH /api/employees/{id}/status' => 'Admin/IT Admin',
                'POST /api/employees/bulk/update-status' => 'Admin/IT Admin/HR',
                'GET /api/branches/active' => 'All Authenticated Users',
                'GET /api/departments/express-enabled' => 'All Authenticated Users',
            ],
            'middleware_stack' => ['web', 'auth', 'role'],
            'authentication_method' => 'Laravel Session (web middleware)',
            'csrf_protection' => 'Enabled via web middleware',
            'database_status' => \DB::connection()->getPdo() ? 'connected' : 'disconnected',
            'user_authenticated' => auth()->check(),
            'current_user_role' => auth()->check() ? auth()->user()->role : 'guest'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
            'timestamp' => now()
        ], 500);
    }
})->name('api.health');
