@extends('layouts.app')

@section('title', 'เพิ่มแผนกใหม่')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('departments.index') }}">จัดการแผนก</a></li>
    <li class="breadcrumb-item active">เพิ่มแผนกใหม่</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-primary fw-bold">
                    <i class="fas fa-plus me-2"></i>เพิ่มแผนกใหม่
                </h1>
                <p class="text-muted mb-0">เพิ่มแผนกงานใหม่เข้าสู่ระบบ</p>
            </div>
            <a href="{{ route('departments.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>กลับ
            </a>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row text-center">
            <div class="col-md-4 mb-2">
                <button type="button" class="btn btn-outline-primary w-100" id="generateCodeBtn">
                    <i class="fas fa-magic me-1"></i>สร้างรหัสอัตโนมัติ
                </button>
                <div class="form-text mt-1">
                    <small class="text-muted">จากชื่อแผนก</small>
                </div>
            </div>
            <div class="col-md-4 mb-2">
                <button type="button" class="btn btn-outline-info w-100" id="previewBtn">
                    <i class="fas fa-eye me-1"></i>ดูตัวอย่าง
                </button>
                <div class="form-text mt-1">
                    <small class="text-muted">ดูก่อนบันทึก</small>
                </div>
            </div>
            <div class="col-md-4 mb-2">
                <button type="button" class="btn btn-outline-warning w-100" id="clearAllBtn">
                    <i class="fas fa-trash me-1"></i>ล้างทั้งหมด
                </button>
                <div class="form-text mt-1">
                    <small class="text-muted">เริ่มใหม่</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Form -->
<form id="departmentForm" action="{{ route('departments.store') }}" method="POST">
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
    
    <!-- Basic Information -->
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <div class="border border-2 border-secondary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; background: #f8f9fa;">
                    <i class="fas fa-building text-secondary" style="font-size: 18px;"></i>
                </div>
                <div>
                    <h5 class="card-title mb-0">ข้อมูลพื้นฐาน</h5>
                    <small class="text-muted">ข้อมูลของแผนกใหม่</small>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <!-- Department Name -->
                <div class="col-md-8">
                    <label for="name" class="form-label">
                        ชื่อแผนก <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                           id="name" name="name" value="{{ old('name') }}" 
                           placeholder="เช่น แผนกเทคโนโลยีสารสนเทศ" required>
                    <div class="form-text">
                        <small class="text-muted">
                            <i class="fas fa-lightbulb me-1"></i>
                            รหัสแผนกจะถูกสร้างอัตโนมัติจากชื่อ
                        </small>
                    </div>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- Department Code -->
                <div class="col-md-4">
                    <label for="code" class="form-label">
                        รหัสแผนก <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <input type="text" class="form-control @error('code') is-invalid @enderror" 
                               id="code" name="code" value="{{ old('code') }}" 
                               placeholder="IT" required style="text-transform: uppercase;">
                        <button type="button" class="btn btn-outline-primary" id="codeGenerateBtn">
                            <i class="fas fa-magic"></i>
                        </button>
                    </div>
                    <div class="form-text">รหัสแผนกสำหรับระบบ (ภาษาอังกฤษ)</div>
                    @error('code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- Description -->
                <div class="col-12">
                    <label for="description" class="form-label">รายละเอียด</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="3" 
                              placeholder="รายละเอียดเกี่ยวกับแผนกนี้">{{ old('description') }}</textarea>
                    <div class="form-text">รายละเอียดเพิ่มเติมเกี่ยวกับแผนก (ไม่บังคับ)</div>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- Status -->
                <div class="col-md-6">
                    <label for="is_active" class="form-label">สถานะ</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                               {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            <span id="statusText">เปิดใช้งาน</span>
                        </label>
                    </div>
                    <div class="form-text">แผนกที่เปิดใช้งานจะปรากฏในรายการเลือกแผนก</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Express Support Info -->
    <div class="card mb-4" id="expressInfo" style="display: none;">
        <div class="card-header bg-warning bg-opacity-10">
            <div class="d-flex align-items-center">
                <div class="border border-2 border-warning rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; background: #fff8e1;">
                    <i class="fas fa-bolt text-warning" style="font-size: 18px;"></i>
                </div>
                <div>
                    <h5 class="card-title mb-0">รองรับโปรแกรม Express</h5>
                    <small class="text-muted">แผนกบัญชีสามารถใช้งาน Express ได้</small>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="alert alert-info" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                <strong>แผนกบัญชี</strong> จะมีการสร้าง Username และ Password สำหรับโปรแกรม Express อัตโนมัติเมื่อเพิ่มพนักงาน
            </div>
            <div class="row">
                <div class="col-md-6">
                    <h6><i class="fas fa-check text-success me-1"></i>ฟีเจอร์ที่รองรับ:</h6>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-user text-primary me-2"></i>Express Username (7 ตัวอักษร)</li>
                        <li><i class="fas fa-lock text-primary me-2"></i>Express Password (4 ตัวอักษร)</li>
                        <li><i class="fas fa-magic text-primary me-2"></i>สร้างอัตโนมัติ</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6><i class="fas fa-cog text-info me-1"></i>การทำงาน:</h6>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-arrow-right text-muted me-2"></i>เฉพาะแผนกบัญชี</li>
                        <li><i class="fas fa-arrow-right text-muted me-2"></i>แสดงในฟอร์มเพิ่มพนักงาน</li>
                        <li><i class="fas fa-arrow-right text-muted me-2"></i>บันทึกในฐานข้อมูล</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Actions -->
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <a href="{{ route('departments.index') }}" 
                   class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>ยกเลิก
                </a>
                
                <button type="submit" 
                        class="btn btn-primary"
                        id="submitBtn">
                    <i class="fas fa-plus me-2"></i>สร้างแผนก
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
                    <i class="fas fa-eye me-2"></i>ตัวอย่างข้อมูลแผนก
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
    console.log('🏢 Department Create Form Loaded');
    
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
        generateCodeFromName: (name) => {
            if (!name) return '';
            
            // Extract first letters from each word
            const words = name.split(' ');
            let code = '';
            
            for (let word of words) {
                if (word.trim()) {
                    code += word.trim().charAt(0).toUpperCase();
                }
            }
            
            // If code is too short, use first 3 characters
            if (code.length < 3) {
                code = name.replace(/\s/g, '').substring(0, 3).toUpperCase();
            }
            
            return code;
        }
    };
    
    // Event Handlers
    const nameInput = document.getElementById('name');
    const codeInput = document.getElementById('code');
    const statusSwitch = document.getElementById('is_active');
    const statusText = document.getElementById('statusText');
    const expressInfo = document.getElementById('expressInfo');
    
    // Auto-generate code from name
    nameInput.addEventListener('input', function() {
        const name = this.value.trim();
        const generatedCode = generators.generateCodeFromName(name);
        
        // Only auto-fill if code is empty or was previously auto-generated
        if (!codeInput.dataset.manuallyEdited) {
            codeInput.value = generatedCode;
        }
        
        // Show Express info for accounting department
        checkAccountingDepartment(name);
    });
    
    // Mark code as manually edited
    codeInput.addEventListener('input', function() {
        this.dataset.manuallyEdited = 'true';
        this.value = this.value.toUpperCase();
    });
    
    // Status switch
    statusSwitch.addEventListener('change', function() {
        statusText.textContent = this.checked ? 'เปิดใช้งาน' : 'ปิดใช้งาน';
    });
    
    // Check if this is accounting department
    function checkAccountingDepartment(name) {
        const isAccounting = name.includes('บัญชี') || 
                           name.toLowerCase().includes('account') ||
                           name.toLowerCase().includes('acc');
        
        if (isAccounting) {
            expressInfo.style.display = 'block';
            utils.showNotification('🎉 แผนกนี้จะรองรับโปรแกรม Express!', 'success');
        } else {
            expressInfo.style.display = 'none';
        }
    }
    
    // Generate code button
    document.getElementById('codeGenerateBtn').addEventListener('click', function() {
        const name = nameInput.value.trim();
        if (!name) {
            utils.showNotification('กรุณากรอกชื่อแผนกก่อน', 'error');
            nameInput.focus();
            return;
        }
        
        const generatedCode = generators.generateCodeFromName(name);
        codeInput.value = generatedCode;
        codeInput.dataset.manuallyEdited = 'false';
        
        utils.showNotification(`✅ สร้างรหัสแผนกสำเร็จ: ${generatedCode}`, 'success');
    });
    
    // Generate all button
    document.getElementById('generateCodeBtn').addEventListener('click', function() {
        const name = nameInput.value.trim();
        if (!name) {
            utils.showNotification('กรุณากรอกชื่อแผนกก่อน', 'error');
            nameInput.focus();
            return;
        }
        
        const generatedCode = generators.generateCodeFromName(name);
        codeInput.value = generatedCode;
        codeInput.dataset.manuallyEdited = 'false';
        
        checkAccountingDepartment(name);
        
        utils.showNotification('🎉 สร้างข้อมูลอัตโนมัติสำเร็จ!', 'success');
    });
    
    // Clear all button
    document.getElementById('clearAllBtn').addEventListener('click', function() {
        if (confirm('ต้องการล้างข้อมูลทั้งหมดหรือไม่?')) {
            document.getElementById('departmentForm').reset();
            codeInput.dataset.manuallyEdited = 'false';
            statusText.textContent = 'เปิดใช้งาน';
            expressInfo.style.display = 'none';
            
            utils.showNotification('🗑️ ล้างข้อมูลทั้งหมดแล้ว', 'success');
        }
    });
    
    // Preview button
    document.getElementById('previewBtn').addEventListener('click', function() {
        const formData = new FormData(document.getElementById('departmentForm'));
        const data = {};
        for (let [key, value] of formData.entries()) {
            data[key] = value;
        }
        
        const previewContent = document.getElementById('previewContent');
        if (!previewContent) return;
        
        const isAccounting = data.name && (data.name.includes('บัญชี') || 
                                         data.name.toLowerCase().includes('account'));
        
        previewContent.innerHTML = `
            <div class="row">
                <div class="col-md-8">
                    <h6 class="text-primary">ข้อมูลแผนก</h6>
                    <p><strong>ชื่อแผนก:</strong> ${data.name || '-'}</p>
                    <p><strong>รหัสแผนก:</strong> <span class="badge bg-primary">${data.code || '-'}</span></p>
                    <p><strong>รายละเอียด:</strong> ${data.description || 'ไม่มี'}</p>
                    <p><strong>สถานะ:</strong> 
                        <span class="badge bg-${data.is_active ? 'success' : 'secondary'}">
                            ${data.is_active ? 'เปิดใช้งาน' : 'ปิดใช้งาน'}
                        </span>
                    </p>
                </div>
                <div class="col-md-4">
                    <h6 class="text-primary">ฟีเจอร์พิเศษ</h6>
                    ${isAccounting ? `
                        <div class="alert alert-warning">
                            <i class="fas fa-bolt me-2"></i>
                            <strong>รองรับ Express!</strong><br>
                            <small>แผนกนี้จะมีการสร้าง Username และ Password สำหรับโปรแกรม Express อัตโนมัติ</small>
                        </div>
                    ` : `
                        <div class="text-muted">
                            <i class="fas fa-info-circle me-2"></i>
                            แผนกทั่วไป
                        </div>
                    `}
                </div>
            </div>
        `;
        
        const modal = new bootstrap.Modal(document.getElementById('previewModal'));
        modal.show();
    });
    
    // Form submission
    const departmentForm = document.getElementById('departmentForm');
    if (departmentForm) {
        departmentForm.addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            if (submitBtn) {
                utils.showLoading(submitBtn);
            }
        });
    }
    
    console.log('✅ Department Create Form Ready');
    console.log('🏢 Features: Auto-generate code, Express support detection, Live preview');
    console.log('⚡ Express Support: Detects "บัญชี", "account", "acc" in department name');
});

// Modal functions
function submitForm() {
    const form = document.getElementById('departmentForm');
    if (form) {
        form.submit();
    }
}
</script>
@endpush