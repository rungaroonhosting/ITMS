@extends('layouts.app')

@section('title', 'รายละเอียดพนักงาน - ' . $employee->full_name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <!-- Employee Information Card -->
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-user"></i> ข้อมูลพนักงาน
                        </h5>
                        <span class="badge bg-{{ $employee->status_badge }} fs-6">
                            {{ $employee->status_thai }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-info-circle"></i> ข้อมูลพื้นฐาน
                            </h6>
                            
                            <dl class="row">
                                <dt class="col-sm-5">รหัสพนักงาน:</dt>
                                <dd class="col-sm-7">
                                    <strong class="text-primary">{{ $employee->employee_id }}</strong>
                                </dd>
                                
                                <dt class="col-sm-5">ชื่อ-นามสกุล:</dt>
                                <dd class="col-sm-7">{{ $employee->full_name }}</dd>
                                
                                @if($employee->english_name)
                                    <dt class="col-sm-5">ชื่อภาษาอังกฤษ:</dt>
                                    <dd class="col-sm-7">{{ $employee->english_name }}</dd>
                                @endif
                                
                                <dt class="col-sm-5">อีเมล:</dt>
                                <dd class="col-sm-7">
                                    <a href="mailto:{{ $employee->email }}">{{ $employee->email }}</a>
                                </dd>
                                
                                @if($employee->phone)
                                    <dt class="col-sm-5">เบอร์โทร:</dt>
                                    <dd class="col-sm-7">
                                        <a href="tel:{{ $employee->phone }}">{{ $employee->phone }}</a>
                                    </dd>
                                @endif
                            </dl>
                        </div>
                        
                        <!-- Work Information -->
                        <div class="col-md-6">
                            <h6 class="text-success mb-3">
                                <i class="fas fa-briefcase"></i> ข้อมูลการทำงาน
                            </h6>
                            
                            <dl class="row">
                                <dt class="col-sm-5">แผนก:</dt>
                                <dd class="col-sm-7">
                                    {{ $employee->department }}
                                    @if($employee->is_accounting_department)
                                        <span class="badge bg-info ms-2">
                                            <i class="fas fa-calculator"></i> Express
                                        </span>
                                    @endif
                                </dd>
                                
                                <dt class="col-sm-5">ตำแหน่ง:</dt>
                                <dd class="col-sm-7">{{ $employee->position }}</dd>
                                
                                <dt class="col-sm-5">วันที่เริ่มงาน:</dt>
                                <dd class="col-sm-7">
                                    {{ $employee->hire_date->format('d/m/Y') }}
                                    <small class="text-muted">
                                        ({{ $employee->years_of_service }} ปี)
                                    </small>
                                </dd>
                                
                                @if($employee->salary)
                                    <dt class="col-sm-5">เงินเดือน:</dt>
                                    <dd class="col-sm-7">{{ $employee->formatted_salary }}</dd>
                                @endif
                                
                                <dt class="col-sm-5">สถานะ:</dt>
                                <dd class="col-sm-7">
                                    <span class="badge bg-{{ $employee->status_badge }}">
                                        {{ $employee->status_thai }}
                                    </span>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Security Information Card -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-lock"></i> ข้อมูลความปลอดภัย
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-5">รหัสผ่าน:</dt>
                                <dd class="col-sm-7">
                                    @if($canViewPassword)
                                        <span class="text-info">
                                            <i class="fas fa-shield-alt"></i> {{ $employee->display_password }}
                                        </span>
                                        <br><small class="text-muted">เข้ารหัสด้วย Hash Algorithm</small>
                                    @else
                                        <span class="text-muted">
                                            <i class="fas fa-eye-slash"></i> {{ $employee->display_password }}
                                        </span>
                                    @endif
                                </dd>
                            </dl>
                        </div>
                        
                        @if($employee->is_accounting_department)
                            <div class="col-md-6">
                                <dl class="row">
                                    @if($employee->express_username)
                                        <dt class="col-sm-6">Express Username:</dt>
                                        <dd class="col-sm-6">
                                            <code>{{ $employee->express_username }}</code>
                                        </dd>
                                    @endif
                                    
                                    @if($employee->express_password)
                                        <dt class="col-sm-6">Express Password:</dt>
                                        <dd class="col-sm-6">
                                            @if($canViewPassword || (auth()->user()->email === $employee->email))
                                                <code>{{ $employee->express_password }}</code>
                                            @else
                                                <span class="text-muted">
                                                    <i class="fas fa-eye-slash"></i> [ซ่อน]
                                                </span>
                                            @endif
                                        </dd>
                                    @endif
                                </dl>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- System Information Card -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-database"></i> ข้อมูลระบบ
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-6">วันที่สร้าง:</dt>
                                <dd class="col-sm-6">{{ $employee->created_at->format('d/m/Y H:i') }}</dd>
                                
                                <dt class="col-sm-6">อัปเดตล่าสุด:</dt>
                                <dd class="col-sm-6">{{ $employee->updated_at->format('d/m/Y H:i') }}</dd>
                            </dl>
                        </div>
                        
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-6">ID ในระบบ:</dt>
                                <dd class="col-sm-6"><code>#{{ $employee->id }}</code></dd>
                                
                                <dt class="col-sm-6">การเข้าถึง:</dt>
                                <dd class="col-sm-6">
                                    @if($employee->canBeManaged())
                                        <span class="badge bg-success">สามารถจัดการได้</span>
                                    @else
                                        <span class="badge bg-secondary">ดูได้อย่างเดียว</span>
                                    @endif
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-4">
                <a href="{{ route('employees.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> กลับ
                </a>
                
                @if($employee->canBeManaged())
                    <a href="{{ route('employees.edit', $employee) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> แก้ไข
                    </a>
                    
                    @if(auth()->user()->role === 'super_admin')
                        <button type="button" class="btn btn-info" onclick="resetPassword({{ $employee->id }})">
                            <i class="fas fa-key"></i> รีเซ็ตรหัสผ่าน
                        </button>
                    @endif
                    
                    <button type="button" class="btn btn-danger" 
                            onclick="confirmDelete({{ $employee->id }}, '{{ $employee->full_name }}')">
                        <i class="fas fa-trash"></i> ลบ
                    </button>
                @endif
            </div>
        </div>

        <!-- Side Panel -->
        <div class="col-md-4">
            <!-- Quick Actions Card -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-lightning-bolt"></i> การดำเนินการด่วน
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="mailto:{{ $employee->email }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-envelope"></i> ส่งอีเมล
                        </a>
                        
                        @if($employee->phone)
                            <a href="tel:{{ $employee->phone }}" class="btn btn-outline-success btn-sm">
                                <i class="fas fa-phone"></i> โทร
                            </a>
                        @endif
                        
                        <button type="button" class="btn btn-outline-info btn-sm" onclick="exportEmployee()">
                            <i class="fas fa-download"></i> ส่งออกข้อมูล
                        </button>
                        
                        @if($employee->is_accounting_department && $employee->express_username)
                            <button type="button" class="btn btn-outline-warning btn-sm" onclick="copyExpressCredentials()">
                                <i class="fas fa-copy"></i> คัดลอก Express
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Employee Statistics -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-bar"></i> สถิติ
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h4 class="text-primary">{{ $employee->years_of_service }}</h4>
                                <small class="text-muted">ปีที่ทำงาน</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="text-success">{{ $employee->status === 'active' ? '✓' : '✗' }}</h4>
                            <small class="text-muted">สถานะปัจจุบัน</small>
                        </div>
                    </div>
                    
                    @if($employee->is_accounting_department)
                        <hr>
                        <div class="text-center">
                            <span class="badge bg-info">
                                <i class="fas fa-calculator"></i> ระบบ Express
                            </span>
                            <p class="small text-muted mt-2">
                                พนักงานแผนกบัญชีมีสิทธิ์ใช้งานระบบ Express
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Department Info -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-building"></i> ข้อมูลแผนก
                    </h6>
                </div>
                <div class="card-body">
                    <h6>{{ $employee->department }}</h6>
                    <p class="text-muted mb-2">ตำแหน่ง: {{ $employee->position }}</p>
                    
                    <div class="small">
                        <a href="{{ route('employees.search', ['q' => $employee->department]) }}" 
                           class="text-decoration-none">
                            <i class="fas fa-users"></i> ดูพนักงานแผนกเดียวกัน
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">ยืนยันการลบพนักงาน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>คุณต้องการลบพนักงาน <strong id="deleteEmployeeName"></strong> หรือไม่?</p>
                <p class="text-muted">ข้อมูลจะถูกย้ายไปยังถังขยะและสามารถกู้คืนได้ภายหลัง</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">ลบพนักงาน</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Reset Password Modal -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">รีเซ็ตรหัสผ่าน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>คุณต้องการรีเซ็ตรหัสผ่านสำหรับ <strong>{{ $employee->full_name }}</strong> หรือไม่?</p>
                <div class="form-group">
                    <label for="newPassword">รหัสผ่านใหม่:</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="newPassword" minlength="6">
                        <button type="button" class="btn btn-outline-secondary" onclick="generateNewPassword()">
                            <i class="fas fa-dice"></i> สุ่ม
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-warning" onclick="executePasswordReset()">
                    <i class="fas fa-key"></i> รีเซ็ตรหัสผ่าน
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Delete confirmation
function confirmDelete(employeeId, employeeName) {
    document.getElementById('deleteEmployeeName').textContent = employeeName;
    document.getElementById('deleteForm').action = `/employees/${employeeId}`;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

// Reset password
function resetPassword(employeeId) {
    const modal = new bootstrap.Modal(document.getElementById('resetPasswordModal'));
    modal.show();
}

function generateNewPassword() {
    const chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    let password = '';
    for (let i = 0; i < 8; i++) {
        password += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    document.getElementById('newPassword').value = password;
}

function executePasswordReset() {
    const newPassword = document.getElementById('newPassword').value;
    if (newPassword.length < 6) {
        alert('รหัสผ่านต้องมีความยาวอย่างน้อย 6 ตัวอักษร');
        return;
    }
    
    // Implement password reset API call here
    alert('ฟังก์ชันนี้จะพัฒนาในเวอร์ชันถัดไป');
}

// Export employee data
function exportEmployee() {
    window.open(`/employees/{{ $employee->id }}/export`, '_blank');
}

// Copy Express credentials
function copyExpressCredentials() {
    @if($employee->express_username && ($canViewPassword || auth()->user()->email === $employee->email))
        const credentials = `Username: {{ $employee->express_username }}\nPassword: {{ $employee->express_password }}`;
        navigator.clipboard.writeText(credentials).then(() => {
            alert('คัดลอกข้อมูล Express เรียบร้อยแล้ว');
        });
    @else
        alert('ไม่มีสิทธิ์ดูข้อมูล Express');
    @endif
}

// Tooltip initialization
document.addEventListener('DOMContentLoaded', function() {
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips.forEach(tooltip => {
        new bootstrap.Tooltip(tooltip);
    });
});
</script>

<style>
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.card-header {
    background-color: rgba(0, 0, 0, 0.03);
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
}

dl.row dt {
    font-weight: 600;
    color: #495057;
}

dl.row dd {
    margin-bottom: 0.5rem;
}

code {
    color: #e83e8c;
    background-color: #f8f9fa;
    padding: 0.2rem 0.4rem;
    border-radius: 0.25rem;
}

.badge {
    font-size: 0.75rem;
}

.btn-sm {
    font-size: 0.8rem;
}

.border-end {
    border-right: 1px solid #dee2e6 !important;
}

.text-decoration-none:hover {
    text-decoration: underline !important;
}
</style>
@endsection