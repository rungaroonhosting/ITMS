<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - ITMS</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">


    <!-- Google Fonts - Prompt -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* Red to Orange Gradient Theme - Darker Version */
        :root {
            /* Primary Colors - Made Darker */
            --primary-red: #B54544;         /* เข้มขึ้นจากเดิม */
            --primary-orange: #E6952A;      /* เข้มขึ้นจากเดิม */
            --light-red: #C55B5A;           /* เข้มขึ้นจากเดิม */
            --dark-orange: #D18A2B;         /* เข้มขึ้นจากเดิม */
            --accent-red: #A33E3D;          /* สีแดงเข้มมาก */
            --accent-orange: #CC7F1F;       /* สีส้มเข้มมาก */
            
            /* Supporting Colors */
            --white: #FFFFFF;
            --light-gray: #F5F7FA;          /* เข้มขึ้นเล็กน้อย */
            --gray: #5A6C7D;                /* เข้มขึ้น */
            --dark-gray: #2C3E50;           /* เข้มขึ้นมาก */
            --text-primary: #1A252F;        /* เข้มขึ้น */
            --text-secondary: #5A6C7D;      /* เข้มขึ้น */
            --border-color: #D1D9E6;        /* เข้มขึ้นเล็กน้อย */
            
            /* Status Colors */
            --success: #1E7E34;             /* เข้มขึ้น */
            --danger: #C82333;              /* เข้มขึ้น */
            --warning: #E0A800;             /* เข้มขึ้น */
            --info: #138496;                /* เข้มขึ้น */
            
            /* Dashboard Specific */
            --sidebar-bg: linear-gradient(180deg, #B54544 0%, #E6952A 100%);
            --content-bg: #F5F7FA;
            --card-shadow: 0 6px 20px rgba(181, 69, 68, 0.15);
            --hover-shadow: 0 10px 30px rgba(181, 69, 68, 0.25);
        }

        * {
            font-family: 'Prompt', sans-serif;
        }

        body {
            background: var(--content-bg);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            color: var(--text-primary);
        }

        /* Sidebar Styles */
        .sidebar {
            background: var(--sidebar-bg);
            min-height: 100vh;
            width: 280px;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
            transition: all 0.3s ease;
            box-shadow: 6px 0 20px rgba(181, 69, 68, 0.3);
        }

        .sidebar.collapsed {
            width: 80px;
        }

        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
            background: rgba(0, 0, 0, 0.1);
        }

        .sidebar-header h4 {
            color: var(--white);
            font-weight: 700;
            margin: 0;
            transition: all 0.3s ease;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .sidebar.collapsed .sidebar-header h4 {
            font-size: 0;
        }

        .sidebar-header .subtitle {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.9rem;
            margin-top: 5px;
            transition: all 0.3s ease;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        }

        .sidebar.collapsed .sidebar-header .subtitle {
            font-size: 0;
        }

        /* Navigation Styles */
        .nav-menu {
            padding: 20px 0;
        }

        .nav-section {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 15px;
            padding-bottom: 15px;
        }

        .nav-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .nav-section-title {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 8px 20px;
            margin-bottom: 8px;
        }

        .sidebar.collapsed .nav-section-title {
            display: none;
        }

        .nav-item {
            margin-bottom: 3px;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.95);
            padding: 14px 20px;
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: all 0.3s ease;
            border-radius: 0;
            position: relative;
            font-weight: 500;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.15);
            color: var(--white);
            transform: translateX(8px);
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
        }

        .nav-link.active {
            background: rgba(255, 255, 255, 0.25);
            color: var(--white);
            border-right: 5px solid var(--white);
            font-weight: 600;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
        }

        .nav-link i {
            width: 20px;
            margin-right: 15px;
            text-align: center;
            transition: all 0.3s ease;
            font-size: 1.1rem;
        }

        .sidebar.collapsed .nav-link {
            padding: 14px 30px;
            justify-content: center;
        }

        .sidebar.collapsed .nav-link span {
            display: none;
        }

        .sidebar.collapsed .nav-link i {
            margin-right: 0;
        }

        /* Express Badge Animation */
        .express-badge {
            background: linear-gradient(45deg, #FFD700, #FFA500);
            color: #000;
            font-size: 0.7rem;
            padding: 2px 6px;
            border-radius: 8px;
            font-weight: 700;
            animation: expressGlow 2s infinite alternate;
            margin-left: auto;
        }

        @keyframes expressGlow {
            from { box-shadow: 0 0 5px rgba(255, 215, 0, 0.5); }
            to { box-shadow: 0 0 15px rgba(255, 215, 0, 0.8); }
        }

        .sidebar.collapsed .express-badge {
            display: none;
        }

        /* User Profile in Sidebar */
        .user-profile {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            background: rgba(0, 0, 0, 0.15);
        }

        .user-profile .dropdown-toggle {
            color: var(--white);
            text-decoration: none;
            display: flex;
            align-items: center;
            width: 100%;
            padding: 12px;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .user-profile .dropdown-toggle:hover {
            background: rgba(255, 255, 255, 0.15);
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
        }

        .user-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            color: var(--white);
            font-size: 1.3rem;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .sidebar.collapsed .user-profile .user-info {
            display: none;
        }

        .sidebar.collapsed .user-avatar {
            margin-right: 0;
        }

        /* Main Content */
        .main-content {
            margin-left: 280px;
            transition: all 0.3s ease;
            min-height: 100vh;
        }

        .main-content.expanded {
            margin-left: 80px;
        }

        /* Top Navigation */
        .top-nav {
            background: var(--white);
            padding: 18px 30px;
            box-shadow: var(--card-shadow);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 999;
            border-bottom: 3px solid var(--primary-red);
        }

        .sidebar-toggle {
            background: none;
            border: none;
            color: var(--primary-red);
            font-size: 1.3rem;
            padding: 10px;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .sidebar-toggle:hover {
            background: rgba(181, 69, 68, 0.15);
            color: var(--accent-red);
            transform: scale(1.1);
        }

        .breadcrumb-custom {
            background: none;
            padding: 0;
            margin: 0;
        }

        .breadcrumb-custom .breadcrumb-item {
            color: var(--text-secondary);
            font-weight: 500;
        }

        .breadcrumb-custom .breadcrumb-item.active {
            color: var(--primary-red);
            font-weight: 600;
        }

        .breadcrumb-custom .breadcrumb-item + .breadcrumb-item::before {
            color: var(--text-secondary);
        }

        /* Content Area */
        .content-area {
            padding: 30px;
        }

        /* Card Styles */
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            overflow: hidden;
            border: 1px solid rgba(181, 69, 68, 0.1);
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: var(--hover-shadow);
        }

        .card-header {
            background: linear-gradient(45deg, var(--primary-red), var(--primary-orange));
            color: var(--white);
            border: none;
            padding: 22px;
            font-weight: 600;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
        }

        .card-body {
            padding: 28px;
            background: var(--white);
        }

        /* Stat Cards */
        .stat-card {
            background: var(--white);
            transition: all 0.3s ease;
            cursor: pointer;
            border: 2px solid rgba(181, 69, 68, 0.1);
        }

        .stat-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 15px 35px rgba(181, 69, 68, 0.25);
            border-color: rgba(181, 69, 68, 0.2);
        }

        .stat-card .card-body {
            padding: 32px 22px;
        }

        .stat-card i {
            color: var(--primary-orange);
            margin-bottom: 16px;
            transition: all 0.3s ease;
        }

        .stat-card:hover i {
            color: var(--accent-red);
            transform: scale(1.15);
        }

        .stat-card h3 {
            font-size: 2.4rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 8px;
        }

        .stat-card p {
            color: var(--text-secondary);
            font-weight: 600;
            font-size: 0.95rem;
        }

        /* Buttons */
        .btn-primary {
            background: linear-gradient(45deg, var(--primary-red), var(--primary-orange));
            border: none;
            border-radius: 12px;
            padding: 14px 28px;
            font-weight: 600;
            color: var(--white);
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(181, 69, 68, 0.4);
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(181, 69, 68, 0.5);
            filter: brightness(1.15);
            color: var(--white);
        }

        .btn-outline-primary {
            border: 2px solid var(--primary-red);
            color: var(--primary-red);
            border-radius: 12px;
            padding: 14px 28px;
            font-weight: 600;
            transition: all 0.3s ease;
            background: transparent;
        }

        .btn-outline-primary:hover {
            background: linear-gradient(45deg, var(--primary-red), var(--primary-orange));
            border-color: var(--primary-red);
            color: var(--white);
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(181, 69, 68, 0.3);
        }

        /* Alerts */
        .alert {
            border-radius: 14px;
            border: none;
            padding: 18px 22px;
            font-weight: 500;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .alert-success {
            background: linear-gradient(45deg, var(--success), #2ECC71);
            color: var(--white);
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        }

        .alert-danger {
            background: linear-gradient(45deg, var(--danger), #E74C3C);
            color: var(--white);
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        }

        .alert-warning {
            background: linear-gradient(45deg, var(--warning), #F39C12);
            color: var(--white);
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        }

        .alert-info {
            background: linear-gradient(45deg, var(--primary-red), var(--primary-orange));
            color: var(--white);
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        }

        /* Badges */
        .badge {
            border-radius: 10px;
            padding: 10px 14px;
            font-weight: 600;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        }

        .badge.bg-primary {
            background: linear-gradient(45deg, var(--primary-red), var(--primary-orange)) !important;
        }

        /* Text Colors */
        .text-primary {
            color: var(--primary-red) !important;
        }

        .link-primary {
            color: var(--primary-orange);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .link-primary:hover {
            color: var(--accent-red);
            text-decoration: underline;
        }

        /* Dropdown Menus */
        .dropdown-menu {
            border: none;
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            padding: 12px 0;
            border: 1px solid rgba(181, 69, 68, 0.1);
        }

        .dropdown-item {
            padding: 12px 22px;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .dropdown-item:hover {
            background: linear-gradient(45deg, var(--primary-red), var(--primary-orange));
            color: var(--white);
        }

        /* Role Badge Styles */
        .role-badge {
            padding: 6px 14px;
            border-radius: 25px;
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        }

        .role-badge.super-admin {
            background: linear-gradient(45deg, #FFD700, #FFA500);
            color: #000;
            text-shadow: none;
            animation: adminGlow 2s infinite alternate;
        }

        @keyframes adminGlow {
            from { box-shadow: 0 0 5px rgba(255, 215, 0, 0.5); }
            to { box-shadow: 0 0 15px rgba(255, 215, 0, 0.8); }
        }

        .role-badge.it-admin {
            background: linear-gradient(45deg, var(--primary-red), var(--primary-orange));
            color: var(--white);
        }

        .role-badge.express {
            background: linear-gradient(45deg, #FFD700, #FFA500);
            color: #000;
            text-shadow: none;
            animation: expressGlow 3s infinite alternate;
        }

        .role-badge.employee {
            background: linear-gradient(45deg, var(--info), #20C997);
            color: var(--white);
        }

        /* Progress bars */
        .progress {
            background-color: rgba(181, 69, 68, 0.1);
            border-radius: 8px;
        }

        .progress-bar {
            background: linear-gradient(45deg, var(--primary-red), var(--primary-orange));
            border-radius: 8px;
        }

        /* List groups */
        .list-group-item {
            border: none;
            border-bottom: 1px solid rgba(181, 69, 68, 0.1);
            padding: 12px 0;
            font-weight: 500;
        }

        /* Responsive Design */
        @media (max-width: 991px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .main-content.expanded {
                margin-left: 0;
            }
            
            .content-area {
                padding: 20px;
            }
        }

        @media (max-width: 767px) {
            .top-nav {
                padding: 15px 20px;
            }
            
            .content-area {
                padding: 15px;
            }
            
            .stat-card .card-body {
                padding: 22px 18px;
            }
            
            .stat-card h3 {
                font-size: 2rem;
            }
        }

        /* Animation for page load */
        .content-area {
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

        /* Mobile overlay */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: 999;
            display: none;
        }

        .sidebar-overlay.show {
            display: block;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <!-- Sidebar Header -->
        <div class="sidebar-header">
            <h4>ITMS</h4>
            <div class="subtitle">IT Management System</div>
        </div>

        <!-- Navigation Menu -->
        <div class="nav-menu">
            <!-- Dashboard -->
            <div class="nav-section">
                <div class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                </div>
            </div>
            
            @php $userRole = auth()->user()->role ?? 'employee'; @endphp
            
            <!-- Employee & Department Management -->
            @if($userRole === 'super_admin' || $userRole === 'it_admin' || $userRole === 'hr' || $userRole === 'express')
                <div class="nav-section">
                    <div class="nav-section-title">
                        <i class="fas fa-users me-1"></i>จัดการบุคลากร
                    </div>
                    
                    <!-- Employee Management -->
                    <div class="nav-item">
                        <a href="{{ route('employees.index') }}" class="nav-link {{ request()->routeIs('employees.*') ? 'active' : '' }}">
                            <i class="fas fa-users"></i>
                            <span>จัดการพนักงาน</span>
                            @if($userRole === 'express')
                                <span class="express-badge">EXPRESS</span>
                            @endif
                        </a>
                    </div>
                    
                    <!-- Department Management (Admin Only) -->
                    @if($userRole === 'super_admin' || $userRole === 'it_admin')
                        <div class="nav-item">
                            <a href="{{ route('departments.index') }}" class="nav-link {{ request()->routeIs('departments.*') ? 'active' : '' }}">
                                <i class="fas fa-building"></i>
                                <span>จัดการแผนก</span>
                            </a>
                        </div>
                    @endif
                </div>
            @endif
            
            <!-- IT Management (Super Admin & IT Admin Only) -->
            @if($userRole === 'super_admin' || $userRole === 'it_admin')
                <div class="nav-section">
                    <div class="nav-section-title">
                        <i class="fas fa-cogs me-1"></i>จัดการ IT
                    </div>
                    
                    <div class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-laptop"></i>
                            <span>จัดการอุปกรณ์ IT</span>
                        </a>
                    </div>
                    
                    <div class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-file-contract"></i>
                            <span>จัดการข้อตกลง IT</span>
                        </a>
                    </div>
                    
                    <div class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-tools"></i>
                            <span>จัดการแจ้งซ่อม</span>
                        </a>
                    </div>
                    
                    <div class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-ticket-alt"></i>
                            <span>จัดการคำขอบริการ</span>
                        </a>
                    </div>
                    
                    <div class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-chart-bar"></i>
                            <span>รายงานทั้งหมด</span>
                        </a>
                    </div>
                </div>
            @endif
            
            <!-- Employee Services -->
            @if($userRole === 'employee' || $userRole === 'express')
                <div class="nav-section">
                    <div class="nav-section-title">
                        <i class="fas fa-user me-1"></i>บริการพนักงาน
                    </div>
                    
                    <div class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-user"></i>
                            <span>ข้อมูลตัวเอง</span>
                        </a>
                    </div>
                    
                    <div class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-file-signature"></i>
                            <span>เซ็นข้อตกลง IT</span>
                        </a>
                    </div>
                    
                    <div class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-tools"></i>
                            <span>แจ้งซ่อม</span>
                        </a>
                    </div>
                    
                    <div class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-ticket-alt"></i>
                            <span>ขอใช้บริการ</span>
                        </a>
                    </div>
                    
                    <div class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-tasks"></i>
                            <span>ติดตามสถานะงาน</span>
                        </a>
                    </div>
                </div>
            @endif
            
            <!-- Express Special Section -->
            @if($userRole === 'express')
                <div class="nav-section">
                    <div class="nav-section-title">
                        <i class="fas fa-bolt me-1"></i>Express Services
                    </div>
                    
                    <div class="nav-item">
                        <a href="{{ route('employees.create') }}" class="nav-link">
                            <i class="fas fa-user-plus"></i>
                            <span>เพิ่มพนักงานบัญชี</span>
                            <span class="express-badge">FAST</span>
                        </a>
                    </div>
                    
                    <div class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-calculator"></i>
                            <span>ระบบบัญชี Express</span>
                            <span class="express-badge">NEW</span>
                        </a>
                    </div>
                </div>
            @endif
            
            <!-- Quick Actions -->
            @if($userRole === 'super_admin' || $userRole === 'it_admin')
                <div class="nav-section">
                    <div class="nav-section-title">
                        <i class="fas fa-plus me-1"></i>เพิ่มข้อมูล
                    </div>
                    
                    <div class="nav-item">
                        <a href="{{ route('employees.create') }}" class="nav-link">
                            <i class="fas fa-user-plus"></i>
                            <span>เพิ่มพนักงาน</span>
                        </a>
                    </div>
                    
                    <div class="nav-item">
                        <a href="{{ route('departments.create') }}" class="nav-link">
                            <i class="fas fa-building"></i>
                            <span>เพิ่มแผนก</span>
                        </a>
                    </div>
                </div>
            @endif
            
            <!-- System Management (Super Admin Only) -->
            @if($userRole === 'super_admin')
                <div class="nav-section">
                    <div class="nav-section-title">
                        <i class="fas fa-crown me-1"></i>ระบบขั้นสูง
                    </div>
                    
                    <div class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-cog"></i>
                            <span>ตั้งค่าระบบ</span>
                        </a>
                    </div>
                    
                    <div class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-users-cog"></i>
                            <span>จัดการผู้ใช้งาน</span>
                        </a>
                    </div>
                    
                    <div class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-database"></i>
                            <span>จัดการฐานข้อมูล</span>
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- User Profile -->
        <div class="user-profile">
            <div class="dropdown">
                <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="user-avatar">
                        @if($userRole === 'super_admin')
                            <i class="fas fa-crown"></i>
                        @elseif($userRole === 'it_admin')
                            <i class="fas fa-laptop-code"></i>
                        @elseif($userRole === 'express')
                            <i class="fas fa-bolt"></i>
                        @else
                            <i class="fas fa-user"></i>
                        @endif
                    </div>
                    <div class="user-info">
                        <div style="color: white; font-weight: 500; font-size: 0.9rem;">
                            {{ auth()->user()->first_name_th ?? auth()->user()->name ?? 'User' }}
                        </div>
                        <div style="color: rgba(255,255,255,0.8); font-size: 0.8rem;">
                            @php
                                $roleDisplay = [
                                    'super_admin' => 'Super Admin',
                                    'it_admin' => 'IT Admin',
                                    'hr' => 'HR',
                                    'manager' => 'Manager',
                                    'express' => 'Express',
                                    'employee' => 'Employee'
                                ];
                                echo $roleDisplay[$userRole] ?? 'Employee';
                            @endphp
                        </div>
                    </div>
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>โปรไฟล์</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>ตั้งค่า</a></li>
                    @if($userRole === 'express')
                        <li><a class="dropdown-item" href="#"><i class="fas fa-bolt me-2"></i>ตั้งค่า Express</a></li>
                    @endif
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="fas fa-sign-out-alt me-2"></i>ออกจากระบบ
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Main Content -->
    <main class="main-content" id="mainContent">
        <!-- Top Navigation -->
        <nav class="top-nav">
            <div class="d-flex align-items-center">
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                
                <nav aria-label="breadcrumb" class="ms-3">
                    <ol class="breadcrumb breadcrumb-custom">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">หน้าหลัก</a></li>
                        @yield('breadcrumb')
                    </ol>
                </nav>
            </div>
            
            <div class="d-flex align-items-center">
                <span class="text-muted me-3">{{ now()->locale('th')->translatedFormat('l, d F Y') }}</span>
                <span class="role-badge {{ str_replace('_', '-', $userRole) }}">
                    @php
                        echo $roleDisplay[$userRole] ?? 'Employee';
                    @endphp
                </span>
                <div class="dropdown ms-3">
                    <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-bell me-1"></i>
                        แจ้งเตือน
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#">ไม่มีการแจ้งเตือนใหม่</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Content Area -->
        <div class="content-area">
            <!-- Alerts -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Page Content -->
            @yield('content')
        </div>
    </main>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // CSRF Token Setup
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            // Sidebar Toggle Function
            function toggleSidebar() {
                if (window.innerWidth <= 991) {
                    // Mobile behavior
                    sidebar.classList.toggle('show');
                    sidebarOverlay.classList.toggle('show');
                } else {
                    // Desktop behavior
                    sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('expanded');
                }
            }

            // Toggle sidebar
            sidebarToggle.addEventListener('click', toggleSidebar);

            // Close sidebar when clicking overlay
            sidebarOverlay.addEventListener('click', function() {
                sidebar.classList.remove('show');
                sidebarOverlay.classList.remove('show');
            });

            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth > 991) {
                    sidebar.classList.remove('show');
                    sidebarOverlay.classList.remove('show');
                }
            });

            // Auto-hide alerts after 5 seconds
            setTimeout(() => {
                const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
                alerts.forEach(alert => {
                    if (alert.querySelector('.btn-close')) {
                        alert.style.transition = 'all 0.5s ease';
                        alert.style.opacity = '0';
                        alert.style.transform = 'translateY(-20px)';
                        setTimeout(() => {
                            if (alert.parentNode) {
                                alert.remove();
                            }
                        }, 500);
                    }
                });
            }, 5000);

            // Add click effect to stat cards
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach(card => {
                card.addEventListener('click', function() {
                    this.style.transform = 'scale(0.98)';
                    setTimeout(() => {
                        this.style.transform = '';
                    }, 150);
                });
            });

            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Role-specific console messages
            @if($userRole === 'super_admin')
                console.log('🔧 Super Admin Access: Full system control');
                console.log('👑 Permissions: All modules, User management, System settings');
            @elseif($userRole === 'it_admin')
                console.log('💻 IT Admin Access: IT management focus');
                console.log('🔧 Permissions: Employee management, Department management, IT modules');
            @elseif($userRole === 'express')
                console.log('⚡ Express User Access: Accounting department focus');
                console.log('📊 Permissions: Employee management (Accounting only), Express features');
            @elseif($userRole === 'hr')
                console.log('👥 HR Access: Human resources focus');
                console.log('📋 Permissions: Employee management, HR modules');
            @else
                console.log('👤 Employee Access: Standard user permissions');
                console.log('📄 Permissions: Personal profile, Service requests');
            @endif
        });

        // Global error handler
        window.addEventListener('error', function(e) {
            console.error('JavaScript Error:', e.error);
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('scripts')
</body>
</html>