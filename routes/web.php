<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes - Employee Management System v2.1 (Web Interface Only)
|--------------------------------------------------------------------------
| NOTE: API routes are now handled in routes/api.php
| This file contains only web interface routes
| 🔧 FIXED: Route order to prevent {employee} parameter conflicts
*/

// ✅ Dashboard Routes (แก้ไขให้ใช้ DashboardController)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/stats', [DashboardController::class, 'getStats'])->name('dashboard.stats');
    Route::get('/dashboard/role-data', [DashboardController::class, 'getRoleData'])->name('dashboard.role-data');
    Route::get('/dashboard/notifications', [DashboardController::class, 'getNotifications'])->name('dashboard.notifications');
    
    // Redirect root to dashboard
    Route::get('/', function () {
        return redirect()->route('dashboard');
    })->name('home');
});

// ✅ CRITICAL FIX: Employee Routes with Proper Order (Specific routes BEFORE parameter routes)
Route::prefix('employees')->middleware(['auth'])->group(function () {
    
    // ✅ List & Search (First - most common route)
    Route::get('/', [EmployeeController::class, 'index'])
         ->middleware('role:super_admin,it_admin,hr,manager,express')
         ->name('employees.index');
    
    // 🔧 CRITICAL FIX: Trash Management Routes FIRST (before {employee} parameter)
    Route::prefix('trash')->middleware('role:super_admin')->group(function () {
        Route::get('/', [EmployeeController::class, 'trash'])
             ->name('employees.trash');
             
        Route::patch('/{id}/restore', [EmployeeController::class, 'restore'])
             ->name('employees.restore');
             
        Route::delete('/{id}/force', [EmployeeController::class, 'forceDelete'])
             ->name('employees.force-delete');
             
        Route::delete('/empty', [EmployeeController::class, 'emptyTrash'])
             ->name('employees.empty-trash');
    });
    
    // ✅ Create Routes (before {employee})
    Route::get('/create', [EmployeeController::class, 'create'])
         ->middleware('role:super_admin,it_admin,hr')
         ->name('employees.create');
         
    Route::post('/', [EmployeeController::class, 'store'])
         ->middleware('role:super_admin,it_admin,hr')
         ->name('employees.store');
    
    // ✅ Export Routes (before {employee} parameter)
    Route::get('/export/excel', [EmployeeController::class, 'exportExcel'])
         ->middleware('role:super_admin,it_admin,hr')
         ->name('employees.export.excel');
         
    Route::get('/export/pdf', [EmployeeController::class, 'exportPdf'])
         ->middleware('role:super_admin,it_admin,hr')
         ->name('employees.export.pdf');
         
    Route::get('/export/csv', [EmployeeController::class, 'exportCsv'])
         ->middleware('role:super_admin,it_admin,hr')
         ->name('employees.export.csv');
    
    // ✅ Bulk Operations (before {employee} parameter)
    Route::post('/bulk/delete', [EmployeeController::class, 'bulkDelete'])
         ->middleware('role:super_admin')
         ->name('employees.bulk.delete');
         
    Route::post('/bulk/restore', [EmployeeController::class, 'bulkRestore'])
         ->middleware('role:super_admin')
         ->name('employees.bulk.restore');
         
    Route::post('/bulk/permanent-delete', [EmployeeController::class, 'bulkPermanentDelete'])
         ->middleware('role:super_admin')
         ->name('employees.bulk.permanent-delete');
    
    // ✅ Additional Bulk Operations
    Route::post('/bulk/update-status', [EmployeeController::class, 'bulkUpdateStatus'])
         ->middleware('role:super_admin,it_admin,hr')
         ->name('employees.bulk.update-status');
         
    Route::post('/bulk/update-department', [EmployeeController::class, 'bulkUpdateDepartment'])
         ->middleware('role:super_admin,it_admin,hr')
         ->name('employees.bulk.update-department');
         
    Route::post('/bulk/send-email', [EmployeeController::class, 'bulkSendEmail'])
         ->middleware('role:super_admin,it_admin,hr')
         ->name('employees.bulk.send-email');
         
    Route::post('/bulk/export-selected', [EmployeeController::class, 'bulkExportSelected'])
         ->middleware('role:super_admin,it_admin,hr')
         ->name('employees.bulk.export-selected');
         
    Route::post('/bulk/move-to-trash', [EmployeeController::class, 'bulkMoveToTrash'])
         ->middleware('role:super_admin')
         ->name('employees.bulk.move-to-trash');
    
    // ✅ Photo Management Routes (before {employee} parameter)
    Route::prefix('photos')->middleware('role:super_admin,it_admin')->group(function () {
        Route::post('/mass-upload', [EmployeeController::class, 'massPhotoUpload'])
             ->name('employees.photos.mass-upload');
             
        Route::post('/compress-all', [EmployeeController::class, 'compressAllPhotos'])
             ->name('employees.photos.compress-all');
             
        Route::get('/export-report', [EmployeeController::class, 'exportPhotoReport'])
             ->name('employees.photos.export-report');
             
        Route::post('/backup', [EmployeeController::class, 'photoBackup'])
             ->name('employees.photos.backup');
    });
    
    // 🔧 IMPORTANT: Individual Employee Routes LAST (with {employee} parameter)
    // These must come after all specific routes to prevent parameter matching conflicts
    
    // ✅ Show Individual Employee
    Route::get('/{employee}', [EmployeeController::class, 'show'])
         ->name('employees.show');
    
    // ✅ Edit Individual Employee
    Route::get('/{employee}/edit', [EmployeeController::class, 'edit'])
         ->middleware('role:super_admin,it_admin,hr')
         ->name('employees.edit');
         
    Route::put('/{employee}', [EmployeeController::class, 'update'])
         ->middleware('role:super_admin,it_admin,hr')
         ->name('employees.update');
    
    // ✅ Individual Employee Status Update
    Route::patch('/{employee}/status', [EmployeeController::class, 'updateStatus'])
         ->middleware('role:super_admin,it_admin')
         ->name('employees.update-status');
    
    // ✅ Individual Employee Photo Management
    Route::post('/{employee}/photo', [EmployeeController::class, 'uploadPhoto'])
         ->middleware('role:super_admin,it_admin,hr')
         ->name('employees.upload-photo');
         
    Route::delete('/{employee}/photo', [EmployeeController::class, 'deletePhoto'])
         ->middleware('role:super_admin,it_admin,hr')
         ->name('employees.delete-photo');
         
    Route::get('/{employee}/photo-info', [EmployeeController::class, 'getPhotoInfo'])
         ->name('employees.photo-info');
    
    // ✅ Delete Individual Employee (Soft Delete)
    Route::delete('/{employee}', [EmployeeController::class, 'destroy'])
         ->middleware('role:super_admin')
         ->name('employees.destroy');
});

// ✅ Department Routes (Web Interface)
Route::prefix('departments')->middleware(['auth', 'role:super_admin,it_admin'])->group(function () {
    Route::get('/', [DepartmentController::class, 'index'])->name('departments.index');
    Route::get('/create', [DepartmentController::class, 'create'])->name('departments.create');
    Route::post('/', [DepartmentController::class, 'store'])->name('departments.store');
    Route::get('/{department}', [DepartmentController::class, 'show'])->name('departments.show');
    Route::get('/{department}/edit', [DepartmentController::class, 'edit'])->name('departments.edit');
    Route::put('/{department}', [DepartmentController::class, 'update'])->name('departments.update');
    Route::delete('/{department}', [DepartmentController::class, 'destroy'])->name('departments.destroy');
    
    // Department Trash
    Route::get('/trash', [DepartmentController::class, 'trash'])->name('departments.trash');
    Route::patch('/{id}/restore', [DepartmentController::class, 'restore'])->name('departments.restore');
    Route::delete('/{id}/force', [DepartmentController::class, 'forceDelete'])->name('departments.force-delete');
    
    // Department Export
    Route::get('/export/excel', [DepartmentController::class, 'exportExcel'])->name('departments.export.excel');
    Route::get('/export/pdf', [DepartmentController::class, 'exportPdf'])->name('departments.export.pdf');
});

// ✅ Branch Routes (Web Interface) - แก้ไขเพิ่ม toggle-status route ที่หายไป
Route::prefix('branches')->middleware(['auth', 'role:super_admin,it_admin,hr'])->group(function () {
    // ✅ List and basic operations
    Route::get('/', [BranchController::class, 'index'])->name('branches.index');
    Route::get('/create', [BranchController::class, 'create'])->name('branches.create');
    Route::post('/', [BranchController::class, 'store'])->name('branches.store');
    
    // ✅ Export Routes (before {branch} parameter)
    Route::get('/export/excel', [BranchController::class, 'exportExcel'])->name('branches.export.excel');
    Route::get('/export/pdf', [BranchController::class, 'exportPdf'])->name('branches.export.pdf');
    Route::get('/export/csv', [BranchController::class, 'exportCsv'])->name('branches.export.csv');
    
    // ✅ FIXED: แก้ไข export route สำหรับ dashboard quick action
    Route::get('/export', [BranchController::class, 'exportExcel'])->name('branches.export');
    
    // ✅ Data and AJAX routes (before {branch} parameter)
    Route::get('/data', [BranchController::class, 'getData'])->name('branches.data');
    
    // ✅ Trash Management (before {branch} parameter)
    Route::prefix('trash')->group(function () {
        Route::get('/', [BranchController::class, 'trash'])->name('branches.trash');
        Route::patch('/{id}/restore', [BranchController::class, 'restore'])->name('branches.restore');
        Route::delete('/{id}/force', [BranchController::class, 'forceDelete'])->name('branches.force-delete');
    });
    
    // ✅ Individual Branch Routes (with {branch} parameter - LAST)
    Route::get('/{branch}', [BranchController::class, 'show'])->name('branches.show');
    Route::get('/{branch}/edit', [BranchController::class, 'edit'])->name('branches.edit');
    Route::put('/{branch}', [BranchController::class, 'update'])->name('branches.update');
    Route::delete('/{branch}', [BranchController::class, 'destroy'])->name('branches.destroy');
    
    // ✅ FIXED: เพิ่ม toggle-status route ที่หายไป
    Route::patch('/{branch}/toggle-status', [BranchController::class, 'toggleStatus'])
         ->middleware('role:super_admin,it_admin')
         ->name('branches.toggle-status');
});

// ✅ Authentication Routes
require __DIR__.'/auth.php';

// ✅ Health Check Route (Web Only)
Route::get('/health', function () {
    try {
        // ตรวจสอบการเชื่อมต่อฐานข้อมูล
        \DB::connection()->getPdo();
        $dbStatus = 'connected';
    } catch (\Exception $e) {
        $dbStatus = 'disconnected';
    }
    
    return response()->json([
        'status' => 'ok',
        'timestamp' => now(),
        'laravel_version' => app()->version(),
        'php_version' => PHP_VERSION,
        'environment' => app()->environment(),
        'routes_cached' => app()->routesAreCached(),
        'config_cached' => app()->configurationIsCached(),
        'database_status' => $dbStatus,
        'memory_usage' => round(memory_get_usage(true) / 1024 / 1024, 2) . ' MB',
        'note' => 'ITMS Employee Management System v2.1 - Web routes only (FIXED Route Order)',
        'routes_fixed' => 'Route order optimized to prevent parameter conflicts'
    ]);
})->name('health.check');

// ✅ Development Routes (เฉพาะ local environment)
if (app()->environment('local', 'development')) {
    Route::prefix('dev')->middleware(['auth', 'role:super_admin'])->group(function () {
        Route::get('/clear-cache', function () {
            \Artisan::call('cache:clear');
            \Artisan::call('config:clear');
            \Artisan::call('route:clear');
            \Artisan::call('view:clear');
            return response()->json([
                'success' => true,
                'message' => 'Cache cleared successfully!',
                'cleared' => ['cache', 'config', 'routes', 'views'],
                'timestamp' => now()
            ]);
        })->name('dev.clear-cache');
        
        Route::get('/routes', function () {
            $routes = \Route::getRoutes();
            $routeList = [];
            foreach ($routes as $route) {
                $routeList[] = [
                    'method' => implode('|', $route->methods()),
                    'uri' => $route->uri(),
                    'name' => $route->getName(),
                    'action' => $route->getActionName(),
                    'middleware' => $route->middleware()
                ];
            }
            return response()->json([
                'total_routes' => count($routeList),
                'routes' => $routeList,
                'employee_routes' => array_filter($routeList, function($route) {
                    return strpos($route['uri'], 'employees') !== false;
                })
            ]);
        })->name('dev.routes');
        
        Route::get('/test-employee-routes', function () {
            $employeeRoutes = [
                'employees.index' => route('employees.index'),
                'employees.trash' => route('employees.trash'),
                'employees.create' => route('employees.create'),
                'employees.export.excel' => route('employees.export.excel'),
            ];
            
            return response()->json([
                'status' => 'testing employee routes',
                'routes' => $employeeRoutes,
                'user_role' => auth()->user()->role,
                'trash_route_exists' => \Route::has('employees.trash'),
                'timestamp' => now()
            ]);
        })->name('dev.test-routes');
    });
}

// ✅ Fallback for 404
Route::fallback(function () {
    return redirect()->route('dashboard')->with('error', 'หน้าที่คุณต้องการไม่พบในระบบ');
});

/*
|--------------------------------------------------------------------------
| 🔧 Route Order Fix Summary
|--------------------------------------------------------------------------
| 
| PROBLEM: Laravel was matching {employee} parameter instead of 'trash'
| SOLUTION: Moved all specific routes BEFORE parameterized routes
| 
| ORDER (CRITICAL):
| 1. Static routes (/employees/, /employees/create, /employees/trash)
| 2. Prefix groups (/employees/trash/*, /employees/photos/*)
| 3. Export routes (/employees/export/*)
| 4. Bulk operations (/employees/bulk/*)
| 5. Parameter routes (/employees/{employee}) - LAST
| 
| This ensures 'trash' is matched as a literal string before Laravel
| tries to match it as an {employee} parameter.
|
*/
