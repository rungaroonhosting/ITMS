@extends('layouts.app')

@section('title', 'ข้อมูลพนักงาน - ' . $employee->full_name_th)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('employees.index') }}">จัดการพนักงาน</a></li>
    <li class="breadcrumb-item active">{{ $employee->full_name_th }}</li>
@endsection

@section('content')

@php
    $photoUrl = null;
    $photoExists = false;
    $photoInfo = [];
    
    if ($employee->photo) {
        $cleanPhotoPath = $employee->photo;
        
        if (!str_starts_with($cleanPhotoPath, 'employees/photos/')) {
            $cleanPhotoPath = 'employees/photos/' . $cleanPhotoPath;
        }
        
        $photoUrl = asset('storage/' . $cleanPhotoPath);
        
        $storagePath = storage_path('app/public/' . $cleanPhotoPath);
        $publicPath = public_path('storage/' . $cleanPhotoPath);
        
        $photoExists = file_exists($storagePath) || file_exists($publicPath);
        
        if (!$photoExists) {
            $alternativePaths = [
                storage_path('app/public/' . $employee->photo),
                storage_path('app/public/employees/photos/' . basename($employee->photo)),
            ];
            
            foreach ($alternativePaths as $altPath) {
                if (file_exists($altPath)) {
                    $storagePath = $altPath;
                    $photoExists = true;
                    
                    $relativePath = str_replace(storage_path('app/public/'), '', $altPath);
                    $photoUrl = asset('storage/' . $relativePath);
                    break;
                }
            }
        }
        
        if ($photoExists) {
            try {
                $photoInfo = [
                    'exists' => true,
                    'photo_url' => $photoUrl,
                    'photo_path' => $cleanPhotoPath,
                    'storage_path' => $storagePath,
                    'file_exists' => file_exists($storagePath),
                ];
                
                if (file_exists($storagePath)) {
                    $fileSize = filesize($storagePath);
                    $photoInfo['file_size'] = $fileSize;
                    $photoInfo['file_size_human'] = formatBytes($fileSize);
                    $photoInfo['last_modified'] = date('d/m/Y H:i:s', filemtime($storagePath));
                    
                    $imageInfo = @getimagesize($storagePath);
                    if ($imageInfo !== false) {
                        $photoInfo['width'] = $imageInfo[0];
                        $photoInfo['height'] = $imageInfo[1];
                        $photoInfo['mime_type'] = $imageInfo['mime'];
                    }
                }
            } catch (Exception $e) {
                $photoInfo = ['error' => $e->getMessage(), 'exists' => false];
                $photoExists = false;
            }
        }
    }
    
    $initials = strtoupper(substr($employee->first_name_en ?? 'U', 0, 1) . substr($employee->last_name_en ?? 'N', 0, 1));
    $colors = ['B54544', 'E6952A', '0d6efd', '198754', '6f42c1', 'dc3545', 'fd7e14'];
    $colorIndex = ($employee->id % count($colors));
    $backgroundColor = $colors[$colorIndex];
    $avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode($initials) . "&size=200&background={$backgroundColor}&color=ffffff&bold=true&format=png";
    
    function formatBytes($size, $precision = 2) {
        if ($size === 0) return '0 B';
        $units = ['B', 'KB', 'MB', 'GB'];
        $base = log($size, 1024);
        return round(pow(1024, $base - floor($base)), $precision) . ' ' . $units[floor($base)];
    }
@endphp

<div class="card mb-4">
    <div class="card-header" style="background: linear-gradient(135deg, #B54544 0%, #E6952A 100%); color: white; border: none;">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="employee-photo-container">
                            @if($employee->photo && $photoExists)
                                <img src="{{ $photoUrl }}" 
                                     alt="รูปภาพ {{ $employee->full_name_th }}" 
                                     class="employee-photo-large"
                                     id="headerPhoto"
                                     onclick="showPhotoModal('{{ $photoUrl }}', '{{ $employee->full_name_th }}', '{{ basename($employee->photo) }}')"
                                     onerror="handlePhotoError(this, '{{ $avatarUrl }}', '{{ $employee->full_name_th }}')">
                            @elseif($employee->photo && !$photoExists)
                                <img src="{{ $avatarUrl }}" 
                                     alt="Avatar {{ $employee->full_name_th }}" 
                                     class="employee-photo-large photo-missing"
                                     id="headerPhoto"
                                     title="ไฟล์รูปภาพหาย - แสดง Avatar แทน">
                            @else
                                <img src="{{ $avatarUrl }}" 
                                     alt="Avatar {{ $employee->full_name_th }}" 
                                     class="employee-photo-large"
                                     id="headerPhoto">
                            @endif
                            
                            <div class="photo-status-badge">
                                @if($employee->photo && $photoExists)
                                    <span class="badge bg-success" title="มีรูปภาพ - คลิกเพื่อดูขนาดเต็ม">
                                        <i class="fas fa-camera me-1"></i>รูปภาพ
                                    </span>
                                @elseif($employee->photo && !$photoExists)
                                    <span class="badge bg-warning text-dark" title="ไฟล์รูปภาพหาย">
                                        <i class="fas fa-exclamation-triangle me-1"></i>ไฟล์หาย
                                    </span>
                                @else
                                    <span class="badge bg-secondary" title="ใช้ Avatar อัตโนมัติ">
                                        <i class="fas fa-user-circle me-1"></i>Avatar
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div>
                        <h2 class="mb-1" style="color: #fff; text-shadow: 1px 1px 2px rgba(0,0,0,0.3);">
                            {{ $employee->full_name_th }}
                        </h2>
                        <h5 class="mb-2" style="color: #fff; text-shadow: 1px 1px 2px rgba(0,0,0,0.3);">
                            {{ $employee->full_name_en }}
                        </h5>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-primary">
                                {{ $employee->employee_code }}
                            </span>
                            <span class="badge bg-{{ $employee->status == 'active' ? 'success' : 'secondary' }}">
                                {{ $employee->status == 'active' ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                            </span>
                            <span class="badge bg-info">
                                {{ $employee->role_display ?? ucfirst($employee->role) }}
                            </span>
                            @if($employee->department)
                                <span class="badge bg-warning text-dark">
                                    {{ $employee->department->name }}
                                    @if($employee->department->express_enabled ?? false)
                                        <i class="fas fa-bolt ms-1"></i>
                                    @endif
                                </span>
                            @endif
                            
                            @if($employee->branch)
                                <span class="badge text-white" style="background: linear-gradient(45deg, #B54544, #E6952A);">
                                    <i class="fas fa-building me-1"></i>{{ $employee->branch->name }}
                                </span>
                            @else
                                <span class="badge bg-secondary">
                                    <i class="fas fa-building me-1"></i>ไม่ระบุสาขา
                                </span>
                            @endif
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
                        ($currentUser->role === 'hr' && $employee->role === 'employee')
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

@if($employee->photo && !$photoExists)
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <h6 class="fw-bold">
            <i class="fas fa-exclamation-triangle me-2"></i>ไฟล์รูปภาพหาย
        </h6>
        <p class="mb-1">
            <strong>ไฟล์:</strong> <code>{{ basename($employee->photo) }}</code> ไม่พบในระบบ<br>
            <strong>Path ที่ค้นหา:</strong> <code>{{ $photoUrl }}</code>
        </p>
        <p class="mb-0">
            <strong>วิธีแก้ไข:</strong> 
            @if($canEdit)
                <a href="{{ route('employees.edit', $employee) }}" class="btn btn-sm btn-warning">
                    <i class="fas fa-camera me-1"></i>อัปโหลดรูปใหม่
                </a>
                <button class="btn btn-sm btn-info" onclick="debugPhotoSystem()">
                    <i class="fas fa-bug me-1"></i>Debug
                </button>
            @else
                กรุณาติดต่อ Administrator เพื่อแก้ไข
            @endif
        </p>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(auth()->user() && in_array(auth()->user()->role, ['super_admin', 'it_admin']))
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <h6 class="fw-bold">
            <i class="fas fa-bug me-2"></i>Debug Information (Admin Only)
        </h6>
        <div class="row">
            <div class="col-md-6">
                <strong>Database:</strong><br>
                <small>
                    Photo Field: <code>{{ $employee->photo ?? 'NULL' }}</code><br>
                    Employee ID: <code>{{ $employee->id }}</code><br>
                    Photo URL: <code>{{ $photoUrl ?? 'N/A' }}</code>
                </small>
            </div>
            <div class="col-md-6">
                <strong>File System:</strong><br>
                <small>
                    File Exists: <code>{{ $photoExists ? 'TRUE' : 'FALSE' }}</code><br>
                    Storage Path: <code>{{ $photoInfo['storage_path'] ?? 'N/A' }}</code><br>
                    Avatar URL: <code>{{ $avatarUrl }}</code>
                </small>
            </div>
        </div>
        @if(!empty($photoInfo) && isset($photoInfo['error']))
            <div class="mt-2">
                <strong class="text-danger">Error:</strong>
                <pre class="bg-danger text-white p-2 rounded"><code>{{ $photoInfo['error'] }}</code></pre>
            </div>
        @endif
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-camera me-2" style="color: #B54544;"></i>รูปภาพพนักงาน
                    <span class="badge text-white ms-2" style="background: linear-gradient(45deg, #B54544, #E6952A);">
                        Photo System
                    </span>
                    @if($employee->photo && !$photoExists)
                        <span class="badge bg-warning text-dark ms-2">
                            <i class="fas fa-exclamation-triangle me-1"></i>ไฟล์หาย
                        </span>
                    @endif
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="employee-photo-display text-center">
                            @if($employee->photo && $photoExists)
                                <div class="photo-container mb-3">
                                    <img src="{{ $photoUrl }}" 
                                         alt="รูปภาพ {{ $employee->full_name_th }}" 
                                         class="employee-photo-display-img"
                                         id="mainPhotoDisplay"
                                         onclick="showPhotoModal('{{ $photoUrl }}', '{{ $employee->full_name_th }}', '{{ basename($employee->photo) }}')"
                                         onerror="handlePhotoError(this, '{{ $avatarUrl }}', '{{ $employee->full_name_th }}')">
                                    <div class="photo-overlay">
                                        <i class="fas fa-search-plus"></i>
                                        <div class="mt-2">คลิกเพื่อดูขนาดเต็ม</div>
                                    </div>
                                </div>
                                <div class="photo-info">
                                    <h6 class="text-success">
                                        <i class="fas fa-check-circle me-2"></i>มีรูปภาพแล้ว
                                    </h6>
                                    <p class="text-muted mb-2">คลิกที่รูปเพื่อดูขนาดเต็ม</p>
                                    <div class="d-flex justify-content-center gap-2 flex-wrap">
                                        <button class="btn btn-outline-primary btn-sm" onclick="showPhotoModal('{{ $photoUrl }}', '{{ $employee->full_name_th }}', '{{ basename($employee->photo) }}')">
                                            <i class="fas fa-search-plus me-1"></i>ดูขนาดเต็ม
                                        </button>
                                        <button class="btn btn-outline-success btn-sm" onclick="downloadPhoto('{{ $photoUrl }}', '{{ $employee->full_name_th }}', '{{ basename($employee->photo) }}')">
                                            <i class="fas fa-download me-1"></i>ดาวน์โหลด
                                        </button>
                                        <button class="btn btn-outline-info btn-sm" onclick="copyPhotoUrl('{{ $photoUrl }}')">
                                            <i class="fas fa-link me-1"></i>Copy URL
                                        </button>
                                        @if($canEdit)
                                            <a href="{{ route('employees.edit', $employee) }}" class="btn btn-outline-warning btn-sm">
                                                <i class="fas fa-edit me-1"></i>แก้ไขรูป
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @elseif($employee->photo && !$photoExists)
                                <div class="missing-photo-container mb-3">
                                    <img src="{{ $avatarUrl }}" 
                                         alt="Avatar {{ $employee->full_name_th }}" 
                                         class="employee-photo-display-img avatar-generated photo-error"
                                         id="mainPhotoDisplay">
                                    <div class="photo-error-overlay">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <div class="mt-2">ไฟล์รูปภาพหาย</div>
                                    </div>
                                </div>
                                <div class="photo-error-info">
                                    <h6 class="text-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i>ไฟล์รูปภาพหาย
                                    </h6>
                                    <p class="text-muted mb-2">
                                        มีข้อมูลรูปภาพในระบบแต่ไม่พบไฟล์<br>
                                        <small class="text-muted">ไฟล์: <code>{{ basename($employee->photo) }}</code></small>
                                    </p>
                                    <div class="d-flex justify-content-center gap-2">
                                        @if($canEdit)
                                            <a href="{{ route('employees.edit', $employee) }}" class="btn btn-warning btn-sm">
                                                <i class="fas fa-camera me-1"></i>อัปโหลดรูปใหม่
                                            </a>
                                        @endif
                                        <button class="btn btn-outline-secondary btn-sm" onclick="debugPhotoSystem()">
                                            <i class="fas fa-bug me-1"></i>Debug
                                        </button>
                                    </div>
                                </div>
                            @else
                                <div class="no-photo-container mb-3">
                                    <img src="{{ $avatarUrl }}" 
                                         alt="Avatar {{ $employee->full_name_th }}" 
                                         class="employee-photo-display-img avatar-generated"
                                         id="mainPhotoDisplay">
                                </div>
                                <div class="no-photo-info">
                                    <h6 class="text-muted">
                                        <i class="fas fa-user-circle me-2"></i>ยังไม่มีรูปภาพ
                                    </h6>
                                    <p class="text-muted mb-2">ใช้ Avatar อัตโนมัติจากชื่อภาษาอังกฤษ</p>
                                    <div class="d-flex justify-content-center gap-2">
                                        @if($canEdit)
                                            <a href="{{ route('employees.edit', $employee) }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-camera me-1"></i>เพิ่มรูปภาพ
                                            </a>
                                        @endif
                                        <button class="btn btn-outline-success btn-sm" onclick="downloadPhoto('{{ $avatarUrl }}', '{{ $employee->full_name_th }}', 'avatar.png')">
                                            <i class="fas fa-download me-1"></i>ดาวน์โหลด Avatar
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="photo-stats">
                            <h6 class="text-info mb-3">
                                <i class="fas fa-chart-bar me-2"></i>ข้อมูลรูปภาพ
                            </h6>
                            
                            <div class="stat-item">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">สถานะ:</span>
                                    @if($employee->photo && $photoExists)
                                        <span class="badge bg-success">มีรูปภาพ</span>
                                    @elseif($employee->photo && !$photoExists)
                                        <span class="badge bg-warning text-dark">ไฟล์หาย</span>
                                    @else
                                        <span class="badge bg-secondary">ไม่มีรูป</span>
                                    @endif
                                </div>
                            </div>
                            
                            @if($employee->photo)
                                <div class="stat-item">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">ไฟล์:</span>
                                        <span class="text-dark small">{{ basename($employee->photo) }}</span>
                                    </div>
                                </div>
                                
                                @if($photoExists && !empty($photoInfo))
                                    @if(isset($photoInfo['file_size']) && $photoInfo['file_size'])
                                        <div class="stat-item">
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">ขนาด:</span>
                                                <span class="text-success">{{ $photoInfo['file_size_human'] ?? 'ไม่ทราบ' }}</span>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if(isset($photoInfo['width']) && isset($photoInfo['height']))
                                        <div class="stat-item">
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">ความละเอียด:</span>
                                                <span class="text-dark">{{ $photoInfo['width'] }}×{{ $photoInfo['height'] }}</span>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if(isset($photoInfo['last_modified']))
                                        <div class="stat-item">
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">แก้ไขล่าสุด:</span>
                                                <span class="text-dark">{{ $photoInfo['last_modified'] }}</span>
                                            </div>
                                        </div>
                                    @endif
                                @else
                                    <div class="stat-item">
                                        <div class="text-center">
                                            <span class="text-danger">
                                                <i class="fas fa-exclamation-triangle me-1"></i>
                                                ไฟล์ไม่พบในระบบ
                                            </span>
                                        </div>
                                    </div>
                                @endif
                            @else
                                <div class="stat-item">
                                    <div class="text-center">
                                        <i class="fas fa-info-circle text-info me-2"></i>
                                        <small class="text-muted">Avatar สร้างจาก: <strong>{{ $initials }}</strong></small>
                                    </div>
                                </div>
                                
                                <div class="stat-item">
                                    <div class="text-center">
                                        <small class="text-muted">สี: <span class="badge" style="background-color: #{{ $backgroundColor }}; color: white;">#{{ $backgroundColor }}</span></small>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

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
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-building me-2" style="color: #B54544;"></i>ข้อมูลสาขา
                    <span class="badge text-white ms-2" style="background: linear-gradient(45deg, #B54544, #E6952A);">
                        Branch System
                    </span>
                </h5>
            </div>
            <div class="card-body">
                @if($employee->branch)
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
                    <div class="info-item-inline">
                        <span class="info-label">สถานะสาขา:</span>
                        <span class="info-value">
                            <span class="badge bg-{{ $employee->branch->is_active ? 'success' : 'secondary' }}">
                                {{ $employee->branch->is_active ? 'เปิดดำเนินการ' : 'ปิดชั่วคราว' }}
                            </span>
                        </span>
                    </div>
                @else
                    <div class="text-center py-3">
                        <div class="text-muted">
                            <i class="fas fa-building fa-2x mb-2" style="opacity: 0.3;"></i>
                            <p class="mb-1">ไม่ได้ระบุสาขา</p>
                            <small>พนักงานนี้ยังไม่ได้กำหนดสาขาที่สังกัด</small>
                        </div>
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

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-shield-alt text-danger me-2"></i>สิทธิ์พิเศษ
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
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
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-desktop text-success me-2"></i>ระบบคอมพิวเตอร์
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
                
                @php $canSeePasswords = $currentUser && in_array($currentUser->role, ['super_admin', 'it_admin']); @endphp
                
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
                                <i class="fas fa-lock me-1"></i>[เฉพาะ Admin]
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

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-envelope text-info me-2"></i>ระบบอีเมลและเข้าสู่ระบบ
                </h5>
            </div>
            <div class="card-body">
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
                    <span class="info-label">รหัสผ่านเข้าระบบ:</span>
                    <span class="info-value">
                        <span class="badge bg-secondary">
                            <i class="fas fa-shield-alt me-1"></i>Hash Protected
                        </span>
                    </span>
                </div>
            </div>
        </div>

        @if($employee->express_username)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bolt text-warning me-2"></i>โปรแกรม Express v2.0
                        <span class="badge bg-warning text-dark ms-2">Enhanced</span>
                    </h5>
                </div>
                <div class="card-body">
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
                                <span class="badge bg-success me-2">4 ตัวเลข</span>
                                <button class="btn btn-outline-secondary btn-xs" onclick="copyToClipboard('{{ $employee->express_password }}')">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </span>
                    </div>
                </div>
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-header" style="background: linear-gradient(45deg, #B54544, #E6952A); color: white;">
                <h5 class="mb-0" style="color: white; text-shadow: 1px 1px 2px rgba(0,0,0,0.3);">
                    <i class="fas fa-chart-line me-2"></i>สรุปข้อมูลระบบ
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary">ข้อมูลองค์กร</h6>
                        <ul class="list-unstyled">
                            <li><strong>สาขา:</strong> 
                                @if($employee->branch)
                                    <span class="badge text-white" style="background: linear-gradient(45deg, #B54544, #E6952A);">{{ $employee->branch->name }}</span>
                                @else
                                    <span class="text-muted">ไม่ระบุ</span>
                                @endif
                            </li>
                            <li><strong>แผนก:</strong> {{ $employee->department->name ?? 'ไม่ระบุ' }}</li>
                            <li><strong>ตำแหน่ง:</strong> {{ $employee->position }}</li>
                            <li><strong>สิทธิ์:</strong> 
                                <span class="badge bg-primary">{{ $employee->role_display ?? ucfirst($employee->role) }}</span>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 style="color: #B54544;">Photo & Express</h6>
                        <ul class="list-unstyled">
                            <li><strong>รูปภาพ:</strong> 
                                @if($employee->photo && $photoExists)
                                    <span class="badge bg-success">
                                        <i class="fas fa-camera me-1"></i>มีรูป
                                    </span>
                                @elseif($employee->photo && !$photoExists)
                                    <span class="badge bg-warning text-dark">
                                        <i class="fas fa-exclamation-triangle me-1"></i>ไฟล์หาย
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-user-circle me-1"></i>Avatar
                                    </span>
                                @endif
                            </li>
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
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="photoModal" tabindex="-1" aria-labelledby="photoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(45deg, #B54544, #E6952A); color: white;">
                <h5 class="modal-title" id="photoModalLabel">
                    <i class="fas fa-camera me-2"></i>รูปภาพพนักงาน
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-4">
                <div class="photo-modal-container">
                    <img src="" alt="" class="img-fluid rounded shadow" id="modalPhotoImage" style="max-height: 70vh; max-width: 100%;">
                </div>
                <div class="mt-4">
                    <h6 id="modalEmployeeName" class="text-primary mb-3"></h6>
                    <div id="modalPhotoInfo" class="text-muted mb-3"></div>
                    <div class="d-flex justify-content-center gap-2 flex-wrap">
                        <button class="btn btn-success" onclick="downloadPhotoFromModal()">
                            <i class="fas fa-download me-2"></i>ดาวน์โหลด
                        </button>
                        <button class="btn btn-info" onclick="copyPhotoUrlFromModal()">
                            <i class="fas fa-link me-2"></i>Copy URL
                        </button>
                        <button class="btn btn-secondary" onclick="viewFullSize()">
                            <i class="fas fa-external-link-alt me-2"></i>เปิดในแท็บใหม่
                        </button>
                        @if($canEdit)
                            <a href="{{ route('employees.edit', $employee) }}" class="btn btn-warning">
                                <i class="fas fa-edit me-2"></i>แก้ไขรูป
                            </a>
                        @endif
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

.employee-photo-container {
    position: relative;
    display: inline-block;
}

.employee-photo-large {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid rgba(255, 255, 255, 0.2);
    cursor: pointer;
    transition: all 0.3s ease;
}

.employee-photo-large:hover {
    transform: scale(1.05);
    box-shadow: 0 0 20px rgba(255, 255, 255, 0.5);
}

.employee-photo-large.photo-missing {
    border-color: #ffc107;
    box-shadow: 0 0 10px rgba(255, 193, 7, 0.3);
}

.photo-status-badge {
    position: absolute;
    bottom: -5px;
    right: -5px;
}

.employee-photo-display {
    max-width: 100%;
}

.photo-container {
    position: relative;
    display: inline-block;
    cursor: pointer;
}

.employee-photo-display-img {
    width: 200px;
    height: 200px;
    border-radius: 12px;
    object-fit: cover;
    border: 3px solid rgba(181, 69, 68, 0.2);
    transition: all 0.3s ease;
}

.employee-photo-display-img:hover {
    transform: scale(1.02);
    border-color: #B54544;
    box-shadow: 0 8px 25px rgba(181, 69, 68, 0.3);
}

.photo-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    opacity: 0;
    transition: opacity 0.3s ease;
    color: white;
    font-size: 1.5rem;
}

.photo-container:hover .photo-overlay {
    opacity: 1;
}

.photo-error-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 193, 7, 0.8);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    color: #000;
    font-size: 1.2rem;
}

.avatar-generated {
    border: 3px solid rgba(13, 110, 253, 0.2);
}

.avatar-generated:hover {
    border-color: #0d6efd;
    box-shadow: 0 8px 25px rgba(13, 110, 253, 0.3);
}

.photo-error {
    border-color: #ffc107 !important;
}

.photo-error:hover {
    border-color: #ffb700 !important;
    box-shadow: 0 8px 25px rgba(255, 193, 7, 0.4) !important;
}

.no-photo-container, .missing-photo-container {
    position: relative;
    display: inline-block;
}

.photo-stats {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1rem;
}

.stat-item {
    margin-bottom: 0.75rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #e9ecef;
}

.stat-item:last-child {
    margin-bottom: 0;
    border-bottom: none;
}

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

.modal-xl {
    max-width: 95vw;
}

.photo-modal-container {
    position: relative;
    display: inline-block;
}

#modalPhotoImage {
    cursor: pointer;
    transition: transform 0.3s ease;
    border: 2px solid rgba(181, 69, 68, 0.2);
}

#modalPhotoImage:hover {
    transform: scale(1.02);
    border-color: #B54544;
}

.btn-xs:hover {
    transform: scale(1.1);
    transition: transform 0.2s ease;
}

.copy-success {
    animation: copyPulse 0.3s ease;
}

@keyframes copyPulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

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
    
    .employee-photo-large {
        width: 60px;
        height: 60px;
    }
    
    .employee-photo-display-img {
        width: 150px;
        height: 150px;
    }
    
    .photo-stats {
        margin-top: 1rem;
    }
    
    .modal-xl {
        max-width: 98vw;
        margin: 0.5rem;
    }
    
    #modalPhotoImage {
        max-height: 60vh;
    }
}

.btn-xs:disabled {
    opacity: 0.6;
    transform: none;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
}

.badge-gradient {
    background: linear-gradient(45deg, #B54544, #E6952A);
    color: white;
}

.card.border-success { border-color: #198754 !important; }
.card.border-warning { border-color: #ffc107 !important; }
.card.border-info { border-color: #0dcaf0 !important; }
.card.border-danger { border-color: #dc3545 !important; }
.card.border-secondary { border-color: #6c757d !important; }

.notification-success {
    background: linear-gradient(45deg, #198754, #20c997);
    color: white;
}

.notification-error {
    background: linear-gradient(45deg, #dc3545, #fd7e14);
    color: white;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Employee Show Page Loaded - Complete Photo System');
    
    const employeeId = {{ $employee->id }};
    const hasPhoto = {{ $employee->photo ? 'true' : 'false' }};
    const photoExists = {{ $photoExists ? 'true' : 'false' }};
    const photoFileName = '{{ $employee->photo ?? '' }}';
    
    console.log('Photo System Info:', {
        employee_id: employeeId,
        has_photo_in_db: hasPhoto,
        photo_file_exists: photoExists,
        photo_filename: photoFileName
    });
    
    if (hasPhoto && photoExists) {
        testPhotoAccessibility();
    }
    
    if (typeof bootstrap !== 'undefined') {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    }
});

function testPhotoAccessibility() {
    const photoUrl = '{{ $photoExists && $photoUrl ? $photoUrl : "" }}';
    
    if (photoUrl) {
        const img = new Image();
        img.onload = function() {
            console.log('Photo loaded successfully:', photoUrl);
        };
        img.onerror = function() {
            console.error('Photo load failed:', photoUrl);
            showNotification('ไม่สามารถโหลดรูปภาพได้', 'warning');
        };
        img.src = photoUrl;
    }
}

function showPhotoModal(photoUrl, employeeName, fileName = '') {
    const modal = new bootstrap.Modal(document.getElementById('photoModal'));
    const modalImage = document.getElementById('modalPhotoImage');
    const modalName = document.getElementById('modalEmployeeName');
    const modalLabel = document.getElementById('photoModalLabel');
    const modalInfo = document.getElementById('modalPhotoInfo');
    
    modalImage.src = photoUrl;
    modalImage.alt = 'รูปภาพ ' + employeeName;
    modalName.textContent = employeeName;
    modalLabel.innerHTML = '<i class="fas fa-camera me-2"></i>รูปภาพ - ' + employeeName;
    
    if (fileName) {
        modalInfo.innerHTML = `
            <small class="text-muted">
                <i class="fas fa-file me-1"></i>ไฟล์: <code>${fileName}</code><br>
                <i class="fas fa-link me-1"></i>URL: <code>${photoUrl}</code>
            </small>
        `;
    } else {
        modalInfo.innerHTML = '<small class="text-muted">Avatar อัตโนมัติ</small>';
    }
    
    window.currentPhoto = {
        url: photoUrl,
        name: employeeName,
        fileName: fileName
    };
    
    modal.show();
    
    console.log('Photo modal opened:', {
        employee: employeeName,
        filename: fileName,
        photo_url: photoUrl
    });
}

function downloadPhoto(photoUrl, employeeName, fileName = 'photo.jpg') {
    const link = document.createElement('a');
    link.href = photoUrl;
    
    const sanitizedName = employeeName.replace(/[^a-zA-Z0-9ก-๙\s]/g, '').replace(/\s+/g, '_');
    const extension = fileName.includes('.') ? fileName.split('.').pop() : 'jpg';
    link.download = `${sanitizedName}_photo.${extension}`;
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    showNotification(`กำลังดาวน์โหลดรูปภาพของ ${employeeName}`, 'success');
    
    console.log('Photo download initiated:', {
        employee: employeeName,
        filename: link.download,
        url: photoUrl
    });
}

function downloadPhotoFromModal() {
    if (window.currentPhoto) {
        downloadPhoto(window.currentPhoto.url, window.currentPhoto.name, window.currentPhoto.fileName);
    }
}

function copyPhotoUrl(photoUrl) {
    copyToClipboard(photoUrl);
    showNotification('คัดลอก URL รูปภาพแล้ว', 'success');
}

function copyPhotoUrlFromModal() {
    if (window.currentPhoto) {
        copyPhotoUrl(window.currentPhoto.url);
    }
}

function viewFullSize() {
    if (window.currentPhoto) {
        window.open(window.currentPhoto.url, '_blank');
        console.log('Opened photo in new tab:', window.currentPhoto.url);
    }
}

function handlePhotoError(img, fallbackUrl, employeeName) {
    console.warn('Photo load error, switching to fallback:', img.src);
    img.src = fallbackUrl;
    img.classList.add('photo-error');
    img.title = 'ไฟล์รูปภาพหาย - แสดง Avatar แทน';
    
    img.onclick = function() {
        showPhotoModal(fallbackUrl, employeeName, '');
    };
    
    showNotification(`ไฟล์รูปภาพของ ${employeeName} ไม่พบ กำลังแสดง Avatar แทน`, 'warning');
}

function generateNewAvatar(employeeName) {
    const initials = '{{ strtoupper(substr($employee->first_name_en ?? "U", 0, 1) . substr($employee->last_name_en ?? "N", 0, 1)) }}';
    const colors = ['B54544', 'E6952A', '0d6efd', '198754', '6f42c1', 'dc3545', 'fd7e14'];
    const randomColor = colors[Math.floor(Math.random() * colors.length)];
    
    const newAvatarUrl = `https://ui-avatars.com/api/?name=${encodeURIComponent(initials)}&size=200&background=${randomColor}&color=ffffff&bold=true&format=png`;
    
    const mainPhoto = document.getElementById('mainPhotoDisplay');
    if (mainPhoto) {
        mainPhoto.src = newAvatarUrl;
    }
    
    const headerPhoto = document.getElementById('headerPhoto');
    if (headerPhoto) {
        headerPhoto.src = newAvatarUrl;
    }
    
    showNotification(`สร้าง Avatar ใหม่สำหรับ ${employeeName} แล้ว`, 'success');
    
    console.log('Generated new avatar:', {
        employee: employeeName,
        initials: initials,
        color: randomColor,
        url: newAvatarUrl
    });
}

function copyToClipboard(text) {
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(function() {
            showNotification('คัดลอกเรียบร้อยแล้ว: ' + maskText(text), 'success');
        }).catch(function(err) {
            console.error('Failed to copy: ', err);
            fallbackCopyTextToClipboard(text);
        });
    } else {
        fallbackCopyTextToClipboard(text);
    }
}

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
            showNotification('คัดลอกเรียบร้อยแล้ว: ' + maskText(text), 'success');
        } else {
            showNotification('ไม่สามารถคัดลอกได้', 'error');
        }
    } catch (err) {
        console.error('Fallback: Unable to copy', err);
        showNotification('ไม่สามารถคัดลอกได้', 'error');
    }
    
    document.body.removeChild(textArea);
}

function maskText(text) {
    if (text.length <= 2) {
        return text;
    } else if (text.includes('@') || text.includes('http')) {
        return text.length > 30 ? text.substring(0, 30) + '...' : text;
    } else if (text.length <= 4) {
        return text.charAt(0) + '***';
    } else {
        return text.substring(0, 2) + '***';
    }
}

function showNotification(message, type = 'success') {
    const alertClass = type === 'success' ? 'alert-success notification-success' : 
                      type === 'error' ? 'alert-danger notification-error' : 
                      type === 'warning' ? 'alert-warning' : 'alert-info';
    const iconClass = type === 'success' ? 'fa-check-circle' : 
                     type === 'error' ? 'fa-exclamation-triangle' :
                     type === 'warning' ? 'fa-exclamation-circle' : 'fa-info-circle';
    
    const alert = document.createElement('div');
    alert.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
    alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px; max-width: 400px; box-shadow: 0 8px 25px rgba(0,0,0,0.15); border-radius: 8px;';
    alert.innerHTML = `
        <i class="fas ${iconClass} me-2"></i>${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alert);
    
    setTimeout(() => {
        if (alert.parentNode) {
            alert.classList.remove('show');
            setTimeout(() => alert.remove(), 150);
        }
    }, 5000);
}

function debugPhotoSystem() {
    console.log('Photo System Debug Info:');
    console.log('Employee ID: {{ $employee->id }}');
    console.log('Photo in DB: {{ $employee->photo ?? "NULL" }}');
    console.log('Photo URL: {{ $photoUrl ?? "N/A" }}');
    console.log('File Exists: {{ $photoExists ? "TRUE" : "FALSE" }}');
    console.log('Avatar URL: {{ $avatarUrl }}');
    
    @if($photoExists && $photoUrl)
        const img = new Image();
        img.onload = function() {
            console.log('Photo loaded successfully');
            alert('รูปภาพโหลดได้แล้ว!\nURL: {{ $photoUrl }}');
        };
        img.onerror = function() {
            console.error('Photo load failed');
            alert('ไม่สามารถโหลดรูปภาพได้\nURL: {{ $photoUrl }}\nกรุณาตรวจสอบไฟล์และ symlink');
        };
        img.src = '{{ $photoUrl }}';
    @else
        alert('ไม่มีรูปภาพหรือไฟล์ไม่พบ\nกำลังใช้ Avatar แทน');
    @endif
}

document.querySelectorAll('.btn-xs').forEach(button => {
    button.addEventListener('click', function(e) {
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

function testPhotoSystem() {
    console.log('Testing Photo System...');
    
    const hasPhoto = {{ $employee->photo ? 'true' : 'false' }};
    const photoExists = {{ $photoExists ? 'true' : 'false' }};
    
    console.log('Photo in database:', hasPhoto);
    console.log('Photo file exists:', photoExists);
    
    if (hasPhoto && !photoExists) {
        console.warn('Photo file missing - showing fallback avatar');
    } else if (hasPhoto && photoExists) {
        console.log('Photo system working correctly');
    } else {
        console.log('No photo - using generated avatar');
    }
    
    const modal = document.getElementById('photoModal');
    if (modal) {
        console.log('Photo modal available');
    } else {
        console.error('Photo modal not found');
    }
    
    console.log('Photo system test complete');
}

setTimeout(() => {
    testPhotoSystem();
}, 1000);

console.log('Employee Show Page Script Loaded - Complete Photo System');
console.log('Available functions: showPhotoModal(), downloadPhoto(), copyPhotoUrl(), handlePhotoError(), generateNewAvatar(), testPhotoSystem()');
console.log('Features: Photo Display, Modal View, Download, URL Copy, Error Handling, Auto Fallback, Statistics Display');
console.log('Photo System: Complete with file validation, error recovery, and enhanced UI');
console.log('ITMS Theme: Perfect integration with red-orange gradient');
</script>
@endpush
