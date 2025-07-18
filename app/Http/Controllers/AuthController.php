<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\Employee;

class AuthController extends Controller
{
    /**
     * แสดงหน้า Login
     */
    public function showLoginForm()
    {
        // ถ้า login แล้วให้ redirect ไป dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        
        return view('auth.login');
    }

    /**
     * ประมวลผล Login
     */
    public function login(Request $request)
    {
        // Validate ข้อมูล
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'กรุณากรอกอีเมล',
            'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
            'password.required' => 'กรุณากรอกรหัสผ่าน',
        ]);

        // ตรวจสอบว่าพนักงานมีอยู่จริง
        $employee = Employee::where('email', $credentials['email'])->first();

        if (!$employee) {
            throw ValidationException::withMessages([
                'email' => ['ไม่พบอีเมลนี้ในระบบ'],
            ]);
        }

        // ตรวจสอบสถานะพนักงาน
        if ($employee->status !== 'active') {
            throw ValidationException::withMessages([
                'email' => ['บัญชีผู้ใช้ถูกระงับการใช้งาน กรุณาติดต่อผู้ดูแลระบบ'],
            ]);
        }

        // ลองเข้าสู่ระบบ
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // บันทึก log การเข้าสู่ระบบ
            \Log::info('User logged in', [
                'user_id' => $employee->id,
                'email' => $employee->email,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // ส่งข้อความต้อนรับ
            $welcomeMessage = 'ยินดีต้อนรับเข้าสู่ระบบ ' . $employee->full_name_th;
            
            return redirect()->intended(route('dashboard'))
                ->with('success', $welcomeMessage);
        }

        // หากเข้าสู่ระบบไม่สำเร็จ
        throw ValidationException::withMessages([
            'email' => ['ข้อมูลการเข้าสู่ระบบไม่ถูกต้อง'],
        ]);
    }

    /**
     * ออกจากระบบ
     */
    public function logout(Request $request)
    {
        $employee = Auth::user();

        // บันทึก log การออกจากระบบ
        if ($employee) {
            \Log::info('User logged out', [
                'user_id' => $employee->id,
                'email' => $employee->email,
                'ip' => $request->ip(),
            ]);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'ออกจากระบบเรียบร้อยแล้ว');
    }

    /**
     * หน้า Dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // สถิติสำหรับ Dashboard
        $stats = [
            'total_employees' => Employee::count(),
            'active_employees' => Employee::where('status', 'active')->count(),
            'departments' => \App\Models\Department::where('is_active', true)->count(),
        ];

        // ข้อมูลเฉพาะตามสิทธิ์
        if ($user->canViewPasswords()) {
            $stats['suspended_employees'] = Employee::where('status', 'suspended')->count();
            $stats['inactive_employees'] = Employee::where('status', 'inactive')->count();
            $stats['it_admin_count'] = Employee::where('role', 'it_admin')->count();
            $stats['manager_count'] = Employee::where('role', 'manager')->count();
        }

        // ข้อมูลแผนกพร้อมจำนวนพนักงาน
        $departments = \App\Models\Department::withCount(['employees' => function ($query) {
            $query->where('status', 'active');
        }])->where('is_active', true)->get();

        // กิจกรรมล่าสุด (ถ้ามี)
        $recentActivities = [];
        if ($user->canViewPasswords()) {
            $recentActivities = Employee::where('status', 'active')
                ->latest('updated_at')
                ->limit(5)
                ->get()
                ->map(function ($emp) {
                    return [
                        'user' => $emp->full_name_th,
                        'action' => 'อัปเดตข้อมูล',
                        'time' => $emp->updated_at->diffForHumans(),
                    ];
                });
        }

        return view('dashboard', compact('stats', 'departments', 'recentActivities'));
    }

    /**
     * หน้าโปรไฟล์
     */
    public function profile()
    {
        $user = Auth::user();
        return view('auth.profile', compact('user'));
    }

    /**
     * อัปเดตโปรไฟล์
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'nickname' => ['nullable', 'string', 'max:255'],
            'current_password' => ['required_with:password'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'nickname.max' => 'ชื่อเล่นต้องไม่เกิน 255 ตัวอักษร',
            'current_password.required_with' => 'กรุณากรอกรหัสผ่านปัจจุบัน',
            'password.min' => 'รหัสผ่านต้องมีอย่างน้อย 8 ตัวอักษร',
            'password.confirmed' => 'รหัสผ่านและการยืนยันรหัสผ่านไม่ตรงกัน',
        ]);

        // อัปเดตชื่อเล่น
        if (isset($validated['nickname'])) {
            $user->nickname = $validated['nickname'];
        }

        // เปลี่ยนรหัสผ่าน
        if (!empty($validated['password'])) {
            if (!Hash::check($validated['current_password'], $user->password)) {
                throw ValidationException::withMessages([
                    'current_password' => ['รหัสผ่านปัจจุบันไม่ถูกต้อง'],
                ]);
            }

            $user->password = Hash::make($validated['password']);

            // Log การเปลี่ยนรหัสผ่าน
            \Log::info('User changed password', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip(),
            ]);
        }

        $user->save();

        return back()->with('success', 'อัปเดตข้อมูลโปรไฟล์เรียบร้อยแล้ว');
    }

    /**
     * ตรวจสอบสถานะการเข้าสู่ระบบ (สำหรับ AJAX)
     */
    public function checkAuth(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            return response()->json([
                'authenticated' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->full_name_th,
                    'email' => $user->email,
                    'role' => $user->role,
                    'can_view_passwords' => $user->canViewPasswords(),
                    'avatar_url' => null, // สามารถเพิ่มรูป avatar ได้ในอนาคต
                ]
            ]);
        }

        return response()->json([
            'authenticated' => false
        ]);
    }
}
