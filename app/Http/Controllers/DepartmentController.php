<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $departments = Department::withCount('employees')
                                   ->orderBy('created_at', 'desc')
                                   ->get();
            
            return view('departments.index', compact('departments'));
        } catch (\Exception $e) {
            return view('departments.index', [
                'departments' => collect()
            ])->with('error', 'เกิดข้อผิดพลาดในการโหลดข้อมูลแผนก');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('departments.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('departments', 'name')
            ],
            'code' => [
                'required',
                'string',
                'max:10',
                'alpha_dash',
                Rule::unique('departments', 'code')
            ],
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean'
        ];

        $messages = [
            'name.required' => 'ชื่อแผนกจำเป็นต้องกรอก',
            'name.unique' => 'ชื่อแผนกนี้มีอยู่แล้วในระบบ',
            'code.required' => 'รหัสแผนกจำเป็นต้องกรอก',
            'code.unique' => 'รหัสแผนกนี้มีอยู่แล้วในระบบ',
            'code.alpha_dash' => 'รหัสแผนกสามารถใช้ตัวอักษร ตัวเลข และ - _ เท่านั้น',
        ];

        $validated = $request->validate($rules, $messages);

        try {
            // Convert code to uppercase
            $validated['code'] = strtoupper($validated['code']);
            $validated['is_active'] = $request->has('is_active');

            $department = Department::create($validated);

            return redirect()->route('departments.index')
                ->with('success', 'เพิ่มแผนกใหม่เรียบร้อยแล้ว: ' . $department->name);

        } catch (\Exception $e) {
            \Log::error('Department creation failed: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Department $department)
    {
        try {
            $department->load(['employees' => function($query) {
                $query->orderBy('first_name_th');
            }]);
            
            return view('departments.show', compact('department'));
        } catch (\Exception $e) {
            return redirect()->route('departments.index')
                ->with('error', 'ไม่พบข้อมูลแผนก');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Department $department)
    {
        return view('departments.edit', compact('department'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Department $department)
    {
        $rules = [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('departments', 'name')->ignore($department->id)
            ],
            'code' => [
                'required',
                'string',
                'max:10',
                'alpha_dash',
                Rule::unique('departments', 'code')->ignore($department->id)
            ],
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean'
        ];

        $validated = $request->validate($rules);

        try {
            $validated['code'] = strtoupper($validated['code']);
            $validated['is_active'] = $request->has('is_active');

            $department->update($validated);

            return redirect()->route('departments.index')
                ->with('success', 'อัปเดตข้อมูลแผนกเรียบร้อยแล้ว: ' . $department->name);

        } catch (\Exception $e) {
            \Log::error('Department update failed: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'เกิดข้อผิดพลาดในการอัปเดตข้อมูล: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
    {
        try {
            // Check if department has employees
            if ($department->employees()->count() > 0) {
                return redirect()->route('departments.index')
                    ->with('error', 'ไม่สามารถลบแผนกที่มีพนักงานอยู่ได้ กรุณาย้ายพนักงานก่อน');
            }

            $departmentName = $department->name;
            $department->delete();

            return redirect()->route('departments.index')
                ->with('success', 'ลบแผนกเรียบร้อยแล้ว: ' . $departmentName);

        } catch (\Exception $e) {
            \Log::error('Department deletion failed: ' . $e->getMessage());
            
            return redirect()->route('departments.index')
                ->with('error', 'เกิดข้อผิดพลาดในการลบข้อมูล');
        }
    }

    /**
     * Toggle department status
     */
    public function toggleStatus(Department $department)
    {
        try {
            $department->update([
                'is_active' => !$department->is_active
            ]);

            $status = $department->is_active ? 'เปิดใช้งาน' : 'ปิดใช้งาน';
            
            return response()->json([
                'success' => true,
                'message' => $status . 'แผนก ' . $department->name . ' เรียบร้อยแล้ว',
                'is_active' => $department->is_active
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการเปลี่ยนสถานะ'
            ], 500);
        }
    }

    /**
     * Bulk actions for multiple departments
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'department_ids' => 'required|array',
            'department_ids.*' => 'exists:departments,id'
        ]);

        try {
            $departmentIds = $request->department_ids;
            $action = $request->action;
            $count = count($departmentIds);

            switch ($action) {
                case 'activate':
                    Department::whereIn('id', $departmentIds)->update(['is_active' => true]);
                    $message = "เปิดใช้งานแผนก {$count} แผนกเรียบร้อยแล้ว";
                    break;
                
                case 'deactivate':
                    Department::whereIn('id', $departmentIds)->update(['is_active' => false]);
                    $message = "ปิดใช้งานแผนก {$count} แผนกเรียบร้อยแล้ว";
                    break;
                
                case 'delete':
                    // Check if any department has employees
                    $departmentsWithEmployees = Department::whereIn('id', $departmentIds)
                                                        ->withCount('employees')
                                                        ->having('employees_count', '>', 0)
                                                        ->count();
                    
                    if ($departmentsWithEmployees > 0) {
                        return response()->json([
                            'success' => false,
                            'message' => 'ไม่สามารถลบแผนกที่มีพนักงานอยู่ได้'
                        ], 400);
                    }
                    
                    Department::whereIn('id', $departmentIds)->delete();
                    $message = "ลบแผนก {$count} แผนกเรียบร้อยแล้ว";
                    break;
            }

            return response()->json(['success' => true, 'message' => $message]);

        } catch (\Exception $e) {
            \Log::error('Department bulk action failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false, 
                'message' => 'เกิดข้อผิดพลาดในการดำเนินการ'
            ], 500);
        }
    }

    /**
     * Export departments to Excel
     */
    public function exportExcel()
    {
        try {
            // This would require a package like maatwebsite/excel
            // For now, return a simple response
            return redirect()->route('departments.index')
                ->with('info', 'ฟีเจอร์ส่งออก Excel จะพร้อมใช้งานเร็วๆ นี้');
        } catch (\Exception $e) {
            return redirect()->route('departments.index')
                ->with('error', 'เกิดข้อผิดพลาดในการส่งออกข้อมูล');
        }
    }

    /**
     * API: Get list of departments
     */
    public function apiList()
    {
        try {
            $departments = Department::select('id', 'name', 'code', 'is_active')
                                   ->orderBy('name')
                                   ->get();
            
            return response()->json([
                'success' => true,
                'data' => $departments
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการโหลดข้อมูล'
            ], 500);
        }
    }

    /**
     * Get departments for select options
     */
    public function getSelectOptions()
    {
        try {
            $departments = Department::where('is_active', true)
                                   ->select('id', 'name')
                                   ->orderBy('name')
                                   ->get();
            
            return response()->json([
                'success' => true,
                'departments' => $departments
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการโหลดข้อมูล'
            ], 500);
        }
    }

    /**
     * Generate department code
     */
    public function generateCode(Request $request)
    {
        try {
            $name = $request->get('name', '');
            
            if (empty($name)) {
                return response()->json(['code' => '']);
            }
            
            // Extract first letters from each word
            $words = explode(' ', $name);
            $code = '';
            
            foreach ($words as $word) {
                if (!empty($word)) {
                    $code .= strtoupper(substr($word, 0, 1));
                }
            }
            
            // If code is too short, pad with numbers
            if (strlen($code) < 3) {
                $code = strtoupper(substr($name, 0, 3));
            }
            
            // Check if code already exists and modify if needed
            $originalCode = $code;
            $counter = 1;
            
            while (Department::where('code', $code)->exists()) {
                $code = $originalCode . $counter;
                $counter++;
            }
            
            return response()->json(['code' => $code]);
        } catch (\Exception $e) {
            return response()->json(['code' => ''], 500);
        }
    }
}