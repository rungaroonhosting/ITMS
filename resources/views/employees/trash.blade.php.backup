@extends('layouts.app')

@section('title', 'ถังขยะพนักงาน')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h4>
                    <i class="fas fa-trash text-danger"></i> ถังขยะพนักงาน
                    <span class="badge bg-danger">{{ $trashedEmployees->total() }} คน</span>
                </h4>
                
                <div class="btn-group">
                    <a href="{{ route('employees.index') }}" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> กลับไปรายการพนักงาน
                    </a>
                    
                    @if($trashedEmployees->count() > 0)
                        <button type="button" class="btn btn-warning" onclick="bulkRestore()">
                            <i class="fas fa-undo"></i> กู้คืนที่เลือก
                        </button>
                        
                        <button type="button" class="btn btn-danger" onclick="confirmEmptyTrash()">
                            <i class="fas fa-fire"></i> ลบถาวรทั้งหมด
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Trash Table -->
    <div class="card">
        <div class="card-header bg-danger text-white">
            <h6 class="mb-0">
                <i class="fas fa-list"></i> พนักงานที่ถูกลบ
                @if($trashedEmployees->count() > 0)
                    <div class="float-end">
                        <input type="checkbox" id="selectAll" class="form-check-input me-2">
                        <label for="selectAll" class="form-check-label">เลือกทั้งหมด</label>
                    </div>
                @endif
            </h6>
        </div>
        <div class="card-body">
            @if($trashedEmployees->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="50">เลือก</th>
                                <th>รหัสพนักงาน</th>
                                <th>ชื่อ-นามสกุล</th>
                                <th>แผนก</th>
                                <th>ตำแหน่ง</th>
                                <th>อีเมล</th>
                                <th>วันที่ลบ</th>
                                <th width="200">การจัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($trashedEmployees as $employee)
                                <tr class="text-muted">
                                    <td>
                                        <input type="checkbox" name="employee_ids[]" value="{{ $employee->id }}" 
                                               class="form-check-input employee-checkbox">
                                    </td>
                                    <td>
                                        <del>{{ $employee->employee_id }}</del>
                                        @if($employee->is_accounting_department && $employee->express_username)
                                            <br><small class="text-info">
                                                <i class="fas fa-calculator"></i> Express: {{ $employee->express_username }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <del>{{ $employee->full_name }}</del>
                                        @if($employee->english_name)
                                            <br><small><del>{{ $employee->english_name }}</del></small>
                                        @endif
                                    </td>
                                    <td><del>{{ $employee->department }}</del></td>
                                    <td><del>{{ $employee->position }}</del></td>
                                    <td><del>{{ $employee->email }}</del></td>
                                    <td>
                                        <span class="text-danger">
                                            <i class="fas fa-clock"></i>
                                            {{ $employee->deleted_at->format('d/m/Y H:i') }}
                                        </span>
                                        <br><small class="text-muted">
                                            {{ $employee->deleted_at->diffForHumans() }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-info" 
                                                    onclick="previewEmployee({{ $employee->id }})"
                                                    title="ดูตัวอย่าง">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            
                                            <button type="button" class="btn btn-outline-success" 
                                                    onclick="confirmRestore({{ $employee->id }}, '{{ $employee->full_name }}')"
                                                    title="กู้คืน">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                            
                                            <button type="button" class="btn btn-outline-danger" 
                                                    onclick="confirmForceDelete({{ $employee->id }}, '{{ $employee->full_name }}')"
                                                    title="ลบถาวร">
                                                <i class="fas fa-fire"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $trashedEmployees->appends(request()->query())->links() }}
                </div>

                <!-- Bulk Actions -->
                <div class="mt-3 p-3 bg-light rounded">
                    <h6>การดำเนินการแบบกลุ่ม</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <button type="button" class="btn btn-success me-2" onclick="bulkRestore()" disabled id="bulkRestoreBtn">
                                <i class="fas fa-undo"></i> กู้คืนที่เลือก
                            </button>
                            <button type="button" class="btn btn-danger me-2" onclick="bulkForceDelete()" disabled id="bulkDeleteBtn">
                                <i class="fas fa-fire"></i> ลบถาวรที่เลือก
                            </button>
                        </div>
                        <div class="col-md-6 text-end">
                            <span class="text-muted">เลือกแล้ว: <span id="selectedCount">0</span> รายการ</span>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-trash fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">ถังขยะว่างเปล่า</h5>
                    <p class="text-muted">ไม่มีพนักงานที่ถูกลบ</p>
                    <a href="{{ route('employees.index') }}" class="btn btn-primary">
                        <i class="fas fa-users"></i> ไปยังรายการพนักงาน
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Statistics -->
    @if($trashedEmployees->count() > 0)
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-trash fa-2x me-3"></i>
                            <div>
                                <h5 class="mb-0">{{ $trashedEmployees->total() }}</h5>
                                <small>ถูกลบทั้งหมด</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-clock fa-2x me-3"></i>
                            <div>
                                <h5 class="mb-0">{{ $trashedEmployees->where('deleted_at', '>=', now()->subDays(7))->count() }}</h5>
                                <small>ลบใน 7 วันที่แล้ว</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-calculator fa-2x me-3"></i>
                            <div>
                                <h5 class="mb-0">{{ $trashedEmployees->where('express_username', '!=', null)->count() }}</h5>
                                <small>Express Users</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card bg-secondary text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-database fa-2x me-3"></i>
                            <div>
                                <h5 class="mb-0">{{ number_format($trashedEmployees->count() * 1.2, 1) }} MB</h5>
                                <small>ขนาดข้อมูลโดยประมาณ</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Restore Confirmation Modal -->
<div class="modal fade" id="restoreModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">ยืนยันการกู้คืนพนักงาน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>คุณต้องการกู้คืนพนักงาน <strong id="restoreEmployeeName"></strong> หรือไม่?</p>
                <p class="text-success">ข้อมูลจะถูกกู้คืนกลับสู่รายการพนักงานปกติ</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <form id="restoreForm" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-success">กู้คืนพนักงาน</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Force Delete Confirmation Modal -->
<div class="modal fade" id="forceDeleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">⚠️ ยืนยันการลบถาวร</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>คำเตือน!</strong> การดำเนินการนี้ไม่สามารถย้อนกลับได้
                </div>
                <p>คุณต้องการลบพนักงาน <strong id="forceDeleteEmployeeName"></strong> ออกจากระบบถาวรหรือไม่?</p>
                <p class="text-danger">
                    <i class="fas fa-fire"></i> ข้อมูลจะถูกลบออกจากฐานข้อมูลและไม่สามารถกู้คืนได้
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <form id="forceDeleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">ลบถาวร</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Empty Trash Confirmation Modal -->
<div class="modal fade" id="emptyTrashModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">⚠️ ยืนยันการล้างถังขยะ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>คำเตือน!</strong> การดำเนินการนี้จะลบข้อมูลทั้งหมดในถังขยะและไม่สามารถย้อนกลับได้
                </div>
                <p>คุณต้องการลบพนักงานทั้งหมดในถังขยะ <strong>{{ $trashedEmployees->total() }} คน</strong> ออกจากระบบถาวรหรือไม่?</p>
                <ul class="text-muted">
                    <li>ข้อมูลจะถูกลบออกจากฐานข้อมูลทั้งหมด</li>
                    <li>ไม่สามารถกู้คืนข้อมูลได้</li>
                    <li>ประหยัดพื้นที่จัดเก็บข้อมูล</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <form id="emptyTrashForm" method="POST" action="{{ route('employees.empty-trash') }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">ล้างถังขยะ</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Employee Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">ตัวอย่างข้อมูลพนักงาน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="previewContent">
                <!-- Preview content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select All functionality
    const selectAllCheckbox = document.getElementById('selectAll');
    const employeeCheckboxes = document.querySelectorAll('.employee-checkbox');
    const bulkRestoreBtn = document.getElementById('bulkRestoreBtn');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    const selectedCountSpan = document.getElementById('selectedCount');

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            employeeCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkActionButtons();
        });
    }

    employeeCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedBoxes = document.querySelectorAll('.employee-checkbox:checked');
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = checkedBoxes.length === employeeCheckboxes.length;
                selectAllCheckbox.indeterminate = checkedBoxes.length > 0 && checkedBoxes.length < employeeCheckboxes.length;
            }
            updateBulkActionButtons();
        });
    });

    function updateBulkActionButtons() {
        const checkedBoxes = document.querySelectorAll('.employee-checkbox:checked');
        const count = checkedBoxes.length;
        
        if (bulkRestoreBtn) bulkRestoreBtn.disabled = count === 0;
        if (bulkDeleteBtn) bulkDeleteBtn.disabled = count === 0;
        if (selectedCountSpan) selectedCountSpan.textContent = count;
    }
});

// Individual restore
function confirmRestore(employeeId, employeeName) {
    document.getElementById('restoreEmployeeName').textContent = employeeName;
    document.getElementById('restoreForm').action = `/employees/${employeeId}/restore`;
    
    const modal = new bootstrap.Modal(document.getElementById('restoreModal'));
    modal.show();
}

// Individual force delete
function confirmForceDelete(employeeId, employeeName) {
    document.getElementById('forceDeleteEmployeeName').textContent = employeeName;
    document.getElementById('forceDeleteForm').action = `/employees/${employeeId}/force-delete`;
    
    const modal = new bootstrap.Modal(document.getElementById('forceDeleteModal'));
    modal.show();
}

// Empty trash
function confirmEmptyTrash() {
    const modal = new bootstrap.Modal(document.getElementById('emptyTrashModal'));
    modal.show();
}

// Bulk restore
function bulkRestore() {
    const checkedBoxes = document.querySelectorAll('.employee-checkbox:checked');
    
    if (checkedBoxes.length === 0) {
        alert('กรุณาเลือกพนักงานที่ต้องการกู้คืน');
        return;
    }

    if (confirm(`คุณต้องการกู้คืนพนักงาน ${checkedBoxes.length} คน หรือไม่?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/employees/bulk-restore';
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(csrfToken);
        
        // Add employee IDs
        checkedBoxes.forEach(checkbox => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'employee_ids[]';
            input.value = checkbox.value;
            form.appendChild(input);
        });
        
        document.body.appendChild(form);
        form.submit();
    }
}

// Bulk force delete
function bulkForceDelete() {
    const checkedBoxes = document.querySelectorAll('.employee-checkbox:checked');
    
    if (checkedBoxes.length === 0) {
        alert('กรุณาเลือกพนักงานที่ต้องการลบถาวร');
        return;
    }

    if (confirm(`⚠️ คุณต้องการลบพนักงาน ${checkedBoxes.length} คน ออกจากระบบถาวรหรือไม่?\n\nการดำเนินการนี้ไม่สามารถย้อนกลับได้!`)) {
        // Implementation for bulk force delete
        alert('ฟังก์ชันนี้จะพัฒนาในเวอร์ชันถัดไป');
    }
}

// Preview employee
function previewEmployee(employeeId) {
    // Implementation for preview
    document.getElementById('previewContent').innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> กำลังโหลด...</div>';
    
    const modal = new bootstrap.Modal(document.getElementById('previewModal'));
    modal.show();
    
    // Mock preview data - replace with actual API call
    setTimeout(() => {
        document.getElementById('previewContent').innerHTML = `
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> 
                ฟีเจอร์นี้จะพัฒนาในเวอร์ชันถัดไป
            </div>
            <p>แสดงข้อมูลพนักงาน ID: ${employeeId}</p>
        `;
    }, 1000);
}

// Auto-refresh notifications
@if(session('success'))
    setTimeout(() => {
        const alert = document.querySelector('.alert-success');
        if (alert) {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }
    }, 5000);
@endif
</script>

<style>
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

.table td {
    vertical-align: middle;
}

.table del {
    text-decoration: line-through;
    opacity: 0.7;
}

.employee-checkbox, #selectAll {
    transform: scale(1.2);
}

.text-danger {
    color: #dc3545 !important;
}

.bg-light {
    background-color: #f8f9fa !important;
}
</style>
@endsection