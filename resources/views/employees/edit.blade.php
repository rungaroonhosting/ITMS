@extends('layouts.app')

@section('title', 'แก้ไขข้อมูลพนักงาน - ' . $employee->full_name_th)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('employees.index') }}">จัดการพนักงาน</a></li>
    <li class="breadcrumb-item"><a href="{{ route('employees.show', $employee) }}">{{ $employee->full_name_th }}</a></li>
    <li class="breadcrumb-item active">แก้ไข</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-success fw-bold">
                    <i class="fas fa-user-edit me-2"></i>แก้ไขข้อมูลพนักงาน
                </h1>
                <p class="text-muted mb-0">แก้ไขข้อมูล: {{ $employee->full_name_th }} ({{ $employee->employee_code }}) - Enhanced v2.0</p>
                <div class="mt-2">
                    <span class="badge bg-{{ $employee->status == 'active' ? 'success' : 'secondary' }} me-2">
                        {{ $employee->status == 'active' ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                    </span>
                    <span class="badge bg-primary me-2">
                        {{ $employee->role_display ?? ucfirst($employee->role) }}
                    </span>
                    @if($employee->department)
                        <span class="badge bg-info me-2">
                            {{ $employee->department->name }}
                            @if($employee->department->express_enabled ?? false)
                                (Express ✓)
                            @endif
                        </span>
                    @endif
                    <span class="badge bg-success">
                        <i class="fas fa-phone me-1"></i>
                        ✅ เบอร์โทรซ้ำได้แล้ว
                    </span>
                    <span class="badge bg-primary">
                        <i class="fas fa-eye me-1"></i>
                        แสดงรหัสผ่านได้ทั้งหมด
                    </span>
                </div>
            </div>
            <a href="{{ route('employees.show', $employee) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>กลับ
            </a>
        </div>
    </div>
</div>

<!-- ✅ FIXED: Success Alert for Phone Fix & Edit Mode -->
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <h6 class="fw-bold"><i class="fas fa-check-circle me-2"></i>โหมดแก้ไข - ระบบพร้อมใช้งาน! (แก้ไข Password Handling แล้ว)</h6>
    <div class="row">
        <div class="col-md-6">
            <ul class="mb-0">
                <li><strong>✅ เบอร์โทรซ้ำได้:</strong> สามารถใช้เบอร์เดียวกันได้หลายคน</li>
                <li><strong>🔒 รหัสผ่าน:</strong> แก้ไข NULL error แล้ว</li>
            </ul>
        </div>
        <div class="col-md-6">
            <ul class="mb-0">
                <li><strong>🔒 ความปลอดภัย:</strong> Email, Username ยังคง unique</li>
                <li><strong>⚡ Express v2.0:</strong> ทำงานปกติ ไม่กระทบ</li>
            </ul>
        </div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>

<!-- Current Data Overview -->
<div class="alert alert-info alert-dismissible fade show" role="alert">
    <h6 class="fw-bold"><i class="fas fa-info-circle me-2"></i>ข้อมูลปัจจุบัน - สำหรับอ้างอิง</h6>
    <div class="row">
        <div class="col-md-4">
            <ul class="mb-0 small">
                <li><strong>รหัสพนักงาน:</strong> {{ $employee->employee_code }}</li>
                <li><strong>อีเมล:</strong> {{ $employee->email }}</li>
                <li><strong>Username:</strong> {{ $employee->username }}</li>
            </ul>
        </div>
        <div class="col-md-4">
            <ul class="mb-0 small">
                <li><strong>แผนก:</strong> {{ $employee->department ? $employee->department->name : 'ไม่ระบุ' }}</li>
                <li><strong>ตำแหน่ง:</strong> {{ $employee->position }}</li>
                <li><strong>เบอร์โทร:</strong> {{ $employee->phone }} <span class="badge bg-success">ซ้ำได้</span></li>
            </ul>
        </div>
        <div class="col-md-4">
            <ul class="mb-0 small">
                <li><strong>รหัสผ่านคอม:</strong> <code>{{ $employee->computer_password ?: 'ไม่มี' }}</code></li>
                <li><strong>รหัสผ่านอีเมล:</strong> <code>{{ $employee->email_password ?: 'ไม่มี' }}</code></li>
                @if($employee->express_username)
                    <li><strong>Express:</strong> <code>{{ $employee->express_username }}</code>/<code>{{ $employee->express_password }}</code></li>
                @endif
            </ul>
        </div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>

<!-- Enhanced Quick Actions -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row text-center g-3">
            <div class="col-md-3 col-sm-6">
                <div class="d-flex flex-column h-100">
                    <button type="button" class="btn btn-outline-primary w-100 flex-fill d-flex align-items-center justify-content-center" id="autoFillBtn" style="min-height: 45px;">
                        <span class="d-flex align-items-center">
                            <i class="fas fa-magic me-2"></i>
                            <span class="d-none d-lg-inline">เติมข้อมูลอัตโนมัติ</span>
                            <span class="d-lg-none">เติมข้อมูล</span>
                        </span>
                    </button>
                    <div class="form-text mt-2">
                        <small class="text-muted">Username, Email จากชื่อ</small>
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
                        <small class="text-muted">ตรวจสอบก่อนบันทึก</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="d-flex flex-column h-100">
                    <button type="button" class="btn btn-outline-warning w-100 flex-fill d-flex align-items-center justify-content-center" id="resetPasswordBtn" style="min-height: 45px;">
                        <span class="d-flex align-items-center">
                            <i class="fas fa-key me-2"></i>
                            <span class="d-none d-lg-inline">รีเซ็ตรหัสผ่าน</span>
                            <span class="d-lg-none">รีเซ็ต</span>
                        </span>
                    </button>
                    <div class="form-text mt-2">
                        <small class="text-warning">เลือกรหัสที่ต้องการรีเซ็ต</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="d-flex flex-column h-100">
                    <button type="button" class="btn btn-outline-success w-100 flex-fill d-flex align-items-center justify-content-center" id="generateAllBtn" style="min-height: 45px;">
                        <span class="d-flex align-items-center">
                            <i class="fas fa-sync me-2"></i>
                            <span class="d-none d-lg-inline">สร้างรหัสใหม่ทั้งหมด</span>
                            <span class="d-lg-none">สร้างใหม่</span>
                        </span>
                    </button>
                    <div class="form-text mt-2">
                        <small class="text-success">รหัสผ่านใหม่ทั้งหมด</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Form -->
<form id="employeeForm" action="{{ route('employees.update', $employee) }}" method="POST">
    @csrf
    @method('PUT')
    
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
                <small class="text-success">
                    <i class="fas fa-check-circle me-1"></i>
                    <strong>แก้ไขแล้ว:</strong> ปัญหา Password NULL - ระบบจะไม่อัปเดตรหัสผ่านถ้าเว้นว่าง
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
                    <small class="text-muted">ข้อมูルส่วนตัวและการติดต่อ</small>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <!-- รหัสพนักงาน -->
                <div class="col-md-6">
                    <label for="employee_code" class="form-label">รหัสพนักงาน <span class="text-danger">*</span></label>
                    <div class="input-group">
                        @php $userRole = auth()->user()->role ?? 'employee'; @endphp
                        <input type="text" class="form-control @error('employee_code') is-invalid @enderror" 
                               id="employee_code" name="employee_code" 
                               value="{{ old('employee_code', $employee->employee_code) }}" 
                               placeholder="รหัสพนักงาน" 
                               {{ ($userRole === 'super_admin' || $userRole === 'it_admin') ? '' : 'readonly' }}
                               required>
                        @if($userRole === 'super_admin' || $userRole === 'it_admin')
                            <button type="button" class="btn btn-outline-primary" data-target="employee_code">
                                <i class="fas fa-magic"></i>
                            </button>
                        @endif
                    </div>
                    <div class="form-text">
                        ปัจจุบัน: <strong>{{ $employee->employee_code }}</strong>
                        @if($userRole !== 'super_admin' && $userRole !== 'it_admin')
                            (เฉพาะ Admin แก้ไขได้)
                        @endif
                    </div>
                    @error('employee_code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- ID Keycard -->
                <div class="col-md-6">
                    <label for="keycard_id" class="form-label">ID Keycard <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="text" class="form-control @error('keycard_id') is-invalid @enderror" 
                               id="keycard_id" name="keycard_id" 
                               value="{{ old('keycard_id', $employee->keycard_id) }}" 
                               placeholder="ID Keycard" 
                               {{ ($userRole === 'super_admin' || $userRole === 'it_admin') ? '' : 'readonly' }}
                               required>
                        @if($userRole === 'super_admin' || $userRole === 'it_admin')
                            <button type="button" class="btn btn-outline-primary" data-target="keycard_id">
                                <i class="fas fa-magic"></i>
                            </button>
                        @endif
                    </div>
                    <div class="form-text">
                        ปัจจุบัน: <strong>{{ $employee->keycard_id }}</strong>
                        @if($userRole !== 'super_admin' && $userRole !== 'it_admin')
                            (เฉพาะ Admin แก้ไขได้)
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
                           id="first_name_th" name="first_name_th" 
                           value="{{ old('first_name_th', $employee->first_name_th) }}" 
                           placeholder="ชื่อภาษาไทย" required>
                    <div class="form-text">
                        ปัจจุบัน: <strong>{{ $employee->first_name_th }}</strong>
                    </div>
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
                           id="last_name_th" name="last_name_th" 
                           value="{{ old('last_name_th', $employee->last_name_th) }}" 
                           placeholder="นามสกุลภาษาไทย" required>
                    <div class="form-text">
                        ปัจจุบัน: <strong>{{ $employee->last_name_th }}</strong>
                    </div>
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
                           id="first_name_en" name="first_name_en" 
                           value="{{ old('first_name_en', $employee->first_name_en) }}" 
                           placeholder="First Name" required>
                    <div class="form-text">
                        <small class="text-muted">
                            <i class="fas fa-globe me-1"></i>
                            ปัจจุบัน: <strong>{{ $employee->first_name_en }}</strong> (ใช้เฉพาะ A-Z เท่านั้น สำหรับสร้าง Username และ Express)
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
                           id="last_name_en" name="last_name_en" 
                           value="{{ old('last_name_en', $employee->last_name_en) }}" 
                           placeholder="Last Name" required>
                    <div class="form-text">
                        <small class="text-muted">
                            <i class="fas fa-globe me-1"></i>
                            ปัจจุบัน: <strong>{{ $employee->last_name_en }}</strong> (ใช้เฉพาะ A-Z เท่านั้น สำหรับสร้าง Username และ Email)
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
                           id="phone" name="phone" 
                           value="{{ old('phone', $employee->phone) }}" 
                           placeholder="08x-xxx-xxxx" required>
                    <div class="form-text">
                        <div class="alert alert-success p-2 mt-2 mb-0">
                            <small>
                                ปัจจุบัน: <strong>{{ $employee->phone }}</strong><br>
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
                           id="nickname" name="nickname" 
                           value="{{ old('nickname', $employee->nickname) }}" 
                           placeholder="ชื่อเล่น">
                    <div class="form-text">
                        ปัจจุบัน: <strong>{{ $employee->nickname ?: 'ไม่มี' }}</strong>
                    </div>
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
                               value="{{ old('username', $employee->username) }}"
                               placeholder="Username"
                               required>
                        <button type="button" class="btn btn-outline-primary" data-target="username">
                            <i class="fas fa-user"></i>
                        </button>
                    </div>
                    <div class="form-text">
                        ปัจจุบัน: <strong>{{ $employee->username }}</strong>
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

                <!-- ✅ FIXED: Computer Password (แสดงได้ทั้งหมด) -->
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
                               value=""
                               placeholder="เว้นว่างหากไม่เปลี่ยน">
                        <button type="button" class="btn btn-outline-primary" data-target="computer_password">
                            <i class="fas fa-lock"></i>
                        </button>
                        <button type="button" class="btn btn-outline-secondary" data-toggle-password="computer_password">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="form-text">
                        ปัจจุบัน: <code class="text-success">{{ $employee->computer_password ?: 'ไม่มีข้อมูล' }}</code>
                        <br><small class="text-warning">เว้นว่างหากไม่ต้องการเปลี่ยน</small>
                        <br><span class="text-success">(แสดงให้เห็นได้ทั้งหมด)</span>
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
                               value="{{ old('copier_code', $employee->copier_code) }}"
                               placeholder="รหัส 4 หลัก" 
                               maxlength="4">
                        <button type="button" class="btn btn-outline-primary" data-target="copier_code">
                            <i class="fas fa-print"></i>
                        </button>
                    </div>
                    <div class="form-text">
                        ปัจจุบัน: <strong>{{ $employee->copier_code ?: 'ไม่มี' }}</strong>
                        <small class="text-muted ms-2">
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
                    <small class="text-muted">อีเมลและรหัสผ่าน แยกระบบแล้ว (รหัสผ่านต่างกัน) - แก้ไข NULL Error แล้ว</small>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i>
                <strong>✅ แก้ไขแล้ว:</strong> ปัญหา Password NULL Error - ระบบจะไม่อัปเดตรหัสผ่านถ้าเว้นว่าง
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
                               value="{{ old('email', $employee->email) }}"
                               placeholder="อีเมล"
                               required>
                        <select class="form-select" id="email_domain" style="max-width: 220px;">
                            @php
                                $currentDomain = '';
                                if($employee->email && strpos($employee->email, '@') !== false) {
                                    $currentDomain = substr($employee->email, strpos($employee->email, '@') + 1);
                                }
                            @endphp
                            <option value="bettersystem.co.th" {{ $currentDomain === 'bettersystem.co.th' ? 'selected' : '' }}>@bettersystem.co.th</option>
                            <option value="better-groups.com" {{ $currentDomain === 'better-groups.com' ? 'selected' : '' }}>@better-groups.com</option>
                        </select>
                        <button type="button" class="btn btn-outline-primary" data-target="email">
                            <i class="fas fa-envelope"></i>
                        </button>
                    </div>
                    <div class="form-text">
                        ปัจจุบัน: <strong>{{ $employee->email }}</strong>
                        <br>รูปแบบ: <strong>ชื่อ.ตัวแรกของนามสกุล@โดเมน</strong>
                        <div id="emailPreview" class="mt-2" style="display: none;">
                            <span class="text-success">ตัวอย่าง: </span>
                            <code class="text-primary" id="emailPreviewText"></code>
                        </div>
                    </div>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- ✅ FIXED: Email Password (แยกแล้ว) -->
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
                               value=""
                               placeholder="เว้นว่างหากไม่เปลี่ยน">
                        <button type="button" class="btn btn-outline-primary" data-target="email_password">
                            <i class="fas fa-mail-bulk"></i>
                        </button>
                        <button type="button" class="btn btn-outline-secondary" data-toggle-password="email_password">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="form-text">
                        ปัจจุบัน: <code class="text-warning">{{ $employee->email_password ?: 'ไม่มีข้อมูล' }}</code>
                        <br><small class="text-warning">เว้นว่างหากไม่ต้องการเปลี่ยน</small>
                        <br><span class="text-warning">
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
                               value="{{ old('login_email', $employee->email) }}"
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

                <!-- ✅ FIXED: Login Password (แยกแล้ว) -->
                <div class="col-md-4">
                    <label for="login_password" class="form-label">
                        Password เข้าระบบ
                        <span class="badge bg-success ms-2">Login Only</span>
                    </label>
                    <div class="input-group">
                        <input type="text" 
                               class="form-control @error('login_password') is-invalid @enderror" 
                               id="login_password" 
                               name="login_password" 
                               value=""
                               placeholder="เว้นว่างหากไม่เปลี่ยน">
                        <button type="button" class="btn btn-outline-primary" data-target="login_password">
                            <i class="fas fa-key"></i>
                        </button>
                        <button type="button" class="btn btn-outline-secondary" data-toggle-password="login_password">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="form-text">
                        <small class="text-success">
                            <i class="fas fa-check-circle me-1"></i>
                            <strong>แก้ไขแล้ว:</strong> ไม่เปลี่ยนรหัสผ่านถ้าเว้นว่าง
                        </small>
                        <br><small class="text-warning">เว้นว่างหากไม่ต้องการเปลี่ยน</small>
                        <br><span class="text-success">
                            <i class="fas fa-shield-alt me-1"></i>
                            เฉพาะเข้าสู่ระบบ (ไม่เกี่ยวกับอีเมล)
                        </span>
                    </div>
                    @error('login_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Summary Card -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border-success">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-check-circle me-2"></i>✅ สรุปการแก้ไข Password Handling
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-warning">
                                        <i class="fas fa-envelope me-2"></i>ระบบอีเมล (ปัจจุบัน)
                                    </h6>
                                    <ul class="list-unstyled">
                                        <li><strong>อีเมล:</strong> <span class="text-muted">{{ $employee->email }}</span></li>
                                        <li><strong>รหัสผ่าน:</strong> <span class="text-muted">{{ $employee->email_password ?: 'ไม่มีข้อมูล' }}</span></li>
                                        <li><strong>ใช้สำหรับ:</strong> <span class="text-info">ระบบอีเมลเท่านั้น</span></li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-success">
                                        <i class="fas fa-sign-in-alt me-2"></i>ระบบเข้าสู่ระบบ (ปัจจุบัน)
                                    </h6>
                                    <ul class="list-unstyled">
                                        <li><strong>อีเมล:</strong> <span class="text-muted">{{ $employee->email }}</span></li>
                                        <li><strong>รหัสผ่าน:</strong> <span class="text-success">✅ แก้ไข NULL แล้ว</span></li>
                                        <li><strong>ใช้สำหรับ:</strong> <span class="text-success">เข้าสู่ระบบเท่านั้น</span></li>
                                    </ul>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <div class="row">
                                <div class="col-12">
                                    <div class="alert alert-success mb-0">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6><i class="fas fa-check-circle me-1"></i> ✅ แก้ไขแล้ว:</h6>
                                                <ul class="mb-0">
                                                    <li>🛡️ <strong>NULL Error:</strong> แก้ไขแล้ว</li>
                                                    <li>🔒 <strong>Password Handling:</strong> ไม่อัปเดตถ้าเว้นว่าง</li>
                                                    <li>👁️ <strong>แสดงได้:</strong> ดูรหัสผ่านทั้งหมด</li>
                                                </ul>
                                            </div>
                                            <div class="col-md-6">
                                                <h6><i class="fas fa-cogs me-1"></i> การทำงาน:</h6>
                                                <ul class="mb-0">
                                                    <li>👔 <strong>Admin:</strong> แก้ไขได้ปกติ</li>
                                                    <li>👤 <strong>Update:</strong> เฉพาะที่เปลี่ยน</li>
                                                    <li>🔧 <strong>System:</strong> ป้องกัน NULL error</li>
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

    <!-- แผนกและสิทธิ์ -->
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <div class="border border-2 border-warning rounded-circle d-flex align-items-center justify-content-center me-3 bg-light" style="width: 45px; height: 45px; min-width: 45px;">
                    <i class="fas fa-building text-warning" style="font-size: 20px;"></i>
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
                        <span class="badge bg-warning text-dark ms-2" id="expressIndicator" 
                              style="{{ ($employee->department && ($employee->department->express_enabled ?? false)) ? 'display: inline-block;' : 'display: none;' }}">
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
                                        {{ old('department_id', $employee->department_id) == $department->id ? 'selected' : '' }}>
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
                                        {{ old('department_id', $employee->department_id) == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                    @if($department->express_enabled ?? false)
                                        (Express)
                                    @endif
                                </option>
                            @endforeach
                        @endif
                    </select>
                    <div class="form-text">
                        ปัจจุบัน: <strong>{{ $employee->department ? $employee->department->name : 'ไม่ระบุ' }}</strong>
                        @if($employee->department && ($employee->department->express_enabled ?? false))
                            <span class="badge bg-info ms-1">Express</span>
                        @endif
                        @if($userRole === 'express')
                            <br><span class="text-info">Express: สามารถเลือกเฉพาะแผนกที่รองรับ Express</span>
                        @elseif($userRole === 'super_admin')
                            <br><span class="text-success">
                                <i class="fas fa-plus-circle me-1"></i>
                                SuperAdmin: สามารถจัดการ Express ของแผนกได้ใน
                                <a href="#" target="_blank">หน้าจัดการแผนก</a>
                            </span>
                        @endif
                    </div>
                    @error('department_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
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
                           value="{{ old('position', $employee->position) }}"
                           placeholder="เช่น Developer, Accountant"
                           required>
                    <div class="form-text">
                        ปัจจุบัน: <strong>{{ $employee->position }}</strong>
                    </div>
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
                        <option value="employee" {{ old('role', $employee->role) == 'employee' ? 'selected' : '' }}>พนักงานทั่วไป (Employee)</option>
                        @if($userRole === 'super_admin' || $userRole === 'it_admin')
                            <option value="hr" {{ old('role', $employee->role) == 'hr' ? 'selected' : '' }}>ฝ่ายบุคคล (HR)</option>
                            <option value="manager" {{ old('role', $employee->role) == 'manager' ? 'selected' : '' }}>ผู้จัดการ (Manager)</option>
                            <option value="express" {{ old('role', $employee->role) == 'express' ? 'selected' : '' }}>Express User</option>
                            @if($userRole === 'super_admin')
                                <option value="it_admin" {{ old('role', $employee->role) == 'it_admin' ? 'selected' : '' }}>IT Admin</option>
                            @endif
                        @endif
                    </select>
                    <div class="form-text">
                        ปัจจุบัน: <strong>{{ $employee->role_display ?? ucfirst($employee->role) }}</strong>
                    </div>
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
                        <option value="active" {{ old('status', $employee->status) == 'active' ? 'selected' : '' }}>ใช้งาน (Active)</option>
                        <option value="inactive" {{ old('status', $employee->status) == 'inactive' ? 'selected' : '' }}>ไม่ใช้งาน (Inactive)</option>
                    </select>
                    <div class="form-text">
                        ปัจจุบัน: 
                        <span class="badge bg-{{ $employee->status == 'active' ? 'success' : 'secondary' }}">
                            {{ $employee->status == 'active' ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                        </span>
                    </div>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- โปรแกรม Express (Dynamic v2.0 Enhanced) -->
    <div class="card mb-4" id="expressSection" 
         style="{{ ($employee->department && ($employee->department->express_enabled ?? false)) ? 'display: block;' : 'display: none;' }}">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <div class="border border-2 border-danger rounded-circle d-flex align-items-center justify-content-center me-3 bg-light" style="width: 45px; height: 45px; min-width: 45px;">
                    <i class="fas fa-bolt text-danger" style="font-size: 20px;"></i>
                </div>
                <div>
                    <h5 class="card-title mb-0">
                        โปรแกรม Express v2.0
                        <span class="badge bg-warning text-dark ms-2">Enhanced</span>
                        @if($employee->express_username)
                            <span class="badge bg-success ms-2">มีข้อมูล</span>
                        @else
                            <span class="badge bg-secondary ms-2">ยังไม่มีข้อมูล</span>
                        @endif
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
            
            @if($employee->express_username)
                <div class="alert alert-info mb-3">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>ข้อมูลปัจจุบัน:</strong> 
                    Username: <code>{{ $employee->express_username }}</code>, 
                    Password: <code>{{ $employee->express_password }}</code>
                </div>
            @endif
            
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
                               value="{{ old('express_username', $employee->express_username) }}"
                               placeholder="จะสร้างจากชื่อ EN (1-7 ตัว)" 
                               maxlength="7">
                        <button type="button" class="btn btn-outline-primary" data-target="express_username">
                            <i class="fas fa-bolt"></i>
                        </button>
                    </div>
                    <div class="form-text">
                        @if($employee->express_username)
                            ปัจจุบัน: <code class="text-info">{{ $employee->express_username }}</code> ({{ strlen($employee->express_username) }} ตัว)
                            <br>
                        @endif
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
                               value="{{ old('express_password', $employee->express_password) }}"
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
                        @if($employee->express_password)
                            ปัจจุบัน: <code class="text-info">{{ $employee->express_password }}</code>
                            <br>
                        @endif
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
                                       {{ old('vpn_access', $employee->vpn_access) ? 'checked' : '' }}>
                                <label class="form-check-label ms-2" for="vpn_access">
                                    <span id="vpnStatus">{{ old('vpn_access', $employee->vpn_access) ? 'อนุญาต' : 'ไม่อนุญาต' }}</span>
                                </label>
                            </div>
                            <small class="text-muted">
                                อนุญาตให้เชื่อมต่อ VPN เพื่อทำงานจากที่บ้าน
                            </small>
                            <div class="form-text mt-2">
                                ปัจจุบัน: 
                                <span class="badge bg-{{ $employee->vpn_access ? 'success' : 'secondary' }}">
                                    {{ $employee->vpn_access ? 'อนุญาต' : 'ไม่อนุญาต' }}
                                </span>
                            </div>
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
                                       {{ old('color_printing', $employee->color_printing) ? 'checked' : '' }}>
                                <label class="form-check-label ms-2" for="color_printing">
                                    <span id="printingStatus">{{ old('color_printing', $employee->color_printing) ? 'อนุญาต' : 'ไม่อนุญาต' }}</span>
                                </label>
                            </div>
                            <small class="text-muted">
                                อนุญาตให้ใช้เครื่องพิมพ์สีในการพิมพ์เอกสาร
                            </small>
                            <div class="form-text mt-2">
                                ปัจจุบัน: 
                                <span class="badge bg-{{ $employee->color_printing ? 'warning text-dark' : 'secondary' }}">
                                    {{ $employee->color_printing ? 'อนุญาต' : 'ไม่อนุญาต' }}
                                </span>
                            </div>
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
                <a href="{{ route('employees.show', $employee) }}" 
                   class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>ยกเลิก
                </a>
                
                <button type="submit" 
                        class="btn btn-success"
                        id="submitBtn">
                    <i class="fas fa-save me-2"></i>บันทึกการแก้ไข
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
                    <i class="fas fa-eye me-2"></i>ตัวอย่างการแก้ไขข้อมูลพนักงาน
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="previewContent">
                <!-- Preview content will be inserted here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                <button type="button" class="btn btn-success" onclick="submitForm()">
                    ยืนยันและบันทึก
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Reset Password Modal -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="resetPasswordModalLabel">
                    <i class="fas fa-key me-2"></i>รีเซ็ตรหัสผ่าน - เลือกรหัสที่ต้องการ
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle me-2"></i>เลือกรหัสผ่านที่ต้องการรีเซ็ต</h6>
                    <p class="mb-0">คุณสามารถเลือกรีเซ็ตรหัสผ่านแยกกันได้ หรือรีเซ็ตทั้งหมดพร้อมกัน</p>
                </div>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <button type="button" class="btn btn-outline-primary w-100 d-flex align-items-center justify-content-center" onclick="resetSpecificPassword('computer')" style="min-height: 60px;">
                            <div class="text-center">
                                <i class="fas fa-desktop fa-2x mb-2"></i><br>
                                <strong>รหัสผ่านคอมพิวเตอร์</strong><br>
                                <small class="text-muted">(10 ตัวอักษร)</small>
                            </div>
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button type="button" class="btn btn-outline-success w-100 d-flex align-items-center justify-content-center" onclick="resetSpecificPassword('login')" style="min-height: 60px;">
                            <div class="text-center">
                                <i class="fas fa-sign-in-alt fa-2x mb-2"></i><br>
                                <strong>รหัสผ่านเข้าระบบ</strong><br>
                                <small class="text-muted">(12 ตัวอักษร)</small>
                            </div>
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button type="button" class="btn btn-outline-warning w-100 d-flex align-items-center justify-content-center" onclick="resetSpecificPassword('email')" style="min-height: 60px;">
                            <div class="text-center">
                                <i class="fas fa-envelope fa-2x mb-2"></i><br>
                                <strong>รหัสผ่านอีเมล</strong><br>
                                <small class="text-muted">(10 ตัวอักษร)</small>
                            </div>
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button type="button" class="btn btn-outline-danger w-100 d-flex align-items-center justify-content-center" onclick="resetAllPasswords()" style="min-height: 60px;">
                            <div class="text-center">
                                <i class="fas fa-sync fa-2x mb-2"></i><br>
                                <strong>รีเซ็ตทั้งหมด</strong><br>
                                <small class="text-muted">(ทุกรหัสผ่าน)</small>
                            </div>
                        </button>
                    </div>
                </div>
                
                <div class="mt-3">
                    <div class="alert alert-success mb-0">
                        <i class="fas fa-check-circle me-2"></i>
                        <strong>✅ แก้ไขแล้ว:</strong> ระบบจะไม่อัปเดตรหัสผ่านถ้าเว้นว่าง - ป้องกัน NULL Error
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Enhanced Employee Edit Form Loaded - Password NULL Error FIXED! ✅');
    
    // Utility Functions
    const utils = {
        showLoading: (button) => {
            button.disabled = true;
            const originalText = button.innerHTML;
            button.dataset.originalText = originalText;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>กำลังประมวลผล...';
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
        password: (length = 12) => utils.generateRandomString(length, true),
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
        expressPassword: () => utils.generateUniqueNumbers(4)
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
                        value = utils.generateRandomString(10, true);
                        break;
                    case 'login_password':
                        value = generators.password();
                        break;
                    case 'email_password':
                        value = utils.generateRandomString(10, true);
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
                        // Auto-sync login email
                        const loginEmailEl = document.getElementById('login_email');
                        if (loginEmailEl) {
                            loginEmailEl.value = value;
                        }
                    }
                    
                    // Show different messages based on target
                    let message = '';
                    switch (target) {
                        case 'email':
                            message = `✅ อัปเดต Email สำเร็จ: ${value}`;
                            break;
                        case 'express_username':
                            message = `✅ สร้าง Express Username สำเร็จ: ${value} (${value.length} ตัวอักษร)`;
                            break;
                        case 'express_password':
                            message = `✅ สร้างรหัส Express สำเร็จ: ${value} (4 ตัวเลขไม่ซ้ำ)`;
                            break;
                        case 'login_password':
                            message = `✅ สร้างรหัสผ่านเข้าระบบสำเร็จ (12 ตัวอักษร) - แก้ไข NULL Error แล้ว`;
                            break;
                        case 'computer_password':
                            message = `✅ สร้างรหัสผ่านคอมพิวเตอร์สำเร็จ (10 ตัวอักษร)`;
                            break;
                        case 'email_password':
                            message = `✅ สร้างรหัสผ่านอีเมลสำเร็จ (10 ตัวอักษร)`;
                            break;
                        default:
                            message = `✅ อัปเดต ${target} สำเร็จ`;
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
            
            if (!departmentSelect || !expressSection) return;
            
            const selectedOption = departmentSelect.options[departmentSelect.selectedIndex];
            if (!selectedOption || !selectedOption.value) {
                expressSection.style.display = 'none';
                if (expressIndicator) expressIndicator.style.display = 'none';
                return;
            }
            
            const expressEnabled = selectedOption.dataset.express === 'true';
            
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
                
                utils.showNotification(`⚡ ${selectedOption.textContent}: เปิดใช้งาน Express แล้ว`, 'success');
            } else {
                expressSection.style.display = 'none';
                if (expressIndicator) expressIndicator.style.display = 'none';
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
        
        // Handle Special Permission Switches
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
        },
        
        // Email Auto-sync Handler
        handleEmailSync: () => {
            const emailInput = document.getElementById('email');
            const loginEmailInput = document.getElementById('login_email');
            
            if (emailInput && loginEmailInput) {
                emailInput.addEventListener('input', function() {
                    loginEmailInput.value = this.value;
                });
                
                // Initial sync
                loginEmailInput.value = emailInput.value;
            }
        }
    };
    
    // Form Actions
    const formActions = {
        autoFill: async () => {
            const button = document.getElementById('autoFillBtn');
            utils.showLoading(button);
            
            try {
                console.log('🪄 Auto-filling data...');
                
                // Auto-fill username and email based on names
                const firstName = document.getElementById('first_name_en').value.trim();
                const lastName = document.getElementById('last_name_en').value.trim();
                
                if (firstName && lastName) {
                    autoGenerate.username();
                    await new Promise(resolve => setTimeout(resolve, 200));
                    autoGenerate.email();
                    
                    // Sync login email
                    const emailValue = document.getElementById('email').value;
                    const loginEmailEl = document.getElementById('login_email');
                    if (loginEmailEl && emailValue) {
                        loginEmailEl.value = emailValue;
                    }
                } else {
                    utils.showNotification('❌ กรุณากรอกชื่อ-นามสกุลภาษาอังกฤษก่อน', 'error');
                    return;
                }
                
                utils.showNotification('🪄 เติมข้อมูลอัตโนมัติสำเร็จ!', 'success');
                
            } catch (error) {
                console.error('Error in autoFill:', error);
                utils.showNotification('❌ เกิดข้อผิดพลาดในการเติมข้อมูล', 'error');
            } finally {
                utils.hideLoading(button);
            }
        },
        
        generateAll: async () => {
            const button = document.getElementById('generateAllBtn');
            utils.showLoading(button);
            
            try {
                console.log('🎯 Starting generateAll Enhanced Edit...');
                
                // ✅ FIXED: Generate passwords only if fields are CURRENTLY EMPTY (don't overwrite existing values)
                if (!document.getElementById('computer_password').value) {
                    document.getElementById('computer_password').value = utils.generateRandomString(10, true);
                }
                
                if (!document.getElementById('login_password').value) {
                    const loginPassword = generators.password();
                    document.getElementById('login_password').value = loginPassword;
                }
                
                if (!document.getElementById('email_password').value) {
                    document.getElementById('email_password').value = utils.generateRandomString(10, true);
                }
                
                // Express fields (ถ้าแสดงอยู่)
                const expressSection = document.getElementById('expressSection');
                if (expressSection && expressSection.style.display !== 'none') {
                    if (!document.getElementById('express_username').value) {
                        document.getElementById('express_username').value = generators.expressUsername();
                    }
                    if (!document.getElementById('express_password').value) {
                        document.getElementById('express_password').value = generators.expressPassword();
                    }
                }
                
                // Generate copier code if empty
                if (!document.getElementById('copier_code').value) {
                    document.getElementById('copier_code').value = generators.copierCode();
                }
                
                utils.showNotification('🎉 สร้างรหัสผ่านใหม่ทั้งหมดสำเร็จ! (แก้ไข NULL Error แล้ว)', 'success');
                
            } catch (error) {
                console.error('Error in generateAll:', error);
                utils.showNotification('❌ เกิดข้อผิดพลาดในการสร้างข้อมูล', 'error');
            } finally {
                utils.hideLoading(button);
            }
        },
        
        resetPassword: () => {
            const modal = new bootstrap.Modal(document.getElementById('resetPasswordModal'));
            modal.show();
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
                            <tr><th>รหัสผ่านคอม:</th><td>${data.computer_password ? '••••••••••' : 'ไม่เปลี่ยน'}</td></tr>
                            <tr><th>รหัสถ่ายเอกสาร:</th><td>${data.copier_code || '-'}</td></tr>
                        </table>
                        
                        <h6 class="text-info mt-3">ระบบอีเมลและ Login</h6>
                        <table class="table table-sm">
                            <tr><th>อีเมล:</th><td>${data.email || '-'}</td></tr>
                            <tr><th>รหัสผ่านอีเมล:</th><td>${data.email_password ? '••••••••••' : 'ไม่เปลี่ยน'}</td></tr>
                            <tr><th>รหัสผ่านเข้าระบบ:</th><td>${data.login_password ? '<span class="text-success">••••••••••••</span>' : '<span class="text-warning">ไม่เปลี่ยน</span>'}</td></tr>
                        </table>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-6">
                        <h6 class="text-warning">แผนกและสิทธิ์</h6>
                        <table class="table table-sm">
                            <tr><th>แผนก:</th><td>${document.querySelector('#department_id option:checked')?.textContent || '-'}</td></tr>
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
                    <strong>✅ Password Handling แก้ไขแล้ว:</strong> ระบบจะไม่อัปเดตรหัสผ่านถ้าฟิลด์เว้นว่าง
                </div>
            `;
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
                    autoGenerate.showEmailPreview();
                }, 300);
            });
        }
        
        if (lastNameEn) {
            lastNameEn.addEventListener('input', eventHandlers.handleInputValidation);
            lastNameEn.addEventListener('input', () => {
                setTimeout(() => {
                    autoGenerate.showEmailPreview();
                }, 300);
            });
        }
        
        // Email domain change handler
        const emailDomain = document.getElementById('email_domain');
        if (emailDomain) {
            emailDomain.addEventListener('change', () => {
                autoGenerate.showEmailPreview();
            });
        }
        
        // Quick Action buttons
        const autoFillBtn = document.getElementById('autoFillBtn');
        if (autoFillBtn) {
            autoFillBtn.addEventListener('click', formActions.autoFill);
        }
        
        const previewBtn = document.getElementById('previewBtn');
        if (previewBtn) {
            previewBtn.addEventListener('click', formActions.showPreview);
        }
        
        const resetPasswordBtn = document.getElementById('resetPasswordBtn');
        if (resetPasswordBtn) {
            resetPasswordBtn.addEventListener('click', formActions.resetPassword);
        }
        
        const generateAllBtn = document.getElementById('generateAllBtn');
        if (generateAllBtn) {
            generateAllBtn.addEventListener('click', formActions.generateAll);
        }
        
        // Setup permission switches
        eventHandlers.handlePermissionSwitches();
        
        // Setup email auto-sync
        eventHandlers.handleEmailSync();
        
        console.log('✅ All event listeners attached successfully (Enhanced Edit Version - Password FIXED)');
        
    } catch (error) {
        console.error('❌ Error setting up event listeners:', error);
    }
    
    // Initial setup
    setTimeout(() => {
        try {
            // Initialize department change handler
            eventHandlers.handleDepartmentChange();
            
            // Initialize email preview
            autoGenerate.showEmailPreview();
            
            // Initialize permission switches
            const vpnStatus = document.getElementById('vpnStatus');
            const printingStatus = document.getElementById('printingStatus');
            
            const vpnSwitch = document.getElementById('vpn_access');
            const printingSwitch = document.getElementById('color_printing');
            
            if (vpnStatus && vpnSwitch) {
                vpnStatus.textContent = vpnSwitch.checked ? 'อนุญาต' : 'ไม่อนุญาต';
                vpnStatus.className = vpnSwitch.checked ? 'text-success' : 'text-muted';
            }
            
            if (printingStatus && printingSwitch) {
                printingStatus.textContent = printingSwitch.checked ? 'อนุญาต' : 'ไม่อนุญาต';
                printingStatus.className = printingSwitch.checked ? 'text-success' : 'text-muted';
            }
            
            // Initialize email sync
            eventHandlers.handleEmailSync();
            
            console.log('✅ Enhanced Employee Edit Form Ready - Password NULL Error FIXED! 🎉');
            console.log('📝 Features: แสดงรหัสผ่านปัจจุบัน, แยกกลุ่มตาม create.blade.php');
            console.log('🔒 Password Display: แสดงรหัสผ่านทั้งหมดให้เห็น');
            console.log('⚡ Express v2.0: ทำงานปกติตามแผนกที่เปิดใช้งาน');
            console.log('📞 Phone Duplicates: อนุญาตให้ซ้ำได้แล้ว (แก้ไขเรียบร้อย)');
            console.log('🛡️ Password Handling: แก้ไข NULL Error แล้ว - ไม่อัปเดตถ้าเว้นว่าง');
            
        } catch (error) {
            console.error('❌ Error in initial setup:', error);
        }
    }, 1000);
});

// Global Functions for Reset Password Actions
window.resetSpecificPassword = function(type) {
    let fieldId = '';
    let length = 10;
    let label = '';
    
    switch (type) {
        case 'computer':
            fieldId = 'computer_password';
            length = 10;
            label = 'รหัสผ่านคอมพิวเตอร์';
            break;
        case 'login':
            fieldId = 'login_password';
            length = 12;
            label = 'รหัสผ่านเข้าระบบ';
            break;
        case 'email':
            fieldId = 'email_password';
            length = 10;
            label = 'รหัสผ่านอีเมล';
            break;
    }
    
    if (fieldId) {
        const chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
        let password = '';
        
        for (let i = 0; i < length; i++) {
            password += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        
        document.getElementById(fieldId).value = password;
        
        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('resetPasswordModal'));
        if (modal) {
            modal.hide();
        }
        
        // Show success notification with fixed message
        const utils = {
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
        
        utils.showNotification(`🔑 รีเซ็ต${label}สำเร็จ (${length} ตัวอักษร) - แก้ไข NULL Error แล้ว`);
    }
};

window.resetAllPasswords = function() {
    resetSpecificPassword('computer');
    setTimeout(() => resetSpecificPassword('login'), 100);
    setTimeout(() => resetSpecificPassword('email'), 200);
    
    // Close modal
    setTimeout(() => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('resetPasswordModal'));
        if (modal) {
            modal.hide();
        }
    }, 500);
};

// Modal functions
function submitForm() {
    const form = document.getElementById('employeeForm');
    if (form) {
        form.submit();
    }
}

console.log('📝 Enhanced Employee Edit Form Script Loaded (Password NULL Error FIXED! ✅)');
console.log('🔧 Available functions: resetSpecificPassword(), resetAllPasswords(), submitForm()');
console.log('⚡ Features: Auto-fill, Preview, Reset Password Modal, Email Sync');
console.log('🛡️ FIXED: Password handling - ไม่อัปเดตรหัสผ่านถ้าเว้นว่าง');
</script>
@endpush
