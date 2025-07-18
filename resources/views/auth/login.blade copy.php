@extends('layouts.auth')

@section('title', 'Login')
@section('header-title', 'Welcome Back')
@section('header-subtitle', 'Please sign in to your account')

@section('content')
<form id="loginForm" method="POST" action="{{ route('login.post') }}">
    @csrf
    
    <div class="mb-3">
        <label for="email" class="form-label fw-medium">
            <i class="fas fa-envelope text-primary me-2"></i>Email Address
        </label>
        <div class="input-group">
            <span class="input-group-text">
                <i class="fas fa-envelope"></i>
            </span>
            <input type="email" 
                   class="form-control @error('email') is-invalid @enderror" 
                   id="email" 
                   name="email" 
                   value="{{ old('email') }}" 
                   placeholder="Enter your email"
                   required 
                   autofocus>
        </div>
        @error('email')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="password" class="form-label fw-medium">
            <i class="fas fa-lock text-primary me-2"></i>Password
        </label>
        <div class="input-group">
            <span class="input-group-text">
                <i class="fas fa-lock"></i>
            </span>
            <input type="password" 
                   class="form-control @error('password') is-invalid @enderror" 
                   id="password" 
                   name="password" 
                   placeholder="Enter your password"
                   required>
            <button type="button" 
                    class="btn btn-outline-secondary" 
                    onclick="togglePassword('password')">
                <i class="fas fa-eye"></i>
            </button>
        </div>
        @error('password')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <div class="form-check">
                <input class="form-check-input" 
                       type="checkbox" 
                       name="remember" 
                       id="remember" 
                       {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label" for="remember">
                    Remember me
                </label>
            </div>
        </div>
        <div class="col-md-6 text-end">
            {{--<a href="{{ route('password.request') }}" class="link-primary">
                Forgot password?
            </a>--}}
        </div>
    </div>

    <div class="d-grid mb-3">
        <button type="submit" class="btn btn-primary btn-lg" id="loginBtn">
            <span class="loading-spinner spinner-border spinner-border-sm me-2"></span>
            <span class="btn-text">
                <i class="fas fa-sign-in-alt me-2"></i>Sign In
            </span>
        </button>
    </div>

    <div class="text-center">
        <p class="text-muted mb-0">
            Need help? Contact your IT administrator
        </p>
    </div>
</form>

<!-- Demo Credentials Info -->
<div class="alert alert-info mt-3">
    <strong><i class="fas fa-info-circle me-2"></i>Demo Credentials:</strong><br>
    <small class="d-block">Super Admin: admin@itms.com / password</small>
    <small class="d-block">IT Admin: manager@itms.com / password</small>
    <small class="d-block">Employee: employee@itms.com / password</small>
</div>
@endsection

@push('scripts')
<script>
// Enhanced form submission with better error handling
document.getElementById('loginForm').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('loginBtn');
    
    // Check if CSRF token exists
    const csrfToken = document.querySelector('input[name="_token"]').value;
    if (!csrfToken) {
        e.preventDefault();
        alert('CSRF token is missing. Please refresh the page.');
        return;
    }
    
    console.log('Form submitting with CSRF token:', csrfToken);
    
    setButtonLoading(submitBtn, true);
    
    // Add timeout to prevent infinite loading
    setTimeout(() => {
        setButtonLoading(submitBtn, false);
    }, 10000);
});

// Auto-refresh CSRF token every 5 minutes
setInterval(() => {
    fetch('/login', {
        method: 'GET',
        credentials: 'same-origin'
    })
    .then(response => response.text())
    .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newToken = doc.querySelector('input[name="_token"]');
        if (newToken) {
            document.querySelector('input[name="_token"]').value = newToken.value;
            console.log('CSRF token refreshed');
        }
    })
    .catch(error => {
        console.log('Token refresh failed:', error);
    });
}, 300000); // 5 minutes
</script>
@endpush
