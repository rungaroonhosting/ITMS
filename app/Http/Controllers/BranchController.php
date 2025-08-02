<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BranchController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        // Remove role middleware for testing
        // $this->middleware(['auth', 'role:super_admin,admin']);
    }

    /**
     * Display a listing of branches
     */
    public function index(Request $request)
    {
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
    }

    /**
     * Show the form for creating a new branch
     */
    public function create()
    {
        // Get users who can be managers (no role restrictions for now)
        $availableManagers = User::whereNull('managed_branch_id')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('branches.create', compact('availableManagers'));
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
                'manager_id' => 'nullable|exists:users,id',
                'is_active' => 'nullable'
            ]);

            Log::info('Validation Passed:', $validated);

            // Handle checkbox - if not checked, it won't be in request
            $validated['is_active'] = $request->has('is_active');

            // Create branch
            $branch = Branch::create($validated);

            Log::info('Branch Created:', $branch->toArray());

            // Update manager's managed_branch_id if manager is assigned
            if ($request->manager_id) {
                User::where('id', $request->manager_id)->update([
                    'managed_branch_id' => $branch->id
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
        $branch->load(['manager', 'employees']);
        
        // Get branch statistics
        $statistics = [
            'total_employees' => $branch->employees()->count(),
            'active_employees' => $branch->employees()->where('is_active', true)->count(),
            'departments' => $branch->employees()->with('department')->get()->pluck('department.name')->unique()->filter()->values(),
        ];

        return view('branches.show', compact('branch', 'statistics'));
    }

    /**
     * Show the form for editing the specified branch
     */
    public function edit(Branch $branch)
    {
        // Get available managers (including current manager)
        $availableManagers = User::where(function($query) use ($branch) {
            $query->whereNull('managed_branch_id')
                  ->orWhere('id', $branch->manager_id);
        })
        ->where('is_active', true)
        ->orderBy('name')
        ->get();

        return view('branches.edit', compact('branch', 'availableManagers'));
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
                'manager_id' => 'nullable|exists:users,id',
                'is_active' => 'nullable'
            ]);

            // Handle checkbox
            $validated['is_active'] = $request->has('is_active');

            // Remove managed_branch_id from old manager
            if ($branch->manager_id && $branch->manager_id != $request->manager_id) {
                User::where('id', $branch->manager_id)->update([
                    'managed_branch_id' => null
                ]);
            }

            // Update branch
            $branch->update($validated);

            // Set managed_branch_id for new manager
            if ($request->manager_id) {
                User::where('id', $request->manager_id)->update([
                    'managed_branch_id' => $branch->id
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

            // Remove managed_branch_id from manager
            if ($branch->manager_id) {
                User::where('id', $branch->manager_id)->update([
                    'managed_branch_id' => null
                ]);
            }

            $branch->delete();

            return redirect()->route('branches.index')
                ->with('success', 'ลบสาขาเรียบร้อยแล้ว');

        } catch (\Exception $e) {
            Log::error('Branch Delete Error:', ['error' => $e->getMessage()]);
            return redirect()->route('branches.index')
                ->with('error', 'เกิดข้อผิดพลาดในการลบสาขา: ' . $e->getMessage());
        }
    }

    /**
     * Toggle branch active status
     */
    public function toggleStatus(Branch $branch)
    {
        try {
            $branch->update(['is_active' => !$branch->is_active]);
            
            $status = $branch->is_active ? 'เปิดใช้งาน' : 'ปิดใช้งาน';
            
            return redirect()->back()
                ->with('success', "เปลี่ยนสถานะสาขาเป็น {$status} เรียบร้อยแล้ว");

        } catch (\Exception $e) {
            Log::error('Branch Toggle Status Error:', ['error' => $e->getMessage()]);
            return redirect()->back()
                ->with('error', 'เกิดข้อผิดพลาดในการเปลี่ยนสถานะ');
        }
    }

    /**
     * Get branch data for AJAX
     */
    public function getData(Request $request)
    {
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
    }
}
