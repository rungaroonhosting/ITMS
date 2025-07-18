@extends('layouts.app')

@section('title', 'เพิ่มพนักงานใหม่')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('employees.index') }}">จัดการพนักงาน</a></li>
    <li class="breadcrumb-item active">เพิ่มพนักงานใหม่</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-primary fw-bold">
                    <i class="fas fa-user-plus me-2"></i>เพิ่มพนักงานใหม่
                </h1>
                <p class="text-muted mb-0">กรอกข้อมูลพนักงานใหม่เข้าระบบ</p>
                <div class="mt-2">
                    <span class="badge bg-info">
                        <i class="fas fa-info-circle me-1"></i>
                        Email จะถูกสร้างอัตโนมัติจาก ชื่อ.ตัวแรกนามสกุล@โดเมน
                    </span>
                </div>
            </div>
            <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>กลับ
            </a>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row text-center">
            <div class="col-md-3 mb-2">
                <button type="button" class="btn btn-outline-primary w-100" id="generateAllBtn">
                    <i class="fas fa-magic me-1"></i>สร้างทั้งหมดอัตโนมัติ
                </button>
                <div class="form-text mt-1">
                    <small class="text-muted">ต้องกรอกชื่อ-นามสกุล EN ก่อน</small>
                </div>
            </div>
            <div class="col-md-3 mb-2">
                <button type="button" class="btn btn-outline-info w-100" id="previewBtn">
                    <i class="fas fa-eye me-1"></i>ดูตัวอย่าง
                </button>
                <div class="form-text mt-1">
                    <small class="text-muted">ดูก่อนบันทึก</small>
                </div>
            </div>
            <div class="col-md-3 mb-2">
                <button type="button" class="btn btn-outline-warning w-100" id="clearAllBtn">
                    <i class="fas fa-trash me-1"></i>ล้างทั้งหมด
                </button>
                <div class="form-text mt-1">
                    <small class="text-muted">เริ่มใหม่</small>
                </div>
            </div>
            <div class="col-md-3 mb-2">
                <button type="button" class="btn btn-outline-success w-100" id="generateComputerBtn2">
                    <i class="fas fa-desktop me-1"></i>สร้างระบบคอมฯ
                </button>
                <div class="form-text mt-1">
                    <small class="text-muted">Username + Password</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Form -->
<form id="employeeForm" action="{{ route('employees.store') }}" method="POST">
    @csrf
    
    <!-- Error Messages -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h6 class="fw-bold"><i class="fas fa-exclamation-triangle me-2"></i>กรุณาแก้ไขข้อผิดพลาดต่อไปนี้:</h6>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    <!-- ข้อมูลพื้นฐาน -->
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <div class="border border-2 border-secondary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; background: #f8f9fa;">
                    <i class="fas fa-user text-secondary" style="font-size: 18px;"></i>
                </div>
                <div>
                    <h5 class="card-title mb-0">ข้อมูลพื้นฐาน</h5>
                    <small class="text-muted">ข้อมูลส่วนตัวและการติดต่อ</small>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <!-- รหัสพนักงาน -->
                <div class="col-md-6">
                    <label for="employee_code" class="form-label">รหัสพนักงาน</label>
                    <div class="input-group">
                        @php $userRole = auth()->user()->role ?? 'employee'; @endphp
                        @if($userRole === 'super_admin' || $userRole === 'it_admin')
                            <input type="text" class="form-control @error('employee_code') is-invalid @enderror" 
                                   id="employee_code" name="employee_code" value="{{ old('employee_code') }}" 
                                   placeholder="กรอกรหัสพนักงาน หรือสร้างอัตโนมัติ">
                        @else
                            <input type="text" class="form-control @error('employee_code') is-invalid @enderror" 
                                   id="employee_code" name="employee_code" value="{{ old('employee_code') }}" 
                                   placeholder="สร้างอัตโนมัติ" readonly>
                        @endif
                        <button type="button" class="btn btn-outline-primary" data-target="employee_code">
                            <i class="fas fa-magic"></i>
                        </button>
                    </div>
                    <div class="form-text">
                        @if($userRole === 'super_admin' || $userRole === 'it_admin')
                            สามารถกรอกเองหรือสร้างอัตโนมัติ เช่น EMP001
                        @else
                            รหัสจะถูกสร้างอัตโนมัติ เช่น EMP001 (เฉพาะ Admin แก้ไขได้)
                        @endif
                    </div>
                    @error('employee_code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- ID Keycard -->
                <div class="col-md-6">
                    <label for="keycard_id" class="form-label">ID Keycard</label>
                    <div class="input-group">
                        @if($userRole === 'super_admin' || $userRole === 'it_admin')
                            <input type="text" class="form-control @error('keycard_id') is-invalid @enderror" 
                                   id="keycard_id" name="keycard_id" value="{{ old('keycard_id') }}" 
                                   placeholder="กรอก ID Keycard หรือสร้างอัตโนมัติ">
                        @else
                            <input type="text" class="form-control @error('keycard_id') is-invalid @enderror" 
                                   id="keycard_id" name="keycard_id" value="{{ old('keycard_id') }}" 
                                   placeholder="สร้างอัตโนมัติ" readonly>
                        @endif
                        <button type="button" class="btn btn-outline-primary" data-target="keycard_id">
                            <i class="fas fa-magic"></i>
                        </button>
                    </div>
                    <div class="form-text">
                        @if($userRole === 'super_admin' || $userRole === 'it_admin')
                            สามารถกรอกเองหรือสร้างอัตโนมัติ เช่น KC123456
                        @else
                            รหัสบัตรจะถูกสร้างอัตโนมัติ เช่น KC123456 (เฉพาะ Admin แก้ไขได้)
                        @endif
                    </div>
                    @error('keycard_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- ชื่อภาษาไทย -->
                <div class="col-md-6">
                    <label for="first_name_th" class="form-label">
                        ชื่อภาษาไทย <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control @error('first_name_th') is-invalid @enderror" 
                           id="first_name_th" name="first_name_th" value="{{ old('first_name_th') }}" 
                           placeholder="กรอกชื่อภาษาไทย" required>
                    @error('first_name_th')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- นามสกุลภาษาไทย -->
                <div class="col-md-6">
                    <label for="last_name_th" class="form-label">
                        นามสกุลภาษาไทย <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control @error('last_name_th') is-invalid @enderror" 
                           id="last_name_th" name="last_name_th" value="{{ old('last_name_th') }}" 
                           placeholder="กรอกนามสกุลภาษาไทย" required>
                    @error('last_name_th')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- ชื่อภาษาอังกฤษ -->
                <div class="col-md-6">
                    <label for="first_name_en" class="form-label">
                        ชื่อภาษาอังกฤษ <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control @error('first_name_en') is-invalid @enderror" 
                           id="first_name_en" name="first_name_en" value="{{ old('first_name_en') }}" 
                           placeholder="First Name" required>
                    <div class="form-text">
                        <small class="text-muted">
                            <i class="fas fa-globe me-1"></i>
                            ใช้เฉพาะตัวอักษร A-Z เท่านั้น (สำหรับสร้าง Username)
                        </small>
                    </div>
                    @error('first_name_en')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- นามสกุลภาษาอังกฤษ -->
                <div class="col-md-6">
                    <label for="last_name_en" class="form-label">
                        นามสกุลภาษาอังกฤษ <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control @error('last_name_en') is-invalid @enderror" 
                           id="last_name_en" name="last_name_en" value="{{ old('last_name_en') }}" 
                           placeholder="Last Name" required>
                    <div class="form-text">
                        <small class="text-muted">
                            <i class="fas fa-globe me-1"></i>
                            ใช้เฉพาะตัวอักษร A-Z เท่านั้น (สำหรับสร้าง Username และ Email)
                        </small>
                    </div>
                    @error('last_name_en')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- เบอร์โทร -->
                <div class="col-md-6">
                    <label for="phone" class="form-label">
                        เบอร์โทรศัพท์ <span class="text-danger">*</span>
                    </label>
                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                           id="phone" name="phone" value="{{ old('phone') }}" 
                           placeholder="08x-xxx-xxxx" required>
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- ชื่อเล่น -->
                <div class="col-md-6">
                    <label for="nickname" class="form-label">ชื่อเล่น</label>
                    <input type="text" class="form-control @error('nickname') is-invalid @enderror" 
                           id="nickname" name="nickname" value="{{ old('nickname') }}" 
                           placeholder="กรอกชื่อเล่น">
                    @error('nickname')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- ระบบคอมพิวเตอร์ -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <div class="border border-2 border-secondary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; background: #f8f9fa;">
                    <i class="fas fa-desktop text-secondary" style="font-size: 18px;"></i>
                </div>
                <div>
                    <h5 class="card-title mb-0">ระบบคอมพิวเตอร์</h5>
                    <small class="text-muted">Username และรหัสผ่านสำหรับคอมพิวเตอร์</small>
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="generateComputerBtn">
                <i class="fas fa-desktop me-1"></i>สร้างระบบคอมฯ
            </button>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <!-- Username -->
                <div class="col-md-6">
                    <label for="username" class="form-label">
                        Username (เปิดคอมพิวเตอร์) <span class="text-danger">*</span>
                        <span class="badge bg-info ms-2">Auto Generate</span>
                    </label>
                    <div class="input-group">
                        <input type="text" 
                               class="form-control @error('username') is-invalid @enderror" 
                               id="username" 
                               name="username" 
                               value="{{ old('username') }}"
                               placeholder="จะสร้างจากชื่อ EN"
                               required>
                        <button type="button" class="btn btn-outline-primary" data-target="username">
                            <i class="fas fa-user"></i>
                        </button>
                    </div>
                    <div class="form-text">
                        รูปแบบ: <strong>ชื่อ</strong> ภาษาอังกฤษตัวเล็ก (เช่น john)
                        <div class="mt-1">
                            <small class="text-muted">
                                <i class="fas fa-lightbulb me-1"></i>
                                Username นี้จะใช้เป็นฐานในการสร้าง Email
                            </small>
                        </div>
                    </div>
                    @error('username')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Computer Password -->
                <div class="col-md-6">
                    <label for="computer_password" class="form-label">Password (เปิดคอมพิวเตอร์)</label>
                    <div class="input-group">
                        @if($userRole === 'super_admin' || $userRole === 'it_admin')
                            <input type="password" 
                                   class="form-control @error('computer_password') is-invalid @enderror" 
                                   id="computer_password" 
                                   name="computer_password" 
                                   value="{{ old('computer_password') }}"
                                   placeholder="Random 10 ตัวอักษร">
                            <button type="button" class="btn btn-outline-primary" data-target="computer_password">
                                <i class="fas fa-lock"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary" data-toggle-password="computer_password">
                                <i class="fas fa-eye"></i>
                            </button>
                        @else
                            <input type="password" 
                                   class="form-control bg-light" 
                                   id="computer_password" 
                                   name="computer_password" 
                                   value="{{ old('computer_password') }}"
                                   placeholder="สร้างอัตโนมัติ"
                                   readonly>
                            <button type="button" class="btn btn-outline-primary" data-target="computer_password">
                                <i class="fas fa-lock"></i>
                            </button>
                        @endif
                    </div>
                    <div class="form-text">
                        รหัสผ่านสำหรับเปิดคอมพิวเตอร์
                        @if(!($userRole === 'super_admin' || $userRole === 'it_admin'))
                            <span class="text-warning">(มองไม่เห็น - เฉพาะ Admin)</span>
                        @endif
                    </div>
                    @error('computer_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Copier Code -->
                <div class="col-md-6">
                    <label for="copier_code" class="form-label">รหัสเครื่องถ่ายเอกสาร</label>
                    <div class="input-group">
                        <input type="text" 
                               class="form-control @error('copier_code') is-invalid @enderror" 
                               id="copier_code" 
                               name="copier_code" 
                               value="{{ old('copier_code') }}"
                               placeholder="Random 4 หลัก" 
                               maxlength="4">
                        <button type="button" class="btn btn-outline-primary" data-target="copier_code">
                            <i class="fas fa-print"></i>
                        </button>
                    </div>
                    <div class="form-text">รหัส 4 หลักตัวเลขสำหรับใช้เครื่องถ่ายเอกสาร</div>
                    @error('copier_code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- ระบบอีเมล -->
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <div class="border border-2 border-secondary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; background: #f8f9fa;">
                    <i class="fas fa-envelope text-secondary" style="font-size: 18px;"></i>
                </div>
                <div>
                    <h5 class="card-title mb-0">ระบบอีเมล</h5>
                    <small class="text-muted">อีเมลและรหัสผ่านสำหรับระบบอีเมล</small>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <!-- Email -->
                <div class="col-md-8">
                    <label for="email" class="form-label">
                        อีเมล <span class="text-danger">*</span>
                        <span class="badge bg-info ms-2">Auto Generate</span>
                    </label>
                    <div class="input-group">
                        <input type="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}"
                               placeholder="จะสร้างจากชื่อ.ตัวแรกนามสกุล"
                               required>
                        <select class="form-select" id="email_domain" style="max-width: 220px;">
                            <option value="bettersystem.co.th">@bettersystem.co.th</option>
                            <option value="better-groups.com">@better-groups.com</option>
                        </select>
                        <button type="button" class="btn btn-outline-primary" data-target="email">
                            <i class="fas fa-envelope"></i>
                        </button>
                    </div>
                    <div class="form-text">
                        รูปแบบ: <strong>ชื่อ.ตัวแรกของนามสกุล@โดเมน</strong>
                        <div id="emailPreview" class="mt-2" style="display: none;">
                            <span class="text-success">ตัวอย่าง: </span>
                            <code class="text-primary" id="emailPreviewText"></code>
                        </div>
                        <div class="mt-2">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Email นี้จะถูกใช้เป็น Login Email ด้วย
                            </small>
                        </div>
                    </div>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Email Password -->
                <div class="col-md-4">
                    <label for="email_password" class="form-label">Password อีเมล</label>
                    <div class="input-group">
                        @if($userRole === 'super_admin' || $userRole === 'it_admin')
                            <input type="password" 
                                   class="form-control @error('email_password') is-invalid @enderror" 
                                   id="email_password" 
                                   name="email_password" 
                                   value="{{ old('email_password') }}"
                                   placeholder="Random 10 ตัวอักษร">
                            <button type="button" class="btn btn-outline-primary" data-target="email_password">
                                <i class="fas fa-lock"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary" data-toggle-password="email_password">
                                <i class="fas fa-eye"></i>
                            </button>
                        @else
                            <input type="password" 
                                   class="form-control bg-light" 
                                   id="email_password" 
                                   name="email_password" 
                                   value="{{ old('email_password') }}"
                                   placeholder="สร้างอัตโนมัติ"
                                   readonly>
                            <button type="button" class="btn btn-outline-primary" data-target="email_password">
                                <i class="fas fa-lock"></i>
                            </button>
                        @endif
                    </div>
                    <div class="form-text">
                        @if(!($userRole === 'super_admin' || $userRole === 'it_admin'))
                            <span class="text-warning">(มองไม่เห็น - เฉพาะ Admin)</span>
                        @endif
                    </div>
                    @error('email_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- แผนกและสิทธิ์ -->
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <div class="border border-2 border-secondary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; background: #f8f9fa;">
                    <i class="fas fa-building text-secondary" style="font-size: 18px;"></i>
                </div>
                <div>
                    <h5 class="card-title mb-0">แผนกและสิทธิ์</h5>
                    <small class="text-muted">แผนกการทำงานและสิทธิ์การใช้งาน</small>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <!-- Department -->
                <div class="col-md-6">
                    <label for="department_id" class="form-label">
                        แผนกการทำงาน <span class="text-danger">*</span>
                    </label>
                    <select class="form-select @error('department_id') is-invalid @enderror" 
                            id="department_id" 
                            name="department_id" 
                            required>
                        <option value="">เลือกแผนก</option>
                        @php
                            $departmentsList = [
                                '1' => 'บัญชี',
                                '2' => 'IT',
                                '3' => 'ฝ่ายขาย',
                                '4' => 'การตลาด',
                                '5' => 'บุคคล',
                                '6' => 'ผลิต',
                                '7' => 'คลังสินค้า',
                                '8' => 'บริหาร'
                            ];
                            
                            if (isset($departments) && is_object($departments)) {
                                $deptCollection = $departments;
                            } elseif (isset($departments) && is_array($departments)) {
                                $deptCollection = collect($departments);
                            } else {
                                $deptCollection = collect($departmentsList)->map(function($name, $id) {
                                    return (object)['id' => $id, 'name' => $name];
                                });
                            }
                        @endphp
                        
                        @if($userRole === 'express')
                            @foreach($deptCollection->where('name', 'บัญชี') as $department)
                                <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        @else
                            @foreach($deptCollection as $department)
                                <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('department_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @if($userRole === 'express')
                        <div class="form-text text-info">Express: สามารถเลือกเฉพาะแผนกบัญชี</div>
                    @elseif($userRole === 'super_admin')
                        <div class="form-text text-success">
                            <i class="fas fa-plus-circle me-1"></i>
                            SuperAdmin: สามารถเพิ่มแผนกใหม่ได้ในภายหลัง
                        </div>
                    @endif
                </div>

                <!-- Position -->
                <div class="col-md-6">
                    <label for="position" class="form-label">
                        ตำแหน่ง <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           class="form-control @error('position') is-invalid @enderror" 
                           id="position" 
                           name="position" 
                           value="{{ old('position') }}"
                           placeholder="เช่น Developer, Accountant"
                           required>
                    @error('position')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Role -->
                <div class="col-md-6">
                    <label for="role" class="form-label">
                        สิทธิ์การใช้งาน <span class="text-danger">*</span>
                    </label>
                    <select class="form-select @error('role') is-invalid @enderror" 
                            id="role" 
                            name="role" 
                            required>
                        <option value="">เลือกสิทธิ์</option>
                        <option value="employee" {{ old('role') == 'employee' ? 'selected' : '' }}>พนักงานทั่วไป (Employee)</option>
                        @if($userRole === 'super_admin' || $userRole === 'it_admin')
                            <option value="hr" {{ old('role') == 'hr' ? 'selected' : '' }}>ฝ่ายบุคคล (HR)</option>
                            <option value="manager" {{ old('role') == 'manager' ? 'selected' : '' }}>ผู้จัดการ (Manager)</option>
                            <option value="express" {{ old('role') == 'express' ? 'selected' : '' }}>Express</option>
                            @if($userRole === 'super_admin')
                                <option value="it_admin" {{ old('role') == 'it_admin' ? 'selected' : '' }}>IT Admin</option>
                            @endif
                        @endif
                    </select>
                    @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Status -->
                <div class="col-md-6">
                    <label for="status" class="form-label">
                        สถานะ <span class="text-danger">*</span>
                    </label>
                    <select class="form-select @error('status') is-invalid @enderror" 
                            id="status" 
                            name="status" 
                            required>
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>ใช้งาน (Active)</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>ไม่ใช้งาน (Inactive)</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- โปรแกรม Express (Conditional) -->
    <div class="card mb-4" id="expressSection" style="display: none;">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <div class="border border-2 border-warning rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; background: #fff8e1;">
                    <i class="fas fa-bolt text-warning" style="font-size: 18px;"></i>
                </div>
                <div>
                    <h5 class="card-title mb-0">โปรแกรม Express</h5>
                    <small class="text-muted">เฉพาะแผนกบัญชี</small>
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-outline-warning" id="generateExpressBtn">
                <i class="fas fa-bolt me-1"></i>สร้าง Express
            </button>
        </div>
        <div class="card-body">
            <div class="alert alert-info" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                โปรแกรม Express จะถูกลงทะเบียนเฉพาะพนักงานในแผนกบัญชีเท่านั้น
            </div>
            
            <div class="row g-3">
                <!-- Express Username -->
                <div class="col-md-6">
                    <label for="express_username" class="form-label">
                        Username Express (7 ตัวอักษร)
                        <span class="badge bg-warning ms-2">Auto Generate</span>
                    </label>
                    <div class="input-group">
                        <input type="text" 
                               class="form-control @error('express_username') is-invalid @enderror" 
                               id="express_username" 
                               name="express_username" 
                               value="{{ old('express_username') }}"
                               placeholder="จะสร้างจากชื่อ EN" 
                               maxlength="7">
                        <button type="button" class="btn btn-outline-primary" data-target="express_username">
                            <i class="fas fa-bolt"></i>
                        </button>
                    </div>
                    <div class="form-text">สร้างจากชื่อภาษาอังกฤษ 7 ตัวอักษร</div>
                    @error('express_username')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Express Password -->
                <div class="col-md-6">
                    <label for="express_password" class="form-label">Password โปรแกรม Express</label>
                    <div class="input-group">
                        @if($userRole === 'super_admin' || $userRole === 'it_admin')
                            <input type="text" 
                                   class="form-control @error('express_password') is-invalid @enderror" 
                                   id="express_password" 
                                   name="express_password" 
                                   value="{{ old('express_password') }}"
                                   placeholder="Random 4 ตัวอักษร + ตัวเลข" 
                                   maxlength="4">
                            <button type="button" class="btn btn-outline-primary" data-target="express_password">
                                <i class="fas fa-lock"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary" data-toggle-password="express_password">
                                <i class="fas fa-eye"></i>
                            </button>
                        @else
                            <input type="text" 
                                   class="form-control bg-light" 
                                   id="express_password" 
                                   name="express_password" 
                                   value="{{ old('express_password') }}"
                                   placeholder="สร้างอัตโนมัติ"
                                   readonly>
                            <button type="button" class="btn btn-outline-primary" data-target="express_password">
                                <i class="fas fa-lock"></i>
                            </button>
                        @endif
                    </div>
                    <div class="form-text">
                        รหัสผ่าน 4 ตัวอักษรและตัวเลขสำหรับโปรแกรม Express
                        @if(!($userRole === 'super_admin' || $userRole === 'it_admin'))
                            <span class="text-warning">(มองไม่เห็น - เฉพาะ Admin)</span>
                        @endif
                    </div>
                    @error('express_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- ระบบ Login -->
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <div class="border border-2 border-secondary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; background: #f8f9fa;">
                    <i class="fas fa-sign-in-alt text-secondary" style="font-size: 18px;"></i>
                </div>
                <div>
                    <h5 class="card-title mb-0">ระบบ Login</h5>
                    <small class="text-muted">ข้อมูลสำหรับเข้าสู่ระบบ IT Management</small>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <!-- Login Email (Auto-sync) -->
                <div class="col-md-6">
                    <label for="login_email" class="form-label">
                        Email สำหรับ Login
                        <span class="badge bg-success ms-2">Auto Sync</span>
                    </label>
                    <input type="email" 
                           class="form-control bg-light" 
                           id="login_email" 
                           name="login_email" 
                           value="{{ old('login_email') }}"
                           placeholder="จะใช้อีเมลเดียวกันข้างต้น"
                           readonly>
                    <div class="form-text text-success">จะใช้อีเมลเดียวกันกับที่สร้างข้างต้น</div>
                </div>

                <!-- System Password -->
                <div class="col-md-6">
                    <label for="password" class="form-label">
                        Password สำหรับ Login ระบบ <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        @if($userRole === 'super_admin' || $userRole === 'it_admin')
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   value="{{ old('password') }}"
                                   placeholder="Random 10 ตัวอักษร"
                                   required>
                            <button type="button" class="btn btn-outline-primary" data-target="password">
                                <i class="fas fa-lock"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary" data-toggle-password="password">
                                <i class="fas fa-eye"></i>
                            </button>
                        @else
                            <input type="hidden" name="password" value="Bettersystem123">
                            <input type="password" 
                                   class="form-control bg-light" 
                                   id="password_display" 
                                   value="Bettersystem123"
                                   placeholder="รหัsผ่านเริ่มต้น"
                                   readonly>
                            <button type="button" class="btn btn-outline-secondary" disabled>
                                <i class="fas fa-lock"></i>
                            </button>
                        @endif
                    </div>
                    <div class="form-text">
                        รหัสผ่านสำหรับเข้าระบบ IT Management
                        @if(!($userRole === 'super_admin' || $userRole === 'it_admin'))
                            <span class="text-warning">(ใช้รหัสเริ่มต้น - เปลี่ยนได้ภายหลัง)</span>
                        @endif
                    </div>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Form Actions -->
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <a href="{{ route('employees.index') }}" 
                   class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>ยกเลิก
                </a>
                
                <button type="submit" 
                        class="btn btn-primary"
                        id="submitBtn">
                    <i class="fas fa-plus me-2"></i>สร้างพนักงาน
                </button>
            </div>
        </div>
    </div>
</form>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalLabel">
                    <i class="fas fa-eye me-2"></i>ตัวอย่างข้อมูลพนักงาน
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="previewContent">
                <!-- Preview content will be inserted here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                <button type="button" class="btn btn-primary" onclick="submitForm()">
                    ยืนยันและบันทึก
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Employee Create Form Loaded - Starting Fresh');
    
    // Utility Functions
    const utils = {
        showLoading: (button) => {
            button.disabled = true;
            const originalText = button.innerHTML;
            button.dataset.originalText = originalText;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>กำลังสร้าง...';
        },
        
        hideLoading: (button) => {
            button.disabled = false;
            if (button.dataset.originalText) {
                button.innerHTML = button.dataset.originalText;
            }
        },
        
        generateRandomString: (length, includeNumbers = true) => {
            const chars = includeNumbers ? 
                'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789' :
                'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
            let result = '';
            for (let i = 0; i < length; i++) {
                result += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            return result;
        },
        
        generateRandomNumber: (length) => {
            let result = '';
            for (let i = 0; i < length; i++) {
                result += Math.floor(Math.random() * 10);
            }
            return result;
        },
        
        showNotification: (message, type = 'success') => {
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            const iconClass = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
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
    };
    
    // Generator Functions
    const generators = {
        employeeCode: () => `EMP${utils.generateRandomNumber(3)}`,
        keycardId: () => `KC${utils.generateRandomNumber(6)}`,
        username: () => {
            const firstName = document.getElementById('first_name_en').value.trim();
            const englishRegex = /^[a-zA-Z\s]+$/;
            
            if (firstName && englishRegex.test(firstName)) {
                return firstName.toLowerCase();
            }
            return '';
        },
        email: () => {
            const firstName = document.getElementById('first_name_en').value.trim();
            const lastName = document.getElementById('last_name_en').value.trim();
            const domain = document.getElementById('email_domain').value;
            const englishRegex = /^[a-zA-Z\s]+$/;
            
            if (firstName && lastName && domain && englishRegex.test(firstName) && englishRegex.test(lastName)) {
                return `${firstName.toLowerCase()}.${lastName.charAt(0).toLowerCase()}@${domain}`;
            }
            return '';
        },
        password: () => utils.generateRandomString(10, true),
        copierCode: () => utils.generateRandomNumber(4),
        expressUsername: () => {
            const firstName = document.getElementById('first_name_en').value.trim().toLowerCase();
            if (firstName.length >= 7) {
                return firstName.substring(0, 7);
            } else if (firstName.length > 0) {
                return firstName.padEnd(7, 'x');
            }
            return utils.generateRandomString(7, false).toLowerCase();
        },
        expressPassword: () => utils.generateRandomString(4, true)
    };
    
    // Auto-generation functions
    const autoGenerate = {
        username: () => {
            const username = generators.username();
            if (username) {
                document.getElementById('username').value = username;
                
                // Add animation
                const usernameInput = document.getElementById('username');
                usernameInput.style.background = 'linear-gradient(45deg, #e3f2fd, #f3e5f5)';
                usernameInput.style.transform = 'scale(1.02)';
                usernameInput.style.transition = 'all 0.3s ease';
                
                setTimeout(() => {
                    usernameInput.style.background = '';
                    usernameInput.style.transform = '';
                }, 1500);
                
                console.log('✅ Username generated:', username);
            }
        },
        
        email: () => {
            const email = generators.email();
            if (email) {
                document.getElementById('email').value = email;
                document.getElementById('login_email').value = email;
                autoGenerate.showEmailPreview();
                
                // Add subtle animation for both fields
                const emailInput = document.getElementById('email');
                const loginEmailInput = document.getElementById('login_email');
                
                [emailInput, loginEmailInput].forEach(input => {
                    if (input) {
                        input.style.background = 'linear-gradient(45deg, #e3f2fd, #f3e5f5)';
                        input.style.transform = 'scale(1.02)';
                        input.style.transition = 'all 0.3s ease';
                    }
                });
                
                setTimeout(() => {
                    [emailInput, loginEmailInput].forEach(input => {
                        if (input) {
                            input.style.background = '';
                            input.style.transform = '';
                        }
                    });
                }, 1500);
                
                console.log('✅ Email generated:', email);
            }
        },
        
        showEmailPreview: () => {
            const firstName = document.getElementById('first_name_en').value.trim();
            const lastName = document.getElementById('last_name_en').value.trim();
            const domain = document.getElementById('email_domain').value;
            const previewDiv = document.getElementById('emailPreview');
            const previewText = document.getElementById('emailPreviewText');
            
            if (!previewDiv || !previewText) return;
            
            // Validate English characters
            const englishRegex = /^[a-zA-Z\s]+$/;
            
            if (firstName && lastName) {
                if (englishRegex.test(firstName) && englishRegex.test(lastName)) {
                    const emailPreview = `${firstName.toLowerCase()}.${lastName.charAt(0).toLowerCase()}@${domain}`;
                    previewText.textContent = emailPreview;
                    previewDiv.style.display = 'block';
                    previewDiv.className = 'mt-2 text-success';
                } else {
                    previewText.textContent = 'กรุณากรอกชื่อ-นามสกุลภาษาอังกฤษเท่านั้น';
                    previewDiv.style.display = 'block';
                    previewDiv.className = 'mt-2 text-warning';
                }
            } else {
                previewDiv.style.display = 'none';
            }
        }
    };
    
    // Event Handlers
    const eventHandlers = {
        handleMagicClick: async (event) => {
            const button = event.target.closest('[data-target]');
            if (!button) return;
            
            const target = button.dataset.target;
            const targetElement = document.getElementById(target);
            if (!targetElement) return;
            
            utils.showLoading(button);
            
            try {
                let value = '';
                
                switch (target) {
                    case 'employee_code':
                        value = generators.employeeCode();
                        break;
                    case 'keycard_id':
                        value = generators.keycardId();
                        break;
                    case 'username':
                        value = generators.username();
                        break;
                    case 'email':
                        value = generators.email();
                        break;
                    case 'computer_password':
                    case 'email_password':
                    case 'password':
                        value = generators.password();
                        break;
                    case 'copier_code':
                        value = generators.copierCode();
                        break;
                    case 'express_username':
                        value = generators.expressUsername();
                        break;
                    case 'express_password':
                        value = generators.expressPassword();
                        break;
                }
                
                if (value) {
                    targetElement.value = value;
                    
                    if (target === 'email') {
                        const loginEmail = document.getElementById('login_email');
                        if (loginEmail) loginEmail.value = value;
                        autoGenerate.showEmailPreview();
                    }
                    
                    // Show different messages based on target
                    let message = '';
                    switch (target) {
                        case 'email':
                            message = `✅ สร้าง Email สำเร็จ: ${value}`;
                            break;
                        case 'username':
                            message = `✅ สร้าง Username สำเร็จ: ${value}`;
                            break;
                        case 'employee_code':
                            message = `✅ สร้างรหัสพนักงานสำเร็จ: ${value}`;
                            break;
                        case 'keycard_id':
                            message = `✅ สร้าง ID Keycard สำเร็จ: ${value}`;
                            break;
                        case 'express_username':
                            message = `✅ สร้าง Express Username สำเร็จ: ${value}`;
                            break;
                        case 'express_password':
                            message = `✅ สร้างรหัส Express สำเร็จ: ${value}`;
                            break;
                        default:
                            message = `✅ สร้าง ${target} สำเร็จ: ${value}`;
                    }
                    
                    utils.showNotification(message);
                }
                
            } catch (error) {
                console.error(`Error generating ${target}:`, error);
                utils.showNotification(`❌ เกิดข้อผิดพลาดในการสร้าง ${target}`, 'error');
            } finally {
                utils.hideLoading(button);
            }
        },
        
        handlePasswordToggle: (event) => {
            const button = event.target.closest('[data-toggle-password]');
            if (!button) return;
            
            const target = button.dataset.togglePassword;
            const targetElement = document.getElementById(target);
            
            if (targetElement) {
                if (targetElement.type === 'password') {
                    targetElement.type = 'text';
                    button.innerHTML = '<i class="fas fa-eye-slash"></i>';
                } else {
                    targetElement.type = 'password';
                    button.innerHTML = '<i class="fas fa-eye"></i>';
                }
            }
        },
        
        handleDepartmentChange: () => {
            const departmentSelect = document.getElementById('department_id');
            const expressSection = document.getElementById('expressSection');
            
            if (!departmentSelect || !expressSection) {
                console.log('❌ Department select or express section not found');
                return;
            }
            
            const selectedOption = departmentSelect.options[departmentSelect.selectedIndex];
            const departmentName = selectedOption ? selectedOption.textContent.trim() : '';
            
            console.log('🏢 Department selected:', departmentName);
            
            if (departmentName === 'บัญชี') {
                expressSection.style.display = 'block';
                
                // Auto-generate Express fields if name is available
                const firstName = document.getElementById('first_name_en').value.trim();
                if (firstName) {
                    const expressUsernameEl = document.getElementById('express_username');
                    const expressPasswordEl = document.getElementById('express_password');
                    
                    if (expressUsernameEl) {
                        expressUsernameEl.value = generators.expressUsername();
                    }
                    if (expressPasswordEl) {
                        expressPasswordEl.value = generators.expressPassword();
                    }
                }
                
                utils.showNotification('⚡ แผนกบัญชี: เปิดใช้งานโปรแกรม Express แล้ว', 'success');
            } else {
                expressSection.style.display = 'none';
                
                // Clear Express fields
                const expressUsernameEl = document.getElementById('express_username');
                const expressPasswordEl = document.getElementById('express_password');
                
                if (expressUsernameEl) expressUsernameEl.value = '';
                if (expressPasswordEl) expressPasswordEl.value = '';
                
                if (departmentName && departmentName !== 'เลือกแผนก') {
                    console.log('🏢 Department changed to:', departmentName, '- Express section hidden');
                }
            }
        },
        
        handleInputValidation: (event) => {
            const input = event.target;
            const englishRegex = /^[a-zA-Z\s]*$/;
            
            if (input.id === 'first_name_en' || input.id === 'last_name_en') {
                if (!englishRegex.test(input.value)) {
                    input.style.borderColor = '#dc3545';
                    input.style.boxShadow = '0 0 0 0.2rem rgba(220, 53, 69, 0.25)';
                    
                    // Show warning
                    let warningDiv = input.parentElement.querySelector('.english-warning');
                    if (!warningDiv) {
                        warningDiv = document.createElement('div');
                        warningDiv.className = 'english-warning mt-1 text-danger';
                        warningDiv.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i>กรุณากรอกเฉพาะตัวอักษร A-Z เท่านั้น';
                        input.parentElement.appendChild(warningDiv);
                    }
                    
                    // Remove non-English characters
                    input.value = input.value.replace(/[^a-zA-Z\s]/g, '');
                } else {
                    input.style.borderColor = '';
                    input.style.boxShadow = '';
                    
                    // Remove warning
                    const warningDiv = input.parentElement.querySelector('.english-warning');
                    if (warningDiv) {
                        warningDiv.remove();
                    }
                }
            }
        },
        
        handlePhoneFormat: (event) => {
            let value = event.target.value.replace(/\D/g, '');
            if (value.length >= 3 && value.length <= 6) {
                value = value.slice(0, 3) + '-' + value.slice(3);
            } else if (value.length > 6) {
                value = value.slice(0, 3) + '-' + value.slice(3, 6) + '-' + value.slice(6, 10);
            }
            event.target.value = value;
        }
    };
    
    // Form Actions
    const formActions = {
        generateAll: async () => {
            const button = document.getElementById('generateAllBtn');
            utils.showLoading(button);
            
            try {
                console.log('🎯 Starting generateAll...');
                
                // Generate basic codes
                document.getElementById('employee_code').value = generators.employeeCode();
                document.getElementById('keycard_id').value = generators.keycardId();
                
                // Username และ Email generation
                if (document.getElementById('first_name_en').value) {
                    autoGenerate.username();
                    await new Promise(resolve => setTimeout(resolve, 200));
                    
                    if (document.getElementById('last_name_en').value) {
                        autoGenerate.email();
                    }
                } else {
                    utils.showNotification('❌ กรุณากรอกชื่อภาษาอังกฤษก่อน จึงจะสร้าง Username และ Email ได้', 'error');
                    return;
                }
                
                // Generate passwords
                document.getElementById('computer_password').value = generators.password();
                document.getElementById('email_password').value = generators.password();
                document.getElementById('password').value = generators.password();
                document.getElementById('copier_code').value = generators.copierCode();
                
                // Express fields (ถ้าเลือกแผนกบัญชี)
                const departmentSelect = document.getElementById('department_id');
                const selectedOption = departmentSelect.options[departmentSelect.selectedIndex];
                if (selectedOption && selectedOption.textContent.trim() === 'บัญชี') {
                    document.getElementById('express_username').value = generators.expressUsername();
                    document.getElementById('express_password').value = generators.expressPassword();
                }
                
                utils.showNotification('🎉 สร้างข้อมูลทั้งหมดสำเร็จ! ตรวจสอบข้อมูลที่สร้างแล้ว', 'success');
                
            } catch (error) {
                console.error('Error in generateAll:', error);
                utils.showNotification('❌ เกิดข้อผิดพลาดในการสร้างข้อมูล', 'error');
            } finally {
                utils.hideLoading(button);
            }
        },
        
        clearAll: () => {
            if (confirm('ต้องการล้างข้อมูลทั้งหมดหรือไม่?')) {
                document.getElementById('employeeForm').reset();
                
                const emailPreview = document.getElementById('emailPreview');
                const expressSection = document.getElementById('expressSection');
                
                if (emailPreview) emailPreview.style.display = 'none';
                if (expressSection) expressSection.style.display = 'none';
                
                // Re-generate initial codes
                setTimeout(() => {
                    document.getElementById('employee_code').value = generators.employeeCode();
                    document.getElementById('keycard_id').value = generators.keycardId();
                    document.getElementById('copier_code').value = generators.copierCode();
                }, 100);
                
                utils.showNotification('🗑️ ล้างข้อมูลทั้งหมดแล้ว', 'success');
            }
        },
        
        showPreview: () => {
            const formData = new FormData(document.getElementById('employeeForm'));
            const data = {};
            for (let [key, value] of formData.entries()) {
                data[key] = value;
            }
            
            const previewContent = document.getElementById('previewContent');
            if (!previewContent) return;
            
            const departmentSelect = document.getElementById('department_id');
            const departmentName = departmentSelect && departmentSelect.selectedIndex > 0 ? 
                departmentSelect.options[departmentSelect.selectedIndex].textContent : '-';
            
            previewContent.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary">ข้อมูลพื้นฐาน</h6>
                        <p><strong>รหัสพนักงาน:</strong> ${data.employee_code || '-'}</p>
                        <p><strong>ชื่อ-นามสกุล (ไทย):</strong> ${data.first_name_th || '-'} ${data.last_name_th || '-'}</p>
                        <p><strong>ชื่อ-นามสกุล (อังกฤษ):</strong> ${data.first_name_en || '-'} ${data.last_name_en || '-'}</p>
                        <p><strong>เบอร์โทร:</strong> ${data.phone || '-'}</p>
                        <p><strong>ชื่อเล่น:</strong> ${data.nickname || '-'}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary">ระบบคอมพิวเตอร์</h6>
                        <p><strong>Username:</strong> ${data.username || '-'}</p>
                        <p><strong>Email:</strong> <span class="text-success">${data.email || '-'}</span></p>
                        <p><strong>รหัสถ่ายเอกสาร:</strong> ${data.copier_code || '-'}</p>
                        <p><strong>แผนก:</strong> ${departmentName}</p>
                        <p><strong>ตำแหน่ง:</strong> ${data.position || '-'}</p>
                        ${data.express_username ? `<p><strong>Express Username:</strong> ${data.express_username}</p>` : ''}
                        ${data.express_password ? `<p><strong>Express Password:</strong> ${data.express_password}</p>` : ''}
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle me-2"></i>รูปแบบ Email</h6>
                            <p class="mb-0">Email จะถูกสร้างในรูปแบบ: <strong>ชื่อ.ตัวแรกนามสกุล@โดเมน</strong></p>
                            <p class="mb-0">ตัวอย่าง: john.s@bettersystem.co.th</p>
                        </div>
                    </div>
                </div>
            `;
            
            const modal = new bootstrap.Modal(document.getElementById('previewModal'));
            modal.show();
        }
    };
    
    // Event Listeners Setup
    try {
        // Click handlers
        document.addEventListener('click', eventHandlers.handleMagicClick);
        document.addEventListener('click', eventHandlers.handlePasswordToggle);
        
        // Department change handler
        const departmentSelect = document.getElementById('department_id');
        if (departmentSelect) {
            departmentSelect.addEventListener('change', eventHandlers.handleDepartmentChange);
            console.log('✅ Department change listener attached');
        } else {
            console.log('❌ Department select not found');
        }
        
        // Phone format handler
        const phoneInput = document.getElementById('phone');
        if (phoneInput) {
            phoneInput.addEventListener('input', eventHandlers.handlePhoneFormat);
        }
        
        // English validation handlers
        const firstNameEn = document.getElementById('first_name_en');
        const lastNameEn = document.getElementById('last_name_en');
        
        if (firstNameEn) {
            firstNameEn.addEventListener('input', eventHandlers.handleInputValidation);
            firstNameEn.addEventListener('input', () => {
                setTimeout(() => {
                    autoGenerate.username();
                    autoGenerate.email();
                    autoGenerate.showEmailPreview();
                    
                    // Auto-generate Express fields if accounting department is selected
                    const departmentSelect = document.getElementById('department_id');
                    if (departmentSelect && departmentSelect.selectedIndex > 0) {
                        const selectedOption = departmentSelect.options[departmentSelect.selectedIndex];
                        if (selectedOption && selectedOption.textContent.trim() === 'บัญชี') {
                            const expressUsername = document.getElementById('express_username');
                            if (expressUsername) {
                                expressUsername.value = generators.expressUsername();
                            }
                        }
                    }
                }, 300);
            });
        }
        
        if (lastNameEn) {
            lastNameEn.addEventListener('input', eventHandlers.handleInputValidation);
            lastNameEn.addEventListener('input', () => {
                setTimeout(() => {
                    autoGenerate.email();
                    autoGenerate.showEmailPreview();
                }, 300);
            });
        }
        
        // Email domain change handler
        const emailDomain = document.getElementById('email_domain');
        if (emailDomain) {
            emailDomain.addEventListener('change', () => {
                autoGenerate.email();
                autoGenerate.showEmailPreview();
            });
        }
        
        // Email input sync
        const emailInput = document.getElementById('email');
        if (emailInput) {
            emailInput.addEventListener('input', (e) => {
                const loginEmail = document.getElementById('login_email');
                if (loginEmail) {
                    loginEmail.value = e.target.value;
                }
            });
        }
        
        // Quick Action buttons
        const generateAllBtn = document.getElementById('generateAllBtn');
        if (generateAllBtn) {
            generateAllBtn.addEventListener('click', formActions.generateAll);
        }
        
        const clearAllBtn = document.getElementById('clearAllBtn');
        if (clearAllBtn) {
            clearAllBtn.addEventListener('click', formActions.clearAll);
        }
        
        const previewBtn = document.getElementById('previewBtn');
        if (previewBtn) {
            previewBtn.addEventListener('click', formActions.showPreview);
        }
        
        // Computer generation buttons
        const generateComputerBtn2 = document.getElementById('generateComputerBtn2');
        if (generateComputerBtn2) {
            generateComputerBtn2.addEventListener('click', async () => {
                const button = generateComputerBtn2;
                utils.showLoading(button);
                
                try {
                    if (!document.getElementById('first_name_en').value) {
                        utils.showNotification('❌ กรุณากรอกชื่อภาษาอังกฤษก่อน', 'error');
                        return;
                    }
                    
                    autoGenerate.username();
                    await new Promise(resolve => setTimeout(resolve, 200));
                    
                    if (document.getElementById('last_name_en').value) {
                        autoGenerate.email();
                    }
                    
                    document.getElementById('computer_password').value = generators.password();
                    document.getElementById('copier_code').value = generators.copierCode();
                    
                    utils.showNotification('💻 สร้างข้อมูลระบบคอมพิวเตอร์สำเร็จ!', 'success');
                } catch (error) {
                    console.error('Error in generateComputer:', error);
                    utils.showNotification('❌ เกิดข้อผิดพลาดในการสร้างข้อมูล', 'error');
                } finally {
                    utils.hideLoading(button);
                }
            });
        }
        
        const generateComputerBtn = document.getElementById('generateComputerBtn');
        if (generateComputerBtn) {
            generateComputerBtn.addEventListener('click', async () => {
                const button = generateComputerBtn;
                utils.showLoading(button);
                
                try {
                    if (!document.getElementById('first_name_en').value) {
                        utils.showNotification('❌ กรุณากรอกชื่อภาษาอังกฤษก่อน', 'error');
                        return;
                    }
                    
                    autoGenerate.username();
                    await new Promise(resolve => setTimeout(resolve, 200));
                    
                    if (document.getElementById('last_name_en').value) {
                        autoGenerate.email();
                    }
                    
                    document.getElementById('computer_password').value = generators.password();
                    document.getElementById('copier_code').value = generators.copierCode();
                    
                    utils.showNotification('💻 สร้างข้อมูลระบบคอมพิวเตอร์สำเร็จ!', 'success');
                } catch (error) {
                    console.error('Error in generateComputer:', error);
                    utils.showNotification('❌ เกิดข้อผิดพลาดในการสร้างข้อมูล', 'error');
                } finally {
                    utils.hideLoading(button);
                }
            });
        }
        
        // Express generation button
        const generateExpressBtn = document.getElementById('generateExpressBtn');
        if (generateExpressBtn) {
            generateExpressBtn.addEventListener('click', async () => {
                const button = generateExpressBtn;
                utils.showLoading(button);
                
                try {
                    if (!document.getElementById('first_name_en').value) {
                        utils.showNotification('❌ กรุณากรอกชื่อภาษาอังกฤษก่อน', 'error');
                        return;
                    }
                    
                    document.getElementById('express_username').value = generators.expressUsername();
                    document.getElementById('express_password').value = generators.expressPassword();
                    
                    utils.showNotification('⚡ สร้างข้อมูล Express สำเร็จ!', 'success');
                } catch (error) {
                    console.error('Error in generateExpress:', error);
                    utils.showNotification('❌ เกิดข้อผิดพลาดในการสร้างข้อมูล Express', 'error');
                } finally {
                    utils.hideLoading(button);
                }
            });
        }
        
        // Form submit handler
        const employeeForm = document.getElementById('employeeForm');
        if (employeeForm) {
            employeeForm.addEventListener('submit', function(e) {
                const submitBtn = document.getElementById('submitBtn');
                if (submitBtn) {
                    utils.showLoading(submitBtn);
                }
            });
        }
        
        console.log('✅ All event listeners attached successfully');
        
    } catch (error) {
        console.error('❌ Error setting up event listeners:', error);
    }
    
    // Initial setup
    setTimeout(() => {
        console.log('🔧 Starting initial setup...');
        
        try {
            // Auto-generate employee code and keycard if empty
            const employeeCodeEl = document.getElementById('employee_code');
            const keycardIdEl = document.getElementById('keycard_id');
            const copierCodeEl = document.getElementById('copier_code');
            
            if (employeeCodeEl && !employeeCodeEl.value) {
                employeeCodeEl.value = generators.employeeCode();
                console.log('✅ Employee code generated');
            }
            
            if (keycardIdEl && !keycardIdEl.value) {
                keycardIdEl.value = generators.keycardId();
                console.log('✅ Keycard ID generated');
            }
            
            if (copierCodeEl && !copierCodeEl.value) {
                copierCodeEl.value = generators.copierCode();
                console.log('✅ Copier code generated');
            }
            
            // Initialize department change handler
            eventHandlers.handleDepartmentChange();
            
            // Force trigger department change if already selected
            const departmentSelect = document.getElementById('department_id');
            if (departmentSelect && departmentSelect.value) {
                console.log('🔄 Force triggering department change for:', departmentSelect.options[departmentSelect.selectedIndex]?.textContent);
                eventHandlers.handleDepartmentChange();
            }
            
            console.log('✅ Department handler initialized');
            
            // Initialize email preview
            autoGenerate.showEmailPreview();
            console.log('✅ Email preview initialized');
            
            // Debug functions for testing
            window.testExpress = function() {
                const expressSection = document.getElementById('expressSection');
                if (expressSection) {
                    expressSection.style.display = 'block';
                    console.log('✅ Express section manually shown');
                } else {
                    console.log('❌ Express section not found');
                }
            };
            
            window.testDepartmentChange = function() {
                eventHandlers.handleDepartmentChange();
                console.log('✅ Department change handler manually triggered');
            };
            
            window.testGenerateAll = function() {
                formActions.generateAll();
                console.log('✅ Generate all manually triggered');
            };
            
            console.log('🧪 Debug functions available: testExpress(), testDepartmentChange(), testGenerateAll()');
            
            console.log('✅ Employee Create Form Ready - Enhanced with Express Support');
            console.log('📧 Email Format: firstname.lastnameFirstChar@domain');
            console.log('🔄 Available Domains: @bettersystem.co.th, @better-groups.com');
            console.log('👤 Username Format: firstname (lowercase only)');
            console.log('💻 Computer: Username + Password + Copier Code');
            console.log('⚡ Express Support: Username (7 chars) + Password (4 chars) with Button');
            console.log('🎨 Icons: User, Desktop, Email, Building, Bolt, Sign-in (Clean Style)');
            console.log('🚀 All systems ready for employee registration!');
            
        } catch (error) {
            console.error('❌ Error in initial setup:', error);
        }
    }, 1000);
});

// Modal functions
function submitForm() {
    const form = document.getElementById('employeeForm');
    if (form) {
        form.submit();
    }
}
</script>
@endpush