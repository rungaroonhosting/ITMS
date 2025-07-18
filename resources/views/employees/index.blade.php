@extends('layouts.app')

@section('title', 'จัดการพนักงาน')

@section('breadcrumb')
    <li class="breadcrumb-item active">จัดการพนักงาน</li>
@endsection

@section('content')

@php
    // Define user role at the top
    $userRole = auth()->user()->role ?? 'employee';
    
    // Prepare employee data safely
    $employeeCollection = isset($employees) ? $employees : collect();
    $departmentCollection = isset($departments) ? $departments : collect();
@endphp

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-primary fw-bold">
            <i class="fas fa-users me-2"></i>จัดการพนักงาน
        </h1>
        <p class="text-muted mb-0">รายการพนักงานทั้งหมดในระบบ</p>
    </div>
    <div class="d-flex gap-2">
        <div class="dropdown">
            <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="fas fa-download me-1"></i>ส่งออกข้อมูล
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#" onclick="exportToExcel()">
                    <i class="fas fa-file-excel me-2 text-success"></i>Excel
                </a></li>
                <li><a class="dropdown-item" href="#" onclick="exportToPDF()">
                    <i class="fas fa-file-pdf me-2 text-danger"></i>PDF
                </a></li>
                <li><a class="dropdown-item" href="#" onclick="exportToCSV()">
                    <i class="fas fa-file-csv me-2 text-info"></i>CSV
                </a></li>
            </ul>
        </div>
        <a href="{{ route('employees.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>เพิ่มพนักงานใหม่
        </a>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card stat-card border-start border-primary border-4 h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            พนักงานทั้งหมด
                        </div>
                        <div class="h4 mb-0 font-weight-bold text-gray-800" id="totalEmployees">
                            {{ $employeeCollection->count() }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-primary opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card stat-card border-start border-success border-4 h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            พนักงานที่ใช้งาน
                        </div>
                        <div class="h4 mb-0 font-weight-bold text-gray-800" id="activeEmployees">
                            {{ $employeeCollection->where('status', 'active')->count() }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-check fa-2x text-success opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card stat-card border-start border-warning border-4 h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            พนักงานไม่ใช้งาน
                        </div>
                        <div class="h4 mb-0 font-weight-bold text-gray-800" id="inactiveEmployees">
                            {{ $employeeCollection->where('status', 'inactive')->count() }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-times fa-2x text-warning opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card stat-card border-start border-info border-4 h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            เพิ่มเข้าวันนี้
                        </div>
                        <div class="h4 mb-0 font-weight-bold text-gray-800" id="todayEmployees">
                            @php
                                $todayEmployees = $employeeCollection->filter(function($employee) {
                                    return isset($employee->created_at) && $employee->created_at->isToday();
                                })->count();
                            @endphp
                            {{ $todayEmployees }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-plus fa-2x text-info opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters and Search -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row align-items-end">
            <div class="col-lg-3 col-md-6 mb-3">
                <label for="searchInput" class="form-label">ค้นหาพนักงาน</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" class="form-control" id="searchInput" placeholder="ค้นหาชื่อ, รหัส, อีเมล...">
                </div>
            </div>
            
            <div class="col-lg-2 col-md-6 mb-3">
                <label for="departmentFilter" class="form-label">แผนก</label>
                <select class="form-select" id="departmentFilter">
                    <option value="">ทุกแผนก</option>
                    @foreach($departmentCollection as $department)
                        <option value="{{ $department->name ?? $department }}">{{ $department->name ?? $department }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-lg-2 col-md-6 mb-3">
                <label for="roleFilter" class="form-label">สิทธิ์</label>
                <select class="form-select" id="roleFilter">
                    <option value="">ทุกสิทธิ์</option>
                    <option value="super_admin">Super Admin</option>
                    <option value="it_admin">IT Admin</option>
                    <option value="hr">HR</option>
                    <option value="manager">Manager</option>
                    <option value="express">Express</option>
                    <option value="employee">Employee</option>
                </select>
            </div>
            
            <div class="col-lg-2 col-md-6 mb-3">
                <label for="statusFilter" class="form-label">สถานะ</label>
                <select class="form-select" id="statusFilter">
                    <option value="">ทุกสถานะ</option>
                    <option value="active">ใช้งาน</option>
                    <option value="inactive">ไม่ใช้งาน</option>
                </select>
            </div>
            
            <div class="col-lg-3 col-md-12 mb-3">
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary" onclick="clearFilters()">
                        <i class="fas fa-times me-1"></i>ล้างตัวกรอง
                    </button>
                    <button type="button" class="btn btn-outline-primary" onclick="refreshTable()">
                        <i class="fas fa-sync me-1"></i>รีเฟรช
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Employee Table -->
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fas fa-list me-2"></i>รายการพนักงาน
            </h5>
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted small">
                    แสดง <span id="showingCount">{{ $employeeCollection->count() }}</span> รายการ
                </span>
                <div class="d-flex align-items-center gap-2">
                    <label class="form-label mb-0 small">แสดง:</label>
                    <select class="form-select form-select-sm" id="entriesPerPage" style="width: auto;">
                        <option value="10">10</option>
                        <option value="25" selected>25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="employeeTable">
                <thead class="table-light">
                    <tr>
                        <th scope="col" class="text-center" style="width: 60px;">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="selectAll">
                            </div>
                        </th>
                        <th scope="col" class="sortable" data-sort="employee_code">
                            รหัสพนักงาน
                            <i class="fas fa-sort ms-1 text-muted"></i>
                        </th>
                        <th scope="col" class="sortable" data-sort="name">
                            ชื่อ-นามสกุล
                            <i class="fas fa-sort ms-1 text-muted"></i>
                        </th>
                        <th scope="col" class="sortable" data-sort="email">
                            อีเมล
                            <i class="fas fa-sort ms-1 text-muted"></i>
                        </th>
                        <th scope="col" class="sortable" data-sort="department">
                            แผนก
                            <i class="fas fa-sort ms-1 text-muted"></i>
                        </th>
                        <th scope="col" class="sortable" data-sort="position">
                            ตำแหน่ง
                            <i class="fas fa-sort ms-1 text-muted"></i>
                        </th>
                        <th scope="col" class="sortable" data-sort="role">
                            สิทธิ์
                            <i class="fas fa-sort ms-1 text-muted"></i>
                        </th>
                        <th scope="col" class="sortable" data-sort="status">
                            สถานะ
                            <i class="fas fa-sort ms-1 text-muted"></i>
                        </th>
                        <th scope="col" class="text-center" style="width: 150px;">การจัดการ</th>
                    </tr>
                </thead>
                <tbody id="employeeTableBody">
                    @if($employeeCollection->count() > 0)
                        @foreach($employeeCollection as $employee)
                            <tr class="employee-row" data-employee-id="{{ $employee->id ?? '' }}">
                                <td class="text-center">
                                    <div class="form-check">
                                        <input class="form-check-input row-checkbox" type="checkbox" value="{{ $employee->id ?? '' }}">
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                            <i class="fas fa-user text-white"></i>
                                        </div>
                                        <span class="fw-medium">{{ $employee->employee_code ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div class="fw-medium">
                                            {{ ($employee->first_name_th ?? '') }} {{ ($employee->last_name_th ?? '') }}
                                        </div>
                                        <div class="text-muted small">
                                            {{ ($employee->first_name_en ?? '') }} {{ ($employee->last_name_en ?? '') }}
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div class="fw-medium">{{ $employee->email ?? 'N/A' }}</div>
                                        @if(isset($employee->phone) && $employee->phone)
                                            <div class="text-muted small">
                                                <i class="fas fa-phone me-1"></i>{{ $employee->phone }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        @if(isset($employee->department))
                                            {{ $employee->department->name ?? 'N/A' }}
                                        @else
                                            N/A
                                        @endif
                                    </span>
                                </td>
                                <td>{{ $employee->position ?? 'N/A' }}</td>
                                <td>
                                    @php
                                        $roleClasses = [
                                            'super_admin' => 'bg-warning text-dark',
                                            'it_admin' => 'bg-danger text-white',
                                            'hr' => 'bg-info text-white',
                                            'manager' => 'bg-success text-white',
                                            'express' => 'bg-primary text-white',
                                            'employee' => 'bg-secondary text-white',
                                        ];
                                        
                                        $roleNames = [
                                            'super_admin' => 'Super Admin',
                                            'it_admin' => 'IT Admin',
                                            'hr' => 'HR',
                                            'manager' => 'Manager',
                                            'express' => 'Express',
                                            'employee' => 'Employee',
                                        ];
                                        
                                        $employeeRole = $employee->role ?? 'employee';
                                        $roleClass = $roleClasses[$employeeRole] ?? 'bg-secondary text-white';
                                        $roleName = $roleNames[$employeeRole] ?? 'Employee';
                                    @endphp
                                    <span class="badge {{ $roleClass }} role-badge">
                                        {{ $roleName }}
                                    </span>
                                </td>
                                <td>
                                    @if(($employee->status ?? 'active') === 'active')
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i>ใช้งาน
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="fas fa-times-circle me-1"></i>ไม่ใช้งาน
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                onclick="viewEmployee({{ $employee->id ?? 0 }})" 
                                                title="ดูรายละเอียด">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        
                                        @if($userRole === 'super_admin' || $userRole === 'it_admin')
                                            <button type="button" class="btn btn-sm btn-outline-warning" 
                                                    onclick="editEmployee({{ $employee->id ?? 0 }})" 
                                                    title="แก้ไข">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    onclick="deleteEmployee({{ $employee->id ?? 0 }})" 
                                                    title="ลบ">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-users fa-3x mb-3 opacity-25"></i>
                                    <p class="mb-0">ไม่พบข้อมูลพนักงาน</p>
                                    <a href="{{ route('employees.create') }}" class="btn btn-primary mt-3">
                                        <i class="fas fa-plus me-2"></i>เพิ่มพนักงานใหม่
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Pagination -->
    <div class="card-footer">
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted small">
                @php
                    $entriesPerPage = 25;
                    $endEntry = min($entriesPerPage, $employeeCollection->count());
                @endphp
                แสดง <span id="startEntry">1</span> - <span id="endEntry">{{ $endEntry }}</span> 
                จาก <span id="totalEntries">{{ $employeeCollection->count() }}</span> รายการ
            </div>
            <nav aria-label="Employee pagination">
                <ul class="pagination pagination-sm mb-0" id="pagination">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- Bulk Actions (Hidden by default) -->
<div class="card mt-4" id="bulkActionsCard" style="display: none;">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <span class="fw-medium">เลือกแล้ว <span id="selectedCount">0</span> รายการ</span>
            </div>
            <div class="d-flex gap-2">
                @if($userRole === 'super_admin' || $userRole === 'it_admin')
                    <button type="button" class="btn btn-sm btn-outline-success" onclick="bulkActivate()">
                        <i class="fas fa-check me-1"></i>เปิดใช้งาน
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-warning" onclick="bulkDeactivate()">
                        <i class="fas fa-times me-1"></i>ปิดใช้งาน
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="bulkDelete()">
                        <i class="fas fa-trash me-1"></i>ลบทั้งหมด
                    </button>
                @endif
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="bulkExport()">
                    <i class="fas fa-download me-1"></i>ส่งออก
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Employee Detail Modal -->
<div class="modal fade" id="employeeModal" tabindex="-1" aria-labelledby="employeeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="employeeModalLabel">
                    <i class="fas fa-user me-2"></i>รายละเอียดพนักงาน
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="employeeModalBody">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">กำลังโหลด...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                <button type="button" class="btn btn-primary" onclick="printEmployee()">
                    <i class="fas fa-print me-1"></i>พิมพ์
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>ยืนยันการลบ
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">คุณแน่ใจหรือไม่ที่จะลบพนักงานนี้? การกระทำนี้ไม่สามารถย้อนกลับได้</p>
                <div class="mt-3 p-3 bg-light rounded">
                    <strong id="deleteEmployeeName"></strong>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="fas fa-trash me-1"></i>ลบ
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .stat-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
    }
    
    .sortable {
        cursor: pointer;
        user-select: none;
    }
    
    .sortable:hover {
        background-color: rgba(0, 0, 0, 0.05);
    }
    
    .sortable.sorted-asc .fa-sort:before {
        content: "\f0de";
        color: var(--primary-red);
    }
    
    .sortable.sorted-desc .fa-sort:before {
        content: "\f0dd";
        color: var(--primary-red);
    }
    
    .employee-row {
        transition: all 0.2s ease;
    }
    
    .employee-row:hover {
        background-color: rgba(181, 69, 68, 0.05);
    }
    
    .role-badge {
        font-size: 0.75rem;
        padding: 0.375rem 0.75rem;
        border-radius: 6px;
        font-weight: 600;
        letter-spacing: 0.5px;
    }
    
    .btn-group .btn {
        border-radius: 6px;
        margin-right: 2px;
    }
    
    .btn-group .btn:last-child {
        margin-right: 0;
    }
    
    .table th {
        border-bottom: 2px solid var(--primary-red);
        font-weight: 600;
        color: var(--text-primary);
        vertical-align: middle;
    }
    
    .table td {
        vertical-align: middle;
        padding: 1rem 0.75rem;
    }
    
    .search-highlight {
        background-color: #fff3cd;
        padding: 0.1rem 0.2rem;
        border-radius: 3px;
    }
    
    .fade-in {
        animation: fadeIn 0.3s ease-in;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .loading-row {
        opacity: 0.6;
        pointer-events: none;
    }
    
    .pagination .page-link {
        color: var(--primary-red);
        border-color: var(--primary-red);
    }
    
    .pagination .page-item.active .page-link {
        background-color: var(--primary-red);
        border-color: var(--primary-red);
    }
    
    .pagination .page-link:hover {
        background-color: var(--light-red);
        border-color: var(--primary-red);
        color: white;
    }
    
    .form-check-input:checked {
        background-color: var(--primary-red);
        border-color: var(--primary-red);
    }
    
    .btn-outline-primary:hover {
        background-color: var(--primary-red);
        border-color: var(--primary-red);
    }
    
    .text-primary {
        color: var(--primary-red) !important;
    }
    
    .border-primary {
        border-color: var(--primary-red) !important;
    }
    
    .bg-primary {
        background-color: var(--primary-red) !important;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .card-body {
            padding: 1rem;
        }
        
        .btn-group .btn {
            padding: 0.25rem 0.5rem;
        }
        
        .table td {
            padding: 0.75rem 0.5rem;
        }
        
        .stat-card .h4 {
            font-size: 1.2rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Employee Index Page Loaded');
    
    // Global variables
    let currentPage = 1;
    let entriesPerPage = 25;
    let sortColumn = null;
    let sortDirection = 'asc';
    let allEmployees = [];
    let filteredEmployees = [];
    let selectedEmployees = [];
    
    // Initialize
    init();
    
    function init() {
        loadEmployeeData();
        setupEventListeners();
        setupTableSorting();
        updateStats();
    }
    
    // Load employee data
    function loadEmployeeData() {
        allEmployees = Array.from(document.querySelectorAll('.employee-row')).map(row => ({
            id: row.dataset.employeeId,
            element: row,
            data: extractRowData(row)
        }));
        filteredEmployees = [...allEmployees];
        updateTable();
    }
    
    // Extract data from table row
    function extractRowData(row) {
        const cells = row.querySelectorAll('td');
        return {
            employee_code: cells[1].textContent.trim(),
            name: cells[2].textContent.trim(),
            email: cells[3].textContent.trim(),
            department: cells[4].textContent.trim(),
            position: cells[5].textContent.trim(),
            role: cells[6].textContent.trim(),
            status: cells[7].textContent.trim()
        };
    }
    
    // Event listeners
    function setupEventListeners() {
        // Search input
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', debounce(handleSearch, 300));
        }
        
        // Filters
        const departmentFilter = document.getElementById('departmentFilter');
        if (departmentFilter) {
            departmentFilter.addEventListener('change', handleFilter);
        }
        
        const roleFilter = document.getElementById('roleFilter');
        if (roleFilter) {
            roleFilter.addEventListener('change', handleFilter);
        }
        
        const statusFilter = document.getElementById('statusFilter');
        if (statusFilter) {
            statusFilter.addEventListener('change', handleFilter);
        }
        
        // Entries per page
        const entriesPerPageSelect = document.getElementById('entriesPerPage');
        if (entriesPerPageSelect) {
            entriesPerPageSelect.addEventListener('change', handleEntriesChange);
        }
        
        // Select all checkbox
        const selectAllCheckbox = document.getElementById('selectAll');
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', handleSelectAll);
        }
        
        // Row checkboxes
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('row-checkbox')) {
                handleRowSelect(e.target);
            }
        });
        
        // Stat cards click
        document.querySelectorAll('.stat-card').forEach(card => {
            card.addEventListener('click', function() {
                this.style.transform = 'scale(0.98)';
                setTimeout(() => {
                    this.style.transform = '';
                }, 150);
            });
        });
    }
    
    // Table sorting
    function setupTableSorting() {
        document.querySelectorAll('.sortable').forEach(header => {
            header.addEventListener('click', function() {
                const column = this.dataset.sort;
                handleSort(column, this);
            });
        });
    }
    
    // Handle search
    function handleSearch(e) {
        const searchTerm = e.target.value.toLowerCase();
        
        if (searchTerm === '') {
            filteredEmployees = [...allEmployees];
        } else {
            filteredEmployees = allEmployees.filter(employee => {
                const data = employee.data;
                return Object.values(data).some(value => 
                    value.toLowerCase().includes(searchTerm)
                );
            });
        }
        
        currentPage = 1;
        updateTable();
        highlightSearchResults(searchTerm);
    }
    
    // Handle filters
    function handleFilter() {
        const department = document.getElementById('departmentFilter').value;
        const role = document.getElementById('roleFilter').value;
        const status = document.getElementById('statusFilter').value;
        
        filteredEmployees = allEmployees.filter(employee => {
            const data = employee.data;
            return (
                (department === '' || data.department.includes(department)) &&
                (role === '' || data.role.includes(role)) &&
                (status === '' || data.status.includes(status))
            );
        });
        
        currentPage = 1;
        updateTable();
    }
    
    // Handle sorting
    function handleSort(column, header) {
        if (sortColumn === column) {
            sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            sortColumn = column;
            sortDirection = 'asc';
        }
        
        // Update header classes
        document.querySelectorAll('.sortable').forEach(h => {
            h.classList.remove('sorted-asc', 'sorted-desc');
        });
        header.classList.add(sortDirection === 'asc' ? 'sorted-asc' : 'sorted-desc');
        
        // Sort data
        filteredEmployees.sort((a, b) => {
            const aValue = a.data[column] || '';
            const bValue = b.data[column] || '';
            
            if (sortDirection === 'asc') {
                return aValue.localeCompare(bValue);
            } else {
                return bValue.localeCompare(aValue);
            }
        });
        
        updateTable();
    }
    
    // Handle entries per page change
    function handleEntriesChange(e) {
        entriesPerPage = parseInt(e.target.value);
        currentPage = 1;
        updateTable();
    }
    
    // Handle select all
    function handleSelectAll(e) {
        const checkboxes = document.querySelectorAll('.row-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = e.target.checked;
        });
        updateSelectedEmployees();
    }
    
    // Handle row select
    function handleRowSelect(checkbox) {
        updateSelectedEmployees();
        
        // Update select all checkbox
        const allCheckboxes = document.querySelectorAll('.row-checkbox');
        const checkedCheckboxes = document.querySelectorAll('.row-checkbox:checked');
        const selectAllCheckbox = document.getElementById('selectAll');
        
        if (selectAllCheckbox) {
            if (checkedCheckboxes.length === 0) {
                selectAllCheckbox.indeterminate = false;
                selectAllCheckbox.checked = false;
            } else if (checkedCheckboxes.length === allCheckboxes.length) {
                selectAllCheckbox.indeterminate = false;
                selectAllCheckbox.checked = true;
            } else {
                selectAllCheckbox.indeterminate = true;
                selectAllCheckbox.checked = false;
            }
        }
    }
    
    // Update selected employees
    function updateSelectedEmployees() {
        selectedEmployees = Array.from(document.querySelectorAll('.row-checkbox:checked'))
            .map(checkbox => checkbox.value);
        
        const selectedCount = selectedEmployees.length;
        const selectedCountElement = document.getElementById('selectedCount');
        if (selectedCountElement) {
            selectedCountElement.textContent = selectedCount;
        }
        
        const bulkActionsCard = document.getElementById('bulkActionsCard');
        if (bulkActionsCard) {
            if (selectedCount > 0) {
                bulkActionsCard.style.display = 'block';
            } else {
                bulkActionsCard.style.display = 'none';
            }
        }
    }
    
    // Update table
    function updateTable() {
        const startIndex = (currentPage - 1) * entriesPerPage;
        const endIndex = startIndex + entriesPerPage;
        const pageEmployees = filteredEmployees.slice(startIndex, endIndex);
        
        // Hide all rows
        allEmployees.forEach(employee => {
            employee.element.style.display = 'none';
        });
        
        // Show page employees
        pageEmployees.forEach(employee => {
            employee.element.style.display = '';
            employee.element.classList.add('fade-in');
        });
        
        updateStats();
        
        // Update showing count
        const showingCountElement = document.getElementById('showingCount');
        if (showingCountElement) {
            showingCountElement.textContent = filteredEmployees.length;
        }
        
        const startEntryElement = document.getElementById('startEntry');
        if (startEntryElement) {
            startEntryElement.textContent = startIndex + 1;
        }
        
        const endEntryElement = document.getElementById('endEntry');
        if (endEntryElement) {
            endEntryElement.textContent = Math.min(endIndex, filteredEmployees.length);
        }
        
        const totalEntriesElement = document.getElementById('totalEntries');
        if (totalEntriesElement) {
            totalEntriesElement.textContent = filteredEmployees.length;
        }
    }
    
    // Update stats
    function updateStats() {
        const totalEmployees = allEmployees.length;
        const activeEmployees = allEmployees.filter(e => e.data.status.includes('ใช้งาน')).length;
        const inactiveEmployees = totalEmployees - activeEmployees;
        
        const totalElement = document.getElementById('totalEmployees');
        if (totalElement) {
            totalElement.textContent = totalEmployees;
        }
        
        const activeElement = document.getElementById('activeEmployees');
        if (activeElement) {
            activeElement.textContent = activeEmployees;
        }
        
        const inactiveElement = document.getElementById('inactiveEmployees');
        if (inactiveElement) {
            inactiveElement.textContent = inactiveEmployees;
        }
    }
    
    // Highlight search results
    function highlightSearchResults(searchTerm) {
        if (searchTerm === '') {
            // Remove all highlights
            document.querySelectorAll('.search-highlight').forEach(el => {
                el.outerHTML = el.innerHTML;
            });
            return;
        }
        
        filteredEmployees.forEach(employee => {
            const row = employee.element;
            const cells = row.querySelectorAll('td');
            
            cells.forEach(cell => {
                const text = cell.textContent;
                const regex = new RegExp(`(${searchTerm})`, 'gi');
                const highlightedText = text.replace(regex, '<span class="search-highlight">$1</span>');
                
                if (highlightedText !== text) {
                    cell.innerHTML = highlightedText;
                }
            });
        });
    }
    
    // Debounce function
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    // Global functions for onclick events
    window.clearFilters = function() {
        const searchInput = document.getElementById('searchInput');
        if (searchInput) searchInput.value = '';
        
        const departmentFilter = document.getElementById('departmentFilter');
        if (departmentFilter) departmentFilter.value = '';
        
        const roleFilter = document.getElementById('roleFilter');
        if (roleFilter) roleFilter.value = '';
        
        const statusFilter = document.getElementById('statusFilter');
        if (statusFilter) statusFilter.value = '';
        
        filteredEmployees = [...allEmployees];
        currentPage = 1;
        updateTable();
        
        // Remove search highlights
        document.querySelectorAll('.search-highlight').forEach(el => {
            el.outerHTML = el.innerHTML;
        });
        
        showNotification('ล้างตัวกรองแล้ว', 'success');
    };
    
    window.refreshTable = function() {
        loadEmployeeData();
        showNotification('รีเฟรชข้อมูลแล้ว', 'success');
    };
    
    // Employee actions
    window.viewEmployee = function(id) {
        console.log('View employee:', id);
        showNotification('ฟีเจอร์ดูรายละเอียดจะพร้อมใช้งานเร็วๆ นี้', 'info');
    };
    
    window.editEmployee = function(id) {
        window.location.href = `/employees/${id}/edit`;
    };
    
    window.deleteEmployee = function(id) {
        if (confirm('คุณแน่ใจหรือไม่ที่จะลบพนักงานนี้?')) {
            showNotification('ฟีเจอร์ลบจะพร้อมใช้งานเร็วๆ นี้', 'info');
        }
    };
    
    // Bulk actions
    window.bulkActivate = function() {
        if (selectedEmployees.length === 0) return;
        showNotification(`กำลังเปิดใช้งาน ${selectedEmployees.length} รายการ...`, 'info');
    };
    
    window.bulkDeactivate = function() {
        if (selectedEmployees.length === 0) return;
        showNotification(`กำลังปิดใช้งาน ${selectedEmployees.length} รายการ...`, 'info');
    };
    
    window.bulkDelete = function() {
        if (selectedEmployees.length === 0) return;
        if (confirm(`คุณแน่ใจหรือไม่ที่จะลบพนักงาน ${selectedEmployees.length} รายการ?`)) {
            showNotification(`กำลังลบ ${selectedEmployees.length} รายการ...`, 'info');
        }
    };
    
    window.bulkExport = function() {
        if (selectedEmployees.length === 0) return;
        showNotification(`กำลังส่งออกข้อมูล ${selectedEmployees.length} รายการ...`, 'info');
    };
    
    // Export functions
    window.exportToExcel = function() {
        showNotification('กำลังส่งออกไฟล์ Excel...', 'info');
    };
    
    window.exportToPDF = function() {
        showNotification('กำลังส่งออกไฟล์ PDF...', 'info');
    };
    
    window.exportToCSV = function() {
        showNotification('กำลังส่งออกไฟล์ CSV...', 'info');
    };
    
    window.printEmployee = function() {
        window.print();
    };
    
    // Notification function
    function showNotification(message, type = 'info') {
        const alertClass = {
            'success': 'alert-success',
            'error': 'alert-danger',
            'warning': 'alert-warning',
            'info': 'alert-info'
        };
        
        const icon = {
            'success': 'fas fa-check-circle',
            'error': 'fas fa-exclamation-circle',
            'warning': 'fas fa-exclamation-triangle',
            'info': 'fas fa-info-circle'
        };
        
        const notification = document.createElement('div');
        notification.className = `alert ${alertClass[type]} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            <i class="${icon[type]} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }
    
    console.log('✅ Employee Index Page Ready');
});
</script>
@endpush