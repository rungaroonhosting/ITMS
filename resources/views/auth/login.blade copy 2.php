<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>เข้าสู่ระบบ - IT Management System</title>
    
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
            --primary-red: #B54544;
            --primary-orange: #E6952A;
            --light-red: #C55B5A;
            --dark-orange: #D18A2B;
            --accent-red: #A33E3D;
            --accent-orange: #CC7F1F;
            --white: #FFFFFF;
            --light-gray: #F5F7FA;
            --gray: #5A6C7D;
            --dark-gray: #2C3E50;
        }

        * {
            font-family: 'Prompt', sans-serif;
        }

        body {
            background: linear-gradient(135deg, var(--primary-red) 0%, var(--primary-orange) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }

        .login-container {
            max-width: 450px;
            width: 100%;
        }

        .login-card {
            background: var(--white);
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            border: none;
        }

        .login-header {
            background: linear-gradient(45deg, var(--primary-red), var(--primary-orange));
            color: var(--white);
            text-align: center;
            padding: 40px 30px;
        }

        .login-header h1 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .login-header p {
            font-size: 0.95rem;
            opacity: 0.9;
            margin-bottom: 0;
        }

        .login-body {
            padding: 40px 30px;
        }

        .form-floating {
            margin-bottom: 20px;
        }

        .form-control {
            border-radius: 12px;
            border: 2px solid #e9ecef;
            padding: 12px 16px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-red);
            box-shadow: 0 0 0 0.2rem rgba(181, 69, 68, 0.25);
        }

        .form-floating > label {
            padding: 1rem 0.75rem;
            color: var(--gray);
        }

        .btn-login {
            background: linear-gradient(45deg, var(--primary-red), var(--primary-orange));
            border: none;
            border-radius: 12px;
            padding: 14px 30px;
            font-weight: 600;
            font-size: 1.1rem;
            color: var(--white);
            width: 100%;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(181, 69, 68, 0.4);
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(181, 69, 68, 0.5);
            filter: brightness(1.1);
            color: var(--white);
        }

        .btn-login:focus {
            color: var(--white);
            box-shadow: 0 0 0 0.25rem rgba(181, 69, 68, 0.5);
        }

        .remember-me {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
        }

        .form-check-input:checked {
            background-color: var(--primary-red);
            border-color: var(--primary-red);
        }

        .form-check-label {
            color: var(--gray);
            margin-left: 8px;
            font-size: 0.95rem;
        }

        .alert {
            border-radius: 12px;
            border: none;
            padding: 15px 20px;
            margin-bottom: 25px;
            font-weight: 500;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        .alert-success {
            background-color: #d1edff;
            color: #0c5460;
        }

        .system-info {
            background: var(--light-gray);
            border-radius: 12px;
            padding: 20px;
            margin-top: 30px;
            text-align: center;
        }

        .system-info h6 {
            color: var(--primary-red);
            font-weight: 600;
            margin-bottom: 15px;
        }

        .demo-accounts {
            font-size: 0.85rem;
            color: var(--gray);
        }

        .demo-accounts .account {
            background: var(--white);
            border-radius: 8px;
            padding: 8px 12px;
            margin: 5px 0;
            border-left: 4px solid var(--primary-orange);
        }

        .footer-info {
            text-align: center;
            margin-top: 30px;
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
        }

        /* Loading state */
        .btn-login.loading {
            pointer-events: none;
            opacity: 0.8;
        }

        .btn-login.loading::after {
            content: '';
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid transparent;
            border-top: 2px solid currentColor;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-left: 10px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Responsive */
        @media (max-width: 480px) {
            .login-header {
                padding: 30px 20px;
            }
            
            .login-body {
                padding: 30px 20px;
            }
            
            .login-header h1 {
                font-size: 1.5rem;
            }
        }

        /* Animation */
        .login-card {
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            0% {
                opacity: 0;
                transform: translateY(30px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="card login-card">
            <!-- Header -->
            <div class="login-header">
                <h1><i class="fas fa-cogs me-2"></i>ITMS</h1>
                <p>IT Management System</p>
            </div>

            <!-- Body -->
            <div class="login-body">
                <!-- Alerts -->
                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        {{ $errors->first() }}
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-times-circle me-2"></i>
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Login Form -->
                <form method="POST" action="{{ route('login.store') }}" id="loginForm">
                    @csrf
                    
                    <!-- Email -->
                    <div class="form-floating">
                        <input type="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               placeholder="name@example.com" 
                               required 
                               autofocus>
                        <label for="email">
                            <i class="fas fa-envelope me-2"></i>อีเมล
                        </label>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="form-floating">
                        <input type="password" 
                               class="form-control @error('password') is-invalid @enderror" 
                               id="password" 
                               name="password" 
                               placeholder="Password" 
                               required>
                        <label for="password">
                            <i class="fas fa-lock me-2"></i>รหัสผ่าน
                        </label>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="remember-me">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">
                            จดจำการเข้าสู่ระบบ
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-login" id="submitBtn">
                        <i class="fas fa-sign-in-alt me-2"></i>เข้าสู่ระบบ
                    </button>
                </form>

                <!-- Demo Accounts Info -->
                <div class="system-info">
                    <h6><i class="fas fa-info-circle me-2"></i>บัญชีทดสอบ</h6>
                    <div class="demo-accounts">
                        <div class="account">
                            <strong>Super Admin:</strong> admin@bettersystem.co.th / password123
                        </div>
                        <div class="account">
                            <strong>IT Admin:</strong> itadmin@bettersystem.co.th / password123
                        </div>
                        <div class="account">
                            <strong>HR:</strong> somying.j@bettersystem.co.th / password123
                        </div>
                        <div class="account">
                            <strong>Express:</strong> somchai.b@bettersystem.co.th / password123
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer-info">
            <p><i class="fas fa-shield-alt me-2"></i>ระบบปลอดภัยด้วย Laravel & Bootstrap</p>
            <p>&copy; {{ date('Y') }} Better System Co., Ltd. All rights reserved.</p>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('loginForm');
            const submitBtn = document.getElementById('submitBtn');
            
            form.addEventListener('submit', function() {
                submitBtn.classList.add('loading');
                submitBtn.disabled = true;
            });
            
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
        });
    </script>
</body>
</html>