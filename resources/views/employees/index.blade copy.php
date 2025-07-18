@extends('layouts.app')

@section('title', 'จัดการข้อมูลพนักงาน')

@section('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
<link rel="stylesheet" href="{{ asset('css/employees.css') }}">
@endsection

@section('content')
<div class="container-fluid px-4">
    {{-- Header Section --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-primary-red fw-bold">
                <i class="fas fa-users me-2"></i>จัดการข้อมูลพนักงาน
            </h1>
            <p class="text-muted mb-0">
                จัดการข้อมูลพนักงานทั้งหมดในระบบ
            </p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-success" id="exportBtn">
                <i class="fas fa-file-excel me-1"></i>ส่งออก Excel
            </button>
            <a href="{{ route('employees.create') }}" class="btn btn-primary-red">
                <i class="fas fa-plus me-1"></i>เพิ่มพนักงานใหม่
            </a>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="stat-icon bg-primary-red">
                                <i class="fas fa-users text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="stat-number">{{ \App\Models\Employee::count() }}</div>
                            <div class="stat-label">พนักงานทั้งหมด</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="stat-icon bg-success">
                                <i class="fas fa-user-check text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="stat-number">{{ \App\Models\Employee::where('status', 'active')->count() }}</div>
                            <div class="stat-label">พนักงานใช้งาน</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="stat-icon bg-warning">
                                <i class="fas fa-building text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="stat-number">{{ count(\App\Models\Employee::getDepartments()) }}</div>
                            <div class="stat-label">แผนกทั้งหมด</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="stat-icon bg-info">
                                <i class="fas fa-calendar-plus text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="stat-number">{{ \App\Models\Employee::whereMonth('hire_date', now()->month)->count() }}</div>
                            <div class="stat-label">เข้าใหม่เดือนนี้</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Search & Filter Section --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-gradient-primary text-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-search me-2"></i>ค้นหาและกรองข้อมูล
            </h5>
        </div>
        <div class="card-body">
            <form id="filterForm" method="GET">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="search" class="form-label">ค้นหา</label>
                        //<input type="text" class="form-control" id="search" name="search" 
                        //       value="{{ $request->search }}" 
                        //       placeholder="รหัส, ชื่อ, อีเมล...">
                    <input type="text" class="form-control" id="search" name="search" 
       value="{{ old('search', request('search')) }}" 
       placeholder="ค้นหา ชื่อ, อีเมล, รหัสพนักงาน...">
		    </div>
                    <div class="col-md-3">
                        <label for="department" class="form-label">แผนก</label>
                        <select class="form-select" id="department" name="department">
                            <option value="">ทั้งหมด</option>
                            @foreach($departments as $key => $value)
                                //<option value="{{ $key }}" {{ $request->department == $key ? 'selected' : '' }}>
                                <option value="{{ $key }}" {{ request('department') == $key ? 'selected' : '' }}>
				    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">สถานะ</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">ทั้งหมด</option>
                            @foreach($statuses as $key => $value)
                                <option value="{{ $key }}" {{ $request->status == $key ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="role" class="form-label">บทบาท</label>
                        <select class="form-select" id="role" name="role">
                            <option value="">ทั้งหมด</option>
                            @foreach($roles as $key => $value)
                                <option value="{{ $key }}" {{ $request->role == $key ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary-red me-2">
                            <i class="fas fa-search me-1"></i>ค้นหา
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="clearFilter">
                            <i class="fas fa-times me-1"></i>ล้างตัวกรอง
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Data Table Section --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list me-2 text-primary-red"></i>รายการพนักงาน
                </h5>
                <div class="d-flex align-items-center gap-2">
                    <small class="text-muted">
                        แสดงผล {{ $employees->firstItem() ?? 0 }}-{{ $employees->lastItem() ?? 0 }} 
                        จาก {{ $employees->total() }} รายการ
                    </small>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="employeesTable">
                    <thead class="table-light">
                        <tr>
                            <th width="10%">รหัสพนักงาน</th>
                            <th width="20%">ชื่อ-นามสกุล</th>
                            <th width="15%">อีเมล</th>
                            <th width="12%">แผนก</th>
                            <th width="15%">ตำแหน่ง</th>
                            <th width="8%">สถานะ</th>
                            <th width="10%">บทบาท</th>
                            <th width="10%" class="text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $employee)
                        <tr>
                            <td>
                                <span class="fw-bold text-primary-red">{{ $employee->employee_id }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center me-2">
                                        <i class="fas fa-user text-muted"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $employee->full_name }}</div>
                                        <small class="text-muted">{{ $employee->username }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <a href="mailto:{{ $employee->email }}" class="text-decoration-none">
                                    {{ $employee->email }}
                                </a>
                            </td>
                            <td>{{ $employee->department_text }}</td>
                            <td>{{ $employee->position }}</td>
                            <td>
                                @if($employee->status == 'active')
                                    <span class="badge bg-success">{{ $employee->status_text }}</span>
                                @elseif($employee->status == 'inactive')
                                    <span class="badge bg-warning">{{ $employee->status_text }}</span>
                                @else
                                    <span class="badge bg-danger">{{ $employee->status_text }}</span>
                                @endif
                            </td>
                            <td>
                                @if($employee->role == 'super_admin')
                                    <span class="badge bg-danger">{{ $employee->role_text }}</span>
                                @elseif($employee->role == 'it_admin')
                                    <span class="badge bg-warning">{{ $employee->role_text }}</span>
                                @else
                                    <span class="badge bg-info">{{ $employee->role_text }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('employees.show', $employee) }}" 
                                       class="btn btn-sm btn-outline-info" title="ดูรายละเอียด">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('employees.edit', $employee) }}" 
                                       class="btn btn-sm btn-outline-warning" title="แก้ไข">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-sm btn-outline-danger delete-btn" 
                                            data-id="{{ $employee->id }}" 
                                            data-name="{{ $employee->full_name }}" 
                                            title="ลบ">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <div class="empty-state">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">ไม่พบข้อมูลพนักงาน</h5>
                                    <p class="text-muted">ลองปรับเปลี่ยนเงื่อนไขการค้นหา หรือ 
                                        <a href="{{ route('employees.create') }}" class="text-decoration-none">เพิ่มพนักงานใหม่</a>
                                    </p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($employees->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div>
                    <small class="text-muted">
                        แสดงผล {{ $employees->firstItem() }}-{{ $employees->lastItem() }} 
                        จาก {{ $employees->total() }} รายการ
                    </small>
                </div>
                <div>
                    {{ $employees->appends(request()->query())->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/employees.js') }}"></script>

<script>
$(document).ready(function() {
    // Clear filter functionality
    $('#clearFilter').click(function() {
        window.location.href = '{{ route("employees.index") }}';
    });

    // Export Excel functionality
    $('#exportBtn').click(function() {
        const params = new URLSearchParams();
        
        if ($('#search').val()) params.append('search', $('#search').val());
        if ($('#department').val()) params.append('department', $('#department').val());
        if ($('#status').val()) params.append('status', $('#status').val());
        if ($('#role').val()) params.append('role', $('#role').val());
        
        const url = '{{ route("employees.exportExcel") }}' + (params.toString() ? '?' + params.toString() : '');
        window.location.href = url;
    });

    // Delete functionality
    $(document).on('click', '.delete-btn', function() {
        const employeeId = $(this).data('id');
        const employeeName = $(this).data('name');

        Swal.fire({
            title: 'ยืนยันการลบ',
            text: `คุณต้องการลบพนักงาน "${employeeName}" หรือไม่?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'ลบ',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/employees/${employeeId}`,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'สำเร็จ!',
                                text: response.message,
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        Swal.fire({
                            title: 'เกิดข้อผิดพลาด!',
                            text: response?.message || 'ไม่สามารถลบข้อมูลได้',
                            icon: 'error'
                        });
                    }
                });
            }
        });
    });

    // Auto submit form on filter change
    $('#department, #status, #role').change(function() {
        $('#filterForm').submit();
    });

    // Search with delay
    let searchTimeout;
    $('#search').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            $('#filterForm').submit();
        }, 500);
    });
});
</script>
@endsection
