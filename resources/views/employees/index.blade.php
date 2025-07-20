@extends('layouts.app')

@section('title', 'จัดการพนักงาน')

@section('breadcrumb')
    <li class="breadcrumb-item active">จัดการพนักงาน</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-primary fw-bold">
                    <i class="fas fa-users me-2"></i>จัดการพนักงาน
                </h1>
                <p class="text-muted mb-0">จัดการข้อมูลพนักงานในองค์กร</p>
            </div>
            <div class="d-flex gap-2">
                @if(auth()->user()->role === 'super_admin' || auth()->user()->role === 'it_admin' || auth()->user()->role === 'hr')
                    <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#bulkActionModal">
                        <i class="fas fa-tasks me-1"></i>จัดการหลายรายการ
                    </button>
                @endif
                @if(auth()->user()->role !== 'employee')
                    <a href="{{ route('employees.create') }}" class="btn btn-primary">
                        <i class="fas fa-user-plus me-1"></i>เพิ่มพนักงาน
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-users text-primary fa-2x"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="text-muted small">พนักงานทั้งหมด</div>
                        <div class="h4 mb-0 fw-bold">{{ $employees->count() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-success bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-user-check text-success fa-2x"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="text-muted small">ใช้งาน</div>
                        <div class="h4 mb-0 fw-bold text-success">{{ $employees->where('status', 'active')->count() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-bolt text-warning fa-2x"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="text-muted small">Express</div>
                        <div class="h4 mb-0 fw-bold text-warning">{{ $employees->whereNotNull('express_username')->count() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-info bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-building text-info fa-2x"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="text-muted small">แผนก</div>
                        <div class="h4 mb-0 fw-bold text-info">{{ $departments->count() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Search and Filter -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" class="form-control" id="searchInput" placeholder="ค้นหาพนักงาน...">
                </div>
            </div>
            <div class="col-md-2">
                <select class="form-select" id="departmentFilter">
                    <option value="">แผนกทั้งหมด</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" id="roleFilter">
                    <option value="">Role ทั้งหมด</option>
                    <option value="super_admin">Super Admin</option>
                    <option value="it_admin">IT Admin</option>
                    <option value="hr">HR</option>
                    <option value="manager">Manager</option>
                    <option value="express">Express</option>
                    <option value="employee">Employee</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" id="statusFilter">
                    <option value="">สถานะทั้งหมด</option>
                    <option value="active">ใช้งาน</option>
                    <option value="inactive">ไม่ใช้งาน</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-outline-secondary w-100" id="clearFilters">
                    <i class="fas fa-times me-1"></i>ล้างตัวกรอง
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Employees Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="mb-0">รายการพนักงาน</h6>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="window.print()">
                    <i class="fas fa-print me-1"></i>พิมพ์
                </button>
                <button type="button" class="btn btn-sm btn-outline-success" id="exportBtn">
                    <i class="fas fa-file-excel me-1"></i>ส่งออก
                </button>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="employeesTable">
                <thead class="table-light">
                    <tr>
                        @if(auth()->user()->role === 'super_admin' || auth()->user()->role === 'it_admin' || auth()->user()->role === 'hr')
                            <th width="50">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="selectAll">
                                </div>
                            </th>
                        @endif
                        <th>รหัส</th>
                        <th>ชื่อ-นามสกุล</th>
                        <th>แผนก</th>
                        <th>ตำแหน่ง</th>
                        <th>อีเมล</th>
                        <th>Express</th>
                        <th>สถานะ</th>
                        <th width="120">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $employee)
                        <tr data-status="{{ $employee->status }}" 
                            data-department="{{ $employee->department_id }}" 
                            data-role="{{ $employee->role }}"
                            data-id="{{ $employee->id }}">
                            @if(auth()->user()->role === 'super_admin' || auth()->user()->role === 'it_admin' || auth()->user()->role === 'hr')
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input employee-checkbox" type="checkbox" value="{{ $employee->id }}">
                                    </div>
                                </td>
                            @endif
                            <td>
                                <span class="badge bg-secondary">{{ $employee->employee_code }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="fas fa-user text-muted"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $employee->full_name_th }}</div>
                                        <small class="text-muted">{{ $employee->full_name_en }}</small>
                                        @if($employee->nickname)
                                            <div><small class="badge bg-light text-dark">"{{ $employee->nickname }}"</small></div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($employee->department)
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-info me-2">{{ $employee->department->code }}</span>
                                        <span>{{ $employee->department->name }}</span>
                                    </div>
                                @else
                                    <span class="text-muted">ไม่ระบุ</span>
                                @endif
                            </td>
                            <td>
                                <div>{{ $employee->position }}</div>
                                <small class="badge bg-{{ $employee->role === 'super_admin' ? 'danger' : ($employee->role === 'it_admin' ? 'warning' : 'secondary') }}">
                                    {{ $employee->role_display }}
                                </small>
                            </td>
                            <td>
                                <a href="mailto:{{ $employee->email }}" class="text-decoration-none">
                                    {{ $employee->email }}
                                </a>
                                @if($employee->phone)
                                    <div><small class="text-muted">{{ $employee->phone }}</small></div>
                                @endif
                            </td>
                            <td>
                                @if($employee->express_username)
                                    <span class="badge bg-warning text-dark">
                                        <i class="fas fa-bolt me-1"></i>{{ $employee->express_username }}
                                    </span>
                                    @if(auth()->user()->role === 'super_admin')
                                        <div><small class="text-muted">{{ $employee->express_password }}</small></div>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if(auth()->user()->role === 'super_admin' || auth()->user()->role === 'it_admin')
                                    <div class="form-check form-switch">
                                        <input class="form-check-input status-toggle" type="checkbox" 
                                               data-id="{{ $employee->id }}" 
                                               {{ $employee->status === 'active' ? 'checked' : '' }}>
                                        <label class="form-check-label small">
                                            {{ $employee->status_display }}
                                        </label>
                                    </div>
                                @else
                                    <span class="badge bg-{{ $employee->status === 'active' ? 'success' : 'secondary' }}">
                                        {{ $employee->status_display }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    @if(auth()->user()->id === $employee->id || auth()->user()->role === 'super_admin' || auth()->user()->role === 'it_admin' || auth()->user()->role === 'hr')
                                        <a href="{{ route('employees.show', $employee) }}" 
                                           class="btn btn-sm btn-outline-info" 
                                           data-bs-toggle="tooltip" title="ดูรายละเอียด">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    @endif
                                    
                                    @if(auth()->user()->role === 'super_admin' || auth()->user()->role === 'it_admin' || 
                                        (auth()->user()->role === 'hr' && $employee->role === 'employee') ||
                                        (auth()->user()->role === 'express' && $employee->department && ($employee->department->name === 'บัญชี' || $employee->department->isAccounting())))
                                        <a href="{{ route('employees.edit', $employee) }}" 
                                           class="btn btn-sm btn-outline-warning" 
                                           data-bs-toggle="tooltip" title="แก้ไข">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif
                                    
                                    @if(auth()->user()->role === 'super_admin')
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger delete-btn" 
                                                data-id="{{ $employee->id }}" 
                                                data-name="{{ $employee->full_name_th }}"
                                                data-bs-toggle="tooltip" title="ลบ">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->role === 'super_admin' || auth()->user()->role === 'it_admin' || auth()->user()->role === 'hr' ? '9' : '8' }}" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-users fa-3x mb-3"></i>
                                    <p>ไม่มีข้อมูลพนักงาน</p>
                                    @if(auth()->user()->role !== 'employee')
                                        <a href="{{ route('employees.create') }}" class="btn btn-primary">
                                            <i class="fas fa-user-plus me-1"></i>เพิ่มพนักงานคนแรก
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if(auth()->user()->role === 'super_admin' || auth()->user()->role === 'it_admin' || auth()->user()->role === 'hr')
<!-- Bulk Action Modal -->
<div class="modal fade" id="bulkActionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-tasks me-2"></i>จัดการหลายรายการ
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    กรุณาเลือกพนักงานที่ต้องการดำเนินการ
                </div>
                <div class="mb-3">
                    <label class="form-label">การดำเนินการ:</label>
                    <select class="form-select" id="bulkAction">
                        <option value="">เลือกการดำเนินการ</option>
                        <option value="activate">เปิดใช้งาน</option>
                        <option value="deactivate">ปิดใช้งาน</option>
                        @if(auth()->user()->role === 'super_admin')
                            <option value="delete">ลบ</option>
                        @endif
                    </select>
                </div>
                <div id="selectedCount" class="text-muted"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-primary" id="executeBulkAction">ดำเนินการ</button>
            </div>
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
    const searchInput = document.getElementById('searchInput');
    const departmentFilter = document.getElementById('departmentFilter');
    const roleFilter = document.getElementById('roleFilter');
    const statusFilter = document.getElementById('statusFilter');
    const table = document.getElementById('employeesTable');
    
    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const departmentTerm = departmentFilter.value;
        const roleTerm = roleFilter.value;
        const statusTerm = statusFilter.value;
        const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
        
        for (let row of rows) {
            if (row.querySelector('td')) {
                const nameCell = row.cells[{{ auth()->user()->role === 'super_admin' || auth()->user()->role === 'it_admin' || auth()->user()->role === 'hr' ? '2' : '1' }}];
                const emailCell = row.cells[{{ auth()->user()->role === 'super_admin' || auth()->user()->role === 'it_admin' || auth()->user()->role === 'hr' ? '5' : '4' }}];
                const name = nameCell.textContent.toLowerCase();
                const email = emailCell.textContent.toLowerCase();
                const department = row.dataset.department;
                const role = row.dataset.role;
                const status = row.dataset.status;
                
                const matchesSearch = name.includes(searchTerm) || email.includes(searchTerm);
                const matchesDepartment = !departmentTerm || department === departmentTerm;
                const matchesRole = !roleTerm || role === roleTerm;
                const matchesStatus = !statusTerm || status === statusTerm;
                
                row.style.display = matchesSearch && matchesDepartment && matchesRole && matchesStatus ? '' : 'none';
            }
        }
        
        updateVisibleCount();
    }
    
    function updateVisibleCount() {
        const visibleRows = Array.from(table.getElementsByTagName('tbody')[0].getElementsByTagName('tr'))
                                .filter(row => row.style.display !== 'none' && row.querySelector('td'));
        
        // Update header to show filtered count
        const headerText = document.querySelector('.card-header h6');
        if (headerText) {
            const totalCount = {{ $employees->count() }};
            const visibleCount = visibleRows.length;
            
            if (visibleCount === totalCount) {
                headerText.textContent = `รายการพนักงาน (${totalCount} คน)`;
            } else {
                headerText.textContent = `รายการพนักงาน (${visibleCount}/${totalCount} คน)`;
            }
        }
    }
    
    searchInput.addEventListener('input', filterTable);
    departmentFilter.addEventListener('change', filterTable);
    roleFilter.addEventListener('change', filterTable);
    statusFilter.addEventListener('change', filterTable);
    
    // Clear filters
    document.getElementById('clearFilters').addEventListener('click', function() {
        searchInput.value = '';
        departmentFilter.value = '';
        roleFilter.value = '';
        statusFilter.value = '';
        filterTable();
    });
    
    @if(auth()->user()->role === 'super_admin' || auth()->user()->role === 'it_admin' || auth()->user()->role === 'hr')
    // Select all checkbox
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.employee-checkbox');
        checkboxes.forEach(checkbox => {
            const row = checkbox.closest('tr');
            if (row.style.display !== 'none') {  // Only select visible rows
                checkbox.checked = this.checked;
            }
        });
        updateSelectedCount();
    });
    
    // Individual checkboxes
    document.querySelectorAll('.employee-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });
    
    function updateSelectedCount() {
        const selected = document.querySelectorAll('.employee-checkbox:checked').length;
        document.getElementById('selectedCount').textContent = 
            selected > 0 ? `เลือกแล้ว ${selected} รายการ` : '';
    }
    @endif
    
    @if(auth()->user()->role === 'super_admin' || auth()->user()->role === 'it_admin')
    // Status toggle
    document.querySelectorAll('.status-toggle').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const employeeId = this.dataset.id;
            const isActive = this.checked;
            
            // In real implementation, this would be an AJAX call
            console.log(`Toggle employee ${employeeId} to ${isActive ? 'active' : 'inactive'}`);
            
            this.nextElementSibling.textContent = isActive ? 'ใช้งาน' : 'ไม่ใช้งาน';
            showNotification(`${isActive ? 'เปิด' : 'ปิด'}ใช้งานพนักงานเรียบร้อยแล้ว`, 'success');
        });
    });
    @endif
    
    @if(auth()->user()->role === 'super_admin')
    // Delete button
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const employeeId = this.dataset.id;
            const employeeName = this.dataset.name;
            
            if (confirm(`ต้องการลบพนักงาน "${employeeName}" หรือไม่?\n\nการลบจะไม่สามารถกู้คืนได้`)) {
                // In real implementation, this would be an AJAX call
                console.log(`Delete employee ${employeeId}`);
                showNotification('ลบพนักงานเรียบร้อยแล้ว', 'success');
                
                // Remove row from table
                this.closest('tr').remove();
                updateVisibleCount();
            }
        });
    });
    @endif
    
    @if(auth()->user()->role === 'super_admin' || auth()->user()->role === 'it_admin' || auth()->user()->role === 'hr')
    // Bulk action
    document.getElementById('executeBulkAction').addEventListener('click', function() {
        const action = document.getElementById('bulkAction').value;
        const selected = Array.from(document.querySelectorAll('.employee-checkbox:checked'))
                             .map(cb => cb.value);
        
        if (!action) {
            showNotification('กรุณาเลือกการดำเนินการ', 'warning');
            return;
        }
        
        if (selected.length === 0) {
            showNotification('กรุณาเลือกพนักงานที่ต้องการดำเนินการ', 'warning');
            return;
        }
        
        if (confirm(`ต้องการ${getActionText(action)} ${selected.length} คนหรือไม่?`)) {
            // In real implementation, this would be an AJAX call
            console.log(`Bulk ${action} for employees:`, selected);
            showNotification(`${getActionText(action)}พนักงาน ${selected.length} คนเรียบร้อยแล้ว`, 'success');
            
            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('bulkActionModal')).hide();
            
            // Refresh page after some delay
            setTimeout(() => location.reload(), 1500);
        }
    });
    
    function getActionText(action) {
        switch(action) {
            case 'activate': return 'เปิดใช้งาน';
            case 'deactivate': return 'ปิดใช้งาน';
            case 'delete': return 'ลบ';
            default: return '';
        }
    }
    @endif
    
    // Export functionality
    document.getElementById('exportBtn').addEventListener('click', function() {
        window.location.href = '{{ route("employees.exportExcel") }}';
    });
    
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
    
    // Initialize count
    updateVisibleCount();
    
    // Role-based notifications
    @if(auth()->user()->role === 'express')
        console.log('⚡ Express User: Limited to accounting department employees');
    @elseif(auth()->user()->role === 'super_admin')
        console.log('🔧 Super Admin: Full access to all employees and actions');
    @elseif(auth()->user()->role === 'it_admin')
        console.log('💻 IT Admin: Full employee management access');
    @elseif(auth()->user()->role === 'hr')
        console.log('👥 HR: Employee management with restrictions');
    @else
        console.log('👤 Employee: View only access to own profile');
    @endif
});
</script>

<style>
@media print {
    .btn, .input-group, .modal, .card-header .d-flex > div:last-child, .form-check {
        display: none !important;
    }
    
    .card {
        border: 1px solid #000 !important;
        box-shadow: none !important;
    }
    
    .table {
        border: 1px solid #000;
        font-size: 12px;
    }
    
    .table th, .table td {
        border: 1px solid #000 !important;
        padding: 4px !important;
    }
    
    .badge {
        border: 1px solid #000 !important;
        background: white !important;
        color: black !important;
    }
}

/* Express user highlighting */
.badge.bg-warning.text-dark {
    background: linear-gradient(45deg, #ffc107, #fd7e14) !important;
    font-weight: 600;
    animation: express-glow 3s infinite alternate;
}

@keyframes express-glow {
    from { box-shadow: 0 0 5px rgba(255, 193, 7, 0.5); }
    to { box-shadow: 0 0 15px rgba(255, 193, 7, 0.8); }
}

/* Role badge colors */
.badge.bg-danger {
    background: linear-gradient(45deg, #dc3545, #e91e63) !important;
}

.badge.bg-warning {
    background: linear-gradient(45deg, #ffc107, #fd7e14) !important;
    color: #000 !important;
}

/* Row hover effects */
.table tbody tr:hover {
    background-color: rgba(13, 110, 253, 0.05) !important;
    transform: scale(1.01);
    transition: all 0.2s ease;
}
</style>
@endpush