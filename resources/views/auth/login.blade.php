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
            --tech-blue: #1e3a8a;
            --tech-dark: #0f172a;
        }

        * {
            font-family: 'Prompt', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, var(--primary-red) 0%, var(--primary-orange) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            overflow-x: hidden;
        }

        .login-container {
            max-width: 1200px;
            width: 100%;
            background: var(--white);
            border-radius: 25px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            min-height: 600px;
            position: relative;
        }

        .login-wrapper {
            display: flex;
            min-height: 600px;
        }

        /* Left Side - IT Services */
        .services-section {
            flex: 1;
            background: linear-gradient(135deg, var(--tech-dark) 0%, var(--tech-blue) 50%, var(--primary-red) 100%);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px 40px;
            color: var(--white);
            overflow: hidden;
        }

        .services-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><defs><pattern id="grid" width="50" height="50" patternUnits="userSpaceOnUse"><path d="M 50 0 L 0 0 0 50" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1"/></pattern></defs><rect width="100%" height="100%" fill="url(%23grid)"/></svg>');
            opacity: 0.3;
        }

        .tech-icons {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            overflow: hidden;
            pointer-events: none;
        }

        .tech-icon {
            position: absolute;
            color: rgba(255, 255, 255, 0.15);
            font-size: 3.5rem;
            animation: floatTech 8s ease-in-out infinite;
        }

        .tech-icon:nth-child(1) { 
            top: 10%; 
            left: 10%; 
            animation-delay: 0s; 
            font-size: 4rem;
        }
        .tech-icon:nth-child(2) { 
            top: 20%; 
            right: 15%; 
            animation-delay: 1.5s; 
            font-size: 3rem;
        }
        .tech-icon:nth-child(3) { 
            bottom: 30%; 
            left: 20%; 
            animation-delay: 3s; 
            font-size: 3.5rem;
        }
        .tech-icon:nth-child(4) { 
            bottom: 15%; 
            right: 10%; 
            animation-delay: 4.5s; 
            font-size: 2.5rem;
        }
        .tech-icon:nth-child(5) { 
            top: 50%; 
            left: 5%; 
            animation-delay: 6s; 
            font-size: 3rem;
        }
        .tech-icon:nth-child(6) { 
            top: 60%; 
            right: 5%; 
            animation-delay: 7.5s; 
            font-size: 4rem;
        }
        .tech-icon:nth-child(7) { 
            top: 35%; 
            left: 45%; 
            animation-delay: 2s; 
            font-size: 2rem;
        }
        .tech-icon:nth-child(8) { 
            bottom: 45%; 
            right: 35%; 
            animation-delay: 5s; 
            font-size: 2.5rem;
        }

        @keyframes floatTech {
            0%, 100% { 
                transform: translateY(0px) rotate(0deg) scale(1); 
                opacity: 0.15;
            }
            25% { 
                transform: translateY(-30px) rotate(90deg) scale(1.1); 
                opacity: 0.25;
            }
            50% { 
                transform: translateY(-60px) rotate(180deg) scale(1.2); 
                opacity: 0.3;
            }
            75% { 
                transform: translateY(-30px) rotate(270deg) scale(1.1); 
                opacity: 0.25;
            }
        }

        /* Floating particles */
        .floating-particles {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            pointer-events: none;
            overflow: hidden;
        }

        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            animation: floatParticle 15s linear infinite;
        }

        .particle:nth-child(1) { left: 10%; animation-delay: 0s; }
        .particle:nth-child(2) { left: 20%; animation-delay: 2s; }
        .particle:nth-child(3) { left: 30%; animation-delay: 4s; }
        .particle:nth-child(4) { left: 40%; animation-delay: 6s; }
        .particle:nth-child(5) { left: 50%; animation-delay: 8s; }
        .particle:nth-child(6) { left: 60%; animation-delay: 10s; }
        .particle:nth-child(7) { left: 70%; animation-delay: 12s; }
        .particle:nth-child(8) { left: 80%; animation-delay: 14s; }

        @keyframes floatParticle {
            0% {
                transform: translateY(100vh) scale(0);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                transform: translateY(-100vh) scale(1);
                opacity: 0;
            }
        }

        .services-content {
            text-align: center;
            z-index: 3;
            position: relative;
            max-width: 500px;
        }

        .services-content h1 {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 30px;
            text-shadow: 0 4px 8px rgba(0, 0, 0, 0.4);
            background: linear-gradient(45deg, var(--white), var(--primary-orange));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: titleGlow 3s ease-in-out infinite alternate;
        }

        @keyframes titleGlow {
            0% {
                filter: brightness(1);
                transform: scale(1);
            }
            100% {
                filter: brightness(1.1);
                transform: scale(1.02);
            }
        }

        .services-content .main-subtitle {
            font-size: 1.4rem;
            margin-bottom: 15px;
            opacity: 0.95;
            line-height: 1.7;
            font-weight: 500;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .services-content .sub-subtitle {
            font-size: 1.1rem;
            margin-bottom: 20px;
            opacity: 0.85;
            line-height: 1.6;
            font-weight: 400;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.4);
        }

        .services-content .english-subtitle {
            font-size: 1.2rem;
            margin-bottom: 40px;
            opacity: 0.9;
            font-style: italic;
            font-weight: 500;
            color: var(--primary-orange);
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            animation: subtitleFade 4s ease-in-out infinite alternate;
        }

        @keyframes subtitleFade {
            0% { opacity: 0.8; }
            100% { opacity: 1; }
        }

        /* System Features Icons */
        .system-features {
            margin-top: 50px;
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: var(--primary-orange);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
            animation: iconPulse 2s ease-in-out infinite;
        }

        .feature-icon:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: scale(1.1);
            box-shadow: 0 0 20px rgba(230, 149, 42, 0.5);
        }

        @keyframes iconPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .feature-icon:nth-child(1) { animation-delay: 0s; }
        .feature-icon:nth-child(2) { animation-delay: 0.5s; }
        .feature-icon:nth-child(3) { animation-delay: 1s; }
        .feature-icon:nth-child(4) { animation-delay: 1.5s; }
        .feature-icon:nth-child(5) { animation-delay: 2s; }

        /* Right Side - Login Form */
        .login-section {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px 40px;
            background: var(--white);
            position: relative;
        }

        .login-form-container {
            width: 100%;
            max-width: 400px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .login-header h2 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-red);
            margin-bottom: 10px;
        }

        .login-header p {
            color: var(--gray);
            font-size: 1rem;
            font-weight: 500;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--dark-gray);
            font-size: 0.95rem;
        }

        .form-control {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-red);
            background: var(--white);
            box-shadow: 0 0 0 0.2rem rgba(181, 69, 68, 0.15);
        }

        .form-control::placeholder {
            color: #adb5bd;
            font-size: 0.9rem;
        }

        .password-toggle {
            position: relative;
        }

        .password-toggle-btn {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--gray);
            cursor: pointer;
            font-size: 1.1rem;
            transition: color 0.3s ease;
        }

        .password-toggle-btn:hover {
            color: var(--primary-red);
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            font-size: 0.9rem;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .remember-me input[type="checkbox"] {
            width: 18px;
            height: 18px;
            border: 2px solid var(--gray);
            border-radius: 4px;
            cursor: pointer;
        }

        .remember-me input[type="checkbox"]:checked {
            background: var(--primary-red);
            border-color: var(--primary-red);
        }

        .forgot-password {
            color: var(--primary-red);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .forgot-password:hover {
            color: var(--accent-red);
        }

        .btn-login {
            width: 100%;
            padding: 15px 30px;
            background: linear-gradient(45deg, var(--primary-red), var(--primary-orange));
            color: var(--white);
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(181, 69, 68, 0.3);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(181, 69, 68, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .btn-login.loading {
            opacity: 0.8;
            pointer-events: none;
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

        .alert {
            padding: 15px 20px;
            margin-bottom: 25px;
            border-radius: 10px;
            border: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
        }

        .alert-success {
            background: #d1edff;
            color: #0c5460;
        }

        .footer-info {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }

        .footer-info p {
            color: var(--gray);
            font-size: 0.85rem;
            margin-bottom: 5px;
        }

        .footer-info .brand {
            color: var(--primary-red);
            font-weight: 600;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .login-wrapper {
                flex-direction: column;
            }
            
            .services-section {
                padding: 40px 30px;
                min-height: 300px;
            }
            
            .services-content h1 {
                font-size: 2.5rem;
            }
            
            .system-features {
                margin-top: 30px;
                gap: 20px;
            }
            
            .feature-icon {
                width: 50px;
                height: 50px;
                font-size: 1.2rem;
            }
            
            .login-section {
                padding: 40px 30px;
            }
        }

        @media (max-width: 768px) {
            .login-container {
                margin: 10px;
                border-radius: 20px;
            }
            
            .services-section {
                padding: 30px 20px;
            }
            
            .services-content h1 {
                font-size: 2rem;
            }
            
            .services-content .main-subtitle {
                font-size: 1.2rem;
            }
            
            .services-content .sub-subtitle {
                font-size: 1rem;
            }
            
            .login-section {
                padding: 30px 20px;
            }
            
            .login-header h2 {
                font-size: 1.5rem;
            }
        }

        /* Animation */
        .login-container {
            animation: slideIn 0.8s ease-out;
        }

        @keyframes slideIn {
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
    <div class="login-container">
        <div class="login-wrapper">
            <!-- Left Side - IT Services -->
            <div class="services-section">
                <!-- Tech Icons Background -->
                <div class="tech-icons">
                    <i class="fas fa-server tech-icon"></i>
                    <i class="fas fa-laptop tech-icon"></i>
                    <i class="fas fa-network-wired tech-icon"></i>
                    <i class="fas fa-shield-alt tech-icon"></i>
                    <i class="fas fa-cloud tech-icon"></i>
                    <i class="fas fa-cogs tech-icon"></i>
                    <i class="fas fa-database tech-icon"></i>
                    <i class="fas fa-wifi tech-icon"></i>
                </div>

                <!-- Floating Particles -->
                <div class="floating-particles">
                    <div class="particle"></div>
                    <div class="particle"></div>
                    <div class="particle"></div>
                    <div class="particle"></div>
                    <div class="particle"></div>
                    <div class="particle"></div>
                    <div class="particle"></div>
                    <div class="particle"></div>
                </div>
                
                <div class="services-content">
                    <h1>บริการออนไลน์</h1>
                    <p class="main-subtitle">
                        ให้คุณจัดการกลุ่มเรื่องต่าง ๆ ด้วยตัวคุณเอง
                    </p>
                    <p class="sub-subtitle">
                        ตลอด 24 ชั่วโมง<br>
                        ระบบจัดการ IT ที่ครอบคลุมและใช้งานง่าย
                    </p>
                    <p class="english-subtitle">
                        Professional IT Management Solutions
                    </p>
                    
                    <div class="system-features">
                        <div class="feature-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="feature-icon">
                            <i class="fas fa-laptop"></i>
                        </div>
                        <div class="feature-icon">
                            <i class="fas fa-ticket-alt"></i>
                        </div>
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side - Login Form -->
            <div class="login-section">
                <div class="login-form-container">
                    <div class="login-header">
                        <h2>เข้าสู่ระบบ</h2>
                        <p>IT Management System</p>
                    </div>

                    <!-- Alerts -->
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i>
                            <span>{{ $errors->first() }}</span>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            <i class="fas fa-times-circle"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif

                    <!-- Login Form -->
                    <form method="POST" action="{{ route('login.store') }}" id="loginForm">
                        @csrf
                        
                        <div class="form-group">
                            <label for="email">อีเมล</label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   placeholder="กรุณากรอกอีเมล" 
                                   required 
                                   autofocus>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password">รหัสผ่าน</label>
                            <div class="password-toggle">
                                <input type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       placeholder="กรุณากรอกรหัสผ่าน" 
                                       required>
                                <button type="button" class="password-toggle-btn" onclick="togglePassword()">
                                    <i class="fas fa-eye" id="passwordIcon"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="remember-forgot">
                            <label class="remember-me">
                                <input type="checkbox" name="remember" id="remember">
                                <span>จดจำการเข้าสู่ระบบ</span>
                            </label>
                        </div>

                        <button type="submit" class="btn-login" id="submitBtn">
                            <i class="fas fa-sign-in-alt"></i> เข้าสู่ระบบ
                        </button>
                    </form>

                    <div class="footer-info">
                        <p>© {{ date('Y') }} <span class="brand">IT Management System</span></p>
                        <p>System By Rungaroon <em>"Solution"</em></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Password toggle functionality
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('passwordIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.classList.remove('fa-eye');
                passwordIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordIcon.classList.remove('fa-eye-slash');
                passwordIcon.classList.add('fa-eye');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('loginForm');
            const submitBtn = document.getElementById('submitBtn');
            
            // Handle form submission
            form.addEventListener('submit', function() {
                submitBtn.classList.add('loading');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> กำลังเข้าสู่ระบบ...';
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

            // Add focus effects
            const inputs = document.querySelectorAll('.form-control');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'translateY(-2px)';
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'translateY(0)';
                });
            });
        });
    </script>
</body>
</html>