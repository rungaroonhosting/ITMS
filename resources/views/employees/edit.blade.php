@extends('layouts.app')

@section('title', 'แก้ไขข้อมูลพนักงาน')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/employees.css') }}">
@endsection

@section('content')
<div class="container-fluid px-4">
    {{-- Header Section --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-primary-red fw-bold">
                <i class="fas fa-user-edit me-2"></i>แก้ไขข้อมูลพนักงาน
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}" class="text-decoration-none">หน้าแรก</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('employees.index') }}" class="text-decoration-none">จัดการพนักงาน</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('employees.show', $employee) }}" class="text-decoration-none">{{ $employee->full_name }}</a>
                    </li>
                    <li class="breadcrumb-item active">แก้ไข</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('employees.show', $employee) }}" class="btn btn-outline-info">
                <i class="fas fa-eye me-1"></i>ดูรายละเอียด
            </a>
            <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>กลับ
            </a>
        </div>
    </div>

    {{-- Employee Info Banner --}}
    <div class="card border-0 shadow-sm mb-4 bg-gradient-info text-white">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <div class="avatar-lg bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center me-3">
                    <i class="fas fa-user fa-2x text-white"></i>
                </div>
                <div>
                    <h4 class="mb-1">{{ $employee->full_name }}</h4>
                    <div class="d-flex gap-3">
                        <span><i class="fas fa-id-badge me-1"></i>{{ $employee->employee_id }}</span>
                        <span><i class="fas fa-envelope me-1"></i>{{ $employee->email }}</span>
                        <span><i class="fas fa-building me-1"></i>{{ $employee->department_text }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Form Section --}}
    <div class="row justify-content-center">
        <div class="col-xl-10">
            <form id="employeeForm" action="{{ route('employees.update', $employee) }}" method="POST" novalidate>
                @csrf
                @method('PUT')
                
                {{-- Basic Information Card --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-gradient-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user me-2"></i>ข้อมูลพื้นฐาน
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="prefix" class="form-label required">คำนำหน้า</label>
                                <select class="form-select @error('prefix') is-invalid @enderror" 
                                        id="prefix" name="prefix" required>
                                    <option value="">เลือกคำนำหน้า</option>
                                    @foreach($prefixes as $key => $value)
                                        <option value="{{ $key }}" {{ old('prefix', $employee->prefix) == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('prefix')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="first_name" class="form-label required">ชื่อ</label>
                                <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                       id="first_name" name="first_name" value="{{ old('first_name', $employee->first_name) }}" 
                                       placeholder="กรอกชื่อ" required>
                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-5">
                                <label for="last_name" class="form-label required">นามสกุล</label>
                                <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                       id="last_name" name="last_name" value="{{ old('last_name', $employee->last_name) }}" 
                                       placeholder="กรอกนามสกุล" required>
                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- System Information Card --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-gradient-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-key me-2"></i>ข้อมูลระบบ
                            </h5>
                            <button type="button" class="btn btn-light btn-sm" id="generatePasswordBtn">
                                <i class="fas fa-key me-1"></i>สร้างรหัสผ่านใหม่
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="employee_id" class="form-label">รหัสพนักงาน</label>
                                <input type="text" class="form-control @error('employee_id') is-invalid @enderror" 
                                       id="employee_id" name="employee_id" value="{{ old('employee_id', $employee->employee_id) }}" 
                                       placeholder="รหัสพนักงาน">
                                @error('employee_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">รหัสพนักงานในระบบ</div>
                            </div>
                            <div class="col-md-6">
                                <label for="username" class="form-label">ชื่อผู้ใช้</label>
                                <input type="text" class="form-control @error('username') is-invalid @enderror" 
                                       id="username" name="username" value="{{ old('username', $employee->username) }}" 
                                       placeholder="ชื่อผู้ใช้ในระบบ">
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">ชื่อสำหรับเข้าสู่ระบบ</div>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">อีเมล</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email', $employee->email) }}" 
                                       placeholder="อีเมลของพนักงาน">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">อีเมลสำหรับติดต่อ</div>
                            </div>
                            <div class="col-md-6">
                                <label for="password" class="form-label">รหัสผ่านใหม่</label>
                                <div class="input-group">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                           id="password" name="password" value="{{ old('password') }}" 
                                           placeholder="เว้นว่างหากไม่ต้องการเปลี่ยน">
                                    <button type="button" class="btn btn-outline-info" id="togglePasswordBtn">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-text">เว้นว่างไว้หากไม่ต้องการเปลี่ยนรหัสผ่าน</div>
                            </div>
                        </div>
                        
                        @if(auth()->user()->role == 'super_admin')
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="alert alert-info d-flex align-items-center">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <div>
                                        <strong>รหัสผ่านปัจจุบัน:</strong> 
                                        <span class="password-display">••••••••</span>
                                        <button type="button" class="btn btn-sm btn-outline-secondary ms-2" id="showCurrentPassword">
                                            <i class="fas fa-eye"></i> แสดง
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Work Information Card --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-gradient-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-briefcase me-2"></i>ข้อมูลการทำงาน
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="department" class="form-label required">แผนก</label>
                                <select class="form-select @error('department') is-invalid @enderror" 
                                        id="department" name="department" required>
                                    <option value="">เลือกแผนก</option>
                                    @foreach($departments as $key => $value)
                                        <option value="{{ $key }}" {{ old('department', $employee->department) == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('department')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="position" class="form-label required">ตำแหน่ง</label>
                                <input type="text" class="form-control @error('position') is-invalid @enderror" 
                                       id="position" name="position" value="{{ old('position', $employee->position) }}" 
                                       placeholder="เช่น นักพัฒนาระบบ" required>
                                @error('position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="hire_date" class="form-label required">วันที่เริ่มงาน</label>
                                <input type="date" class="form-control @error('hire_date') is-invalid @enderror" 
                                       id="hire_date" name="hire_date" value="{{ old('hire_date', $employee->hire_date->format('Y-m-d')) }}" 
                                       max="{{ date('Y-m-d') }}" required>
                                @error('hire_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="salary" class="form-label">เงินเดือน</label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('salary') is-invalid @enderror" 
                                           id="salary" name="salary" value="{{ old('salary', $employee->salary) }}" 
                                           placeholder="0.00" min="0" step="0.01">
                                    <span class="input-group-text">บาท</span>
                                    @error('salary')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="phone" class="form-label">เบอร์โทรศัพท์</label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone', $employee->phone) }}" 
                                       placeholder="08X-XXX-XXXX">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Status & Role Card --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-gradient-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user-cog me-2"></i>สถานะและบทบาท
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="status" class="form-label required">สถานะ</label>
                                <select class="form-select @error('status') is-invalid @enderror" 
                                        id="status" name="status" required>
                                    @foreach($statuses as $key => $value)
                                        <option value="{{ $key }}" {{ old('status', $employee->status) == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="role" class="form-label required">บทบาท</label>
                                <select class="form-select @error('role') is-invalid @enderror" 
                                        id="role" name="role" required>
                                    @foreach($roles as $key => $value)
                                        <option value="{{ $key }}" {{ old('role', $employee->role) == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Additional Information Card --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-gradient-secondary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle me-2"></i>ข้อมูลเพิ่มเติม
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="address" class="form-label">ที่อยู่</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" 
                                          id="address" name="address" rows="3" 
                                          placeholder="ที่อยู่ปัจจุบัน">{{ old('address', $employee->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="emergency_contact" class="form-label">ผู้ติดต่อฉุกเฉิน</label>
                                <input type="text" class="form-control @error('emergency_contact') is-invalid @enderror" 
                                       id="emergency_contact" name="emergency_contact" value="{{ old('emergency_contact', $employee->emergency_contact) }}" 
                                       placeholder="ชื่อผู้ติดต่อฉุกเฉิน">
                                @error('emergency_contact')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="emergency_phone" class="form-label">เบอร์ติดต่อฉุกเฉิน</label>
                                <input type="tel" class="form-control @error('emergency_phone') is-invalid @enderror" 
                                       id="emergency_phone" name="emergency_phone" value="{{ old('emergency_phone', $employee->emergency_phone) }}" 
                                       placeholder="เบอร์โทรผู้ติดต่อฉุกเฉิน">
                                @error('emergency_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label for="notes" class="form-label">หมายเหตุ</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                          id="notes" name="notes" rows="3" 
                                          placeholder="หมายเหตุเพิ่มเติม">{{ old('notes', $employee->notes) }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('employees.show', $employee) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>ยกเลิก
                            </a>
                            <div>
                                <button type="button" class="btn btn-info me-2" id="previewBtn">
                                    <i class="fas fa-eye me-1"></i>ดูตัวอย่าง
                                </button>
                                <button type="submit" class="btn btn-primary-red" id="submitBtn">
                                    <i class="fas fa-save me-1"></i>บันทึกการแก้ไข
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Preview Modal --}}
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary-red text-white">
                <h5 class="modal-title" id="previewModalLabel">
                    <i class="fas fa-eye me-2"></i>ตัวอย่างข้อมูลที่แก้ไข
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="previewContent">
                <!-- Preview content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                <button type="button" class="btn btn-primary-red" onclick="$('#employeeForm').submit()">
                    <i class="fas fa-save me-1"></i>ยืนยันและบันทึก
                </button>
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
    // Generate new password
    $('#generatePasswordBtn').click(function() {
        $.get('{{ route("employees.generateData") }}', { type: 'password' })
            .done(function(data) {
                $('#password').val(data.password);
                $('#password').attr('type', 'text');
                $('#togglePasswordBtn i').removeClass('fa-eye').addClass('fa-eye-slash');
            });
    });

    // Toggle password visibility
    $('#togglePasswordBtn').click(function() {
        const passwordField = $('#password');
        const icon = $(this).find('i');
        
        if (passwordField.attr('type') === 'password') {
            passwordField.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            passwordField.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // Show current password (Super Admin only)
    @if(auth()->user()->role == 'super_admin')
    $('#showCurrentPassword').click(function() {
        const btn = $(this);
        const display = $('.password-display');
        
        if (btn.data('shown')) {
            display.text('••••••••');
            btn.html('<i class="fas fa-eye"></i> แสดง');
            btn.data('shown', false);
        } else {
            // In real implementation, you might want to fetch this via AJAX for security
            display.text('{{ $employee->getRawOriginal("password") ?? "ไม่สามารถแสดงได้" }}');
            btn.html('<i class="fas fa-eye-slash"></i> ซ่อน');
            btn.data('shown', true);
        }
    });
    @endif

    // Preview functionality
    $('#previewBtn').click(function() {
        const formData = $('#employeeForm').serializeArray();
        let previewHtml = '<div class="row g-3">';
        
        const fieldLabels = {
            'prefix': 'คำนำหน้า',
            'first_name': 'ชื่อ',
            'last_name': 'นามสกุล',
            'employee_id': 'รหัสพนักงาน',
            'username': 'ชื่อผู้ใช้',
            'email': 'อีเมล',
            'department': 'แผนก',
            'position': 'ตำแหน่ง',
            'hire_date': 'วันที่เริ่มงาน',
            'salary': 'เงินเดือน',
            'phone': 'เบอร์โทรศัพท์',
            'status': 'สถานะ',
            'role': 'บทบาท',
            'password': 'รหัสผ่านใหม่'
        };

        formData.forEach(function(field) {
            if (fieldLabels[field.name] && field.value) {
                let value = field.value;
                
                // Format specific fields
                if (field.name === 'salary') {
                    value = parseFloat(value).toLocaleString() + ' บาท';
                } else if (field.name === 'hire_date') {
                    value = new Date(value).toLocaleDateString('th-TH');
                } else if (field.name === 'password') {
                    value = '••••••••';
                }
                
                previewHtml += `
                    <div class="col-md-6">
                        <strong>${fieldLabels[field.name]}:</strong> ${value}
                    </div>
                `;
            }
        });
        
        previewHtml += '</div>';
        $('#previewContent').html(previewHtml);
        $('#previewModal').modal('show');
    });

    // Form submission
    $('#employeeForm').on('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = $('#submitBtn');
        const originalText = submitBtn.html();
        
        submitBtn.prop('disabled', true)
                 .html('<i class="fas fa-spinner fa-spin me-1"></i>กำลังบันทึก...');

        $.ajax({
            url: $(this).attr('action'),
            type: 'PUT',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        title: 'สำเร็จ!',
                        text: response.message,
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = response.redirect;
                    });
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                
                if (response.errors) {
                    // Display validation errors
                    Object.keys(response.errors).forEach(function(field) {
                        const input = $(`[name="${field}"]`);
                        input.addClass('is-invalid');
                        input.next('.invalid-feedback').remove();
                        input.after(`<div class="invalid-feedback">${response.errors[field][0]}</div>`);
                    });
                }
                
                Swal.fire({
                    title: 'เกิดข้อผิดพลาด!',
                    text: response.message || 'ไม่สามารถบันทึกข้อมูลได้',
                    icon: 'error'
                });
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    // Clear validation on input change
    $('input, select, textarea').on('input change', function() {
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();
    });
});
</script>
@endsection
