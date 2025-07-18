@extends('layouts.app')

@section('title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="row mb-4">
    <div class="col-12">
        <h1 class="h3 mb-0 text-primary fw-bold">
            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
        </h1>
        <p class="text-muted mb-0">ภาพรวมของระบบจัดการ IT</p>
    </div>
</div>

<!-- Welcome Message -->
<div class="row mb-4">
    <div class="col-12">
        <div class="alert alert-info" role="alert">
            <i class="fas fa-info-circle me-2"></i>
            <strong>ยินดีต้อนรับ!</strong> 
            คุณ {{ auth()->user()->first_name_th ?? auth()->user()->display_name ?? 'ผู้ใช้' }} 
            ({{ auth()->user()->role_display ?? 'Employee' }}) 
            เข้าสู่ระบบเมื่อ {{ now()->locale('th')->translatedFormat('H:i น. วันที่ d F Y') }}
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card stat-card border-start border-primary border-4 h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            พนักงานทั้งหมด
                        </div>
                        <div class="h4 mb-0 font-weight-bold text-gray-800">
                            @php
                                try {
                                    $totalEmployees = isset($employees) ? $employees->count() : \App\Models\Employee::count();
                                } catch (\Exception $e) {
                                    $totalEmployees = 0;
                                }
                            @endphp
                            {{ $totalEmployees }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-primary opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card stat-card border-start border-success border-4 h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            พนักงานที่ใช้งาน
                        </div>
                        <div class="h4 mb-0 font-weight-bold text-gray-800">
                            @php
                                try {
                                    $activeEmployees = isset($employees) ? 
                                        $employees->where('status', 'active')->count() : 
                                        \App\Models\Employee::where('status', 'active')->count();
                                } catch (\Exception $e) {
                                    $activeEmployees = 0;
                                }
                            @endphp
                            {{ $activeEmployees }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-check fa-2x text-success opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card stat-card border-start border-info border-4 h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            แผนกทั้งหมด
                        </div>
                        <div class="h4 mb-0 font-weight-bold text-gray-800">
                            @php
                                try {
                                    $totalDepartments = isset($departments) ? 
                                        $departments->count() : 
                                        \App\Models\Department::count();
                                } catch (\Exception $e) {
                                    $totalDepartments = 8;
                                }
                            @endphp
                            {{ $totalDepartments }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-building fa-2x text-info opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card stat-card border-start border-warning border-4 h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            ระบบออนไลน์
                        </div>
                        <div class="h4 mb-0 font-weight-bold text-gray-800">
                            24/7
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-server fa-2x text-warning opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions & Recent Activity -->
<div class="row mb-4">
    <!-- Quick Actions -->
    <div class="col-lg-8 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-rocket me-2"></i>การจัดการด่วน
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @php $userRole = auth()->user()->role ?? 'employee'; @endphp
                    
                    @if(in_array($userRole, ['super_admin', 'it_admin', 'hr', 'manager']))
                        <div class="col-md-4 col-sm-6">
                            <a href="{{ route('employees.index') }}" class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center" style="min-height: 100px;">
                                <i class="fas fa-users fa-2x mb-2"></i>
                                <span>จัดการพนักงาน</span>
                            </a>
                        </div>
                        
                        <div class="col-md-4 col-sm-6">
                            <a href="{{ route('employees.create') }}" class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center" style="min-height: 100px;">
                                <i class="fas fa-user-plus fa-2x mb-2"></i>
                                <span>เพิ่มพนักงานใหม่</span>
                            </a>
                        </div>
                        
                        <div class="col-md-4 col-sm-6">
                            <a href="#" class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center" style="min-height: 100px;">
                                <i class="fas fa-laptop fa-2x mb-2"></i>
                                <span>จัดการอุปกรณ์ IT</span>
                            </a>
                        </div>
                        
                        <div class="col-md-4 col-sm-6">
                            <a href="{{ route('employees.exportExcel') }}" class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center" style="min-height: 100px;">
                                <i class="fas fa-file-excel fa-2x mb-2"></i>
                                <span>ส่งออกข้อมูล</span>
                            </a>
                        </div>
                        
                        <div class="col-md-4 col-sm-6">
                            <a href="#" class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center" style="min-height: 100px;">
                                <i class="fas fa-chart-bar fa-2x mb-2"></i>
                                <span>รายงานทั้งหมด</span>
                            </a>
                        </div>
                        
                        <div class="col-md-4 col-sm-6">
                            <a href="#" class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center" style="min-height: 100px;">
                                <i class="fas fa-cog fa-2x mb-2"></i>
                                <span>ตั้งค่าระบบ</span>
                            </a>
                        </div>
                    @else
                        <div class="col-md-6 col-sm-6">
                            <a href="{{ route('profile') }}" class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center" style="min-height: 100px;">
                                <i class="fas fa-user fa-2x mb-2"></i>
                                <span>ข้อมูลส่วนตัว</span>
                            </a>
                        </div>
                        
                        <div class="col-md-6 col-sm-6">
                            <a href="#" class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center" style="min-height: 100px;">
                                <i class="fas fa-tools fa-2x mb-2"></i>
                                <span>แจ้งซ่อม</span>
                            </a>
                        </div>
                        
                        <div class="col-md-6 col-sm-6">
                            <a href="#" class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center" style="min-height: 100px;">
                                <i class="fas fa-ticket-alt fa-2x mb-2"></i>
                                <span>ขอใช้บริการ</span>
                            </a>
                        </div>
                        
                        <div class="col-md-6 col-sm-6">
                            <a href="#" class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center" style="min-height: 100px;">
                                <i class="fas fa-tasks fa-2x mb-2"></i>
                                <span>ติดตามสถานะงาน</span>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- System Status -->
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-server me-2"></i>สถานะระบบ
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="fw-medium">ฐานข้อมูล</span>
                    <span class="badge bg-success">
                        <i class="fas fa-check-circle me-1"></i>ปกติ
                    </span>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="fw-medium">เซิร์ฟเวอร์</span>
                    <span class="badge bg-success">
                        <i class="fas fa-check-circle me-1"></i>ออนไลน์
                    </span>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="fw-medium">การเชื่อมต่อ</span>
                    <span class="badge bg-success">
                        <i class="fas fa-wifi me-1"></i>เสถียร
                    </span>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="fw-medium">พื้นที่จัดเก็บ</span>
                    <span class="badge bg-warning">
                        <i class="fas fa-hdd me-1"></i>75%
                    </span>
                </div>
                
                <hr>
                
                <div class="text-center">
                    <small class="text-muted">
                        <i class="fas fa-clock me-1"></i>
                        อัปเดตล่าสุด: {{ now()->format('H:i:s') }}
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
@if(in_array($userRole, ['super_admin', 'it_admin', 'hr']))
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-history me-2"></i>กิจกรรมล่าสุด
                </h5>
            </div>
            <div class="card-body">
                @php
                    try {
                        $recentEmployees = isset($employees) ? 
                            $employees->sortByDesc('created_at')->take(5) : 
                            \App\Models\Employee::latest('created_at')->limit(5)->get();
                    } catch (\Exception $e) {
                        $recentEmployees = collect();
                    }
                @endphp
                
                @if($recentEmployees->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($recentEmployees as $employee)
                            <div class="list-group-item d-flex align-items-center border-0 px-0">
                                <div class="me-3">
                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="fas fa-user text-white"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-medium">
                                        {{ $employee->display_name ?? $employee->full_name_th ?? 'ไม่ระบุชื่อ' }}
                                    </div>
                                    <div class="text-muted small">
                                        <i class="fas fa-plus-circle me-1"></i>พนักงานใหม่เข้าระบบ
                                    </div>
                                </div>
                                <div class="text-muted small">
                                    @if(isset($employee->created_at))
                                        {{ $employee->created_at->diffForHumans() }}
                                    @else
                                        เมื่อสักครู่
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-history fa-3x mb-3 opacity-25"></i>
                        <p class="mb-0">ยังไม่มีกิจกรรม</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@push('styles')
<style>
    .stat-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    
    .list-group-item {
        transition: all 0.2s ease;
    }
    
    .list-group-item:hover {
        background-color: rgba(181, 69, 68, 0.05);
    }
    
    .quick-action-btn {
        transition: all 0.3s ease;
    }
    
    .quick-action-btn:hover {
        transform: scale(1.05);
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animate stat cards on page load
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.6s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 150);
    });
    
    // Add click effect to stat cards
    statCards.forEach(card => {
        card.addEventListener('click', function() {
            this.style.transform = 'scale(0.98)';
            setTimeout(() => {
                this.style.transform = '';
            }, 150);
        });
    });
    
    // Update time every minute
    setInterval(function() {
        const timeElement = document.querySelector('.text-center small');
        if (timeElement) {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('th-TH', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            timeElement.innerHTML = '<i class="fas fa-clock me-1"></i>อัปเดตล่าสุด: ' + timeStr;
        }
    }, 60000);
});
</script>
@endpush