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
                <p class="text-muted mb-0">กรอกข้อมูลพนักงานใหม่เข้าระบบ (รองรับ Branch + Photo System)</p>
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
                        <i class="fas fa-camera me-1"></i>
                        📷 Photo System Ready
                    </span>
                </div>
            </div>
            <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>กลับ
            </a>
        </div>
    </div>
</div>

<!-- Success Alert -->
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <h6 class="fw-bold"><i class="fas fa-check-circle me-2"></i>ระบบได้รับการอัปเดต! (Photo System พร้อมใช้งาน)</h6>
    <div class="row">
        <div class="col-md-3">
            <ul class="mb-0">
                <li><strong>✅ เบอร์โทรซ้ำได้:</strong> ครอบครัว, เพื่อนร่วมงาน</li>
                <li><strong>🔒 ความปลอดภัย:</strong> Email, Username unique</li>
            </ul>
        </div>
        <div class="col-md-3">
            <ul class="mb-0">
                <li><strong>⚡ Express v2.0:</strong> ทำงานปกติ</li>
                <li><strong>🏢 Branch System:</strong> เลือกสาขาได้</li>
            </ul>
        </div>
        <div class="col-md-3">
            <ul class="mb-0">
                <li><strong>📷 Photo Upload:</strong> รองรับรูปภาพแล้ว</li>
                <li><strong>🎨 ITMS Theme:</strong> ธีมสีแดง-ส้ม</li>
            </ul>
        </div>
        <div class="col-md-3">
            <ul class="mb-0">
                <li><strong>💾 File Support:</strong> JPG, PNG, GIF</li>
                <li><strong>🛡️ Security:</strong> Max 2MB, Validation</li>
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
                    <button type="button" class="btn w-100 flex-fill d-flex align-items-center justify-content-center" id="testPhotoBtn" style="min-height: 45px; background: linear-gradient(45deg, #B54544, #E6952A); color: white; border: none;">
                        <span class="d-flex align-items-center">
                            <i class="fas fa-camera me-2"></i>
                            <span class="d-none d-lg-inline">ทดสอบ Photo</span>
                            <span class="d-lg-none">Photo</span>
                        </span>
                    </button>
                    <div class="form-text mt-2">
                        <small class="text-success">✅ Photo System Ready</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ✅ FIXED: Form with proper enctype for photo upload -->
<form id="employeeForm" action="{{ route('employees.store') }}" method="POST" enctype="multipart/form-data">
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

    <!-- ✅ ENHANCED: รูปภาพพนักงาน - Photo System -->
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <div class="border border-2 rounded-circle d-flex align-items-center justify-content-center me-3 bg-light" 
                     style="width: 45px; height: 45px; min-width: 45px; background: linear-gradient(45deg, #B54544, #E6952A) !important;">
                    <i class="fas fa-camera text-white" style="font-size: 20px;"></i>
                </div>
                <div>
                    <h5 class="card-title mb-0">
                        รูปภาพพนักงาน
                        <span class="badge bg-success ms-2">
                            <i class="fas fa-check me-1"></i>Ready
                        </span>
                    </h5>
                    <small class="text-muted">อัปโหลดรูปโปรไฟล์พนักงาน (ไม่บังคับ)</small>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <!-- Photo Upload Section -->
                <div class="col-md-6">
                    <label for="photo" class="form-label">
                        รูปภาพ
                        <span class="badge bg-info ms-2">ไม่บังคับ</span>
                        <span class="badge bg-secondary ms-1">Max 2MB</span>
                    </label>
                    
                    <!-- Drag & Drop Area -->
                    <div class="photo-upload-area" id="photoUploadArea">
                        <input type="file" 
                               class="form-control @error('photo') is-invalid @enderror" 
                               id="photo" 
                               name="photo" 
                               accept="image/*"
                               style="display: none;">
                        
                        <div class="upload-placeholder" id="uploadPlaceholder">
                            <div class="text-center py-4">
                                <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                <h6 class="text-muted">ลากและวางรูปภาพที่นี่</h6>
                                <p class="text-muted mb-3">หรือ</p>
                                <button type="button" class="btn btn-outline-primary" id="selectPhotoBtn">
                                    <i class="fas fa-image me-2"></i>เลือกรูปภาพ
                                </button>
                            </div>
                            <div class="mt-3 text-center">
                                <small class="text-muted">
                                    รองรับ: JPG, PNG, GIF | ขนาดสูงสุด: 2MB
                                </small>
                            </div>
                        </div>
                        
                        <!-- Photo Preview -->
                        <div class="photo-preview" id="photoPreview" style="display: none;">
                            <div class="position-relative">
                                <img id="previewImage" src="" alt="Preview" class="img-fluid rounded" style="max-height: 200px; width: 100%; object-fit: cover;">
                                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2" id="removePhotoBtn">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div class="mt-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge bg-success">
                                            <i class="fas fa-check me-1"></i>รูปภาพพร้อม
                                        </span>
                                        <small class="text-muted ms-2" id="photoInfo"></small>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="changePhotoBtn">
                                        <i class="fas fa-exchange-alt me-1"></i>เปลี่ยนรูป
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @error('photo')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- Avatar Preview Section -->
                <div class="col-md-6">
                    <label class="form-label">ตัวอย่างการแสดงผล</label>
                    
                    <div class="card border-light">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <i class="fas fa-user-circle me-2"></i>Avatar Preview
                            </h6>
                        </div>
                        <div class="card-body text-center">
                            <!-- Avatar Display -->
                            <div class="mb-3">
                                <div class="avatar-preview-container">
                                    <img id="avatarPreview" 
                                         src="https://ui-avatars.com/api/?name=Employee&size=120&background=B54544&color=ffffff&bold=true&format=png" 
                                         alt="Avatar Preview" 
                                         class="rounded-circle avatar-preview" 
                                         style="width: 120px; height: 120px; object-fit: cover; border: 3px solid #e9ecef;">
                                </div>
                            </div>
                            
                            <!-- Employee Info Preview -->
                            <div class="employee-info-preview">
                                <h6 class="mb-1 text-primary" id="employeeNamePreview">
                                    <span id="previewFirstNameTh">-</span> <span id="previewLastNameTh">-</span>
                                </h6>
                                <small class="text-muted" id="employeeCodePreview">รหัสพนักงาน: -</small>
                            </div>
                            
                            <hr>
                            
                            <!-- Photo Status -->
                            <div class="photo-status">
                                <div id="hasPhotoStatus" style="display: none;">
                                    <span class="badge bg-success">
                                        <i class="fas fa-camera me-1"></i>มีรูปภาพ
                                    </span>
                                    <div class="mt-2">
                                        <small class="text-success">
                                            <i class="fas fa-check me-1"></i>
                                            จะแสดงรูปที่อัปโหลด
                                        </small>
                                    </div>
                                </div>
                                <div id="noPhotoStatus">
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-user-circle me-1"></i>Avatar อัตโนมัติ
                                    </span>
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle me-1"></i>
                                            สร้างจากชื่อพนักงาน
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Photo Upload Tips -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="alert alert-light border" style="border-color: #B54544 !important;">
                        <div class="row">
                            <div class="col-md-4">
                                <h6 class="text-success">
                                    <i class="fas fa-lightbulb me-2"></i>คำแนะนำ
                                </h6>
                                <ul class="mb-0 small">
                                    <li>ใช้รูปหน้าตรง ชัดเจน</li>
                                    <li>ขนาดแนะนำ: 400x400 px</li>
                                    <li>พื้นหลังเรียบร้อย</li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <h6 class="text-info">
                                    <i class="fas fa-file-image me-2"></i>รูปแบบไฟล์
                                </h6>
                                <ul class="mb-0 small">
                                    <li>JPG, JPEG (แนะนำ)</li>
                                    <li>PNG (รองรับพื้นหลังโปร่งใส)</li>
                                    <li>GIF (รองรับภาพเคลื่อนไหว)</li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <h6 class="text-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>ข้อจำกัด
                                </h6>
                                <ul class="mb-0 small">
                                    <li>ขนาดไฟล์สูงสุด: 2MB</li>
                                    <li>ไม่อัปโหลด = Avatar อัตโนมัติ</li>
                                    <li>สามารถแก้ไขได้ภายหลัง</li>
                                </ul>
                            </div>
                        </div>
                    </div>
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
                    </div>
                    @error('username')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Computer Password -->
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
                        รหัสผ่านสำหรับเปิดคอมพิวเตอร์ (แสดงให้เห็นได้ทั้งหมด)
                    </div>
                    @error('computer_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Copier Code -->
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

    <!-- ระบบอีเมลและ Login -->
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <div class="border border-2 border-info rounded-circle d-flex align-items-center justify-content-center me-3 bg-light" style="width: 45px; height: 45px; min-width: 45px;">
                    <i class="fas fa-envelope text-info" style="font-size: 20px;"></i>
                </div>
                <div>
                    <h5 class="card-title mb-0">ระบบอีเมลและ Login</h5>
                    <small class="text-muted">อีเมลและรหัสผ่าน แยกระบบแล้ว</small>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-3">
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

                <!-- Login Password -->
                <div class="col-md-4">
                    <label for="login_password" class="form-label">
                        Password เข้าระบบ <span class="text-danger">*</span>
                        <span class="badge bg-success ms-2">12 ตัวอักษร</span>
                    </label>
                    <div class="input-group">
                        <input type="text" 
                               class="form-control @error('password') is-invalid @enderror" 
                               id="login_password" 
                               name="login_password" 
                               value="{{ old('login_password') }}"
                               placeholder="รหัสผ่านเข้าระบบ"
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
                            รหัสผ่านเข้าระบบ (12 ตัวอักษร)
                        </span>
                    </div>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Hidden password field -->
                <input type="hidden" id="password" name="password" value="{{ old('password') }}">
            </div>
        </div>
    </div>

    <!-- แผนก, สาขา และสิทธิ์ -->
    <div class="card mb-4">
        <div class="card-header" style="background: linear-gradient(45deg, #B54544, #E6952A); color: white;">
            <div class="d-flex align-items-center">
                <div class="border border-2 border-white rounded-circle d-flex align-items-center justify-content-center me-3 bg-light" style="width: 45px; height: 45px; min-width: 45px;">
                    <i class="fas fa-building" style="font-size: 20px; color: #B54544;"></i>
                </div>
                <div>
                    <h5 class="card-title mb-0" style="color: white; text-shadow: 1px 1px 2px rgba(0,0,0,0.3);">
                        แผนก, สาขา และสิทธิ์
                    </h5>
                    <small style="color: rgba(255,255,255,0.9);">
                        แผนกการทำงาน, สาขาที่สังกัด และสิทธิ์การใช้งาน
                    </small>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <!-- Branch Selection -->
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
                            @if(isset($branches))
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }} ({{ $branch->code ?? 'N/A' }})
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        <button type="button" class="btn btn-outline-info" id="refreshBranchBtn" title="รีเฟรชรายการสาขา">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                    <div class="form-text">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            เลือกสาขาที่พนักงานสังกัด (ไม่บังคับ)
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
                        @if(isset($departments))
                            @foreach($departments as $department)
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
                <div class="col-md-12">
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

    <!-- โปรแกรม Express -->
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
                    <small class="text-muted">รองรับแผนกที่เปิดใช้งาน Express</small>
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-outline-warning" id="generateExpressBtn">
                <i class="fas fa-bolt me-1"></i>สร้าง Express
            </button>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <!-- Express Username -->
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
                               placeholder="จะสร้างจากชื่อ EN" 
                               maxlength="7">
                        <button type="button" class="btn btn-outline-primary" data-target="express_username">
                            <i class="fas fa-bolt"></i>
                        </button>
                    </div>
                    <div class="form-text">
                        ใช้ชื่อภาษาอังกฤษได้ 1-7 ตัวอักษร
                    </div>
                    @error('express_username')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Express Password -->
                <div class="col-md-6">
                    <label for="express_password" class="form-label">
                        Password โปรแกรม Express
                        <span class="badge bg-success ms-2">4 ตัวเลข</span>
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
                        4 ตัวเลขที่ไม่ซ้ำกัน (เช่น 1234, 5678)
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
                    <i class="fas fa-plus me-2"></i>สร้างพนักงาน (รวมรูปภาพ)
                </button>
            </div>
        </div>
    </div>
</form>

<!-- Photo Test Modal -->
<div class="modal fade" id="photoTestModal" tabindex="-1" aria-labelledby="photoTestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(45deg, #B54544, #E6952A); color: white;">
                <h5 class="modal-title" id="photoTestModalLabel">
                    <i class="fas fa-camera me-2"></i>ทดสอบ Photo System ✅
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-success">
                    <h6><i class="fas fa-check-circle me-2"></i>Photo Upload System พร้อมใช้งาน!</h6>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <h6>✅ ฟีเจอร์ที่พร้อม:</h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success me-2"></i>Drag & Drop Upload</li>
                            <li><i class="fas fa-check text-success me-2"></i>Live Preview</li>
                            <li><i class="fas fa-check text-success me-2"></i>File Validation</li>
                            <li><i class="fas fa-check text-success me-2"></i>Default Avatar</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>🎯 รองรับไฟล์:</h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-file-image text-info me-2"></i>JPG, JPEG, PNG, GIF</li>
                            <li><i class="fas fa-weight text-warning me-2"></i>ขนาดสูงสุด: 2MB</li>
                            <li><i class="fas fa-shield-alt text-success me-2"></i>Auto Validation</li>
                            <li><i class="fas fa-trash text-danger me-2"></i>Auto Cleanup</li>
                        </ul>
                    </div>
                </div>
                
                <hr>
                
                <div class="alert alert-info mb-0">
                    <h6><i class="fas fa-lightbulb me-2"></i>วิธีใช้:</h6>
                    <ol class="mb-0">
                        <li>กรอกข้อมูลพนักงานในฟอร์ม</li>
                        <li>ลากและวางรูปภาพใน Photo Section</li>
                        <li>ดู Live Preview ที่แสดงขึ้น</li>
                        <li>กดปุ่ม "สร้างพนักงาน" เพื่อบันทึก</li>
                    </ol>
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

@push('styles')
<style>
/* ✅ Photo Upload Styles */
.photo-upload-area {
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    background: #f8f9fa;
    transition: all 0.3s ease;
    min-height: 200px;
    position: relative;
}

.photo-upload-area:hover {
    border-color: #B54544;
    background: rgba(181, 69, 68, 0.05);
}

.photo-upload-area.dragover {
    border-color: #E6952A;
    background: rgba(230, 149, 42, 0.1);
    transform: scale(1.02);
}

.upload-placeholder {
    padding: 20px;
}

.photo-preview {
    padding: 15px;
}

.avatar-preview {
    transition: all 0.3s ease;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.avatar-preview:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 12px rgba(0,0,0,0.15);
}

.avatar-preview-container {
    position: relative;
    display: inline-block;
}

.employee-info-preview {
    margin-top: 10px;
}

.photo-status {
    margin-top: 10px;
}

/* Enhanced button styles */
#selectPhotoBtn, #changePhotoBtn {
    transition: all 0.3s ease;
}

#selectPhotoBtn:hover, #changePhotoBtn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

#removePhotoBtn {
    opacity: 0.8;
    transition: all 0.3s ease;
}

#removePhotoBtn:hover {
    opacity: 1;
    transform: scale(1.1);
}

/* File drag feedback */
.photo-upload-area.drag-active {
    border-color: #28a745;
    background: rgba(40, 167, 69, 0.1);
}

/* ITMS Theme Integration */
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

/* Form section headers */
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
    .avatar-preview {
        width: 80px !important;
        height: 80px !important;
    }
    
    .photo-upload-area {
        min-height: 150px;
    }
    
    .upload-placeholder {
        padding: 15px;
    }
    
    .upload-placeholder h6 {
        font-size: 0.9rem;
    }
    
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
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Employee Create Form Loaded - Photo System Ready');
    
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
    
    // ✅ Photo Upload System
    const photoUpload = {
        init: () => {
            const photoInput = document.getElementById('photo');
            const uploadArea = document.getElementById('photoUploadArea');
            const uploadPlaceholder = document.getElementById('uploadPlaceholder');
            const photoPreview = document.getElementById('photoPreview');
            const previewImage = document.getElementById('previewImage');
            const selectPhotoBtn = document.getElementById('selectPhotoBtn');
            const changePhotoBtn = document.getElementById('changePhotoBtn');
            const removePhotoBtn = document.getElementById('removePhotoBtn');
            const photoInfo = document.getElementById('photoInfo');
            const avatarPreview = document.getElementById('avatarPreview');
            const hasPhotoStatus = document.getElementById('hasPhotoStatus');
            const noPhotoStatus = document.getElementById('noPhotoStatus');

            if (!photoInput || !uploadArea) {
                console.warn('⚠️ Photo upload elements not found');
                return;
            }

            // Event listeners
            selectPhotoBtn?.addEventListener('click', () => photoInput.click());
            changePhotoBtn?.addEventListener('click', () => photoInput.click());
            photoInput.addEventListener('change', photoUpload.handleFileSelect);
            removePhotoBtn?.addEventListener('click', photoUpload.removePhoto);
            
            // Drag and drop
            uploadArea.addEventListener('dragover', photoUpload.handleDragOver);
            uploadArea.addEventListener('dragleave', photoUpload.handleDragLeave);
            uploadArea.addEventListener('drop', photoUpload.handleDrop);
            
            console.log('📷 Photo upload system initialized');
        },

        handleFileSelect: (event) => {
            const file = event.target.files[0];
            if (file) {
                photoUpload.processFile(file);
            }
        },
        
        handleDragOver: (event) => {
            event.preventDefault();
            document.getElementById('photoUploadArea')?.classList.add('dragover');
        },
        
        handleDragLeave: (event) => {
            event.preventDefault();
            document.getElementById('photoUploadArea')?.classList.remove('dragover');
        },
        
        handleDrop: (event) => {
            event.preventDefault();
            const uploadArea = document.getElementById('photoUploadArea');
            uploadArea?.classList.remove('dragover');
            
            const files = event.dataTransfer.files;
            if (files.length > 0) {
                const file = files[0];
                if (file.type.startsWith('image/')) {
                    document.getElementById('photo').files = files;
                    photoUpload.processFile(file);
                } else {
                    utils.showNotification('❌ กรุณาเลือกไฟล์รูปภาพเท่านั้น', 'error');
                }
            }
        },
        
        processFile: (file) => {
            // Validate file size (2MB)
            if (file.size > 2048 * 1024) {
                utils.showNotification('❌ ไฟล์รูปภาพมีขนาดใหญ่เกิน 2MB', 'error');
                document.getElementById('photo').value = '';
                return;
            }
            
            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (!allowedTypes.includes(file.type)) {
                utils.showNotification('❌ รองรับเฉพาะไฟล์ JPG, PNG, GIF เท่านั้น', 'error');
                document.getElementById('photo').value = '';
                return;
            }
            
            // Create preview
            const reader = new FileReader();
            reader.onload = function(e) {
                const previewImage = document.getElementById('previewImage');
                const avatarPreview = document.getElementById('avatarPreview');
                
                if (previewImage) previewImage.src = e.target.result;
                if (avatarPreview) avatarPreview.src = e.target.result;
                
                // Update UI
                const uploadPlaceholder = document.getElementById('uploadPlaceholder');
                const photoPreview = document.getElementById('photoPreview');
                const hasPhotoStatus = document.getElementById('hasPhotoStatus');
                const noPhotoStatus = document.getElementById('noPhotoStatus');
                const photoInfo = document.getElementById('photoInfo');
                
                if (uploadPlaceholder) uploadPlaceholder.style.display = 'none';
                if (photoPreview) photoPreview.style.display = 'block';
                if (hasPhotoStatus) hasPhotoStatus.style.display = 'block';
                if (noPhotoStatus) noPhotoStatus.style.display = 'none';
                
                // Update file info
                const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
                if (photoInfo) photoInfo.textContent = `${file.name} (${fileSizeMB} MB)`;
                
                utils.showNotification(`✅ อัปโหลดรูปภาพสำเร็จ: ${file.name}`, 'success');
                
                console.log('✅ Photo preview updated:', {
                    name: file.name,
                    size: fileSizeMB + ' MB',
                    type: file.type
                });
            };
            reader.readAsDataURL(file);
        },
        
        removePhoto: () => {
            const photoInput = document.getElementById('photo');
            const uploadPlaceholder = document.getElementById('uploadPlaceholder');
            const photoPreview = document.getElementById('photoPreview');
            const hasPhotoStatus = document.getElementById('hasPhotoStatus');
            const noPhotoStatus = document.getElementById('noPhotoStatus');
            
            if (photoInput) photoInput.value = '';
            if (uploadPlaceholder) uploadPlaceholder.style.display = 'block';
            if (photoPreview) photoPreview.style.display = 'none';
            if (hasPhotoStatus) hasPhotoStatus.style.display = 'none';
            if (noPhotoStatus) noPhotoStatus.style.display = 'block';
            
            // Reset to default avatar
            photoUpload.updateAvatarPreview();
            
            utils.showNotification('🗑️ ลบรูปภาพแล้ว', 'success');
            console.log('🗑️ Photo removed');
        },

        updateAvatarPreview: () => {
            const firstNameTh = document.getElementById('first_name_th')?.value || '';
            const lastNameTh = document.getElementById('last_name_th')?.value || '';
            const firstNameEn = document.getElementById('first_name_en')?.value || '';
            const lastNameEn = document.getElementById('last_name_en')?.value || '';
            const employeeCode = document.getElementById('employee_code')?.value || '';
            
            // Update name preview
            const previewFirstNameTh = document.getElementById('previewFirstNameTh');
            const previewLastNameTh = document.getElementById('previewLastNameTh');
            const employeeCodePreview = document.getElementById('employeeCodePreview');
            
            if (previewFirstNameTh) previewFirstNameTh.textContent = firstNameTh || '-';
            if (previewLastNameTh) previewLastNameTh.textContent = lastNameTh || '-';
            if (employeeCodePreview) employeeCodePreview.textContent = 'รหัสพนักงาน: ' + (employeeCode || '-');
            
            // Generate initials for avatar
            let initials = '';
            if (firstNameEn && lastNameEn) {
                initials = firstNameEn.charAt(0) + lastNameEn.charAt(0);
            } else if (firstNameEn) {
                initials = firstNameEn.charAt(0) + firstNameEn.charAt(1);
            } else if (employeeCode) {
                initials = employeeCode.slice(-2);
            } else {
                initials = 'EM';
            }
            
            // Generate avatar URL with ITMS colors
            const colors = ['B54544', 'E6952A', '0d6efd', '198754', '6f42c1'];
            const colorIndex = (employeeCode.length + firstNameEn.length) % colors.length;
            const backgroundColor = colors[colorIndex];
            
            const avatarUrl = `https://ui-avatars.com/api/?name=${encodeURIComponent(initials)}&size=120&background=${backgroundColor}&color=ffffff&bold=true&format=png`;
            
            const avatarPreview = document.getElementById('avatarPreview');
            const photoInput = document.getElementById('photo');
            
            if (avatarPreview && photoInput && !photoInput.files.length) {
                avatarPreview.src = avatarUrl;
            }
        }
    };
    
    // Enhanced Generator Functions
    const generators = {
        employeeCode: () => `EMP${utils.generateRandomNumber(3)}`,
        keycardId: () => `KC${utils.generateRandomNumber(6)}`,
        username: () => {
            const firstName = document.getElementById('first_name_en')?.value.trim();
            const englishRegex = /^[a-zA-Z\s]+$/;
            
            if (firstName && englishRegex.test(firstName)) {
                return firstName.toLowerCase();
            }
            return '';
        },
        email: () => {
            const firstName = document.getElementById('first_name_en')?.value.trim();
            const lastName = document.getElementById('last_name_en')?.value.trim();
            const domain = document.getElementById('email_domain')?.value;
            const englishRegex = /^[a-zA-Z\s]+$/;
            
            if (firstName && lastName && domain && englishRegex.test(firstName) && englishRegex.test(lastName)) {
                return `${firstName.toLowerCase()}.${lastName.charAt(0).toLowerCase()}@${domain}`;
            }
            return '';
        },
        password: () => utils.generateRandomString(12, true),
        copierCode: () => utils.generateRandomNumber(4),
        expressUsername: () => {
            const firstName = document.getElementById('first_name_en')?.value.trim().toLowerCase();
            if (firstName && firstName.length > 0) {
                return firstName.length <= 7 ? firstName : firstName.substring(0, 7);
            }
            return utils.generateRandomString(5, false).toLowerCase();
        },
        expressPassword: () => utils.generateUniqueNumbers(4),
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
                        // Auto-sync to hidden password field
                        const passwordField = document.getElementById('password');
                        if (passwordField) {
                            passwordField.value = value;
                        }
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
                    
                    let message = `✅ สร้าง ${target} สำเร็จ: ${value}`;
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
            const expressIndicator = document.getElementById('expressIndicator');
            
            if (!departmentSelect || !expressSection) return;
            
            const selectedOption = departmentSelect.options[departmentSelect.selectedIndex];
            if (!selectedOption || !selectedOption.value) {
                expressSection.style.display = 'none';
                if (expressIndicator) expressIndicator.style.display = 'none';
                return;
            }
            
            const expressEnabled = selectedOption.dataset.express === 'true';
            const departmentName = selectedOption.textContent;
            
            if (expressEnabled) {
                expressSection.style.display = 'block';
                if (expressIndicator) expressIndicator.style.display = 'inline-block';
                
                // Auto-generate Express fields if name is available and fields are empty
                const firstName = document.getElementById('first_name_en')?.value.trim();
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
        
        handleInputValidation: (event) => {
            const input = event.target;
            const englishRegex = /^[a-zA-Z\s]*$/;
            
            if (input.id === 'first_name_en' || input.id === 'last_name_en') {
                if (!englishRegex.test(input.value)) {
                    input.style.borderColor = '#dc3545';
                    input.style.boxShadow = '0 0 0 0.2rem rgba(220, 53, 69, 0.25)';
                    
                    // Remove non-English characters
                    input.value = input.value.replace(/[^a-zA-Z\s]/g, '');
                } else {
                    input.style.borderColor = '';
                    input.style.boxShadow = '';
                }
            }
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
                const usernameEl = document.getElementById('username');
                if (usernameEl) usernameEl.value = username;
                console.log('✅ Username generated:', username);
            }
        },
        
        email: () => {
            const email = generators.email();
            if (email) {
                const emailEl = document.getElementById('email');
                if (emailEl) emailEl.value = email;
                autoGenerate.showEmailPreview();
                console.log('✅ Email generated:', email);
            }
        },
        
        showEmailPreview: () => {
            const firstName = document.getElementById('first_name_en')?.value.trim();
            const lastName = document.getElementById('last_name_en')?.value.trim();
            const domain = document.getElementById('email_domain')?.value;
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
    
    // Form Actions
    const formActions = {
        generateAll: async () => {
            const button = document.getElementById('generateAllBtn');
            if (!button) return;
            
            utils.showLoading(button);
            
            try {
                console.log('🎯 Starting generateAll...');
                
                // Generate basic codes
                const employeeCodeEl = document.getElementById('employee_code');
                const keycardIdEl = document.getElementById('keycard_id');
                
                if (employeeCodeEl) employeeCodeEl.value = generators.employeeCode();
                if (keycardIdEl) keycardIdEl.value = generators.keycardId();
                
                // Username และ Email generation
                if (document.getElementById('first_name_en')?.value) {
                    autoGenerate.username();
                    await new Promise(resolve => setTimeout(resolve, 200));
                    
                    if (document.getElementById('last_name_en')?.value) {
                        autoGenerate.email();
                    }
                } else {
                    utils.showNotification('❌ กรุณากรอกชื่อภาษาอังกฤษก่อน จึงจะสร้าง Username และ Email ได้', 'error');
                    return;
                }
                
                // Generate passwords
                const computerPasswordEl = document.getElementById('computer_password');
                const loginPasswordEl = document.getElementById('login_password');
                const passwordEl = document.getElementById('password');
                
                const computerPassword = utils.generateRandomString(10, true);
                const loginPassword = generators.password(); // 12 ตัวอักษร
                
                if (computerPasswordEl) computerPasswordEl.value = computerPassword;
                if (loginPasswordEl) loginPasswordEl.value = loginPassword;
                if (passwordEl) passwordEl.value = loginPassword; // Sync hidden field
                
                // Express fields (ถ้าแสดงอยู่)
                const expressSection = document.getElementById('expressSection');
                if (expressSection && expressSection.style.display !== 'none') {
                    const expressUsernameEl = document.getElementById('express_username');
                    const expressPasswordEl = document.getElementById('express_password');
                    
                    if (expressUsernameEl) expressUsernameEl.value = generators.expressUsername();
                    if (expressPasswordEl) expressPasswordEl.value = generators.expressPassword();
                }
                
                utils.showNotification('🎉 สร้างข้อมูลทั้งหมดสำเร็จ! (รวม Photo System)', 'success');
                
            } catch (error) {
                console.error('Error in generateAll:', error);
                utils.showNotification('❌ เกิดข้อผิดพลาดในการสร้างข้อมูล', 'error');
            } finally {
                utils.hideLoading(button);
            }
        },
        
        clearAll: () => {
            if (confirm('ต้องการล้างข้อมูลทั้งหมดหรือไม่?')) {
                const form = document.getElementById('employeeForm');
                if (form) form.reset();
                
                // Reset photo upload
                photoUpload.removePhoto();
                
                // Reset UI elements
                const emailPreview = document.getElementById('emailPreview');
                const expressSection = document.getElementById('expressSection');
                const expressIndicator = document.getElementById('expressIndicator');
                const vpnStatus = document.getElementById('vpnStatus');
                const printingStatus = document.getElementById('printingStatus');
                
                if (emailPreview) emailPreview.style.display = 'none';
                if (expressSection) expressSection.style.display = 'none';
                if (expressIndicator) expressIndicator.style.display = 'none';
                if (vpnStatus) vpnStatus.textContent = 'ไม่อนุญาต';
                if (printingStatus) printingStatus.textContent = 'ไม่อนุญาต';
                
                // Re-generate initial codes
                setTimeout(() => {
                    const employeeCodeEl = document.getElementById('employee_code');
                    const keycardIdEl = document.getElementById('keycard_id');
                    
                    if (employeeCodeEl) employeeCodeEl.value = generators.employeeCode();
                    if (keycardIdEl) keycardIdEl.value = generators.keycardId();
                }, 100);
                
                utils.showNotification('🗑️ ล้างข้อมูลทั้งหมดแล้ว', 'success');
            }
        },
        
        showPhotoTest: () => {
            const modal = new bootstrap.Modal(document.getElementById('photoTestModal'));
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
                    photoUpload.updateAvatarPreview();
                }, 300);
            });
        }
        
        if (lastNameEn) {
            lastNameEn.addEventListener('input', eventHandlers.handleInputValidation);
            lastNameEn.addEventListener('input', () => {
                setTimeout(() => {
                    autoGenerate.email();
                    autoGenerate.showEmailPreview();
                    photoUpload.updateAvatarPreview();
                }, 300);
            });
        }
        
        // Thai name handlers for avatar preview
        const firstNameTh = document.getElementById('first_name_th');
        const lastNameTh = document.getElementById('last_name_th');
        const employeeCode = document.getElementById('employee_code');
        
        [firstNameTh, lastNameTh, employeeCode].forEach(element => {
            if (element) {
                element.addEventListener('input', photoUpload.updateAvatarPreview);
            }
        });
        
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
        
        const testPhotoBtn = document.getElementById('testPhotoBtn');
        if (testPhotoBtn) {
            testPhotoBtn.addEventListener('click', formActions.showPhotoTest);
        }
        
        // Generate Computer System Button
        const generateComputerBtn = document.getElementById('generateComputerBtn');
        if (generateComputerBtn) {
            generateComputerBtn.addEventListener('click', async () => {
                const button = generateComputerBtn;
                utils.showLoading(button);
                
                try {
                    const username = generators.username();
                    const computerPassword = utils.generateRandomString(10, true);
                    const copierCode = generators.copierCode();
                    
                    if (username) {
                        const usernameEl = document.getElementById('username');
                        if (usernameEl) usernameEl.value = username;
                    }
                    if (computerPassword) {
                        const computerPasswordEl = document.getElementById('computer_password');
                        if (computerPasswordEl) computerPasswordEl.value = computerPassword;
                    }
                    if (copierCode) {
                        const copierCodeEl = document.getElementById('copier_code');
                        if (copierCodeEl) copierCodeEl.value = copierCode;
                    }
                    
                    utils.showNotification('🖥️ สร้างระบบคอมพิวเตอร์สำเร็จ!', 'success');
                } catch (error) {
                    utils.showNotification('❌ เกิดข้อผิดพลาด', 'error');
                } finally {
                    utils.hideLoading(button);
                }
            });
        }
        
        // Generate Express Button
        const generateExpressBtn = document.getElementById('generateExpressBtn');
        if (generateExpressBtn) {
            generateExpressBtn.addEventListener('click', async () => {
                const button = generateExpressBtn;
                utils.showLoading(button);
                
                try {
                    const expressUsername = generators.expressUsername();
                    const expressPassword = generators.expressPassword();
                    
                    if (expressUsername) {
                        const expressUsernameEl = document.getElementById('express_username');
                        if (expressUsernameEl) expressUsernameEl.value = expressUsername;
                    }
                    if (expressPassword) {
                        const expressPasswordEl = document.getElementById('express_password');
                        if (expressPasswordEl) expressPasswordEl.value = expressPassword;
                    }
                    
                    utils.showNotification('⚡ สร้าง Express Credentials สำเร็จ!', 'success');
                } catch (error) {
                    utils.showNotification('❌ เกิดข้อผิดพลาด', 'error');
                } finally {
                    utils.hideLoading(button);
                }
            });
        }
        
        // Password field listeners for sync
        const loginPasswordField = document.getElementById('login_password');
        if (loginPasswordField) {
            loginPasswordField.addEventListener('input', () => {
                const passwordField = document.getElementById('password');
                if (passwordField) {
                    passwordField.value = loginPasswordField.value;
                }
            });
        }
        
        // Setup permission switches
        eventHandlers.handlePermissionSwitches();
        
        // Initialize photo upload system
        photoUpload.init();
        
        console.log('✅ All event listeners attached successfully');
        
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
            
            // Initialize handlers
            eventHandlers.handleDepartmentChange();
            autoGenerate.showEmailPreview();
            photoUpload.updateAvatarPreview();
            
            console.log('✅ Employee Create Form Ready - Photo System Enabled');
            console.log('📷 Photo Upload: Ready');
            console.log('🔒 Security: Form validation enabled');
            console.log('⚡ Express v2.0: Working');
            console.log('🎨 ITMS Theme: Perfect');
            console.log('🔧 Form Features:');
            console.log('  - Photo Upload with Drag & Drop');
            console.log('  - Live Avatar Preview');
            console.log('  - File Validation (2MB, JPG/PNG/GIF)');
            console.log('  - Auto-cleanup on errors');
            console.log('  - Transaction safety');
            
        } catch (error) {
            console.error('❌ Error in initial setup:', error);
        }
    }, 1000);
    
    // Form submission validation
    const submitBtn = document.getElementById('submitBtn');
    if (submitBtn) {
        submitBtn.addEventListener('click', (e) => {
            const form = document.getElementById('employeeForm');
            const photoInput = document.getElementById('photo');
            
            if (form && photoInput && photoInput.files.length > 0) {
                const file = photoInput.files[0];
                
                // Final validation before submit
                if (file.size > 2048 * 1024) {
                    e.preventDefault();
                    utils.showNotification('❌ ไฟล์รูปภาพมีขนาดใหญ่เกิน 2MB', 'error');
                    return false;
                }
                
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                if (!allowedTypes.includes(file.type)) {
                    e.preventDefault();
                    utils.showNotification('❌ รองรับเฉพาะไฟล์ JPG, PNG, GIF เท่านั้น', 'error');
                    return false;
                }
                
                console.log('✅ Form submission with photo:', {
                    filename: file.name,
                    size: (file.size / (1024 * 1024)).toFixed(2) + ' MB',
                    type: file.type
                });
            }
            
            // Show loading on submit button
            utils.showLoading(submitBtn);
        });
    }
});

console.log('📝 Employee Create Form Script Loaded - Photo System Ready');
console.log('🔧 Available functions: Photo Upload, Auto-fill, Preview, Express v2.0');
console.log('📷 Photo Features: Drag & Drop, Validation, Preview, Cleanup');
console.log('🎨 ITMS Theme: Red-Orange Colors Perfect');
console.log('🔑 All systems ready for employee creation with photos!');
</script>
@endpush
