<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Employee::with('department');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name_th', 'like', "%{$search}%")
                  ->orWhere('last_name_th', 'like', "%{$search}%")
                  ->orWhere('first_name_en', 'like', "%{$search}%")
                  ->orWhere('last_name_en', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('employee_code', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('nickname', 'like', "%{$search}%");
            });
        }

        // Department filter
        if ($request->filled('department')) {
            $query->where('department_id', $request->department);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Role filter
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Apply role-based restrictions for Express users
        if (auth()->user()->role === 'express') {
            $query->whereHas('department', function($q) {
                $q->where('name', 'บัญชี');
            });
        }

        // Order by created_at desc
        $query->orderBy('created_at', 'desc');

        // Paginate results
        $employees = $query->paginate(15);

        // Get departments for filter dropdown
        $departments = $this->getDepartments();
        
        return view('employees.index', compact('employees', 'departments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get departments - create if not exist
        $departments = $this->getDepartments();
        
        return view('employees.create', compact('departments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validation rules
            $rules = [
                'first_name_th' => 'required|string|max:255',
                'last_name_th' => 'required|string|max:255',
                'first_name_en' => 'required|string|max:255',
                'last_name_en' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'email' => 'required|email|unique:employees,email',
                'username' => 'nullable|string|max:255',
                'department_id' => 'required|exists:departments,id',
                'position' => 'required|string|max:255',
                'role' => 'required|in:employee,hr,manager,it_admin,super_admin,express',
                'status' => 'required|in:active,inactive',
            ];

            // Add password validation based on user role
            if (auth()->user()->role === 'super_admin' || auth()->user()->role === 'it_admin') {
                $rules['password'] = 'required|string|min:8';
            }

            $validated = $request->validate($rules);

            // Auto-generate missing fields
            $validated = $this->autoGenerateFields($validated, $request);

            // Create employee
            $employee = Employee::create($validated);

            return redirect()->route('employees.index')
                ->with('success', 'สร้างพนักงานใหม่สำเร็จ: ' . $employee->first_name_th . ' ' . $employee->last_name_th);

        } catch (\Exception $e) {
            return back()->withInput()
                ->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        return view('employees.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        $departments = $this->getDepartments();
        
        return view('employees.edit', compact('employee', 'departments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        try {
            $rules = [
                'first_name_th' => 'required|string|max:255',
                'last_name_th' => 'required|string|max:255',
                'first_name_en' => 'required|string|max:255',
                'last_name_en' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'email' => 'required|email|unique:employees,email,' . $employee->id,
                'username' => 'nullable|string|max:255',
                'department_id' => 'required|exists:departments,id',
                'position' => 'required|string|max:255',
                'role' => 'required|in:employee,hr,manager,it_admin,super_admin,express',
                'status' => 'required|in:active,inactive',
            ];

            if ($request->filled('password')) {
                $rules['password'] = 'string|min:8';
            }

            $validated = $request->validate($rules);

            if ($request->filled('password')) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']);
            }

            $employee->update($validated);

            return redirect()->route('employees.index')
                ->with('success', 'อัปเดตข้อมูลพนักงานสำเร็จ');

        } catch (\Exception $e) {
            return back()->withInput()
                ->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        try {
            $name = $employee->first_name_th . ' ' . $employee->last_name_th;
            $employee->delete();

            return redirect()->route('employees.index')
                ->with('success', 'ลบพนักงาน ' . $name . ' สำเร็จ');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'เกิดข้อผิดพลาดในการลบ: ' . $e->getMessage()]);
        }
    }

    /**
     * Generate data for auto-completion
     */
    public function generateData(Request $request)
    {
        $type = $request->get('type');
        $data = [];

        try {
            switch ($type) {
                case 'employee_code':
                    $data['employee_code'] = $this->generateEmployeeCode();
                    break;

                case 'keycard_id':
                    $data['keycard_id'] = $this->generateKeycardId();
                    break;

                case 'username':
                    $firstName = $request->get('first_name_en', '');
                    $lastName = $request->get('last_name_en', '');
                    $data['username'] = $this->generateUsername($firstName, $lastName);
                    break;

                case 'email':
                    $firstName = $request->get('first_name_en', '');
                    $lastName = $request->get('last_name_en', '');
                    $domain = $request->get('domain', 'bettersystem.co.th');
                    $data['email'] = $this->generateEmail($firstName, $lastName, $domain);
                    break;

                case 'password':
                    $data['password'] = $this->generatePassword();
                    break;

                case 'copier_code':
                    $data['copier_code'] = $this->generateCopierCode();
                    break;

                case 'express_username':
                    $firstName = $request->get('first_name_en', '');
                    $data['express_username'] = $this->generateExpressUsername($firstName);
                    break;

                case 'express_code':
                    $data['express_code'] = $this->generateExpressCode();
                    break;

                default:
                    return response()->json(['error' => 'Invalid type'], 400);
            }

            return response()->json($data);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get or create departments
     */
    private function getDepartments()
    {
        // Check if Department model exists
        if (!class_exists(Department::class)) {
            // Return collection of objects if Department model doesn't exist
            return collect([
                (object)['id' => '1', 'name' => 'บัญชี'],
                (object)['id' => '2', 'name' => 'IT'],
                (object)['id' => '3', 'name' => 'ฝ่ายขาย'],
                (object)['id' => '4', 'name' => 'การตลาด'],
                (object)['id' => '5', 'name' => 'บุคคล'],
                (object)['id' => '6', 'name' => 'ผลิต'],
                (object)['id' => '7', 'name' => 'คลังสินค้า'],
                (object)['id' => '8', 'name' => 'บริหาร'],
            ]);
        }

        // Try to get departments from database
        try {
            $departments = Department::all();
            
            // If no departments exist, create default ones
            if ($departments->isEmpty()) {
                $defaultDepartments = [
                    'บัญชี', 'IT', 'ฝ่ายขาย', 'การตลาด', 
                    'บุคคล', 'ผลิต', 'คลังสินค้า', 'บริหาร'
                ];

                foreach ($defaultDepartments as $name) {
                    Department::create(['name' => $name]);
                }

                $departments = Department::all();
            }

            return $departments;

        } catch (\Exception $e) {
            // Fallback to static collection if database error
            return collect([
                (object)['id' => '1', 'name' => 'บัญชี'],
                (object)['id' => '2', 'name' => 'IT'],
                (object)['id' => '3', 'name' => 'ฝ่ายขาย'],
                (object)['id' => '4', 'name' => 'การตลาด'],
                (object)['id' => '5', 'name' => 'บุคคล'],
                (object)['id' => '6', 'name' => 'ผลิต'],
                (object)['id' => '7', 'name' => 'คลังสินค้า'],
                (object)['id' => '8', 'name' => 'บริหาร'],
            ]);
        }
    }

    /**
     * Auto-generate missing fields
     */
    private function autoGenerateFields($validated, $request)
    {
        // Generate employee code if not provided
        if (empty($validated['employee_code'])) {
            $validated['employee_code'] = $this->generateEmployeeCode();
        }

        // Generate keycard ID if not provided
        if (empty($validated['keycard_id'])) {
            $validated['keycard_id'] = $this->generateKeycardId();
        }

        // Generate username if not provided
        if (empty($validated['username'])) {
            $validated['username'] = $this->generateUsername(
                $validated['first_name_en'], 
                $validated['last_name_en']
            );
        }

        // Generate email if not provided
        if (empty($validated['email'])) {
            $domain = $request->get('email_domain', 'bettersystem.co.th');
            $validated['email'] = $this->generateEmail(
                $validated['first_name_en'], 
                $validated['last_name_en'], 
                $domain
            );
        }

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
            // Use default password for other roles
            $validated['password'] = Hash::make('Bettersystem123');
        }

        // Generate copier code if not provided
        if (empty($validated['copier_code'])) {
            $validated['copier_code'] = $this->generateCopierCode();
        }

        // Generate Express fields if department is accounting
        $department = $this->getDepartments()->find($validated['department_id']);
        if ($department && $department->name === 'บัญชี') {
            if (empty($validated['express_username'])) {
                $validated['express_username'] = $this->generateExpressUsername($validated['first_name_en']);
            }
            if (empty($validated['express_code'])) {
                $validated['express_code'] = $this->generateExpressCode();
            }
        }

        return $validated;
    }

    /**
     * Generate employee code
     */
    private function generateEmployeeCode()
    {
        do {
            $code = 'EMP' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
        } while (Employee::where('employee_code', $code)->exists());

        return $code;
    }

    /**
     * Generate keycard ID
     */
    private function generateKeycardId()
    {
        do {
            $code = 'KEY' . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (Employee::where('keycard_id', $code)->exists());

        return $code;
    }

    /**
     * Generate username from English names
     */
    private function generateUsername($firstName, $lastName)
    {
        $firstName = strtolower(trim($firstName));
        $lastName = strtolower(trim($lastName));
        
        if ($firstName && $lastName) {
            return $firstName . '.' . $lastName;
        }
        
        return '';
    }

    /**
     * Generate email from English names
     */
    private function generateEmail($firstName, $lastName, $domain = 'bettersystem.co.th')
    {
        $firstName = strtolower(trim($firstName));
        $lastName = strtolower(trim($lastName));
        
        if ($firstName && $lastName) {
            return $firstName . '.' . substr($lastName, 0, 1) . '@' . $domain;
        }
        
        return '';
    }

    /**
     * Show credentials for the specified employee (Admin only).
     */
    public function showCredentials(Employee $employee)
    {
        // Check if user has permission to view passwords
        if (!auth()->user()->canViewPasswords()) {
            return response()->json(['success' => false, 'message' => 'ไม่มีสิทธิ์ดูข้อมูลนี้'], 403);
        }

        try {
            $html = view('employees.partials.credentials', compact('employee'))->render();
            
            return response()->json([
                'success' => true,
                'html' => $html
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Toggle employee status.
     */
    public function toggleStatus(Request $request, Employee $employee)
    {
        try {
            $newStatus = $request->input('status');
            
            if (!in_array($newStatus, ['active', 'inactive'])) {
                return response()->json(['success' => false, 'message' => 'สถานะไม่ถูกต้อง'], 400);
            }

            $employee->update(['status' => $newStatus]);

            return response()->json([
                'success' => true,
                'message' => 'เปลี่ยนสถานะสำเร็จ',
                'new_status' => $newStatus
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Export employees to Excel.
     */
    public function exportExcel(Request $request)
    {
        try {
            // For now, return a simple CSV format
            // You can implement proper Excel export using maatwebsite/excel later
            
            $query = Employee::with('department');

            // Apply same filters as index
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('first_name_th', 'like', "%{$search}%")
                      ->orWhere('last_name_th', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('employee_code', 'like', "%{$search}%");
                });
            }

            if ($request->filled('department')) {
                $query->where('department_id', $request->department);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('role')) {
                $query->where('role', $request->role);
            }

            $employees = $query->get();

            $filename = 'employees_' . date('Y-m-d_H-i-s') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($employees) {
                $file = fopen('php://output', 'w');
                
                // Add BOM for UTF-8
                fwrite($file, "\xEF\xBB\xBF");
                
                // CSV Headers
                fputcsv($file, [
                    'รหัสพนักงาน',
                    'ชื่อภาษาไทย',
                    'นามสกุลภาษาไทย',
                    'ชื่อภาษาอังกฤษ',
                    'นามสกุลภาษาอังกฤษ',
                    'อีเมล',
                    'เบอร์โทร',
                    'แผนก',
                    'ตำแหน่ง',
                    'สิทธิ์',
                    'สถานะ',
                    'Username',
                    'รหัสถ่ายเอกสาร'
                ]);

                foreach ($employees as $employee) {
                    fputcsv($file, [
                        $employee->employee_code,
                        $employee->first_name_th,
                        $employee->last_name_th,
                        $employee->first_name_en,
                        $employee->last_name_en,
                        $employee->email,
                        $employee->phone,
                        $employee->department_name,
                        $employee->position,
                        $employee->role_display,
                        $employee->status_display,
                        $employee->username,
                        $employee->copier_code
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'เกิดข้อผิดพลาดในการ Export: ' . $e->getMessage()]);
        }
    }

    /**
     * Export employees to PDF.
     */
    public function exportPdf(Request $request)
    {
        try {
            // For now, return a simple text format
            // You can implement proper PDF export using dompdf or tcpdf later
            
            $query = Employee::with('department');

            // Apply same filters as index
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('first_name_th', 'like', "%{$search}%")
                      ->orWhere('last_name_th', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('employee_code', 'like', "%{$search}%");
                });
            }

            if ($request->filled('department')) {
                $query->where('department_id', $request->department);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('role')) {
                $query->where('role', $request->role);
            }

            $employees = $query->get();

            // For now, return HTML that can be printed as PDF
            return view('employees.exports.pdf', compact('employees'));

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'เกิดข้อผิดพลาดในการ Export PDF: ' . $e->getMessage()]);
        }
    }

    /**
     * Reset employee password.
     */
    public function resetPassword(Request $request, Employee $employee)
    {
        if (!auth()->user()->canViewPasswords()) {
            return response()->json(['success' => false, 'message' => 'ไม่มีสิทธิ์ดำเนินการนี้'], 403);
        }

        try {
            $newPassword = $this->generatePassword();
            $employee->update(['password' => Hash::make($newPassword)]);

            return response()->json([
                'success' => true,
                'message' => 'รีเซ็ตรหัสผ่านสำเร็จ',
                'new_password' => $newPassword
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Generate copier code
     */
    private function generateCopierCode()
    {
        return str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate Express username (7 characters)
     */
    private function generateExpressUsername($firstName)
    {
        $firstName = strtolower(trim($firstName));
        
        if (strlen($firstName) >= 7) {
            return substr($firstName, 0, 7);
        } elseif (strlen($firstName) > 0) {
            return str_pad($firstName, 7, 'x');
        }
        
        return substr(Str::random(7), 0, 7);
    }

    /**
     * Generate Express code (4 digits)
     */
    private function generateExpressCode()
    {
        return str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    }
}