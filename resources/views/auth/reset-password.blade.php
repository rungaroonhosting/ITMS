@extends('layouts.auth')

@section('title', 'Reset Password')
@section('header-title', 'Reset Password')
@section('header-subtitle', 'Create a new password')

@section('content')
<form id="resetPasswordForm" method="POST" action="{{ route('password.update') }}">
    @csrf
    
    <input type="hidden" name="token" value="{{ $token }}">
    <input type="hidden" name="email" value="{{ $email }}">

    <div class="mb-3">
        <label for="email_display" class="form-label fw-medium">
            <i class="fas fa-envelope text-primary me-2"></i>Email Address
        </label>
        <div class="input-group">
            <span class="input-group-text">
                <i class="fas fa-envelope"></i>
            </span>
            <input type="email" 
                   class="form-control" 
                   id="email_display" 
                   value="{{ $email }}" 
                   readonly>
        </div>
    </div>

    <div class="mb-3">
        <label for="password" class="form-label fw-medium">
            <i class="fas fa-lock text-primary me-2"></i>New Password
        </label>
        <div class="input-group">
            <span class="input-group-text">
                <i class="fas fa-lock"></i>
            </span>
            <input type="password" 
                   class="form-control @error('password') is-invalid @enderror" 
                   id="password" 
                   name="password" 
                   placeholder="Enter new password"
                   required 
                   minlength="8">
            <button type="button" 
                    class="btn btn-outline-secondary" 
                    onclick="togglePassword('password')">
                <i class="fas fa-eye"></i>
            </button>
        </div>
        <small class="form-text text-muted">Password must be at least 8 characters long.</small>
        @error('password')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="password_confirmation" class="form-label fw-medium">
            <i class="fas fa-lock text-primary me-2"></i>Confirm Password
        </label>
        <div class="input-group">
            <span class="input-group-text">
                <i class="fas fa-lock"></i>
            </span>
            <input type="password" 
                   class="form-control" 
                   id="password_confirmation" 
                   name="password_confirmation" 
                   placeholder="Confirm new password"
                   required>
            <button type="button" 
                    class="btn btn-outline-secondary" 
                    onclick="togglePassword('password_confirmation')">
                <i class="fas fa-eye"></i>
            </button>
        </div>
    </div>

    <div class="d-grid mb-3">
        <button type="submit" class="btn btn-primary btn-lg" id="updateBtn">
            <span class="loading-spinner spinner-border spinner-border-sm me-2"></span>
            <span class="btn-text">
                <i class="fas fa-save me-2"></i>Update Password
            </span>
        </button>
    </div>

    <div class="text-center">
        <a href="{{ route('login') }}" class="link-primary">
            <i class="fas fa-arrow-left me-2"></i>Back to Login
        </a>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.getElementById('resetPasswordForm').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const passwordConfirmation = document.getElementById('password_confirmation').value;
    
    if (password !== passwordConfirmation) {
        e.preventDefault();
        alert('Passwords do not match!');
        return;
    }
    
    const submitBtn = document.getElementById('updateBtn');
    setButtonLoading(submitBtn, true);
});

// Real-time password confirmation validation
document.getElementById('password_confirmation').addEventListener('input', function(e) {
    const password = document.getElementById('password').value;
    const passwordConfirmation = e.target.value;
    
    if (passwordConfirmation && password !== passwordConfirmation) {
        e.target.classList.add('is-invalid');
    } else {
        e.target.classList.remove('is-invalid');
    }
});
</script>
@endpush
