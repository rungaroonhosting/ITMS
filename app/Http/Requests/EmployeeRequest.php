<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Employee;

class EmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Basic authorization - can be enhanced based on your needs
        $user = auth()->user();
        
        if (!$user) {
            return false;
        }
        
        // Super admin and IT admin can always create/update
        if (in_array($user->role, ['super_admin', 'it_admin'])) {
            return true;
        }
        
        // HR can create/update employee and express roles
        if ($user->role === 'hr') {
            $targetRole = $this->input('role', 'employee');
            return in_array($targetRole, ['employee', 'express']);
        }
        
        // Manager can update employees in their department (for updates only)
        if ($user->role === 'manager' && $this->isMethod('PUT')) {
            $employee = $this->route('employee');
            return $employee && $user->department_id === $employee->department_id;
        }
        
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');
        $employeeId = null;
        
        if ($isUpdate) {
            $employee = $this->route('employee');
            $employeeId = $employee ? $employee->id : null;
        }
        
        return [
            // Basic Information
            'employee_code' => [
                'required',
                'string',
                'max:20',
                $employeeId ? Rule::unique('employees')->ignore($employeeId) : 'unique:employees'
            ],
            'keycard_id' => [
                'required',
                'string',
                'max:20',
                $employeeId ? Rule::unique('employees')->ignore($employeeId) : 'unique:employees'
            ],
            'first_name_th' => 'required|string|max:100',
            'last_name_th' => 'required|string|max:100',
            'first_name_en' => 'required|string|max:100|regex:/^[a-zA-Z\s]+$/',
            'last_name_en' => 'required|string|max:100|regex:/^[a-zA-Z\s]+$/',
            'phone' => 'required|string|max:20', // ✅ FIXED: ลบ unique constraint ออกแล้ว
            'nickname' => 'nullable|string|max:50',
            
            // Computer System
            'username' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-Z0-9._-]+$/',
                $employeeId ? Rule::unique('employees')->ignore($employeeId) : 'unique:employees'
            ],
            'computer_password' => 'nullable|string|min:6|max:50',
            'copier_code' => 'nullable|string|max:10|regex:/^[0-9]+$/',
            
            // Email System
            'email' => [
                'required',
                'email',
                'max:255',
                'regex:/^[a-zA-Z0-9._%+-]+@(bettersystem\.co\.th|better-groups\.com)$/',
                $employeeId ? Rule::unique('employees')->ignore($employeeId) : 'unique:employees'
            ],
            'email_password' => 'nullable|string|min:6|max:50',
            'login_password' => 'nullable|string|min:6|max:50', // This will be converted to 'password' field
            
            // Express Program
            'express_username' => [
                'nullable',
                'string',
                'min:1',
                'max:7',
                'regex:/^[a-zA-Z0-9]+$/',
                $employeeId ? Rule::unique('employees')->ignore($employeeId) : 'unique:employees'
            ],
            'express_password' => [
                'nullable',
                'string',
                'size:4',
                'regex:/^[0-9]{4}$/',
                function ($attribute, $value, $fail) use ($employeeId) {
                    if ($value) {
                        // Check if all digits are unique
                        $digits = str_split($value);
                        if (count($digits) !== count(array_unique($digits))) {
                            $fail('รหัสผ่าน Express ต้องเป็นตัวเลข 4 หลักที่ไม่ซ้ำกัน');
                        }
                        
                        // Check if password already exists (excluding current employee)
                        $query = Employee::where('express_password', $value)->whereNull('deleted_at');
                        if ($employeeId) {
                            $query->where('id', '!=', $employeeId);
                        }
                        if ($query->exists()) {
                            $fail('รหัสผ่าน Express นี้ถูกใช้งานแล้ว');
                        }
                    }
                }
            ],
            
            // ✅ FIXED: เพิ่ม Branch Validation
            'branch_id' => [
                'nullable',
                'integer',
                'exists:branches,id,is_active,1' // ตรวจสอบว่า branch ต้องเป็น active
            ],
            
            // Department and Role
            'department_id' => 'required|exists:departments,id',
            'position' => 'required|string|max:100',
            'role' => [
                'required',
                Rule::in(['super_admin', 'it_admin', 'hr', 'manager', 'express', 'employee'])
            ],
            'status' => [
                'required',
                Rule::in(['active', 'inactive'])
            ],
            
            // Additional Fields
            'hire_date' => 'nullable|date|before_or_equal:today',
            
            // ✅ FIXED: Permission Fields Validation
            'vpn_access' => 'nullable|boolean',
            'color_printing' => 'nullable|boolean',
            'remote_work' => 'nullable|boolean',
            'admin_access' => 'nullable|boolean',
            
            // ✅ NEW: Photo System Validation
            'photo' => [
                'nullable',
                'file',
                'image',
                'mimes:jpeg,jpg,png,gif',
                'max:2048', // 2MB limit
                'dimensions:min_width=50,min_height=50,max_width=2000,max_height=2000'
            ],
            'remove_photo' => 'nullable|boolean', // For removing existing photo during update
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            // Basic Information Messages
            'employee_code.required' => 'กรุณากรอกรหัสพนักงาน',
            'employee_code.unique' => 'รหัสพนักงานนี้ถูกใช้งานแล้ว',
            'keycard_id.required' => 'กรุณากรอก ID Keycard',
            'keycard_id.unique' => 'ID Keycard นี้ถูกใช้งานแล้ว',
            'first_name_th.required' => 'กรุณากรอกชื่อภาษาไทย',
            'last_name_th.required' => 'กรุณากรอกนามสกุลภาษาไทย',
            'first_name_en.required' => 'กรุณากรอกชื่อภาษาอังกฤษ',
            'first_name_en.regex' => 'ชื่อภาษาอังกฤษต้องเป็นตัวอักษร A-Z เท่านั้น',
            'last_name_en.required' => 'กรุณากรอกนามสกุลภาษาอังกฤษ',
            'last_name_en.regex' => 'นามสกุลภาษาอังกฤษต้องเป็นตัวอักษร A-Z เท่านั้น',  
            'phone.required' => 'กรุณากรอกเบอร์โทรศัพท์',
            
            // Computer System Messages
            'username.required' => 'กรุณากรอก Username',
            'username.unique' => 'Username นี้ถูกใช้งานแล้ว',
            'username.regex' => 'Username ต้องเป็นตัวอักษร ตัวเลข หรือเครื่องหมาย . _ - เท่านั้น',
            'computer_password.min' => 'รหัสผ่านคอมพิวเตอร์ต้องมีอย่างน้อย 6 ตัวอักษร',
            'copier_code.regex' => 'รหัสเครื่องถ่ายเอกสารต้องเป็นตัวเลขเท่านั้น',
            
            // Email System Messages
            'email.required' => 'กรุณากรอกอีเมล',
            'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
            'email.unique' => 'อีเมลนี้ถูกใช้งานแล้ว',
            'email.regex' => 'อีเมลต้องเป็น @bettersystem.co.th หรือ @better-groups.com เท่านั้น',
            'email_password.min' => 'รหัสผ่านอีเมลต้องมีอย่างน้อย 6 ตัวอักษร',
            'login_password.min' => 'รหัสผ่านเข้าระบบต้องมีอย่างน้อย 6 ตัวอักษร',
            
            // Express Program Messages
            'express_username.min' => 'Username Express ต้องมีอย่างน้อย 1 ตัวอักษร',
            'express_username.max' => 'Username Express ต้องไม่เกิน 7 ตัวอักษร',
            'express_username.regex' => 'Username Express ต้องเป็นตัวอักษรและตัวเลขเท่านั้น',
            'express_username.unique' => 'Username Express นี้ถูกใช้งานแล้ว',
            'express_password.size' => 'รหัสผ่าน Express ต้องเป็นตัวเลข 4 หลักเท่านั้น',
            'express_password.regex' => 'รหัสผ่าน Express ต้องเป็นตัวเลข 0-9 เท่านั้น',
            
            // ✅ NEW: Branch Messages
            'branch_id.integer' => 'รหัสสาขาต้องเป็นตัวเลข',
            'branch_id.exists' => 'สาขาที่เลือกไม่ถูกต้องหรือไม่เปิดให้บริการ',
            
            // Department and Role Messages
            'department_id.required' => 'กรุณาเลือกแผนกการทำงาน',
            'department_id.exists' => 'แผนกที่เลือกไม่ถูกต้อง',
            'position.required' => 'กรุณากรอกตำแหน่ง',
            'role.required' => 'กรุณาเลือกสิทธิ์การใช้งาน',
            'role.in' => 'สิทธิ์การใช้งานที่เลือกไม่ถูกต้อง',
            'status.required' => 'กรุณาเลือกสถานะการใช้งาน',
            'status.in' => 'สถานะการใช้งานที่เลือกไม่ถูกต้อง',
            
            // Additional Fields Messages
            'hire_date.date' => 'วันที่เข้าทำงานไม่ถูกต้อง',
            'hire_date.before_or_equal' => 'วันที่เข้าทำงานต้องไม่เกินวันปัจจุบัน',
            
            // Permission Fields Messages
            'vpn_access.boolean' => 'สิทธิ์ VPN ต้องเป็นค่า true หรือ false',
            'color_printing.boolean' => 'สิทธิ์การปริ้นสีต้องเป็นค่า true หรือ false',
            'remote_work.boolean' => 'สิทธิ์ทำงานจากที่บ้านต้องเป็นค่า true หรือ false',
            'admin_access.boolean' => 'สิทธิ์เข้าถึงแผงควบคุมต้องเป็นค่า true หรือ false',
            
            // ✅ NEW: Photo System Messages
            'photo.file' => 'รูปภาพต้องเป็นไฟล์',
            'photo.image' => 'ไฟล์ต้องเป็นรูปภาพ',
            'photo.mimes' => 'รูปภาพต้องเป็นไฟล์ประเภท JPEG, JPG, PNG หรือ GIF เท่านั้น',
            'photo.max' => 'ขนาดรูปภาพต้องไม่เกิน 2 MB',
            'photo.dimensions' => 'รูปภาพต้องมีขนาดอย่างน้อย 50x50 พิกเซล และไม่เกิน 2000x2000 พิกเซล',
            'remove_photo.boolean' => 'การลบรูปภาพต้องเป็นค่า true หรือ false',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'employee_code' => 'รหัสพนักงาน',
            'keycard_id' => 'ID Keycard',
            'first_name_th' => 'ชื่อภาษาไทย',
            'last_name_th' => 'นามสกุลภาษาไทย',
            'first_name_en' => 'ชื่อภาษาอังกฤษ',
            'last_name_en' => 'นามสกุลภาษาอังกฤษ',
            'phone' => 'เบอร์โทรศัพท์',
            'nickname' => 'ชื่อเล่น',
            'username' => 'Username',
            'computer_password' => 'รหัสผ่านคอมพิวเตอร์',
            'copier_code' => 'รหัสเครื่องถ่ายเอกสาร',
            'email' => 'อีเมล',
            'email_password' => 'รหัสผ่านอีเมล',
            'login_password' => 'รหัสผ่านเข้าระบบ',
            'express_username' => 'Username Express',
            'express_password' => 'รหัสผ่าน Express',
            'branch_id' => 'สาขา', // ✅ NEW: เพิ่ม branch attribute
            'department_id' => 'แผนกการทำงาน',
            'position' => 'ตำแหน่ง',
            'role' => 'สิทธิ์การใช้งาน',
            'status' => 'สถานะการใช้งาน',
            'hire_date' => 'วันที่เข้าทำงาน',
            'vpn_access' => 'สิทธิ์ VPN',
            'color_printing' => 'สิทธิ์การปริ้นสี',
            'remote_work' => 'สิทธิ์ทำงานจากที่บ้าน',
            'admin_access' => 'สิทธิ์เข้าถึงแผงควบคุม',
            'photo' => 'รูปภาพพนักงาน', // ✅ NEW: เพิ่ม photo attribute
            'remove_photo' => 'การลบรูปภาพ', // ✅ NEW: เพิ่ม remove_photo attribute
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // ✅ ENHANCED: Express username and password interdependency validation
            $this->validateExpressCredentials($validator);
            
            // ✅ NEW: Permission-based role validation
            $this->validatePermissionRoleConsistency($validator);
            
            // ✅ ENHANCED: Phone number format validation
            $this->validatePhoneFormat($validator);
            
            // ✅ NEW: Department Express compatibility validation
            $this->validateDepartmentExpressCompatibility($validator);
            
            // ✅ NEW: Branch validation
            $this->validateBranchConsistency($validator);
            
            // ✅ NEW: Photo system validation
            $this->validatePhotoSystem($validator);
        });
    }

    /**
     * ✅ ENHANCED: Validate Express credentials interdependency
     */
    private function validateExpressCredentials($validator)
    {
        $expressUsername = $this->input('express_username');
        $expressPassword = $this->input('express_password');
        $departmentId = $this->input('department_id');
        
        // Check if department supports Express
        if ($departmentId) {
            $department = \App\Models\Department::find($departmentId);
            $isExpressEnabled = $department && ($department->express_enabled ?? false);
            
            if ($isExpressEnabled) {
                // Express department - both username and password should be provided or both empty
                if (!empty($expressUsername) && empty($expressPassword)) {
                    $validator->errors()->add('express_password', 
                        'กรุณากรอกรหัสผ่าน Express เมื่อมี Username Express');
                }
                
                if (empty($expressUsername) && !empty($expressPassword)) {
                    $validator->errors()->add('express_username', 
                        'กรุณากรอก Username Express เมื่อมีรหัสผ่าน Express');
                }
            } else {
                // Non-Express department - should not have Express credentials
                if (!empty($expressUsername) || !empty($expressPassword)) {
                    $validator->errors()->add('express_username', 
                        'แผนกนี้ไม่รองรับ Express กรุณาเลือกแผนกที่รองรับ Express หรือเอาข้อมูล Express ออก');
                }
            }
        }
    }

    /**
     * ✅ NEW: Validate permission and role consistency
     */
    private function validatePermissionRoleConsistency($validator)
    {
        $role = $this->input('role');
        $adminAccess = $this->input('admin_access');
        $vpnAccess = $this->input('vpn_access');
        
        // Super admin and IT admin should have admin access
        if (in_array($role, ['super_admin', 'it_admin']) && !$adminAccess) {
            $validator->errors()->add('admin_access', 
                'Admin และ IT Admin ควรมีสิทธิ์เข้าถึงแผงควบคุม');
        }
        
        // Regular employees shouldn't have admin access
        if ($role === 'employee' && $adminAccess) {
            $validator->errors()->add('admin_access', 
                'พนักงานทั่วไปไม่ควรมีสิทธิ์เข้าถึงแผงควบคุม');
        }
        
        // Warning for regular employees with VPN access
        if ($role === 'employee' && $vpnAccess) {
            // This is just a warning, not an error
            // You can add this to a warnings array if you implement that feature
        }
    }

    /**
     * ✅ ENHANCED: Validate phone number format
     */
    private function validatePhoneFormat($validator)
    {
        $phone = $this->input('phone');
        
        if ($phone) {
            // Remove all non-digits
            $cleanPhone = preg_replace('/\D/', '', $phone);
            
            // Check if it's a valid length (8-15 digits)
            if (strlen($cleanPhone) < 8 || strlen($cleanPhone) > 15) {
                $validator->errors()->add('phone', 
                    'เบอร์โทรศัพท์ต้องมี 8-15 หลัก');
            }
            
            // Check for Thai mobile patterns (08x, 09x, 06x)
            if (strlen($cleanPhone) === 10) {
                $firstTwoDigits = substr($cleanPhone, 0, 2);
                if (!in_array($firstTwoDigits, ['08', '09', '06', '02'])) {
                    $validator->errors()->add('phone', 
                        'รูปแบบเบอร์โทรศัพท์ไม่ถูกต้อง (ควรขึ้นต้นด้วย 02, 06, 08, หรือ 09)');
                }
            }
        }
    }

    /**
     * ✅ NEW: Validate department Express compatibility
     */
    private function validateDepartmentExpressCompatibility($validator)
    {
        $departmentId = $this->input('department_id');
        $role = $this->input('role');
        
        if ($departmentId && $role === 'express') {
            $department = \App\Models\Department::find($departmentId);
            
            if (!$department || !($department->express_enabled ?? false)) {
                $validator->errors()->add('role', 
                    'ไม่สามารถกำหนดสิทธิ์ Express ให้กับแผนกที่ไม่รองรับ Express ได้');
            }
        }
    }

    /**
     * ✅ NEW: Validate branch consistency
     */
    private function validateBranchConsistency($validator)
    {
        $branchId = $this->input('branch_id');
        
        if ($branchId) {
            // Check if branch exists and is active
            $branch = \App\Models\Branch::where('id', $branchId)
                                      ->where('is_active', true)
                                      ->first();
            
            if (!$branch) {
                $validator->errors()->add('branch_id', 
                    'สาขาที่เลือกไม่มีอยู่ในระบบหรือไม่เปิดให้บริการ');
            }
        }
        
        // Note: branch_id is nullable, so empty value is allowed
    }

    /**
     * ✅ NEW: Validate photo system consistency
     */
    private function validatePhotoSystem($validator)
    {
        $photo = $this->file('photo');
        $removePhoto = $this->input('remove_photo');
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');
        
        // Can't upload and remove photo at the same time
        if ($photo && $removePhoto) {
            $validator->errors()->add('photo', 
                'ไม่สามารถอัปโหลดและลบรูปภาพพร้อมกันได้');
        }
        
        // Additional photo security checks
        if ($photo && $photo->isValid()) {
            // Check file size manually (additional check)
            if ($photo->getSize() > 2048 * 1024) { // 2MB in bytes
                $validator->errors()->add('photo', 
                    'ขนาดไฟล์รูปภาพเกิน 2 MB');
            }
            
            // Check if it's really an image by trying to get image info
            $imageInfo = @getimagesize($photo->getPathname());
            if ($imageInfo === false) {
                $validator->errors()->add('photo', 
                    'ไฟล์ที่อัปโหลดไม่ใช่รูปภาพที่ถูกต้อง');
            }
            
            // Check MIME type for additional security
            $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (!in_array($photo->getMimeType(), $allowedMimes)) {
                $validator->errors()->add('photo', 
                    'ประเภทไฟล์รูปภาพไม่ถูกต้อง (อนุญาตเฉพาะ JPEG, PNG, GIF)');
            }
            
            // Check for reasonable dimensions
            if ($imageInfo !== false) {
                $width = $imageInfo[0];
                $height = $imageInfo[1];
                
                if ($width < 50 || $height < 50) {
                    $validator->errors()->add('photo', 
                        'รูปภาพมีขนาดเล็กเกินไป (ต้องมีขนาดอย่างน้อย 50x50 พิกเซล)');
                }
                
                if ($width > 2000 || $height > 2000) {
                    $validator->errors()->add('photo', 
                        'รูปภาพมีขนาดใหญ่เกินไป (ต้องไม่เกิน 2000x2000 พิกเซล)');
                }
                
                // Check aspect ratio for profile photos (optional - can be removed if not needed)
                $aspectRatio = $width / $height;
                if ($aspectRatio < 0.5 || $aspectRatio > 2.0) {
                    // This is just a warning, not blocking validation
                    // $validator->errors()->add('photo', 'รูปภาพควรมีสัดส่วนที่เหมาะสมสำหรับรูปโปรไฟล์');
                }
            }
            
            // Check file extension
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            $extension = strtolower($photo->getClientOriginalExtension());
            if (!in_array($extension, $allowedExtensions)) {
                $validator->errors()->add('photo', 
                    'นามสกุลไฟล์ไม่ถูกต้อง (อนุญาตเฉพาะ .jpg, .jpeg, .png, .gif)');
            }
        }
        
        // Validate remove_photo flag for updates only
        if ($removePhoto && !$isUpdate) {
            $validator->errors()->add('remove_photo', 
                'ไม่สามารถลบรูปภาพเมื่อสร้างพนักงานใหม่');
        }
    }

    /**
     * ✅ NEW: Get validation rules for quick validation
     */
    public static function getQuickRules($employeeId = null): array 
    {
        return [
            'branch_id' => 'nullable|integer|exists:branches,id,is_active,1',
            'vpn_access' => 'nullable|boolean',
            'color_printing' => 'nullable|boolean',
            'remote_work' => 'nullable|boolean',
            'admin_access' => 'nullable|boolean',
            'photo' => 'nullable|file|image|mimes:jpeg,jpg,png,gif|max:2048|dimensions:min_width=50,min_height=50,max_width=2000,max_height=2000',
            'remove_photo' => 'nullable|boolean',
        ];
    }

    /**
     * ✅ NEW: Get photo-specific validation rules
     */
    public static function getPhotoRules(): array
    {
        return [
            'photo' => [
                'required',
                'file',
                'image',
                'mimes:jpeg,jpg,png,gif',
                'max:2048', // 2MB
                'dimensions:min_width=50,min_height=50,max_width=2000,max_height=2000'
            ]
        ];
    }

    /**
     * ✅ NEW: Get photo validation messages
     */
    public static function getPhotoMessages(): array
    {
        return [
            'photo.required' => 'กรุณาเลือกไฟล์รูปภาพ',
            'photo.file' => 'รูปภาพต้องเป็นไฟล์',
            'photo.image' => 'ไฟล์ต้องเป็นรูปภาพ',
            'photo.mimes' => 'รูปภาพต้องเป็นไฟล์ประเภท JPEG, JPG, PNG หรือ GIF เท่านั้น',
            'photo.max' => 'ขนาดรูปภาพต้องไม่เกิน 2 MB',
            'photo.dimensions' => 'รูปภาพต้องมีขนาดอย่างน้อย 50x50 พิกเซล และไม่เกิน 2000x2000 พิกเซล',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        // Log validation failures for debugging (excluding sensitive data)
        \Log::warning('Employee validation failed', [
            'errors' => $validator->errors()->toArray(),
            'input' => $this->getSafeData(),
            'has_photo' => $this->hasFile('photo'),
            'photo_size' => $this->hasFile('photo') ? $this->file('photo')->getSize() : null,
            'photo_mime' => $this->hasFile('photo') ? $this->file('photo')->getMimeType() : null,
        ]);
        
        parent::failedValidation($validator);
    }

    /**
     * ✅ ENHANCED: Get sanitized data for logging/debugging (including photo info)
     */
    public function getSafeData(): array
    {
        $data = $this->except([
            'password', 
            'computer_password', 
            'email_password', 
            'login_password', 
            'express_password',
            'remember_token',
            'photo' // Don't log actual photo file
        ]);
        
        // Add photo metadata if present
        if ($this->hasFile('photo')) {
            $photo = $this->file('photo');
            $data['photo_info'] = [
                'has_photo' => true,
                'original_name' => $photo->getClientOriginalName(),
                'size' => $photo->getSize(),
                'mime_type' => $photo->getMimeType(),
                'extension' => $photo->getClientOriginalExtension(),
            ];
        } else {
            $data['photo_info'] = ['has_photo' => false];
        }
        
        return $data;
    }

    /**
     * ✅ NEW: Check if photo is being uploaded
     */
    public function hasPhotoUpload(): bool
    {
        return $this->hasFile('photo') && $this->file('photo')->isValid();
    }

    /**
     * ✅ NEW: Check if photo removal is requested
     */
    public function isPhotoRemovalRequested(): bool
    {
        return $this->boolean('remove_photo');
    }

    /**
     * ✅ NEW: Get photo file if valid
     */
    public function getPhotoFile()
    {
        if ($this->hasPhotoUpload()) {
            return $this->file('photo');
        }
        return null;
    }
}
