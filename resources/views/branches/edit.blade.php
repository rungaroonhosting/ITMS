@extends('layouts.app')

@section('title', 'แก้ไขข้อมูลสาขา')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">แก้ไขข้อมูลสาขา: {{ $branch->name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('branches.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> กลับ
                        </a>
                        <a href="{{ route('branches.show', $branch) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-eye"></i> ดูข้อมูล
                        </a>
                    </div>
                </div>

                <form action="{{ route('branches.update', $branch) }}" method="POST">
                    @csrf
                    @method('PUT')
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
                                           value="{{ old('name', $branch->name) }}" 
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
                                           value="{{ old('code', $branch->code) }}" 
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
                                            class="form-control @error('manager_id') is-invalid @enderror">
                                        <option value="">เลือกผู้จัดการสาขา</option>
                                        @foreach($availableManagers as $manager)
                                            <option value="{{ $manager->id }}" 
                                                    {{ old('manager_id', $branch->manager_id) == $manager->id ? 'selected' : '' }}>
                                                {{ $manager->full_name_th }} ({{ $manager->employee_id }})
                                                @if($manager->id == $branch->manager_id)
                                                    - ผู้จัดการปัจจุบัน
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('manager_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        เลือกจากพนักงานที่สามารถเป็น Manager ได้
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
                                               {{ old('is_active', $branch->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            เปิดใช้งาน
                                        </label>
                                    </div>
                                    @if(!$branch->is_active)
                                        <small class="form-text text-warning">
                                            <i class="fas fa-exclamation-triangle"></i> 
                                            สาขานี้ปิดใช้งานอยู่
                                        </small>
                                    @endif
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
                                              placeholder="คำอธิบายเพิ่มเติมเกี่ยวกับสาขา">{{ old('description', $branch->description) }}</textarea>
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
                                              placeholder="ที่อยู่ของสาขา">{{ old('address', $branch->address) }}</textarea>
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
                                           value="{{ old('phone', $branch->phone) }}" 
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
                                           value="{{ old('email', $branch->email) }}" 
                                           placeholder="เช่น branch@company.com">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Branch Statistics -->
                        @if($branch->employees->count() > 0)
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h5>สถิติสาขา</h5>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-info">
                                                    <i class="fas fa-users"></i>
                                                </span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">พนักงานทั้งหมด</span>
                                                    <span class="info-box-number">{{ $branch->employees->count() }} คน</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-success">
                                                    <i class="fas fa-user-check"></i>
                                                </span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">พนักงานปกติ</span>
                                                    <span class="info-box-number">{{ $branch->employees->where('role', 'employee')->count() }} คน</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-warning">
                                                    <i class="fas fa-user-tie"></i>
                                                </span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Admin</span>
                                                    <span class="info-box-number">{{ $branch->employees->where('role', 'admin')->count() }} คน</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-danger">
                                                    <i class="fas fa-calendar-times"></i>
                                                </span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">วันที่สร้าง</span>
                                                    <span class="info-box-number text-xs">{{ $branch->created_at->format('d/m/Y') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="card-footer">
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> บันทึกการแก้ไข
                                </button>
                                <a href="{{ route('branches.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> ยกเลิก
                                </a>
                                <a href="{{ route('branches.show', $branch) }}" class="btn btn-info">
                                    <i class="fas fa-eye"></i> ดูข้อมูล
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
    
    // Form validation
    $('form').on('submit', function(e) {
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
    });
    
    // Highlight changes
    $('input, select, textarea').on('change', function() {
        $(this).addClass('border-warning');
    });
});
</script>
@endpush
@endsection
