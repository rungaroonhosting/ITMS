@extends('layouts.app')

@section('title', 'เพิ่มสาขาใหม่')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">เพิ่มสาขาใหม่</h3>
                    <div class="card-tools">
                        <a href="{{ route('branches.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> กลับ
                        </a>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger m-3">
                        <h5><i class="icon fas fa-ban"></i> พบข้อผิดพลาด!</h5>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('branches.store') }}" method="POST" id="branchForm">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <!-- ชื่อสาขา -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">ชื่อสาขา <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name') }}" 
                                           placeholder="เช่น สาขากรุงเทพฯ"
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- รหัสสาขา -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="code">รหัสสาขา <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('code') is-invalid @enderror" 
                                           id="code" 
                                           name="code" 
                                           value="{{ old('code') }}" 
                                           placeholder="เช่น BKK001"
                                           maxlength="10"
                                           style="text-transform: uppercase;"
                                           required>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- ผู้จัดการสาขา -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="manager_id">ผู้จัดการสาขา</label>
                                    <select name="manager_id" 
                                            class="form-control @error('manager_id') is-invalid @enderror"
                                            id="manager_id">
                                        <option value="">เลือกผู้จัดการสาขา</option>
                                        @if(isset($availableManagers))
                                            @foreach($availableManagers as $manager)
                                                <option value="{{ $manager->id }}" 
                                                        {{ old('manager_id') == $manager->id ? 'selected' : '' }}>
                                                    {{ $manager->name }} 
                                                    @if($manager->email)
                                                        ({{ $manager->email }})
                                                    @endif
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('manager_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        เลือกจากผู้ใช้ที่ยังไม่ได้เป็น Manager สาขาอื่น
                                    </small>
                                </div>
                            </div>

                            <!-- สถานะ -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="is_active">สถานะ</label>
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="is_active" 
                                               name="is_active"
                                               value="1"
                                               {{ old('is_active', '1') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            เปิดใช้งาน
                                        </label>
                                    </div>
                                    @error('is_active')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- คำอธิบาย -->
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="description">คำอธิบาย</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" 
                                              name="description" 
                                              rows="3"
                                              placeholder="คำอธิบายเพิ่มเติมเกี่ยวกับสาขา">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- ที่อยู่ -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="address">ที่อยู่</label>
                                    <textarea class="form-control @error('address') is-invalid @enderror" 
                                              id="address" 
                                              name="address" 
                                              rows="3"
                                              placeholder="ที่อยู่ของสาขา">{{ old('address') }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- เบอร์โทร -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">เบอร์โทร</label>
                                    <input type="text" 
                                           class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" 
                                           name="phone" 
                                           value="{{ old('phone') }}" 
                                           placeholder="เช่น 02-123-4567"
                                           maxlength="20">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- อีเมล -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">อีเมล</label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email') }}" 
                                           placeholder="เช่น branch@company.com">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="fas fa-save"></i> บันทึก
                                </button>
                                <a href="{{ route('branches.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> ยกเลิก
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Auto uppercase for branch code
    $('#code').on('input', function() {
        this.value = this.value.toUpperCase();
    });
    
    // Debug form submission
    $('#branchForm').on('submit', function(e) {
        console.log('Form submitting...');
        console.log('Form data:');
        
        // Log all form data
        const formData = new FormData(this);
        for (let [key, value] of formData.entries()) {
            console.log(key + ': ' + value);
        }
        
        // Basic validation
        let name = $('#name').val().trim();
        let code = $('#code').val().trim();
        
        if (!name || !code) {
            e.preventDefault();
            alert('กรุณากรอกข้อมูลที่จำเป็น (ชื่อสาขาและรหัสสาขา)');
            return false;
        }
        
        if (code.length < 3) {
            e.preventDefault();
            alert('รหัสสาขาต้องมีความยาวอย่างน้อย 3 ตัวอักษร');
            return false;
        }
        
        // Disable submit button to prevent double submission
        $('#submitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> กำลังบันทึก...');
    });

    // Show success/error messages if any
    @if(session('success'))
        alert('{{ session('success') }}');
    @endif

    @if(session('error'))
        alert('{{ session('error') }}');
    @endif
});
</script>
@endpush
@endsection
