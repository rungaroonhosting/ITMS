<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * ✅ Show the login form
     */
    public function showLoginForm()
    {
        // Redirect if already authenticated
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * ✅ Enhanced login with comprehensive security and logging
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6|max:255',
        ], [
            'email.required' => 'กรุณากรอกอีเมล',
            'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
            'password.required' => 'กรุณากรอกรหัสผ่าน',
            'password.min' => 'รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        // ✅ Enhanced rate limiting with IP and email-based tracking
        $emailKey = 'login.email:' . $request->input('email');
        $ipKey = 'login.ip:' . $request->ip();
        
        if (RateLimiter::tooManyAttempts($emailKey, 5) || RateLimiter::tooManyAttempts($ipKey, 10)) {
            $emailSeconds = RateLimiter::availableIn($emailKey);
            $ipSeconds = RateLimiter::availableIn($ipKey);
            $waitTime = max($emailSeconds, $ipSeconds);
            
            Log::warning('🚫 Rate limit exceeded for login attempt', [
                'email' => $request->input('email'),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'wait_time' => $waitTime
            ]);
            
            throw ValidationException::withMessages([
                'email' => "มีการพยายามเข้าสู่ระบบมากเกินไป กรุณารอ {$waitTime} วินาที",
            ]);
        }

        // ✅ Enhanced authentication attempt with detailed logging
        Log::info('🔐 Login attempt started', [
            'email' => $request->input('email'),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'remember' => $remember
        ]);

        if (Auth::guard('web')->attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            // ✅ Clear rate limiting on successful login
            RateLimiter::clear($emailKey);
            RateLimiter::clear($ipKey);

            $user = Auth::user();
            
            // ✅ Comprehensive user validation
            if (!$user) {
                Log::error('🚨 Authentication succeeded but user not found');
                Auth::logout();
                throw ValidationException::withMessages([
                    'email' => 'เกิดข้อผิดพลาดในระบบ กรุณาลองใหม่อีกครั้ง',
                ]);
            }

            // ✅ Check user status
            if (isset($user->status) && $user->status !== 'active') {
                Log::warning('🚫 Inactive user login attempt', [
                    'user_id' => $user->id,
                    'user_status' => $user->status,
                    'email' => $user->email
                ]);
                
                Auth::logout();
                throw ValidationException::withMessages([
                    'email' => 'บัญชีของคุณถูกระงับการใช้งาน กรุณาติดต่อผู้ดูแลระบบ',
                ]);
            }

            // ✅ Update last login with error handling
            try {
                if (method_exists($user, 'updateLastLogin')) {
                    $user->updateLastLogin();
                } else {
                    // Fallback: update manually
                    $user->update([
                        'last_login_at' => now(),
                        'last_login_ip' => $request->ip()
                    ]);
                }
            } catch (\Exception $e) {
                Log::warning('Failed to update last login: ' . $e->getMessage());
            }

            // ✅ Create API token with role-based expiration
            try {
                $tokenExpiry = $this->getTokenExpiry($user);
                $token = $user->createToken('ITMS-Login-' . now()->format('Y-m-d-H-i-s'), ['*'], $tokenExpiry)->plainTextToken;
            } catch (\Exception $e) {
                Log::warning('Failed to create API token: ' . $e->getMessage());
                $token = null;
            }

            // ✅ Success logging
            Log::info('✅ Login successful', [
                'user_id' => $user->id,
                'user_role' => $user->role,
                'user_name' => $user->full_name_th ?? $user->name,
                'email' => $user->email,
                'ip' => $request->ip(),
                'remember' => $remember,
                'token_created' => $token ? 'yes' : 'no'
            ]);

            // ✅ AJAX response
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'เข้าสู่ระบบสำเร็จ',
                    'user' => $this->formatUserResponse($user),
                    'token' => $token,
                    'redirect' => $this->redirectTo($user)
                ]);
            }

            // ✅ Standard redirect with success message
            return redirect()
                ->intended($this->redirectTo($user))
                ->with('success', 'ยินดีต้อนรับเข้าสู่ระบบ IT Management System');
        }

        // ✅ Failed login handling
        RateLimiter::hit($emailKey);
        RateLimiter::hit($ipKey);

        Log::warning('🚫 Login failed - Invalid credentials', [
            'email' => $request->input('email'),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'อีเมลหรือรหัสผ่านไม่ถูกต้อง',
                'errors' => [
                    'email' => ['อีเมลหรือรหัสผ่านไม่ถูกต้อง']
                ]
            ], 422);
        }

        throw ValidationException::withMessages([
            'email' => 'อีเมลหรือรหัสผ่านไม่ถูกต้อง',
        ]);
    }

    /**
     * ✅ Enhanced logout with comprehensive cleanup
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        
        if ($user) {
            Log::info('👋 User logout initiated', [
                'user_id' => $user->id,
                'user_role' => $user->role,
                'user_name' => $user->full_name_th ?? $user->name,
                'ip' => $request->ip()
            ]);

            // ✅ Revoke current token if exists
            try {
                if (method_exists($user, 'currentAccessToken')) {
                    $currentToken = $user->currentAccessToken();
                    if ($currentToken) {
                        $currentToken->delete();
                        Log::info('🔑 API token revoked successfully');
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Failed to revoke token: ' . $e->getMessage());
            }
        }

        // ✅ Standard Laravel logout
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Log::info('✅ Logout completed successfully');

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'ออกจากระบบสำเร็จ',
                'redirect' => route('login')
            ]);
        }

        return redirect()->route('login')->with('success', 'ออกจากระบบสำเร็จ');
    }

    /**
     * ✅ Enhanced redirect logic based on user role
     */
    protected function redirectTo($user = null)
    {
        if (!$user) {
            $user = Auth::user();
        }

        if (!$user) {
            return route('login');
        }

        // ✅ Role-based redirects
        switch ($user->role) {
            case 'super_admin':
                return route('dashboard'); // Can go anywhere
            case 'it_admin':
                return route('employees.index'); // Go to employee management
            case 'hr':
                return route('employees.index'); // Go to employee management
            case 'manager':
                return route('dashboard'); // Go to dashboard
            case 'express':
                return route('employees.index'); // Go to employee management
            case 'employee':
            default:
                return route('dashboard'); // Go to dashboard
        }
    }

    /**
     * ✅ Get token expiry based on user role
     */
    protected function getTokenExpiry($user)
    {
        switch ($user->role) {
            case 'super_admin':
                return now()->addHours(12); // 12 hours for super admin
            case 'it_admin':
                return now()->addHours(10); // 10 hours for IT admin
            case 'hr':
                return now()->addHours(8); // 8 hours for HR
            case 'manager':
                return now()->addHours(6); // 6 hours for manager
            case 'express':
                return now()->addHours(4); // 4 hours for express
            case 'employee':
            default:
                return now()->addHours(2); // 2 hours for regular employee
        }
    }

    /**
     * ✅ Format user response for API
     */
    protected function formatUserResponse($user)
    {
        if (!$user) return null;

        return [
            'id' => $user->id,
            'name' => $user->full_name_th ?? $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'status' => $user->status ?? 'active',
            'department' => $user->department->name ?? null,
            'branch' => $user->branch->name ?? null,
            'last_login' => $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i:s') : null,
        ];
    }

    /**
     * ✅ Show user profile
     */
    public function showProfile()
    {
        $user = Auth::user();
        return view('auth.profile', compact('user'));
    }

    /**
     * ✅ Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:employees,email,' . $user->id,
            'current_password' => 'nullable|string',
            'password' => 'nullable|string|min:6|confirmed',
        ], [
            'name.required' => 'กรุณากรอกชื่อ',
            'email.required' => 'กรุณากรอกอีเมล',
            'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
            'email.unique' => 'อีเมลนี้ถูกใช้งานแล้ว',
            'password.min' => 'รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร',
            'password.confirmed' => 'รหัสผ่านยืนยันไม่ตรงกัน',
        ]);

        // ✅ Verify current password if provided
        if ($request->filled('current_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                throw ValidationException::withMessages([
                    'current_password' => 'รหัสผ่านปัจจุบันไม่ถูกต้อง',
                ]);
            }
        }

        // ✅ Update basic info
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // ✅ Update password if provided
        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
            
            Log::info('🔐 Password updated', [
                'user_id' => $user->id,
                'user_email' => $user->email
            ]);
        }

        Log::info('👤 Profile updated', [
            'user_id' => $user->id,
            'user_email' => $user->email
        ]);

        return redirect()->route('profile')->with('success', 'อัปเดตข้อมูลส่วนตัวสำเร็จ');
    }

    /**
     * ✅ Send password reset link
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:employees,email'
        ], [
            'email.required' => 'กรุณากรอกอีเมล',
            'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
            'email.exists' => 'ไม่พบอีเมลนี้ในระบบ'
        ]);

        $status = Password::broker('employees')->sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('success', 'ส่งลิงก์รีเซ็ตรหัสผ่านไปยังอีเมลของคุณแล้ว');
        }

        throw ValidationException::withMessages([
            'email' => 'ไม่สามารถส่งลิงก์รีเซ็ตรหัสผ่านได้ กรุณาลองใหม่อีกครั้ง'
        ]);
    }

    /**
     * ✅ Reset password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $status = Password::broker('employees')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('success', 'รีเซ็ตรหัสผ่านสำเร็จ กรุณาเข้าสู่ระบบด้วยรหัสผ่านใหม่');
        }

        throw ValidationException::withMessages([
            'email' => 'ไม่สามารถรีเซ็ตรหัสผ่านได้ กรุณาตรวจสอบข้อมูลและลองใหม่อีกครั้ง'
        ]);
    }

    /**
     * ✅ Confirm password
     */
    public function confirmPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ], [
            'password.required' => 'กรุณากรอกรหัสผ่านเพื่อยืนยัน',
        ]);

        if (!Hash::check($request->password, Auth::user()->password)) {
            throw ValidationException::withMessages([
                'password' => 'รหัสผ่านไม่ถูกต้อง',
            ]);
        }

        $request->session()->put('auth.password_confirmed_at', time());

        return redirect()->intended();
    }

    /**
     * ✅ Two-factor challenge (for future implementation)
     */
    public function twoFactorChallenge(Request $request)
    {
        // TODO: Implement two-factor authentication
        return response()->json([
            'success' => false,
            'message' => 'Two-factor authentication is not yet implemented'
        ], 501);
    }

    /**
     * ✅ Admin: Manage users (Super Admin only)
     */
    public function manageUsers()
    {
        $this->authorize('viewAny', Employee::class);
        
        $users = Employee::with(['department', 'branch'])
            ->paginate(20);
            
        return view('admin.auth.users', compact('users'));
    }

    /**
     * ✅ Admin: Suspend user
     */
    public function suspendUser(Employee $user)
    {
        $this->authorize('update', $user);
        
        $user->update(['status' => 'suspended']);
        
        // Revoke all tokens
        $user->tokens()->delete();
        
        Log::warning('👤 User suspended', [
            'suspended_user_id' => $user->id,
            'suspended_by' => Auth::id(),
            'ip' => request()->ip()
        ]);
        
        return back()->with('success', "ระงับบัญชี {$user->name} สำเร็จ");
    }

    /**
     * ✅ Admin: Activate user
     */
    public function activateUser(Employee $user)
    {
        $this->authorize('update', $user);
        
        $user->update(['status' => 'active']);
        
        Log::info('👤 User activated', [
            'activated_user_id' => $user->id,
            'activated_by' => Auth::id(),
            'ip' => request()->ip()
        ]);
        
        return back()->with('success', "เปิดใช้งานบัญชี {$user->name} สำเร็จ");
    }

    /**
     * ✅ Admin: Force logout user
     */
    public function forceLogout(Employee $user)
    {
        $this->authorize('update', $user);
        
        // Revoke all tokens
        $user->tokens()->delete();
        
        Log::warning('👤 User force logged out', [
            'force_logout_user_id' => $user->id,
            'forced_by' => Auth::id(),
            'ip' => request()->ip()
        ]);
        
        return back()->with('success', "บังคับออกจากระบบ {$user->name} สำเร็จ");
    }
}
