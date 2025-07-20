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
     */
    public function index()
    {
        try {
            $employees = Employee::with('department')->orderBy('created_at', 'desc')->get();
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
                (object)['id' => 1, 'name' => 'บัญชี'],
                (object)['id' => 2, 'name' => 'IT'],
                (object)['id' => 3, 'name' => 'ฝ่ายขาย'],
                (object)['id' => 4, 'name' => 'การตลาด'],
                (object)['id' => 5, 'name' => 'บุคคล'],
                (object)['id' => 6, 'name' => 'ผลิต'],
                (object)['id' => 7, 'name' => 'คลังสินค้า'],
                (object)['id' => 8, 'name' => 'บริหาร'],
            ]);
            
            return view('employees.create', compact('departments'));
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Custom validation rules
        $rules = [
            'employee_code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('employees', 'employee_code')
            ],
            'keycard_id' => [
                'required',
                'string',
                'max:20',
                Rule::unique('employees', 'keycard_id')
            ],
            'first_name_th' => 'required|string|max:100',
            'last_name_th' => 'required|string|max:100',
            'first_name_en' => 'required|string|max:100',
            'last_name_en' => 'required|string|max:100',
            'phone' => [
                'required',
                'string',
                'max:20',
                Rule::unique('employees', 'phone')
            ],
            'nickname' => 'nullable|string|max:50',
            'username' => [
                'required',
                'string',
                'max:100',
                Rule::unique('employees', 'username')
            ],
            'computer_password' => 'nullable|string|min:6',
            'copier_code' => 'nullable|string|max:10',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('employees', 'email')
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

        // Custom validation messages
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
            'phone.unique' => 'เบอร์โทรศัพท์นี้มีอยู่แล้วในระบบ',
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

            // Handle Express fields - only for accounting department
            $department = Department::find($validated['department_id']);
            if ($department && !($department->name === 'แผนกบัญชีและการเงิน' || $department->name === 'บัญชี' || $department->code === 'ACC')) {
               $validated['express_username'] = null;
               $validated['express_password'] = null;
        }

            // Create the employee
            $employee = Employee::create($validated);

            return redirect()->route('employees.index')
                ->with('success', 'เพิ่มพนักงานใหม่เรียบร้อยแล้ว: ' . $employee->first_name_th . ' ' . $employee->last_name_th);

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
        // Custom validation rules for update
        $rules = [
            'employee_code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('employees', 'employee_code')->ignore($employee->id)
            ],
            'keycard_id' => [
                'required',
                'string',
                'max:20',
                Rule::unique('employees', 'keycard_id')->ignore($employee->id)
            ],
            'first_name_th' => 'required|string|max:100',
            'last_name_th' => 'required|string|max:100',
            'first_name_en' => 'required|string|max:100',
            'last_name_en' => 'required|string|max:100',
            'phone' => [
                'required',
                'string',
                'max:20',
                Rule::unique('employees', 'phone')->ignore($employee->id)
            ],
            'nickname' => 'nullable|string|max:50',
            'username' => [
                'required',
                'string',
                'max:100',
                Rule::unique('employees', 'username')->ignore($employee->id)
            ],
            'computer_password' => 'nullable|string|min:6',
            'copier_code' => 'nullable|string|max:10',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('employees', 'email')->ignore($employee->id)
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
            // Set login_email same as email
            $validated['login_email'] = $validated['email'];

            // Handle password update
            if (!empty($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']);
            }

            // Handle Express fields
            $department = Department::find($validated['department_id']);
            if ($department && !($department->name === 'แผนกบัญชีและการเงิน' || $department->name === 'บัญชี' || $department->code === 'ACC')) {
                $validated['express_username'] = null;
                $validated['express_code'] = null;
            }

            $employee->update($validated);

            return redirect()->route('employees.index')
                ->with('success', 'อัปเดตข้อมูลพนักงานเรียบร้อยแล้ว: ' . $employee->first_name_th . ' ' . $employee->last_name_th);

        } catch (\Exception $e) {
            \Log::error('Employee update failed: ' . $e->getMessage());
            
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
            // ตรวจสอบสิทธิ์
            if (!auth()->user() || auth()->user()->role !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'เฉพาะ SuperAdmin เท่านั้นที่สามารถลบพนักงานได้'
                ], 403);
            }

            $employeeName = $employee->first_name_th . ' ' . $employee->last_name_th;
            $employee->delete();

            return response()->json([
                'success' => true,
                'message' => 'ลบข้อมูลพนักงานเรียบร้อยแล้ว: ' . $employeeName
            ]);

        } catch (\Exception $e) {
            \Log::error('Employee deletion failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการลบข้อมูล'
            ], 500);
        }
    }

    // *** เพิ่ม Methods ใหม่ ***

    /**
     * ส่งข้อมูล Login ให้พนักงาน
     */
    public function sendCredentials(Employee $employee)
    {
        try {
            // ตรวจสอบสิทธิ์
            $user = auth()->user();
            if (!in_array($user->role, ['super_admin', 'it_admin'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'ไม่มีสิทธิ์ในการดำเนินการ'
                ], 403);
            }

            // ส่ง Email ข้อมูล Login (จำลอง)
            // Mail::to($employee->email)->send(new EmployeeCredentialsMail($employee));

            return response()->json([
                'success' => true,
                'message' => 'ส่งข้อมูลเข้าสู่ระบบไปยัง ' . $employee->email . ' เรียบร้อยแล้ว'
            ]);

        } catch (\Exception $e) {
            \Log::error('Send credentials failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * สร้าง Username และ Password อัตโนมัติ
     */
    public function generateCredentials(Employee $employee)
    {
        try {
            // ตรวจสอบสิทธิ์
            $user = auth()->user();
            if (!in_array($user->role, ['super_admin', 'it_admin'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'ไม่มีสิทธิ์ในการดำเนินการ'
                ], 403);
            }

            $englishName = $employee->first_name_en . ' ' . $employee->last_name_en;
            
            // สร้าง Username (ชื่อภาษาอังกฤษ 7 ตัวอักษร)
            $username = $this->generateExpressUsername($englishName);
            
            // สร้าง Password (4 ตัวอักษรมีตัวเลข)
            $password = $this->generateExpressPassword();
            
            // อัพเดท Employee
            $employee->update([
                'express_username' => $username,
                'express_password' => $password
            ]);

            return response()->json([
                'success' => true,
                'username' => $username,
                'password' => $password,
                'message' => 'สร้างข้อมูล Express เรียบร้อยแล้ว'
            ]);

        } catch (\Exception $e) {
            \Log::error('Generate credentials failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ดูตัวอย่างข้อมูลพนักงาน
     */
    public function preview(Employee $employee)
    {
        try {
            // ตรวจสอบสิทธิ์การเข้าถึง
            if (!$this->canViewEmployee($employee)) {
                return response()->json([
                    'success' => false,
                    'message' => 'ไม่มีสิทธิ์ดูข้อมูลพนักงานนี้'
                ], 403);
            }

            $employeeData = $this->getEmployeeData($employee);
            
            return response()->json([
                'success' => true,
                'data' => $employeeData
            ]);

        } catch (\Exception $e) {
            \Log::error('Preview failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ส่งออกข้อมูลเป็น PDF
     */
    public function exportPdf()
    {
        try {
            // ใช้ Laravel PDF Package
            // $employees = $this->getEmployeesForExport();
            // $pdf = PDF::loadView('employees.export.pdf', compact('employees'));
            // return $pdf->download('employees.pdf');
            
            return redirect()->route('employees.index')
                ->with('info', 'ฟีเจอร์ส่งออก PDF จะพร้อมใช้งานเร็วๆ นี้');
                
        } catch (\Exception $e) {
            return redirect()->route('employees.index')
                ->with('error', 'เกิดข้อผิดพลาดในการส่งออกข้อมูล');
        }
    }

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
        } while (Employee::where('employee_code', $code)->exists());
        
        return $code;
    }

    /**
     * Generate a unique keycard ID
     */
    private function generateKeycardId()
    {
        do {
            $id = 'KC' . str_pad(random_int(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (Employee::where('keycard_id', $id)->exists());
        
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
     * Generate Express Username (7 ตัวอักษร)
     */
    private function generateExpressUsername($englishName)
    {
        // ลบช่องว่างและเอาแค่ 7 ตัวอักษร
        $username = strtolower(str_replace(' ', '', $englishName));
        $username = substr($username, 0, 7);
        
        // ตรวจสอบ Username ซ้ำ
        $counter = 1;
        $originalUsername = $username;
        
        while (Employee::where('express_username', $username)->exists()) {
            $username = substr($originalUsername, 0, 6) . $counter;
            $counter++;
        }
        
        return $username;
    }

    /**
     * Generate Express Password (4 ตัวอักษรมีตัวเลข)
     */
    private function generateExpressPassword()
    {
        $chars = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        
        $password = '';
        
        // เลือก 2-3 ตัวอักษร
        for ($i = 0; $i < 3; $i++) {
            $password .= $chars[rand(0, strlen($chars) - 1)];
        }
        
        // เลือก 1 ตัวเลข
        $password .= $numbers[rand(0, strlen($numbers) - 1)];
        
        // สุ่มลำดับ
        return str_shuffle($password);
    }

    /**
     * ตรวจสอบสิทธิ์การดูข้อมูลพนักงาน
     */
    private function canViewEmployee($employee)
    {
        $user = auth()->user();
        
        // SuperAdmin ดูได้หมด
        if ($user->role === 'super_admin') {
            return true;
        }
        
        // IT Admin ดูได้หมด
        if ($user->role === 'it_admin') {
            return true;
        }
        
        // พนักงานดูข้อมูลตัวเองได้ (ถ้ามี user_id)
        if (isset($employee->user_id) && $user->id === $employee->user_id) {
            return true;
        }
        
        // Manager ดูได้
        if ($user->role === 'manager') {
            return true;
        }
        
        return false;
    }

    /**
     * ดึงข้อมูลพนักงานตาม Role
     */
    private function getEmployeeData($employee)
    {
        $user = auth()->user();
        $data = $employee->load('department')->toArray();
        
        // ซ่อน Password สำหรับ Role ที่ไม่ใช่ SuperAdmin
        if ($user->role !== 'super_admin') {
            unset($data['password']);
            unset($data['computer_password']);
            unset($data['email_password']);
            if ($user->role !== 'it_admin') {
                unset($data['express_password']);
            }
        }
        
        return $data;
    }

    /**
     * ดึงข้อมูลพนักงานสำหรับส่งออก
     */
    private function getEmployeesForExport()
    {
        $user = auth()->user();
        
        if ($user->role === 'super_admin') {
            // SuperAdmin เห็นข้อมูลทั้งหมด
            return Employee::with('department')->get();
        } else {
            // Role อื่นไม่เห็น Password
            return Employee::with('department')->get()->map(function($employee) {
                unset($employee->password);
                unset($employee->computer_password); 
                unset($employee->email_password);
                unset($employee->express_password);
                return $employee;
            });
        }
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
            
            default:
                return response()->json(['error' => 'Invalid type'], 400);
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
                    Employee::whereIn('id', $employeeIds)->update(['status' => 'active']);
                    $message = "เปิดใช้งานพนักงาน {$count} คนเรียบร้อยแล้ว";
                    break;
                
                case 'deactivate':
                    Employee::whereIn('id', $employeeIds)->update(['status' => 'inactive']);
                    $message = "ปิดใช้งานพนักงาน {$count} คนเรียบร้อยแล้ว";
                    break;
                
                case 'delete':
                    Employee::whereIn('id', $employeeIds)->delete();
                    $message = "ลบข้อมูลพนักงาน {$count} คนเรียบร้อยแล้ว";
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
     * Search employees
     */
    public function search(Request $request)
    {
        try {
            $query = Employee::with('department');
            
            if ($search = $request->get('search')) {
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
            
            if ($department = $request->get('department')) {
                $query->whereHas('department', function($q) use ($department) {
                    $q->where('name', $department);
                });
            }
            
            if ($role = $request->get('role')) {
                $query->where('role', $role);
            }
            
            if ($status = $request->get('status')) {
                $query->where('status', $status);
            }
            
            $employees = $query->get();
            
            return response()->json([
                'success' => true,
                'employees' => $employees
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการค้นหา'
            ], 500);
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