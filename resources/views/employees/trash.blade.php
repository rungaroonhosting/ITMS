@extends('layouts.app')

@section('title', 'ถังขยะ - พนักงาน')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('employees.index') }}">จัดการพนักงาน</a>
    </li>
    <li class="breadcrumb-item active">ถังขยะ</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-danger fw-bold">
                    <i class="fas fa-trash me-2"></i>ถังขยะ - พนักงาน
                </h1>
                <p class="text-muted mb-0">จัดการพนักงานที่ถูกลบแล้ว (เฉพาะ Super Admin)</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('employees.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-1"></i>กลับไปรายการพนักงาน
                </a>
                @if($trashedEmployees->count() > 0)
                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#bulkRestoreModal">
                        <i class="fas fa-undo me-1"></i>กู้คืนหลายรายการ
                    </button>
                    <button type="button" class="btn btn-danger" id="emptyTrashBtn">
                        <i class="fas fa-fire me-1"></i>ล้างถังขยะ
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Statistics -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-danger text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-trash fa-2x opacity-75"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="small opacity-75">พนักงานในถังขยะ</div>
                        <div class="h4 mb-0 fw-bold">{{ $trashedEmployees->count() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-warning text-dark">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-clock fa-2x opacity-75"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="small opacity-75">ลบล่าสุด</div>
                        <div class="small fw-bold">
                            @if($trashedEmployees->count() > 0)
                                {{ $trashedEmployees->first()->deleted_at->diffForHumans() }}
                            @else
                                -
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-info text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-shield-alt fa-2x opacity-75"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="small opacity-75">การป้องกัน</div>
                        <div class="small fw-bold">Soft Delete</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($trashedEmployees->count() > 0)
<!-- Warning Alert -->
<div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
    <i class="fas fa-exclamation-triangle me-3 fa-2x"></i>
    <div>
        <h6 class="mb-1">⚠️ คำเตือนสำคัญ</h6>
        <p class="mb-0">
            ข้อมูลในถังขยะยังสามารถ <strong>กู้คืนได้</strong> หากต้องการลบอย่างถาวร ให้ใช้ปุ่ม "ลบถาวร" หรือ "ล้างถังขยะ"
            <br><strong>การลบถาวรจะไม่สามารถกู้คืนได้อีก!</strong>
        </p>
    </div>
</div>

<!-- Search and Filter -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" class="form-control" id="searchTrashInput" placeholder="ค้นหาในถังขยะ...">
                </div>
            </div>
            <div class="col-md-3">
                <select class="form-select" id="deletedDateFilter">
                    <option value="">วันที่ลบทั้งหมด</option>
                    <option value="today">วันนี้</option>
                    <option value="week">สัปดาห์นี้</option>
                    <option value="month">เดือนนี้</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="button" class="btn btn-outline-secondary w-100" id="clearTrashFilters">
                    <i class="fas fa-times me-1"></i>ล้างตัวกรอง
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Trashed Employees Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="mb-0">รายการพนักงานในถังขยะ</h6>
            <div class="d-flex gap-2">
                <span class="badge bg-danger">{{ $trashedEmployees->count() }} รายการ</span>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="trashedEmployeesTable">
                <thead class="table-light">
                    <tr>
                        <th width="50">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="selectAllTrashed">
                            </div>
                        </th>
                        <th>รหัส</th>
                        <th>ชื่อ-นามสกุล</th>
                        <th>แผนก</th>
                        <th>อีเมล</th>
                        <th>วันที่ลบ</th>
                        <th width="180">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($trashedEmployees as $employee)
                        <tr data-id="{{ $employee->id }}" data-deleted-date="{{ $employee->deleted_at->format('Y-m-d') }}">
                            <td>
                                <div class="form-check">
                                    <input class="form-check-input trashed-employee-checkbox" type="checkbox" value="{{ $employee->id }}">
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-secondary opacity-75">{{ $employee->employee_code }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center opacity-50" style="width: 40px; height: 40px;">
                                            <i class="fas fa-user-slash text-muted"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-muted">{{ $employee->full_name_th }}</div>
                                        <small class="text-muted">{{ $employee->full_name_en }}</small>
                                        @if($employee->nickname)
                                            <div><small class="badge bg-light text-dark opacity-75">"{{ $employee->nickname }}"</small></div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($employee->department)
                                    <div class="d-flex align-items-center opacity-75">
                                        <span class="badge bg-secondary me-2">{{ $employee->department->code }}</span>
                                        <span class="text-muted">{{ $employee->department->name }}</span>
                                    </div>
                                @else
                                    <span class="text-muted">ไม่ระบุ</span>
                                @endif
                            </td>
                            <td>
                                <div class="text-muted">{{ $employee->email }}</div>
                                @if($employee->phone)
                                    <div><small class="text-muted opacity-75">{{ $employee->phone }}</small></div>
                                @endif
                            </td>
                            <td>
                                <div class="text-danger fw-bold">
                                    {{ $employee->deleted_at->format('d/m/Y H:i') }}
                                </div>
                                <small class="text-muted">{{ $employee->deleted_at->diffForHumans() }}</small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" 
                                            class="btn btn-sm btn-success restore-btn" 
                                            data-id="{{ $employee->id }}" 
                                            data-name="{{ $employee->full_name_th }}"
                                            data-bs-toggle="tooltip" title="กู้คืน">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                    
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-info preview-btn" 
                                            data-id="{{ $employee->id }}"
                                            data-bs-toggle="tooltip" title="ดูข้อมูล">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    
                                    <button type="button" 
                                            class="btn btn-sm btn-danger force-delete-btn" 
                                            data-id="{{ $employee->id }}" 
                                            data-name="{{ $employee->full_name_th }}"
                                            data-bs-toggle="tooltip" title="ลบถาวร">
                                        <i class="fas fa-fire"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Bulk Restore Modal -->
<div class="modal fade" id="bulkRestoreModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-undo me-2"></i>กู้คืนหลายรายการ
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    กรุณาเลือกพนักงานที่ต้องการกู้คืนจากถังขยะ
                </div>
                <div id="selectedTrashCount" class="text-muted"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-success" id="executeBulkRestore">
                    <i class="fas fa-undo me-1"></i>กู้คืนที่เลือก
                </button>
            </div>
        </div>
    </div>
</div>

@else
<!-- Empty State -->
<div class="card border-0 shadow-sm">
    <div class="card-body text-center py-5">
        <div class="text-muted">
            <i class="fas fa-trash fa-5x mb-4 opacity-25"></i>
            <h4>ถังขยะว่างเปล่า</h4>
            <p class="mb-4">ไม่มีพนักงานในถังขยะ</p>
            <a href="{{ route('employees.index') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left me-1"></i>กลับไปรายการพนักงาน
            </a>
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
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    // Search and filter functionality
    const searchInput = document.getElementById('searchTrashInput');
    const deletedDateFilter = document.getElementById('deletedDateFilter');
    const table = document.getElementById('trashedEmployeesTable');
    
    function filterTrashTable() {
        if (!table) return;
        
        const searchTerm = searchInput?.value.toLowerCase() || '';
        const dateFilter = deletedDateFilter?.value || '';
        const rows = table.getElementsByTagName('tbody')[0]?.getElementsByTagName('tr') || [];
        
        const today = new Date();
        const weekAgo = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
        const monthAgo = new Date(today.getTime() - 30 * 24 * 60 * 60 * 1000);
        
        for (let row of rows) {
            if (row.querySelector('td')) {
                const nameCell = row.cells[2];
                const emailCell = row.cells[4];
                const deletedDateStr = row.dataset.deletedDate;
                const deletedDate = new Date(deletedDateStr);
                
                const name = nameCell?.textContent.toLowerCase() || '';
                const email = emailCell?.textContent.toLowerCase() || '';
                
                const matchesSearch = name.includes(searchTerm) || email.includes(searchTerm);
                
                let matchesDate = true;
                if (dateFilter === 'today') {
                    matchesDate = deletedDate.toDateString() === today.toDateString();
                } else if (dateFilter === 'week') {
                    matchesDate = deletedDate >= weekAgo;
                } else if (dateFilter === 'month') {
                    matchesDate = deletedDate >= monthAgo;
                }
                
                row.style.display = matchesSearch && matchesDate ? '' : 'none';
            }
        }
        
        updateVisibleTrashCount();
    }
    
    function updateVisibleTrashCount() {
        if (!table) return;
        
        const visibleRows = Array.from(table.getElementsByTagName('tbody')[0]?.getElementsByTagName('tr') || [])
                                .filter(row => row.style.display !== 'none' && row.querySelector('td'));
        
        const headerBadge = document.querySelector('.card-header .badge');
        if (headerBadge) {
            const totalCount = {{ $trashedEmployees->count() }};
            const visibleCount = visibleRows.length;
            
            if (visibleCount === totalCount) {
                headerBadge.textContent = `${totalCount} รายการ`;
            } else {
                headerBadge.textContent = `${visibleCount}/${totalCount} รายการ`;
            }
        }
    }
    
    searchInput?.addEventListener('input', filterTrashTable);
    deletedDateFilter?.addEventListener('change', filterTrashTable);
    
    // Clear filters
    document.getElementById('clearTrashFilters')?.addEventListener('click', function() {
        if (searchInput) searchInput.value = '';
        if (deletedDateFilter) deletedDateFilter.value = '';
        filterTrashTable();
    });
    
    // Select all functionality
    document.getElementById('selectAllTrashed')?.addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.trashed-employee-checkbox');
        checkboxes.forEach(checkbox => {
            const row = checkbox.closest('tr');
            if (row.style.display !== 'none') {  
                checkbox.checked = this.checked;
            }
        });
        updateSelectedTrashCount();
    });
    
    // Individual checkboxes
    document.querySelectorAll('.trashed-employee-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedTrashCount);
    });
    
    function updateSelectedTrashCount() {
        const selected = document.querySelectorAll('.trashed-employee-checkbox:checked').length;
        const countElement = document.getElementById('selectedTrashCount');
        if (countElement) {
            countElement.textContent = selected > 0 ? `เลือกแล้ว ${selected} รายการ` : '';
        }
    }
    
    // Restore individual employee
    document.querySelectorAll('.restore-btn').forEach(btn => {
        btn.addEventListener('click', async function() {
            const employeeId = this.dataset.id;
            const employeeName = this.dataset.name;
            
            if (!confirm(`ต้องการกู้คืนพนักงาน "${employeeName}" หรือไม่?`)) {
                return;
            }
            
            try {
                const response = await fetch(`/employees/${employeeId}/restore`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showNotification(data.message, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification(data.message, 'error');
                }
            } catch (error) {
                showNotification('เกิดข้อผิดพลาดในการกู้คืน', 'error');
            }
        });
    });
    
    // Force delete individual employee
    document.querySelectorAll('.force-delete-btn').forEach(btn => {
        btn.addEventListener('click', async function() {
            const employeeId = this.dataset.id;
            const employeeName = this.dataset.name;
            
            if (!confirm(`⚠️ ต้องการลบพนักงาน "${employeeName}" อย่างถาวรหรือไม่?\n\nการลบถาวรจะไม่สามารถกู้คืนได้อีก!`)) {
                return;
            }
            
            if (!confirm(`❌ ยืนยันอีกครั้ง: ลบ "${employeeName}" อย่างถาวร?\n\nข้อมูลจะหายไปตลอดกาล!`)) {
                return;
            }
            
            try {
                const response = await fetch(`/employees/${employeeId}/force-delete`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showNotification(data.message, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification(data.message, 'error');
                }
            } catch (error) {
                showNotification('เกิดข้อผิดพลาดในการลบถาวร', 'error');
            }
        });
    });
    
    // Preview employee
    document.querySelectorAll('.preview-btn').forEach(btn => {
        btn.addEventListener('click', async function() {
            const employeeId = this.dataset.id;
            
            try {
                const response = await fetch(`/employees/${employeeId}/preview`);
                const data = await response.json();
                
                if (data.success) {
                    // Show employee data in modal or alert
                    const employee = data.data;
                    alert(`ข้อมูลพนักงาน:\n\nชื่อ: ${employee.first_name_th} ${employee.last_name_th}\nอีเมล: ${employee.email}\nแผนก: ${employee.department?.name || 'ไม่ระบุ'}\nสถานะ: ${employee.status_display}\nลบเมื่อ: ${new Date(employee.deleted_at).toLocaleString('th-TH')}`);
                } else {
                    showNotification(data.message, 'error');
                }
            } catch (error) {
                showNotification('เกิดข้อผิดพลาดในการดูข้อมูล', 'error');
            }
        });
    });
    
    // Bulk restore
    document.getElementById('executeBulkRestore')?.addEventListener('click', async function() {
        const selected = Array.from(document.querySelectorAll('.trashed-employee-checkbox:checked'))
                             .map(cb => cb.value);
        
        if (selected.length === 0) {
            showNotification('กรุณาเลือกพนักงานที่ต้องการกู้คืน', 'warning');
            return;
        }
        
        if (!confirm(`ต้องการกู้คืนพนักงาน ${selected.length} คนหรือไม่?`)) {
            return;
        }
        
        try {
            const response = await fetch('/employees/bulk-restore', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ employee_ids: selected })
            });
            
            const data = await response.json();
            
            if (data.success) {
                showNotification(data.message, 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showNotification(data.message, 'error');
            }
        } catch (error) {
            showNotification('เกิดข้อผิดพลาดในการกู้คืนหลายรายการ', 'error');
        } finally {
            bootstrap.Modal.getInstance(document.getElementById('bulkRestoreModal'))?.hide();
        }
    });
    
    // Empty trash
    document.getElementById('emptyTrashBtn')?.addEventListener('click', async function() {
        const trashCount = {{ $trashedEmployees->count() }};
        
        if (!confirm(`⚠️ ต้องการล้างถังขยะ (ลบ ${trashCount} คนอย่างถาวร) หรือไม่?\n\nข้อมูลทั้งหมดจะหายไปตลอดกาล!`)) {
            return;
        }
        
        if (!confirm(`❌ ยืนยันอีกครั้ง: ล้างถังขยะทั้งหมด?\n\nการกระทำนี้ไม่สามารถยกเลิกได้!`)) {
            return;
        }
        
        if (!confirm(`🔥 ยืนยันครั้งสุดท้าย: ลบข้อมูล ${trashCount} คนอย่างถาวร?`)) {
            return;
        }
        
        try {
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>กำลังล้าง...';
            
            const response = await fetch('/employees/empty-trash', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                showNotification(data.message, 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showNotification(data.message, 'error');
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-fire me-1"></i>ล้างถังขยะ';
            }
        } catch (error) {
            showNotification('เกิดข้อผิดพลาดในการล้างถังขยะ', 'error');
            this.disabled = false;
            this.innerHTML = '<i class="fas fa-fire me-1"></i>ล้างถังขยะ';
        }
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
    updateVisibleTrashCount();
    
    console.log('🗑️ Trash Management initialized - Super Admin only');
});
</script>

<style>
.opacity-25 { opacity: 0.25 !important; }
.opacity-50 { opacity: 0.5 !important; }
.opacity-75 { opacity: 0.75 !important; }

/* Trash table styles */
#trashedEmployeesTable tbody tr {
    background-color: rgba(248, 249, 250, 0.5);
}

#trashedEmployeesTable tbody tr:hover {
    background-color: rgba(248, 249, 250, 0.8) !important;
}

/* Deleted date highlighting */
.text-danger.fw-bold {
    font-family: 'Courier New', monospace;
}

/* Button animations */
.restore-btn:hover {
    transform: scale(1.1);
    transition: transform 0.2s ease;
}

.force-delete-btn:hover {
    transform: scale(1.1);
    animation: pulse-danger 1s infinite;
}

@keyframes pulse-danger {
    0% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7); }
    70% { box-shadow: 0 0 0 10px rgba(220, 53, 69, 0); }
    100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
}

/* Empty state styling */
.fa-5x {
    font-size: 5em !important;
}
</style>
@endpush
