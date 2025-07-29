<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     * ✅ FIXED: เพิ่ม withoutTrashed() เพื่อไม่แสดง soft deleted records
     */
    public function index()
    {
        try {
            // ✅ FIX: เพิ่ม withoutTrashed() เพื่อซ่อน soft deleted records
            $employees = Employee::withoutTrashed()->with('department')->orderBy('created_at', 'desc')->get();
            $departments = Department::all();
            
            return view('employees.index', compact('employees', 'departments'));
        } catch (\Exception $e) {
            return view('employees.index', [
                'employees' => collect(),
                'departments' => collect()
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $departments = Department::all();
            return view('employees.create', compact('departments'));
        } catch (\Exception $e) {
            // Fallback departments if database is not available
            $departments = collect([
                (object)['id' => 1, 'name' => 'บัญชี', 'express_enabled' => true],
                (object)['id' => 2, 'name' => 'IT', 'express_enabled' => false],
                (object)['id' => 3, 'name' => 'ฝ่ายขาย', 'express_enabled' => false],
                (object)['id' => 4, 'name' => 'การตลาด', 'express_enabled' => false],
                (object)['id' => 5, 'name' => 'บุคคล', 'express_enabled' => false],
                (object)['id' => 6, 'name' => 'ผลิต', 'express_enabled' => false],
                (object)['id' => 7, 'name' => 'คลังสินค้า', 'express_enabled' => false],
                (object)['id' => 8, 'name' => 'บริหาร', 'express_enabled' => false],
            ]);
            
            return view('employees.create', compact('departments'));
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // ✅ FIXED: Custom validation rules - ลบ unique constraint จาก phone
        $rules = [
            'employee_code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('employees', 'employee_code')->whereNull('deleted_at') // ✅ เพิ่มเงื่อนไข whereNull('deleted_at')
            ],
            'keycard_id' => [
                'required',
                'string',
                'max:20',
                Rule::unique('employees', 'keycard_id')->whereNull('deleted_at') // ✅ เพิ่มเงื่อนไข whereNull('deleted_at')
            ],
            'first_name_th' => 'required|string|max:100',
            'last_name_th' => 'required|string|max:100',
            'first_name_en' => 'required|string|max:100',
            'last_name_en' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'nickname' => 'nullable|string|max:50',
            'username' => [
                'required',
                'string',
                'max:100',
                Rule::unique('employees', 'username')->whereNull('deleted_at') // ✅ เพิ่มเงื่อนไข whereNull('deleted_at')
            ],
            'computer_password' => 'nullable|string|min:6',
            'copier_code' => 'nullable|string|max:10',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('employees', 'email')->whereNull('deleted_at') // ✅ เพิ่มเงื่อนไข whereNull('deleted_at')
            ],
            'email_password' => 'nullable|string|min:6',
            'express_username' => 'nullable|string|max:7',
            'express_password' => 'nullable|string|max:4',
            'department_id' => 'required|exists:departments,id',
            'position' => 'required|string|max:100',
            'role' => 'required|in:super_admin,it_admin,hr,manager,express,employee',
            'status' => 'required|in:active,inactive',
            'password' => 'required|string|min:6',
        ];

        $messages = [
            'employee_code.required' => 'รหัสพนักงานจำเป็นต้องกรอก',
            'employee_code.unique' => 'รหัสพนักงานนี้มีอยู่แล้วในระบบ',
            'keycard_id.required' => 'ID Keycard จำเป็นต้องกรอก',
            'keycard_id.unique' => 'ID Keycard นี้มีอยู่แล้วในระบบ',
            'first_name_th.required' => 'ชื่อภาษาไทยจำเป็นต้องกรอก',
            'last_name_th.required' => 'นามสกุลภาษาไทยจำเป็นต้องกรอก',
            'first_name_en.required' => 'ชื่อภาษาอังกฤษจำเป็นต้องกรอก',
            'last_name_en.required' => 'นามสกุลภาษาอังกฤษจำเป็นต้องกรอก',
            'phone.required' => 'เบอร์โทรศัพท์จำเป็นต้องกรอก',
            'username.required' => 'Username จำเป็นต้องกรอก',
            'username.unique' => 'Username นี้มีอยู่แล้วในระบบ',
            'email.required' => 'อีเมลจำเป็นต้องกรอก',
            'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
            'email.unique' => 'อีเมลนี้มีอยู่แล้วในระบบ',
            'department_id.required' => 'แผนกจำเป็นต้องเลือก',
            'department_id.exists' => 'แผนกที่เลือกไม่มีในระบบ',
            'position.required' => 'ตำแหน่งจำเป็นต้องกรอก',
            'role.required' => 'สิทธิ์การใช้งานจำเป็นต้องเลือก',
            'role.in' => 'สิทธิ์การใช้งานที่เลือกไม่ถูกต้อง',
            'status.required' => 'สถานะจำเป็นต้องเลือก',
            'password.required' => 'รหัสผ่านจำเป็นต้องกรอก',
            'password.min' => 'รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร',
        ];

        // Validate the request
        $validated = $request->validate($rules, $messages);

        try {
            // Set login_email same as email
            $validated['login_email'] = $validated['email'];

            // Generate passwords if not provided
            if (empty($validated['computer_password'])) {
                $validated['computer_password'] = $this->generatePassword();
            }

            if (empty($validated['email_password'])) {
                $validated['email_password'] = $this->generatePassword();
            }

            // Handle system password
            if (auth()->user()->role === 'super_admin' || auth()->user()->role === 'it_admin') {
                if (!empty($validated['password'])) {
                    $validated['password'] = Hash::make($validated['password']);
                }
            } else {
                // Default password for non-admin users
                $validated['password'] = Hash::make('Bettersystem123');
            }

            // Generate auto codes if empty
            if (empty($validated['employee_code'])) {
                $validated['employee_code'] = $this->generateEmployeeCode();
            }

            if (empty($validated['keycard_id'])) {
                $validated['keycard_id'] = $this->generateKeycardId();
            }

            if (empty($validated['copier_code'])) {
                $validated['copier_code'] = $this->generateCopierCode();
            }

            // Express fields handling
            if ($this->isDepartmentExpressEnabled($validated['department_id'])) {
                if (empty($validated['express_username'])) {
                    $validated['express_username'] = $this->generateExpressUsername(
                        $validated['first_name_en'], 
                        $validated['last_name_en']
                    );
                }
                
                if (empty($validated['express_password'])) {
                    $validated['express_password'] = $this->generateExpressPassword();
                }
            } else {
                $validated['express_username'] = null;
                $validated['express_password'] = null;
            }

            // Create the employee
            $employee = Employee::create($validated);

            if ($employee->express_username) {
                \Log::info("Express credentials created for employee: {$employee->employee_code}");
            }

            \Log::info("Employee created with phone (duplicates allowed): {$validated['phone']}");

            return redirect()->route('employees.index')
                ->with('success', 'เพิ่มพนักงานใหม่เรียบร้อยแล้ว: ' . $employee->first_name_th . ' ' . $employee->last_name_th . ' (เบอร์โทร: ' . $validated['phone'] . ' - ซ้ำได้)');

        } catch (\Exception $e) {
            \Log::error('Employee creation failed: ' . $e->getMessage());
            
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
            $employee->load('department');
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
            $departments = Department::all();
            return view('employees.edit', compact('employee', 'departments'));
        } catch (\Exception $e) {
            return redirect()->route('employees.index')
                ->with('error', 'ไม่สามารถแก้ไขข้อมูลได้');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        // ✅ FIXED: Custom validation rules for update with soft delete consideration
        $rules = [
            'employee_code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('employees', 'employee_code')->ignore($employee->id)->whereNull('deleted_at') // ✅ เพิ่มเงื่อนไข whereNull('deleted_at')
            ],
            'keycard_id' => [
                'required',
                'string',
                'max:20',
                Rule::unique('employees', 'keycard_id')->ignore($employee->id)->whereNull('deleted_at') // ✅ เพิ่มเงื่อนไข whereNull('deleted_at')
            ],
            'first_name_th' => 'required|string|max:100',
            'last_name_th' => 'required|string|max:100',
            'first_name_en' => 'required|string|max:100',
            'last_name_en' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'nickname' => 'nullable|string|max:50',
            'username' => [
                'required',
                'string',
                'max:100',
                Rule::unique('employees', 'username')->ignore($employee->id)->whereNull('deleted_at') // ✅ เพิ่มเงื่อนไข whereNull('deleted_at')
            ],
            'computer_password' => 'nullable|string|min:6',
            'copier_code' => 'nullable|string|max:10',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('employees', 'email')->ignore($employee->id)->whereNull('deleted_at') // ✅ เพิ่มเงื่อนไข whereNull('deleted_at')
            ],
            'email_password' => 'nullable|string|min:6',
            'express_username' => 'nullable|string|max:7',
            'express_password' => 'nullable|string|max:4',
            'department_id' => 'required|exists:departments,id',
            'position' => 'required|string|max:100',
            'role' => 'required|in:super_admin,it_admin,hr,manager,express,employee',
            'status' => 'required|in:active,inactive',
            'password' => 'nullable|string|min:6',
        ];

        $validated = $request->validate($rules);

        try {
            $validated['login_email'] = $validated['email'];

            if (!empty($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']);
            }

            if ($this->isDepartmentExpressEnabled($validated['department_id'])) {
                if (empty($validated['express_username'])) {
                    $validated['express_username'] = $this->generateExpressUsername(
                        $validated['first_name_en'], 
                        $validated['last_name_en']
                    );
                }
                
                if (empty($validated['express_password'])) {
                    $validated['express_password'] = $this->generateExpressPassword();
                }
            } else {
                $validated['express_username'] = null;
                $validated['express_password'] = null;
            }

            $employee->update($validated);

            \Log::info("Employee updated with phone (duplicates allowed): {$validated['phone']}");

            return redirect()->route('employees.index')
                ->with('success', 'อัปเดตข้อมูลพนักงานเรียบร้อยแล้ว: ' . $employee->first_name_th . ' ' . $employee->last_name_th . ' (เบอร์โทร: ' . $validated['phone'] . ' - ซ้ำได้)');

        } catch (\Exception $e) {
            \Log::error('Employee update failed: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'เกิดข้อผิดพลาดในการอัปเดตข้อมูล: ' . $e->getMessage());
        }
    }

    /**
     * ✅ FIXED: Remove the specified resource from storage.
     * เพิ่มตัวเลือกระหว่าง Soft Delete และ Force Delete
     */
    public function destroy(Employee $employee)
    {
        try {
            // ตรวจสอบสิทธิ์
            if (!auth()->user() || auth()->user()->role !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'เฉพาะ SuperAdmin เท่านั้นที่สามารถลบพนักงานได้'
                ], 403);
            }

            $employeeName = $employee->first_name_th . ' ' . $employee->last_name_th;
            $employeePhone = $employee->phone;
            
            // ✅ ใช้ Soft Delete (default behavior)
            $employee->delete();

            \Log::info("Employee soft deleted: {$employeeName} (Phone: {$employeePhone} - duplicates were allowed)");

            return response()->json([
                'success' => true,
                'message' => 'ลบข้อมูลพนักงานเรียบร้อยแล้ว: ' . $employeeName . ' (ย้ายไปถังขยะ)'
            ]);

        } catch (\Exception $e) {
            \Log::error('Employee deletion failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการลบข้อมูล'
            ], 500);
        }
    }

    /**
     * ✅ NEW: Force delete employee (ลบจริงๆ จากฐานข้อมูล)
     */
    public function forceDestroy(Employee $employee)
    {
        try {
            // ตรวจสอบสิทธิ์ (เฉพาะ Super Admin)
            if (!auth()->user() || auth()->user()->role !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'เฉพาะ SuperAdmin เท่านั้นที่สามารถลบข้อมูลถาวรได้'
                ], 403);
            }

            $employeeName = $employee->first_name_th . ' ' . $employee->last_name_th;
            $employeePhone = $employee->phone;
            
            // ✅ Force Delete (ลบจริงจากฐานข้อมูล)
            $employee->forceDelete();

            \Log::info("Employee force deleted: {$employeeName} (Phone: {$employeePhone} - permanently removed)");

            return response()->json([
                'success' => true,
                'message' => 'ลบข้อมูลพนักงานถาวรเรียบร้อยแล้ว: ' . $employeeName
            ]);

        } catch (\Exception $e) {
            \Log::error('Employee force deletion failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการลบข้อมูลถาวร'
            ], 500);
        }
    }

    /**
     * ✅ NEW: แสดงรายการพนักงานที่ถูก soft delete (ถังขยะ)
     */
    public function trash()
    {
        try {
            // ตรวจสอบสิทธิ์
            if (!auth()->user() || auth()->user()->role !== 'super_admin') {
                return redirect()->route('employees.index')
                    ->with('error', 'ไม่มีสิทธิ์เข้าถึงถังขยะ');
            }

            $trashedEmployees = Employee::onlyTrashed()->with('department')->orderBy('deleted_at', 'desc')->get();
            
            return view('employees.trash', compact('trashedEmployees'));

        } catch (\Exception $e) {
            return redirect()->route('employees.index')
                ->with('error', 'เกิดข้อผิดพลาดในการโหลดถังขยะ');
        }
    }

    /**
     * ✅ NEW: กู้คืนพนักงานจากถังขยะ
     */
    public function restore($id)
    {
        try {
            // ตรวจสอบสิทธิ์
            if (!auth()->user() || auth()->user()->role !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'ไม่มีสิทธิ์กู้คืนข้อมูล'
                ], 403);
            }

            $employee = Employee::onlyTrashed()->findOrFail($id);
            $employee->restore();

            \Log::info("Employee restored: {$employee->first_name_th} {$employee->last_name_th}");

            return response()->json([
                'success' => true,
                'message' => 'กู้คืนพนักงานเรียบร้อยแล้ว: ' . $employee->first_name_th . ' ' . $employee->last_name_th
            ]);

        } catch (\Exception $e) {
            \Log::error('Employee restoration failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการกู้คืนข้อมูล'
            ], 500);
        }
    }

    /**
     * ✅ NEW: ล้างถังขยะ (ลบทุกคนในถังขยะอย่างถาวร)
     */
    public function emptyTrash()
    {
        try {
            // ตรวจสอบสิทธิ์
            if (!auth()->user() || auth()->user()->role !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'ไม่มีสิทธิ์ล้างถังขยะ'
                ], 403);
            }

            $trashedCount = Employee::onlyTrashed()->count();
            Employee::onlyTrashed()->forceDelete();

            \Log::info("Trash emptied: {$trashedCount} employees permanently deleted");

            return response()->json([
                'success' => true,
                'message' => "ล้างถังขยะเรียบร้อยแล้ว: ลบ {$trashedCount} คนอย่างถาวร"
            ]);

        } catch (\Exception $e) {
            \Log::error('Empty trash failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการล้างถังขยะ'
            ], 500);
        }
    }

    /**
     * ✅ NEW: Bulk restore employees from trash
     */
    public function bulkRestore(Request $request)
    {
        $request->validate([
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:employees,id'
        ]);

        try {
            // ตรวจสอบสิทธิ์
            if (!auth()->user() || auth()->user()->role !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'ไม่มีสิทธิ์กู้คืนข้อมูล'
                ], 403);
            }

            $employeeIds = $request->employee_ids;
            $restoredEmployees = Employee::onlyTrashed()->whereIn('id', $employeeIds)->get();
            
            if ($restoredEmployees->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'ไม่พบพนักงานในถังขยะที่ต้องการกู้คืน'
                ], 404);
            }

            // Restore employees
            foreach ($restoredEmployees as $employee) {
                $employee->restore();
            }

            $count = $restoredEmployees->count();
            $names = $restoredEmployees->pluck('first_name_th')->take(3)->join(', ');
            if ($count > 3) {
                $names .= ' และอีก ' . ($count - 3) . ' คน';
            }

            \Log::info("Bulk restore completed: {$count} employees restored");

            return response()->json([
                'success' => true,
                'message' => "กู้คืนพนักงาน {$count} คนเรียบร้อยแล้ว: {$names}",
                'restored_count' => $count
            ]);

        } catch (\Exception $e) {
            \Log::error('Bulk restore failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการกู้คืนข้อมูล'
            ], 500);
        }
    }

    // =====================================================
    // EXPRESS METHODS v2.0 🚀
    // =====================================================

    /**
     * ✅ ตรวจสอบว่าแผนกนี้เปิดใช้งาน Express หรือไม่ (v2.0)
     */
    private function isDepartmentExpressEnabled($departmentId)
    {
        try {
            $department = Department::find($departmentId);
            if (!$department) return false;
            
            // ใช้ field express_enabled จากฐานข้อมูล
            return (bool) $department->express_enabled;
            
        } catch (\Exception $e) {
            \Log::error('Express eligibility check failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ปรับปรุง Express Username Generator
     */
    private function generateExpressUsername($firstName, $lastName)
    {
        // Clean and combine names
        $combined = preg_replace('/[^a-zA-Z]/', '', $firstName . $lastName);
        $username = strtolower($combined);
        
        // Ensure exactly 7 characters
        if (strlen($username) >= 7) {
            $username = substr($username, 0, 7);
        } else {
            $username = str_pad($username, 7, 'x'); // Pad with 'x' if too short
        }
        
        // Check for uniqueness (เฉพาะที่ไม่ได้ถูก soft delete)
        $counter = 1;
        $originalUsername = $username;
        
        while (Employee::withoutTrashed()->where('express_username', $username)->exists()) {
            if ($counter == 1) {
                $username = substr($originalUsername, 0, 6) . '1';
            } else {
                $username = substr($originalUsername, 0, 6) . $counter;
            }
            $counter++;
            
            // Prevent infinite loop
            if ($counter > 99) {
                $username = 'exp' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
                break;
            }
        }
        
        return $username;
    }

    /**
     * ปรับปรุง Express Password Generator
     */
    private function generateExpressPassword()
    {
        $letters = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        
        $password = '';
        
        // Add 1 number (guaranteed)
        $password .= $numbers[mt_rand(0, strlen($numbers) - 1)];
        
        // Add 3 letters
        for ($i = 0; $i < 3; $i++) {
            $password .= $letters[mt_rand(0, strlen($letters) - 1)];
        }
        
        // Shuffle the password to randomize position of number
        $passwordArray = str_split($password);
        shuffle($passwordArray);
        
        return implode('', $passwordArray);
    }

    // =====================================================
    // HELPER METHODS
    // =====================================================

    /**
     * Generate a random password
     */
    private function generatePassword($length = 10)
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*';
        $password = '';
        
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, strlen($chars) - 1)];
        }
        
        return $password;
    }

    /**
     * Generate a unique employee code
     */
    private function generateEmployeeCode()
    {
        do {
            $code = 'EMP' . str_pad(random_int(1, 999), 3, '0', STR_PAD_LEFT);
        } while (Employee::withoutTrashed()->where('employee_code', $code)->exists()); // ✅ เพิ่ม withoutTrashed()
        
        return $code;
    }

    /**
     * Generate a unique keycard ID
     */
    private function generateKeycardId()
    {
        do {
            $id = 'KC' . str_pad(random_int(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (Employee::withoutTrashed()->where('keycard_id', $id)->exists()); // ✅ เพิ่ม withoutTrashed()
        
        return $id;
    }

    /**
     * Generate a copier code
     */
    private function generateCopierCode()
    {
        return str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate data for AJAX requests
     */
    public function generateData(Request $request)
    {
        $type = $request->get('type');
        
        switch ($type) {
            case 'employee_code':
                return response()->json(['employee_code' => $this->generateEmployeeCode()]);
            
            case 'keycard_id':
                return response()->json(['keycard_id' => $this->generateKeycardId()]);
            
            case 'password':
                return response()->json(['password' => $this->generatePassword()]);
            
            case 'copier_code':
                return response()->json(['copier_code' => $this->generateCopierCode()]);
                
            case 'username':
                $firstName = $request->get('first_name_en', '');
                $lastName = $request->get('last_name_en', '');
                $username = strtolower($firstName . '.' . $lastName);
                $email = $username . '@bettersystem.co.th';
                return response()->json([
                    'username' => $username,
                    'email' => $email
                ]);
                
            case 'express_username':
                $firstName = $request->get('first_name_en', '');
                $lastName = $request->get('last_name_en', '');
                return response()->json(['express_username' => $this->generateExpressUsername($firstName, $lastName)]);
                
            case 'express_password':
                return response()->json(['express_password' => $this->generateExpressPassword()]);
            
            default:
                return response()->json(['error' => 'Invalid type'], 400);
        }
    }

    /**
     * Bulk actions for multiple employees
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:employees,id'
        ]);

        try {
            $employeeIds = $request->employee_ids;
            $action = $request->action;
            $count = count($employeeIds);

            switch ($action) {
                case 'activate':
                    Employee::withoutTrashed()->whereIn('id', $employeeIds)->update(['status' => 'active']); // ✅ เพิ่ม withoutTrashed()
                    $message = "เปิดใช้งานพนักงาน {$count} คนเรียบร้อยแล้ว";
                    break;
                
                case 'deactivate':
                    Employee::withoutTrashed()->whereIn('id', $employeeIds)->update(['status' => 'inactive']); // ✅ เพิ่ม withoutTrashed()
                    $message = "ปิดใช้งานพนักงาน {$count} คนเรียบร้อยแล้ว";
                    break;
                
                case 'delete':
                    Employee::withoutTrashed()->whereIn('id', $employeeIds)->delete(); // ✅ เพิ่ม withoutTrashed() (soft delete)
                    $message = "ย้ายพนักงาน {$count} คนไปถังขยะเรียบร้อยแล้ว";
                    break;
            }

            return response()->json(['success' => true, 'message' => $message]);

        } catch (\Exception $e) {
            \Log::error('Bulk action failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false, 
                'message' => 'เกิดข้อผิดพลาดในการดำเนินการ'
            ], 500);
        }
    }

    /**
     * Export employees to Excel
     */
    public function exportExcel()
    {
        try {
            // This would require a package like maatwebsite/excel
            // For now, return a simple response
            return redirect()->route('employees.index')
                ->with('info', 'ฟีเจอร์ส่งออก Excel จะพร้อมใช้งานเร็วๆ นี้');
        } catch (\Exception $e) {
            return redirect()->route('employees.index')
                ->with('error', 'เกิดข้อผิดพลาดในการส่งออกข้อมูล');
        }
    }

    /**
     * Reset employee password
     */
    public function resetPassword(Employee $employee)
    {
        try {
            // Check if user has permission
            $user = auth()->user();
            if (!in_array($user->role, ['super_admin', 'it_admin'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'ไม่มีสิทธิ์ในการรีเซ็ตรหัสผ่าน'
                ], 403);
            }

            // Generate new password
            $newPassword = $this->generatePassword();
            
            // Update employee password
            $employee->update([
                'password' => Hash::make($newPassword)
            ]);

            \Log::info("Password reset for employee: {$employee->first_name_th} {$employee->last_name_th} (Phone: {$employee->phone} - duplicates allowed)");

            return response()->json([
                'success' => true,
                'message' => 'รีเซ็ตรหัสผ่านสำเร็จ',
                'new_password' => $newPassword
            ]);

        } catch (\Exception $e) {
            \Log::error('Password reset failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการรีเซ็ตรหัสผ่าน'
            ], 500);
        }
    }
}
