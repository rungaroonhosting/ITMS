@extends('layouts.app')

@section('title', 'รายละเอียดแผนก - ' . $department->name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('departments.index') }}">จัดการแผนก</a></li>
    <li class="breadcrumb-item active">{{ $department->name }}</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-primary fw-bold">
                    <i class="fas fa-building me-2"></i>{{ $department->name }}
                </h1>
                <p class="text-muted mb-0">รายละเอียดและพนักงานในแผนก</p>
            </div>
            <div class="d-flex gap-2">
                @if(auth()->user()->role === 'super_admin' || auth()->user()->role === 'it_admin')
                    <a href="{{ route('departments.edit', $department) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-1"></i>แก้ไข
                    </a>
                @endif
                <a href="{{ route('departments.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>กลับ
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Department Info Card -->
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-header bg-gradient-primary text-white">
        <div class="d-flex align-items-center">
            <div class="flex-shrink-0">
                <div class="bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="fas fa-building fa-2x text-white"></i>
                </div>
            </div>
            <div class="flex-grow-1 ms-3">
                <h4 class="mb-1">{{ $department->name }}</h4>
                <div class="d-flex gap-3">
                    <span><i class="fas fa-code me-1"></i>{{ $department->code }}</span>
                    <span><i class="fas fa-users me-1"></i>{{ $department->employees->count() }} พนักงาน</span>
                    <span>
                        <i class="fas fa-{{ $department->is_active ? 'check-circle' : 'times-circle' }} me-1"></i>
                        {{ $department->status_display }}
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <h6>รายละเอียด</h6>
                <p class="text-muted mb-0">
                    {{ $department->description ?: 'ไม่มีรายละเอียด' }}
                </p>
                
                @if($department->isAccounting())
                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-bolt me-2"></i>
                        <strong>รองรับโปรแกรม Express!</strong> แผนกนี้สามารถสร้าง Username และ Password สำหรับโปรแกรม Express ได้
                    </div>
                @endif
            </div>
            <div class="col-md-4">
                <h6>สถิติ</h6>
                <div class="row g-3">
                    <div class="col-6">
                        <div class="text-center p-3 bg-success bg-opacity-10 rounded">
                            <div class="h4 mb-0 text-success">{{ $department->activeEmployees->count() }}</div>
                            <small class="text-muted">ใช้งาน</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center p-3 bg-warning bg-opacity-10 rounded">
                            <div class="h4 mb-0 text-warning">{{ $department->employees->where('status', 'inactive')->count() }}</div>
                            <small class="text-muted">ไม่ใช้งาน</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center p-3 bg-info bg-opacity-10 rounded">
                            <div class="h4 mb-0 text-info">{{ $department->employees->whereIn('role', ['manager', 'hr'])->count() }}</div>
                            <small class="text-muted">ผู้จัดการ</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center p-3 bg-warning bg-opacity-10 rounded">
                            <div class="h4 mb-0 text-warning">{{ $department->employees->whereNotNull('express_username')->count() }}</div>
                            <small class="text-muted">Express</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row text-center">
            <div class="col-md-3 mb-2">
                <a href="{{ route('employees.create') }}?department={{ $department->id }}" class="btn btn-outline-primary w-100">
                    <i class="fas fa-user-plus me-1"></i>เพิ่มพนักงาน
                </a>
                <div class="form-text mt-1">
                    <small class="text-muted">เพิ่มพนักงานในแผนกนี้</small>
                </div>
            </div>
            <div class="col-md-3 mb-2">
                <button type="button" class="btn btn-outline-info w-100" onclick="exportEmployees()">
                    <i class="fas fa-file-excel me-1"></i>ส่งออกรายชื่อ
                </button>
                <div class="form-text mt-1">
                    <small class="text-muted">ส่งออกพนักงานในแผนก</small>
                </div>
            </div>
            <div class="col-md-3 mb-2">
                <button type="button" class="btn btn-outline-success w-100" onclick="printDepartment()">
                    <i class="fas fa-print me-1"></i>พิมพ์รายงาน
                </button>
                <div class="form-text mt-1">
                    <small class="text-muted">พิมพ์รายงานแผนก</small>
                </div>
            </div>
            @if(auth()->user()->role === 'super_admin' || auth()->user()->role === 'it_admin')
                <div class="col-md-3 mb-2">
                    <button type="button" class="btn btn-outline-warning w-100" data-bs-toggle="modal" data-bs-target="#transferModal">
                        <i class="fas fa-exchange-alt me-1"></i>ย้ายพนักงาน
                    </button>
                    <div class="form-text mt-1">
                        <small class="text-muted">ย้ายพนักงานไปแผนกอื่น</small>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Employees List -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="mb-0">พนักงานในแผนก ({{ $department->employees->count() }} คน)</h6>
            <div class="d-flex gap-2">
                <div class="input-group" style="width: 300px;">
                    <span class="input-group-text">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" class="form-control" id="searchEmployee" placeholder="ค้นหาพนักงาน...">
                </div>
                <select class="form-select" id="statusFilter" style="width: 150px;">
                    <option value="">สถานะทั้งหมด</option>
                    <option value="active">ใช้งาน</option>
                    <option value="inactive">ไม่ใช้งาน</option>
                </select>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="employeesTable">
                <thead class="table-light">
                    <tr>
                        <th>รหัส</th>
                        <th>ชื่อ-นามสกุล</th>
                        <th>ตำแหน่ง</th>
                        <th>อีเมล</th>
                        <th>เบอร์โทร</th>
                        <th>Express</th>
                        <th>สถานะ</th>
                        <th width="120">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($department->employees as $employee)
                        <tr data-status="{{ $employee->status }}" data-id="{{ $employee->id }}">
                            <td>
                                <span class="badge bg-secondary">{{ $employee->employee_code }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                            <i class="fas fa-user text-muted"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $employee->full_name_th }}</div>
                                        <small class="text-muted">{{ $employee->full_name_en }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>{{ $employee->position }}</div>
                                <small class="text-muted">{{ $employee->role_display }}</small>
                            </td>
                            <td>
                                <a href="mailto:{{ $employee->email }}" class="text-decoration-none">
                                    {{ $employee->email }}
                                </a>
                            </td>
                            <td>
                                @if($employee->phone)
                                    <a href="tel:{{ $employee->phone }}" class="text-decoration-none">
                                        {{ $employee->phone }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($employee->express_username)
                                    <span class="badge bg-warning text-dark">
                                        <i class="fas fa-bolt me-1"></i>{{ $employee->express_username }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $employee->status === 'active' ? 'success' : 'secondary' }}">
                                    {{ $employee->status_display }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('employees.show', $employee) }}" 
                                       class="btn btn-sm btn-outline-info" 
                                       data-bs-toggle="tooltip" title="ดูรายละเอียด">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(auth()->user()->role === 'super_admin' || auth()->user()->role === 'it_admin' || 
                                        (auth()->user()->role === 'hr' && $employee->role === 'employee'))
                                        <a href="{{ route('employees.edit', $employee) }}" 
                                           class="btn btn-sm btn-outline-warning" 
                                           data-bs-toggle="tooltip" title="แก้ไข">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-users fa-3x mb-3"></i>
                                    <p>ยังไม่มีพนักงานในแผนกนี้</p>
                                    <a href="{{ route('employees.create') }}?department={{ $department->id }}" class="btn btn-primary">
                                        <i class="fas fa-user-plus me-1"></i>เพิ่มพนักงานคนแรก
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if(auth()->user()->role === 'super_admin' || auth()->user()->role === 'it_admin')
<!-- Transfer Modal -->
<div class="modal fade" id="transferModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-exchange-alt me-2"></i>ย้ายพนักงาน
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="transferForm">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        เลือกพนักงานและแผนกปลายทางที่ต้องการย้าย
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">พนักงานที่ต้องการย้าย:</label>
                        <select class="form-select" id="employeeToTransfer" multiple size="5">
                            @foreach($department->employees as $employee)
                                <option value="{{ $employee->id }}">
                                    {{ $employee->employee_code }} - {{ $employee->full_name_th }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">กด Ctrl+Click เพื่อเลือกหลายคน</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">แผนกปลายทาง:</label>
                        <select class="form-select" id="targetDepartment">
                            <option value="">เลือกแผนกปลายทาง</option>
                            @foreach(\App\Models\Department::where('id', '!=', $department->id)->where('is_active', true)->get() as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-warning">ย้ายพนักงาน</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Search and filter functionality
    const searchInput = document.getElementById('searchEmployee');
    const statusFilter = document.getElementById('statusFilter');
    const table = document.getElementById('employeesTable');
    
    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusTerm = statusFilter.value;
        const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
        
        for (let row of rows) {
            if (row.querySelector('td')) {
                const name = row.cells[1].textContent.toLowerCase();
                const email = row.cells[3].textContent.toLowerCase();
                const position = row.cells[2].textContent.toLowerCase();
                const status = row.dataset.status;
                
                const matchesSearch = name.includes(searchTerm) || 
                                    email.includes(searchTerm) || 
                                    position.includes(searchTerm);
                const matchesStatus = !statusTerm || status === statusTerm;
                
                row.style.display = matchesSearch && matchesStatus ? '' : 'none';
            }
        }
    }
    
    searchInput.addEventListener('input', filterTable);
    statusFilter.addEventListener('change', filterTable);
    
    @if(auth()->user()->role === 'super_admin' || auth()->user()->role === 'it_admin')
    // Transfer form
    document.getElementById('transferForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const selectedEmployees = Array.from(document.getElementById('employeeToTransfer').selectedOptions)
                                       .map(option => option.value);
        const targetDepartment = document.getElementById('targetDepartment').value;
        
        if (selectedEmployees.length === 0) {
            showNotification('กรุณาเลือกพนักงานที่ต้องการย้าย', 'warning');
            return;
        }
        
        if (!targetDepartment) {
            showNotification('กรุณาเลือกแผนกปลายทาง', 'warning');
            return;
        }
        
        if (confirm(`ต้องการย้ายพนักงาน ${selectedEmployees.length} คนไปแผนกใหม่หรือไม่?`)) {
            // In real implementation, this would be an AJAX call
            showNotification('ฟีเจอร์ย้ายพนักงานจะพร้อมใช้งานเร็วๆ นี้', 'info');
            bootstrap.Modal.getInstance(document.getElementById('transferModal')).hide();
        }
    });
    @endif
});

// Export functions
function exportEmployees() {
    window.location.href = `/departments/{{ $department->id }}/export-employees`;
}

function printDepartment() {
    window.print();
}

// Notification function
function showNotification(message, type = 'info') {
    const alertClass = {
        'success': 'alert-success',
        'error': 'alert-danger',
        'warning': 'alert-warning',
        'info': 'alert-info'
    }[type] || 'alert-info';
    
    const iconClass = {
        'success': 'fa-check-circle',
        'error': 'fa-exclamation-triangle',
        'warning': 'fa-exclamation-circle',
        'info': 'fa-info-circle'
    }[type] || 'fa-info-circle';
    
    const alert = document.createElement('div');
    alert.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
    alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);';
    alert.innerHTML = `
        <i class="fas ${iconClass} me-2"></i>${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alert);
    
    setTimeout(() => {
        if (alert.parentNode) {
            alert.remove();
        }
    }, 5000);
}
</script>

<style>
@media print {
    .btn, .input-group, .modal, .card-header .d-flex > div:last-child {
        display: none !important;
    }
    
    .card {
        border: 1px solid #000 !important;
        box-shadow: none !important;
    }
    
    .table {
        border: 1px solid #000;
    }
    
    .table th, .table td {
        border: 1px solid #000 !important;
    }
}
</style>
@endpush