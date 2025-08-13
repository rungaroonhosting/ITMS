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
                <p class="text-muted mb-0">กรอกข้อมูลพนักงานใหม่เข้าระบบ (รองรับ Branch + Express v2.0)</p>
                <div class="mt-2">
                    <span class="badge bg-info">
                        <i class="fas fa-info-circle me-1"></i>
                        Email จะถูกสร้างอัตโนมัติจาก ชื่อ.ตัวแรกนามสกุล@โดเมน
                    </span>
                    <span class="badge bg-warning">
                        <i class="fas fa-bolt me-1"></i>
                        Express จะแสดงเฉพาะแผนกที่เปิดใช้งาน
                    </span>
                    <span class="badge bg-success">
                        <i class="fas fa-phone me-1"></i>
                        ✅ เบอร์โทรซ้ำได้แล้ว - แก้ไขเรียบร้อย
                    </span>
                    <span class="badge bg-primary">
                        <i class="fas fa-eye me-1"></i>
                        แสดงรหัสผ่านได้ทั้งหมด
                    </span>
                    <span class="badge" style="background: linear-gradient(45deg, #B54544, #E6952A); color: white;">
                        <i class="fas fa-building me-1"></i>
                        ✨ รองรับเลือกสาขา - ITMS Theme
                    </span>
                </div>
            </div>
            <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>กลับ
            </a>
        </div>
    </div>
</div>

<!-- Success Alert for Phone Fix -->
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <h6 class="fw-bold"><i class="fas fa-check-circle me-2"></i>ระบบได้รับการอัปเดต! (รองรับ Branch System แล้ว)</h6>
    <div class="row">
        <div class="col-md-4">
            <ul class="mb-0">
                <li><strong>✅ เบอร์โทรซ้ำได้:</strong> สามารถใช้เบอร์เดียวกันได้หลายคน</li>
                <li><strong>👨‍👩‍👧‍👦 เหมาะสำหรับ:</strong> ครอบครัว, เพื่อนร่วมงาน</li>
            </ul>
        </div>
        <div class="col-md-4">
            <ul class="mb-0">
                <li><strong>🔒 ความปลอดภัย:</strong> Email, Username ยังคง unique</li>
                <li><strong>⚡ Express v2.0:</strong> ทำงานปกติ ไม่กระทบ</li>
            </ul>
        </div>
        <div class="col-md-4">
            <ul class="mb-0">
                <li><strong>🏢 Branch System:</strong> เลือกสาขาได้</li>
                <li><strong>🎨 ITMS Theme:</strong> ธีมสีแดง-ส้ม</li>
            </ul>
        </div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>

<!-- Quick Actions -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row text-center g-3">
            <div class="col-md-3 col-sm-6">
                <div class="d-flex flex-column h-100">
                    <button type="button" class="btn btn-outline-primary w-100 flex-fill d-flex align-items-center justify-content-center" id="generateAllBtn" style="min-height: 45px;">
                        <span class="d-flex align-items-center">
                            <i class="fas fa-magic me-2"></i>
                            <span class="d-none d-lg-inline">สร้างทั้งหมดอัตโนมัติ</span>
                            <span class="d-lg-none">สร้างทั้งหมด</span>
                        </span>
                    </button>
                    <div class="form-text mt-2">
                        <small class="text-muted">ต้องกรอกชื่อ-นามสกุล EN ก่อน</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="d-flex flex-column h-100">
                    <button type="button" class="btn btn-outline-info w-100 flex-fill d-flex align-items-center justify-content-center" id="previewBtn" style="min-height: 45px;">
                        <span class="d-flex align-items-center">
                            <i class="fas fa-eye me-2"></i>
                            <span>ดูตัวอย่าง</span>
                        </span>
                    </button>
                    <div class="form-text mt-2">
                        <small class="text-muted">ดูก่อนบันทึก</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="d-flex flex-column h-100">
                    <button type="button" class="btn btn-outline-warning w-100 flex-fill d-flex align-items-center justify-content-center" id="clearAllBtn" style="min-height: 45px;">
                        <span class="d-flex align-items-center">
                            <i class="fas fa-trash me-2"></i>
                            <span>ล้างทั้งหมด</span>
                        </span>
                    </button>
                    <div class="form-text mt-2">
                        <small class="text-muted">เริ่มใหม่</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="d-flex flex-column h-100">
                    <button type="button" class="btn w-100 flex-fill d-flex align-items-center justify-content-center" id="testBranchBtn" style="min-height: 45px; background: linear-gradient(45deg, #B54544, #E6952A); color: white; border: none;">
                        <span class="d-flex align-items-center">
                            <i class="fas fa-building me-2"></i>
                            <span class="d-none d-lg-inline">ทดสอบ Branch</span>
                            <span class="d-lg-none">Branch</span>
                        </span>
                    </button>
                    <div class="form-text mt-2">
                        <small class="text-success">✅ Branch System Ready</small>
                    </div>
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
            <div class="mt-2">
                <small class="text-info">
                    <i class="fas fa-info-circle me-1"></i>
                    <strong>หมายเหตุ:</strong> ถ้าเป็นปัญหาเบอร์โทรซ้ำ ระบบได้แก้ไขแล้ว - ลองบันทึกใหม่
                </small>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    <!-- ข้อมูลพื้นฐาน -->
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <div class="border border-2 border-primary rounded-circle d-flex align-items-center justify-content-center me-3 bg-light" style="width: 45px; height: 45px; min-width: 45px;">
                    <i class="fas fa-user text-primary" style="font-size: 20px;"></i>
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
                            ใช้เฉพาะตัวอักษร A-Z เท่านั้น (สำหรับสร้าง Username และ Express)
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
                
                <!-- เบอร์โทร (✅ FIXED - อนุญาติซ้ำได้แล้ว) -->
                <div class="col-md-6">
                    <label for="phone" class="form-label">
                        เบอร์โทรศัพท์ <span class="text-danger">*</span>
                        <span class="badge bg-success ms-2">✅ ซ้ำได้แล้ว</span>
                    </label>
                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                           id="phone" name="phone" value="{{ old('phone') }}" 
                           placeholder="08x-xxx-xxxx" required>
                    <div class="form-text">
                        <div class="alert alert-success p-2 mt-2 mb-0">
                            <small>
                                <i class="fas fa-check-circle me-1"></i>
                                <strong>✅ แก้ไขแล้ว:</strong> สามารถใช้เบอร์โทรที่ซ้ำกันได้
                            </small>
                            <br>
                            <small class="text-muted">
                                <i class="fas fa-users me-1"></i>
                                เหมาะสำหรับ: ครอบครัว, เพื่อนร่วมงาน, เบอร์ออฟฟิศ, เบอร์บ้าน
                            </small>
                            <br>
                            <small class="text-info">
                                <i class="fas fa-shield-alt me-1"></i>
                                Email และ Username ยังคง unique (ปลอดภัย)
                            </small>
                        </div>
                    </div>
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
                <div class="border border-2 border-success rounded-circle d-flex align-items-center justify-content-center me-3 bg-light" style="width: 45px; height: 45px; min-width: 45px;">
                    <i class="fas fa-desktop text-success" style="font-size: 20px;"></i>
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

                <!-- Computer Password (แสดงได้ทั้งหมด) -->
                <div class="col-md-6">
                    <label for="computer_password" class="form-label">
                        Password (เปิดคอมพิวเตอร์)
                        <span class="badge bg-primary ms-2">แสดงได้</span>
                    </label>
                    <div class="input-group">
                        <input type="text" 
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
                    </div>
                    <div class="form-text">
                        รหัสผ่านสำหรับเปิดคอมพิวเตอร์ 
                        <span class="text-success">(แสดงให้เห็นได้ทั้งหมด)</span>
                    </div>
                    @error('computer_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Copier Code (On Demand) -->
                <div class="col-md-6">
                    <label for="copier_code" class="form-label">
                        รหัสเครื่องถ่ายเอกสาร
                        <span class="badge bg-info ms-2">On Demand</span>
                    </label>
                    <div class="input-group">
                        <input type="text" 
                               class="form-control @error('copier_code') is-invalid @enderror" 
                               id="copier_code" 
                               name="copier_code" 
                               value="{{ old('copier_code') }}"
                               placeholder="กดปุ่มเพื่อสร้าง" 
                               maxlength="4">
                        <button type="button" class="btn btn-outline-primary" data-target="copier_code">
                            <i class="fas fa-print"></i>
                        </button>
                    </div>
                    <div class="form-text">
                        <small class="text-muted">
                            <i class="fas fa-mouse-pointer me-1"></i>
                            รหัส 4 หลักตัวเลข - กดปุ่มเมื่อต้องการสร้าง
                        </small>
                    </div>
                    @error('copier_code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- ระบบอีเมลและ Login (แยกแล้ว) -->
<div class="card mb-4">
    <div class="card-header">
        <div class="d-flex align-items-center">
            <div class="border border-2 border-info rounded-circle d-flex align-items-center justify-content-center me-3 bg-light" style="width: 45px; height: 45px; min-width: 45px;">
                <i class="fas fa-envelope text-info" style="font-size: 20px;"></i>
            </div>
            <div>
                <h5 class="card-title mb-0">ระบบอีเมลและ Login</h5>
                <small class="text-muted">อีเมลและรหัสผ่าน แยกระบบแล้ว (รหัสผ่านต่างกัน)</small>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="alert alert-success">
            <i class="fas fa-check-circle me-2"></i>
            <strong>ปรับปรุงแล้ว:</strong> แยกรหัสผ่าน Email และ Login เป็นคนละรหัส (ปลอดภัยกว่า) และแสดงให้เห็นได้ทั้งหมด
        </div>
        
        <div class="row g-3">
            <!-- Email System -->
            <div class="col-md-12">
                <h6 class="text-info mb-3">
                    <i class="fas fa-envelope me-2"></i>ระบบอีเมล
                    <span class="badge bg-info ms-2">Email System</span>
                </h6>
            </div>
            
            <!-- Email Address -->
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
                </div>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Email Password (แยกแล้ว) -->
            <div class="col-md-4">
                <label for="email_password" class="form-label">
                    Password อีเมล
                    <span class="badge bg-warning text-dark ms-2">Email Only</span>
                </label>
                <div class="input-group">
                    <input type="text" 
                           class="form-control @error('email_password') is-invalid @enderror" 
                           id="email_password" 
                           name="email_password" 
                           value="{{ old('email_password') }}"
                           placeholder="รหัสผ่านอีเมล (10 ตัว)">
                    <button type="button" class="btn btn-outline-primary" data-target="email_password">
                        <i class="fas fa-mail-bulk"></i>
                    </button>
                    <button type="button" class="btn btn-outline-secondary" data-toggle-password="email_password">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div class="form-text">
                    <span class="text-warning">
                        <i class="fas fa-envelope me-1"></i>
                        เฉพาะระบบอีเมล (ไม่ใช่ Login ระบบ)
                    </span>
                </div>
                @error('email_password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Separator -->
            <div class="col-md-12">
                <hr class="my-3">
                <h6 class="text-success mb-3">
                    <i class="fas fa-sign-in-alt me-2"></i>ระบบเข้าสู่ระบบ
                    <span class="badge bg-success ms-2">Login System</span>
                </h6>
            </div>

            <!-- Login Email (Auto-sync จาก email) -->
            <div class="col-md-8">
                <label for="login_email" class="form-label">
                    อีเมลเข้าระบบ
                    <span class="badge bg-secondary ms-2">Auto Sync</span>
                </label>
                <div class="input-group">
                    <input type="email" 
                           class="form-control" 
                           id="login_email" 
                           name="login_email" 
                           value="{{ old('login_email') }}"
                           placeholder="จะ sync จากอีเมลด้านบน"
                           readonly>
                    <button type="button" class="btn btn-outline-secondary" disabled>
                        <i class="fas fa-link"></i>
                    </button>
                </div>
                <div class="form-text">
                    <span class="text-info">
                        <i class="fas fa-sync me-1"></i>
                        จะถูก sync จากอีเมลด้านบนอัตโนมัติ
                    </span>
                </div>
            </div>

            <!-- Login Password (แยกแล้ว) -->
            <div class="col-md-4">
                <label for="login_password" class="form-label">
                    Password เข้าระบบ <span class="text-danger">*</span>
                    <span class="badge bg-success ms-2">Login Only</span>
                </label>
                <div class="input-group">
                    <input type="text" 
                           class="form-control @error('password') is-invalid @enderror" 
                           id="login_password" 
                           name="login_password" 
                           value="{{ old('login_password') }}"
                           placeholder="รหัสผ่านเข้าระบบ (12 ตัว)"
                           required>
                    <button type="button" class="btn btn-outline-primary" data-target="login_password">
                        <i class="fas fa-key"></i>
                    </button>
                    <button type="button" class="btn btn-outline-secondary" data-toggle-password="login_password">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div class="form-text">
                    <span class="text-success">
                        <i class="fas fa-shield-alt me-1"></i>
                        เฉพาะเข้าสู่ระบบ (ไม่เกี่ยวกับอีเมล)
                    </span>
                </div>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Hidden fields for backend compatibility -->
            <input type="hidden" id="password" name="password" value="{{ old('password') }}">
        </div>

        <!-- Summary Card -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-primary">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>สรุปการแยกระบบ
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-warning">
                                    <i class="fas fa-envelope me-2"></i>ระบบอีเมล
                                </h6>
                                <ul class="list-unstyled">
                                    <li><strong>อีเมล:</strong> <span id="summaryEmail" class="text-muted">-</span></li>
                                    <li><strong>รหัสผ่าน:</strong> <span id="summaryEmailPassword" class="text-muted">-</span></li>
                                    <li><strong>ใช้สำหรับ:</strong> <span class="text-info">ระบบอีเมลเท่านั้น</span></li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-success">
                                    <i class="fas fa-sign-in-alt me-2"></i>ระบบเข้าสู่ระบบ
                                </h6>
                                <ul class="list-unstyled">
                                    <li><strong>อีเมล:</strong> <span id="summaryLoginEmail" class="text-muted">-</span></li>
                                    <li><strong>รหัสผ่าน:</strong> <span id="summaryLoginPassword" class="text-muted">-</span></li>
                                    <li><strong>ใช้สำหรับ:</strong> <span class="text-success">เข้าสู่ระบบเท่านั้น</span></li>
                                </ul>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-info mb-0">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6><i class="fas fa-check-circle me-1"></i> ข้อดี:</h6>
                                            <ul class="mb-0">
                                                <li>🛡️ <strong>ปลอดภัยกว่า:</strong> รหัสผ่านแยกกัน</li>
                                                <li>🔒 <strong>การจัดการ:</strong> เปลี่ยนรหัสแยกได้</li>
                                                <li>👁️ <strong>แสดงได้:</strong> ดูรหัสผ่านทั้งหมด</li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <h6><i class="fas fa-users me-1"></i> สำหรับใคร:</h6>
                                            <ul class="mb-0">
                                                <li>👔 <strong>Admin:</strong> จัดการรหัสผ่านแยก</li>
                                                <li>👤 <strong>พนักงาน:</strong> ใช้รหัสต่างกัน</li>
                                                <li>🔧 <strong>IT:</strong> ดูแลระบบง่ายขึ้น</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- แผนก, สาขา และสิทธิ์ (✅ เพิ่ม Branch Selection) -->
    <div class="card mb-4">
        <div class="card-header" style="background: linear-gradient(45deg, #B54544, #E6952A); color: white;">
            <div class="d-flex align-items-center">
                <div class="border border-2 border-white rounded-circle d-flex align-items-center justify-content-center me-3 bg-light" style="width: 45px; height: 45px; min-width: 45px;">
                    <i class="fas fa-building" style="font-size: 20px; color: #B54544;"></i>
                </div>
                <div>
                    <h5 class="card-title mb-0" style="color: white; text-shadow: 1px 1px 2px rgba(0,0,0,0.3);">
                        แผนก, สาขา และสิทธิ์
                        <span class="badge bg-light text-dark ms-2">
                            <i class="fas fa-plus-circle me-1"></i>เพิ่ม Branch
                        </span>
                    </h5>
                    <small style="color: rgba(255,255,255,0.9); text-shadow: 1px 1px 2px rgba(0,0,0,0.3);">
                        แผนกการทำงาน, สาขาที่สังกัด และสิทธิ์การใช้งาน
                    </small>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <!-- ✅ NEW: Branch Selection -->
                <div class="col-md-6">
                    <label for="branch_id" class="form-label">
                        สาขาที่สังกัด
                        <span class="badge bg-gradient text-white ms-2" style="background: linear-gradient(45deg, #B54544, #E6952A);">
                            <i class="fas fa-building me-1"></i>Branch System
                        </span>
                    </label>
                    <div class="input-group">
                        <select class="form-select @error('branch_id') is-invalid @enderror" 
                                id="branch_id" 
                                name="branch_id">
                            <option value="">เลือกสาขา (ไม่บังคับ)</option>
                            <!-- Options will be loaded via JavaScript -->
                        </select>
                        <button type="button" class="btn btn-outline-info" id="refreshBranchBtn" title="รีเฟรชรายการสาขา">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                        <button type="button" class="btn" style="background: linear-gradient(45deg, #B54544, #E6952A); color: white; border: none;" id="branchInfoBtn" title="ดูข้อมูลสาขา">
                            <i class="fas fa-info-circle"></i>
                        </button>
                    </div>
                    <div class="form-text">
                        <div id="branchInfo" style="display: none;" class="mt-2">
                            <div class="alert alert-info p-2 mb-0">
                                <small>
                                    <strong>ข้อมูลสาขา:</strong>
                                    <div id="branchDetails"></div>
                                </small>
                            </div>
                        </div>
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            เลือกสาขาที่พนักงานสังกัด (ไม่บังคับ - สามารถเว้นว่างได้)
                        </small>
                    </div>
                    @error('branch_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Department -->
                <div class="col-md-6">
                    <label for="department_id" class="form-label">
                        แผนกการทำงาน <span class="text-danger">*</span>
                        <span class="badge bg-warning text-dark ms-2" id="expressIndicator" style="display: none;">
                            <i class="fas fa-bolt me-1"></i>Express Ready
                        </span>
                    </label>
                    <select class="form-select @error('department_id') is-invalid @enderror" 
                            id="department_id" 
                            name="department_id" 
                            required>
                        <option value="">เลือกแผนก</option>
                        @php
                            // ใช้ departments ที่ส่งมาจาก controller หรือ fallback
                            if (isset($departments) && is_object($departments)) {
                                $deptCollection = $departments;
                            } elseif (isset($departments) && is_array($departments)) {
                                $deptCollection = collect($departments);
                            } else {
                                // Fallback departments
                                $deptCollection = collect([
                                    (object)['id' => 1, 'name' => 'บัญชี', 'express_enabled' => true],
                                    (object)['id' => 2, 'name' => 'IT', 'express_enabled' => false],
                                    (object)['id' => 3, 'name' => 'ฝ่ายขาย', 'express_enabled' => false],
                                    (object)['id' => 4, 'name' => 'การตลาด', 'express_enabled' => false],
                                    (object)['id' => 5, 'name' => 'บุคคล', 'express_enabled' => false],
                                    (object)['id' => 6, 'name' => 'ผลิต', 'express_enabled' => false],
                                    (object)['id' => 7, 'name' => 'คลังสินค้า', 'express_enabled' => false],
                                    (object)['id' => 8, 'name' => 'บริหาร', 'express_enabled' => false],
                                ]);
                            }
                        @endphp
                        
                        @if($userRole === 'express')
                            @foreach($deptCollection->where('express_enabled', true) as $department)
                                <option value="{{ $department->id }}" 
                                        data-express="{{ $department->express_enabled ?? false ? 'true' : 'false' }}"
                                        {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                    @if($department->express_enabled ?? false)
                                        (Express)
                                    @endif
                                </option>
                            @endforeach
                        @else
                            @foreach($deptCollection as $department)
                                <option value="{{ $department->id }}" 
                                        data-express="{{ $department->express_enabled ?? false ? 'true' : 'false' }}"
                                        {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                    @if($department->express_enabled ?? false)
                                        (Express)
                                    @endif
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('department_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @if($userRole === 'express')
                        <div class="form-text text-info">Express: สามารถเลือกเฉพาะแผนกที่รองรับ Express</div>
                    @elseif($userRole === 'super_admin')
                        <div class="form-text text-success">
                            <i class="fas fa-plus-circle me-1"></i>
                            SuperAdmin: สามารถจัดการ Express ของแผนกได้ใน
                            <a href="#" target="_blank">หน้าจัดการแผนก</a>
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
                            <option value="express" {{ old('role') == 'express' ? 'selected' : '' }}>Express User</option>
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

                <!-- ✅ Branch & Department Summary -->
                <div class="col-md-12">
                    <div class="alert alert-light border" style="border-color: #B54544 !important;">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-primary">
                                    <i class="fas fa-building me-2"></i>สรุปข้อมูลสาขา
                                </h6>
                                <div id="selectedBranchSummary">
                                    <span class="text-muted">ยังไม่ได้เลือกสาขา</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-warning">
                                    <i class="fas fa-users me-2"></i>สรุปข้อมูลแผนก
                                </h6>
                                <div id="selectedDepartmentSummary">
                                    <span class="text-muted">ยังไม่ได้เลือกแผนก</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- โปรแกรม Express (Dynamic v2.0 Enhanced) -->
    <div class="card mb-4" id="expressSection" style="display: none;">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <div class="border border-2 border-danger rounded-circle d-flex align-items-center justify-content-center me-3 bg-light" style="width: 45px; height: 45px; min-width: 45px;">
                    <i class="fas fa-bolt text-danger" style="font-size: 20px;"></i>
                </div>
                <div>
                    <h5 class="card-title mb-0">
                        โปรแกรม Express v2.0
                        <span class="badge bg-warning text-dark ms-2">Enhanced</span>
                    </h5>
                    <small class="text-muted">รองรับแผนกที่เปิดใช้งาน Express - ปรับแต่งใหม่</small>
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-outline-warning" id="generateExpressBtn">
                <i class="fas fa-bolt me-1"></i>สร้าง Express
            </button>
        </div>
        <div class="card-body">
            <div class="alert alert-success" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle me-2"></i>
                    <div>
                        <strong>Express v2.0 Enhanced:</strong> ระบบได้รับการปรับปรุง
                        <div class="mt-2">
                            <span class="badge bg-success me-1">
                                <i class="fas fa-user me-1"></i>Username: 1-7 ตัวอักษร
                            </span>
                            <span class="badge bg-info me-1">
                                <i class="fas fa-lock me-1"></i>Password: 4 ตัวเลข (ไม่ซ้ำ)
                            </span>
                            <span class="badge bg-primary me-1">
                                <i class="fas fa-eye me-1"></i>แสดงได้ทั้งหมด
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row g-3">
                <!-- Express Username (Enhanced: 1-7 ตัวอักษร) -->
                <div class="col-md-6">
                    <label for="express_username" class="form-label">
                        Username Express (1-7 ตัวอักษร)
                        <span class="badge bg-warning text-dark ms-2">Enhanced</span>
                    </label>
                    <div class="input-group">
                        <input type="text" 
                               class="form-control @error('express_username') is-invalid @enderror" 
                               id="express_username" 
                               name="express_username" 
                               value="{{ old('express_username') }}"
                               placeholder="จะสร้างจากชื่อ EN (1-7 ตัว)" 
                               maxlength="7">
                        <button type="button" class="btn btn-outline-primary" data-target="express_username">
                            <i class="fas fa-bolt"></i>
                        </button>
                    </div>
                    <div class="form-text">
                        <strong class="text-success">ปรับปรุงใหม่:</strong> ใช้ชื่อภาษาอังกฤษได้ 1-7 ตัวอักษร (ไม่ต้อง pad)
                        <div class="mt-1">
                            <small class="text-muted">
                                <i class="fas fa-lightbulb me-1"></i>
                                ตัวอย่าง: "John" → "john", "Alexandra" → "alexand"
                            </small>
                        </div>
                    </div>
                    @error('express_username')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Express Password (Enhanced: 4 ตัวเลขไม่ซ้ำ) -->
                <div class="col-md-6">
                    <label for="express_password" class="form-label">
                        Password โปรแกรม Express
                        <span class="badge bg-success ms-2">Numbers Only</span>
                        <span class="badge bg-primary ms-2">แสดงได้</span>
                    </label>
                    <div class="input-group">
                        <input type="text" 
                               class="form-control @error('express_password') is-invalid @enderror" 
                               id="express_password" 
                               name="express_password" 
                               value="{{ old('express_password') }}"
                               placeholder="4 ตัวเลขไม่ซ้ำ" 
                               maxlength="4"
                               pattern="[0-9]{4}">
                        <button type="button" class="btn btn-outline-primary" data-target="express_password">
                            <i class="fas fa-lock"></i>
                        </button>
                        <button type="button" class="btn btn-outline-secondary" data-toggle-password="express_password">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="form-text">
                        <strong class="text-success">ปรับปรุงใหม่:</strong> 4 ตัวเลขที่ไม่ซ้ำกัน (เช่น 1234, 5678) 
                        <span class="text-success">(แสดงให้เห็นได้ทั้งหมด)</span>
                        <div class="mt-1">
                            <small class="text-info">
                                <i class="fas fa-calculator me-1"></i>
                                ตัวอย่าง: 1357, 2468, 1029 (ไม่ซ้ำเลข)
                            </small>
                        </div>
                    </div>
                    @error('express_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- สิทธิ์พิเศษ -->
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <div class="border border-2 border-secondary rounded-circle d-flex align-items-center justify-content-center me-3 bg-light" style="width: 45px; height: 45px; min-width: 45px;">
                    <i class="fas fa-shield-alt text-secondary" style="font-size: 20px;"></i>
                </div>
                <div>
                    <h5 class="card-title mb-0">สิทธิ์พิเศษ</h5>
                    <small class="text-muted">การอนุญาตใช้งานเพิ่มเติม</small>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <!-- VPN Permission -->
                <div class="col-md-6">
                    <div class="card border-primary">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="fas fa-shield-alt text-primary fa-2x"></i>
                            </div>
                            <h6 class="card-title">การใช้งาน VPN</h6>
                            <div class="form-check form-switch d-flex justify-content-center">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="vpn_access" 
                                       name="vpn_access" 
                                       value="1"
                                       {{ old('vpn_access') ? 'checked' : '' }}>
                                <label class="form-check-label ms-2" for="vpn_access">
                                    <span id="vpnStatus">{{ old('vpn_access') ? 'อนุญาต' : 'ไม่อนุญาต' }}</span>
                                </label>
                            </div>
                            <small class="text-muted">
                                อนุญาตให้เชื่อมต่อ VPN เพื่อทำงานจากที่บ้าน
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Color Printing Permission -->
                <div class="col-md-6">
                    <div class="card border-warning">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="fas fa-palette text-warning fa-2x"></i>
                            </div>
                            <h6 class="card-title">การปริ้นสี</h6>
                            <div class="form-check form-switch d-flex justify-content-center">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="color_printing" 
                                       name="color_printing" 
                                       value="1"
                                       {{ old('color_printing') ? 'checked' : '' }}>
                                <label class="form-check-label ms-2" for="color_printing">
                                    <span id="printingStatus">{{ old('color_printing') ? 'อนุญาต' : 'ไม่อนุญาต' }}</span>
                                </label>
                            </div>
                            <small class="text-muted">
                                อนุญาตให้ใช้เครื่องพิมพ์สีในการพิมพ์เอกสาร
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mt-3">
                <div class="col-12">
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>หมายเหตุ:</strong> สิทธิ์พิเศษเหล่านี้สามารถปรับเปลี่ยนได้ภายหลังโดย Admin
                    </div>
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
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalLabel">
                    <i class="fas fa-eye me-2"></i>ตัวอย่างข้อมูลพนักงาน (รองรับ Branch System)
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

<!-- ✅ NEW: Branch Test Modal -->
<div class="modal fade" id="branchTestModal" tabindex="-1" aria-labelledby="branchTestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(45deg, #B54544, #E6952A); color: white;">
                <h5 class="modal-title" id="branchTestModalLabel">
                    <i class="fas fa-building me-2"></i>ทดสอบ Branch System ✅
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-success">
                    <h6><i class="fas fa-check-circle me-2"></i>Branch Management System พร้อมใช้งาน!</h6>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <h6>✅ ฟีเจอร์ที่พร้อม:</h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success me-2"></i>เลือกสาขาได้</li>
                            <li><i class="fas fa-check text-success me-2"></i>ดูข้อมูลสาขา</li>
                            <li><i class="fas fa-check text-success me-2"></i>รีเฟรชรายการ</li>
                            <li><i class="fas fa-check text-success me-2"></i>ITMS Theme Integration</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>🎯 การทำงาน:</h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-building text-info me-2"></i>โหลดสาขาจาก API</li>
                            <li><i class="fas fa-sync text-warning me-2"></i>รีเฟรชแบบ Real-time</li>
                            <li><i class="fas fa-info-circle text-primary me-2"></i>แสดงข้อมูลสาขา</li>
                            <li><i class="fas fa-mobile text-success me-2"></i>Responsive Design</li>
                        </ul>
                    </div>
                </div>
                
                <hr>
                
                <div class="row">
                    <div class="col-12">
                        <h6>🏢 Branch Statistics:</h6>
                        <div id="branchStats" class="row text-center">
                            <div class="col-md-3">
                                <div class="card border-primary">
                                    <div class="card-body">
                                        <h5 class="text-primary" id="totalBranches">-</h5>
                                        <small>สาขาทั้งหมด</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-success">
                                    <div class="card-body">
                                        <h5 class="text-success" id="activeBranches">-</h5>
                                        <small>สาขาที่เปิด</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-info">
                                    <div class="card-body">
                                        <h5 class="text-info" id="branchesWithManager">-</h5>
                                        <small>มี Manager</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-warning">
                                    <div class="card-body">
                                        <h5 class="text-warning" id="employeesInBranches">-</h5>
                                        <small>พนักงานในสาขา</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-3">
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-lightbulb me-2"></i>
                        <strong>วิธีใช้:</strong> เลือกสาขาจาก dropdown ด้านบน - ระบบจะแสดงข้อมูลสาขาอัตโนมัติ
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-bs-dismiss="modal">
                    <i class="fas fa-thumbs-up me-1"></i>เข้าใจแล้ว
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Employee Create Form Loaded - Branch System + ITMS Theme');
    
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
        
        // Enhanced: สร้างเลข 4 หลักไม่ซ้ำกัน
        generateUniqueNumbers: (length = 4) => {
            const digits = [];
            while (digits.length < length) {
                const digit = Math.floor(Math.random() * 10);
                if (!digits.includes(digit)) {
                    digits.push(digit);
                }
            }
            return digits.join('');
        },
        
        showNotification: (message, type = 'success') => {
            const alertClass = type === 'success' ? 'alert-success' : 
                              type === 'error' ? 'alert-danger' : 
                              type === 'warning' ? 'alert-warning' : 'alert-info';
            const iconClass = type === 'success' ? 'fa-check-circle' : 
                             type === 'error' ? 'fa-exclamation-triangle' :
                             type === 'warning' ? 'fa-exclamation-circle' : 'fa-info-circle';
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
    
    // ✅ NEW: Branch Management Functions
    const branchManager = {
        // Load branches from API
        loadBranches: async () => {
            try {
                const response = await fetch('/api/branches/active');
                const branches = await response.json();
                
                const branchSelect = document.getElementById('branch_id');
                
                // Clear existing options (except first one)
                branchSelect.innerHTML = '<option value="">เลือกสาขา (ไม่บังคับ)</option>';
                
                // Add branch options
                branches.forEach(branch => {
                    const option = document.createElement('option');
                    option.value = branch.id;
                    option.textContent = branch.text;
                    option.dataset.name = branch.name;
                    option.dataset.code = branch.code;
                    branchSelect.appendChild(option);
                });
                
                console.log(`✅ Loaded ${branches.length} branches`);
                return branches;
                
            } catch (error) {
                console.error('❌ Error loading branches:', error);
                utils.showNotification('❌ ไม่สามารถโหลดข้อมูลสาขาได้', 'error');
                return [];
            }
        },
        
        // Get branch info
        getBranchInfo: async (branchId) => {
            if (!branchId) return null;
            
            try {
                const response = await fetch(`/api/branches/${branchId}/info`);
                const branchInfo = await response.json();
                
                return branchInfo;
                
            } catch (error) {
                console.error('❌ Error getting branch info:', error);
                return null;
            }
        },
        
        // Update branch info display
        updateBranchInfo: (branchInfo) => {
            const branchInfoDiv = document.getElementById('branchInfo');
            const branchDetailsDiv = document.getElementById('branchDetails');
            const selectedBranchSummary = document.getElementById('selectedBranchSummary');
            
            if (branchInfo) {
                branchDetailsDiv.innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <strong>ชื่อสาขา:</strong> ${branchInfo.name}<br>
                            <strong>รหัสสาขา:</strong> ${branchInfo.code}
                        </div>
                        <div class="col-md-6">
                            <strong>พนักงานปัจจุบัน:</strong> ${branchInfo.current_employees} คน<br>
                            <strong>ผู้จัดการ:</strong> ${branchInfo.manager ? branchInfo.manager.name : 'ไม่มี'}
                        </div>
                    </div>
                `;
                branchInfoDiv.style.display = 'block';
                
                selectedBranchSummary.innerHTML = `
                    <span class="badge" style="background: linear-gradient(45deg, #B54544, #E6952A); color: white;">
                        <i class="fas fa-building me-1"></i>${branchInfo.name} (${branchInfo.code})
                    </span>
                    <small class="text-muted ms-2">${branchInfo.current_employees} พนักงาน</small>
                `;
            } else {
                branchInfoDiv.style.display = 'none';
                selectedBranchSummary.innerHTML = '<span class="text-muted">ยังไม่ได้เลือกสาขา</span>';
            }
        },
        
        // Load branch statistics
        loadBranchStats: async () => {
            try {
                const response = await fetch('/api/branches/statistics');
                const data = await response.json();
                
                if (data.success) {
                    const stats = data.statistics;
                    
                    document.getElementById('totalBranches').textContent = stats.total_branches || 0;
                    document.getElementById('activeBranches').textContent = stats.active_branches || 0;
                    document.getElementById('branchesWithManager').textContent = stats.branches_with_manager || 0;
                    document.getElementById('employeesInBranches').textContent = stats.total_employees_in_branches || 0;
                }
                
            } catch (error) {
                console.error('❌ Error loading branch statistics:', error);
            }
        }
    };
    
    // Enhanced Generator Functions
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
        password: () => utils.generateRandomString(12, true),
        copierCode: () => utils.generateRandomNumber(4),
        
        // Enhanced Express Username: 1-7 ตัวอักษร
        expressUsername: () => {
            const firstName = document.getElementById('first_name_en').value.trim().toLowerCase();
            if (firstName.length > 0) {
                return firstName.length <= 7 ? firstName : firstName.substring(0, 7);
            }
            return utils.generateRandomString(5, false).toLowerCase();
        },
        
        // Enhanced Express Password: 4 ตัวเลขไม่ซ้ำกัน
        expressPassword: () => utils.generateUniqueNumbers(4),
        
        // ✅ NEW: Phone number generator (duplicates allowed)
        phoneNumber: () => {
            const prefixes = ['08', '09', '06', '02'];
            const prefix = prefixes[Math.floor(Math.random() * prefixes.length)];
            const middle = utils.generateRandomNumber(3);
            const last = utils.generateRandomNumber(4);
            return `${prefix}${middle}-${last}`;
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
                        value = utils.generateRandomString(10, true);
                        break;
                    case 'login_password':
                        value = generators.password();  // 12 ตัวอักษร
                        // Auto-sync to hidden password field
                        const passwordField = document.getElementById('password');
                        if (passwordField) {
                            passwordField.value = value;
                        }
                        break;
                    case 'email_password':
                        value = utils.generateRandomString(10, true);  // 10 ตัวอักษร
                        break;
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
                        case 'computer_password':
                            message = `✅ สร้างรหัสผ่านคอมพิวเตอร์สำเร็จ (10 ตัวอักษร)`;
                            break;
                        case 'login_password':
                            message = `✅ สร้างรหัสผ่านเข้าระบบสำเร็จ (12 ตัวอักษร)`;
                            break;
                        case 'email_password':
                            message = `✅ สร้างรหัสผ่านอีเมลสำเร็จ (10 ตัวอักษร)`;
                            break;
                        case 'password':
                            message = `✅ สร้างรหัสผ่านสำเร็จ (12 ตัวอักษร)`;
                            break;
                        case 'express_username':
                            message = `✅ สร้าง Express Username สำเร็จ: ${value} (${value.length} ตัวอักษร)`;
                            break;
                        case 'express_password':
                            message = `✅ สร้างรหัส Express สำเร็จ: ${value} (4 ตัวเลขไม่ซ้ำ)`;
                            break;
                        case 'copier_code':
                            message = `✅ สร้างรหัสถ่ายเอกสารสำเร็จ: ${value}`;
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
        
        // Department Change Handler - Express v2.0
        handleDepartmentChange: () => {
            const departmentSelect = document.getElementById('department_id');
            const expressSection = document.getElementById('expressSection');
            const expressIndicator = document.getElementById('expressIndicator');
            const selectedDepartmentSummary = document.getElementById('selectedDepartmentSummary');
            
            if (!departmentSelect || !expressSection) return;
            
            const selectedOption = departmentSelect.options[departmentSelect.selectedIndex];
            if (!selectedOption || !selectedOption.value) {
                expressSection.style.display = 'none';
                if (expressIndicator) expressIndicator.style.display = 'none';
                if (selectedDepartmentSummary) {
                    selectedDepartmentSummary.innerHTML = '<span class="text-muted">ยังไม่ได้เลือกแผนก</span>';
                }
                return;
            }
            
            const expressEnabled = selectedOption.dataset.express === 'true';
            const departmentName = selectedOption.textContent;
            
            // Update department summary
            if (selectedDepartmentSummary) {
                selectedDepartmentSummary.innerHTML = `
                    <span class="badge bg-warning text-dark">
                        <i class="fas fa-users me-1"></i>${departmentName}
                    </span>
                    ${expressEnabled ? '<span class="badge bg-success ms-2"><i class="fas fa-bolt me-1"></i>Express</span>' : ''}
                `;
            }
            
            if (expressEnabled) {
                expressSection.style.display = 'block';
                if (expressIndicator) expressIndicator.style.display = 'inline-block';
                
                // Auto-generate Express fields if name is available and fields are empty
                const firstName = document.getElementById('first_name_en').value.trim();
                if (firstName) {
                    const expressUsernameEl = document.getElementById('express_username');
                    const expressPasswordEl = document.getElementById('express_password');
                    
                    if (expressUsernameEl && !expressUsernameEl.value) {
                        expressUsernameEl.value = generators.expressUsername();
                    }
                    if (expressPasswordEl && !expressPasswordEl.value) {
                        expressPasswordEl.value = generators.expressPassword();
                    }
                }
                
                utils.showNotification(`⚡ ${departmentName}: เปิดใช้งาน Express แล้ว`, 'success');
            } else {
                expressSection.style.display = 'none';
                if (expressIndicator) expressIndicator.style.display = 'none';
            }
        },
        
        // ✅ NEW: Branch Change Handler
        handleBranchChange: async () => {
            const branchSelect = document.getElementById('branch_id');
            const branchId = branchSelect.value;
            
            if (branchId) {
                const branchInfo = await branchManager.getBranchInfo(branchId);
                branchManager.updateBranchInfo(branchInfo);
                
                if (branchInfo) {
                    utils.showNotification(`🏢 เลือกสาขา: ${branchInfo.name} (${branchInfo.code})`, 'success');
                }
            } else {
                branchManager.updateBranchInfo(null);
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
        },
        
        handlePermissionSwitches: () => {
            const vpnSwitch = document.getElementById('vpn_access');
            const printingSwitch = document.getElementById('color_printing');
            const vpnStatus = document.getElementById('vpnStatus');
            const printingStatus = document.getElementById('printingStatus');
            
            if (vpnSwitch && vpnStatus) {
                vpnSwitch.addEventListener('change', function() {
                    vpnStatus.textContent = this.checked ? 'อนุญาต' : 'ไม่อนุญาต';
                    vpnStatus.className = this.checked ? 'text-success' : 'text-muted';
                });
            }
            
            if (printingSwitch && printingStatus) {
                printingSwitch.addEventListener('change', function() {
                    printingStatus.textContent = this.checked ? 'อนุญาต' : 'ไม่อนุญาต';
                    printingStatus.className = this.checked ? 'text-success' : 'text-muted';
                });
            }
        }
    };
    
    // Auto-generation functions
    const autoGenerate = {
        username: () => {
            const username = generators.username();
            if (username) {
                document.getElementById('username').value = username;
                console.log('✅ Username generated:', username);
            }
        },
        
        email: () => {
            const email = generators.email();
            if (email) {
                document.getElementById('email').value = email;
                autoGenerate.showEmailPreview();
                console.log('✅ Email generated:', email);
            }
        },
        
        phoneNumber: () => {
            const phone = generators.phoneNumber();
            if (phone) {
                document.getElementById('phone').value = phone;
                console.log('✅ Phone number generated (duplicates allowed):', phone);
                return phone;
            }
        },
        
        showEmailPreview: () => {
            const firstName = document.getElementById('first_name_en').value.trim();
            const lastName = document.getElementById('last_name_en').value.trim();
            const domain = document.getElementById('email_domain').value;
            const previewDiv = document.getElementById('emailPreview');
            const previewText = document.getElementById('emailPreviewText');
            
            if (!previewDiv || !previewText) return;
            
            const englishRegex = /^[a-zA-Z\s]+$/;
            
            if (firstName && lastName) {
                if (englishRegex.test(firstName) && englishRegex.test(lastName)) {
                    const emailPreview = `${firstName.toLowerCase()}.${lastName.charAt(0).toLowerCase()}@${domain}`;
                    previewText.textContent = emailPreview;
                    previewDiv.style.display = 'block';
                    previewDiv.className = 'mt-2 text-success';
                    
                    // Auto-sync login email
                    const loginEmailEl = document.getElementById('login_email');
                    if (loginEmailEl) {
                        loginEmailEl.value = emailPreview;
                    }
                    
                    // ✅ Update summary
                    const summaryEmail = document.getElementById('summaryEmail');
                    const summaryLoginEmail = document.getElementById('summaryLoginEmail');
                    if (summaryEmail) summaryEmail.textContent = emailPreview;
                    if (summaryLoginEmail) summaryLoginEmail.textContent = emailPreview;
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
    
    // Form Actions
    const formActions = {
        generateAll: async () => {
            const button = document.getElementById('generateAllBtn');
            utils.showLoading(button);
            
            try {
                console.log('🎯 Starting generateAll Branch System + ITMS Theme...');
                
                // Generate basic codes
                document.getElementById('employee_code').value = generators.employeeCode();
                document.getElementById('keycard_id').value = generators.keycardId();
                
                // ✅ Generate phone number (duplicates allowed)
                autoGenerate.phoneNumber();
                
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
                
                // Generate passwords - แยกแล้ว
                const computerPassword = utils.generateRandomString(10, true);
                const loginPassword = generators.password(); // 12 ตัวอักษร
                const emailPassword = utils.generateRandomString(10, true);
                
                document.getElementById('computer_password').value = computerPassword;
                document.getElementById('login_password').value = loginPassword;
                document.getElementById('email_password').value = emailPassword;
                
                // ✅ IMPORTANT: Sync hidden password field for backend
                document.getElementById('password').value = loginPassword;
                
                // Express fields (ถ้าแสดงอยู่)
                const expressSection = document.getElementById('expressSection');
                if (expressSection && expressSection.style.display !== 'none') {
                    document.getElementById('express_username').value = generators.expressUsername();
                    document.getElementById('express_password').value = generators.expressPassword();
                }
                
                utils.showNotification('🎉 สร้างข้อมูลทั้งหมดสำเร็จ! (รองรับ Branch System)', 'success');
                
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
                const expressIndicator = document.getElementById('expressIndicator');
                const vpnStatus = document.getElementById('vpnStatus');
                const printingStatus = document.getElementById('printingStatus');
                const branchInfo = document.getElementById('branchInfo');
                const selectedBranchSummary = document.getElementById('selectedBranchSummary');
                const selectedDepartmentSummary = document.getElementById('selectedDepartmentSummary');
                
                if (emailPreview) emailPreview.style.display = 'none';
                if (expressSection) expressSection.style.display = 'none';
                if (expressIndicator) expressIndicator.style.display = 'none';
                if (vpnStatus) vpnStatus.textContent = 'ไม่อนุญาต';
                if (printingStatus) printingStatus.textContent = 'ไม่อนุญาต';
                if (branchInfo) branchInfo.style.display = 'none';
                if (selectedBranchSummary) selectedBranchSummary.innerHTML = '<span class="text-muted">ยังไม่ได้เลือกสาขา</span>';
                if (selectedDepartmentSummary) selectedDepartmentSummary.innerHTML = '<span class="text-muted">ยังไม่ได้เลือกแผนก</span>';
                
                // Re-generate initial codes
                setTimeout(() => {
                    document.getElementById('employee_code').value = generators.employeeCode();
                    document.getElementById('keycard_id').value = generators.keycardId();
                    
                    // Reload branches
                    branchManager.loadBranches();
                }, 100);
                
                utils.showNotification('🗑️ ล้างข้อมูลทั้งหมดแล้ว', 'success');
            }
        },
        
        showPreview: () => {
            // Create preview content
            const previewContent = formActions.generatePreviewContent();
            document.getElementById('previewContent').innerHTML = previewContent;
            
            const modal = new bootstrap.Modal(document.getElementById('previewModal'));
            modal.show();
        },
        
        generatePreviewContent: () => {
            const formData = new FormData(document.getElementById('employeeForm'));
            const data = Object.fromEntries(formData.entries());
            
            // Get selected branch and department names
            const branchSelect = document.getElementById('branch_id');
            const departmentSelect = document.getElementById('department_id');
            const selectedBranch = branchSelect.options[branchSelect.selectedIndex];
            const selectedDepartment = departmentSelect.options[departmentSelect.selectedIndex];
            
            return `
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary">ข้อมูลพื้นฐาน</h6>
                        <table class="table table-sm">
                            <tr><th>รหัสพนักงาน:</th><td>${data.employee_code || '-'}</td></tr>
                            <tr><th>ID Keycard:</th><td>${data.keycard_id || '-'}</td></tr>
                            <tr><th>ชื่อ-นามสกุล (ไทย):</th><td>${data.first_name_th || ''} ${data.last_name_th || ''}</td></tr>
                            <tr><th>ชื่อ-นามสกุล (EN):</th><td>${data.first_name_en || ''} ${data.last_name_en || ''}</td></tr>
                            <tr><th>เบอร์โทร:</th><td>${data.phone || '-'} <span class="badge bg-success">ซ้ำได้</span></td></tr>
                            <tr><th>ชื่อเล่น:</th><td>${data.nickname || '-'}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-success">ระบบคอมพิวเตอร์</h6>
                        <table class="table table-sm">
                            <tr><th>Username:</th><td>${data.username || '-'}</td></tr>
                            <tr><th>รหัสผ่านคอม:</th><td>${data.computer_password ? '••••••••••' : '-'}</td></tr>
                            <tr><th>รหัสถ่ายเอกสาร:</th><td>${data.copier_code || '-'}</td></tr>
                        </table>
                        
                        <h6 class="text-info mt-3">ระบบอีเมลและ Login</h6>
                        <table class="table table-sm">
                            <tr><th>อีเมล:</th><td>${data.email || '-'}</td></tr>
                            <tr><th>รหัสผ่านอีเมล:</th><td>${data.email_password ? '••••••••••' : '-'}</td></tr>
                            <tr><th>รหัสผ่านเข้าระบบ:</th><td>${data.login_password ? '<span class="text-success">••••••••••••</span>' : '-'}</td></tr>
                        </table>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-6">
                        <h6 style="color: #B54544;">🏢 สาขา และแผนก</h6>
                        <table class="table table-sm">
                            <tr><th>สาขา:</th><td>${selectedBranch && selectedBranch.value ? selectedBranch.textContent : '<span class="text-muted">ไม่ระบุ</span>'}</td></tr>
                            <tr><th>แผนก:</th><td>${selectedDepartment && selectedDepartment.value ? selectedDepartment.textContent : '-'}</td></tr>
                            <tr><th>ตำแหน่ง:</th><td>${data.position || '-'}</td></tr>
                            <tr><th>สิทธิ์:</th><td>${document.querySelector('#role option:checked')?.textContent || '-'}</td></tr>
                            <tr><th>สถานะ:</th><td>${document.querySelector('#status option:checked')?.textContent || '-'}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-danger">Express v2.0</h6>
                        <table class="table table-sm">
                            <tr><th>Express Username:</th><td>${data.express_username || 'ไม่มี'}</td></tr>
                            <tr><th>Express Password:</th><td>${data.express_password || 'ไม่มี'}</td></tr>
                        </table>
                        
                        <h6 class="text-secondary mt-3">สิทธิ์พิเศษ</h6>
                        <table class="table table-sm">
                            <tr><th>VPN:</th><td>${data.vpn_access ? '<span class="badge bg-success">อนุญาต</span>' : '<span class="badge bg-secondary">ไม่อนุญาต</span>'}</td></tr>
                            <tr><th>ปริ้นสี:</th><td>${data.color_printing ? '<span class="badge bg-warning text-dark">อนุญาต</span>' : '<span class="badge bg-secondary">ไม่อนุญาต</span>'}</td></tr>
                        </table>
                    </div>
                </div>
                
                <div class="alert alert-success mt-3">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong>✅ Branch System Ready:</strong> รองรับเลือกสาขาและแผนก พร้อม ITMS Theme
                </div>
            `;
        },
        
        showBranchTest: () => {
            const modal = new bootstrap.Modal(document.getElementById('branchTestModal'));
            modal.show();
            
            // Load branch statistics
            branchManager.loadBranchStats();
        }
    };
    
    // Event Listeners Setup
    try {
        // Click handlers
        document.addEventListener('click', eventHandlers.handleMagicClick);
        document.addEventListener('click', eventHandlers.handlePasswordToggle);
        
        // ✅ Branch management handlers
        const branchSelect = document.getElementById('branch_id');
        if (branchSelect) {
            branchSelect.addEventListener('change', eventHandlers.handleBranchChange);
        }
        
        const refreshBranchBtn = document.getElementById('refreshBranchBtn');
        if (refreshBranchBtn) {
            refreshBranchBtn.addEventListener('click', async () => {
                const button = refreshBranchBtn;
                utils.showLoading(button);
                
                try {
                    await branchManager.loadBranches();
                    utils.showNotification('🔄 รีเฟรชรายการสาขาเรียบร้อย', 'success');
                } catch (error) {
                    utils.showNotification('❌ ไม่สามารถรีเฟรชได้', 'error');
                } finally {
                    utils.hideLoading(button);
                }
            });
        }
        
        const branchInfoBtn = document.getElementById('branchInfoBtn');
        if (branchInfoBtn) {
            branchInfoBtn.addEventListener('click', async () => {
                const branchId = document.getElementById('branch_id').value;
                if (branchId) {
                    const branchInfo = await branchManager.getBranchInfo(branchId);
                    if (branchInfo) {
                        utils.showNotification(`🏢 ${branchInfo.name}: ${branchInfo.current_employees} พนักงาน`, 'info');
                    }
                } else {
                    utils.showNotification('❌ กรุณาเลือกสาขาก่อน', 'warning');
                }
            });
        }
        
        // Department change handler
        const departmentSelect = document.getElementById('department_id');
        if (departmentSelect) {
            departmentSelect.addEventListener('change', eventHandlers.handleDepartmentChange);
        }
        
        // Phone format handler
        const phoneInput = document.getElementById('phone');
        if (phoneInput) {
            phoneInput.addEventListener('input', eventHandlers.handlePhoneFormat);
            phoneInput.addEventListener('focus', function() {
                if (!phoneInput.dataset.notificationShown) {
                    utils.showNotification('📞 เบอร์โทรซ้ำได้แล้ว - ระบบได้แก้ไขเรียบร้อย!', 'success');
                    phoneInput.dataset.notificationShown = 'true';
                }
            });
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
        
        const testBranchBtn = document.getElementById('testBranchBtn');
        if (testBranchBtn) {
            testBranchBtn.addEventListener('click', formActions.showBranchTest);
        }
        
        // ✅ Generate Computer System Button
        const generateComputerBtn = document.getElementById('generateComputerBtn');
        if (generateComputerBtn) {
            generateComputerBtn.addEventListener('click', async () => {
                const button = generateComputerBtn;
                utils.showLoading(button);
                
                try {
                    // Generate computer credentials
                    const username = generators.username();
                    const computerPassword = utils.generateRandomString(10, true);
                    const copierCode = generators.copierCode();
                    
                    if (username) document.getElementById('username').value = username;
                    if (computerPassword) document.getElementById('computer_password').value = computerPassword;
                    if (copierCode) document.getElementById('copier_code').value = copierCode;
                    
                    utils.showNotification('🖥️ สร้างระบบคอมพิวเตอร์สำเร็จ!', 'success');
                } catch (error) {
                    utils.showNotification('❌ เกิดข้อผิดพลาด', 'error');
                } finally {
                    utils.hideLoading(button);
                }
            });
        }
        
        // ✅ Generate Express Button
        const generateExpressBtn = document.getElementById('generateExpressBtn');
        if (generateExpressBtn) {
            generateExpressBtn.addEventListener('click', async () => {
                const button = generateExpressBtn;
                utils.showLoading(button);
                
                try {
                    // Generate Express credentials
                    const expressUsername = generators.expressUsername();
                    const expressPassword = generators.expressPassword();
                    
                    if (expressUsername) document.getElementById('express_username').value = expressUsername;
                    if (expressPassword) document.getElementById('express_password').value = expressPassword;
                    
                    utils.showNotification('⚡ สร้าง Express Credentials สำเร็จ!', 'success');
                } catch (error) {
                    utils.showNotification('❌ เกิดข้อผิดพลาด', 'error');
                } finally {
                    utils.hideLoading(button);
                }
            });
        }
        
        // ✅ Password field listeners for real-time summary update
        const emailPasswordField = document.getElementById('email_password');
        const loginPasswordField = document.getElementById('login_password');
        
        if (emailPasswordField) {
            emailPasswordField.addEventListener('input', () => {
                const summaryEmailPassword = document.getElementById('summaryEmailPassword');
                if (summaryEmailPassword) {
                    summaryEmailPassword.textContent = emailPasswordField.value ? '••••••••••' : '-';
                }
            });
        }
        
        if (loginPasswordField) {
            loginPasswordField.addEventListener('input', () => {
                const summaryLoginPassword = document.getElementById('summaryLoginPassword');
                const passwordField = document.getElementById('password');
                
                if (summaryLoginPassword) {
                    summaryLoginPassword.textContent = loginPasswordField.value ? '••••••••••••' : '-';
                }
                
                // Auto-sync hidden password field
                if (passwordField) {
                    passwordField.value = loginPasswordField.value;
                }
            });
        }
        
        // Setup permission switches
        eventHandlers.handlePermissionSwitches();
        
        console.log('✅ All event listeners attached successfully (Branch System + ITMS Theme)');
        
    } catch (error) {
        console.error('❌ Error setting up event listeners:', error);
    }
    
    // Initial setup
    setTimeout(async () => {
        try {
            // Auto-generate employee code and keycard if empty
            const employeeCodeEl = document.getElementById('employee_code');
            const keycardIdEl = document.getElementById('keycard_id');
            
            if (employeeCodeEl && !employeeCodeEl.value) {
                employeeCodeEl.value = generators.employeeCode();
            }
            
            if (keycardIdEl && !keycardIdEl.value) {
                keycardIdEl.value = generators.keycardId();
            }
            
            // ✅ Load branches
            await branchManager.loadBranches();
            
            // Initialize handlers
            eventHandlers.handleDepartmentChange();
            autoGenerate.showEmailPreview();
            
            // ✅ Test password generators
            console.log('🧪 Testing Password Generators:');
            console.log('  - Computer Password (10):', utils.generateRandomString(10, true));
            console.log('  - Email Password (10):', utils.generateRandomString(10, true));
            console.log('  - Login Password (12):', generators.password());
            console.log('  - Express Password (4):', utils.generateUniqueNumbers(4));
            
            console.log('✅ Employee Create Form Ready - Branch System + ITMS Theme');
            console.log('🏢 Branch System: รองรับเลือกสาขาแล้ว');
            console.log('📞 Phone Duplicates: อนุญาตให้ซ้ำได้แล้ว (แก้ไขเรียบร้อย)');
            console.log('🔒 Security: Email, Username, Express Username ยังคง unique');
            console.log('⚡ Express v2.0: ทำงานปกติ');
            console.log('🎨 ITMS Theme: สีแดง-ส้ม Perfect');
            console.log('🔑 Password System: Separated System Ready');
            console.log('  - Email Password: 10 chars');
            console.log('  - Login Password: 12 chars');
            console.log('  - Computer Password: 10 chars');
            console.log('  - Express Password: 4 unique digits');
            
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

console.log('📝 Employee Create Form Script Loaded (Branch System + ITMS Theme)');
console.log('🔧 Available functions: All Branch Management Functions Ready');
console.log('⚡ Features: Branch Selection, Auto-fill, Preview, Express v2.0');
console.log('🏢 Branch System: Load, Refresh, Info Display, Statistics');
console.log('🎨 ITMS Theme: Red-Orange Gradient Colors Perfect');
console.log('🔑 Password Generators Available:');
console.log('  - computer_password (10 chars)');
console.log('  - email_password (10 chars)'); 
console.log('  - login_password (12 chars)');
console.log('  - express_password (4 unique digits)');
</script>

<style>
/* ✅ ITMS Theme Integration - Red-Orange Colors */
.btn-gradient {
    background: linear-gradient(45deg, #B54544, #E6952A);
    color: white;
    border: none;
}

.btn-gradient:hover {
    background: linear-gradient(45deg, #a03f3e, #d4851f);
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.card-header-gradient {
    background: linear-gradient(45deg, #B54544, #E6952A);
    color: white;
}

.text-gradient {
    background: linear-gradient(45deg, #B54544, #E6952A);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Branch selection enhancements */
#branch_id {
    border: 2px solid #dee2e6;
    transition: all 0.3s ease;
}

#branch_id:focus {
    border-color: #B54544;
    box-shadow: 0 0 0 0.2rem rgba(181, 69, 68, 0.25);
}

/* Express section animations */
#expressSection {
    transition: all 0.5s ease;
}

#expressSection.show {
    animation: slideIn 0.5s ease;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Branch info display */
#branchInfo {
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

/* Enhanced button styles */
.btn-outline-primary:hover,
.btn-outline-secondary:hover,
.btn-outline-info:hover {
    transform: scale(1.02);
    transition: transform 0.2s ease;
}

/* Enhanced form section headers */
.card-header {
    border-bottom: 3px solid transparent;
    border-image: linear-gradient(45deg, #B54544, #E6952A) 1;
}

/* Permission cards enhancement */
.form-check-input:checked {
    background-color: #B54544;
    border-color: #B54544;
}

/* Mobile responsiveness */
@media (max-width: 768px) {
    .card-header h5 {
        font-size: 1rem;
    }
    
    .badge {
        font-size: 0.7rem;
    }
    
    .btn {
        font-size: 0.8rem;
        padding: 0.4rem 0.8rem;
    }
}

/* Enhanced loading states */
.btn:disabled {
    opacity: 0.6;
}

/* Notification enhancements */
.alert.position-fixed {
    border-left: 4px solid;
    border-left-color: inherit;
}

.alert-success {
    border-left-color: #198754;
}

.alert-danger {
    border-left-color: #dc3545;
}

.alert-warning {
    border-left-color: #ffc107;
}

.alert-info {
    border-left-color: #0dcaf0;
}

/* Branch statistics cards */
.card.border-primary { border-color: #0d6efd !important; }
.card.border-success { border-color: #198754 !important; }
.card.border-info { border-color: #0dcaf0 !important; }
.card.border-warning { border-color: #ffc107 !important; }

/* Enhanced preview modal */
.modal-xl {
    max-width: 90vw;
}

@media (max-width: 576px) {
    .modal-xl {
        max-width: 95vw;
        margin: 0.5rem;
    }
}
</style>
@endpush
