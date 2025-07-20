@extends('layouts.app')

@section('title', 'แก้ไขแผนก - ' . $department->name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('departments.index') }}">จัดการแผนก</a></li>
    <li class="breadcrumb-item"><a href="{{ route('departments.show', $department) }}">{{ $department->name }}</a></li>
    <li class="breadcrumb-item active">แก้ไข</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-primary fw-bold">
                    <i class="fas fa-edit me-2"></i>แก้ไขแผนก
                </h1>
                <p class="text-muted mb-0">แก้ไขข้อมูลแผนก: {{ $department->name }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('departments.show', $department) }}" class="btn btn-outline-info">
                    <i class="fas fa-eye me-1"></i>ดูรายละเอียด
                </a>
                <a href="{{ route('departments.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>กลับ
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Department Info Banner -->
<div class="card mb-4 border-0 shadow-sm bg-gradient-info text-white">
    <div class="card-body">
        <div class="d-flex align-items-center">
            <div class="flex-shrink-0">
                <div class="bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="fas fa-building fa-2x text-white"></i>
                </div>
            </div>
            <div class="flex-grow-1 ms-3">
                <h4 class="mb-1">{{ $department->name }}</h4>
                <div class="d-flex gap-3">
                    <span><i class="fas fa-code me-1"></i>{{ $department->code }}</span>
                    <span><i class="fas fa-users me-1"></i>{{ $department->employees->count() }} พนักงาน</span>
                    <span><i class="fas fa-calendar me-1"></i>สร้างเมื่อ {{ $department->created_at->format('d/m/Y') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row text-center">
            <div class="col-md-4 mb-2">
                <button type="button" class="btn btn-outline-info w-100" id="previewBtn">
                    <i class="fas fa-eye me-1"></i>ดูตัวอย่าง
                </button>
                <div class="form-text mt-1">
                    <small class="text-muted">ดูการเปลี่ยนแปลง</small>
                </div>
            </div>
            <div class="col-md-4 mb-2">
                <button type="button" class="btn btn-outline-warning w-100" id="resetBtn">
                    <i class="fas fa-undo me-1"></i>รีเซ็ต
                </button>
                <div class="form-text mt-1">
                    <small class="text-muted">กลับไปข้อมูลเดิม</small>
                </div>
            </div>
            <div class="col-md-4 mb-2">
                <button type="button" class="btn btn-outline-primary w-100" id="generateCodeBtn">
                    <i class="fas fa-magic me-1"></i>สร้างรหัสใหม่
                </button>
                <div class="form-text mt-1">
                    <small class="text-muted">จากชื่อแผนก</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Form -->
<form id="departmentForm" action="{{ route('departments.update', $department) }}" method="POST">
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
                    <small class="text-muted">แก้ไขข้อมูลของแผนก</small>
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
                           id="name" name="name" value="{{ old('name', $department->name) }}" 
                           placeholder="เช่น แผนกเทคโนโลยีสารสนเทศ" required>
                    <div class="form-text">
                        <small class="text-muted">
                            <i class="fas fa-lightbulb me-1"></i>
                            รหัสแผนกจะถูกสร้างอัตโนมัติจากชื่อ (หากต้องการ)
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
                               id="code" name="code" value="{{ old('code', $department->code) }}" 
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
                              placeholder="รายละเอียดเกี่ยวกับแผนกนี้">{{ old('description', $department->description) }}</textarea>
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
                               {{ old('is_active', $department->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            <span id="statusText">{{ $department->is_active ? 'เปิดใช้งาน' : 'ปิดใช้งาน' }}</span>
                        </label>
                    </div>
                    <div class="form-text">แผนกที่เปิดใช้งานจะปรากฏในรายการเลือกแผนก</div>
                    
                    @if($department->employees->count() > 0 && $department->is_active)
                        <div class="alert alert-warning mt-2">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <small>หากปิดใช้งานแผนกนี้ พนักงาน {{ $department->employees->count() }} คนจะไม่สามารถเข้าถึงฟีเจอร์บางอย่างได้</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Express Support Info -->
    <div class="card mb-4" id="expressInfo" style="display: {{ $department->isAccounting() ? 'block' : 'none' }};">
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
            
            @if($department->employees->whereNotNull('express_username')->count() > 0)
                <div class="alert alert-success">
                    <i class="fas fa-users me-2"></i>
                    <strong>พนักงานที่ใช้ Express:</strong> {{ $department->employees->whereNotNull('express_username')->count() }} คน
                    <ul class="mt-2 mb-0">
                        @foreach($department->employees->whereNotNull('express_username') as $employee)
                            <li>{{ $employee->full_name_th }} ({{ $employee->express_username }})</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>

    <!-- Changes Summary -->
    <div class="card mb-4" id="changesCard" style="display: none;">
        <div class="card-header bg-info bg-opacity-10">
            <h6 class="mb-0 text-info">
                <i class="fas fa-history me-2"></i>การเปลี่ยนแปลง
            </h6>
        </div>
        <div class="card-body" id="changesContent">
            <!-- Changes will be displayed here -->
        </div>
    </div>

    <!-- Form Actions -->
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <a href="{{ route('departments.show', $department) }}" 
                   class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>ยกเลิก
                </a>
                
                <button type="submit" 
                        class="btn btn-warning"
                        id="submitBtn">
                    <i class="fas fa-save me-2"></i>บันทึกการแก้ไข
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
                    <i class="fas fa-eye me-2"></i>ตัวอย่างการเปลี่ยนแปลง
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="previewContent">
                <!-- Preview content will be inserted here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                <button type="button" class="btn btn-warning" onclick="submitForm()">
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
    console.log('🏢 Department Edit Form Loaded');
    
    // Original data for comparison
    const originalData = {
        name: '{{ $department->name }}',
        code: '{{ $department->code }}',
        description: '{{ $department->description }}',
        is_active: {{ $department->is_active ? 'true' : 'false' }}
    };
    
    // Utility Functions
    const utils = {
        showLoading: (button) => {
            button.disabled = true;
            const originalText = button.innerHTML;
            button.dataset.originalText = originalText;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>กำลังบันทึก...';
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
            
            const words = name.split(' ');
            let code = '';
            
            for (let word of words) {
                if (word.trim()) {
                    code += word.trim().charAt(0).toUpperCase();
                }
            }
            
            if (code.length < 3) {
                code = name.replace(/\s/g, '').substring(0, 3).toUpperCase();
            }
            
            return code;
        }
    };
    
    // Elements
    const nameInput = document.getElementById('name');
    const codeInput = document.getElementById('code');
    const statusSwitch = document.getElementById('is_active');
    const statusText = document.getElementById('statusText');
    const expressInfo = document.getElementById('expressInfo');
    const changesCard = document.getElementById('changesCard');
    const changesContent = document.getElementById('changesContent');
    
    // Track changes
    function trackChanges() {
        const currentData = {
            name: nameInput.value.trim(),
            code: codeInput.value.trim(),
            description: document.getElementById('description').value.trim(),
            is_active: statusSwitch.checked
        };
        
        const changes = [];
        
        if (currentData.name !== originalData.name) {
            changes.push({
                field: 'ชื่อแผนก',
                from: originalData.name,
                to: currentData.name
            });
        }
        
        if (currentData.code !== originalData.code) {
            changes.push({
                field: 'รหัสแผนก',
                from: originalData.code,
                to: currentData.code
            });
        }
        
        if (currentData.description !== originalData.description) {
            changes.push({
                field: 'รายละเอียด',
                from: originalData.description || 'ไม่มี',
                to: currentData.description || 'ไม่มี'
            });
        }
        
        if (currentData.is_active !== originalData.is_active) {
            changes.push({
                field: 'สถานะ',
                from: originalData.is_active ? 'เปิดใช้งาน' : 'ปิดใช้งาน',
                to: currentData.is_active ? 'เปิดใช้งาน' : 'ปิดใช้งาน'
            });
        }
        
        if (changes.length > 0) {
            let changesHtml = '<ul class="list-unstyled mb-0">';
            changes.forEach(change => {
                changesHtml += `
                    <li class="mb-2">
                        <strong>${change.field}:</strong>
                        <div class="d-flex align-items-center gap-2 mt-1">
                            <span class="badge bg-danger">${change.from}</span>
                            <i class="fas fa-arrow-right"></i>
                            <span class="badge bg-success">${change.to}</span>
                        </div>
                    </li>
                `;
            });
            changesHtml += '</ul>';
            
            changesContent.innerHTML = changesHtml;
            changesCard.style.display = 'block';
        } else {
            changesCard.style.display = 'none';
        }
        
        return changes;
    }
    
    // Auto-generate code from name
    nameInput.addEventListener('input', function() {
        const name = this.value.trim();
        
        // Check if this is accounting department
        checkAccountingDepartment(name);
        
        // Track changes
        trackChanges();
    });
    
    // Mark code as manually edited and track changes
    codeInput.addEventListener('input', function() {
        this.dataset.manuallyEdited = 'true';
        this.value = this.value.toUpperCase();
        trackChanges();
    });
    
    // Status switch
    statusSwitch.addEventListener('change', function() {
        statusText.textContent = this.checked ? 'เปิดใช้งาน' : 'ปิดใช้งาน';
        trackChanges();
    });
    
    // Description changes
    document.getElementById('description').addEventListener('input', trackChanges);
    
    // Check if this is accounting department
    function checkAccountingDepartment(name) {
        const isAccounting = name.includes('บัญชี') || 
                           name.toLowerCase().includes('account') ||
                           name.toLowerCase().includes('acc');
        
        if (isAccounting) {
            expressInfo.style.display = 'block';
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
        
        trackChanges();
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
        trackChanges();
        
        utils.showNotification('🎉 สร้างรหัสแผนกสำเร็จ!', 'success');
    });
    
    // Reset button
    document.getElementById('resetBtn').addEventListener('click', function() {
        if (confirm('ต้องการรีเซ็ตข้อมูลกลับเป็นเหมือนเดิมหรือไม่?')) {
            nameInput.value = originalData.name;
            codeInput.value = originalData.code;
            document.getElementById('description').value = originalData.description;
            statusSwitch.checked = originalData.is_active;
            statusText.textContent = originalData.is_active ? 'เปิดใช้งาน' : 'ปิดใช้งาน';
            
            codeInput.dataset.manuallyEdited = 'false';
            checkAccountingDepartment(originalData.name);
            trackChanges();
            
            utils.showNotification('🔄 รีเซ็ตข้อมูลเรียบร้อยแล้ว', 'success');
        }
    });
    
    // Preview button
    document.getElementById('previewBtn').addEventListener('click', function() {
        const changes = trackChanges();
        const formData = new FormData(document.getElementById('departmentForm'));
        const data = {};
        for (let [key, value] of formData.entries()) {
            data[key] = value;
        }
        
        const previewContent = document.getElementById('previewContent');
        if (!previewContent) return;
        
        const isAccounting = data.name && (data.name.includes('บัญชี') || 
                                         data.name.toLowerCase().includes('account'));
        
        let previewHtml = `
            <div class="row">
                <div class="col-md-8">
                    <h6 class="text-primary">ข้อมูลใหม่</h6>
                    <p><strong>ชื่อแผนก:</strong> ${data.name || '-'}</p>
                    <p><strong>รหัสแผนก:</strong> <span class="badge bg-primary">${data.code || '-'}</span></p>
                    <p><strong>รายละเอียด:</strong> ${data.description || 'ไม่มี'}</p>
                    <p><strong>สถานะ:</strong> 
                        <span class="badge bg-${data.is_active ? 'success' : 'secondary'}">
                            ${data.is_active ? 'เปิดใช้งาน' : 'ปิดใช้งาน'}
                        </span>
                    </p>
                    
                    ${isAccounting ? `
                        <div class="alert alert-warning">
                            <i class="fas fa-bolt me-2"></i>
                            <strong>รองรับ Express!</strong><br>
                            <small>แผนกนี้จะมีการสร้าง Username และ Password สำหรับโปรแกรม Express อัตโนมัติ</small>
                        </div>
                    ` : ''}
                </div>
                <div class="col-md-4">
                    <h6 class="text-warning">การเปลี่ยนแปลง</h6>
        `;
        
        if (changes.length > 0) {
            previewHtml += '<ul class="list-unstyled">';
            changes.forEach(change => {
                previewHtml += `
                    <li class="mb-2">
                        <small><strong>${change.field}:</strong></small>
                        <div class="d-flex flex-column gap-1">
                            <span class="badge bg-danger small">${change.from}</span>
                            <i class="fas fa-arrow-down text-center"></i>
                            <span class="badge bg-success small">${change.to}</span>
                        </div>
                    </li>
                `;
            });
            previewHtml += '</ul>';
        } else {
            previewHtml += '<div class="text-muted"><i class="fas fa-info-circle me-2"></i>ไม่มีการเปลี่ยนแปลง</div>';
        }
        
        previewHtml += '</div></div>';
        
        previewContent.innerHTML = previewHtml;
        
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
    
    // Initial check for accounting department
    checkAccountingDepartment(originalData.name);
    
    console.log('✅ Department Edit Form Ready');
    console.log('🔄 Features: Change tracking, Preview, Reset, Auto-generate');
    console.log('⚡ Express Support: Dynamic detection based on name');
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