<?php

namespace App\Http\Requests;

use App\Models\Employee;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return in_array(auth()->user()->role ?? 'employee', ['super_admin', 'it_admin']);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $employeeId = $this->route('employee') ? $this->route('employee')->id : null;
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        $rules = [
            // ข้อมูลพื้นฐาน
            'employee_code' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[A-Z0-9]+$/',
                Rule::unique('employees')->ignore($employeeId)->whereNull('deleted_at')
            ],
            'keycard_id' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[A-Z0-9]+$/',
                Rule::unique('employees')->ignore($employeeId)->whereNull('deleted_at')
            ],
            'first_name_th' => ['required', 'string', 'max:100', 'regex:/^[ก-๙\s]+$/u'],
            'last_name_th' => ['required', 'string', 'max:100', 'regex:/^[ก-๙\s]+$/u'],
            'first_name_en' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s]+$/'],
            'last_name_en' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s]+$/'],
            'nickname' => ['nullable', 'string', 'max:50'],
            
            // ข้อมูลระบบคอมพิวเตอร์
            'username' => [
                'nullable',
                'string',
                'max:50',
                'regex:/^[a-z0-9._]+$/',
                Rule::unique('employees')->ignore($employeeId)->whereNull('deleted_at')
            ],
            'computer_password' => ['nullable', 'string', 'min:6', 'max:50'],
            'copier_code' => [
                'nullable',
                'string',
                'size:4',
                'regex:/^[0-9]{4}$/',
                Rule::unique('employees')->ignore($employeeId)->whereNull('deleted_at')
            ],
            
            // ข้อมูลระบบอีเมล
            'email' => [
                'nullable',
                'email',
                'max:100',
                Rule::unique('employees')->ignore($employeeId)->whereNull('deleted_at')
            ],
            'email_password' => ['nullable', 'string', 'min:6', 'max:50'],
            
            // ข้อมูลโปรแกรม Express
            'express_username' => [
                'nullable',
                'string',
                'size:7',
                'regex:/^[a-z0-9x]+$/',
                Rule::unique('employees')->ignore($employeeId)->whereNull('deleted_at')
            ],
            'express_code' => [
                'nullable',
                'string',
                'size:4',
                'regex:/^[0-9]{4}$/',
                Rule::unique('employees')->ignore($employeeId)->whereNull('deleted_at')
            ],
            
            // แผนกและสิทธิ์
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'can_print_color' => ['boolean'],
            'can_use_vpn' => ['boolean'],
            'status' => ['required', 'string', Rule::in(array_keys(Employee::getStatuses()))],
            'role' => ['required', 'string', Rule::in(array_keys(Employee::getRoles()))],
            
            // ระบบ Login
            'password' => [
                $isUpdate ? 'nullable' : 'required',
                'string',
                'min:6',
                'max:50'
            ],

            // ข้อมูลเก่า (รองรับ backward compatibility)
            'position' => ['nullable', 'string', 'max:100'],
            'hire_date' => ['nullable', 'date', 'before_or_equal:today'],
            'salary' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9-+().\s]+$/'],
            'address' => ['nullable', 'string', 'max:500'],
            'emergency_contact' => ['nullable', 'string', 'max:100'],
            'emergency_phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9-+().\s]+$/'],
            'notes' => ['nullable', 'string', 'max:1000']
        ];

        return $rules;
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            // ข้อมูลพื้นฐาน
            'employee_code.unique' => 'รหัสพนักงานนี้มีอยู่ในระบบแล้ว',
            'employee_code.regex' => 'รหัสพนักงานต้องเป็นตัวอักษรพิมพ์ใหญ่และตัวเลขเท่านั้น',
            'keycard_id.unique' => 'ID Keycard นี้มีอยู่ในระบบแล้ว',
            'keycard_id.regex' => 'ID Keycard ต้องเป็นตัวอักษรพิมพ์ใหญ่และตัวเลขเท่านั้น',
            'first_name_th.required' => 'กรุณากรอกชื่อภาษาไทย',
            'first_name_th.regex' => 'ชื่อภาษาไทยต้องเป็นตัวอักษรไทยเท่านั้น',
            'last_name_th.required' => 'กรุณากรอกนามสกุลภาษาไทย',
            'last_name_th.regex' => 'นามสกุลภาษาไทยต้องเป็นตัวอักษรไทยเท่านั้น',
            'first_name_en.required' => 'กรุณากรอกชื่อภาษาอังกฤษ',
            'first_name_en.regex' => 'ชื่อภาษาอังกฤษต้องเป็นตัวอักษรอังกฤษเท่านั้น',
            'last_name_en.required' => 'กรุณากรอกนามสกุลภาษาอังกฤษ',
            'last_name_en.regex' => 'นามสกุลภาษาอังกฤษต้องเป็นตัวอักษรอังกฤษเท่านั้น',
            
            // ข้อมูลระบบคอมพิวเตอร์
            'username.unique' => 'Username นี้มีอยู่ในระบบแล้ว',
            'username.regex' => 'Username ต้องเป็นตัวอักษรพิมพ์เล็ก ตัวเลข จุด และขีดเส้นใต้เท่านั้น',
            'computer_password.min' => 'Password คอมพิวเตอร์ต้องมีอย่างน้อย 6 ตัวอักษร',
            'copier_code.unique' => 'รหัสเครื่องถ่ายนี้มีอยู่ในระบบแล้ว',
            'copier_code.size' => 'รหัสเครื่องถ่ายต้องเป็นตัวเลข 4 หลัก',
            'copier_code.regex' => 'รหัสเครื่องถ่ายต้องเป็นตัวเลข 4 หลักเท่านั้น',
            
            // ข้อมูลระบบอีเมล
            'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
            'email.unique' => 'อีเมลนี้มีอยู่ในระบบแล้ว',
            'email_password.min' => 'Password อีเมลต้องมีอย่างน้อย 6 ตัวอักษร',
            
            // ข้อมูลโปรแกรม Express
            'express_username.unique' => 'Username Express นี้มีอยู่ในระบบแล้ว',
            'express_username.size' => 'Username Express ต้องเป็น 7 ตัวอักษร',
            'express_username.regex' => 'Username Express ต้องเป็นตัวอักษรพิมพ์เล็กและตัวเลขเท่านั้น',
            'express_code.unique' => 'รหัส Express นี้มีอยู่ในระบบแล้ว',
            'express_code.size' => 'รหัส Express ต้องเป็นตัวเลข 4 หลัก',
            'express_code.regex' => 'รหัส Express ต้องเป็นตัวเลข 4 หลักเท่านั้น',
            
            // แผนกและสิทธิ์
            'department_id.required' => 'กรุณาเลือกแผนก',
            'department_id.exists' => 'แผนกที่เลือกไม่มีอยู่ในระบบ',
            'status.required' => 'กรุณาเลือกสถานะ',
            'status.in' => 'สถานะไม่ถูกต้อง',
            'role.required' => 'กรุณาเลือกบทบาท',
            'role.in' => 'บทบาทไม่ถูกต้อง',
            
            // ระบบ Login
            'password.required' => 'กรุณากรอกรหัสผ่าน',
            'password.min' => 'รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร',
            
            // ข้อมูลเก่า
            'hire_date.before_or_equal' => 'วันที่เริ่มงานต้องไม่เกินวันปัจจุบัน',
            'salary.numeric' => 'เงินเดือนต้องเป็นตัวเลข',
            'salary.max' => 'เงินเดือนต้องไม่เกิน 999,999.99',
            'phone.regex' => 'รูปแบบเบอร์โทรศัพท์ไม่ถูกต้อง',
            'emergency_phone.regex' => 'รูปแบบเบอร์ติดต่อฉุกเฉินไม่ถูกต้อง',
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
            'nickname' => 'ชื่อเล่น',
            'username' => 'Username',
            'computer_password' => 'Password คอมพิวเตอร์',
            'copier_code' => 'รหัสเครื่องถ่าย',
            'email' => 'อีเมล',
            'email_password' => 'Password อีเมล',
            'express_username' => 'Username Express',
            'express_code' => 'รหัส Express',
            'department_id' => 'แผนก',
            'can_print_color' => 'สิทธิ์ปริ้นสี',
            'can_use_vpn' => 'สิทธิ์ใช้ VPN',
            'status' => 'สถานะ',
            'role' => 'บทบาท',
            'password' => 'รหัสผ่าน',
            'position' => 'ตำแหน่ง',
            'hire_date' => 'วันที่เริ่มงาน',
            'salary' => 'เงินเดือน',
            'phone' => 'เบอร์โทรศัพท์',
            'address' => 'ที่อยู่',
            'emergency_contact' => 'ผู้ติดต่อฉุกเฉิน',
            'emergency_phone' => 'เบอร์ติดต่อฉุกเฉิน',
            'notes' => 'หมายเหตุ'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // ทำความสะอาดข้อมูลก่อน validation
        $cleanData = [];

        // ทำความสะอาดข้อความ
        foreach (['first_name_th', 'last_name_th', 'first_name_en', 'last_name_en', 'nickname'] as $field) {
            if ($this->has($field)) {
                $cleanData[$field] = trim($this->input($field));
            }
        }

        // ทำความสะอาดข้อมูลระบบ
        foreach (['username', 'email', 'express_username'] as $field) {
            if ($this->has($field)) {
                $cleanData[$field] = strtolower(trim($this->input($field)));
            }
        }

        // ทำความสะอาดรหัส
        foreach (['employee_code', 'keycard_id'] as $field) {
            if ($this->has($field)) {
                $cleanData[$field] = strtoupper(trim($this->input($field)));
            }
        }

        // ทำความสะอาดเบอร์โทร
        foreach (['phone', 'emergency_phone'] as $field) {
            if ($this->has($field)) {
                $cleanData[$field] = preg_replace('/[^0-9-+().\s]/', '', $this->input($field));
            }
        }

        // ทำความสะอาดรหัสตัวเลข
        foreach (['copier_code', 'express_code'] as $field) {
            if ($this->has($field)) {
                $cleanData[$field] = preg_replace('/[^0-9]/', '', $this->input($field));
            }
        }

        // จัดการ checkbox
        $cleanData['can_print_color'] = $this->boolean('can_print_color');
        $cleanData['can_use_vpn'] = $this->boolean('can_use_vpn');

        $this->merge($cleanData);
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        if ($this->ajax()) {
            $response = response()->json([
                'success' => false,
                'message' => 'ข้อมูลไม่ถูกต้อง กรุณาตรวจสอบและลองใหม่',
                'errors' => $validator->errors()
            ], 422);

            throw new \Illuminate\Http\Exceptions\HttpResponseException($response);
        }

        parent::failedValidation($validator);
    }
}
