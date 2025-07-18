@extends('layouts.app')

@section('title', 'รายละเอียดพนักงาน - ' . $employee->full_name)

@section('styles')
<link rel="stylesheet" href="{{ asset('css/employees.css') }}">
@endsection

@section('content')
<div class="container-fluid px-4">
    {{-- Header Section --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-primary-red fw-bold">
                <i class="fas fa-user me-2"></i>รายละเอียดพนักงาน
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}" class="text-decoration-none">หน้าแรก</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('employees.index') }}" class="text-decoration-none">จัดการพนักงาน</a>
                    </li>
                    <li class="breadcrumb-item active">{{ $employee->full_name }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('employees.edit', $employee) }}" class="btn btn-warning">
                <i class="fas fa-edit me-1"></i>แก้ไข
            </a>
            <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>กลับ
            </a>
        </div>
    </div>

    {{-- Employee Profile Header --}}
    <div class="card border-0 shadow-sm mb-4 overflow-hidden">
        <div class="card-header bg-gradient-primary text-white p-0">
            <div class="position-relative">
                <div class="bg-pattern"></div>
                <div class="card-body position-relative">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <div class="avatar-xl bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center">
                                <i class="fas fa-user fa-3x text-white"></i>
                            </div>
                        </div>
                        <div class="col">
                            <h2 class="mb-1 text-white">{{ $employee->full_name }}</h2>
                            <p class="mb-2 text-white-50">{{ $employee->position }} • {{ $employee->department_text }}</p>
                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge bg-white text-dark">
                                    <i class="fas fa-id-badge me-1"></i>{{ $employee->employee_id }}
                                </span>
                                @if($employee->status == 'active')
                                    <span class="badge bg-success">{{ $employee->status_text }}</span>
                                @elseif($employee->status == 'inactive')
                                    <span class="badge bg-warning">{{ $employee->status_text }}</span>
                                @else
                                    <span class="badge bg-danger">{{ $employee->status_text }}</span>
                                @endif
                                
                                @if($employee->role == 'super_admin')
                                    <span class="badge bg-danger">{{ $employee->role_text }}</span>
                                @elseif($employee->role == 'it_admin')
                                    <span class="badge bg-warning">{{ $employee->role_text }}</span>
                                @else
                                    <span class="badge bg-info">{{ $employee->role_text }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="text-end">
                                <div class="text-white-50 small">อายุงาน</div>
                                <div class="h4 text-white mb-0">{{ $employee->years_of_service }} ปี</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Basic Information --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user me-2 text-primary-red"></i>ข้อมูลพื้นฐาน
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">ชื่อ-นามสกุล</label>
                            <div class="fw-semibold">{{ $employee->full_name }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">รหัสพนักงาน</label>
                            <div class="fw-semibold text-primary-red">{{ $employee->employee_id }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">อีเมล</label>
                            <div>
                                <a href="mailto:{{ $employee->email }}" class="text-decoration-none">
                                    {{ $employee->email }}
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">ชื่อผู้ใช้</label>
                            <div class="fw-semibold">{{ $employee->username }}</div>
                        </div>
                        @if($employee->phone)
                        <div class="col-md-6">
                            <label class="form-label text-muted">เบอร์โทรศัพท์</label>
                            <div>
                                <a href="tel:{{ $employee->phone }}" class="text-decoration-none">
                                    {{ $employee->phone }}
                                </a>
                            </div>
                        </div>
                        @endif
                        @if(in_array(auth()->user()->role, ['super_admin', 'it_admin']))
                        <div class="col-md-6">
                            <label class="form-label text-muted">รหัสผ่าน</label>
                            <div class="d-flex align-items-center">
                                <span class="password-display">••••••••</span>
                                @if(auth()->user()->role == 'super_admin')
                                <button type="button" class="btn btn-sm btn-outline-secondary ms-2" id="showPasswordBtn">
                                    <i class="fas fa-eye"></i>
                                </button>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Work Information --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-briefcase me-2 text-primary-red"></i>ข้อมูลการทำงาน
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">แผนก</label>
                            <div class="fw-semibold">{{ $employee->department_text }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">ตำแหน่ง</label>
                            <div class="fw-semibold">{{ $employee->position }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">วันที่เริ่มงาน</label>
                            <div class="fw-semibold">{{ $employee->hire_date->format('d/m/Y') }}</div>
                            <small class="text-muted">{{ $employee->hire_date->diffForHumans() }}</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">อายุงาน</label>
                            <div class="fw-semibold text-success">{{ $employee->years_of_service }} ปี</div>
                        </div>
                        @if($employee->salary)
                        <div class="col-md-6">
                            <label class="form-label text-muted">เงินเดือน</label>
                            <div class="fw-semibold text-primary-red">{{ $employee->formatted_salary }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Additional Information --}}
            @if($employee->address || $employee->emergency_contact || $employee->notes)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2 text-primary-red"></i>ข้อมูลเพิ่มเติม
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @if($employee->address)
                        <div class="col-12">
                            <label class="form-label text-muted">ที่อยู่</label>
                            <div class="fw-semibold">{{ $employee->address }}</div>
                        </div>
                        @endif
                        @if($employee->emergency_contact)
                        <div class="col-md-6">
                            <label class="form-label text-muted">ผู้ติดต่อฉุกเฉิน</label>
                            <div class="fw-semibold">{{ $employee->emergency_contact }}</div>
                        </div>
                        @endif
                        @if($employee->emergency_phone)
                        <div class="col-md-6">
                            <label class="form-label text-muted">เบอร์ติดต่อฉุกเฉิน</label>
                            <div>
                                <a href="tel:{{ $employee->emergency_phone }}" class="text-decoration-none">
                                    {{ $employee->emergency_phone }}
                                </a>
                            </div>
                        </div>
                        @endif
                        @if($employee->notes)
                        <div class="col-12">
                            <label class="form-label text-muted">หมายเหตุ</label>
                            <div class="fw-semibold">{{ $employee->notes }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Side Information --}}
        <div class="col-lg-4">
            {{-- Quick Actions --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-cogs me-2 text-primary-red"></i>การจัดการ
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('employees.edit', $employee) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>แก้ไขข้อมูล
                        </a>
                        <button type="button" class="btn btn-info" id="resetPasswordBtn">
                            <i class="fas fa-key me-2"></i>รีเซ็ตรหัสผ่าน
                        </button>
                        <button type="button" class="btn btn-success" id="sendCredentialsBtn">
                            <i class="fas fa-envelope me-2"></i>ส่งข้อมูลเข้าสู่ระบบ
                        </button>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-h me-2"></i>เพิ่มเติม
                            </button>
                            <ul class="dropdown-menu w-100">
                                <li>
                                    <a class="dropdown-item" href="#" onclick="printEmployee()">
                                        <i class="fas fa-print me-2"></i>พิมพ์ข้อมูล
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#" onclick="exportEmployee()">
                                        <i class="fas fa-download me-2"></i>ส่งออกข้อมูล
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="#" id="deleteEmployeeBtn">
                                        <i class="fas fa-trash me-2"></i>ลบพนักงาน
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Statistics --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line me-2 text-primary-red"></i>สถิติ
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3 text-center">
                        <div class="col-6">
                            <div class="border rounded p-3">
                                <div class="h4 mb-1 text-primary-red">{{ $employee->years_of_service }}</div>
                                <div class="small text-muted">ปีการทำงาน</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-3">
                                <div class="h4 mb-1 text-success">{{ $employee->hire_date->format('m') }}</div>
                                <div class="small text-muted">เดือนเริ่มงาน</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="border rounded p-3">
                                <div class="h4 mb-1 text-info">{{ $employee->created_at->format('d/m/Y') }}</div>
                                <div class="small text-muted">วันที่เพิ่มในระบบ</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- System Information --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info me-2 text-primary-red"></i>ข้อมูลระบบ
                    </h5>
                </div>
                <div class="card-body">
                    <div class="small">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">สร้างเมื่อ:</span>
                            <span>{{ $employee->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">แก้ไขล่าสุด:</span>
                            <span>{{ $employee->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">ID:</span>
                            <span class="font-monospace">#{{ $employee->id }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/employees.js') }}"></script>

<script>
$(document).ready(function() {
    // Show password functionality (Super Admin only)
    @if(auth()->user()->role == 'super_admin')
    $('#showPasswordBtn').click(function() {
        const btn = $(this);
        const display = $('.password-display');
        
        if (btn.data('shown')) {
            display.text('••••••••');
            btn.html('<i class="fas fa-eye"></i>');
            btn.data('shown', false);
        } else {
            // Note: In production, consider fetching this via secure AJAX call
            display.text('{{ $employee->getRawOriginal("password") ?? "ไม่สามารถแสดงได้" }}');
            btn.html('<i class="fas fa-eye-slash"></i>');
            btn.data('shown', true);
        }
    });
    @endif

    // Reset password functionality
    $('#resetPasswordBtn').click(function() {
        Swal.fire({
            title: 'รีเซ็ตรหัสผ่าน',
            text: 'คุณต้องการสร้างรหัสผ่านใหม่สำหรับ {{ $employee->full_name }} หรือไม่?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0d6efd',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'รีเซ็ต',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                $.get('{{ route("employees.generateData") }}', { type: 'password' })
                    .done(function(data) {
                        // Update password via AJAX
                        $.ajax({
                            url: '{{ route("employees.update", $employee) }}',
                            type: 'PUT',
                            data: {
                                _token: '{{ csrf_token() }}',
                                password: data.password,
                                // Include current data to avoid validation errors
                                prefix: '{{ $employee->prefix }}',
                                first_name: '{{ $employee->first_name }}',
                                last_name: '{{ $employee->last_name }}',
                                department: '{{ $employee->department }}',
                                position: '{{ $employee->position }}',
                                hire_date: '{{ $employee->hire_date->format("Y-m-d") }}',
                                status: '{{ $employee->status }}',
                                role: '{{ $employee->role }}'
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: 'สำเร็จ!',
                                    html: `รหัสผ่านใหม่: <strong>${data.password}</strong><br><small>กรุณาบันทึกรหัสผ่านนี้</small>`,
                                    icon: 'success',
                                    confirmButtonText: 'ตกลง'
                                });
                            },
                            error: function() {
                                Swal.fire('เกิดข้อผิดพลาด!', 'ไม่สามารถรีเซ็ตรหัสผ่านได้', 'error');
                            }
                        });
                    });
            }
        });
    });

    // Send credentials functionality
    $('#sendCredentialsBtn').click(function() {
        Swal.fire({
            title: 'ส่งข้อมูลเข้าสู่ระบบ',
            text: 'ส่งชื่อผู้ใช้และรหัสผ่านไปยังอีเมล {{ $employee->email }} หรือไม่?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'ส่ง',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                // Simulate sending email (implement actual email sending in production)
                Swal.fire({
                    title: 'กำลังส่ง...',
                    text: 'กรุณารอสักครู่',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                        
                        // Simulate delay
                        setTimeout(() => {
                            Swal.fire({
                                title: 'ส่งสำเร็จ!',
                                text: 'ข้อมูลเข้าสู่ระบบได้ถูกส่งไปยังอีเมลแล้ว',
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }, 2000);
                    }
                });
            }
        });
    });

    // Delete employee functionality
    $('#deleteEmployeeBtn').click(function() {
        Swal.fire({
            title: 'ยืนยันการลบ',
            text: 'คุณต้องการลบพนักงาน "{{ $employee->full_name }}" หรือไม่?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'ลบ',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("employees.destroy", $employee) }}',
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'สำเร็จ!',
                                text: response.message,
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = '{{ route("employees.index") }}';
                            });
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        Swal.fire({
                            title: 'เกิดข้อผิดพลาด!',
                            text: response?.message || 'ไม่สามารถลบข้อมูลได้',
                            icon: 'error'
                        });
                    }
                });
            }
        });
    });
});

// Print employee function
function printEmployee() {
    window.print();
}

// Export employee function
function exportEmployee() {
    const data = {
        employee_id: '{{ $employee->employee_id }}',
        full_name: '{{ $employee->full_name }}',
        email: '{{ $employee->email }}',
        department: '{{ $employee->department_text }}',
        position: '{{ $employee->position }}',
        hire_date: '{{ $employee->hire_date->format("d/m/Y") }}',
        status: '{{ $employee->status_text }}'
    };
    
    const csvContent = "data:text/csv;charset=utf-8," 
        + Object.keys(data).join(",") + "\n"
        + Object.values(data).join(",");
    
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", `employee_${data.employee_id}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>

<style>
@media print {
    .btn, .card-header, nav, .breadcrumb {
        display: none !important;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
    }
}

.bg-pattern {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="2" fill="white" fill-opacity="0.1"/><circle cx="80" cy="80" r="2" fill="white" fill-opacity="0.1"/><circle cx="80" cy="20" r="1" fill="white" fill-opacity="0.05"/><circle cx="20" cy="80" r="1" fill="white" fill-opacity="0.05"/></svg>');
}

.avatar-xl {
    width: 80px;
    height: 80px;
}
</style>
@endsection
