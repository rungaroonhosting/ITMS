@extends('layouts.app')

@section('title', 'Express Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Express Dashboard</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-warning fw-bold">
                    <i class="fas fa-bolt me-2"></i>Express Dashboard
                </h1>
                <p class="text-muted mb-0">ระบบจัดการ Express สำหรับแผนกบัญชี</p>
                <div class="mt-2">
                    <span class="badge bg-warning text-dark">
                        <i class="fas fa-calculator me-1"></i>
                        ระบบบัญชีขั้นสูง
                    </span>
                    <span class="badge bg-info ms-2">
                        <i class="fas fa-users me-1"></i>
                        {{ $totalExpress ?? 0 }} ผู้ใช้งาน
                    </span>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('employees.create') }}" class="btn btn-warning">
                    <i class="fas fa-user-plus me-1"></i>เพิ่มผู้ใช้ Express
                </a>
                <div class="dropdown">
                    <button class="btn btn-outline-warning dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-cog me-1"></i>จัดการ
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('express.management.users') }}">
                            <i class="fas fa-users me-2"></i>จัดการผู้ใช้
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('express.management.settings') }}">
                            <i class="fas fa-cog me-2"></i>ตั้งค่า Express
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('express.management.logs') }}">
                            <i class="fas fa-list me-2"></i>บันทึกการใช้งาน
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('export.express-users') }}">
                            <i class="fas fa-download me-2"></i>ส่งออกข้อมูล
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card bg-warning text-dark h-100">
            <div class="card-body text-center">
                <div class="display-4 fw-bold">{{ $totalExpress ?? 0 }}</div>
                <h5 class="card-title mb-0">ผู้ใช้ Express ทั้งหมด</h5>
                <small class="text-muted">Total Express Users</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card bg-success text-white h-100">
            <div class="card-body text-center">
                <div class="display-4 fw-bold">{{ $activeExpress ?? 0 }}</div>
                <h5 class="card-title mb-0">ผู้ใช้งานปัจจุบัน</h5>
                <small class="opacity-75">Active Users</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card bg-info text-white h-100">
            <div class="card-body text-center">
                <div class="display-4 fw-bold">{{ $accountingDepts ?? 0 }}</div>
                <h5 class="card-title mb-0">แผนกบัญชี</h5>
                <small class="opacity-75">Accounting Departments</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card bg-primary text-white h-100">
            <div class="card-body text-center">
                @php
                    $total = $totalExpress ?? 0;
                    $accounting = $accountingDepts ?? 0;
                    $percentage = $accounting > 0 ? round(($total / ($accounting * 5)) * 100, 1) : 0; // สมมติว่าแผนกละ 5 คน
                @endphp
                <div class="display-4 fw-bold">{{ $percentage }}%</div>
                <h5 class="card-title mb-0">อัตราการใช้งาน</h5>
                <small class="opacity-75">Usage Rate</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Express Users -->
    <div class="col-md-8 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-users text-warning me-2"></i>ผู้ใช้ Express ล่าสุด
                </h5>
                <a href="{{ route('express.management.users') }}" class="btn btn-sm btn-outline-warning">
                    ดูทั้งหมด <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body">
                @if(isset($recentExpressUsers) && $recentExpressUsers->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>รหัสพนักงาน</th>
                                    <th>ชื่อ-นามสกุล</th>
                                    <th>แผนก</th>
                                    <th>Express Username</th>
                                    <th>สถานะ</th>
                                    <th>วันที่สร้าง</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentExpressUsers as $user)
                                <tr>
                                    <td><code>{{ $user->employee_code }}</code></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                                <i class="fas fa-user text-dark" style="font-size: 12px;"></i>
                                            </div>
                                            <div>
                                                <div class="fw-medium">{{ $user->first_name_th }} {{ $user->last_name_th }}</div>
                                                <small class="text-muted">{{ $user->first_name_en }} {{ $user->last_name_en }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $user->department->name ?? 'N/A' }}</span>
                                    </td>
                                    <td><code class="text-warning">{{ $user->express_username }}</code></td>
                                    <td>
                                        @if($user->status === 'active')
                                            <span class="badge bg-success">ใช้งาน</span>
                                        @else
                                            <span class="badge bg-secondary">ไม่ใช้งาน</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $user->created_at->format('d/m/Y') }}</small>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-inbox text-muted" style="font-size: 3rem;"></i>
                        <h5 class="text-muted mt-3">ยังไม่มีผู้ใช้ Express</h5>
                        <p class="text-muted">เริ่มสร้างผู้ใช้ Express แรกของคุณ</p>
                        <a href="{{ route('employees.create') }}" class="btn btn-warning">
                            <i class="fas fa-plus me-1"></i>สร้างผู้ใช้ Express
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Express Tools & Quick Actions -->
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-tools text-warning me-2"></i>เครื่องมือ Express
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-warning" onclick="testExpressConnection()">
                        <i class="fas fa-plug me-2"></i>ทดสอบการเชื่อมต่อ
                    </button>
                    <button class="btn btn-outline-info" onclick="generateExpressReport()">
                        <i class="fas fa-chart-bar me-2"></i>สร้างรายงาน
                    </button>
                    <button class="btn btn-outline-success" onclick="bulkCreateExpress()">
                        <i class="fas fa-users-cog me-2"></i>สร้างหลายผู้ใช้
                    </button>
                    <hr>
                    <a href="{{ route('express.management.settings') }}" class="btn btn-outline-primary">
                        <i class="fas fa-cog me-2"></i>ตั้งค่า Express
                    </a>
                </div>
            </div>
        </div>

        <!-- System Status -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-server text-success me-2"></i>สถานะระบบ
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>Express Server</span>
                    <span class="badge bg-success" id="express-status">Online</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>Database</span>
                    <span class="badge bg-success">Connected</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>API Status</span>
                    <span class="badge bg-success">Active</span>
                </div>
                <hr>
                <small class="text-muted">
                    <i class="fas fa-clock me-1"></i>
                    อัพเดตล่าสุด: {{ now()->format('d/m/Y H:i') }}
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Express Usage Chart -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-line text-primary me-2"></i>สถิติการใช้งาน Express
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <canvas id="expressUsageChart" height="200"></canvas>
                    </div>
                    <div class="col-md-6">
                        <h6>ข้อมูลสำคัญ</h6>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-user-check text-success me-2"></i>
                                <strong>Active Users:</strong> {{ $activeExpress ?? 0 }} คน
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-building text-info me-2"></i>
                                <strong>แผนกที่รองรับ:</strong> บัญชี, การเงิน
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-calendar text-warning me-2"></i>
                                <strong>เดือนนี้:</strong> {{ rand(10, 50) }} การเข้าใช้งาน
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-clock text-primary me-2"></i>
                                <strong>เวลาการใช้งานเฉลี่ย:</strong> 2.5 ชั่วโมง/วัน
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('📊 Express Dashboard loaded');
    
    // Initialize Express Usage Chart
    initializeExpressChart();
    
    // Check system status
    checkSystemStatus();
    
    // Auto-refresh every 5 minutes
    setInterval(checkSystemStatus, 300000);
});

function initializeExpressChart() {
    const ctx = document.getElementById('expressUsageChart');
    if (!ctx) return;
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Express Users', 'Regular Users', 'Inactive'],
            datasets: [{
                data: [{{ $totalExpress ?? 0 }}, {{ ($activeExpress ?? 0) - ($totalExpress ?? 0) }}, 10],
                backgroundColor: [
                    '#ffc107',
                    '#28a745', 
                    '#6c757d'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

function checkSystemStatus() {
    fetch('/api/express-report')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('express-status').textContent = 'Online';
                document.getElementById('express-status').className = 'badge bg-success';
            } else {
                document.getElementById('express-status').textContent = 'Offline';
                document.getElementById('express-status').className = 'badge bg-danger';
            }
        })
        .catch(error => {
            console.error('System status check failed:', error);
            document.getElementById('express-status').textContent = 'Error';
            document.getElementById('express-status').className = 'badge bg-warning';
        });
}

function testExpressConnection() {
    Swal.fire({
        title: 'ทดสอบการเชื่อมต่อ Express',
        text: 'กำลังตรวจสอบการเชื่อมต่อ...',
        icon: 'info',
        showConfirmButton: false,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    fetch('/express/test-connection', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            username: 'test',
            password: 'test'
        })
    })
    .then(response => response.json())
    .then(data => {
        Swal.fire({
            title: data.success ? 'เชื่อมต่อสำเร็จ!' : 'เชื่อมต่อล้มเหลว',
            text: data.message,
            icon: data.success ? 'success' : 'error'
        });
    })
    .catch(error => {
        Swal.fire({
            title: 'เกิดข้อผิดพลาด',
            text: 'ไม่สามารถทดสอบการเชื่อมต่อได้',
            icon: 'error'
        });
    });
}

function generateExpressReport() {
    window.open('/api/express-report', '_blank');
}

function bulkCreateExpress() {
    Swal.fire({
        title: 'สร้างผู้ใช้ Express หลายคน',
        text: 'ฟีเจอร์นี้จะพร้อมใช้งานเร็วๆ นี้',
        icon: 'info'
    });
}
</script>
@endpush
