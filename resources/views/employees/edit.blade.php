@extends('layouts.app')

@section('title', 'แก้ไขข้อมูลพนักงาน - ' . $employee->full_name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-user-edit"></i> แก้ไขข้อมูลพนักงาน
                        <span class="badge bg-{{ $employee->status_badge }} ms-2">{{ $employee->status_thai }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    <form id="employeeForm" method="POST" action="{{ route('employees.update', $employee) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- รหัสพนักงาน -->
                            <div class="col-md-6 mb-3">
                                <label for="employee_id" class="form-label">รหัสพนักงาน <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('employee_id') is-invalid @enderror" 
                                       id="employee_id" name="employee_id" value="{{ old('employee_id', $employee->employee_id) }}" required>
                                @error('employee_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- อีเมล -->
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">อีเมล <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email', $employee->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- ชื่อ -->
                            <div class="col-md-6 mb-3">
                                <label for="first_name" class="form-label">ชื่อ <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                       id="first_name" name="first_name" value="{{ old('first_name', $employee->first_name) }}" required>
                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- นามสกุล -->
                            <div class="col-md-6 mb-3">
                                <label for="last_name" class="form-label">นามสกุล <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                       id="last_name" name="last_name" value="{{ old('last_name', $employee->last_name) }}" required>
                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- ชื่อภาษาอังกฤษ -->
                            <div class="col-md-6 mb-3">
                                <label for="english_name" class="form-label">ชื่อภาษาอังกฤษ</label>
                                <input type="text" class="form-control @error('english_name') is-invalid @enderror" 
                                       id="english_name" name="english_name" value="{{ old('english_name', $employee->english_name) }}"
                                       placeholder="John Smith">
                                @error('english_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- เบอร์โทร -->
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">เบอร์โทร</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone', $employee->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- แผนก -->
                            <div class="col-md-6 mb-3">
                                <label for="department" class="form-label">แผนก <span class="text-danger">*</span></label>
                                <select class="form-control @error('department') is-invalid @enderror" 
                                        id="department" name="department" required>
                                    <option value="">เลือกแผนก</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept }}" {{ old('department', $employee->department) == $dept ? 'selected' : '' }}>
                                            {{ $dept }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('department')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- ตำแหน่ง -->
                            <div class="col-md-6 mb-3">
                                <label for="position" class="form-label">ตำแหน่ง <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('position') is-invalid @enderror" 
                                       id="position" name="position" value="{{ old('position', $employee->position) }}" required>
                                @error('position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- วันที่เริ่มงาน -->
                            <div class="col-md-6 mb-3">
                                <label for="hire_date" class="form-label">วันที่เริ่มงาน <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('hire_date') is-invalid @enderror" 
                                       id="hire_date" name="hire_date" value="{{ old('hire_date', $employee->hire_date->format('Y-m-d')) }}" required>
                                @error('hire_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- เงินเดือน -->
                            <div class="col-md-6 mb-3">
                                <label for="salary" class="form-label">เงินเดือน</label>
                                <input type="number" class="form-control @error('salary') is-invalid @enderror" 
                                       id="salary" name="salary" value="{{ old('salary', $employee->salary) }}" min="0" step="0.01">
                                @error('salary')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- รหัสผ่าน -->
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">
                                    รหัสผ่านใหม่ 
                                    <small class="text-muted">(เว้นว่างไว้หากไม่ต้องการเปลี่ยน)</small>
                                </label>
                                <div class="input-group">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                           id="password" name="password" minlength="6" placeholder="กรอกรหัสผ่านใหม่">
                                    <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-info" id="generatePassword">
                                        <i class="fas fa-key"></i> สุ่ม
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- สถานะ -->
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">สถานะ <span class="text-danger">*</span></label>
                                <select class="form-control @error('status') is-invalid @enderror" 
                                        id="status" name="status" required>
                                    <option value="active" {{ old('status', $employee->status) == 'active' ? 'selected' : '' }}>ใช้งาน</option>
                                    <option value="inactive" {{ old('status', $employee->status) == 'inactive' ? 'selected' : '' }}>ไม่ใช้งาน</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Express Section (แสดงเฉพาะแผนกบัญชี) -->
                        <div id="expressSection" class="card mt-3" style="{{ $employee->is_accounting_department ? 'display: block;' : 'display: none;' }}">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-calculator"></i> ระบบ Express (แผนกบัญชี)
                                    @if($employee->express_username)
                                        <span class="badge bg-success ms-2">มีข้อมูล</span>
                                    @else
                                        <span class="badge bg-warning ms-2">ยังไม่มีข้อมูล</span>
                                    @endif
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="express_username" class="form-label">Express Username (7 ตัวอักษร)</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="express_username" 
                                                   name="express_username" maxlength="7" 
                                                   value="{{ old('express_username', $employee->express_username) }}">
                                            <button type="button" class="btn btn-outline-primary" id="generateExpressUsername">
                                                <i class="fas fa-sync"></i> สร้างใหม่
                                            </button>
                                        </div>
                                        <small class="text-muted">จะสร้างอัตโนมัติจากชื่อภาษาอังกฤษ</small>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="express_password" class="form-label">Express Password (4 ตัวอักษร)</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="express_password" 
                                                   name="express_password" maxlength="10"
                                                   value="{{ old('express_password', $employee->express_password) }}">
                                            <button type="button" class="btn btn-outline-success" id="generateExpressPassword">
                                                <i class="fas fa-sync"></i> สร้างใหม่
                                            </button>
                                        </div>
                                        <small class="text-muted">รหัส 4 ตัวอักษรผสมตัวเลข</small>
                                    </div>
                                </div>
                                
                                @if($employee->express_username)
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i>
                                        <strong>ข้อมูลปัจจุบัน:</strong> Username: <code>{{ $employee->express_username }}</code>
                                        @if($employee->express_password), Password: <code>{{ $employee->express_password }}</code>@endif
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('employees.show', $employee) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> กลับ
                            </a>
                            
                            <div>
                                <button type="button" class="btn btn-info me-2" id="previewBtn">
                                    <i class="fas fa-eye"></i> ดูตัวอย่าง
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> อัปเดต
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Preview Panel -->
        <div class="col-md-4">
            <div class="card" id="previewCard" style="display: none;">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-eye"></i> ตัวอย่างข้อมูลที่จะอัปเดต
                    </h6>
                </div>
                <div class="card-body" id="previewContent">
                    <!-- Preview content will be inserted here -->
                </div>
            </div>

            <!-- Current Information Card -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle"></i> ข้อมูลปัจจุบัน
                    </h6>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-5">รหัสพนักงาน:</dt>
                        <dd class="col-7">{{ $employee->employee_id }}</dd>
                        
                        <dt class="col-5">ชื่อ-นามสกุล:</dt>
                        <dd class="col-7">{{ $employee->full_name }}</dd>
                        
                        <dt class="col-5">แผนก:</dt>
                        <dd class="col-7">{{ $employee->department }}</dd>
                        
                        <dt class="col-5">ตำแหน่ง:</dt>
                        <dd class="col-7">{{ $employee->position }}</dd>
                        
                        <dt class="col-5">สถานะ:</dt>
                        <dd class="col-7">
                            <span class="badge bg-{{ $employee->status_badge }}">
                                {{ $employee->status_thai }}
                            </span>
                        </dd>
                        
                        <dt class="col-5">วันที่เริ่มงาน:</dt>
                        <dd class="col-7">{{ $employee->hire_date->format('d/m/Y') }}</dd>
                        
                        @if($employee->express_username)
                            <dt class="col-5">Express:</dt>
                            <dd class="col-7">
                                <span class="badge bg-info">
                                    <i class="fas fa-calculator"></i> {{ $employee->express_username }}
                                </span>
                            </dd>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-lightning-bolt"></i> การดำเนินการด่วน
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('employees.show', $employee) }}" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-eye"></i> ดูรายละเอียดเต็ม
                        </a>
                        
                        @if(auth()->user()->role === 'super_admin')
                            <button type="button" class="btn btn-outline-warning btn-sm" onclick="resetPassword()">
                                <i class="fas fa-key"></i> รีเซ็ตรหัสผ่าน
                            </button>
                        @endif
                        
                        <button type="button" class="btn btn-outline-danger btn-sm" 
                                onclick="confirmDelete('{{ $employee->full_name }}')">
                            <i class="fas fa-trash"></i> ลบพนักงาน
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const departmentSelect = document.getElementById('department');
    const expressSection = document.getElementById('expressSection');
    const englishNameInput = document.getElementById('english_name');
    const expressUsernameInput = document.getElementById('express_username');
    const expressPasswordInput = document.getElementById('express_password');
    const passwordInput = document.getElementById('password');
    const previewBtn = document.getElementById('previewBtn');
    const previewCard = document.getElementById('previewCard');
    const previewContent = document.getElementById('previewContent');

    // Accounting departments
    const accountingDepartments = [
        'แผนกบัญชี',
        'แผนกบัญชีและการเงิน',
        'แผนกการเงิน'
    ];

    // Show/Hide Express Section based on department
    departmentSelect.addEventListener('change', function() {
        if (accountingDepartments.includes(this.value)) {
            expressSection.style.display = 'block';
            // Auto-generate if fields are empty
            if (!expressUsernameInput.value && englishNameInput.value) {
                autoGenerateExpressCredentials();
            }
        } else {
            expressSection.style.display = 'none';
        }
        updatePreview();
    });

    // Auto-generate Express credentials when English name changes
    englishNameInput.addEventListener('input', function() {
        if (accountingDepartments.includes(departmentSelect.value)) {
            // Only auto-generate if fields are empty
            if (!expressUsernameInput.value) {
                autoGenerateExpressCredentials();
            }
        }
        updatePreview();
    });

    // Auto-generate Express credentials
    function autoGenerateExpressCredentials() {
        const englishName = englishNameInput.value.trim();
        if (englishName) {
            generateExpressUsername(englishName);
            if (!expressPasswordInput.value) {
                generateExpressPassword();
            }
        }
    }

    // Generate Express Username
    function generateExpressUsername(name) {
        fetch(`/api/generate/express-username?name=${encodeURIComponent(name)}`)
            .then(response => response.json())
            .then(data => {
                expressUsernameInput.value = data.express_username;
                updatePreview();
            })
            .catch(error => console.error('Error:', error));
    }

    // Generate Express Password
    function generateExpressPassword() {
        fetch('/api/generate/express-password')
            .then(response => response.json())
            .then(data => {
                expressPasswordInput.value = data.express_password;
                updatePreview();
            })
            .catch(error => console.error('Error:', error));
    }

    // Manual generation buttons
    document.getElementById('generateExpressUsername').addEventListener('click', function() {
        const englishName = englishNameInput.value.trim();
        if (englishName) {
            generateExpressUsername(englishName);
        } else {
            alert('กรุณากรอกชื่อภาษาอังกฤษก่อน');
        }
    });

    document.getElementById('generateExpressPassword').addEventListener('click', function() {
        generateExpressPassword();
    });

    // Password toggle
    document.getElementById('togglePassword').addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
    });

    // Generate random password
    document.getElementById('generatePassword').addEventListener('click', function() {
        const chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        let password = '';
        for (let i = 0; i < 8; i++) {
            password += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        passwordInput.value = password;
        updatePreview();
    });

    // Preview functionality
    previewBtn.addEventListener('click', function() {
        updatePreview();
        previewCard.style.display = previewCard.style.display === 'none' ? 'block' : 'none';
    });

    // Update preview content
    function updatePreview() {
        const formData = {
            employee_id: document.getElementById('employee_id').value,
            first_name: document.getElementById('first_name').value,
            last_name: document.getElementById('last_name').value,
            english_name: document.getElementById('english_name').value,
            email: document.getElementById('email').value,
            phone: document.getElementById('phone').value,
            department: document.getElementById('department').value,
            position: document.getElementById('position').value,
            hire_date: document.getElementById('hire_date').value,
            salary: document.getElementById('salary').value,
            status: document.getElementById('status').value,
            express_username: document.getElementById('express_username').value,
            express_password: document.getElementById('express_password').value,
            password_changed: document.getElementById('password').value ? 'ใช่' : 'ไม่'
        };

        let html = '<div class="preview-data">';
        html += '<h6 class="text-primary mb-3">การเปลี่ยนแปลง</h6>';
        
        if (formData.employee_id) html += `<p><strong>รหัสพนักงาน:</strong> ${formData.employee_id}</p>`;
        if (formData.first_name || formData.last_name) {
            html += `<p><strong>ชื่อ-นามสกุล:</strong> ${formData.first_name} ${formData.last_name}</p>`;
        }
        if (formData.english_name) html += `<p><strong>ชื่อภาษาอังกฤษ:</strong> ${formData.english_name}</p>`;
        if (formData.email) html += `<p><strong>อีเมล:</strong> ${formData.email}</p>`;
        if (formData.phone) html += `<p><strong>เบอร์โทร:</strong> ${formData.phone}</p>`;
        if (formData.department) html += `<p><strong>แผนก:</strong> ${formData.department}</p>`;
        if (formData.position) html += `<p><strong>ตำแหน่ง:</strong> ${formData.position}</p>`;
        if (formData.hire_date) html += `<p><strong>วันที่เริ่มงาน:</strong> ${formData.hire_date}</p>`;
        if (formData.salary) html += `<p><strong>เงินเดือน:</strong> ${Number(formData.salary).toLocaleString()} บาท</p>`;
        if (formData.status) {
            const statusText = formData.status === 'active' ? 'ใช้งาน' : 'ไม่ใช้งาน';
            const statusClass = formData.status === 'active' ? 'text-success' : 'text-secondary';
            html += `<p><strong>สถานะ:</strong> <span class="${statusClass}">${statusText}</span></p>`;
        }
        
        html += `<p><strong>เปลี่ยนรหัสผ่าน:</strong> <span class="${formData.password_changed === 'ใช่' ? 'text-warning' : 'text-muted'}">${formData.password_changed}</span></p>`;
        
        if (formData.express_username || formData.express_password) {
            html += '<hr><h6 class="text-info">ข้อมูล Express</h6>';
            if (formData.express_username) html += `<p><strong>Express Username:</strong> ${formData.express_username}</p>`;
            if (formData.express_password) html += `<p><strong>Express Password:</strong> ${formData.express_password}</p>`;
        }
        
        html += '</div>';
        
        previewContent.innerHTML = html;
    }

    // Auto-update preview when form changes
    document.getElementById('employeeForm').addEventListener('input', updatePreview);
    document.getElementById('employeeForm').addEventListener('change', updatePreview);
});

// Additional functions
function resetPassword() {
    if (confirm('คุณต้องการรีเซ็ตรหัสผ่านสำหรับพนักงานคนนี้หรือไม่?')) {
        alert('ฟังก์ชันนี้จะพัฒนาในเวอร์ชันถัดไป');
    }
}

function confirmDelete(employeeName) {
    if (confirm(`คุณต้องการลบพนักงาน "${employeeName}" หรือไม่?\n\nข้อมูลจะถูกย้ายไปยังถังขยะ`)) {
        // Create and submit delete form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("employees.destroy", $employee) }}';
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        // Add method override
        const methodOverride = document.createElement('input');
        methodOverride.type = 'hidden';
        methodOverride.name = '_method';
        methodOverride.value = 'DELETE';
        form.appendChild(methodOverride);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<style>
.preview-data p {
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

.preview-data hr {
    margin: 1rem 0 0.5rem 0;
}

#expressSection {
    border-left: 4px solid #17a2b8;
}

.form-label .text-danger {
    font-size: 0.8rem;
}

dl.row dt {
    font-size: 0.85rem;
    color: #495057;
}

dl.row dd {
    font-size: 0.85rem;
    margin-bottom: 0.25rem;
}

.btn-sm {
    font-size: 0.8rem;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}
</style>
@endsection