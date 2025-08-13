@extends('layouts.app')

@section('title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<!-- Welcome Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title mb-0">
                    <i class="fas fa-tachometer-alt me-2"></i>
                    Dashboard - IT Management System v2.1
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h4 class="text-primary mb-3">
                            ยินดีต้อนรับ, {{ auth()->user()->full_name_th ?? auth()->user()->name ?? 'ผู้ใช้งาน' }}!
                        </h4>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-2">
                                    <strong>บทบาท:</strong> 
                                    <span class="badge bg-primary ms-1">{{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}</span>
                                </p>
                                <p class="mb-2">
                                    <strong>อีเมล:</strong> {{ auth()->user()->email }}
                                </p>
                                <p class="mb-2">
                                    <strong>รหัสพนักงาน:</strong> {{ auth()->user()->employee_code ?? 'ไม่ระบุ' }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                @if(auth()->user()->department)
                                    <p class="mb-2">
                                        <strong>แผนก:</strong> {{ auth()->user()->department->name }}
                                    </p>
                                @endif
                                @if(auth()->user()->branch)
                                    <p class="mb-2">
                                        <strong>สาขา:</strong> {{ auth()->user()->branch->name }}
                                    </p>
                                @endif
                                <p class="mb-2">
                                    <strong>เข้าสู่ระบบเมื่อ:</strong> {{ now()->format('d/m/Y H:i:s') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="position-relative">
                            @if(auth()->user()->has_photo ?? false)
                                <img src="{{ auth()->user()->photo_url }}" 
                                     alt="Profile Photo" 
                                     class="rounded-circle border border-3 border-primary"
                                     style="width: 120px; height: 120px; object-fit: cover;">
                            @else
                                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center border border-3 border-primary" 
                                     style="width: 120px; height: 120px; font-size: 3rem; font-weight: bold;">
                                    @if(auth()->user()->role === 'super_admin')
                                        <i class="fas fa-crown"></i>
                                    @elseif(auth()->user()->role === 'it_admin')
                                        <i class="fas fa-laptop-code"></i>
                                    @elseif(auth()->user()->role === 'hr')
                                        <i class="fas fa-users"></i>
                                    @elseif(auth()->user()->role === 'manager')
                                        <i class="fas fa-user-tie"></i>
                                    @elseif(auth()->user()->role === 'express')
                                        <i class="fas fa-bolt"></i>
                                    @else
                                        {{ auth()->user()->initials ?? 'U' }}
                                    @endif
                                </div>
                            @endif
                            @if(auth()->user()->role === 'super_admin')
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning">
                                    <i class="fas fa-crown"></i>
                                </span>
                            @elseif(auth()->user()->role === 'express')
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning">
                                    <i class="fas fa-bolt"></i>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- System Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card stat-card h-100">
            <div class="card-body text-center">
                <i class="fas fa-users fa-3x text-primary mb-3"></i>
                <h3 class="text-primary mb-2">{{ $totalEmployees ?? 0 }}</h3>
                <p class="text-muted mb-0">พนักงานทั้งหมด</p>
                <small class="text-success">ใช้งาน: {{ $activeEmployees ?? 0 }} คน</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card h-100">
            <div class="card-body text-center">
                <i class="fas fa-building fa-3x text-success mb-3"></i>
                <h3 class="text-success mb-2">{{ $totalBranches ?? 0 }}</h3>
                <p class="text-muted mb-0">สาขาทั้งหมด</p>
                <small class="text-success">เปิดใช้งาน: {{ $activeBranches ?? 0 }} สาขา</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card h-100">
            <div class="card-body text-center">
                <i class="fas fa-sitemap fa-3x text-warning mb-3"></i>
                <h3 class="text-warning mb-2">{{ $totalDepartments ?? 0 }}</h3>
                <p class="text-muted mb-0">แผนกทั้งหมด</p>
                <small class="text-warning">Express: {{ $expressEnabledDepartments ?? 0 }} แผนก</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card h-100">
            <div class="card-body text-center">
                <i class="fas fa-camera fa-3x text-info mb-3"></i>
                <h3 class="text-info mb-2">{{ $employeesWithPhotos ?? 0 }}</h3>
                <p class="text-muted mb-0">มีรูปภาพ</p>
                <small class="text-info">{{ $photoPercentage ?? 0 }}% ของทั้งหมด</small>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-bolt me-2"></i>
                    การดำเนินการด่วน
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @if(in_array(auth()->user()->role, ['super_admin', 'it_admin', 'hr', 'manager', 'express']))
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                            <a href="{{ route('employees.index') }}" class="btn btn-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                                <i class="fas fa-users fa-2x mb-2"></i>
                                <span>จัดการพนักงาน</span>
                                <small class="mt-1 opacity-75">ดู แก้ไข ค้นหา</small>
                            </a>
                        </div>
                    @endif
                    
                    @if(in_array(auth()->user()->role, ['super_admin', 'it_admin', 'hr']))
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                            <a href="{{ route('employees.create') }}" class="btn btn-success w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                                <i class="fas fa-user-plus fa-2x mb-2"></i>
                                <span>เพิ่มพนักงานใหม่</span>
                                <small class="mt-1 opacity-75">สร้างบัญชีใหม่</small>
                            </a>
                        </div>
                    @endif
                    
                    @if(in_array(auth()->user()->role, ['super_admin', 'it_admin', 'hr', 'manager']))
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                            <a href="{{ route('branches.index') }}" class="btn btn-info w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                                <i class="fas fa-building fa-2x mb-2"></i>
                                <span>จัดการสาขา</span>
                                <small class="mt-1 opacity-75">สาขาและพื้นที่</small>
                            </a>
                        </div>
                    @endif
                    
                    @if(in_array(auth()->user()->role, ['super_admin', 'it_admin']))
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                            <a href="{{ route('departments.index') }}" class="btn btn-warning w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                                <i class="fas fa-sitemap fa-2x mb-2"></i>
                                <span>จัดการแผนก</span>
                                <small class="mt-1 opacity-75">แผนกและหน่วยงาน</small>
                            </a>
                        </div>
                    @endif
                    
                    @if(auth()->user()->role === 'super_admin')
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                            <a href="{{ route('employees.trash') }}" class="btn btn-secondary w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                                <i class="fas fa-trash-restore fa-2x mb-2"></i>
                                <span>ถังขยะ</span>
                                <small class="mt-1 opacity-75">กู้คืนข้อมูล</small>
                            </a>
                        </div>
                    @endif
                    
                    @if(in_array(auth()->user()->role, ['super_admin', 'it_admin', 'hr']))
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                            <a href="{{ route('branches.export') }}" class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                                <i class="fas fa-download fa-2x mb-2"></i>
                                <span>ส่งออกข้อมูล</span>
                                <small class="mt-1 opacity-75">Excel, CSV, PDF</small>
                            </a>
                        </div>
                    @endif
                    
                    @if(auth()->user()->role === 'express')
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                            <a href="{{ route('employees.create') }}" class="btn btn-warning w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                                <i class="fas fa-bolt fa-2x mb-2"></i>
                                <span>Express: เพิ่มพนักงาน</span>
                                <small class="mt-1 opacity-75">สำหรับแผนกบัญชี</small>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity & System Info -->
<div class="row">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    ข้อมูลระบบ
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <tbody>
                            <tr>
                                <td><strong>เวอร์ชันระบบ</strong></td>
                                <td><span class="badge bg-primary">{{ $system_info['version'] ?? 'v2.1' }}</span></td>
                            </tr>
                            <tr>
                                <td><strong>Laravel</strong></td>
                                <td><span class="badge bg-success">{{ $system_info['laravel_version'] ?? app()->version() }}</span></td>
                            </tr>
                            <tr>
                                <td><strong>PHP</strong></td>
                                <td><span class="badge bg-info">{{ $system_info['php_version'] ?? PHP_VERSION }}</span></td>
                            </tr>
                            <tr>
                                <td><strong>Environment</strong></td>
                                <td>
                                    <span class="badge bg-{{ app()->environment('production') ? 'success' : 'warning' }}">
                                        {{ ucfirst($system_info['environment'] ?? app()->environment()) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>เซิร์ฟเวอร์เวลา</strong></td>
                                <td>{{ $system_info['current_time'] ?? now()->format('d/m/Y H:i:s') }}</td>
                            </tr>
                            <tr>
                                <td><strong>สถานะการเชื่อมต่อ</strong></td>
                                <td>
                                    @if(isset($system_info['database_status']) && $system_info['database_status']['status'] === 'connected')
                                        <span class="badge bg-success">{{ $system_info['database_status']['message'] }}</span>
                                    @else
                                        <span class="badge bg-danger">ไม่สามารถเชื่อมต่อได้</span>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                @if(app()->environment('local', 'development'))
                    <div class="mt-3">
                        <h6 class="text-warning">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            Development Tools
                        </h6>
                        <div class="btn-group-vertical w-100" role="group">
                            <a href="{{ route('health.check') }}" target="_blank" class="btn btn-outline-info btn-sm">
                                <i class="fas fa-heartbeat me-1"></i> Health Check
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-pie me-2"></i>
                    สถิติผู้ใช้งาน
                </h5>
            </div>
            <div class="card-body">
                @if(isset($roleDistribution) && !empty($roleDistribution))
                    @php
                        $roleNames = [
                            'super_admin' => 'Super Admin',
                            'it_admin' => 'IT Admin', 
                            'hr' => 'HR',
                            'manager' => 'Manager',
                            'express' => 'Express',
                            'employee' => 'Employee'
                        ];
                        $totalUsers = array_sum($roleDistribution);
                    @endphp
                    
                    <div class="row">
                        @foreach($roleDistribution as $role => $count)
                            @php
                                $percentage = $totalUsers > 0 ? round(($count / $totalUsers) * 100, 1) : 0;
                                $colorClass = match($role) {
                                    'super_admin' => 'warning',
                                    'it_admin' => 'primary',
                                    'hr' => 'success',
                                    'manager' => 'info',
                                    'express' => 'warning',
                                    'employee' => 'secondary',
                                    default => 'secondary'
                                };
                            @endphp
                            <div class="col-12 mb-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-medium">{{ $roleNames[$role] ?? ucfirst(str_replace('_', ' ', $role)) }}</span>
                                    <div>
                                        <span class="badge bg-{{ $colorClass }} me-1">{{ $count }}</span>
                                        <small class="text-muted">({{ $percentage }}%)</small>
                                    </div>
                                </div>
                                <div class="progress mt-1" style="height: 6px;">
                                    <div class="progress-bar bg-{{ $colorClass }}" 
                                         role="progressbar" 
                                         style="width: {{ $percentage }}%"
                                         aria-valuenow="{{ $percentage }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-3 pt-3 border-top">
                        <div class="text-center">
                            <h6 class="mb-1">ผู้ใช้งานทั้งหมด</h6>
                            <h4 class="text-primary mb-0">{{ $totalUsers }} คน</h4>
                        </div>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">ไม่มีข้อมูลผู้ใช้งาน</h6>
                        <p class="text-muted small">ระบบอาจยังไม่พร้อมหรือไม่มีข้อมูลในฐานข้อมูล</p>
                        @if(app()->environment('local'))
                            <button class="btn btn-outline-primary btn-sm" onclick="location.reload()">
                                <i class="fas fa-redo me-1"></i> ลองใหม่
                            </button>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Express System Info (if user has access) -->
@if(in_array(auth()->user()->role, ['super_admin', 'it_admin', 'hr', 'express']))
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-warning">
            <div class="card-header bg-warning text-dark">
                <h5 class="card-title mb-0">
                    <i class="fas fa-bolt me-2"></i>
                    Express System Status
                </h5>
            </div>
            <div class="card-body">
                @php
                    $expressStats = $expressStats ?? [
                        'express_enabled_departments' => 0,
                        'express_department_percentage' => 0,
                        'express_user_percentage' => 0
                    ];
                @endphp
                <div class="row">
                    <div class="col-md-3 text-center">
                        <h4 class="text-warning">{{ $expressStats['express_enabled_departments'] ?? 0 }}</h4>
                        <p class="mb-0">แผนกที่เปิด Express</p>
                    </div>
                    <div class="col-md-3 text-center">
                        <h4 class="text-info">{{ $totalEmployees ?? 0 }}</h4>
                        <p class="mb-0">ผู้ใช้ Express</p>
                    </div>
                    <div class="col-md-3 text-center">
                        <h4 class="text-success">{{ round($expressStats['express_department_percentage'] ?? 0, 1) }}%</h4>
                        <p class="mb-0">แผนกที่เปิดใช้</p>
                    </div>
                    <div class="col-md-3 text-center">
                        <h4 class="text-primary">{{ round($expressStats['express_user_percentage'] ?? 0, 1) }}%</h4>
                        <p class="mb-0">พนักงานที่มี Express</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Recent Activity (if available) -->
@if(isset($recent_activity) && !empty($recent_activity))
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-history me-2"></i>
                    กิจกรรมล่าสุด
                </h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    @foreach($recent_activity as $activity)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-{{ $activity['color'] ?? 'primary' }}">
                                <i class="{{ $activity['icon'] ?? 'fas fa-info' }}"></i>
                            </div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">{{ $activity['title'] }}</h6>
                                <p class="timeline-text">{{ $activity['description'] }}</p>
                                <small class="text-muted">{{ $activity['time'] }}</small>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Dashboard functionality
    console.log('📊 Dashboard v2.1 loaded successfully');
    console.log('👤 User:', {
        id: {{ auth()->user()->id }},
        role: '{{ auth()->user()->role }}',
        email: '{{ auth()->user()->email }}',
        has_photo: {{ (auth()->user()->has_photo ?? false) ? 'true' : 'false' }}
    });
    
    // Auto-refresh stats every 5 minutes
    let refreshTimer = setTimeout(function() {
        if (confirm('ต้องการรีเฟรชข้อมูลสถิติหรือไม่?')) {
            location.reload();
        }
    }, 300000); // 5 minutes
    
    // Add hover effects to stat cards
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px)';
            this.style.transition = 'all 0.3s ease';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
        
        // Click effect
        card.addEventListener('click', function() {
            this.style.transform = 'scale(0.98)';
            setTimeout(() => {
                this.style.transform = 'translateY(-8px)';
            }, 150);
        });
    });
    
    // Quick action button animations
    const quickActionBtns = document.querySelectorAll('.card-body .btn');
    quickActionBtns.forEach(btn => {
        btn.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px)';
            this.style.transition = 'all 0.3s ease';
        });
        
        btn.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
    
    // Console welcome message with role-specific info
    @if(auth()->user()->role === 'super_admin')
        console.log('👑 Super Admin Access: Full system control');
        console.log('🔧 Available: All modules, System settings, Trash management, Bulk operations');
    @elseif(auth()->user()->role === 'it_admin')
        console.log('💻 IT Admin Access: Technical management');
        console.log('🔧 Available: Employee management, Department management, IT modules');
    @elseif(auth()->user()->role === 'hr')
        console.log('👥 HR Access: Human resources focus');
        console.log('📋 Available: Employee management, HR modules, Branch management');
    @elseif(auth()->user()->role === 'manager')
        console.log('👔 Manager Access: Department/Branch oversight');
        console.log('📊 Available: Limited employee management, Branch viewing');
    @elseif(auth()->user()->role === 'express')
        console.log('⚡ Express User Access: Accounting department');
        console.log('📊 Available: Express features, Limited employee management');
    @else
        console.log('👤 Employee Access: Standard user');
        console.log('📄 Available: Personal profile, Service requests');
    @endif
    
    // Branch system info
    @if(auth()->user()->branch_id ?? false)
        console.log('🏢 Assigned Branch: {{ auth()->user()->branch->name ?? "Unknown" }}');
    @else
        console.log('🏢 No branch assigned');
    @endif
    
    // Photo system info
    @if(auth()->user()->has_photo ?? false)
        console.log('📸 Profile photo: Available');
    @else
        console.log('📸 Profile photo: Not set (using avatar)');
    @endif
    
    // Clear refresh timer when user navigates away
    window.addEventListener('beforeunload', function() {
        if (refreshTimer) {
            clearTimeout(refreshTimer);
        }
    });
    
    // Load dashboard stats via API (optional)
    fetch('{{ route("dashboard.stats") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('📊 Dashboard stats loaded:', data.data);
            }
        })
        .catch(error => {
            console.log('⚠️ Could not load dashboard stats:', error);
        });
});

// Global error handler for dashboard
window.addEventListener('error', function(e) {
    console.error('Dashboard Error:', e.error);
    
    // Only show user-friendly message in production
    @if(app()->environment('production'))
        if (e.error && e.error.message.includes('fetch')) {
            // Handle API failures gracefully
            const alerts = document.querySelectorAll('.alert');
            if (alerts.length === 0) {
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-warning alert-dismissible fade show';
                alertDiv.innerHTML = `
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    มีปัญหาในการโหลดข้อมูลบางส่วน กรุณารีเฟรชหน้าเว็บ
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                document.querySelector('.content-area, .container-fluid').prepend(alertDiv);
            }
        }
    @endif
});
</script>
@endpush

@push('styles')
<style>
/* Dashboard-specific styling */
.stat-card {
    transition: all 0.3s ease;
    border: 1px solid rgba(181, 69, 68, 0.1);
    cursor: pointer;
    overflow: hidden;
}

.stat-card:hover {
    box-shadow: 0 15px 35px rgba(181, 69, 68, 0.2);
    border-color: rgba(181, 69, 68, 0.3);
}

.stat-card .fa-3x {
    transition: all 0.3s ease;
}

.stat-card:hover .fa-3x {
    transform: scale(1.1);
}

.card-title {
    font-weight: 600;
}

.btn {
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.progress {
    border-radius: 10px;
    background-color: rgba(0, 0, 0, 0.1);
}

.progress-bar {
    border-radius: 10px;
}

/* Role-specific badge colors */
.badge.bg-primary {
    background: linear-gradient(45deg, var(--primary-red), var(--primary-orange)) !important;
}

/* Profile photo styling */
.rounded-circle {
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.rounded-circle:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
}

/* Quick action cards */
.card-body .btn {
    border-radius: 12px;
    border: 2px solid transparent;
    min-height: 120px;
}

.card-body .btn:hover {
    border-color: rgba(255, 255, 255, 0.3);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

/* Express system card */
.border-warning {
    border-width: 2px !important;
}

/* Timeline styling */
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -30px;
    top: 0;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 12px;
}

.timeline-content {
    margin-left: 10px;
}

.timeline-title {
    margin-bottom: 5px;
    font-weight: 600;
}

.timeline-text {
    margin-bottom: 5px;
    color: #666;
}

/* Animation for loading */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.card {
    animation: fadeIn 0.6s ease-out;
}

.stat-card:nth-child(1) { animation-delay: 0.1s; }
.stat-card:nth-child(2) { animation-delay: 0.2s; }
.stat-card:nth-child(3) { animation-delay: 0.3s; }
.stat-card:nth-child(4) { animation-delay: 0.4s; }

/* Dark mode support preparation */
@media (prefers-color-scheme: dark) {
    .stat-card {
        background: rgba(255, 255, 255, 0.05);
        border-color: rgba(255, 255, 255, 0.1);
    }
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .stat-card .fa-3x {
        font-size: 2rem !important;
    }
    
    .stat-card h3 {
        font-size: 1.8rem;
    }
    
    .card-body .btn {
        min-height: 100px;
    }
    
    .card-body .btn .fa-2x {
        font-size: 1.5rem !important;
    }
    
    .timeline {
        padding-left: 20px;
    }
    
    .timeline-marker {
        left: -20px;
        width: 20px;
        height: 20px;
        font-size: 10px;
    }
}
</style>
@endpush
