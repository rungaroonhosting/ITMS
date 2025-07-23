<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DepartmentController extends Controller
{
    /**
     * Display a listing of departments.
     */
    public function index()
    {
        try {
            // Load departments with employee counts
            $departments = Department::withCount([
                'employees',
                'activeEmployees',
                'expressEmployees'
            ])->get();

            // Calculate Express users count for each department
            foreach ($departments as $department) {
                $department->express_users_count = $department->employees()
                    ->whereNotNull('express_username')
                    ->count();
            }

            return view('departments.index', compact('departments'));

        } catch (\Exception $e) {
            Log::error('Department index error: ' . $e->getMessage());
            
            return view('departments.index', [
                'departments' => collect(),
                'error' => 'เกิดข้อผิดพลาดในการโหลดข้อมูลแผนก'
            ]);
        }
    }

    /**
     * Show the form for creating a new department.
     */
    public function create()
    {
        return view('departments.create');
    }

    /**
     * Store a newly created department.
     */
    public function store(Request $request)
    {
        try {
            // Validation rules with soft delete awareness
            $validator = Validator::make($request->all(), [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('departments', 'name')->whereNull('deleted_at')
                ],
                'code' => [
                    'required',
                    'string',
                    'max:10',
                    'alpha_num',
                    Rule::unique('departments', 'code')->whereNull('deleted_at')
                ],
                'description' => 'nullable|string|max:500',
                'is_active' => 'boolean',
                'express_enabled' => 'boolean'
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $validated = $validator->validated();

            // Auto-detect if department should have Express based on name
            if (!isset($validated['express_enabled'])) {
                $validated['express_enabled'] = $this->shouldHaveExpress($validated['name']);
            }

            // Create department
            $department = Department::create($validated);

            // Auto-generate Express credentials if Express is enabled
            if ($department->express_enabled) {
                // This will be handled by the Department model's boot method
                Log::info('Department created with Express support', [
                    'department_id' => $department->id,
                    'name' => $department->name
                ]);
            }

            return redirect()->route('departments.index')
                ->with('success', "สร้างแผนก '{$department->name}' เรียบร้อยแล้ว");

        } catch (\Exception $e) {
            Log::error('Department store error: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'เกิดข้อผิดพลาดในการสร้างแผนก: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified department.
     */
    public function show(Department $department)
    {
        try {
            // Load department with relationships
            $department->load([
                'employees' => function($query) {
                    $query->orderBy('first_name_th')->orderBy('last_name_th');
                }
            ]);

            // Calculate statistics
            $stats = [
                'total_employees' => $department->employees->count(),
                'active_employees' => $department->employees->where('status', 'active')->count(),
                'express_users' => $department->employees->whereNotNull('express_username')->count(),
                'express_coverage' => 0
            ];

            if ($stats['total_employees'] > 0) {
                $stats['express_coverage'] = round(($stats['express_users'] / $stats['total_employees']) * 100, 1);
            }

            return view('departments.show', compact('department', 'stats'));

        } catch (\Exception $e) {
            Log::error('Department show error: ' . $e->getMessage());
            
            return redirect()->route('departments.index')
                ->with('error', 'เกิดข้อผิดพลาดในการแสดงข้อมูลแผนก');
        }
    }

    /**
     * Show the form for editing the specified department.
     */
    public function edit(Department $department)
    {
        try {
            // Load employees count for display
            $department->loadCount([
                'employees',
                'activeEmployees',
                'expressEmployees'
            ]);

            return view('departments.edit', compact('department'));

        } catch (\Exception $e) {
            Log::error('Department edit error: ' . $e->getMessage());
            
            return redirect()->route('departments.index')
                ->with('error', 'เกิดข้อผิดพลาดในการแก้ไขแผนก');
        }
    }

    /**
     * Update the specified department.
     */
    public function update(Request $request, Department $department)
    {
        try {
            // Validation rules with soft delete awareness
            $validator = Validator::make($request->all(), [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('departments', 'name')
                        ->ignore($department->id)
                        ->whereNull('deleted_at')
                ],
                'code' => [
                    'required',
                    'string',
                    'max:10',
                    'alpha_num',
                    Rule::unique('departments', 'code')
                        ->ignore($department->id)
                        ->whereNull('deleted_at')
                ],
                'description' => 'nullable|string|max:500',
                'is_active' => 'boolean',
                'express_enabled' => 'boolean'
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $validated = $validator->validated();

            // Check if Express status is changing
            $expressStatusChanged = $department->express_enabled !== ($validated['express_enabled'] ?? false);

            // Update department
            $department->update($validated);

            // Handle Express status change
            if ($expressStatusChanged) {
                if ($department->express_enabled) {
                    $department->enableExpress();
                    $message = "อัพเดตแผนก '{$department->name}' และเปิดใช้งาน Express เรียบร้อยแล้ว";
                } else {
                    $department->disableExpress();
                    $message = "อัพเดตแผนก '{$department->name}' และปิดการใช้งาน Express เรียบร้อยแล้ว";
                }
            } else {
                $message = "อัพเดตแผนก '{$department->name}' เรียบร้อยแล้ว";
            }

            return redirect()->route('departments.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Department update error: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'เกิดข้อผิดพลาดในการอัพเดตแผนก: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified department (Soft Delete).
     */
    public function destroy(Department $department)
    {
        try {
            // ตรวจสอบว่าแผนกมีพนักงานหรือไม่
            $employeeCount = $department->employees()->count();
            
            if ($employeeCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "ไม่สามารถลบแผนก '{$department->name}' ได้ เนื่องจากมีพนักงาน {$employeeCount} คน"
                ], 400);
            }

            // ทำการ Soft Delete
            $departmentName = $department->name;
            $department->delete(); // This uses soft delete because of SoftDeletes trait

            Log::info('Department soft deleted', [
                'department_id' => $department->id,
                'name' => $departmentName,
                'deleted_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => "ลบแผนก '{$departmentName}' เรียบร้อยแล้ว"
            ]);

        } catch (\Exception $e) {
            Log::error('Department destroy error: ' . $e->getMessage(), [
                'department_id' => $department->id ?? null,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการลบแผนก: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle department status (Admin only).
     */
    public function toggleStatus(Request $request, Department $department)
    {
        try {
            $validated = $request->validate([
                'is_active' => 'required|boolean'
            ]);

            $department->update(['is_active' => $validated['is_active']]);

            $status = $validated['is_active'] ? 'เปิดใช้งาน' : 'ปิดใช้งาน';

            return response()->json([
                'success' => true,
                'message' => "{$status}แผนก '{$department->name}' เรียบร้อยแล้ว",
                'is_active' => $department->is_active
            ]);

        } catch (\Exception $e) {
            Log::error('Department toggle status error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการเปลี่ยนสถานะ'
            ], 500);
        }
    }

    /**
     * Toggle Express support for department (Admin only).
     */
    public function toggleExpress(Request $request, Department $department)
    {
        try {
            $validated = $request->validate([
                'express_enabled' => 'required|boolean'
            ]);

            $expressEnabled = $validated['express_enabled'];

            // Check if trying to disable Express with existing users
            if (!$expressEnabled) {
                $expressUsersCount = $department->employees()
                    ->whereNotNull('express_username')
                    ->count();

                if ($expressUsersCount > 0) {
                    Log::warning('Attempted to disable Express with existing users', [
                        'department_id' => $department->id,
                        'express_users_count' => $expressUsersCount
                    ]);
                }
            }

            // Use the model methods to handle Express toggle
            if ($expressEnabled) {
                $success = $department->enableExpress();
                $message = $success 
                    ? "เปิดใช้งาน Express สำหรับแผนก '{$department->name}' เรียบร้อยแล้ว"
                    : "เกิดข้อผิดพลาดในการเปิดใช้งาน Express";
            } else {
                $success = $department->disableExpress();
                $message = $success 
                    ? "ปิดการใช้งาน Express สำหรับแผนก '{$department->name}' เรียบร้อยแล้ว"
                    : "เกิดข้อผิดพลาดในการปิดใช้งาน Express";
            }

            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'express_enabled' => $department->fresh()->express_enabled
            ]);

        } catch (\Exception $e) {
            Log::error('Department toggle Express error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการเปลี่ยนสถานะ Express'
            ], 500);
        }
    }

    /**
     * Get Express statistics for all departments.
     */
    public function getExpressStats()
    {
        try {
            $departments = Department::with(['employees'])->get();

            $stats = [
                'totals' => [
                    'total_departments' => $departments->count(),
                    'express_departments' => $departments->where('express_enabled', true)->count(),
                    'total_express_users' => 0,
                    'department_coverage' => 0
                ],
                'departments' => []
            ];

            foreach ($departments as $dept) {
                $totalEmployees = $dept->employees->count();
                $expressUsers = $dept->employees->whereNotNull('express_username')->count();
                
                $stats['totals']['total_express_users'] += $expressUsers;

                $stats['departments'][] = [
                    'id' => $dept->id,
                    'name' => $dept->name,
                    'code' => $dept->code,
                    'express_enabled' => $dept->express_enabled,
                    'employee_count' => $totalEmployees,
                    'express_user_count' => $expressUsers,
                    'coverage_percentage' => $totalEmployees > 0 
                        ? round(($expressUsers / $totalEmployees) * 100, 1) 
                        : 0
                ];
            }

            $stats['totals']['department_coverage'] = $departments->count() > 0
                ? round(($stats['totals']['express_departments'] / $departments->count()) * 100, 1)
                : 0;

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Get Express stats error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการโหลดสถิติ Express'
            ], 500);
        }
    }

    /**
     * Bulk update Express support for multiple departments.
     */
    public function bulkUpdateExpress(Request $request)
    {
        try {
            $validated = $request->validate([
                'action' => 'required|in:activate,deactivate,enable_express,disable_express,delete',
                'department_ids' => 'required|array',
                'department_ids.*' => 'exists:departments,id'
            ]);

            $departments = Department::whereIn('id', $validated['department_ids'])->get();
            $successCount = 0;
            $errors = [];

            DB::beginTransaction();

            foreach ($departments as $department) {
                try {
                    switch ($validated['action']) {
                        case 'activate':
                            $department->update(['is_active' => true]);
                            $successCount++;
                            break;

                        case 'deactivate':
                            $department->update(['is_active' => false]);
                            $successCount++;
                            break;

                        case 'enable_express':
                            if ($department->enableExpress()) {
                                $successCount++;
                            } else {
                                $errors[] = "ไม่สามารถเปิด Express สำหรับแผนก '{$department->name}'";
                            }
                            break;

                        case 'disable_express':
                            if ($department->disableExpress()) {
                                $successCount++;
                            } else {
                                $errors[] = "ไม่สามารถปิด Express สำหรับแผนก '{$department->name}'";
                            }
                            break;

                        case 'delete':
                            if ($department->employees()->count() === 0) {
                                $department->delete();
                                $successCount++;
                            } else {
                                $errors[] = "ไม่สามารถลบแผนก '{$department->name}' ได้เนื่องจากมีพนักงาน";
                            }
                            break;
                    }
                } catch (\Exception $e) {
                    $errors[] = "แผนก '{$department->name}': " . $e->getMessage();
                }
            }

            DB::commit();

            $actionNames = [
                'activate' => 'เปิดใช้งาน',
                'deactivate' => 'ปิดใช้งาน',
                'enable_express' => 'เปิดใช้งาน Express',
                'disable_express' => 'ปิดใช้งาน Express',
                'delete' => 'ลบ'
            ];

            $message = "{$actionNames[$validated['action']]} {$successCount} แผนกเรียบร้อยแล้ว";
            
            if (!empty($errors)) {
                $message .= " (มีข้อผิดพลาด " . count($errors) . " รายการ)";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'success_count' => $successCount,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk update Express error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการดำเนินการ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export departments to Excel.
     */
    public function exportExcel()
    {
        try {
            $departments = Department::with(['employees'])->get();

            $filename = 'departments_' . date('Y-m-d_H-i-s') . '.csv';
            $headers = [
                "Content-type" => "text/csv",
                "Content-Disposition" => "attachment; filename=$filename",
                "Pragma" => "no-cache",
                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                "Expires" => "0"
            ];

            $callback = function() use($departments) {
                $file = fopen('php://output', 'w');
                
                // Add BOM for Thai characters
                fputs($file, "\xEF\xBB\xBF");
                
                // Headers
                fputcsv($file, [
                    'รหัสแผนก',
                    'ชื่อแผนก',
                    'รายละเอียด',
                    'สถานะ',
                    'รองรับ Express',
                    'จำนวนพนักงาน',
                    'ผู้ใช้ Express',
                    'วันที่สร้าง'
                ]);
                
                foreach ($departments as $dept) {
                    $expressUsers = $dept->employees->whereNotNull('express_username')->count();
                    
                    fputcsv($file, [
                        $dept->code,
                        $dept->name,
                        $dept->description ?: '',
                        $dept->is_active ? 'ใช้งาน' : 'ไม่ใช้งาน',
                        $dept->express_enabled ? 'รองรับ' : 'ไม่รองรับ',
                        $dept->employees->count(),
                        $expressUsers,
                        $dept->created_at->format('d/m/Y H:i')
                    ]);
                }
                
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            Log::error('Department export error: ' . $e->getMessage());
            
            return redirect()->route('departments.index')
                ->with('error', 'เกิดข้อผิดพลาดในการส่งออกข้อมูล');
        }
    }

    /**
     * Get Express users for a specific department.
     */
    public function getExpressUsers(Department $department)
    {
        try {
            $expressUsers = $department->employees()
                ->whereNotNull('express_username')
                ->select('id', 'first_name_th', 'last_name_th', 'express_username', 'status')
                ->get()
                ->map(function($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->first_name_th . ' ' . $user->last_name_th,
                        'express_username' => $user->express_username,
                        'status' => $user->status
                    ];
                });

            return response()->json([
                'success' => true,
                'department' => [
                    'id' => $department->id,
                    'name' => $department->name,
                    'express_enabled' => $department->express_enabled
                ],
                'express_users' => $expressUsers
            ]);

        } catch (\Exception $e) {
            Log::error('Get Express users error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการโหลดข้อมูลผู้ใช้ Express'
            ], 500);
        }
    }

    /**
     * API: Get departments list for AJAX.
     */
    public function apiList(Request $request)
    {
        try {
            $query = Department::query();

            // Apply filters
            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('code', 'LIKE', "%{$search}%");
                });
            }

            if ($request->filled('status')) {
                $status = $request->input('status');
                if ($status === 'active') {
                    $query->where('is_active', true);
                } elseif ($status === 'inactive') {
                    $query->where('is_active', false);
                }
            }

            if ($request->filled('express')) {
                $express = $request->input('express');
                if ($express === 'enabled') {
                    $query->where('express_enabled', true);
                } elseif ($express === 'disabled') {
                    $query->where('express_enabled', false);
                }
            }

            $departments = $query->withCount(['employees', 'expressEmployees'])
                                ->orderBy('name')
                                ->get()
                                ->map(function($dept) {
                                    return $dept->toApiArray();
                                });

            return response()->json([
                'success' => true,
                'data' => $departments
            ]);

        } catch (\Exception $e) {
            Log::error('API departments list error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการโหลดข้อมูล'
            ], 500);
        }
    }

    /**
     * Get department select options.
     */
    public function getSelectOptions()
    {
        try {
            $departments = Department::active()
                ->orderBy('name')
                ->get(['id', 'name', 'code', 'express_enabled'])
                ->map(function($dept) {
                    return [
                        'id' => $dept->id,
                        'name' => $dept->name,
                        'code' => $dept->code,
                        'express_enabled' => $dept->express_enabled,
                        'display_name' => $dept->name . ($dept->express_enabled ? ' ⚡' : '')
                    ];
                });

            return response()->json([
                'success' => true,
                'departments' => $departments
            ]);

        } catch (\Exception $e) {
            Log::error('Get select options error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการโหลดตัวเลือกแผนก'
            ], 500);
        }
    }

    /**
     * Check Express support for department.
     */
    public function checkExpressSupport(Request $request)
    {
        try {
            $departmentId = $request->input('department_id');
            
            if (!$departmentId) {
                return response()->json([
                    'success' => false,
                    'message' => 'กรุณาระบุ ID ของแผนก'
                ], 400);
            }

            $department = Department::find($departmentId);
            
            if (!$department) {
                return response()->json([
                    'success' => false,
                    'message' => 'ไม่พบแผนกที่ระบุ'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'department_id' => $department->id,
                'department_name' => $department->name,
                'express_enabled' => $department->express_enabled,
                'express_users_count' => $department->employees()
                    ->whereNotNull('express_username')
                    ->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Check Express support error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการตรวจสอบการรองรับ Express'
            ], 500);
        }
    }

    /**
     * Generate unique department code.
     */
    public function generateCode(Request $request)
    {
        try {
            $name = $request->input('name', '');
            
            // Generate code from department name
            $code = $this->generateDepartmentCode($name);
            
            // Ensure uniqueness
            $originalCode = $code;
            $counter = 1;
            
            while (Department::where('code', $code)->whereNull('deleted_at')->exists()) {
                $code = $originalCode . $counter;
                $counter++;
            }

            return response()->json([
                'success' => true,
                'code' => $code
            ]);

        } catch (\Exception $e) {
            Log::error('Generate code error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการสร้างรหัสแผนก'
            ], 500);
        }
    }

    // ===========================================
    // HELPER METHODS
    // ===========================================

    /**
     * Check if department should have Express based on name.
     */
    private function shouldHaveExpress($name): bool
    {
        $accountingKeywords = [
            'บัญชี', 'การเงิน', 'accounting', 'finance', 'acc', 'fin'
        ];

        $name = strtolower($name);
        
        foreach ($accountingKeywords as $keyword) {
            if (str_contains($name, strtolower($keyword))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate department code from name.
     */
    private function generateDepartmentCode($name): string
    {
        // Remove common words and get initials
        $words = explode(' ', $name);
        $code = '';
        
        foreach ($words as $word) {
            if (strlen($word) > 0) {
                $code .= strtoupper(substr($word, 0, 1));
            }
        }
        
        // If code is too short, pad with department name
        if (strlen($code) < 3) {
            $code = strtoupper(substr($name, 0, 3));
        }
        
        // Remove non-alphanumeric characters
        $code = preg_replace('/[^A-Z0-9]/', '', $code);
        
        return substr($code, 0, 10); // Limit to 10 characters
    }
}
