@extends('layouts.app')

@section('title', 'รายละเอียดสาขา: ' . $branch->name)

@push('styles')
<style>
    /* ITMS Theme Colors - Scoped to content area only */
    .content-wrapper {
        --itms-red: #B54544;
        --itms-orange: #E6952A;
        --itms-red-light: #C55654;
        --itms-orange-light: #F1A43A;
        --itms-dark: #2c3e50;
        --itms-light: #ecf0f1;
    }

    /* Enhanced Typography - Scoped to content only */
    .content-wrapper .branch-title {
        color: var(--itms-red) !important;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        font-weight: 700 !important;
        font-size: 1.8rem !important;
    }

    .content-wrapper .card-title {
        color: var(--itms-dark) !important;
        font-weight: 600 !important;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
    }

    /* Enhanced Card Styling - Scoped to content only */
    .content-wrapper .card {
        border: none !important;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
        border-radius: 12px !important;
        margin-bottom: 1.5rem;
    }

    .content-wrapper .card-header {
        background: linear-gradient(135deg, var(--itms-red), var(--itms-orange)) !important;
        color: white !important;
        border-bottom: none !important;
        border-radius: 12px 12px 0 0 !important;
        padding: 1rem 1.5rem;
    }

    .content-wrapper .card-header .card-title {
        color: white !important;
        margin: 0;
        font-weight: 600;
        text-shadow: 1px 1px 3px rgba(0,0,0,0.3);
    }

    .content-wrapper .card-header .card-title i {
        margin-right: 8px;
        color: rgba(255,255,255,0.9);
    }

    /* Enhanced Definition Lists - Scoped to content only */
    .content-wrapper .card-body dl.row dt {
        color: var(--itms-red) !important;
        font-weight: 600 !important;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        margin-bottom: 0.5rem;
    }

    .content-wrapper .card-body dl.row dd {
        color: var(--itms-dark) !important;
        font-weight: 500 !important;
        margin-bottom: 0.5rem;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
    }

    /* Enhanced Badges - Scoped to content only */
    .content-wrapper .badge {
        font-size: 0.8rem !important;
        padding: 0.4rem 0.8rem !important;
        border-radius: 20px !important;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.3) !important;
        font-weight: 500 !important;
    }

    .content-wrapper .badge-success {
        background: linear-gradient(135deg, #28a745, #20c997) !important;
        border: 1px solid rgba(255,255,255,0.2) !important;
    }

    .content-wrapper .badge-danger {
        background: linear-gradient(135deg, #dc3545, #e74c3c) !important;
        border: 1px solid rgba(255,255,255,0.2) !important;
    }

    .content-wrapper .badge-secondary {
        background: linear-gradient(135deg, var(--itms-dark), #34495e) !important;
        border: 1px solid rgba(255,255,255,0.2) !important;
    }

    .content-wrapper .badge-warning {
        background: linear-gradient(135deg, var(--itms-orange), var(--itms-orange-light)) !important;
        border: 1px solid rgba(255,255,255,0.2) !important;
    }

    .content-wrapper .badge-info {
        background: linear-gradient(135deg, #17a2b8, #20c997) !important;
        border: 1px solid rgba(255,255,255,0.2) !important;
    }

    /* Enhanced Buttons - Scoped to content only */
    .content-wrapper .btn {
        border-radius: 25px !important;
        padding: 0.5rem 1.2rem !important;
        font-weight: 500 !important;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.2) !important;
        transition: all 0.3s ease !important;
    }

    .content-wrapper .btn:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2) !important;
    }

    .content-wrapper .btn-warning {
        background: linear-gradient(135deg, var(--itms-orange), var(--itms-orange-light)) !important;
        border: none !important;
        color: white !important;
    }

    .content-wrapper .btn-secondary {
        background: linear-gradient(135deg, #6c757d, #5a6268) !important;
        border: none !important;
        color: white !important;
    }

    .content-wrapper .btn-success {
        background: linear-gradient(135deg, #28a745, #20c997) !important;
        border: none !important;
        color: white !important;
    }

    /* Card Outline Enhancements - Scoped to content only */
    .content-wrapper .card-outline-card-info {
        border-top: 4px solid #17a2b8 !important;
    }

    .content-wrapper .card-outline-card-primary {
        border-top: 4px solid var(--itms-red) !important;
    }

    .content-wrapper .card-outline-card-warning {
        border-top: 4px solid var(--itms-orange) !important;
    }

    .content-wrapper .card-outline-card-success {
        border-top: 4px solid #28a745 !important;
    }

    .content-wrapper .card-outline-card-dark {
        border-top: 4px solid var(--itms-dark) !important;
    }

    /* Enhanced Info Boxes - Scoped to content only */
    .content-wrapper .info-box {
        border-radius: 12px !important;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1) !important;
        border: none !important;
        margin-bottom: 1rem;
    }

    .content-wrapper .info-box-icon {
        border-radius: 12px 0 0 12px !important;
    }

    .content-wrapper .info-box-text {
        color: var(--itms-dark) !important;
        font-weight: 600 !important;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
    }

    .content-wrapper .info-box-number {
        color: var(--itms-red) !important;
        font-weight: 700 !important;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
    }

    /* Enhanced Table - Scoped to content only */
    .content-wrapper .table {
        border-radius: 8px !important;
        overflow: hidden !important;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1) !important;
    }

    .content-wrapper .table thead th {
        background: linear-gradient(135deg, var(--itms-red), var(--itms-orange)) !important;
        color: white !important;
        border: none !important;
        font-weight: 600 !important;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        padding: 1rem 0.75rem !important;
    }

    .content-wrapper .table tbody td {
        color: var(--itms-dark) !important;
        font-weight: 500 !important;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        border-color: rgba(0,0,0,0.1) !important;
        padding: 0.8rem 0.75rem !important;
    }

    .content-wrapper .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(181, 69, 68, 0.05) !important;
    }

    /* Enhanced Links - Scoped to content only */
    .content-wrapper a {
        color: var(--itms-red) !important;
        text-decoration: none !important;
        font-weight: 500 !important;
        transition: all 0.3s ease !important;
    }

    .content-wrapper a:hover {
        color: var(--itms-orange) !important;
        text-shadow: 1px 1px 3px rgba(0,0,0,0.2);
    }

    /* Enhanced Icons - Scoped to content only */
    .content-wrapper .fas, 
    .content-wrapper .far {
        text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
    }

    /* Empty State Styling - Scoped to content only */
    .content-wrapper .text-muted {
        color: #6c757d !important;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
    }

    .content-wrapper .text-center.text-muted h5 {
        color: var(--itms-dark) !important;
        font-weight: 600 !important;
    }

    /* Alert Enhancements - Scoped to content only */
    .content-wrapper .alert {
        border: none !important;
        border-radius: 12px !important;
        font-weight: 500 !important;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
    }

    .content-wrapper .alert-success {
        background: linear-gradient(135deg, rgba(40, 167, 69, 0.1), rgba(32, 201, 151, 0.1)) !important;
        color: #155724 !important;
        border-left: 4px solid #28a745 !important;
    }

    /* Responsive Text Sizing - Scoped to content only */
    @media (max-width: 768px) {
        .content-wrapper .branch-title {
            font-size: 1.4rem !important;
        }
        
        .content-wrapper .card-title {
            font-size: 1.1rem !important;
        }
        
        .content-wrapper .info-box-text {
            font-size: 0.85rem !important;
        }
        
        .content-wrapper .info-box-number {
            font-size: 1.2rem !important;
        }
    }

    /* Card Body Padding - Scoped to content only */
    .content-wrapper .card-body {
        padding: 1.5rem !important;
    }

    /* Enhanced Status Icons - Scoped to content only */
    .content-wrapper .fas.fa-check, 
    .content-wrapper .fas.fa-times {
        margin-right: 5px;
    }
</style>
@endpush

@section('content')
<div class="content-wrapper">
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Branch Header -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title branch-title">
                        <i class="fas fa-building mr-2"></i>
                        รายละเอียดสาขา: {{ $branch->name }}
                        <span class="badge badge-{{ $branch->is_active ? 'success' : 'danger' }} ml-2">
                            <i class="fas fa-{{ $branch->is_active ? 'check' : 'times' }}"></i>
                            {{ $branch->is_active ? 'เปิดใช้งาน' : 'ปิดใช้งาน' }}
                        </span>
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('branches.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> กลับ
                        </a>
                        <a href="{{ route('branches.edit', $branch) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> แก้ไข
                        </a>
                        <form action="{{ route('branches.toggle-status', $branch) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    class="btn btn-{{ $branch->is_active ? 'secondary' : 'success' }} btn-sm"
                                    onclick="return confirm('คุณต้องการเปลี่ยนสถานะหรือไม่?')">
                                <i class="fas fa-{{ $branch->is_active ? 'pause' : 'play' }}"></i>
                                {{ $branch->is_active ? 'ปิดใช้งาน' : 'เปิดใช้งาน' }}
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-md-6">
                            <div class="card card-outline-card-info">
                                <div class="card-header">
                                    <h5 class="card-title"><i class="fas fa-info-circle"></i> ข้อมูลพื้นฐาน</h5>
                                </div>
                                <div class="card-body">
                                    <dl class="row">
                                        <dt class="col-sm-4">รหัสสาขา:</dt>
                                        <dd class="col-sm-8">
                                            <span class="badge badge-secondary">{{ $branch->code }}</span>
                                        </dd>

                                        <dt class="col-sm-4">ชื่อสาขา:</dt>
                                        <dd class="col-sm-8">{{ $branch->name }}</dd>

                                        @if($branch->description)
                                        <dt class="col-sm-4">คำอธิบาย:</dt>
                                        <dd class="col-sm-8">{{ $branch->description }}</dd>
                                        @endif

                                        <dt class="col-sm-4">สถานะ:</dt>
                                        <dd class="col-sm-8">
                                            <span class="badge badge-{{ $branch->is_active ? 'success' : 'danger' }}">
                                                <i class="fas fa-{{ $branch->is_active ? 'check' : 'times' }}"></i>
                                                {{ $branch->is_active ? 'เปิดใช้งาน' : 'ปิดใช้งาน' }}
                                            </span>
                                        </dd>

                                        <dt class="col-sm-4">วันที่สร้าง:</dt>
                                        <dd class="col-sm-8">{{ $branch->created_at->format('d/m/Y H:i:s') }}</dd>

                                        <dt class="col-sm-4">อัพเดตล่าสุด:</dt>
                                        <dd class="col-sm-8">{{ $branch->updated_at->format('d/m/Y H:i:s') }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="col-md-6">
                            <div class="card card-outline-card-primary">
                                <div class="card-header">
                                    <h5 class="card-title"><i class="fas fa-address-book"></i> ข้อมูลติดต่อ</h5>
                                </div>
                                <div class="card-body">
                                    <dl class="row">
                                        @if($branch->address)
                                        <dt class="col-sm-4">ที่อยู่:</dt>
                                        <dd class="col-sm-8">
                                            <i class="fas fa-map-marker-alt text-danger"></i>
                                            {{ $branch->address }}
                                        </dd>
                                        @endif

                                        @if($branch->phone)
                                        <dt class="col-sm-4">เบอร์โทร:</dt>
                                        <dd class="col-sm-8">
                                            <i class="fas fa-phone text-success"></i>
                                            <a href="tel:{{ $branch->phone }}">{{ $branch->phone }}</a>
                                        </dd>
                                        @endif

                                        @if($branch->email)
                                        <dt class="col-sm-4">อีเมล:</dt>
                                        <dd class="col-sm-8">
                                            <i class="fas fa-envelope text-info"></i>
                                            <a href="mailto:{{ $branch->email }}">{{ $branch->email }}</a>
                                        </dd>
                                        @endif

                                        @if(!$branch->address && !$branch->phone && !$branch->email)
                                        <dt class="col-12 text-center text-muted">
                                            <i class="fas fa-info-circle"></i> ไม่มีข้อมูลติดต่อ
                                        </dt>
                                        @endif
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Manager Information -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card card-outline-card-warning">
                                <div class="card-header">
                                    <h5 class="card-title"><i class="fas fa-user-tie"></i> ผู้จัดการสาขา</h5>
                                </div>
                                <div class="card-body">
                                    @if($branch->manager)
                                        <div class="row">
                                            <div class="col-md-6">
                                                <dl class="row">
                                                    <dt class="col-sm-4">ชื่อ:</dt>
                                                    <dd class="col-sm-8">{{ $branch->manager->full_name_th }}</dd>

                                                    <dt class="col-sm-4">รหัสพนักงาน:</dt>
                                                    <dd class="col-sm-8">
                                                        <span class="badge badge-info">{{ $branch->manager->employee_id }}</span>
                                                    </dd>

                                                    <dt class="col-sm-4">ตำแหน่ง:</dt>
                                                    <dd class="col-sm-8">
                                                        <span class="badge badge-warning">{{ ucfirst($branch->manager->role) }}</span>
                                                    </dd>
                                                </dl>
                                            </div>
                                            <div class="col-md-6">
                                                <dl class="row">
                                                    @if($branch->manager->email)
                                                    <dt class="col-sm-4">อีเมล:</dt>
                                                    <dd class="col-sm-8">
                                                        <a href="mailto:{{ $branch->manager->email }}">{{ $branch->manager->email }}</a>
                                                    </dd>
                                                    @endif

                                                    @if($branch->manager->phone)
                                                    <dt class="col-sm-4">เบอร์โทร:</dt>
                                                    <dd class="col-sm-8">
                                                        <a href="tel:{{ $branch->manager->phone }}">{{ $branch->manager->phone }}</a>
                                                    </dd>
                                                    @endif

                                                    <dt class="col-sm-4">สถานะ:</dt>
                                                    <dd class="col-sm-8">
                                                        <span class="badge badge-{{ $branch->manager->is_active ? 'success' : 'danger' }}">
                                                            <i class="fas fa-{{ $branch->manager->is_active ? 'check' : 'times' }}"></i>
                                                            {{ $branch->manager->is_active ? 'ปกติ' : 'ไม่ปกติ' }}
                                                        </span>
                                                    </dd>
                                                </dl>
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-center text-muted">
                                            <i class="fas fa-user-times fa-3x mb-3"></i>
                                            <h5>ไม่มีผู้จัดการสาขา</h5>
                                            <p>กรุณาแก้ไขข้อมูลเพื่อเพิ่มผู้จัดการสาขา</p>
                                            <a href="{{ route('branches.edit', $branch) }}" class="btn btn-warning">
                                                <i class="fas fa-edit"></i> เพิ่มผู้จัดการ
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Employees List -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card card-outline-card-success">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="fas fa-users"></i> 
                                        พนักงานในสาขา ({{ $branch->employees->count() }} คน)
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @if($branch->employees->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-sm table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>รหัสพนักงาน</th>
                                                        <th>ชื่อ-นามสกุล</th>
                                                        <th>ตำแหน่ง</th>
                                                        <th>อีเมล</th>
                                                        <th>สถานะ</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($branch->employees as $employee)
                                                    <tr>
                                                        <td>
                                                            <span class="badge badge-secondary">{{ $employee->employee_id }}</span>
                                                        </td>
                                                        <td>
                                                            {{ $employee->full_name_th }}
                                                            @if($employee->id == $branch->manager_id)
                                                                <span class="badge badge-warning badge-sm ml-1">Manager</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <span class="badge badge-info">{{ ucfirst($employee->role) }}</span>
                                                        </td>
                                                        <td>{{ $employee->email ?: '-' }}</td>
                                                        <td>
                                                            <span class="badge badge-{{ $employee->is_active ? 'success' : 'danger' }}">
                                                                <i class="fas fa-{{ $employee->is_active ? 'check' : 'times' }}"></i>
                                                                {{ $employee->is_active ? 'ปกติ' : 'ไม่ปกติ' }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center text-muted">
                                            <i class="fas fa-users-slash fa-3x mb-3"></i>
                                            <h5>ไม่มีพนักงานในสาขานี้</h5>
                                            <p>สาขานี้ยังไม่มีพนักงาน</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics -->
                    @if($branch->employees->count() > 0)
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card card-outline-card-dark">
                                <div class="card-header">
                                    <h5 class="card-title"><i class="fas fa-chart-pie"></i> สถิติสาขา</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-info">
                                                    <i class="fas fa-users"></i>
                                                </span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">พนักงานทั้งหมด</span>
                                                    <span class="info-box-number">{{ $branch->employees->count() }}</span>
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
                                                    <span class="info-box-number">{{ $branch->employees->where('role', 'employee')->count() }}</span>
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
                                                    <span class="info-box-number">{{ $branch->employees->where('role', 'admin')->count() }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-danger">
                                                    <i class="fas fa-user-times"></i>
                                                </span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">ไม่ปกติ</span>
                                                    <span class="info-box-number">{{ $branch->employees->where('is_active', false)->count() }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Auto hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);

    // Add loading animation for buttons
    $('.btn').on('click', function() {
        const $btn = $(this);
        const originalText = $btn.html();
        
        if ($btn.attr('type') === 'submit') {
            $btn.html('<i class="fas fa-spinner fa-spin"></i> กำลังประมวลผล...');
            setTimeout(function() {
                $btn.html(originalText);
            }, 2000);
        }
    });

    // Enhanced card animations
    $('.card').hover(
        function() {
            $(this).css('transform', 'translateY(-2px)');
        },
        function() {
            $(this).css('transform', 'translateY(0)');
        }
    );

    // Add smooth scrolling for navigation
    $('a[href^="#"]').on('click', function(event) {
        var target = $($(this).attr('href'));
        if (target.length) {
            event.preventDefault();
            $('html, body').animate({
                scrollTop: target.offset().top - 20
            }, 300);
        }
    });
});
</script>
@endpush
@endsection
