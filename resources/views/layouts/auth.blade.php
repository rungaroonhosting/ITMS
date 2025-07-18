<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'เข้าสู่ระบบ') - ITMS</title>
    
    <!-- Google Fonts - Prompt -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* Red to Orange Gradient Theme */
        :root {
            /* Primary Colors */
            --primary-red: #C96261;         /* สีแดงอ่อม */
            --primary-orange: #FAB250;      /* สีส้ม/เหลือง */
            --light-red: #D47573;           /* สีแดงอ่อนขึ้น */
            --dark-orange: #E6A043;         /* สีส้มเข้ม */
            
            /* Supporting Colors */
            --white: #FFFFFF;
            --light-gray: #F8F9FA;
            --gray: #6C757D;
            --dark-gray: #495057;
            --text-primary: #212529;
            --text-secondary: #6C757D;
            --border-color: #DEE2E6;
            
            /* Status Colors */
            --success: #28A745;
            --danger: #DC3545;
            --warning: #FFC107;
            --info: #17A2B8;
        }

        * {
            font-family: 'Prompt', sans-serif;
        }
        
        body {
            background: 
                linear-gradient(135deg, rgba(201, 98, 97, 0.85) 0%, rgba(250, 178, 80, 0.85) 100%),
                url('/images/bg02.webp') center/cover no-repeat fixed;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }

        .auth-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 20px 0;
        }

        .auth-container {
            background: var(--white);
            border-radius: 25px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(201, 98, 97, 0.2);
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
            display: flex;
            min-height: 600px;
        }

        /* Ensure columns stretch full height */
        .auth-container .row {
            flex: 1;
            min-height: 100%;
        }

        .auth-container .col-lg-5,
        .auth-container .col-md-6 {
            display: flex !important;
            flex-direction: column;
        }

        /* Left Side - Image & Content */
        .auth-left {
            background: 
               /* linear-gradient(135deg, rgba(15, 23, 42, 0.8) 0%, rgba(30, 58, 138, 0.7) 50%, rgba(15, 23, 42, 0.8) 100%),*/
                url('/images/bg01.png') right/cover no-repeat;
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100%;
            height: 100%;
            position: relative;
            overflow: hidden;
            text-align: center;
            border-radius: 25px 0 0 25px;
        }

        .auth-left::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.15) 0%, rgba(37, 99, 235, 0.1) 40%, transparent 70%);
            animation: float 8s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        .service-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: #FFFFFF;
            margin-bottom: 20px;
            position: relative;
            z-index: 2;
            text-shadow: 
                2px 2px 4px rgba(0,0,0,0.8),
                0 0 10px rgba(0,0,0,0.5),
                1px 1px 0px rgba(0,0,0,0.9);
            letter-spacing: 0.5px;
        }
        .service-description {
            font-size: 1.1rem;
            color: #F1F5F9;
            margin-bottom: 40px;
            line-height: 1.6;
            position: relative;
            z-index: 2;
            text-shadow: 
                1px 1px 3px rgba(0,0,0,0.8),
                0 0 8px rgba(0,0,0,0.4);
            font-weight: 500;
        }

        /* Add styling for service content */
        .service-content {
            background: transparent;
            padding: 30px;
            border-radius: 0;
            backdrop-filter: none;
            border: none;
            position: relative;
            z-index: 3;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            width: 100%;
        }

        /* Right Side - Login Form */
        .auth-right {
            padding: 40px;
            background: var(--white);
        }

        .form-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .form-title {
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 10px;
        }

        .form-subtitle {
            color: var(--text-secondary);
            font-size: 1rem;
        }

        /* Fixed Form Group - NO LAYOUT SHIFT */
        .form-group-fixed {
            min-height: 95px;
            margin-bottom: 0;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
        }

        .form-group-fixed .form-label {
            margin-bottom: 8px;
            flex-shrink: 0;
        }

        .form-group-fixed .form-control,
        .form-group-fixed .password-field {
            flex-shrink: 0;
        }

        .form-group-fixed .error-space {
            min-height: 24px;
            margin-top: 8px;
            display: flex;
            align-items: flex-start;
            flex-grow: 1;
        }

        /* Remember Me Fixed Height */
        .form-check-fixed {
            min-height: 45px;
            margin-bottom: 0;
            display: flex;
            align-items: center;
        }

        /* Form Controls */
        .form-control {
            border-radius: 12px;
            border: 2px solid var(--border-color);
            padding: 14px 18px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: var(--white);
        }
        
        .form-control:focus {
            border-color: var(--primary-orange);
            box-shadow: 0 0 0 0.25rem rgba(250, 178, 80, 0.15);
            outline: none;
        }
        .form-control::placeholder {
            color: #ADB5BD;
        }

        .form-label {
            font-weight: 500;
            color: var(--text-primary);
            margin-bottom: 8px;
        }

        /* Buttons */
        .btn-primary {
            background: linear-gradient(45deg, var(--primary-red), var(--primary-orange));
            border: none;
            border-radius: 12px;
            padding: 14px 32px;
            font-weight: 600;
            font-size: 16px;
            color: var(--white);
            width: 100%;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(201, 98, 97, 0.3);
            position: relative;
            overflow: hidden;
            margin-top: 15px;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(201, 98, 97, 0.4);
            filter: brightness(1.1);
            color: var(--white);
        }

        .btn-primary:focus {
            box-shadow: 0 0 0 0.25rem rgba(201, 98, 97, 0.25);
            color: var(--white);
        }

        /* Loading State */
        .loading-spinner {
            display: none;
            width: 18px;
            height: 18px;
            border: 2px solid transparent;
            border-top: 2px solid currentColor;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 8px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .btn-loading .loading-spinner {
            display: inline-block;
        }
        
        .btn-loading .btn-text {
            opacity: 0.7;
        }

        /* Alerts */
        .alert {
            border-radius: 12px;
            border: none;
            padding: 16px 20px;
            font-weight: 500;
            margin-bottom: 20px;
        }
        
        .alert-danger {
            background: linear-gradient(45deg, var(--danger), #E74C3C);
            color: var(--white);
        }
        
        .alert-success {
            background: linear-gradient(45deg, var(--success), #2ECC71);
            color: var(--white);
        }
        
        .alert-info {
            background: linear-gradient(45deg, var(--primary-red), var(--primary-orange));
            color: var(--white);
        }

        .alert-warning {
            background: linear-gradient(45deg, var(--warning), #F39C12);
            color: var(--text-primary);
        }

        /* Footer */
        .auth-footer {
            text-align: center;
            padding: 20px 40px;
            background: #F8F9FA;
            color: var(--text-secondary);
            font-size: 14px;
        }

        .auth-footer .company-info {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }

        .auth-footer .developer i {
            color: var(--primary-orange);
            margin-right: 5px;
        }

        /* Input Group Styles */
        .input-group-text {
            background: linear-gradient(45deg, var(--primary-red), var(--primary-orange));
            color: var(--white);
            border: 2px solid var(--primary-red);
            border-radius: 12px 0 0 12px;
            font-weight: 500;
        }

        .input-group .form-control {
            border-left: none;
            border-radius: 0 12px 12px 0;
        }

        .input-group .form-control:focus {
            border-color: var(--primary-orange);
            border-left: 2px solid var(--primary-orange);
        }

        /* Remember me checkbox */
        .form-check-input:checked {
            background-color: var(--primary-orange);
            border-color: var(--primary-orange);
        }

        .form-check-input:focus {
            box-shadow: 0 0 0 0.25rem rgba(250, 178, 80, 0.25);
        }

        .form-check-label {
            color: var(--text-secondary);
            font-size: 14px;
        }

        /* Text Colors */
        .text-primary {
            color: var(--primary-orange) !important;
        }
        
        .link-primary {
            color: var(--primary-orange);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .link-primary:hover {
            color: var(--primary-red);
            text-decoration: underline;
        }

        /* Password Toggle */
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            cursor: pointer;
            transition: color 0.3s ease;
            z-index: 10;
        }

        .password-toggle:hover {
            color: var(--primary-orange);
        }

        .password-field {
            position: relative;
        }

        /* Validation Styles - FIXED */
        .form-control.is-invalid {
            border-color: var(--danger);
            box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.15);
        }

        .invalid-feedback {
            color: var(--danger);
            font-size: 0.875rem;
            font-weight: 500;
            display: block;
            width: 100%;
            margin: 0;
            padding: 0;
            line-height: 1.3;
        }

        .error-space .invalid-feedback {
            margin: 0;
            padding: 0;
        }

        /* Additional Links Container */
        .additional-links {
            margin-top: 20px;
            text-align: center;
        }

        /* Responsive Design */
        @media (max-width: 991px) {
            .auth-container {
                margin: 20px;
                border-radius: 20px;
            }

            .auth-left {
                padding: 0;
                min-height: 400px;
                justify-content: center;
                align-items: center;
                text-align: center;
                border-radius: 20px 20px 0 0;
            }

            .service-content {
                margin-bottom: 0;
                padding: 25px;
            }

            .service-title {
                font-size: 2rem;
            }

            .auth-right {
                padding: 30px;
            }

            .auth-footer {
                padding: 20px 30px;
            }

            .form-group-fixed {
                min-height: 90px;
            }
        }

        @media (max-width: 767px) {
            .auth-wrapper {
                padding: 10px 0;
            }

            .auth-container {
                margin: 10px;
                border-radius: 15px;
            }

            .auth-left {
                padding: 0;
                min-height: 300px;
                text-align: center;
                justify-content: center;
                align-items: center;
                border-radius: 15px 15px 0 0;
            }

            .service-content {
                margin-bottom: 0;
                padding: 20px;
            }

            .service-title {
                font-size: 1.8rem;
            }

            .service-description {
                font-size: 1rem;
            }

            .auth-right {
                padding: 20px;
            }

            .form-title {
                font-size: 1.5rem;
            }

            .auth-footer {
                padding: 15px 20px;
            }

            .form-group-fixed {
                min-height: 85px;
            }
        }

        @media (max-width: 480px) {
            .auth-left {
                padding: 0;
                justify-content: center;
                align-items: center;
                text-align: center;
                border-radius: 15px 15px 0 0;
            }

            .service-content {
                margin-bottom: 0;
                padding: 15px;
            }

            .auth-right {
                padding: 15px;
            }

            .service-title {
                font-size: 1.6rem;
            }

            .form-title {
                font-size: 1.3rem;
            }

            .auth-footer {
                padding: 15px;
            }

            .form-group-fixed {
                min-height: 80px;
            }
        }

        /* Animation for page load */
        .auth-container {
            animation: slideUp 0.8s ease-out;
        }

        @keyframes slideUp {
            0% {
                opacity: 0;
                transform: translateY(50px) scale(0.95);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="auth-container">
                        <div class="row g-0">
                            <!-- Left Side - Service Info -->
                            <div class="col-lg-5 col-md-6 d-flex">
                                <div class="auth-left">
                                    <div class="service-content">
                                        <h1 class="service-title">บริการออนไลน์</h1>
                                        <p class="service-description">
                                            ให้คุณจัดการกลุ่มเรื่องต่างๆ ด้วยตัวคุณเอง ตลอด 24 ชม.<br>
                                            ระบบจัดการ IT ที่ครอบคลุมและใช้งานง่าย<br>
                                            <small style="opacity: 0.9; color: #CBD5E1; font-weight: 400;">Professional IT Management Solutions</small>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Side - Login Form -->
                            <div class="col-lg-7 col-md-6">
                                <div class="auth-right">
                                    <!-- Form Header -->
                                    <div class="form-header">
                                        <h2 class="form-title">@yield('form-title', 'เข้าสู่ระบบ')</h2>
                                        <p class="form-subtitle">@yield('form-subtitle', 'IT Management System')</p>
                                    </div>

                                    <!-- Alerts -->
                                    @if($errors->any())
                                        <div class="alert alert-danger">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                <div class="flex-grow-1">
                                                    @if($errors->has('error'))
                                                        {{ $errors->first('error') }}
                                                    @else
                                                        กรุณาแก้ไขข้อผิดพลาดต่อไปนี้:
                                                        <ul class="mb-0 mt-2">
                                                            @foreach($errors->all() as $error)
                                                                <li>{{ $error }}</li>
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if(session('success'))
                                        <div class="alert alert-success">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-check-circle me-2"></i>
                                                <span>{{ session('success') }}</span>
                                            </div>
                                        </div>
                                    @endif

                                    @if(session('info'))
                                        <div class="alert alert-info">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-info-circle me-2"></i>
                                                <span>{{ session('info') }}</span>
                                            </div>
                                        </div>
                                    @endif

                                    @if(session('warning'))
                                        <div class="alert alert-warning">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                <span>{{ session('warning') }}</span>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Login Form - FIXED LAYOUT -->
                                    <form method="POST" action="{{ route('login') }}" id="loginForm" novalidate>
                                        @csrf
                                        
                                        <!-- Email Field - Fixed Height -->
                                        <div class="form-group-fixed">
                                            <label for="email" class="form-label">อีเมล</label>
                                            <input type="email" 
                                                   class="form-control @error('email') is-invalid @enderror" 
                                                   id="email" 
                                                   name="email"
                                                   value="{{ old('email') }}" 
                                                   placeholder="กรุณากรอกอีเมล"
                                                   required 
                                                   autocomplete="email"
                                                   autofocus>
                                            <div class="error-space">
                                                @error('email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <!-- Password Field - Fixed Height -->
                                        <div class="form-group-fixed">
                                            <label for="password" class="form-label">รหัสผ่าน</label>
                                            <div class="password-field">
                                                <input type="password" 
                                                       class="form-control @error('password') is-invalid @enderror" 
                                                       id="password" 
                                                       name="password"
                                                       placeholder="กรุณากรอกรหัสผ่าน"
                                                       required 
                                                       autocomplete="current-password">
                                                <span class="password-toggle" onclick="togglePassword('password')">
                                                    <i class="fas fa-eye"></i>
                                                </span>
                                            </div>
                                            <div class="error-space">
                                                @error('password')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <!-- Remember Me - Fixed Height -->
                                        <div class="form-check-fixed">
                                            <input type="checkbox" 
                                                   class="form-check-input" 
                                                   id="remember" 
                                                   name="remember" 
                                                   {{ old('remember') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="remember">
                                                จดจำการเข้าสู่ระบบ
                                            </label>
                                        </div>
                                        
                                        <!-- Submit Button -->
                                        <button type="submit" class="btn btn-primary" id="loginBtn">
                                            <span class="loading-spinner"></span>
                                            <span class="btn-text">เข้าสู่ระบบ</span>
                                        </button>
                                        
                                        <!-- Additional Links -->
                                        <div class="additional-links">
                                            @if (Route::has('password.request'))
                                                <a class="link-primary" href="{{ route('password.request') }}">
                                                    ลืมรหัสผ่าน?
                                                </a>
                                            @endif
                                            
                                            @if (Route::has('register'))
                                                <div class="mt-2">
                                                    ยังไม่มีบัญชี? 
                                                    <a class="link-primary" href="{{ route('register') }}">
                                                        สมัครสมาชิก
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </form>
                                </div>

                                <!-- Footer -->
                                <div class="auth-footer">
                                    <div class="company-info">
                                        <div class="copyright">
                                            &copy; 2025 IT Management System. All rights reserved.
                                        </div>
                                        <div class="developer">
                                            <i class="fas fa-gear"></i>
                                            System By Rungaroon Solution
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // CSRF Token Setup
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        // Set CSRF token for all AJAX requests
        if (window.jQuery && csrfToken) {
            jQuery.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            });
        }

        // Tab Switching
        document.addEventListener('DOMContentLoaded', function() {
            // Main tabs
            const mainTabs = document.querySelectorAll('.auth-tab');
            mainTabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    mainTabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                });
            });

            // Sub tabs
            const subTabs = document.querySelectorAll('.auth-subtab');
            subTabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    subTabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                });
            });

            // Form Setup
            setupLoginForm();
        });
        
        // Loading Button Helper
        function setButtonLoading(button, loading = true) {
            if (loading) {
                button.classList.add('btn-loading');
                button.disabled = true;
                const spinner = button.querySelector('.loading-spinner');
                if (spinner) spinner.style.display = 'inline-block';
            } else {
                button.classList.remove('btn-loading');
                button.disabled = false;
                const spinner = button.querySelector('.loading-spinner');
                if (spinner) spinner.style.display = 'none';
            }
        }
        
        // Show/Hide Password Toggle
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = document.querySelector(`[onclick="togglePassword('${fieldId}')"] i`);
            
            if (field && icon) {
                if (field.type === 'password') {
                    field.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    field.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            }
        }
        
        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.transition = 'all 0.5s ease';
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-20px)';
                setTimeout(() => {
                    if (alert.parentNode) {
                        alert.remove();
                    }
                }, 500);
            });
        }, 5000);

        // Form validation helper
        function validateForm(formId) {
            const form = document.getElementById(formId);
            if (!form) return false;
            
            const inputs = form.querySelectorAll('input[required]');
            let isValid = true;
            
            inputs.forEach(input => {
                if (!input.value.trim()) {
                    input.classList.add('is-invalid');
                    isValid = false;
                } else {
                    input.classList.remove('is-invalid');
                }
            });
            
            return isValid;
        }

        // Setup Login Form
        function setupLoginForm() {
            const loginForm = document.getElementById('loginForm');
            const loginBtn = document.getElementById('loginBtn');
            
            if (!loginForm) return;
            
            // Form submission with validation
            loginForm.addEventListener('submit', function(e) {
                // Reset previous validation
                clearFormValidation();
                
                // Validate required fields
                if (!validateLoginForm()) {
                    e.preventDefault();
                    return false;
                }
                
                // Show loading state
                setButtonLoading(loginBtn, true);
                
                // Optional: Add timeout to prevent infinite loading
                setTimeout(() => {
                    if (loginBtn && loginBtn.classList.contains('btn-loading')) {
                        setButtonLoading(loginBtn, false);
                    }
                }, 10000); // 10 seconds timeout
            });
            
            // Real-time validation
            const emailField = document.getElementById('email');
            const passwordField = document.getElementById('password');
            
            if (emailField) {
                emailField.addEventListener('blur', function() {
                    validateEmail(this);
                });
                
                emailField.addEventListener('input', function() {
                    clearFieldValidation(this);
                });
            }
            
            if (passwordField) {
                passwordField.addEventListener('blur', function() {
                    validatePassword(this);
                });
                
                passwordField.addEventListener('input', function() {
                    clearFieldValidation(this);
                });
            }
        }

        function validateLoginForm() {
            const email = document.getElementById('email');
            const password = document.getElementById('password');
            let isValid = true;
            
            // Validate email
            if (email && !validateEmail(email)) {
                isValid = false;
            }
            
            // Validate password
            if (password && !validatePassword(password)) {
                isValid = false;
            }
            
            return isValid;
        }

        function validateEmail(field) {
            const value = field.value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (!value) {
                showFieldError(field, 'กรุณากรอกอีเมล');
                return false;
            }
            
            if (!emailRegex.test(value)) {
                showFieldError(field, 'รูปแบบอีเมลไม่ถูกต้อง');
                return false;
            }
            
            clearFieldValidation(field);
            return true;
        }

        function validatePassword(field) {
            const value = field.value.trim();
            
            if (!value) {
                showFieldError(field, 'กรุณากรอกรหัสผ่าน');
                return false;
            }
            
            if (value.length < 6) {
                showFieldError(field, 'รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร');
                return false;
            }
            
            clearFieldValidation(field);
            return true;
        }

        function showFieldError(field, message) {
            field.classList.add('is-invalid');
            
            // Find the error space container
            const formGroup = field.closest('.form-group-fixed');
            const errorSpace = formGroup.querySelector('.error-space');
            
            // Remove existing error message
            const existingError = errorSpace.querySelector('.invalid-feedback');
            if (existingError) {
                existingError.remove();
            }
            
            // Add new error message
            const errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback';
            errorDiv.textContent = message;
            errorSpace.appendChild(errorDiv);
        }

        function clearFieldValidation(field) {
            field.classList.remove('is-invalid');
            
            // Find the error space container
            const formGroup = field.closest('.form-group-fixed');
            if (formGroup) {
                const errorSpace = formGroup.querySelector('.error-space');
                const errorDiv = errorSpace.querySelector('.invalid-feedback');
                if (errorDiv && !errorDiv.getAttribute('data-server-error')) {
                    errorDiv.remove();
                }
            }
        }

        function clearFormValidation() {
            const invalidFields = document.querySelectorAll('.is-invalid');
            invalidFields.forEach(field => {
                clearFieldValidation(field);
            });
        }

        // Enhanced form experience
        document.addEventListener('DOMContentLoaded', function() {
            // Add focus effects to form controls
            const formControls = document.querySelectorAll('.form-control');
            formControls.forEach(control => {
                control.addEventListener('focus', function() {
                    this.closest('.form-group-fixed')?.classList.add('focused');
                });
                
                control.addEventListener('blur', function() {
                    this.closest('.form-group-fixed')?.classList.remove('focused');
                });
            });

            // Add loading state to form submission
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function() {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        setButtonLoading(submitBtn, true);
                    }
                });
            });
        });

        // Smooth scroll behavior
        document.documentElement.style.scrollBehavior = 'smooth';

        // Test login credentials (for development)
        @if(app()->environment('local'))
        function fillTestCredentials() {
            const emailField = document.getElementById('email');
            const passwordField = document.getElementById('password');
            if (emailField) emailField.value = 'admin@example.com';
            if (passwordField) passwordField.value = 'password';
        }

        // Add test button for development
        if (typeof window !== 'undefined') {
            console.log('🔑 Development Mode: Use fillTestCredentials() to auto-fill login');
        }
        @endif
    </script>
    
    @stack('scripts')
</body>
</html>
