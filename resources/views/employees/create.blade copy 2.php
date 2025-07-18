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
            </div>
            <div class="col-md-3 mb-2">
                <button type="button" class="btn btn-outline-info w-100" id="previewBtn">
                    <i class="fas fa-eye me-1"></i>ดูตัวอย่าง
                </button>
            </div>
            <div class="col-md-3 mb-2">
                <button type="button" class="btn btn-outline-warning w-100" id="clearAllBtn">
                    <i class="fas fa-trash me-1"></i>ล้างทั้งหมด
                </button>
            </div>
            <div class="col-md-3 mb-2">
                <button type="button" class="btn btn-outline-success w-100" id="generateAllComputerBtn">
                    <i class="fas fa-desktop me-1"></i>สร้างระบบคอมฯ
                </button>
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
            <h5 class="card-title mb-0">
                <i class="fas fa-user me-2"></i>ข้อมูลพื้นฐาน
            </h5>
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
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px; font-size: 14px; font-weight: bold;">
                    2
                </div>
                <div>
                    <h5 class="card-title mb-0">ระบบคอมพิวเตอร์</h5>
                    <small class="text-muted">Username และรหัสผ่านสำหรับคอมพิวเตอร์</small>
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-outline-success" id="generateComputerBtn">
                <i class="fas fa-desktop me-1"></i>สร้างระบบคอมฯ
            </button>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <!-- Username -->
                <div class="col-md-6">
                    <label for="username" class="form-label">Username (เปิดคอมพิวเตอร์)</label>
                    <div class="input-group">
                        <input type="text" 
                               class="form-control @error('username') is-invalid @enderror" 
                               id="username" 
                               name="username" 
                               value="{{ old('username') }}"
                               placeholder="สร้างจากชื่ออังกฤษ">
                        <button type="button" class="btn btn-outline-primary" data-target="username">
                            <i class="fas fa-user"></i>
                        </button>
                    </div>
                    <div class="form-text">จะถูกสร้างจากชื่อ.นามสกุล ภาษาอังกฤษ</div>
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
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px; font-size: 14px; font-weight: bold;">
                    3
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
                    </label>
                    <div class="input-group">
                        <input type="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}"
                               placeholder="สร้างจาก Username"
                               required>
                        <select class="form-select" id="email_domain" style="max-width: 200px;">
                            <option value="bettersystem.co.th">@bettersystem.co.th</option>
                            <option value="better-groups.com">@better-groups.com</option>
                        </select>
                        <button type="button" class="btn btn-outline-primary" data-target="email">
                            <i class="fas fa-envelope"></i>
                        </button>
                    </div>
                    <div class="form-text">รูปแบบ: ชื่อ.ตัวแรกของนามสกุล@โดเมน</div>
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

    <!-- โปรแกรม Express (Conditional) -->
    <div class="card mb-4" id="expressSection" style="display: none;">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px; font-size: 14px; font-weight: bold;">
                    4
                </div>
                <div>
                    <h5 class="card-title mb-0">โปรแกรม Express</h5>
                    <small class="text-muted">เฉพาะแผนกบัญชี</small>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="alert alert-info" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                โปรแกรม Express จะถูกลงทะเบียนเฉพาะพนักงานในแผนกบัญชีเท่านั้น
            </div>
            
            <div class="row g-3">
                <!-- Express Username -->
                <div class="col-md-6">
                    <label for="express_username" class="form-label">Username Express (7 ตัวอักษร)</label>
                    <div class="input-group">
                        <input type="text" 
                               class="form-control @error('express_username') is-invalid @enderror" 
                               id="express_username" 
                               name="express_username" 
                               value="{{ old('express_username') }}"
                               placeholder="7 ตัวอักษร" 
                               maxlength="7">
                        <button type="button" class="btn btn-outline-primary" data-target="express_username">
                            <i class="fas fa-bolt"></i>
                        </button>
                    </div>
                    <div class="form-text">สร้างจากชื่ออังกฤษ 7 ตัวอักษร</div>
                    @error('express_username')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Express Code -->
                <div class="col-md-6">
                    <label for="express_code" class="form-label">รหัสโปรแกรม Express</label>
                    <div class="input-group">
                        @if($userRole === 'super_admin' || $userRole === 'it_admin')
                            <input type="password" 
                                   class="form-control @error('express_code') is-invalid @enderror" 
                                   id="express_code" 
                                   name="express_code" 
                                   value="{{ old('express_code') }}"
                                   placeholder="Random 4 หลัก" 
                                   maxlength="4">
                            <button type="button" class="btn btn-outline-primary" data-target="express_code">
                                <i class="fas fa-lock"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary" data-toggle-password="express_code">
                                <i class="fas fa-eye"></i>
                            </button>
                        @else
                            <input type="password" 
                                   class="form-control bg-light" 
                                   id="express_code" 
                                   name="express_code" 
                                   value="{{ old('express_code') }}"
                                   placeholder="สร้างอัตโนมัติ"
                                   readonly>
                            <button type="button" class="btn btn-outline-primary" data-target="express_code">
                                <i class="fas fa-lock"></i>
                            </button>
                        @endif
                    </div>
                    <div class="form-text">
                        รหัส 4 หลักตัวเลขสำหรับโปรแกรม Express
                        @if(!($userRole === 'super_admin' || $userRole === 'it_admin'))
                            <span class="text-warning">(มองไม่เห็น - เฉพาะ Admin)</span>
                        @endif
                    </div>
                    @error('express_code')
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
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px; font-size: 14px; font-weight: bold;">
                    5
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

    <!-- ระบบ Login -->
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px; font-size: 14px; font-weight: bold;">
                    6
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
                                   placeholder="รหัสผ่านเริ่มต้น"
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
    console.log('🚀 Employee Create Form Loaded');
    
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
            const alert = document.createElement('div');
            alert.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
            alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            alert.innerHTML = `
                ${message}
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
            const firstName = document.getElementById('first_name_en').value.trim().toLowerCase();
            const lastName = document.getElementById('last_name_en').value.trim().toLowerCase();
            return firstName && lastName ? `${firstName}.${lastName}` : '';
        },
        email: () => {
            const username = document.getElementById('username').value.trim();
            const domain = document.getElementById('email_domain').value;
            if (username && domain) {
                return `${username}@${domain}`;
            }
            const firstName = document.getElementById('first_name_en').value.trim().toLowerCase();
            const lastName = document.getElementById('last_name_en').value.trim().toLowerCase();
            return firstName && lastName ? `${firstName}.${lastName.charAt(0)}@${domain}` : '';
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
        expressCode: () => utils.generateRandomNumber(4)
    };
    
    // Auto-generation functions
    const autoGenerate = {
        username: () => {
            const username = generators.username();
            if (username) {
                document.getElementById('username').value = username;
                setTimeout(() => autoGenerate.email(), 100);
            }
        },
        email: () => {
            const email = generators.email();
            if (email) {
                document.getElementById('email').value = email;
                document.getElementById('login_email').value = email;
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
                    case 'express_code':
                        value = generators.expressCode();
                        break;
                }
                
                if (value) {
                    targetElement.value = value;
                    
                    if (target === 'email') {
                        document.getElementById('login_email').value = value;
                    }
                    
                    utils.showNotification(`สร้าง ${target} สำเร็จ: ${value}`);
                }
                
            } catch (error) {
                console.error(`Error generating ${target}:`, error);
                utils.showNotification(`เกิดข้อผิดพลาดในการสร้าง ${target}`, 'error');
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
            
            if (departmentSelect && expressSection) {
                const selectedOption = departmentSelect.options[departmentSelect.selectedIndex];
                const departmentName = selectedOption ? selectedOption.textContent.trim() : '';
                
                if (departmentName === 'บัญชี') {
                    expressSection.style.display = 'block';
                } else {
                    expressSection.style.display = 'none';
                    document.getElementById('express_username').value = '';
                    document.getElementById('express_code').value = '';
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
                document.getElementById('employee_code').value = generators.employeeCode();
                document.getElementById('keycard_id').value = generators.keycardId();
                
                if (document.getElementById('first_name_en').value && document.getElementById('last_name_en').value) {
                    autoGenerate.username();
                    await new Promise(resolve => setTimeout(resolve, 200));
                    autoGenerate.email();
                }
                
                document.getElementById('computer_password').value = generators.password();
                document.getElementById('email_password').value = generators.password();
                document.getElementById('password').value = generators.password();
                document.getElementById('copier_code').value = generators.copierCode();
                
                const departmentSelect = document.getElementById('department_id');
                const selectedOption = departmentSelect.options[departmentSelect.selectedIndex];
                if (selectedOption && selectedOption.textContent.trim() === 'บัญชี') {
                    document.getElementById('express_username').value = generators.expressUsername();
                    document.getElementById('express_code').value = generators.expressCode();
                }
                
                utils.showNotification('สร้างข้อมูลทั้งหมดสำเร็จ!', 'success');
                
            } catch (error) {
                console.error('Error in generateAll:', error);
                utils.showNotification('เกิดข้อผิดพลาดในการสร้างข้อมูล', 'error');
            } finally {
                utils.hideLoading(button);
            }
        },
        
        clearAll: () => {
            if (confirm('ต้องการล้างข้อมูลทั้งหมดหรือไม่?')) {
                document.getElementById('employeeForm').reset();
                utils.showNotification('ล้างข้อมูลทั้งหมดแล้ว', 'success');
            }
        },
        
        showPreview: () => {
            const formData = new FormData(document.getElementById('employeeForm'));
            const data = {};
            for (let [key, value] of formData.entries()) {
                data[key] = value;
            }
            
            const previewContent = document.getElementById('previewContent');
            previewContent.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary">ข้อมูลพื้นฐาน</h6>
                        <p><strong>รหัสพนักงาน:</strong> ${data.employee_code || '-'}</p>
                        <p><strong>ชื่อ:</strong> ${data.first_name_th || '-'} ${data.last_name_th || '-'}</p>
                        <p><strong>English:</strong> ${data.first_name_en || '-'} ${data.last_name_en || '-'}</p>
                        <p><strong>เบอร์โทร:</strong> ${data.phone || '-'}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary">ระบบคอมพิวเตอร์</h6>
                        <p><strong>Username:</strong> ${data.username || '-'}</p>
                        <p><strong>Email:</strong> ${data.email || '-'}</p>
                        <p><strong>รหัสถ่ายเอกสาร:</strong> ${data.copier_code || '-'}</p>
                        ${data.express_username ? `<p><strong>Express Username:</strong> ${data.express_username}</p>` : ''}
                    </div>
                </div>
            `;
            
            const modal = new bootstrap.Modal(document.getElementById('previewModal'));
            modal.show();
        }
    };
    
    // Event Listeners
    document.addEventListener('click', eventHandlers.handleMagicClick);
    document.addEventListener('click', eventHandlers.handlePasswordToggle);
    
    document.getElementById('department_id')?.addEventListener('change', eventHandlers.handleDepartmentChange);
    document.getElementById('phone')?.addEventListener('input', eventHandlers.handlePhoneFormat);
    
    document.getElementById('first_name_en')?.addEventListener('blur', () => {
        setTimeout(autoGenerate.username, 300);
    });
    
    document.getElementById('last_name_en')?.addEventListener('blur', () => {
        setTimeout(autoGenerate.username, 300);
    });
    
    document.getElementById('email_domain')?.addEventListener('change', autoGenerate.email);
    
    document.getElementById('email')?.addEventListener('input', (e) => {
        document.getElementById('login_email').value = e.target.value;
    });
    
    document.getElementById('generateAllBtn')?.addEventListener('click', formActions.generateAll);
    document.getElementById('clearAllBtn')?.addEventListener('click', formActions.clearAll);
    document.getElementById('previewBtn')?.addEventListener('click', formActions.showPreview);
    
    document.getElementById('generateComputerBtn')?.addEventListener('click', async () => {
        const button = event.target;
        utils.showLoading(button);
        
        try {
            autoGenerate.username();
            await new Promise(resolve => setTimeout(resolve, 200));
            autoGenerate.email();
            
            document.getElementById('computer_password').value = generators.password();
            document.getElementById('copier_code').value = generators.copierCode();
            
            utils.showNotification('สร้างข้อมูลระบบคอมพิวเตอร์สำเร็จ!');
        } finally {
            utils.hideLoading(button);
        }
    });
    
    document.getElementById('employeeForm')?.addEventListener('submit', function(e) {
        const submitBtn = document.getElementById('submitBtn');
        utils.showLoading(submitBtn);
    });
    
    // Initial setup
    setTimeout(() => {
        if (!document.getElementById('employee_code').value) {
            document.getElementById('employee_code').value = generators.employeeCode();
        }
        
        if (!document.getElementById('keycard_id').value) {
            document.getElementById('keycard_id').value = generators.keycardId();
        }
        
        eventHandlers.handleDepartmentChange();
        
        console.log('✅ Employee Create Form Ready');
    }, 1000);
});

// Modal functions
function submitForm() {
    document.getElementById('employeeForm').submit();
}
</script>
@endpush