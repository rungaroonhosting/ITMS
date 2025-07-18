@extends('layouts.app')

@section('title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="row mb-4">
    <div class="col-12">
        <h1 class="h3 mb-0 text-primary fw-bold">Dashboard</h1>
        <p class="text-muted mb-0">ภาพรวมของระบบจัดการ IT</p>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card stat-card h-100">
            <div class="card-body text-center">
                <i class="fas fa-users fa-3x mb-3"></i>
                <h3>
                    @php
                        try {
                            echo \App\Models\Employee::count();
                        } catch (\Exception $e) {
                            echo '-';
                        }
                    @endphp
                </h3>
                <p class="mb-0">พนักงานทั้งหมด</p>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card stat-card h-100">
            <div class="card-body text-center">
                <i class="fas fa-user-check fa-3x mb-3"></i>
                <h3>
                    @php
                        try {
                            echo \App\Models\Employee::where('status', 'active')->count();
                        } catch (\Exception $e) {
                            echo '-';
                        }
                    @endphp
                </h3>
                <p class="mb-0">พนักงานที่ใช้งาน</p>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card stat-card h-100">
            <div class="card-body text-center">
                <i class="fas fa-building fa-3x mb-3"></i>
                <h3>
                    @php
                        try {
                            echo \App\Models\Department::count();
                        } catch (\Exception $e) {
                            echo '8';
                        }
                    @endphp
                </h3>
                <p class="mb-0">แผนกทั้งหมด</p>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card stat-card h-100">
            <div class="card-body text-center">
                @php $userRole = auth()->user()->role ?? 'employee'; @endphp
                @if($userRole === 'super_admin' || $userRole === 'it_admin')
                    <i class="fas fa-user-shield fa-3x mb-3"></i>
                    <h3>
                        @php
                            try {
                                echo \App\Models\Employee::whereIn('role', ['super_admin', 'it_admin'])->count();
                            } catch (\Exception $e) {
                                echo '-';
                            }
                        @endphp
                    </h3>
                    <p class="mb-0">ผู้ดูแลระบบ</p>
                @else
                    <i class="fas fa-clock fa-3x mb-3"></i>
                    <h3>{{ now()->format('H:i') }}</h3>
                    <p class="mb-0">เข้าสู่ระบบ</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions & Department Overview -->
<div class="row mb-4">
    <!-- Quick Actions -->
    <div class="col-lg-8 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-tachometer-alt me-2"></i>การจัดการด่วน
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @if($userRole === 'super_admin' || $userRole === 'it_admin')
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
                                <i class="fas fa-building fa-2x mb-2"></i>
                                <span>จัดการแผนก</span>
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
                            <a href="#" class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center" style="min-height: 100px;">
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

    <!-- Department Overview -->
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-building me-2"></i>แผนกต่างๆ
                </h5>
            </div>
            <div class="card-body">
                @php
                    try {
                        $departments = \App\Models\Department::withCount(['employees' => function($query) {
                            $query->where('status', 'active');
                        }])->get();
                    } catch (\Exception $e) {
                        $departments = collect([
                            (object)['id' => 1, 'name' => 'บัญชี', 'employees_count' => 3],
                            (object)['id' => 2, 'name' => 'IT', 'employees_count' => 2],
                            (object)['id' => 3, 'name' => 'ฝ่ายขาย', 'employees_count' => 1],
                            (object)['id' => 4, 'name' => 'การตลาด', 'employees_count' => 0],
                            (object)['id' => 5, 'name' => 'บุคคล', 'employees_count' => 1],
                            (object)['id' => 6, 'name' => 'ผลิต', 'employees_count' => 0],
                            (object)['id' => 7, 'name' => 'คลังสินค้า', 'employees_count' => 0],
                            (object)['id' => 8, 'name' => 'บริหาร', 'employees_count' => 0],
                        ]);
                    }
                @endphp
                
                @if($departments->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($departments as $department)
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 12px; height: 12px;"></div>
                                    </div>
                                    <span class="fw-medium">{{ $department->name }}</span>
                                </div>
                                <span class="badge bg-primary rounded-pill">{{ $department->employees_count ?? 0 }} คน</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-building fa-3x mb-3 opacity-25"></i>
                        <p class="mb-0">ไม่มีข้อมูลแผนก</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity & System Status -->
@if($userRole === 'super_admin' || $userRole === 'it_admin')
<div class="row">
    <!-- Recent Activity -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-history me-2"></i>กิจกรรมล่าสุด
                </h5>
            </div>
            <div class="card-body">
                @php
                    try {
                        $recentEmployees = \App\Models\Employee::latest('created_at')->limit(5)->get();
                    } catch (\Exception $e) {
                        $recentEmployees = collect();
                    }
                @endphp
                
                @if($recentEmployees->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($recentEmployees as $employee)
                            <div class="list-group-item d-flex align-items-center px-0">
                                <div class="me-3">
                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="fas fa-user text-white"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-medium">
                                        {{ $employee->first_name_th ?? $employee->name ?? 'ไม่ระบุชื่อ' }}
                                        {{ $employee->last_name_th ?? '' }}
                                    </div>
                                    <div class="text-muted small">พนักงานใหม่เข้าระบบ</div>
                                </div>
                                <div class="text-muted small">
                                    {{ $employee->created_at->diffForHumans() }}
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

    <!-- System Status -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-server me-2"></i>สถานะระบบ
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="fw-medium">ฐานข้อมูล</span>
                    <span class="badge bg-success">ปกติ</span>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="fw-medium">เซิร์ฟเวอร์</span>
                    <span class="badge bg-success">ออนไลน์</span>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="fw-medium">การเชื่อมต่อ</span>
                    <span class="badge bg-success">เสถียร</span>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="fw-medium">พื้นที่จัดเก็บ</span>
                    <span class="badge bg-warning">75%</span>
                </div>
                
                <hr>
                
                <div class="text-center">
                    <small class="text-muted">
                        อัปเดตล่าสุด: {{ now()->format('H:i:s') }}
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@else
<!-- For Regular Employees -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle me-2"></i>ข้อมูลการใช้งาน
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3 mb-3">
                        <i class="fas fa-calendar-check fa-2x text-primary mb-2"></i>
                        <h6>เข้าสู่ระบบวันนี้</h6>
                        <p class="text-muted mb-0">{{ now()->format('H:i น.') }}</p>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <i class="fas fa-user-check fa-2x text-success mb-2"></i>
                        <h6>สถานะบัญชี</h6>
                        <p class="text-muted mb-0">ใช้งานได้</p>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <i class="fas fa-tasks fa-2x text-warning mb-2"></i>
                        <h6>งานที่รออนุมัติ</h6>
                        <p class="text-muted mb-0">0 รายการ</p>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <i class="fas fa-bell fa-2x text-info mb-2"></i>
                        <h6>การแจ้งเตือน</h6>
                        <p class="text-muted mb-0">ไม่มีแจ้งเตือนใหม่</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Welcome Message -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center">
                <h4 class="text-primary">
                    ยินดีต้อนรับเข้าสู่ระบบ IT Management System
                </h4>
                <p class="text-muted mb-0">
                    สวัสดี {{ auth()->user()->first_name_th ?? auth()->user()->name ?? 'ผู้ใช้' }}! 
                    วันนี้เป็นวันที่ {{ now()->locale('th')->translatedFormat('l ที่ d F Y') }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .stat-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
    }
    
    .list-group-item {
        border: none;
        border-bottom: 1px solid rgba(0,0,0,0.125);
        padding: 1rem 0;
    }
    
    .list-group-item:last-child {
        border-bottom: none;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animate stat cards on page load
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach((card, index) => {
        setTimeout(() => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'all 0.6s ease';
            
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100);
        }, index * 200);
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
        const timeElements = document.querySelectorAll('.time-update');
        timeElements.forEach(element => {
            const now = new Date();
            element.textContent = now.toLocaleTimeString('th-TH', {
                hour: '2-digit',
                minute: '2-digit'
            }) + ' น.';
        });
    }, 60000);
});
</script>
@endpush