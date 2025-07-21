@extends('layouts.app')

@section('title', 'Dashboard - IT Management System')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">
                        <i class="fas fa-tachometer-alt text-primary"></i> Dashboard
                    </h2>
                    <p class="text-muted mb-0">IT Management System (ITMS) v1.4 - Employee Management</p>
                </div>
                <div class="text-end">
                    <div class="text-muted small">
                        <i class="fas fa-clock"></i> {{ now()->format('d/m/Y H:i น.') }}
                    </div>
                    <div class="text-muted small">
                        <i class="fas fa-user"></i> {{ auth()->user()->first_name_th ?? 'ผู้ใช้' }} {{ auth()->user()->last_name_th ?? '' }}
                        <span class="badge bg-info">{{ ucfirst(auth()->user()->role ?? 'user') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-gradient-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="fs-3 fw-bold">{{ $employees->count() }}</div>
                            <div class="small">พนักงานทั้งหมด</div>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('employees.index') }}">
                        ดูรายละเอียด
                    </a>
                    <div class="small text-white">
                        <i class="fas fa-angle-right"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-gradient-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-user-check fa-2x"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="fs-3 fw-bold">{{ $employees->where('status', 'active')->count() }}</div>
                            <div class="small">พนักงานที่ใช้งาน</div>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('employees.search', ['q' => 'active']) }}">
                        ดูรายละเอียด
                    </a>
                    <div class="small text-white">
                        <i class="fas fa-angle-right"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-gradient-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-calculator fa-2x"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="fs-3 fw-bold">{{ $employees->whereNotNull('express_username')->count() }}</div>
                            <div class="small">Express Users</div>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('employees.index') }}#express">
                        ดูรายละเอียด
                    </a>
                    <div class="small text-white">
                        <i class="fas fa-angle-right"></i>
                    </div>
                </div>
            </div>
        </div>

        @if(auth()->user()->role === 'super_admin')
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card bg-gradient-danger text-white h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-trash fa-2x"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="fs-3 fw-bold">{{ $trashCount ?? 0 }}</div>
                                <div class="small">ในถังขยะ</div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white stretched-link" href="{{ route('employees.trash') }}">
                            จัดการถังขยะ
                        </a>
                        <div class="small text-white">
                            <i class="fas fa-angle-right"></i>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt text-warning"></i> การดำเนินการด่วน
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('employees.create') }}" class="btn btn-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                                <i class="fas fa-user-plus fa-2x mb-2"></i>
                                <span>เพิ่มพนักงานใหม่</span>
                            </a>
                        </div>
                        
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('employees.exportExcel') }}" class="btn btn-success w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                                <i class="fas fa-file-excel fa-2x mb-2"></i>
                                <span>ส่งออก Excel</span>
                            </a>
                        </div>
                        
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('employees.exportPdf') }}" class="btn btn-info w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                                <i class="fas fa-file-pdf fa-2x mb-2"></i>
                                <span>รายงาน PDF</span>
                            </a>
                        </div>
                        
                        @if(auth()->user()->role === 'super_admin')
                            <div class="col-lg-3 col-md-6 mb-3">
                                <a href="{{ route('employees.trash') }}" class="btn btn-secondary w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                                    <i class="fas fa-trash fa-2x mb-2"></i>
                                    <span>จัดการถังขยะ</span>
                                    @if($trashCount > 0)
                                        <span class="badge bg-danger">{{ $trashCount }}</span>
                                    @endif
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Employees & Department Summary -->
    <div class="row">
        <!-- Recent Employees -->
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-clock text-info"></i> พนักงานล่าสุด
                    </h5>
                    <a href="{{ route('employees.index') }}" class="btn btn-sm btn-outline-primary">
                        ดูทั้งหมด
                    </a>
                </div>
                <div class="card-body">
                    @if($employees->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>รหัสพนักงาน</th>
                                        <th>ชื่อ-นามสกุล</th>
                                        <th>แผนก</th>
                                        <th>ตำแหน่ง</th>
                                        <th>สถานะ</th>
                                        <th>การจัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($employees->sortByDesc('created_at')->take(5) as $employee)
                                        <tr>
                                            <td>
                                                <strong>{{ $employee->employee_id }}</strong>
                                                @if($employee->express_username)
                                                    <br><small class="text-info">
                                                        <i class="fas fa-calculator"></i> Express
                                                    </small>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $employee->full_name }}
                                                @if($employee->english_name)
                                                    <br><small class="text-muted">{{ $employee->english_name }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $employee->department }}</td>
                                            <td>{{ $employee->position }}</td>
                                            <td>
                                                <span class="badge bg-{{ $employee->status === 'active' ? 'success' : 'secondary' }}">
                                                    {{ $employee->status === 'active' ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('employees.show', $employee) }}" 
                                                       class="btn btn-outline-info" title="ดู">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('employees.edit', $employee) }}" 
                                                       class="btn btn-outline-warning" title="แก้ไข">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h6 class="text-muted">ยังไม่มีข้อมูลพนักงาน</h6>
                            <a href="{{ route('employees.create') }}" class="btn btn-primary">
                                เพิ่มพนักงานคนแรก
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Department Summary -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-building text-success"></i> สรุปตามแผนก
                    </h5>
                </div>
                <div class="card-body">
                    @if($employees->count() > 0)
                        @php $departmentCounts = $employees->groupBy('department'); @endphp
                        
                        @foreach($departmentCounts as $department => $employees_in_dept)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <span class="fw-bold">{{ $department }}</span>
                                    @if(in_array($department, ['แผนกบัญชี', 'แผนกบัญชีและการเงิน', 'แผนกการเงิน']))
                                        <span class="badge bg-info ms-1">Express</span>
                                    @endif
                                </div>
                                <span class="badge bg-primary">{{ $employees_in_dept->count() }} คน</span>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-building fa-2x mb-2"></i>
                            <p class="mb-0">ไม่มีข้อมูลแผนก</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- System Status -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-cogs text-primary"></i> สถานะระบบ
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <div class="text-success">
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                                <small class="text-muted">ระบบปกติ</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-info">
                                <i class="fas fa-database fa-2x"></i>
                            </div>
                            <small class="text-muted">ฐานข้อมูลเชื่อมต่อ</small>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="small">
                        <div class="d-flex justify-content-between">
                            <span>เวอร์ชัน:</span>
                            <span class="fw-bold">v1.4.0</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>อัปเดตล่าสุด:</span>
                            <span class="fw-bold">{{ now()->format('d/m/Y') }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>ฟีเจอร์:</span>
                            <span>
                                <span class="badge bg-success">Express</span>
                                <span class="badge bg-info">Trash</span>
                                <span class="badge bg-warning">Export</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Express Users Section (if applicable) -->
    @if($employees->whereNotNull('express_username')->count() > 0)
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-calculator"></i> พนักงานที่ใช้ระบบ Express
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($employees->whereNotNull('express_username') as $expressUser)
                                <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                                    <div class="card border-info">
                                        <div class="card-body text-center">
                                            <div class="mb-2">
                                                <i class="fas fa-user-circle fa-2x text-info"></i>
                                            </div>
                                            <h6 class="card-title">{{ $expressUser->full_name }}</h6>
                                            <p class="card-text small text-muted">{{ $expressUser->department }}</p>
                                            <div class="badge bg-info">{{ $expressUser->express_username }}</div>
                                            @if(auth()->user()->role === 'super_admin' || auth()->user()->email === $expressUser->email)
                                                @if($expressUser->express_password)
                                                    <br><small class="text-muted">{{ $expressUser->express_password }}</small>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Auto-refresh data every 5 minutes -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-refresh page data every 5 minutes
    setInterval(function() {
        // Only refresh if user is active (has interacted in last 10 minutes)
        if (document.visibilityState === 'visible') {
            fetch('/api/employees/trash-count')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update trash count badges
                        const trashBadges = document.querySelectorAll('.trash-count');
                        trashBadges.forEach(badge => {
                            badge.textContent = data.count;
                            badge.style.display = data.count > 0 ? 'inline' : 'none';
                        });
                    }
                })
                .catch(error => console.log('Auto-refresh failed:', error));
        }
    }, 300000); // 5 minutes

    // Animate statistics cards on page load
    const statsCards = document.querySelectorAll('.bg-gradient-primary, .bg-gradient-success, .bg-gradient-warning, .bg-gradient-danger');
    statsCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});
</script>

<style>
.bg-gradient-primary {
    background: linear-gradient(45deg, #007bff, #0056b3) !important;
}

.bg-gradient-success {
    background: linear-gradient(45deg, #28a745, #20c997) !important;
}

.bg-gradient-warning {
    background: linear-gradient(45deg, #ffc107, #fd7e14) !important;
}

.bg-gradient-danger {
    background: linear-gradient(45deg, #dc3545, #c82333) !important;
}

.card {
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
}

.border-end {
    border-right: 1px solid #dee2e6 !important;
}

.stretched-link::after {
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    z-index: 1;
    content: "";
}
</style>
@endsection