<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
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
            $query = Employee::withoutTrashed()->with('department');
            
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
            
            // Statistics for dashboard (with permissions)
            $stats = [
                'total' => Employee::withoutTrashed()->count(),
                'active' => Employee::withoutTrashed()->where('status', 'active')->count(),
                'express_users' => Employee::withoutTrashed()->whereNotNull('express_username')->count(),
                'vpn_users' => Employee::withoutTrashed()->where('vpn_access', true)->count(),
                'color_printing_users' => Employee::withoutTrashed()->where('color_printing', true)->count(),
                'trash_count' => Employee::onlyTrashed()->count()
            ];
            
            return view('employees.index', compact('employees', 'departments', 'stats'));
        } catch (\Exception $e) {
            return view('employees.index', [
                'employees' => collect(),
                'departments' => collect(),
                'stats' => ['total' => 0, 'active' => 0, 'express_users' => 0, 'vpn_users' => 0, 'color_printing_users' => 0, 'trash_count' => 0]
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
            return view('employees.create', compact('departments'));
        } catch (\Exception $e) {
            return redirect()->route('employees.index')
                ->with('error', 'ไม่สามารถเข้าถึงหน้าเพิ่มพนักงานได้');
        }
    }

    /**
     * Store a newly created resource in storage.
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
            
            // ✅ UPDATED: Auto-sync login_email
            $validated['login_email'] = $validated['email'];
            
            // Create employee
            $employee = Employee::create($validated);
            
            DB::commit();
            
            Log::info("Employee created successfully: {$employee->employee_code} with Permissions System v2.0");
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'เพิ่มพนักงานใหม่เรียบร้อยแล้ว',
                    'employee' => $employee->load('department'),
                    'redirect' => route('employees.show', $employee)
                ]);
            }
            
            return redirect()->route('employees.show', $employee)
                ->with('success', 'เพิ่มพนักงานใหม่เรียบร้อยแล้ว: ' . $employee->full_name_th);
                
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
            // Load relationships
            $employee->load('department');
            
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
            $employee->load('department');
            
            return view('employees.edit', compact('employee', 'departments'));
        } catch (\Exception $e) {
            return redirect()->route('employees.index')
                ->with('error', 'ไม่สามารถแก้ไขข้อมูลได้');
        }
    }

    /**
     * ✅ FIXED: Update method with proper password and permission handling
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
            
            // ✅ FIXED: Handle Permission Fields (NEW)
            $this->handlePermissionFields($validated);
            
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
                'permissions' => [
                    'vpn_access' => $validated['vpn_access'] ?? 'not_set',
                    'color_printing' => $validated['color_printing'] ?? 'not_set',
                    'remote_work' => $validated['remote_work'] ?? 'not_set',
                    'admin_access' => $validated['admin_access'] ?? 'not_set',
                ],
                'has_permission_fields' => array_key_exists('vpn_access', $validated)
            ]);
            
            // Update employee
            $employee->update($validated);
            
            // ✅ VERIFY: Check if permissions were actually saved
            $employee->refresh();
            Log::info('Employee permissions after update:', [
                'employee_id' => $employee->id,
                'vpn_access' => $employee->vpn_access,
                'color_printing' => $employee->color_printing,
                'remote_work' => $employee->remote_work,
                'admin_access' => $employee->admin_access,
            ]);
            
            DB::commit();
            
            Log::info("Employee updated successfully: {$employee->employee_code} with Permissions System v2.0");
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'อัปเดตข้อมูลพนักงานเรียบร้อยแล้ว (รวมสิทธิ์พิเศษ)',
                    'employee' => $employee->fresh()->load('department'),
                    'redirect' => route('employees.show', $employee)
                ]);
            }
            
            return redirect()->route('employees.show', $employee)
                ->with('success', 'อัปเดตข้อมูลพนักงานเรียบร้อยแล้ว: ' . $employee->full_name_th . ' (รวมสิทธิ์พิเศษ)');
                
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
     * Remove the specified resource from storage.
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

            $employeeName = $employee->full_name_th;
            $employeeCode = $employee->employee_code;
            
            // Soft delete
            $employee->delete();

            Log::info("Employee soft deleted: {$employeeName} (Code: {$employeeCode})");

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
}
