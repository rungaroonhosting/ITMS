{{-- ✅ DEBUG: เพิ่มการ debug เพื่อตรวจสอบข้อมูล Branch --}}
@extends('layouts.app')

@section('title', 'ข้อมูลพนักงาน - ' . $employee->full_name_th)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('employees.index') }}">จัดการพนักงาน</a></li>
    <li class="breadcrumb-item active">{{ $employee->full_name_th }}</li>
@endsection

@section('content')

{{-- ✅ NEW: DEBUG SECTION - แสดงเฉพาะเมื่อ APP_DEBUG=true --}}
@if(config('app.debug') && auth()->user() && in_array(auth()->user()->role, ['super_admin', 'it_admin']))
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <h6 class="fw-bold">
            <i class="fas fa-bug me-2"></i>🐛 DEBUG MODE - Branch Information
        </h6>
        <div class="row">
            <div class="col-md-6">
                <p class="mb-1"><strong>Employee ID:</strong> {{ $employee->id }}</p>
                <p class="mb-1"><strong>Branch ID in DB:</strong> 
                    @if($employee->branch_id)
                        <span class="badge bg-success">{{ $employee->branch_id }}</span>
                    @else
                        <span class="badge bg-warning text-dark">NULL</span>
                    @endif
                </p>
                <p class="mb-1"><strong>Raw branch_id:</strong> <code>{{ var_export($employee->branch_id, true) }}</code></p>
            </div>
            <div class="col-md-6">
                <p class="mb-1"><strong>Branch Relationship:</strong>
                    @if($employee->branch)
                        <span class="badge bg-success">✅ Loaded</span>
                    @else
                        <span class="badge bg-danger">❌ NULL</span>
                    @endif
                </p>
                @if($employee->branch)
                    <p class="mb-1"><strong>Branch Name:</strong> {{ $employee->branch->name }}</p>
                    <p class="mb-1"><strong>Branch Code:</strong> {{ $employee->branch->code ?? $employee->branch->branch_code ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>Branch Active:</strong> 
                        <span class="badge bg-{{ $employee->branch->is_active ? 'success' : 'secondary' }}">
                            {{ $employee->branch->is_active ? 'Yes' : 'No' }}
                        </span>
                    </p>
                @else
                    <p class="mb-1 text-muted">No branch data found</p>
                @endif
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Employee Header with ITMS Theme -->
<div class="card mb-4">
    <div class="card-header" style="background: linear-gradient(135deg, #B54544 0%, #E6952A 100%); color: white; border: none;">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-3" 
                         style="width: 60px; height: 60px;">
                        <span class="text-dark fw-bold" style="font-size: 1.5rem;">
                            {{ $employee->initials ?? strtoupper(substr($employee->first_name_en ?? 'U', 0, 1) . substr($employee->last_name_en ?? 'N', 0, 1)) }}
                        </span>
                    </div>
                    <div>
                        <h2 class="mb-1" style="color: #fff; text-shadow: 1px 1px 2px rgba(0,0,0,0.3);">
                            {{ $employee->full_name_th }}
                        </h2>
                        <h5 class="mb-2" style="color: #fff; text-shadow: 1px 1px 2px rgba(0,0,0,0.3);">
                            {{ $employee->full_name_en }}
                        </h5>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge" style="background: linear-gradient(45deg, #007bff, #0056b3); border: 1px solid rgba(255,255,255,0.2); box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                                {{ $employee->employee_code }}
                            </span>
                            <span class="badge bg-{{ $employee->status == 'active' ? 'success' : 'secondary' }}" style="border: 1px solid rgba(255,255,255,0.2); box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                                {{ $employee->status == 'active' ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                            </span>
                            <span class="badge bg-info" style="border: 1px solid rgba(255,255,255,0.2); box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                                {{ $employee->role_display ?? ucfirst($employee->role) }}
                            </span>
                            @if($employee->department)
                                <span class="badge bg-warning text-dark" style="border: 1px solid rgba(255,255,255,0.2); box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                                    {{ $employee->department->name }}
                                    @if($employee->department->express_enabled ?? false)
                                        <i class="fas fa-bolt ms-1"></i>
                                    @endif
                                </span>
                            @endif
                            
                            {{-- ✅ ENHANCED: Branch Display with Debug Info --}}
                            @if($employee->branch)
                                <span class="badge text-white" style="background: linear-gradient(45deg, #B54544, #E6952A); border: 1px solid rgba(255,255,255,0.2); box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                                    <i class="fas fa-building me-1"></i>{{ $employee->branch->name }}
                                    @if(config('app.debug'))
                                        <small style="opacity: 0.7;">(ID:{{ $employee->branch->id }})</small>
                                    @endif
                                </span>
                            @else
                                {{-- ✅ DEBUG: แสดงเมื่อไม่มี branch --}}
                                <span class="badge bg-secondary" style="border: 1px solid rgba(255,255,255,0.2); box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                                    <i class="fas fa-building me-1"></i>ไม่ระบุสาขา
                                    @if(config('app.debug'))
                                        <small style="opacity: 0.7;">(branch_id: {{ $employee->branch_id ?? 'NULL' }})</small>
                                    @endif
                                </span>
                            @endif
                            
                            <span class="badge bg-success" style="border: 1px solid rgba(255,255,255,0.2); box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                                <i class="fas fa-eye me-1"></i>แสดง Password เลย
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-end">
                @php
                    $currentUser = auth()->user();
                    $canEdit = $currentUser && (
                        $currentUser->role === 'super_admin' || 
                        $currentUser->role === 'it_admin' || 
                        ($currentUser->role === 'hr' && $employee->role === 'employee') ||
                        ($currentUser->role === 'express' && $employee->department && $employee->department->express_enabled)
                    );
                @endphp
                @if($canEdit)
                    <a href="{{ route('employees.edit', $employee) }}" class="btn btn-warning btn-sm me-2">
                        <i class="fas fa-edit me-1"></i>แก้ไข
                    </a>
                @endif
                <a href="{{ route('employees.index') }}" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>กลับ
                </a>
            </div>
        </div>
    </div>
</div>

{{-- ✅ NEW: Branch Status Alert --}}
@if(config('app.debug') && auth()->user() && in_array(auth()->user()->role, ['super_admin', 'it_admin']))
    @if($employee->branch_id && !$employee->branch)
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h6 class="fw-bold">
                <i class="fas fa-exclamation-triangle me-2"></i>⚠️ Branch Data Issue Detected
            </h6>
            <p class="mb-1">
                <strong>Issue:</strong> พนักงานมี branch_id = {{ $employee->branch_id }} แต่ไม่พบข้อมูล Branch
            </p>
            <p class="mb-1">
                <strong>Possible Causes:</strong>
            </p>
            <ul class="mb-1">
                <li>Branch ID {{ $employee->branch_id }} ไม่มีอยู่ในตาราง branches</li>
                <li>Branch ถูกลบแล้ว (soft delete)</li>
                <li>Branch ไม่ active (is_active = false)</li>
                <li>Database constraint ปัญหา</li>
            </ul>
            <p class="mb-0">
                <strong>Solution:</strong> 
                <a href="{{ route('employees.edit', $employee) }}" class="btn btn-sm btn-warning">
                    <i class="fas fa-edit me-1"></i>แก้ไขข้อมูลสาขา
                </a>
            </p>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @elseif(!$employee->branch_id)
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <h6 class="fw-bold">
                <i class="fas fa-info-circle me-2"></i>ℹ️ No Branch Assigned
            </h6>
            <p class="mb-0">
                <strong>Note:</strong> พนักงานนี้ยังไม่ได้กำหนดสาขา (branch_id เป็น NULL)
                <a href="{{ route('employees.edit', $employee) }}" class="btn btn-sm btn-primary ms-2">
                    <i class="fas fa-plus me-1"></i>กำหนดสาขา
                </a>
            </p>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
@endif

<!-- Success/Error Messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Password Display Notice -->
@php
    $canSeePasswords = $currentUser && in_array($currentUser->role, ['super_admin', 'it_admin']);
@endphp

@if($canSeePasswords)
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <h6 class="fw-bold">
            <i class="fas fa-shield-alt me-2"></i>🔓 Admin Mode - แสดง Password ทั้งหมด
        </h6>
        <div class="row">
            <div class="col-md-6">
                <p class="mb-1">
                    <i class="fas fa-eye me-2"></i>
                    <strong>คุณมีสิทธิ์ดู Password:</strong> ระบบจะแสดง Password จริงให้เห็นทันที
                </p>
            </div>
            <div class="col-md-6">
                <p class="mb-1">
                    <i class="fas fa-copy me-2"></i>
                    <strong>Copy Function:</strong> คลิกปุ่ม <i class="fas fa-copy"></i> เพื่อคัดลอกรหัสผ่าน
                </p>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row">
    <!-- Left Column -->
    <div class="col-md-6">
        <!-- Basic Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-user text-primary me-2"></i>ข้อมูลพื้นฐาน
                </h5>
            </div>
            <div class="card-body">
                <div class="info-item-inline">
                    <span class="info-label">รหัสพนักงาน:</span>
                    <span class="info-value">{{ $employee->employee_code }}</span>
                </div>
                <div class="info-item-inline">
                    <span class="info-label">ID Keycard:</span>
                    <span class="info-value">{{ $employee->keycard_id }}</span>
                </div>
                <div class="info-item-inline">
                    <span class="info-label">ชื่อ-นามสกุล (ไทย):</span>
                    <span class="info-value">{{ $employee->full_name_th }}</span>
                </div>
                <div class="info-item-inline">
                    <span class="info-label">ชื่อ-นามสกุล (อังกฤษ):</span>
                    <span class="info-value">{{ $employee->full_name_en }}</span>
                </div>
                <div class="info-item-inline">
                    <span class="info-label">เบอร์โทรศัพท์:</span>
                    <span class="info-value">
                        {{ $employee->phone }}
                        <span class="badge bg-success ms-2">ซ้ำได้</span>
                    </span>
                </div>
                @if($employee->nickname)
                    <div class="info-item-inline">
                        <span class="info-label">ชื่อเล่น:</span>
                        <span class="info-value">{{ $employee->nickname }}</span>
                    </div>
                @endif
                @if($employee->hire_date)
                    <div class="info-item-inline">
                        <span class="info-label">วันที่เข้าทำงาน:</span>
                        <span class="info-value">
                            {{ $employee->hire_date->format('d/m/Y') }}
                            <small class="text-muted">({{ \Carbon\Carbon::parse($employee->hire_date)->diffInYears(now()) }} ปี)</small>
                        </span>
                    </div>
                @endif
            </div>
        </div>

        <!-- ✅ ENHANCED Branch Information (with Debug) -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-building me-2" style="color: #B54544;"></i>ข้อมูลสาขา
                    <span class="badge text-white ms-2" style="background: linear-gradient(45deg, #B54544, #E6952A);">
                        Branch System
                    </span>
                    @if(config('app.debug'))
                        <span class="badge bg-secondary ms-1">DEBUG</span>
                    @endif
                </h5>
            </div>
            <div class="card-body">
                @if($employee->branch)
                    {{-- ✅ Branch exists - show all info --}}
                    <div class="info-item-inline">
                        <span class="info-label">สาขาที่สังกัด:</span>
                        <span class="info-value">
                            <span class="badge text-white" style="background: linear-gradient(45deg, #B54544, #E6952A);">
                                <i class="fas fa-building me-1"></i>{{ $employee->branch->name }}
                            </span>
                        </span>
                    </div>
                    <div class="info-item-inline">
                        <span class="info-label">รหัสสาขา:</span>
                        <span class="info-value">
                            <code style="background: rgba(181, 69, 68, 0.1); color: #B54544; padding: 0.25rem 0.5rem; border-radius: 0.25rem;">
                                {{ $employee->branch->code ?? $employee->branch->branch_code ?? 'N/A' }}
                            </code>
                        </span>
                    </div>
                    @if($employee->branch->description)
                        <div class="info-item-inline">
                            <span class="info-label">รายละเอียด:</span>
                            <span class="info-value">{{ $employee->branch->description }}</span>
                        </div>
                    @endif
                    @if($employee->branch->manager)
                        <div class="info-item-inline">
                            <span class="info-label">ผู้จัดการสาขา:</span>
                            <span class="info-value">
                                <span class="badge bg-info">
                                    <i class="fas fa-user-tie me-1"></i>{{ $employee->branch->manager->full_name_th ?? $employee->branch->manager->name }}
                                </span>
                            </span>
                        </div>
                    @endif
                    @if($employee->branch->phone)
                        <div class="info-item-inline">
                            <span class="info-label">เบอร์สาขา:</span>
                            <span class="info-value">
                                <a href="tel:{{ $employee->branch->phone }}" class="text-decoration-none">
                                    <i class="fas fa-phone me-1"></i>{{ $employee->branch->phone }}
                                </a>
                            </span>
                        </div>
                    @endif
                    @if($employee->branch->email)
                        <div class="info-item-inline">
                            <span class="info-label">อีเมลสาขา:</span>
                            <span class="info-value">
                                <a href="mailto:{{ $employee->branch->email }}" class="text-decoration-none">
                                    <i class="fas fa-envelope me-1"></i>{{ $employee->branch->email }}
                                </a>
                            </span>
                        </div>
                    @endif
                    @if($employee->branch->address)
                        <div class="info-item-inline">
                            <span class="info-label">ที่อยู่สาขา:</span>
                            <span class="info-value">
                                <small class="text-muted">{{ $employee->branch->address }}</small>
                            </span>
                        </div>
                    @endif
                    <div class="info-item-inline">
                        <span class="info-label">สถานะสาขา:</span>
                        <span class="info-value">
                            <span class="badge bg-{{ $employee->branch->is_active ? 'success' : 'secondary' }}">
                                {{ $employee->branch->is_active ? 'เปิดดำเนินการ' : 'ปิดชั่วคราว' }}
                            </span>
                        </span>
                    </div>
                    
                    {{-- ✅ DEBUG: Additional branch info --}}
                    @if(config('app.debug'))
                        <hr>
                        <div class="alert alert-info p-2 mb-0">
                            <small>
                                <strong>DEBUG:</strong> Branch ID = {{ $employee->branch->id }}, 
                                Active = {{ $employee->branch->is_active ? 'true' : 'false' }}, 
                                Created = {{ $employee->branch->created_at->format('d/m/Y H:i') }}
                            </small>
                        </div>
                    @endif
                    
                @else
                    {{-- ✅ No branch - show different messages based on branch_id --}}
                    <div class="text-center py-3">
                        <div class="text-muted">
                            <i class="fas fa-building fa-2x mb-2" style="opacity: 0.3;"></i>
                            @if($employee->branch_id)
                                {{-- มี branch_id แต่ไม่พบข้อมูล --}}
                                <p class="mb-1 text-danger">⚠️ ข้อมูลสาขาผิดพลาด</p>
                                <small>
                                    มี Branch ID: {{ $employee->branch_id }} แต่ไม่พบข้อมูลในระบบ<br>
                                    กรุณาติดต่อ IT Admin เพื่อแก้ไข
                                </small>
                                @if(config('app.debug'))
                                    <div class="mt-2">
                                        <code class="text-danger">branch_id = {{ $employee->branch_id }} (NOT FOUND)</code>
                                    </div>
                                @endif
                            @else
                                {{-- ไม่มี branch_id --}}
                                <p class="mb-1">ไม่ได้ระบุสาขา</p>
                                <small>พนักงานนี้ยังไม่ได้กำหนดสาขาที่สังกัด</small>
                                @if(config('app.debug'))
                                    <div class="mt-2">
                                        <code class="text-muted">branch_id = NULL</code>
                                    </div>
                                @endif
                            @endif
                        </div>
                        
                        {{-- ✅ Quick Fix Button --}}
                        @if($canEdit)
                            <div class="mt-3">
                                <a href="{{ route('employees.edit', $employee) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-plus me-1"></i>กำหนดสาขา
                                </a>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Department and Position -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-users text-warning me-2"></i>แผนกและตำแหน่ง
                </h5>
            </div>
            <div class="card-body">
                <div class="info-item-inline">
                    <span class="info-label">แผนก:</span>
                    <span class="info-value">
                        {{ $employee->department ? $employee->department->name : 'ไม่ระบุ' }}
                        @if($employee->department && ($employee->department->express_enabled ?? false))
                            <span class="badge bg-warning text-dark ms-2">
                                <i class="fas fa-bolt me-1"></i>Express
                            </span>
                        @endif
                    </span>
                </div>
                <div class="info-item-inline">
                    <span class="info-label">ตำแหน่ง:</span>
                    <span class="info-value">{{ $employee->position }}</span>
                </div>
                <div class="info-item-inline">
                    <span class="info-label">สิทธิ์การใช้งาน:</span>
                    <span class="info-value">
                        <span class="badge bg-primary">{{ $employee->role_display ?? ucfirst($employee->role) }}</span>
                    </span>
                </div>
                <div class="info-item-inline">
                    <span class="info-label">สถานะ:</span>
                    <span class="info-value">
                        <span class="badge bg-{{ $employee->status == 'active' ? 'success' : 'secondary' }}">
                            {{ $employee->status == 'active' ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                        </span>
                    </span>
                </div>
            </div>
        </div>

        <!-- Special Permissions Section (ใช้เหมือนเดิม) -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-shield-alt text-danger me-2"></i>สิทธิ์พิเศษ
                    <span class="badge bg-success ms-2">✅ แก้ไขแล้ว</span>
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <!-- VPN Access -->
                    <div class="col-md-6">
                        <div class="card border-{{ $employee->vpn_access ? 'success' : 'secondary' }} h-100">
                            <div class="card-body text-center">
                                <div class="mb-2">
                                    <i class="fas fa-shield-alt fa-2x text-{{ $employee->vpn_access ? 'success' : 'secondary' }}"></i>
                                </div>
                                <h6 class="card-title">การใช้งาน VPN</h6>
                                <span class="badge bg-{{ $employee->vpn_access ? 'success' : 'secondary' }} fs-6">
                                    {{ $employee->vpn_access ? 'อนุญาต' : 'ไม่อนุญาต' }}
                                </span>
                                <p class="card-text mt-2 small text-muted">
                                    {{ $employee->vpn_access ? 'สามารถเชื่อมต่อ VPN เพื่อทำงานจากที่บ้านได้' : 'ไม่สามารถเชื่อมต่อ VPN ได้' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Color Printing -->
                    <div class="col-md-6">
                        <div class="card border-{{ $employee->color_printing ? 'warning' : 'secondary' }} h-100">
                            <div class="card-body text-center">
                                <div class="mb-2">
                                    <i class="fas fa-palette fa-2x text-{{ $employee->color_printing ? 'warning' : 'secondary' }}"></i>
                                </div>
                                <h6 class="card-title">การปริ้นสี</h6>
                                <span class="badge bg-{{ $employee->color_printing ? 'warning' : 'secondary' }} fs-6 {{ $employee->color_printing ? 'text-dark' : '' }}">
                                    {{ $employee->color_printing ? 'อนุญาต' : 'ไม่อนุญาต' }}
                                </span>
                                <p class="card-text mt-2 small text-muted">
                                    {{ $employee->color_printing ? 'สามารถใช้เครื่องพิมพ์สีในการพิมพ์เอกสารได้' : 'ใช้เครื่องพิมพ์ขาว-ดำเท่านั้น' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Remote Work -->
                    <div class="col-md-6">
                        <div class="card border-{{ $employee->remote_work ?? false ? 'info' : 'secondary' }} h-100">
                            <div class="card-body text-center">
                                <div class="mb-2">
                                    <i class="fas fa-home fa-2x text-{{ $employee->remote_work ?? false ? 'info' : 'secondary' }}"></i>
                                </div>
                                <h6 class="card-title">ทำงานจากที่บ้าน</h6>
                                <span class="badge bg-{{ $employee->remote_work ?? false ? 'info' : 'secondary' }} fs-6">
                                    {{ $employee->remote_work ?? false ? 'อนุญาต' : 'ไม่อนุญาต' }}
                                </span>
                                <p class="card-text mt-2 small text-muted">
                                    {{ $employee->remote_work ?? false ? 'สามารถทำงานจากที่บ้านได้' : 'ต้องทำงานที่สำนักงานเท่านั้น' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Admin Access -->
                    <div class="col-md-6">
                        <div class="card border-{{ $employee->admin_access ?? false ? 'danger' : 'secondary' }} h-100">
                            <div class="card-body text-center">
                                <div class="mb-2">
                                    <i class="fas fa-user-shield fa-2x text-{{ $employee->admin_access ?? false ? 'danger' : 'secondary' }}"></i>
                                </div>
                                <h6 class="card-title">แผงควบคุมระบบ</h6>
                                <span class="badge bg-{{ $employee->admin_access ?? false ? 'danger' : 'secondary' }} fs-6">
                                    {{ $employee->admin_access ?? false ? 'อนุญาต' : 'ไม่อนุญาต' }}
                                </span>
                                <p class="card-text mt-2 small text-muted">
                                    {{ $employee->admin_access ?? false ? 'สามารถเข้าถึงแผงควบคุมผู้ดูแลระบบได้' : 'ไม่สามารถเข้าถึงแผงควบคุมได้' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Permission Summary -->
                <div class="mt-3">
                    <div class="alert alert-{{ $employee->vpn_access || $employee->color_printing || ($employee->remote_work ?? false) || ($employee->admin_access ?? false) ? 'success' : 'info' }} mb-0">
                        <h6 class="alert-heading">
                            <i class="fas fa-info-circle me-2"></i>สรุปสิทธิ์พิเศษ
                        </h6>
                        @php
                            $permissionCount = 0;
                            $permissions = [];
                            if ($employee->vpn_access) { $permissions[] = 'VPN'; $permissionCount++; }
                            if ($employee->color_printing) { $permissions[] = 'ปริ้นสี'; $permissionCount++; }
                            if ($employee->remote_work ?? false) { $permissions[] = 'ทำงานจากบ้าน'; $permissionCount++; }
                            if ($employee->admin_access ?? false) { $permissions[] = 'แผงควบคุม'; $permissionCount++; }
                        @endphp
                        <p class="mb-0">
                            <strong>{{ $employee->full_name_th }}</strong> มีสิทธิ์พิเศษ: 
                            @if($permissionCount > 0)
                                <span class="fw-bold text-success">{{ implode(', ', $permissions) }} ({{ $permissionCount }} รายการ)</span>
                            @else
                                <span class="fw-bold text-muted">ไม่มีสิทธิ์พิเศษ</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column -->
    <div class="col-md-6">
        <!-- Computer System (ใช้เหมือนเดิม) -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-desktop text-success me-2"></i>ระบบคอมพิวเตอร์
                    <span class="badge bg-success ms-2">
                        <i class="fas fa-eye me-1"></i>แสดงเลย
                    </span>
                </h5>
            </div>
            <div class="card-body">
                <div class="info-item-inline">
                    <span class="info-label">Username:</span>
                    <span class="info-value">
                        <div class="d-flex align-items-center">
                            <code class="me-2">{{ $employee->username }}</code>
                            <button class="btn btn-outline-secondary btn-xs" onclick="copyToClipboard('{{ $employee->username }}')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </span>
                </div>
                
                <div class="info-item-inline">
                    <span class="info-label">รหัสผ่านคอมพิวเตอร์:</span>
                    <span class="info-value">
                        @if($canSeePasswords)
                            <div class="d-flex align-items-center">
                                <div class="password-container me-2">
                                    @if($employee->computer_password)
                                        <code class="text-success fw-bold">{{ $employee->computer_password }}</code>
                                    @else
                                        <span class="text-muted fst-italic">ไม่มีข้อมูล</span>
                                    @endif
                                </div>
                                @if($employee->computer_password)
                                    <button class="btn btn-outline-secondary btn-xs" onclick="copyToClipboard('{{ $employee->computer_password }}')">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                @endif
                            </div>
                        @else
                            <span class="text-muted">
                                <i class="fas fa-lock me-1"></i>[ซ่อน - เฉพาะ Admin]
                            </span>
                        @endif
                    </span>
                </div>
                
                @if($employee->copier_code)
                    <div class="info-item-inline">
                        <span class="info-label">รหัสเครื่องถ่ายเอกสาร:</span>
                        <span class="info-value">
                            <div class="d-flex align-items-center">
                                <code class="me-2">{{ $employee->copier_code }}</code>
                                <button class="btn btn-outline-secondary btn-xs" onclick="copyToClipboard('{{ $employee->copier_code }}')">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Email and Login System (ใช้เหมือนเดิม) -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-envelope text-info me-2"></i>ระบบอีเมลและเข้าสู่ระบบ
                    <span class="badge bg-success ms-2">แยกแล้ว</span>
                    <span class="badge bg-info ms-2">
                        <i class="fas fa-eye me-1"></i>แสดงเลย
                    </span>
                </h5>
            </div>
            <div class="card-body">
                <!-- Email System -->
                <h6 class="text-info mb-3">
                    <i class="fas fa-envelope me-2"></i>ระบบอีเมล
                </h6>
                
                <div class="info-item-inline">
                    <span class="info-label">อีเมล:</span>
                    <span class="info-value">
                        <div class="d-flex align-items-center">
                            <code class="me-2">{{ $employee->email }}</code>
                            <button class="btn btn-outline-secondary btn-xs" onclick="copyToClipboard('{{ $employee->email }}')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </span>
                </div>
                
                <div class="info-item-inline">
                    <span class="info-label">รหัสผ่านอีเมล:</span>
                    <span class="info-value">
                        @if($canSeePasswords)
                            <div class="d-flex align-items-center">
                                <div class="password-container me-2">
                                    @if($employee->email_password)
                                        <code class="text-warning fw-bold">{{ $employee->email_password }}</code>
                                    @else
                                        <span class="text-muted fst-italic">ไม่มีข้อมูล</span>
                                    @endif
                                </div>
                                @if($employee->email_password)
                                    <button class="btn btn-outline-secondary btn-xs" onclick="copyToClipboard('{{ $employee->email_password }}')">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                @endif
                            </div>
                        @else
                            <span class="text-muted">
                                <i class="fas fa-lock me-1"></i>[ซ่อน - เฉพาะ Admin]
                            </span>
                        @endif
                    </span>
                </div>
                
                <hr class="my-3">
                
                <!-- Login System -->
                <h6 class="text-success mb-3">
                    <i class="fas fa-sign-in-alt me-2"></i>ระบบเข้าสู่ระบบ
                </h6>
                
                <div class="info-item-inline">
                    <span class="info-label">อีเมลเข้าระบบ:</span>
                    <span class="info-value">
                        <div class="d-flex align-items-center">
                            <code class="me-2">{{ $employee->login_email ?: $employee->email }}</code>
                            <span class="badge bg-secondary ms-2">Auto Sync</span>
                        </div>
                    </span>
                </div>
                
                <div class="info-item-inline">
                    <span class="info-label">รหัสผ่านเข้าระบบ:</span>
                    <span class="info-value">
                        @if($canSeePasswords)
                            <div class="d-flex align-items-center">
                                <div class="password-container me-2">
                                    <code class="text-primary fw-bold">••••••••••••</code>
                                    <small class="text-success ms-2">
                                        <i class="fas fa-shield-alt me-1"></i>Hash Protected
                                    </small>
                                </div>
                            </div>
                            <div class="mt-2">
                                <div class="alert alert-info p-2 mb-0">
                                    <small>
                                        <i class="fas fa-info-circle me-1"></i>
                                        <strong>Password Login:</strong> ไม่แสดงเพื่อความปลอดภัย (มี hash protection)
                                    </small>
                                </div>
                            </div>
                        @else
                            <span class="text-muted">
                                <i class="fas fa-lock me-1"></i>[ซ่อน - เฉพาะ Admin]
                            </span>
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <!-- Express System (ใช้เหมือนเดิม) -->
        @if($employee->express_username)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bolt text-warning me-2"></i>โปรแกรม Express v2.0
                        <span class="badge bg-warning text-dark ms-2">Enhanced</span>
                        <span class="badge bg-success ms-2">
                            <i class="fas fa-eye me-1"></i>แสดงเลย
                        </span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-success mb-3">
                        <i class="fas fa-check-circle me-2"></i>
                        <strong>Express v2.0 Enhanced:</strong> รองรับ Username 1-7 ตัวอักษร, Password 4 ตัวเลขไม่ซ้ำ
                    </div>
                    
                    <div class="info-item-inline">
                        <span class="info-label">Username Express:</span>
                        <span class="info-value">
                            <div class="d-flex align-items-center">
                                <code class="text-warning fw-bold me-2">{{ $employee->express_username }}</code>
                                <span class="badge bg-info me-2">{{ strlen($employee->express_username) }} ตัว</span>
                                <button class="btn btn-outline-secondary btn-xs" onclick="copyToClipboard('{{ $employee->express_username }}')">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </span>
                    </div>
                    
                    <div class="info-item-inline">
                        <span class="info-label">รหัสผ่าน Express:</span>
                        <span class="info-value">
                            <div class="d-flex align-items-center">
                                <div class="password-container me-2">
                                    <code class="text-danger fw-bold">{{ $employee->express_password }}</code>
                                </div>
                                <span class="badge bg-success me-2">4 ตัวเลขไม่ซ้ำ</span>
                                <button class="btn btn-outline-secondary btn-xs" onclick="copyToClipboard('{{ $employee->express_password }}')">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </span>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- ✅ ENHANCED Summary Card - System Status (with Branch Debug) -->
<div class="card mb-4">
    <div class="card-header" style="background: linear-gradient(45deg, #B54544, #E6952A); color: white;">
        <h5 class="mb-0" style="color: white; text-shadow: 1px 1px 2px rgba(0,0,0,0.3);">
            <i class="fas fa-chart-line me-2"></i>สรุปข้อมูลระบบ - {{ $employee->full_name_th }}
            @if(config('app.debug'))
                <span class="badge bg-secondary ms-2">DEBUG MODE</span>
            @endif
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <h6 class="text-primary">🏢 ข้อมูลองค์กร</h6>
                <ul class="list-unstyled">
                    <li><strong>สาขา:</strong> 
                        @if($employee->branch)
                            <span class="badge text-white" style="background: linear-gradient(45deg, #B54544, #E6952A);">{{ $employee->branch->name }}</span>
                            @if(config('app.debug'))
                                <br><small class="text-muted">ID: {{ $employee->branch->id }}</small>
                            @endif
                        @elseif($employee->branch_id)
                            <span class="badge bg-danger">ERROR (ID: {{ $employee->branch_id }})</span>
                        @else
                            <span class="text-muted">ไม่ระบุ</span>
                            @if(config('app.debug'))
                                <br><small class="text-muted">branch_id: NULL</small>
                            @endif
                        @endif
                    </li>
                    <li><strong>แผนก:</strong> {{ $employee->department->name ?? 'ไม่ระบุ' }}</li>
                    <li><strong>ตำแหน่ง:</strong> {{ $employee->position }}</li>
                    <li><strong>สิทธิ์:</strong> 
                        <span class="badge bg-primary">{{ $employee->role_display ?? ucfirst($employee->role) }}</span>
                    </li>
                </ul>
            </div>
            <div class="col-md-3">
                <h6 class="text-success">🖥️ ระบบคอมพิวเตอร์</h6>
                <ul class="list-unstyled">
                    <li><strong>Username:</strong> {{ $employee->username }}</li>
                    <li><strong>รหัสผ่าน:</strong> 
                        @if($canSeePasswords && $employee->computer_password)
                            <span class="badge bg-success">มีข้อมูล</span>
                        @else
                            <span class="badge bg-secondary">{{ $employee->computer_password ? 'มีข้อมูล' : 'ไม่มี' }}</span>
                        @endif
                    </li>
                    <li><strong>Copier:</strong> 
                        <span class="badge bg-{{ $employee->copier_code ? 'info' : 'secondary' }}">
                            {{ $employee->copier_code ? 'มีรหัส' : 'ไม่มี' }}
                        </span>
                    </li>
                </ul>
            </div>
            <div class="col-md-3">
                <h6 class="text-info">📧 ระบบอีเมล</h6>
                <ul class="list-unstyled">
                    <li><strong>อีเมล:</strong> {{ Str::limit($employee->email, 20) }}</li>
                    <li><strong>รหัสผ่าน:</strong> 
                        @if($canSeePasswords && $employee->email_password)
                            <span class="badge bg-warning text-dark">มีข้อมูล</span>
                        @else
                            <span class="badge bg-secondary">{{ $employee->email_password ? 'มีข้อมูล' : 'ไม่มี' }}</span>
                        @endif
                    </li>
                    <li><strong>Login:</strong> 
                        <span class="badge bg-success">Hash Protected</span>
                    </li>
                </ul>
            </div>
            <div class="col-md-3">
                <h6 class="text-warning">⚡ Express & Permissions</h6>
                <ul class="list-unstyled">
                    <li><strong>Express:</strong> 
                        @if($employee->express_username)
                            <span class="badge bg-warning text-dark">
                                <i class="fas fa-bolt me-1"></i>Active
                            </span>
                        @else
                            <span class="badge bg-secondary">ไม่ใช้งาน</span>
                        @endif
                    </li>
                    <li><strong>VPN:</strong> 
                        <span class="badge bg-{{ $employee->vpn_access ? 'success' : 'secondary' }}">
                            {{ $employee->vpn_access ? 'อนุญาต' : 'ไม่อนุญาต' }}
                        </span>
                    </li>
                    <li><strong>ปริ้นสี:</strong> 
                        <span class="badge bg-{{ $employee->color_printing ? 'warning text-dark' : 'secondary' }}">
                            {{ $employee->color_printing ? 'อนุญาต' : 'ไม่อนุญาต' }}
                        </span>
                    </li>
                </ul>
            </div>
        </div>
        
        <hr>
        
        <div class="row">
            <div class="col-12">
                <div class="alert alert-success mb-0">
                    <div class="row">
                        <div class="col-md-4">
                            <h6><i class="fas fa-check-circle me-1"></i> ✅ ระบบที่พร้อม:</h6>
                            <ul class="mb-0">
                                <li>🏢 <strong>Branch System:</strong> 
                                    @if($employee->branch)
                                        <span class="text-success">รองรับแล้ว</span>
                                    @elseif($employee->branch_id)
                                        <span class="text-danger">ERROR</span>
                                    @else
                                        <span class="text-muted">ไม่ระบุ</span>
                                    @endif
                                </li>
                                <li>🔒 <strong>Separated Passwords:</strong> แยกแล้ว</li>
                                <li>👁️ <strong>Direct Display:</strong> แสดงได้ทันใจ</li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <h6><i class="fas fa-tools me-1"></i> 🔧 Features:</h6>
                            <ul class="mb-0">
                                <li>📞 <strong>Phone Duplicates:</strong> อนุญาตแล้ว</li>
                                <li>⚡ <strong>Express v2.0:</strong> Enhanced</li>
                                <li>🎨 <strong>ITMS Theme:</strong> สีแดง-ส้ม</li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <h6><i class="fas fa-copy me-1"></i> 📋 Debug & Copy:</h6>
                            <ul class="mb-0">
                                <li>📋 <strong>One-Click Copy:</strong> ทุก field</li>
                                <li>🔓 <strong>Admin View:</strong> เห็นรหัสผ่าน</li>
                                <li>🐛 <strong>Debug Mode:</strong> 
                                    @if(config('app.debug'))
                                        <span class="text-info">ON</span>
                                    @else
                                        <span class="text-muted">OFF</span>
                                    @endif
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.info-item-inline {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.75rem;
    padding: 0.5rem 0;
    border-bottom: 1px solid #f8f9fa;
}

.info-item-inline:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.info-label {
    font-weight: 600;
    color: #495057;
    min-width: 140px;
    flex-shrink: 0;
}

.info-value {
    text-align: right;
    color: #212529;
    flex-grow: 1;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 0.5rem;
}

.btn-xs {
    padding: 0.125rem 0.375rem;
    font-size: 0.75rem;
    line-height: 1.5;
    border-radius: 0.25rem;
}

/* ✅ Password display styling */
.password-container {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.password-container code {
    font-family: 'Courier New', monospace;
    font-size: 0.9rem;
    padding: 0.25rem 0.5rem;
    background-color: rgba(0,0,0,0.05);
    border-radius: 0.25rem;
    border: 1px solid rgba(0,0,0,0.1);
}

/* Different colors for different password types */
.password-container code.text-success {
    background-color: rgba(25, 135, 84, 0.1);
    border-color: rgba(25, 135, 84, 0.2);
}

.password-container code.text-warning {
    background-color: rgba(255, 193, 7, 0.1);
    border-color: rgba(255, 193, 7, 0.2);
}

.password-container code.text-danger {
    background-color: rgba(220, 53, 69, 0.1);
    border-color: rgba(220, 53, 69, 0.2);
}

.password-container code.text-primary {
    background-color: rgba(13, 110, 253, 0.1);
    border-color: rgba(13, 110, 253, 0.2);
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
}

/* ✅ ITMS Theme Integration */
.badge-gradient {
    background: linear-gradient(45deg, #B54544, #E6952A);
    color: white;
}

/* Permission cards styling */
.card.border-success {
    border-color: #198754 !important;
}

.card.border-warning {
    border-color: #ffc107 !important;
}

.card.border-info {
    border-color: #0dcaf0 !important;
}

.card.border-danger {
    border-color: #dc3545 !important;
}

.card.border-secondary {
    border-color: #6c757d !important;
}

/* Copy button hover effect */
.btn-xs:hover {
    transform: scale(1.1);
    transition: transform 0.2s ease;
}

/* ✅ Branch card special styling */
.card-header h5 i[style*="color: #B54544"] {
    text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
}

/* Enhanced gradient badges */
.badge[style*="background: linear-gradient"] {
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
}

/* ✅ DEBUG MODE STYLES */
.alert.alert-info[role="alert"] h6 {
    color: #084298;
}

.badge.bg-secondary {
    font-size: 0.7rem;
}

/* Responsive improvements */
@media (max-width: 768px) {
    .info-item-inline {
        flex-direction: column;
        align-items: flex-start;
        text-align: left;
    }
    
    .info-value {
        justify-content: flex-start;
        margin-top: 0.25rem;
        width: 100%;
    }
    
    .info-label {
        min-width: auto;
    }
    
    .password-container {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .card-header h2 {
        font-size: 1.25rem;
    }
    
    .card-header h5 {
        font-size: 1rem;
    }
}

/* Summary card enhancements */
.card-header[style*="background: linear-gradient"] h5 {
    text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
}

/* Loading and interaction states */
.btn-xs:active {
    transform: scale(0.95);
}

.btn-xs:disabled {
    opacity: 0.6;
    transform: none;
}

/* Enhanced notification for copy success */
.copy-success {
    animation: copyPulse 0.3s ease;
}

@keyframes copyPulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🎉 Employee Show Page Loaded - Branch System + Debug Mode Complete');
    console.log('✅ Features: Branch Display, Direct Password Display, Copy Functions, Enhanced UI, Debug Mode');
    console.log('🏢 Branch System: Full Integration Complete with Debug');
    console.log('🎨 ITMS Theme: Red-Orange Perfect');
    console.log('🔓 Admin Mode: Passwords shown immediately without toggle');
    console.log('📋 Copy Functions: All fields copyable');
    console.log('🐛 Debug Mode: ' + ({{ config('app.debug') ? 'true' : 'false' }} ? 'ENABLED' : 'DISABLED'));
    
    // ✅ Branch System Debug
    const employeeId = {{ $employee->id }};
    const branchId = {{ $employee->branch_id ?? 'null' }};
    const hasBranch = {{ $employee->branch ? 'true' : 'false' }};
    
    console.log('🏢 Branch Debug Info:', {
        employee_id: employeeId,
        branch_id: branchId,
        has_branch_relationship: hasBranch,
        branch_name: '{{ $employee->branch ? $employee->branch->name : "NULL" }}'
    });
    
    @if($employee->branch_id && !$employee->branch)
        console.error('⚠️ BRANCH ISSUE DETECTED:', {
            message: 'Employee has branch_id but no branch relationship',
            employee_id: employeeId,
            branch_id: branchId,
            suggested_action: 'Check branches table or fix relationship'
        });
    @endif
});

// Copy to clipboard functionality
function copyToClipboard(text) {
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(function() {
            showNotification('✅ คัดลอกเรียบร้อยแล้ว: ' + maskPassword(text), 'success');
        }).catch(function(err) {
            console.error('Failed to copy: ', err);
            fallbackCopyTextToClipboard(text);
        });
    } else {
        fallbackCopyTextToClipboard(text);
    }
}

// Fallback copy method for older browsers
function fallbackCopyTextToClipboard(text) {
    const textArea = document.createElement("textarea");
    textArea.value = text;
    textArea.style.top = "0";
    textArea.style.left = "0";
    textArea.style.position = "fixed";
    
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
        const successful = document.execCommand('copy');
        if (successful) {
            showNotification('✅ คัดลอกเรียบร้อยแล้ว: ' + maskPassword(text), 'success');
        } else {
            showNotification('❌ ไม่สามารถคัดลอกได้', 'error');
        }
    } catch (err) {
        console.error('Fallback: Oops, unable to copy', err);
        showNotification('❌ ไม่สามารถคัดลอกได้', 'error');
    }
    
    document.body.removeChild(textArea);
}

// Mask password for notification (show first 2 chars + ***)
function maskPassword(text) {
    if (text.length <= 2) {
        return text;
    } else if (text.includes('@')) {
        // Email address - don't mask
        return text;
    } else if (text.length <= 4) {
        // Short passwords - show first char
        return text.charAt(0) + '***';
    } else {
        // Long passwords - show first 2 chars
        return text.substring(0, 2) + '***';
    }
}

// Show notification
function showNotification(message, type = 'success') {
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
    }, 4000);
}

// Add visual feedback for copy buttons
document.querySelectorAll('.btn-xs').forEach(button => {
    button.addEventListener('click', function(e) {
        // Add visual feedback
        const originalHTML = this.innerHTML;
        this.innerHTML = '<i class="fas fa-check text-success"></i>';
        this.disabled = true;
        this.classList.add('copy-success');
        
        setTimeout(() => {
            this.innerHTML = originalHTML;
            this.disabled = false;
            this.classList.remove('copy-success');
        }, 1000);
    });
});

// ✅ Enhanced Branch System Integration Test
function testBranchSystem() {
    console.log('🏢 Branch System Status:');
    const branchElement = document.querySelector('[style*="background: linear-gradient(45deg, #B54544, #E6952A)"]');
    if (branchElement) {
        console.log('✅ Branch badge found with ITMS theme');
    } else {
        console.log('❌ No branch found for this employee');
    }
    
    const branchCard = document.querySelector('h5 i[style*="color: #B54544"]');
    if (branchCard) {
        console.log('✅ Branch information card rendered');
    }
    
    // ✅ Check for branch issues
    const employeeId = {{ $employee->id }};
    const branchId = {{ $employee->branch_id ?? 'null' }};
    const hasBranch = {{ $employee->branch ? 'true' : 'false' }};
    
    if (branchId && !hasBranch) {
        console.error('⚠️ Branch relationship issue detected');
        console.log('📋 Suggested solutions:');
        console.log('1. Check if branch ID ' + branchId + ' exists in branches table');
        console.log('2. Check if branch is active (is_active = true)');
        console.log('3. Check for soft deletes in branches table');
        console.log('4. Verify foreign key constraints');
    }
    
    console.log('✅ Branch system integration test complete');
}

// Test on load
setTimeout(testBranchSystem, 1000);

console.log('📝 Employee Show Page Script Loaded - Complete Integration with Debug');
console.log('🔧 Available functions: copyToClipboard(), showNotification(), maskPassword(), testBranchSystem()');
console.log('✅ Features: Branch Display, Direct Password Display, Enhanced Copy Function, Visual Feedback, Debug Mode');
console.log('🎯 Password System: Computer, Email, Express - All visible for Admins');
console.log('🔓 Admin Mode: No toggle buttons needed - passwords shown immediately');
console.log('🏢 Branch System: Full display with ITMS theme integration + Debug Mode');
console.log('🎨 ITMS Theme: Perfect red-orange gradient throughout');
console.log('🐛 Debug Features: Branch relationship debugging, data validation, issue detection');
</script>
@endpush
