<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        // Rate limiting
        $key = 'login.attempts:' . $request->ip();
        
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            
            throw ValidationException::withMessages([
                'email' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ]);
        }

        if (Auth::guard('web')->attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            // Clear rate limiting on successful login
            RateLimiter::clear($key);

            $user = Auth::user();
            
            // Update last login (with null check)
            if ($user && method_exists($user, 'updateLastLogin')) {
                $user->updateLastLogin();
            }

            // Create API token with role-based expiration (with null check)
            if ($user) {
                $tokenExpiry = $user->isAdmin() ? now()->addHours(8) : now()->addHours(4);
                $token = $user->createToken('API Token', ['*'], $tokenExpiry)->plainTextToken;
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Login successful',
                    'user' => $user ? $user->formatUserResponse() : null,
                    'token' => $token ?? null,
                    'redirect' => $this->redirectTo()
                ]);
            }

            return redirect()->intended($this->redirectTo());
        }

        // Increment rate limiting
        RateLimiter::hit($key);

        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'These credentials do not match our records.',
                'errors' => [
                    'email' => ['These credentials do not match our records.']
                ]
            ], 422);
        }

        throw ValidationException::withMessages([
            'email' => ['These credentials do not match our records.'],
        ]);
    }

    public function logout(Request $request)
    {
        // Revoke current token if exists (with proper null checks)
        if ($request->user() && method_exists($request->user(), 'currentAccessToken')) {
            $currentToken = $request->user()->currentAccessToken();
            if ($currentToken) {
                $currentToken->delete();
            }
        }

        // Standard Laravel logout
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully'
            ]);
        }

        return redirect()->route('login');
    }

    protected function redirectTo()
    {
        $user = Auth::user();
        
        // Default redirect
        return route('dashboard');
    }

    public function showProfile()
    {
        return view('auth.profile');
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:employees,email,' . $user->id,
            'current_password' => 'nullable|string',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Verify current password if provided
        if ($request->filled('current_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                throw ValidationException::withMessages([
                    'current_password' => ['The current password is incorrect.'],
                ]);
            }
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        return redirect()->route('profile')->with('success', 'Profile updated successfully.');
    }
}
