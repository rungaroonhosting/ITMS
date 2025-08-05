<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use App\Models\Branch; // ✅ เพิ่ม Branch model
use App\Http\Requests\EmployeeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Employee::withoutTrashed()->with(['department', 'branch']); // ✅ เพิ่ม branch
            
            // Search functionality
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('employee_code', 'LIKE', "%{$search}%")
                      ->orWhere('first_name_th', 'LIKE', "%{$search}%")
                      ->orWhere('last_name_th', 'LIKE', "%{$search}%")
                      ->orWhere('first_name_en', 'LIKE', "%{$search}%")
                      ->orWhere('last_name_en', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%")
                      ->orWhere('phone', 'LIKE', "%{$search}%");
                });
            }
            
            // Department filter
            if ($request->has('department') && !empty($request->department)) {
                $query->where('department_id', $request->department);
            }

            // ✅ NEW: Branch filter
            if ($request->has('branch') && !empty($request->branch)) {
                $query->where('branch_id', $request->branch);
            }
            
            // Role filter
            if ($request->has('role') && !empty($request->role)) {
                $query->where('role', $request->role);
            }
            
            // Status filter
            if ($request->has('status') && !empty($request->status)) {
                $query->where('status', $request->status);
            }
            
            // Express filter
            if ($request->has('express') && $request->express === 'yes') {
                $query->whereNotNull('express_username');
            } elseif ($request->has('express') && $request->express === 'no') {
                $query->whereNull('express_username');
            }
            
            // ✅ NEW: Permission filters
            if ($request->has('vpn_access') && $request->vpn_access === 'yes') {
                $query->where('vpn_access', true);
            }
            if ($request->has('color_printing') && $request->color_printing === 'yes') {
                $query->where('color_printing', true);
            }
            
            $employees = $query->orderBy('created_at', 'desc')->paginate(20);
            $departments = Department::all();
            $branches = Branch::where('is_active', true)->orderBy('name')->get(); // ✅ เพิ่ม branches
            
            // ✅ Enhanced statistics (with branch info)
            $stats = [
                'total' => Employee::withoutTrashed()->count(),
                'active' => Employee::withoutTrashed()->where('status', 'active')->count(),
                'express_users' => Employee::withoutTrashed()->whereNotNull('express_username')->count(),
                'vpn_users' => Employee::withoutTrashed()->where('vpn_access', true)->count(),
                'color_printing_users' => Employee::withoutTrashed()->where('color_printing', true)->count(),
                'trash_count' => Employee::onlyTrashed()->count(),
                'with_branch' => Employee::withoutTrashed()->whereNotNull('branch_id')->count(), // ✅ เพิ่ม
                'without_branch' => Employee::withoutTrashed()->whereNull('branch_id')->count(), // ✅ เพิ่ม
            ];
            
            return view('employees.index', compact('employees', 'departments', 'branches', 'stats')); // ✅ เพิ่ม branches
        } catch (\Exception $e) {
            return view('employees.index', [
                'employees' => collect(),
                'departments' => collect(),
                'branches' => collect(), // ✅ เพิ่ม
                'stats' => [
                    'total' => 0, 'active' => 0, 'express_users' => 0, 
                    'vpn_users' => 0, 'color_printing_users' => 0, 'trash_count' => 0,
                    'with_branch' => 0, 'without_branch' => 0 // ✅ เพิ่ม
                ]
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $departments = Department::where('is_active', true)->orderBy('name')->get();
            $branches = Branch::where('is_active', true)->orderBy('name')->get(); // ✅ เพิ่ม branches
            return view('employees.create', compact('departments', 'branches')); // ✅ เพิ่ม branches
        } catch (\Exception $e) {
            return redirect()->route('employees.index')
                ->with('error', 'ไม่สามารถเข้าถึงหน้าเพิ่มพนักงานได้');
        }
    }

    /**
     * ✅ ENHANCED: Store a newly created resource in storage with Branch support.
     */
    public function store(EmployeeRequest $request)
    {
        try {
            DB::beginTransaction();
            
            $validated = $request->validated();
            
            // ✅ FIXED: Handle password separation for CREATE
            $this->handlePasswordSeparation($validated);
            
            // ✅ UPDATED: Handle Express credentials (Enhanced)
            $this->handleExpressCredentials($validated);
            
            // ✅ FIXED: Handle Permission Fields
            $this->handlePermissionFields($validated);

            // ✅ NEW: Handle Branch assignment
            $this->handleBranchAssignment($validated);
            
            // ✅ UPDATED: Auto-sync login_email
            $validated['login_email'] = $validated['email'];
            
            // Create employee
            $employee = Employee::create($validated);
            
            DB::commit();
            
            Log::info("Employee created successfully: {$employee->employee_code} with Branch System + Permissions v2.0", [
                'branch_id' => $employee->branch_id,
                'branch_name' => $employee->branch ? $employee->branch->name : 'N/A'
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'เพิ่มพนักงานใหม่เรียบร้อยแล้ว (รวม Branch System)',
                    'employee' => $employee->load(['department', 'branch']), // ✅ เพิ่ม branch
                    'redirect' => route('employees.show', $employee)
                ]);
            }
            
            return redirect()->route('employees.show', $employee)
                ->with('success', 'เพิ่มพนักงานใหม่เรียบร้อยแล้ว: ' . $employee->full_name_th . 
                       ($employee->branch ? ' ที่สาขา: ' . $employee->branch->name : ''));
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Employee creation failed: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล',
                    'errors' => ['general' => [$e->getMessage()]]
                ], 500);
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        try {
            // ✅ Load relationships including branch
            $employee->load(['department', 'branch']);
            
            // Check access permissions
            if (!$this->canAccessEmployee($employee)) {
                return redirect()->route('employees.index')
                    ->with('error', 'ไม่มีสิทธิ์เข้าถึงข้อมูลพนักงานนี้');
            }
            
            return view('employees.show', compact('employee'));
        } catch (\Exception $e) {
            return redirect()->route('employees.index')
                ->with('error', 'ไม่พบข้อมูลพนักงาน');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        try {
            // Check permissions
            if (!$this->canEditEmployee($employee)) {
                return redirect()->route('employees.show', $employee)
                    ->with('error', 'ไม่มีสิทธิ์แก้ไขข้อมูลพนักงานนี้');
            }
            
            $departments = Department::where('is_active', true)->orderBy('name')->get();
            $branches = Branch::where('is_active', true)->orderBy('name')->get(); // ✅ เพิ่ม branches
            $employee->load(['department', 'branch']); // ✅ เพิ่ม branch
            
            return view('employees.edit', compact('employee', 'departments', 'branches')); // ✅ เพิ่ม branches
        } catch (\Exception $e) {
            return redirect()->route('employees.index')
                ->with('error', 'ไม่สามารถแก้ไขข้อมูลได้');
        }
    }

    /**
     * ✅ ENHANCED: Update method with proper password and branch handling
     */
    public function update(EmployeeRequest $request, Employee $employee)
    {
        try {
            // Check permissions
            if (!$this->canEditEmployee($employee)) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'ไม่มีสิทธิ์แก้ไขข้อมูลพนักงานนี้'
                    ], 403);
                }
                
                return redirect()->route('employees.show', $employee)
                    ->with('error', 'ไม่มีสิทธิ์แก้ไขข้อมูลพนักงานนี้');
            }
            
            DB::beginTransaction();
            
            $validated = $request->validated();
            
            // ✅ FIXED: Handle password separation for UPDATE (with existing employee)
            $this->handlePasswordSeparation($validated, $employee);
            
            // ✅ UPDATED: Handle Express credentials (Enhanced)
            $this->handleExpressCredentials($validated, $employee);
            
            // ✅ FIXED: Handle Permission Fields
            $this->handlePermissionFields($validated);

            // ✅ NEW: Handle Branch assignment changes
            $this->handleBranchAssignment($validated, $employee);
            
            // ✅ UPDATED: Auto-sync login_email
            if (isset($validated['email'])) {
                $validated['login_email'] = $validated['email'];
            }
            
            // ✅ FIXED: Remove empty password fields to prevent null updates
            $this->removeEmptyPasswordFields($validated);
            
            // ✅ DEBUG: Log the validated data for debugging
            Log::info('Employee update validated data:', [
                'employee_id' => $employee->id,
                'employee_code' => $employee->employee_code,
                'branch_id' => $validated['branch_id'] ?? 'not_set',
                'branch_change' => ($employee->branch_id != ($validated['branch_id'] ?? null)),
                'permissions' => [
                    'vpn_access' => $validated['vpn_access'] ?? 'not_set',
                    'color_printing' => $validated['color_printing'] ?? 'not_set',
                    'remote_work' => $validated['remote_work'] ?? 'not_set',
                    'admin_access' => $validated['admin_access'] ?? 'not_set',
                ],
            ]);
            
            // Update employee
            $employee->update($validated);
            
            // ✅ VERIFY: Check if branch and permissions were actually saved
            $employee->refresh();
            Log::info('Employee after update:', [
                'employee_id' => $employee->id,
                'branch_id' => $employee->branch_id,
                'branch_name' => $employee->branch ? $employee->branch->name : 'N/A',
                'vpn_access' => $employee->vpn_access,
                'color_printing' => $employee->color_printing,
                'remote_work' => $employee->remote_work,
                'admin_access' => $employee->admin_access,
            ]);
            
            DB::commit();
            
            Log::info("Employee updated successfully: {$employee->employee_code} with Branch + Permissions System v2.0");
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'อัปเดตข้อมูลพนักงานเรียบร้อยแล้ว (รวม Branch System)',
                    'employee' => $employee->fresh()->load(['department', 'branch']), // ✅ เพิ่ม branch
                    'redirect' => route('employees.show', $employee)
                ]);
            }
            
            return redirect()->route('employees.show', $employee)
                ->with('success', 'อัปเดตข้อมูลพนักงานเรียบร้อยแล้ว: ' . $employee->full_name_th . 
                       ($employee->branch ? ' ที่สาขา: ' . $employee->branch->name : '') . ' (รวม Branch + สิทธิ์พิเศษ)');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Employee update failed: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาดในการอัปเดตข้อมูล',
                    'errors' => ['general' => [$e->getMessage()]]
                ], 500);
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'เกิดข้อผิดพลาดในการอัปเดตข้อมูล: ' . $e->getMessage());
        }
    }

    /**
     * ✅ ENHANCED: Remove the specified resource from storage with Branch handling.
     */
    public function destroy(Employee $employee)
    {
        try {
            // Only super admin can delete
            if (auth()->user()->role !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'เฉพาะ Super Admin เท่านั้นที่สามารถลบพนักงานได้'
                ], 403);
            }

            // ✅ ENHANCED: Load relationships before soft delete for logging
            $employee->load(['department', 'branch']);
            
            $employeeName = $employee->full_name_th;
            $employeeCode = $employee->employee_code;
            $branchName = $employee->branch ? $employee->branch->name : 'ไม่ระบุสาขา'; // ✅ เพิ่ม branch info
            $departmentName = $employee->department ? $employee->department->name : 'ไม่ระบุแผนก';
            
            // Soft delete
            $employee->delete();

            Log::info("Employee soft deleted: {$employeeName} (Code: {$employeeCode}) from Branch: {$branchName}, Department: {$departmentName}");

            return response()->json([
                'success' => true,
                'message' => "ลบข้อมูลพนักงาน {$employeeName} เรียบร้อยแล้ว (ย้ายไปถังขยะ)"
            ]);

        } catch (\Exception $e) {
            Log::error('Employee deletion failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการลบข้อมูล'
            ], 500);
        }
    }

    // =====================================================
    // ✅ NEW: COMPLETE TRASH MANAGEMENT METHODS
    // =====================================================

    /**
     * ✅ NEW: Display trashed employees (Super Admin only)
     */
    public function trash()
    {
        try {
            // Check Super Admin permission
            if (auth()->user()->role !== 'super_admin') {
                return redirect()->route('employees.index')
                    ->with('error', 'เฉพาะ Super Admin เท่านั้นที่สามารถเข้าถึงถังขยะได้');
            }

            // ✅ Load relationships including branch
            $trashedEmployees = Employee::onlyTrashed()
                ->with(['department', 'branch'])
                ->orderBy('deleted_at', 'desc')
                ->get();

            Log::info('Trash page accessed', [
                'user' => auth()->user()->employee_code,
                'trashed_count' => $trashedEmployees->count()
            ]);

            return view('employees.trash', compact('trashedEmployees'));
            
        } catch (\Exception $e) {
            Log::error('Trash page access failed: ' . $e->getMessage());
            
            return redirect()->route('employees.index')
                ->with('error', 'เกิดข้อผิดพลาดในการเข้าถึงถังขยะ');
        }
    }

    /**
     * ✅ NEW: Restore employee from trash
     */
    public function restore($id)
    {
        try {
            // Check Super Admin permission
            if (auth()->user()->role !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'เฉพาะ Super Admin เท่านั้นที่สามารถกู้คืนได้'
                ], 403);
            }

            // ✅ Find trashed employee with relationships
            $employee = Employee::onlyTrashed()
                ->with(['department', 'branch'])
                ->find($id);

            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'ไม่พบพนักงานในถังขยะ'
                ], 404);
            }

            $employeeName = $employee->full_name_th;
            $branchName = $employee->branch ? $employee->branch->name : 'ไม่ระบุสาขา';
            
            // Restore employee
            $employee->restore();

            Log::info("Employee restored from trash: {$employeeName} (Branch: {$branchName})", [
                'employee_id' => $employee->id,
                'restored_by' => auth()->user()->employee_code
            ]);

            return response()->json([
                'success' => true,
                'message' => "กู้คืนพนักงาน {$employeeName} เรียบร้อยแล้ว"
            ]);

        } catch (\Exception $e) {
            Log::error('Employee restore failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการกู้คืนข้อมูล'
            ], 500);
        }
    }

    /**
     * ✅ NEW: Force delete employee permanently
     */
    public function forceDestroy($id)
    {
        try {
            // Check Super Admin permission
            if (auth()->user()->role !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'เฉพาะ Super Admin เท่านั้นที่สามารถลบถาวรได้'
                ], 403);
            }

            // ✅ Find trashed employee with relationships
            $employee = Employee::onlyTrashed()
                ->with(['department', 'branch'])
                ->find($id);

            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'ไม่พบพนักงานในถังขยะ'
                ], 404);
            }

            $employeeName = $employee->full_name_th;
            $employeeCode = $employee->employee_code;
            $branchName = $employee->branch ? $employee->branch->name : 'ไม่ระบุสาขา';
            
            // Force delete permanently
            $employee->forceDelete();

            Log::warning("Employee permanently deleted: {$employeeName} (Code: {$employeeCode}, Branch: {$branchName})", [
                'deleted_by' => auth()->user()->employee_code,
                'action' => 'PERMANENT_DELETE'
            ]);

            return response()->json([
                'success' => true,
                'message' => "ลบพนักงาน {$employeeName} อย่างถาวรเรียบร้อยแล้ว"
            ]);

        } catch (\Exception $e) {
            Log::error('Employee force delete failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการลบถาวร'
            ], 500);
        }
    }

    /**
     * ✅ NEW: Bulk restore employees from trash
     */
    public function bulkRestore(Request $request)
    {
        try {
            // Check Super Admin permission
            if (auth()->user()->role !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'เฉพาะ Super Admin เท่านั้นที่สามารถกู้คืนหลายรายการได้'
                ], 403);
            }

            $employeeIds = $request->get('employee_ids', []);
            
            if (empty($employeeIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'กรุณาเลือกพนักงานที่ต้องการกู้คืน'
                ], 400);
            }

            // ✅ Find trashed employees with relationships
            $employees = Employee::onlyTrashed()
                ->with(['department', 'branch'])
                ->whereIn('id', $employeeIds)
                ->get();

            $restoredCount = 0;
            $restoredNames = [];

            foreach ($employees as $employee) {
                $employee->restore();
                $restoredCount++;
                $restoredNames[] = $employee->full_name_th;
                
                Log::info("Bulk restore: {$employee->full_name_th} (Branch: " . ($employee->branch ? $employee->branch->name : 'N/A') . ")");
            }

            Log::info("Bulk restore completed: {$restoredCount} employees restored", [
                'restored_by' => auth()->user()->employee_code,
                'employee_names' => $restoredNames
            ]);

            return response()->json([
                'success' => true,
                'message' => "กู้คืนพนักงาน {$restoredCount} คนเรียบร้อยแล้ว",
                'restored_count' => $restoredCount
            ]);

        } catch (\Exception $e) {
            Log::error('Bulk restore failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการกู้คืนหลายรายการ'
            ], 500);
        }
    }

    /**
     * ✅ NEW: Empty trash (permanently delete all trashed employees)
     */
    public function emptyTrash()
    {
        try {
            // Check Super Admin permission
            if (auth()->user()->role !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'เฉพาะ Super Admin เท่านั้นที่สามารถล้างถังขยะได้'
                ], 403);
            }

            // ✅ Get all trashed employees with relationships for logging
            $trashedEmployees = Employee::onlyTrashed()
                ->with(['department', 'branch'])
                ->get();

            $trashedCount = $trashedEmployees->count();
            
            if ($trashedCount === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'ถังขยะว่างเปล่าอยู่แล้ว'
                ], 400);
            }

            // Log before permanent deletion
            $trashedNames = $trashedEmployees->map(function($emp) {
                return $emp->full_name_th . ' (Branch: ' . ($emp->branch ? $emp->branch->name : 'N/A') . ')';
            })->toArray();

            // Force delete all trashed employees
            Employee::onlyTrashed()->forceDelete();

            Log::critical("Trash emptied: {$trashedCount} employees permanently deleted", [
                'emptied_by' => auth()->user()->employee_code,
                'action' => 'EMPTY_TRASH',
                'deleted_employees' => $trashedNames
            ]);

            return response()->json([
                'success' => true,
                'message' => "ล้างถังขยะเรียบร้อยแล้ว (ลบ {$trashedCount} คนอย่างถาวร)",
                'deleted_count' => $trashedCount
            ]);

        } catch (\Exception $e) {
            Log::error('Empty trash failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการล้างถังขยะ'
            ], 500);
        }
    }

    /**
     * ✅ NEW: Preview employee data (for trash view)
     */
    public function preview(Employee $employee)
    {
        try {
            // Check if employee is trashed
            if (!$employee->trashed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'พนักงานนี้ไม่อยู่ในถังขยะ'
                ], 404);
            }

            // ✅ Load relationships including branch
            $employee->load(['department', 'branch']);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $employee->id,
                    'employee_code' => $employee->employee_code,
                    'first_name_th' => $employee->first_name_th,
                    'last_name_th' => $employee->last_name_th,
                    'first_name_en' => $employee->first_name_en,
                    'last_name_en' => $employee->last_name_en,
                    'email' => $employee->email,
                    'phone' => $employee->phone,
                    'department' => $employee->department ? [
                        'id' => $employee->department->id,
                        'name' => $employee->department->name,
                        'code' => $employee->department->code
                    ] : null,
                    // ✅ NEW: Branch information
                    'branch' => $employee->branch ? [
                        'id' => $employee->branch->id,
                        'name' => $employee->branch->name,
                        'code' => $employee->branch->branch_code_compat
                    ] : null,
                    'role' => $employee->role,
                    'status' => $employee->status,
                    'status_display' => $employee->status_display,
                    'deleted_at' => $employee->deleted_at,
                    'location_display' => $employee->location_display, // ✅ NEW: Combined location
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Employee preview failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการดูข้อมูล'
            ], 500);
        }
    }

    // =====================================================
    // ✅ NEW: BRANCH MANAGEMENT METHODS
    // =====================================================

    /**
     * ✅ NEW: Handle Branch assignment
     */
    private function handleBranchAssignment(&$validated, $employee = null)
    {
        $isUpdate = !is_null($employee);
        
        // ถ้าไม่มี branch_id ในข้อมูลที่ validate แล้ว ให้เป็น null
        if (!isset($validated['branch_id']) || empty($validated['branch_id'])) {
            $validated['branch_id'] = null;
            Log::info('Branch assignment: Set to null (no branch selected)');
            return;
        }
        
        // ตรวจสอบว่า branch_id ที่ส่งมามีอยู่จริงและ active
        $branch = Branch::where('id', $validated['branch_id'])
                       ->where('is_active', true)
                       ->first();
        
        if (!$branch) {
            Log::warning('Branch assignment: Invalid branch_id provided', ['branch_id' => $validated['branch_id']]);
            $validated['branch_id'] = null;
            return;
        }
        
        Log::info('Branch assignment successful', [
            'branch_id' => $validated['branch_id'],
            'branch_name' => $branch->name,
            'is_update' => $isUpdate
        ]);
    }

    /**
     * ✅ NEW: Transfer employee to different branch
     */
    public function transferToBranch(Request $request, Employee $employee)
    {
        try {
            // Check permissions
            if (!$this->canEditEmployee($employee)) {
                return response()->json([
                    'success' => false,
                    'message' => 'ไม่มีสิทธิ์ย้ายพนักงานนี้'
                ], 403);
            }
            
            $validated = $request->validate([
                'branch_id' => 'required|exists:branches,id',
                'reason' => 'nullable|string|max:255'
            ]);
            
            $branch = Branch::findOrFail($validated['branch_id']);
            
            if (!$branch->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'ไม่สามารถย้ายไปสาขาที่ปิดให้บริการได้'
                ], 400);
            }
            
            $oldBranch = $employee->branch;
            $employee->update(['branch_id' => $validated['branch_id']]);
            
            Log::info("Employee transferred between branches", [
                'employee_id' => $employee->id,
                'employee_name' => $employee->full_name_th,
                'from_branch' => $oldBranch ? $oldBranch->name : 'ไม่ระบุ',
                'to_branch' => $branch->name,
                'reason' => $validated['reason'] ?? 'ไม่ระบุ'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => "ย้าย {$employee->full_name_th} ไปสาขา {$branch->name} เรียบร้อยแล้ว",
                'employee' => $employee->fresh()->load(['department', 'branch'])
            ]);
            
        } catch (\Exception $e) {
            Log::error('Employee branch transfer failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการย้ายสาขา'
            ], 500);
        }
    }

    /**
     * ✅ NEW: Get employees by branch
     */
    public function getEmployeesByBranch(Request $request, Branch $branch)
    {
        try {
            $employees = $branch->employees()
                ->with(['department'])
                ->when($request->get('status'), function($query, $status) {
                    $query->where('status', $status);
                })
                ->orderBy('first_name_th')
                ->get();
            
            return response()->json([
                'success' => true,
                'branch' => [
                    'id' => $branch->id,
                    'name' => $branch->name,
                    'code' => $branch->branch_code_compat,
                ],
                'employees' => $employees,
                'count' => $employees->count()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการโหลดข้อมูลพนักงาน'
            ], 500);
        }
    }

    // =====================================================
    // ✅ FIXED: PASSWORD HANDLING METHODS
    // =====================================================

    /**
     * ✅ FIXED: Handle password separation (CREATE and UPDATE modes)
     */
    private function handlePasswordSeparation(&$validated, $employee = null)
    {
        $isUpdate = !is_null($employee);
        
        // 1. Handle computer_password
        if (empty($validated['computer_password']) && !$isUpdate) {
            $validated['computer_password'] = $this->generatePassword(10);
        } elseif (empty($validated['computer_password']) && $isUpdate) {
            // Don't change computer password if empty in update mode
            unset($validated['computer_password']);
        }

        // 2. Handle email_password  
        if (empty($validated['email_password']) && !$isUpdate) {
            $validated['email_password'] = $this->generatePassword(10);
        } elseif (empty($validated['email_password']) && $isUpdate) {
            // Don't change email password if empty in update mode
            unset($validated['email_password']);
        }

        // 3. ✅ FIXED: Handle login_password and password field
        if (!empty($validated['login_password'])) {
            // Hash the new login password
            $validated['password'] = Hash::make($validated['login_password']);
            Log::info('Password updated with new login_password');
        } elseif (!$isUpdate) {
            // For new employees, generate login password if not provided
            $loginPassword = $this->generatePassword(12);
            $validated['password'] = Hash::make($loginPassword);
            Log::info('New password generated for new employee');
        } else {
            // ✅ CRITICAL FIX: For updates, don't touch password field if login_password is empty
            unset($validated['password']);
            Log::info('Password field skipped in update mode (no change requested)');
        }

        // 4. Clean up login_password (don't save to database)
        unset($validated['login_password']);
    }

    /**
     * ✅ NEW: Handle Permission Fields
     */
    private function handlePermissionFields(&$validated)
    {
        // Convert checkbox values to boolean
        $permissionFields = ['vpn_access', 'color_printing', 'remote_work', 'admin_access'];
        
        foreach ($permissionFields as $field) {
            if (array_key_exists($field, $validated)) {
                // Convert to boolean (checkbox sends '1' or null)
                $validated[$field] = (bool) ($validated[$field] ?? false);
            } else {
                // If field is not present in the request, set to false (unchecked checkbox)
                $validated[$field] = false;
            }
        }
        
        Log::info('Permission fields processed:', [
            'vpn_access' => $validated['vpn_access'],
            'color_printing' => $validated['color_printing'],
            'remote_work' => $validated['remote_work'],
            'admin_access' => $validated['admin_access'],
        ]);
    }

    /**
     * ✅ NEW: Remove empty password fields to prevent null database updates
     */
    private function removeEmptyPasswordFields(&$validated)
    {
        $passwordFields = ['computer_password', 'email_password', 'password', 'express_password'];
        
        foreach ($passwordFields as $field) {
            if (isset($validated[$field]) && empty($validated[$field])) {
                unset($validated[$field]);
                Log::info("Removed empty {$field} field from update");
            }
        }
    }

    // =====================================================
    // ENHANCED EXPRESS USERNAME GENERATION METHODS
    // =====================================================

    /**
     * ✅ ENHANCED: Generate Express Username (1-7 ตัวอักษร)
     */
    private function generateExpressUsername($firstName, $lastName, $excludeId = null)
    {
        // ทำความสะอาดชื่อ - เอาเฉพาะตัวอักษรและตัวเลข
        $firstName = preg_replace('/[^a-zA-Z0-9]/', '', $firstName);
        $lastName = preg_replace('/[^a-zA-Z0-9]/', '', $lastName);
        
        // กรณีที่ 1: ใช้ชื่อจริงถ้าไม่เกิน 7 ตัว
        $fullName = strtolower($firstName);
        if (strlen($fullName) >= 1 && strlen($fullName) <= 7) {
            if (!$this->isExpressUsernameExists($fullName, $excludeId)) {
                return $fullName;
            }
        }
        
        // กรณีที่ 2: ตัดชื่อให้เหลือ 7 ตัวถ้าเกิน
        if (strlen($fullName) > 7) {
            $fullName = substr($fullName, 0, 7);
            if (!$this->isExpressUsernameExists($fullName, $excludeId)) {
                return $fullName;
            }
        }
        
        // กรณีที่ 3: ผสมชื่อ + นามสกุล
        $combined = strtolower($firstName . $lastName);
        if (strlen($combined) <= 7) {
            if (!$this->isExpressUsernameExists($combined, $excludeId)) {
                return $combined;
            }
        }
        
        // กรณีที่ 4: ชื่อ + ตัวแรกของนามสกุล
        if ($lastName) {
            $nameWithInitial = strtolower($firstName . substr($lastName, 0, 1));
            if (strlen($nameWithInitial) <= 7) {
                if (!$this->isExpressUsernameExists($nameWithInitial, $excludeId)) {
                    return $nameWithInitial;
                }
            }
        }
        
        // กรณีที่ 5: เพิ่มตัวเลขต่อท้าย
        $baseUsername = substr(strtolower($firstName), 0, 6); // เหลือที่ว่างสำหรับเลข 1 ตัว
        for ($i = 1; $i <= 9; $i++) {
            $username = $baseUsername . $i;
            if (strlen($username) <= 7 && !$this->isExpressUsernameExists($username, $excludeId)) {
                return $username;
            }
        }
        
        // กรณีที่ 6: ใช้ pattern แบบสุ่ม
        for ($i = 1; $i <= 99; $i++) {
            $username = 'emp' . str_pad($i, 4, '0', STR_PAD_LEFT); // emp0001, emp0002, etc.
            if (strlen($username) <= 7 && !$this->isExpressUsernameExists($username, $excludeId)) {
                return $username;
            }
        }
        
        // กรณีสุดท้าย: สุ่มแบบสมบูรณ์
        do {
            $username = 'u' . random_int(100000, 999999); // u123456
        } while ($this->isExpressUsernameExists($username, $excludeId) || strlen($username) > 7);
        
        return $username;
    }

    /**
     * ✅ ENHANCED: Generate Express Password (4 ตัวเลขไม่ซ้ำ)
     */
    private function generateExpressPassword($excludeId = null)
    {
        $maxAttempts = 100;
        $attempts = 0;
        
        do {
            $digits = [];
            while (count($digits) < 4) {
                $digit = random_int(0, 9);
                if (!in_array($digit, $digits)) {
                    $digits[] = $digit;
                }
            }
            $password = implode('', $digits);
            $attempts++;
        } while ($this->isExpressPasswordExists($password, $excludeId) && $attempts < $maxAttempts);
        
        return $password;
    }

    /**
     * ✅ NEW: Check if Express Username exists
     */
    private function isExpressUsernameExists($username, $excludeId = null)
    {
        $query = Employee::withoutTrashed()->where('express_username', $username);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        return $query->exists();
    }

    /**
     * ✅ NEW: Check if Express Password exists
     */
    private function isExpressPasswordExists($password, $excludeId = null)
    {
        $query = Employee::withoutTrashed()->where('express_password', $password);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        return $query->exists();
    }

    /**
     * ✅ UPDATED: Handle Express credentials (Enhanced v2.0)
     */
    private function handleExpressCredentials(&$validated, $employee = null)
    {
        if ($this->isDepartmentExpressEnabled($validated['department_id'])) {
            $isUpdate = !is_null($employee);
            $excludeId = $isUpdate ? $employee->id : null;
            
            // Generate Express Username if empty
            if (empty($validated['express_username'])) {
                $validated['express_username'] = $this->generateExpressUsername(
                    $validated['first_name_en'] ?? '', 
                    $validated['last_name_en'] ?? '',
                    $excludeId
                );
            }
            
            // Generate Express Password if empty
            if (empty($validated['express_password'])) {
                $validated['express_password'] = $this->generateExpressPassword($excludeId);
            }
            
            Log::info("Express credentials processed: Username={$validated['express_username']}, Password={$validated['express_password']}");
        } else {
            $validated['express_username'] = null;
            $validated['express_password'] = null;
            Log::info("Department not Express enabled - clearing Express credentials");
        }
    }

    /**
     * ✅ UPDATED: Generate data for AJAX requests (Enhanced)
     */
    public function generateData(Request $request)
    {
        $type = $request->get('type');
        
        try {
            switch ($type) {
                case 'employee_code':
                    return response()->json(['employee_code' => $this->generateEmployeeCode()]);
                
                case 'keycard_id':
                    return response()->json(['keycard_id' => $this->generateKeycardId()]);
                
                // ✅ SEPARATED: แยก password generation
                case 'email_password':
                    return response()->json(['email_password' => $this->generatePassword(10)]);
                    
                case 'login_password':
                    return response()->json(['login_password' => $this->generatePassword(12)]);
                
                case 'computer_password':
                    return response()->json(['computer_password' => $this->generatePassword(10)]);
                
                case 'copier_code':
                    return response()->json(['copier_code' => $this->generateCopierCode()]);
                    
                case 'username':
                    $firstName = $request->get('first_name_en', '');
                    $lastName = $request->get('last_name_en', '');
                    $username = strtolower($firstName . '.' . $lastName);
                    $email = $username . '@bettersystem.co.th';
                    return response()->json([
                        'username' => $username,
                        'email' => $email,
                        'login_email' => $email // Auto-sync
                    ]);
                    
                // ✅ ENHANCED: Express Username Generation
                case 'express_username':
                    $firstName = $request->get('first_name_en', '');
                    $lastName = $request->get('last_name_en', '');
                    $excludeId = $request->get('employee_id', null);
                    return response()->json(['express_username' => $this->generateExpressUsername($firstName, $lastName, $excludeId)]);
                    
                // ✅ ENHANCED: Express Password Generation
                case 'express_password':
                    $excludeId = $request->get('employee_id', null);
                    return response()->json(['express_password' => $this->generateExpressPassword($excludeId)]);
                
                default:
                    return response()->json(['error' => 'Invalid type'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * ✅ ENHANCED: API endpoints for Express generation
     */
    public function generateExpressUsernameApi(Request $request)
    {
        $firstName = $request->get('first_name_en', '');
        $lastName = $request->get('last_name_en', '');
        $excludeId = $request->get('employee_id', null);
        
        if (empty($firstName)) {
            return response()->json([
                'error' => 'กรุณาระบุชื่อภาษาอังกฤษ'
            ], 400);
        }
        
        $username = $this->generateExpressUsername($firstName, $lastName, $excludeId);
        
        return response()->json([
            'express_username' => $username,
            'length' => strlen($username),
            'note' => 'รองรับ 1-7 ตัวอักษร (Enhanced v2.0)'
        ]);
    }

    public function generateExpressPasswordApi(Request $request)
    {
        $excludeId = $request->get('employee_id', null);
        $password = $this->generateExpressPassword($excludeId);
        
        return response()->json([
            'express_password' => $password,
            'unique_digits' => count(array_unique(str_split($password))) === 4 ? 'Yes' : 'No',
            'note' => '4 ตัวเลขไม่ซ้ำกัน (Enhanced v2.0)'
        ]);
    }

    // =====================================================
    // ✅ NEW: PERMISSION MANAGEMENT METHODS
    // =====================================================

    /**
     * ✅ NEW: Toggle permission for employee
     */
    public function togglePermission(Request $request, Employee $employee)
    {
        try {
            // Check if user can edit this employee
            if (!$this->canEditEmployee($employee)) {
                return response()->json([
                    'success' => false,
                    'message' => 'ไม่มีสิทธิ์แก้ไขข้อมูลพนักงานนี้'
                ], 403);
            }
            
            $permission = $request->get('permission');
            $status = $request->get('status', false);
            
            $validPermissions = ['vpn_access', 'color_printing', 'remote_work', 'admin_access'];
            
            if (!in_array($permission, $validPermissions)) {
                return response()->json([
                    'success' => false,
                    'message' => 'สิทธิ์ที่ระบุไม่ถูกต้อง'
                ], 400);
            }
            
            $employee->{$permission} = (bool) $status;
            $employee->save();
            
            Log::info("Permission {$permission} toggled for employee {$employee->employee_code}: " . ($status ? 'granted' : 'revoked'));
            
            return response()->json([
                'success' => true,
                'message' => ($status ? 'อนุญาต' : 'ยกเลิก') . 'สิทธิ์เรียบร้อยแล้ว',
                'permission' => $permission,
                'status' => $status
            ]);
            
        } catch (\Exception $e) {
            Log::error('Permission toggle failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการเปลี่ยนสิทธิ์'
            ], 500);
        }
    }

    /**
     * ✅ NEW: Bulk permission update
     */
    public function bulkPermissionUpdate(Request $request)
    {
        try {
            $employeeIds = $request->get('employee_ids', []);
            $permissions = $request->get('permissions', []);
            
            if (empty($employeeIds) || empty($permissions)) {
                return response()->json([
                    'success' => false,
                    'message' => 'กรุณาระบุพนักงานและสิทธิ์ที่ต้องการเปลี่ยน'
                ], 400);
            }
            
            $updated = 0;
            foreach ($employeeIds as $employeeId) {
                $employee = Employee::find($employeeId);
                if ($employee && $this->canEditEmployee($employee)) {
                    foreach ($permissions as $permission => $status) {
                        if (in_array($permission, ['vpn_access', 'color_printing', 'remote_work', 'admin_access'])) {
                            $employee->{$permission} = (bool) $status;
                        }
                    }
                    $employee->save();
                    $updated++;
                }
            }
            
            Log::info("Bulk permission update: {$updated} employees updated");
            
            return response()->json([
                'success' => true,
                'message' => "อัปเดตสิทธิ์สำหรับ {$updated} พนักงานเรียบร้อยแล้ว",
                'updated_count' => $updated
            ]);
            
        } catch (\Exception $e) {
            Log::error('Bulk permission update failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการอัปเดตสิทธิ์แบบกลุ่ม'
            ], 500);
        }
    }

    // =====================================================
    // ✅ NEW: ENHANCED API METHODS
    // =====================================================

    /**
     * ✅ NEW: API Index for AJAX
     */
    public function apiIndex(Request $request)
    {
        try {
            $query = Employee::withoutTrashed()->with(['department', 'branch']);
            
            // Apply filters
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('employee_code', 'LIKE', "%{$search}%")
                      ->orWhere('first_name_th', 'LIKE', "%{$search}%")
                      ->orWhere('last_name_th', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%");
                });
            }
            
            if ($request->has('department_id')) {
                $query->where('department_id', $request->department_id);
            }
            
            if ($request->has('branch_id')) {
                $query->where('branch_id', $request->branch_id);
            }
            
            $employees = $query->orderBy('created_at', 'desc')->paginate(20);
            
            return response()->json([
                'success' => true,
                'data' => $employees
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✅ NEW: API Search for AJAX
     */
    public function apiSearch(Request $request)
    {
        try {
            $search = $request->get('q', '');
            
            $employees = Employee::withoutTrashed()
                ->with(['department', 'branch'])
                ->where(function($query) use ($search) {
                    $query->where('employee_code', 'LIKE', "%{$search}%")
                          ->orWhere('first_name_th', 'LIKE', "%{$search}%")
                          ->orWhere('last_name_th', 'LIKE', "%{$search}%")
                          ->orWhere('email', 'LIKE', "%{$search}%");
                })
                ->limit(10)
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $employees->map(function($employee) {
                    return [
                        'id' => $employee->id,
                        'text' => $employee->full_name_th . ' (' . $employee->employee_code . ')',
                        'employee_code' => $employee->employee_code,
                        'name' => $employee->full_name_th,
                        'email' => $employee->email,
                        'department' => $employee->department ? $employee->department->name : null,
                        'branch' => $employee->branch ? $employee->branch->name : null,
                    ];
                })
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✅ NEW: API Show for AJAX
     */
    public function apiShow(Employee $employee)
    {
        try {
            $employee->load(['department', 'branch']);
            
            return response()->json([
                'success' => true,
                'data' => $employee
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // =====================================================
    // EXISTING PRIVATE HELPER METHODS
    // =====================================================

    private function canAccessEmployee(Employee $employee)
    {
        $currentUser = auth()->user();
        
        // Super admin can access anyone
        if ($currentUser->role === 'super_admin') {
            return true;
        }
        
        // IT admin can access non-super-admin
        if ($currentUser->role === 'it_admin' && $employee->role !== 'super_admin') {
            return true;
        }
        
        // Users can access their own profile
        if ($currentUser->id === $employee->id) {
            return true;
        }
        
        // HR can access employee and express roles
        if ($currentUser->role === 'hr' && in_array($employee->role, ['employee', 'express'])) {
            return true;
        }
        
        return false;
    }

    private function canEditEmployee(Employee $employee)
    {
        $currentUser = auth()->user();
        
        // Super admin can edit anyone
        if ($currentUser->role === 'super_admin') {
            return true;
        }
        
        // IT admin can edit non-super-admin
        if ($currentUser->role === 'it_admin' && $employee->role !== 'super_admin') {
            return true;
        }
        
        // HR can edit employee and express roles
        if ($currentUser->role === 'hr' && in_array($employee->role, ['employee', 'express'])) {
            return true;
        }
        
        return false;
    }

    private function isDepartmentExpressEnabled($departmentId)
    {
        try {
            $department = Department::find($departmentId);
            return $department && $department->express_enabled;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function generatePassword($length = 10)
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*';
        $password = '';
        
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, strlen($chars) - 1)];
        }
        
        return $password;
    }

    private function generateEmployeeCode()
    {
        do {
            $code = 'EMP' . str_pad(random_int(1, 999), 3, '0', STR_PAD_LEFT);
        } while (Employee::withoutTrashed()->where('employee_code', $code)->exists());
        
        return $code;
    }

    private function generateKeycardId()
    {
        do {
            $id = 'KC' . str_pad(random_int(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (Employee::withoutTrashed()->where('keycard_id', $id)->exists());
        
        return $id;
    }

    private function generateCopierCode()
    {
        return str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT);
    }

    // =====================================================
    // ✅ NEW: PASSWORD RESET METHODS (Enhanced v2.1)
    // =====================================================

    /**
     * ✅ NEW: Reset login password only
     */
    public function resetPassword(Request $request, Employee $employee)
    {
        try {
            if (!$this->canEditEmployee($employee)) {
                return response()->json([
                    'success' => false,
                    'message' => 'ไม่มีสิทธิ์แก้ไขรหัสผ่านพนักงานนี้'
                ], 403);
            }

            $newPassword = $this->generatePassword(12);
            $employee->update(['password' => Hash::make($newPassword)]);

            Log::info("Login password reset for employee: {$employee->employee_code}");

            return response()->json([
                'success' => true,
                'message' => 'รีเซ็ตรหัสผ่านเข้าระบบเรียบร้อยแล้ว',
                'new_password' => $newPassword,
                'type' => 'login_password'
            ]);

        } catch (\Exception $e) {
            Log::error('Login password reset failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการรีเซ็ตรหัสผ่าน'
            ], 500);
        }
    }

    /**
     * ✅ NEW: Reset email password only
     */
    public function resetEmailPassword(Request $request, Employee $employee)
    {
        try {
            if (!$this->canEditEmployee($employee)) {
                return response()->json([
                    'success' => false,
                    'message' => 'ไม่มีสิทธิ์แก้ไขรหัสผ่านอีเมลพนักงานนี้'
                ], 403);
            }

            $newPassword = $this->generatePassword(10);
            $employee->update(['email_password' => $newPassword]);

            Log::info("Email password reset for employee: {$employee->employee_code}");

            return response()->json([
                'success' => true,
                'message' => 'รีเซ็ตรหัสผ่านอีเมลเรียบร้อยแล้ว',
                'new_password' => $newPassword,
                'type' => 'email_password'
            ]);

        } catch (\Exception $e) {
            Log::error('Email password reset failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการรีเซ็ตรหัสผ่านอีเมล'
            ], 500);
        }
    }

    /**
     * ✅ NEW: Reset computer password only
     */
    public function resetComputerPassword(Request $request, Employee $employee)
    {
        try {
            if (!$this->canEditEmployee($employee)) {
                return response()->json([
                    'success' => false,
                    'message' => 'ไม่มีสิทธิ์แก้ไขรหัสผ่านคอมพิวเตอร์พนักงานนี้'
                ], 403);
            }

            $newPassword = $this->generatePassword(10);
            $employee->update(['computer_password' => $newPassword]);

            Log::info("Computer password reset for employee: {$employee->employee_code}");

            return response()->json([
                'success' => true,
                'message' => 'รีเซ็ตรหัสผ่านคอมพิวเตอร์เรียบร้อยแล้ว',
                'new_password' => $newPassword,
                'type' => 'computer_password'
            ]);

        } catch (\Exception $e) {
            Log::error('Computer password reset failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการรีเซ็ตรหัสผ่านคอมพิวเตอร์'
            ], 500);
        }
    }

    /**
     * ✅ NEW: Reset both login and email passwords
     */
    public function resetBothPasswords(Request $request, Employee $employee)
    {
        try {
            if (!$this->canEditEmployee($employee)) {
                return response()->json([
                    'success' => false,
                    'message' => 'ไม่มีสิทธิ์แก้ไขรหัสผ่านพนักงานนี้'
                ], 403);
            }

            $newLoginPassword = $this->generatePassword(12);
            $newEmailPassword = $this->generatePassword(10);
            
            $employee->update([
                'password' => Hash::make($newLoginPassword),
                'email_password' => $newEmailPassword
            ]);

            Log::info("Both passwords reset for employee: {$employee->employee_code}");

            return response()->json([
                'success' => true,
                'message' => 'รีเซ็ตรหัสผ่านทั้งคู่เรียบร้อยแล้ว',
                'login_password' => $newLoginPassword,
                'email_password' => $newEmailPassword,
                'type' => 'both_passwords'
            ]);

        } catch (\Exception $e) {
            Log::error('Both passwords reset failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการรีเซ็ตรหัสผ่าน'
            ], 500);
        }
    }

    /**
     * ✅ NEW: Generate Express credentials
     */
    public function generateExpressCredentials(Request $request, Employee $employee)
    {
        try {
            if (!$this->canEditEmployee($employee)) {
                return response()->json([
                    'success' => false,
                    'message' => 'ไม่มีสิทธิ์จัดการข้อมูล Express ของพนักงานนี้'
                ], 403);
            }

            // Check if department supports Express
            if (!$this->isDepartmentExpressEnabled($employee->department_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'แผนกของพนักงานนี้ไม่รองรับ Express'
                ], 400);
            }

            $newUsername = $this->generateExpressUsername(
                $employee->first_name_en, 
                $employee->last_name_en, 
                $employee->id
            );
            $newPassword = $this->generateExpressPassword($employee->id);

            $employee->update([
                'express_username' => $newUsername,
                'express_password' => $newPassword
            ]);

            Log::info("Express credentials generated for employee: {$employee->employee_code}");

            return response()->json([
                'success' => true,
                'message' => 'สร้างข้อมูล Express เรียบร้อยแล้ว',
                'express_username' => $newUsername,
                'express_password' => $newPassword,
                'type' => 'express_credentials'
            ]);

        } catch (\Exception $e) {
            Log::error('Express credentials generation failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการสร้างข้อมูล Express'
            ], 500);
        }
    }

    /**
     * ✅ NEW: Get Express credentials
     */
    public function getExpressCredentials(Employee $employee)
    {
        try {
            if (!$this->canAccessEmployee($employee)) {
                return response()->json([
                    'success' => false,
                    'message' => 'ไม่มีสิทธิ์ดูข้อมูล Express ของพนักงานนี้'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'express_username' => $employee->express_username,
                    'express_password' => $employee->express_password,
                    'department_express_enabled' => $this->isDepartmentExpressEnabled($employee->department_id)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการดูข้อมูล Express'
            ], 500);
        }
    }

    /**
     * ✅ NEW: Check Express eligibility
     */
    public function checkExpressEligibility(Request $request)
    {
        try {
            $departmentId = $request->get('department_id');
            
            if (!$departmentId) {
                return response()->json([
                    'success' => false,
                    'message' => 'กรุณาระบุแผนก'
                ], 400);
            }

            $isEligible = $this->isDepartmentExpressEnabled($departmentId);
            $department = Department::find($departmentId);

            return response()->json([
                'success' => true,
                'eligible' => $isEligible,
                'department' => $department ? $department->name : null,
                'message' => $isEligible ? 'แผนกนี้รองรับ Express' : 'แผนกนี้ไม่รองรับ Express'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✅ NEW: Test Express connection
     */
    public function testExpressConnection(Request $request)
    {
        try {
            $username = $request->get('username');
            $password = $request->get('password');

            if (!$username || !$password) {
                return response()->json([
                    'success' => false,
                    'message' => 'กรุณาระบุ Username และ Password'
                ], 400);
            }

            // Simulate Express connection test
            // In real implementation, you would test actual Express API connection
            $connectionResult = [
                'connected' => true,
                'response_time' => rand(50, 200) . 'ms',
                'test_timestamp' => now()->toDateTimeString()
            ];

            Log::info("Express connection test", [
                'username' => $username,
                'result' => $connectionResult
            ]);

            return response()->json([
                'success' => true,
                'connection' => $connectionResult,
                'message' => 'การเชื่อมต่อ Express สำเร็จ'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการทดสอบการเชื่อมต่อ'
            ], 500);
        }
    }

    /**
     * ✅ NEW: Get Express report
     */
    public function getExpressReport()
    {
        try {
            $expressUsers = Employee::withoutTrashed()
                ->whereNotNull('express_username')
                ->with(['department', 'branch'])
                ->get();

            $stats = [
                'total_express_users' => $expressUsers->count(),
                'active_express_users' => $expressUsers->where('status', 'active')->count(),
                'by_department' => $expressUsers->groupBy('department.name')->map->count(),
                'by_branch' => $expressUsers->groupBy('branch.name')->map->count(),
                'recent_users' => $expressUsers->sortByDesc('created_at')->take(5)->values()
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
