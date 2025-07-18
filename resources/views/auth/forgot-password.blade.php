@extends('layouts.auth')

@section('title', 'Forgot Password')
@section('header-title', 'Forgot Password')
@section('header-subtitle', 'We\'ll send you a reset link')

@section('content')
<form id="forgotPasswordForm" method="POST" action="{{ route('password.email') }}">
    @csrf
    
    <div class="mb-4">
        <p class="text-muted">
            Enter your email address and we'll send you a link to reset your password.
        </p>
    </div>

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
                   placeholder="Enter your email address"
                   required 
                   autofocus>
        </div>
        @error('email')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="d-grid mb-3">
        <button type="submit" class="btn btn-primary btn-lg" id="resetBtn">
            <span class="loading-spinner spinner-border spinner-border-sm me-2"></span>
            <span class="btn-text">
                <i class="fas fa-paper-plane me-2"></i>Send Reset Link
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
document.getElementById('forgotPasswordForm').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('resetBtn');
    setButtonLoading(submitBtn, true);
});
</script>
@endpush
