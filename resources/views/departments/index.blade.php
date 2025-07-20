@extends('layouts.app')

@section('title', 'จัดการแผนก')

@section('breadcrumb')
    <li class="breadcrumb-item active">จัดการแผนก</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-primary fw-bold">
                    <i class="fas fa-building me-2"></i>จัดการแผนก
                </h1>
                <p class="text-muted mb-0">จัดการแผนกงานในองค์กร</p>
            </div>
            @if(auth()->user()->role === 'super_admin' || auth()->user()->role === 'it_admin')
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#bulkActionModal">
                        <i class="fas fa-tasks me-1"></i>จัดการหลายรายการ
                    </button>
                    <a href="{{ route('departments.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>เพิ่มแผนกใหม่
                    </a>
                </div>
            @endif
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
                            <i class="fas fa-building text-primary fa-2x"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="text-muted small">แผนกทั้งหมด</div>
                        <div class="h4 mb-0 fw-bold">{{ $departments->count() }}</div>
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
                            <i class="fas fa-check-circle text-success fa-2x"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="text-muted small">ใช้งาน</div>
                        <div class="h4 mb-0 fw-bold text-success">{{ $departments->where('is_active', true)->count() }}</div>
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
                            <i class="fas fa-pause-circle text-warning fa-2x"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="text-muted small">ไม่ใช้งาน</div>
                        <div class="h4 mb-0 fw-bold text-warning">{{ $departments->where('is_active', false)->count() }}</div>
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
                            <i class="fas fa-users text-info fa-2x"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="text-muted small">พนักงานทั้งหมด</div>
                        <div class="h4 mb-0 fw-bold text-info">{{ $departments->sum('employees_count') }}</div>
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
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" class="form-control" id="searchInput" placeholder="ค้นหาแผนก...">
                </div>
            </div>
            <div class="col-md-3">
                <select class="form-select" id="statusFilter">
                    <option value="">สถานะทั้งหมด</option>
                    <option value="active">ใช้งาน</option>
                    <option value="inactive">ไม่ใช้งาน</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="button" class="btn btn-outline-secondary w-100" id="clearFilters">
                    <i class="fas fa-times me-1"></i>ล้างตัวกรอง
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Departments Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="mb-0">รายการแผนก</h6>
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
            <table class="table table-hover mb-0" id="departmentsTable">
                <thead class="table-light">
                    <tr>
                        @if(auth()->user()->role === 'super_admin' || auth()->user()->role === 'it_admin')
                            <th width="50">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="selectAll">
                                </div>
                            </th>
                        @endif
                        <th>รหัส</th>
                        <th>ชื่อแผนก</th>
                        <th>รายละเอียด</th>
                        <th>พนักงาน</th>
                        <th>สถานะ</th>
                        <th>วันที่สร้าง</th>
                        <th width="120">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($departments as $department)
                        <tr data-status="{{ $department->is_active ? 'active' : 'inactive' }}" data-id="{{ $department->id }}">
                            @if(auth()->user()->role === 'super_admin' || auth()->user()->role === 'it_admin')
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input department-checkbox" type="checkbox" value="{{ $department->id }}">
                                    </div>
                                </td>
                            @endif
                            <td>
                                <span class="badge bg-primary">{{ $department->code }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="fas fa-building text-muted"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $department->name }}</div>
                                        @if($department->name === 'บัญชี' || $department->code === 'ACC')
                                            <small class="text-warning">
                                                <i class="fas fa-bolt me-1"></i>รองรับ Express
                                            </small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="text-truncate" style="max-width: 200px;">
                                    {{ $department->description ?: '-' }}
                                </div>
                            </td>
                            <td>
                                @if($department->employees_count > 0)
                                    <a href="{{ route('departments.show', $department) }}" class="text-decoration-none">
                                        <span class="badge bg-info">{{ $department->employees_count }} คน</span>
                                    </a>
                                @else
                                    <span class="text-muted">ไม่มีพนักงาน</span>
                                @endif
                            </td>
                            <td>
                                @if(auth()->user()->role === 'super_admin' || auth()->user()->role === 'it_admin')
                                    <div class="form-check form-switch">
                                        <input class="form-check-input status-toggle" type="checkbox" 
                                               data-id="{{ $department->id }}" 
                                               {{ $department->is_active ? 'checked' : '' }}>
                                        <label class="form-check-label small">
                                            {{ $department->status_display }}
                                        </label>
                                    </div>
                                @else
                                    <span class="badge bg-{{ $department->is_active ? 'success' : 'secondary' }}">
                                        {{ $department->status_display }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $department->created_at->format('d/m/Y') }}
                                </small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('departments.show', $department) }}" 
                                       class="btn btn-sm btn-outline-info" 
                                       data-bs-toggle="tooltip" title="ดูรายละเอียด">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(auth()->user()->role === 'super_admin' || auth()->user()->role === 'it_admin')
                                        <a href="{{ route('departments.edit', $department) }}" 
                                           class="btn btn-sm btn-outline-warning" 
                                           data-bs-toggle="tooltip" title="แก้ไข">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($department->employees_count == 0)
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger delete-btn" 
                                                    data-id="{{ $department->id }}" 
                                                    data-name="{{ $department->name }}"
                                                    data-bs-toggle="tooltip" title="ลบ">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->role === 'super_admin' || auth()->user()->role === 'it_admin' ? '8' : '7' }}" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-building fa-3x mb-3"></i>
                                    <p>ไม่มีข้อมูลแผนก</p>
                                    @if(auth()->user()->role === 'super_admin' || auth()->user()->role === 'it_admin')
                                        <a href="{{ route('departments.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus me-1"></i>เพิ่มแผนกแรก
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

@if(auth()->user()->role === 'super_admin' || auth()->user()->role === 'it_admin')
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
                    กรุณาเลือกแผนกที่ต้องการดำเนินการ
                </div>
                <div class="mb-3">
                    <label class="form-label">การดำเนินการ:</label>
                    <select class="form-select" id="bulkAction">
                        <option value="">เลือกการดำเนินการ</option>
                        <option value="activate">เปิดใช้งาน</option>
                        <option value="deactivate">ปิดใช้งาน</option>
                        <option value="delete">ลบ</option>
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
    
    // Search functionality
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const table = document.getElementById('departmentsTable');
    
    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusTerm = statusFilter.value;
        const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
        
        for (let row of rows) {
            if (row.querySelector('td')) {
                const name = row.cells[{{ auth()->user()->role === 'super_admin' || auth()->user()->role === 'it_admin' ? '2' : '1' }}].textContent.toLowerCase();
                const code = row.cells[{{ auth()->user()->role === 'super_admin' || auth()->user()->role === 'it_admin' ? '1' : '0' }}].textContent.toLowerCase();
                const status = row.dataset.status;
                
                const matchesSearch = name.includes(searchTerm) || code.includes(searchTerm);
                const matchesStatus = !statusTerm || status === statusTerm;
                
                row.style.display = matchesSearch && matchesStatus ? '' : 'none';
            }
        }
    }
    
    searchInput.addEventListener('input', filterTable);
    statusFilter.addEventListener('change', filterTable);
    
    // Clear filters
    document.getElementById('clearFilters').addEventListener('click', function() {
        searchInput.value = '';
        statusFilter.value = '';
        filterTable();
    });
    
    @if(auth()->user()->role === 'super_admin' || auth()->user()->role === 'it_admin')
    // Select all checkbox
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.department-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateSelectedCount();
    });
    
    // Individual checkboxes
    document.querySelectorAll('.department-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });
    
    function updateSelectedCount() {
        const selected = document.querySelectorAll('.department-checkbox:checked').length;
        document.getElementById('selectedCount').textContent = 
            selected > 0 ? `เลือกแล้ว ${selected} รายการ` : '';
    }
    
    // Status toggle
    document.querySelectorAll('.status-toggle').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const departmentId = this.dataset.id;
            const isActive = this.checked;
            
            fetch(`/departments/${departmentId}/toggle-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.nextElementSibling.textContent = data.is_active ? 'ใช้งาน' : 'ไม่ใช้งาน';
                    
                    // Show notification
                    showNotification(data.message, 'success');
                } else {
                    // Revert toggle on error
                    this.checked = !isActive;
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                // Revert toggle on error
                this.checked = !isActive;
                showNotification('เกิดข้อผิดพลาดในการเปลี่ยนสถานะ', 'error');
            });
        });
    });
    
    // Delete button
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const departmentId = this.dataset.id;
            const departmentName = this.dataset.name;
            
            if (confirm(`ต้องการลบแผนก "${departmentName}" หรือไม่?`)) {
                fetch(`/departments/${departmentId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => {
                    if (response.ok) {
                        location.reload();
                    } else {
                        return response.text().then(text => {
                            throw new Error(text);
                        });
                    }
                })
                .catch(error => {
                    showNotification('เกิดข้อผิดพลาดในการลบข้อมูล', 'error');
                });
            }
        });
    });
    
    // Bulk action
    document.getElementById('executeBulkAction').addEventListener('click', function() {
        const action = document.getElementById('bulkAction').value;
        const selected = Array.from(document.querySelectorAll('.department-checkbox:checked'))
                             .map(cb => cb.value);
        
        if (!action) {
            showNotification('กรุณาเลือกการดำเนินการ', 'warning');
            return;
        }
        
        if (selected.length === 0) {
            showNotification('กรุณาเลือกแผนกที่ต้องการดำเนินการ', 'warning');
            return;
        }
        
        if (confirm(`ต้องการ${getActionText(action)} ${selected.length} แผนกหรือไม่?`)) {
            fetch('/departments/bulk-action', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    action: action,
                    department_ids: selected
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                showNotification('เกิดข้อผิดพลาดในการดำเนินการ', 'error');
            });
            
            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('bulkActionModal')).hide();
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
        window.location.href = '{{ route("departments.exportExcel") }}';
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
});
</script>
@endpush