<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class BranchController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        // Role middleware is applied in routes/web.php
    }

    /**
     * Display a listing of branches
     */
    public function index(Request $request)
    {
        try {
            $query = Branch::with(['manager', 'employees']);

            // Search functionality
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('code', 'LIKE', "%{$search}%")
                      ->orWhere('address', 'LIKE', "%{$search}%");
                });
            }

            // Status filter
            if ($request->has('status') && $request->status !== '') {
                $query->where('is_active', $request->status);
            }

            $branches = $query->orderBy('name')->paginate(10);

            return view('branches.index', compact('branches'));

        } catch (\Exception $e) {
            Log::error('Branch Index Error: ' . $e->getMessage());
            return redirect()->route('dashboard')
                ->with('error', 'เกิดข้อผิดพลาดในการโหลดข้อมูลสาขา');
        }
    }

    /**
     * Show the form for creating a new branch
     */
    public function create()
    {
        try {
            // Get employees who can be managers
            $availableManagers = Employee::whereNull('branch_id')
                ->where('status', 'active')
                ->whereIn('role', ['manager', 'super_admin', 'it_admin'])
                ->orderBy('first_name_th')
                ->orderBy('last_name_th')
                ->get();

            return view('branches.create', compact('availableManagers'));

        } catch (\Exception $e) {
            Log::error('Branch Create Form Error: ' . $e->getMessage());
            return redirect()->route('branches.index')
                ->with('error', 'เกิดข้อผิดพลาดในการโหลดฟอร์มสร้างสาขา');
        }
    }

    /**
     * Store a newly created branch
     */
    public function store(Request $request)
    {
        Log::info('=== Branch Store Debug ===');
        Log::info('Request Data:', $request->all());

        try {
            // Validation
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:10|unique:branches,code',
                'description' => 'nullable|string',
                'address' => 'nullable|string',
                'phone' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'manager_id' => 'nullable|exists:employees,id',
                'is_active' => 'nullable',
                'capacity' => 'nullable|integer|min:1|max:1000',
                'area_sqm' => 'nullable|numeric|min:0|max:99999.99',
                'opening_date' => 'nullable|date|before_or_equal:today',
            ]);

            Log::info('Validation Passed:', $validated);

            // Handle checkbox - if not checked, it won't be in request
            $validated['is_active'] = $request->has('is_active');

            // Create branch
            $branch = Branch::create($validated);

            Log::info('Branch Created:', $branch->toArray());

            // Update manager's branch_id if manager is assigned
            if ($request->manager_id) {
                Employee::where('id', $request->manager_id)->update([
                    'branch_id' => $branch->id
                ]);
                Log::info('Manager Updated:', ['manager_id' => $request->manager_id, 'branch_id' => $branch->id]);
            }

            return redirect()->route('branches.index')
                ->with('success', 'สร้างสาขาใหม่เรียบร้อยแล้ว');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation Error:', $e->errors());
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Branch Store Error:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified branch
     */
    public function show(Branch $branch)
    {
        try {
            $branch->load(['manager', 'employees']);
            
            // Get branch statistics
            $statistics = [
                'total_employees' => $branch->employees()->count(),
                'active_employees' => $branch->employees()->where('status', 'active')->count(),
                'departments' => $branch->employees()->with('department')->get()->pluck('department.name')->unique()->filter()->values(),
                'capacity_info' => $this->getCapacityInfo($branch),
            ];

            return view('branches.show', compact('branch', 'statistics'));

        } catch (\Exception $e) {
            Log::error('Branch Show Error: ' . $e->getMessage());
            return redirect()->route('branches.index')
                ->with('error', 'เกิดข้อผิดพลาดในการโหลดข้อมูลสาขา');
        }
    }

    /**
     * Show the form for editing the specified branch
     */
    public function edit(Branch $branch)
    {
        try {
            // Get available managers (including current manager)
            $availableManagers = Employee::where(function($query) use ($branch) {
                $query->whereNull('branch_id')
                      ->orWhere('id', $branch->manager_id);
            })
            ->where('status', 'active')
            ->whereIn('role', ['manager', 'super_admin', 'it_admin'])
            ->orderBy('first_name_th')
            ->orderBy('last_name_th')
            ->get();

            return view('branches.edit', compact('branch', 'availableManagers'));

        } catch (\Exception $e) {
            Log::error('Branch Edit Form Error: ' . $e->getMessage());
            return redirect()->route('branches.index')
                ->with('error', 'เกิดข้อผิดพลาดในการโหลดฟอร์มแก้ไขสาขา');
        }
    }

    /**
     * Update the specified branch
     */
    public function update(Request $request, Branch $branch)
    {
        Log::info('=== Branch Update Debug ===');
        Log::info('Request Data:', $request->all());

        try {
            // Validation
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:10|unique:branches,code,' . $branch->id,
                'description' => 'nullable|string',
                'address' => 'nullable|string',
                'phone' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'manager_id' => 'nullable|exists:employees,id',
                'is_active' => 'nullable',
                'capacity' => 'nullable|integer|min:1|max:1000',
                'area_sqm' => 'nullable|numeric|min:0|max:99999.99',
                'opening_date' => 'nullable|date|before_or_equal:today',
            ]);

            // Handle checkbox
            $validated['is_active'] = $request->has('is_active');

            // Remove branch_id from old manager
            if ($branch->manager_id && $branch->manager_id != $request->manager_id) {
                Employee::where('id', $branch->manager_id)->update([
                    'branch_id' => null
                ]);
            }

            // Update branch
            $branch->update($validated);

            // Set branch_id for new manager
            if ($request->manager_id) {
                Employee::where('id', $request->manager_id)->update([
                    'branch_id' => $branch->id
                ]);
            }

            return redirect()->route('branches.index')
                ->with('success', 'อัพเดตข้อมูลสาขาเรียบร้อยแล้ว');

        } catch (\Exception $e) {
            Log::error('Branch Update Error:', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified branch
     */
    public function destroy(Branch $branch)
    {
        try {
            // Check if branch has employees
            if ($branch->employees()->count() > 0) {
                return redirect()->route('branches.index')
                    ->with('error', 'ไม่สามารถลบสาขาที่มีพนักงานอยู่ได้');
            }

            // Remove branch_id from manager
            if ($branch->manager_id) {
                Employee::where('id', $branch->manager_id)->update([
                    'branch_id' => null
                ]);
            }

            $branchName = $branch->name;
            $branch->delete();

            return redirect()->route('branches.index')
                ->with('success', "ลบสาขา {$branchName} เรียบร้อยแล้ว");

        } catch (\Exception $e) {
            Log::error('Branch Delete Error:', ['error' => $e->getMessage()]);
            return redirect()->route('branches.index')
                ->with('error', 'เกิดข้อผิดพลาดในการลบสาขา: ' . $e->getMessage());
        }
    }

    /**
     * ✅ FIXED: Toggle branch active status - ตรงกับ route ใน view
     */
    public function toggleStatus(Branch $branch)
    {
        try {
            $oldStatus = $branch->is_active;
            $branch->update(['is_active' => !$branch->is_active]);
            
            $status = $branch->is_active ? 'เปิดใช้งาน' : 'ปิดใช้งาน';
            
            Log::info('Branch Toggle Status', [
                'branch_id' => $branch->id,
                'branch_name' => $branch->name,
                'old_status' => $oldStatus,
                'new_status' => $branch->is_active,
                'user_id' => Auth::id()
            ]);
            
            return redirect()->back()
                ->with('success', "เปลี่ยนสถานะสาขา {$branch->name} เป็น {$status} เรียบร้อยแล้ว");

        } catch (\Exception $e) {
            Log::error('Branch Toggle Status Error:', [
                'branch_id' => $branch->id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()
                ->with('error', 'เกิดข้อผิดพลาดในการเปลี่ยนสถานะสาขา');
        }
    }

    /**
     * Get branch data for AJAX
     */
    public function getData(Request $request)
    {
        try {
            $query = Branch::with(['manager', 'employees']);

            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('code', 'LIKE', "%{$search}%");
                });
            }

            $branches = $query->get();

            return response()->json([
                'success' => true,
                'data' => $branches
            ]);

        } catch (\Exception $e) {
            Log::error('Branch AJAX Data Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการโหลดข้อมูล'
            ], 500);
        }
    }

    // ========================================
    // EXPORT METHODS
    // ========================================

    /**
     * Export branches to Excel (Default export route)
     */
    public function exportExcel(Request $request)
    {
        $user = Auth::user();
        
        // Check permissions
        if (!$this->canExportBranches($user)) {
            return redirect()->route('branches.index')
                ->with('error', 'ไม่มีสิทธิ์ส่งออกข้อมูลสาขา');
        }

        try {
            $query = Branch::with(['manager', 'employees']);
            
            // Apply filters if provided
            if ($request->filled('status')) {
                $query->where('is_active', $request->status);
            }
            
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('code', 'LIKE', "%{$search}%")
                      ->orWhere('address', 'LIKE', "%{$search}%");
                });
            }
            
            $branches = $query->get();
            
            // Format data for export
            $data = $branches->map(function ($branch) {
                return [
                    'รหัสสาขา' => $branch->code,
                    'ชื่อสาขา' => $branch->name,
                    'ที่อยู่' => $branch->address ?? '-',
                    'เบอร์โทร' => $branch->phone ?? '-',
                    'อีเมล' => $branch->email ?? '-',
                    'จำนวนพนักงาน' => $branch->employees->count(),
                    'จำนวนสูงสุด' => $branch->capacity ?? '-',
                    'ใช้กำลังการผลิต' => $this->getCapacityUsage($branch),
                    'ผู้จัดการ' => $branch->manager ? $branch->manager->full_name_th ?? $branch->manager->name : '-',
                    'สถานะ' => $branch->is_active ? 'เปิดใช้งาน' : 'ปิดชั่วคราว',
                    'พื้นที่' => $branch->area_sqm ? number_format($branch->area_sqm, 2) . ' ตร.ม.' : '-',
                    'คำอธิบาย' => $branch->description ?? '-',
                    'วันที่เปิด' => $branch->opening_date ? $branch->opening_date->format('d/m/Y') : '-',
                    'วันที่สร้าง' => $branch->created_at->format('d/m/Y H:i'),
                ];
            });

            Log::info('🏢 Branch Export Excel', [
                'exported_by' => $user->id,
                'exported_by_name' => $user->full_name_th ?? $user->name,
                'total_records' => $data->count(),
                'filters' => $request->only(['status', 'search'])
            ]);

            // For now, return JSON. In a real implementation, you'd use Laravel Excel
            return response()->json([
                'success' => true,
                'message' => 'ข้อมูลสาขาสำหรับส่งออก Excel',
                'data' => $data,
                'total' => $data->count(),
                'export_url' => 'data:application/json;charset=utf-8,' . urlencode(json_encode($data, JSON_UNESCAPED_UNICODE))
            ]);

        } catch (\Exception $e) {
            Log::error('Branch export Excel failed: ' . $e->getMessage());
            return back()->with('error', 'เกิดข้อผิดพลาดในการส่งออกข้อมูล Excel: ' . $e->getMessage());
        }
    }

    /**
     * Export branches to PDF
     */
    public function exportPdf(Request $request)
    {
        $user = Auth::user();
        
        if (!$this->canExportBranches($user)) {
            return redirect()->route('branches.index')
                ->with('error', 'ไม่มีสิทธิ์ส่งออกข้อมูลสาขา');
        }

        try {
            $branches = Branch::with(['manager', 'employees'])->get();
            
            Log::info('🏢 Branch Export PDF', [
                'exported_by' => $user->id,
                'total_records' => $branches->count()
            ]);

            // TODO: Implement PDF export using DomPDF or similar
            return back()->with('info', 'การส่งออก PDF กำลังพัฒนา - ใช้ Excel ชั่วคราว');

        } catch (\Exception $e) {
            Log::error('Branch export PDF failed: ' . $e->getMessage());
            return back()->with('error', 'เกิดข้อผิดพลาดในการส่งออกข้อมูล PDF');
        }
    }

    /**
     * Export branches to CSV
     */
    public function exportCsv(Request $request)
    {
        $user = Auth::user();
        
        if (!$this->canExportBranches($user)) {
            return redirect()->route('branches.index')
                ->with('error', 'ไม่มีสิทธิ์ส่งออกข้อมูลสาขา');
        }

        try {
            $branches = Branch::with(['manager', 'employees'])->get();
            
            $filename = 'branches_export_' . now()->format('Y-m-d_H-i-s') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];

            Log::info('🏢 Branch Export CSV', [
                'exported_by' => $user->id,
                'filename' => $filename,
                'total_records' => $branches->count()
            ]);

            $callback = function() use ($branches) {
                $file = fopen('php://output', 'w');
                
                // Add BOM for UTF-8
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                
                // CSV Header
                fputcsv($file, [
                    'รหัสสาขา',
                    'ชื่อสาขา', 
                    'ที่อยู่',
                    'เบอร์โทร',
                    'อีเมล',
                    'จำนวนพนักงาน',
                    'จำนวนสูงสุด',
                    'ใช้กำลังการผลิต(%)',
                    'ผู้จัดการ',
                    'สถานะ',
                    'พื้นที่',
                    'คำอธิบาย',
                    'วันที่เปิด',
                    'วันที่สร้าง'
                ]);

                // CSV Data
                foreach ($branches as $branch) {
                    fputcsv($file, [
                        $branch->code,
                        $branch->name,
                        $branch->address ?? '',
                        $branch->phone ?? '',
                        $branch->email ?? '',
                        $branch->employees->count(),
                        $branch->capacity ?? '',
                        $this->getCapacityUsage($branch),
                        $branch->manager ? ($branch->manager->full_name_th ?? $branch->manager->name) : '',
                        $branch->is_active ? 'เปิดใช้งาน' : 'ปิดชั่วคราว',
                        $branch->area_sqm ?? '',
                        $branch->description ?? '',
                        $branch->opening_date ? $branch->opening_date->format('d/m/Y') : '',
                        $branch->created_at->format('d/m/Y H:i')
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            Log::error('Branch export CSV failed: ' . $e->getMessage());
            return back()->with('error', 'เกิดข้อผิดพลาดในการส่งออกข้อมูล CSV');
        }
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    /**
     * Get capacity information for a branch
     */
    private function getCapacityInfo($branch)
    {
        if (!$branch->capacity) {
            return [
                'total' => 0,
                'used' => $branch->employees->count(),
                'percentage' => 0,
                'available' => 'ไม่จำกัด'
            ];
        }

        $used = $branch->employees->count();
        $percentage = $branch->capacity > 0 ? round(($used / $branch->capacity) * 100, 1) : 0;

        return [
            'total' => $branch->capacity,
            'used' => $used,
            'percentage' => $percentage,
            'available' => $branch->capacity - $used
        ];
    }

    /**
     * Get capacity usage percentage as string
     */
    private function getCapacityUsage($branch)
    {
        if (!$branch->capacity) {
            return '-';
        }

        $used = $branch->employees->count();
        $percentage = $branch->capacity > 0 ? round(($used / $branch->capacity) * 100, 1) : 0;
        
        return $percentage . '%';
    }

    /**
     * Check if user can export branches
     */
    private function canExportBranches($user): bool
    {
        return in_array($user->role, ['super_admin', 'it_admin', 'hr']);
    }

    /**
     * Check if user can view branches
     */
    private function canViewBranches($user): bool
    {
        return in_array($user->role, ['super_admin', 'it_admin', 'hr', 'manager']);
    }

    /**
     * Check if user can create branches
     */
    private function canCreateBranches($user): bool
    {
        return in_array($user->role, ['super_admin', 'it_admin', 'hr']);
    }

    /**
     * Check if user can edit branches
     */
    private function canEditBranches($user): bool
    {
        return in_array($user->role, ['super_admin', 'it_admin', 'hr']);
    }

    /**
     * Check if user can delete branches
     */
    private function canDeleteBranches($user): bool
    {
        return in_array($user->role, ['super_admin', 'it_admin']);
    }

    /**
     * ✅ TRASH MANAGEMENT METHODS
     */
    public function trash()
    {
        try {
            $trashedBranches = Branch::onlyTrashed()
                ->with(['manager', 'employees'])
                ->orderBy('deleted_at', 'desc')
                ->paginate(10);

            return view('branches.trash', compact('trashedBranches'));

        } catch (\Exception $e) {
            Log::error('Branch Trash Error: ' . $e->getMessage());
            return redirect()->route('branches.index')
                ->with('error', 'เกิดข้อผิดพลาดในการโหลดถังขยะสาขา');
        }
    }

    public function restore($id)
    {
        try {
            $branch = Branch::onlyTrashed()->findOrFail($id);
            $branch->restore();

            return redirect()->route('branches.trash')
                ->with('success', "กู้คืนสาขา {$branch->name} เรียบร้อยแล้ว");

        } catch (\Exception $e) {
            Log::error('Branch Restore Error: ' . $e->getMessage());
            return redirect()->route('branches.trash')
                ->with('error', 'เกิดข้อผิดพลาดในการกู้คืนสาขา');
        }
    }

    public function forceDelete($id)
    {
        try {
            $branch = Branch::onlyTrashed()->findOrFail($id);
            $branchName = $branch->name;
            $branch->forceDelete();

            return redirect()->route('branches.trash')
                ->with('success', "ลบสาขา {$branchName} ถาวรเรียบร้อยแล้ว");

        } catch (\Exception $e) {
            Log::error('Branch Force Delete Error: ' . $e->getMessage());
            return redirect()->route('branches.trash')
                ->with('error', 'เกิดข้อผิดพลาดในการลบสาขาถาวร');
        }
    }
}
