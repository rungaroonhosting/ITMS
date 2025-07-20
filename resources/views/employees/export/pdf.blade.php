<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายงานข้อมูลพนักงาน</title>
    <style>
        @font-face {
            font-family: 'THSarabunNew';
            font-style: normal;
            font-weight: normal;
            src: url('{{ storage_path('fonts/THSarabunNew.ttf') }}') format('truetype');
        }
        
        @font-face {
            font-family: 'THSarabunNew';
            font-style: normal;
            font-weight: bold;
            src: url('{{ storage_path('fonts/THSarabunNew Bold.ttf') }}') format('truetype');
        }

        body {
            font-family: 'THSarabunNew', Arial, sans-serif;
            font-size: 14px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #007bff;
        }

        .header h1 {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin: 0;
        }

        .header .subtitle {
            font-size: 16px;
            color: #666;
            margin: 5px 0;
        }

        .header .date {
            font-size: 12px;
            color: #999;
            margin-top: 10px;
        }

        .stats-section {
            margin-bottom: 30px;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }

        .stats-title {
            font-size: 18px;
            font-weight: bold;
            color: #495057;
            margin-bottom: 15px;
            text-align: center;
        }

        .stats-grid {
            display: table;
            width: 100%;
            table-layout: fixed;
        }

        .stats-row {
            display: table-row;
        }

        .stats-item {
            display: table-cell;
            padding: 10px;
            text-align: center;
            border-right: 1px solid #dee2e6;
            vertical-align: middle;
        }

        .stats-item:last-child {
            border-right: none;
        }

        .stats-number {
            font-size: 20px;
            font-weight: bold;
            color: #007bff;
            display: block;
        }

        .stats-label {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #495057;
            margin: 30px 0 15px 0;
            padding-bottom: 5px;
            border-bottom: 2px solid #007bff;
        }

        .employee-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 12px;
        }

        .employee-table th {
            background-color: #007bff;
            color: white;
            padding: 10px 8px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #0056b3;
        }

        .employee-table td {
            padding: 8px;
            border: 1px solid #dee2e6;
            vertical-align: middle;
        }

        .employee-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .employee-table tr:hover {
            background-color: #e9ecef;
        }

        .status-badge {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            text-align: center;
            color: white;
        }

        .status-active {
            background-color: #28a745;
        }

        .status-inactive {
            background-color: #6c757d;
        }

        .express-badge {
            background-color: #17a2b8;
            color: white;
            padding: 2px 6px;
            border-radius: 8px;
            font-size: 9px;
            font-weight: bold;
        }

        .department-summary {
            margin-top: 30px;
        }

        .dept-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }

        .dept-item:last-child {
            border-bottom: none;
        }

        .dept-name {
            font-weight: bold;
            color: #495057;
        }

        .dept-count {
            color: #007bff;
            font-weight: bold;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            text-align: center;
            font-size: 12px;
            color: #666;
        }

        .page-break {
            page-break-before: always;
        }

        .no-print {
            display: none;
        }

        @media print {
            body {
                padding: 10px;
            }
            
            .stats-section {
                background-color: #f8f9fa !important;
                -webkit-print-color-adjust: exact;
            }
            
            .employee-table th {
                background-color: #007bff !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
            }
        }

        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80px;
            color: rgba(0, 123, 255, 0.1);
            z-index: -1;
            font-weight: bold;
        }

        .confidential {
            color: #dc3545;
            font-weight: bold;
            text-align: center;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <!-- Watermark -->
    <div class="watermark">ITMS</div>

    <!-- Header -->
    <div class="header">
        <h1>รายงานข้อมูลพนักงาน</h1>
        <div class="subtitle">IT Management System (ITMS)</div>
        <div class="subtitle">Employee Management v1.4</div>
        <div class="date">สร้างเมื่อ: {{ now()->format('d/m/Y H:i:s น.') }}</div>
        
        @if($canViewPassword)
            <div class="confidential">*** เอกสารลับ - เฉพาะ SuperAdmin ***</div>
        @endif
    </div>

    <!-- Statistics Section -->
    <div class="stats-section">
        <div class="stats-title">สถิติภาพรวม</div>
        <div class="stats-grid">
            <div class="stats-row">
                <div class="stats-item">
                    <span class="stats-number">{{ $stats['total'] }}</span>
                    <div class="stats-label">พนักงานทั้งหมด</div>
                </div>
                <div class="stats-item">
                    <span class="stats-number">{{ $stats['active'] }}</span>
                    <div class="stats-label">ใช้งาน</div>
                </div>
                <div class="stats-item">
                    <span class="stats-number">{{ $stats['inactive'] }}</span>
                    <div class="stats-label">ไม่ใช้งาน</div>
                </div>
                <div class="stats-item">
                    <span class="stats-number">{{ $stats['departments']->count() }}</span>
                    <div class="stats-label">แผนกทั้งหมด</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Employee List -->
    <div class="section-title">รายชื่อพนักงาน</div>
    
    @if($employees->count() > 0)
        <table class="employee-table">
            <thead>
                <tr>
                    <th style="width: 10%">รหัสพนักงาน</th>
                    <th style="width: 20%">ชื่อ-นามสกุล</th>
                    <th style="width: 15%">แผนก</th>
                    <th style="width: 15%">ตำแหน่ง</th>
                    <th style="width: 20%">อีเมล</th>
                    <th style="width: 10%">วันที่เริ่มงาน</th>
                    <th style="width: 10%">สถานะ</th>
                    @if($canViewPassword)
                        <th style="width: 10%">Express</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($employees as $employee)
                    <tr>
                        <td><strong>{{ $employee->employee_id }}</strong></td>
                        <td>
                            {{ $employee->full_name }}
                            @if($employee->english_name)
                                <br><small style="color: #666;">{{ $employee->english_name }}</small>
                            @endif
                        </td>
                        <td>{{ $employee->department }}</td>
                        <td>{{ $employee->position }}</td>
                        <td>{{ $employee->email }}</td>
                        <td>{{ $employee->hire_date->format('d/m/Y') }}</td>
                        <td>
                            <span class="status-badge status-{{ $employee->status }}">
                                {{ $employee->status === 'active' ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                            </span>
                        </td>
                        @if($canViewPassword)
                            <td>
                                @if($employee->express_username)
                                    <span class="express-badge">Express</span>
                                    <br><small>{{ $employee->express_username }}</small>
                                @else
                                    <span style="color: #999;">-</span>
                                @endif
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div style="text-align: center; padding: 40px; color: #666;">
            ไม่มีข้อมูลพนักงาน
        </div>
    @endif

    <!-- Page Break -->
    <div class="page-break"></div>

    <!-- Department Summary -->
    <div class="section-title">สรุปตามแผนก</div>
    <div class="department-summary">
        @if($stats['departments']->count() > 0)
            @foreach($stats['departments'] as $department => $count)
                <div class="dept-item">
                    <span class="dept-name">{{ $department }}</span>
                    <span class="dept-count">{{ $count }} คน</span>
                </div>
            @endforeach
        @else
            <div style="text-align: center; color: #666; padding: 20px;">
                ไม่มีข้อมูลแผนก
            </div>
        @endif
    </div>

    @if($canViewPassword)
        <!-- Express Users Section -->
        <div class="section-title">พนักงานที่ใช้ระบบ Express</div>
        @php $expressUsers = $employees->filter(function($emp) { return $emp->express_username; }); @endphp
        
        @if($expressUsers->count() > 0)
            <table class="employee-table">
                <thead>
                    <tr>
                        <th>รหัสพนักงาน</th>
                        <th>ชื่อ-นามสกุล</th>
                        <th>แผนก</th>
                        <th>Express Username</th>
                        <th>Express Password</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($expressUsers as $employee)
                        <tr>
                            <td>{{ $employee->employee_id }}</td>
                            <td>{{ $employee->full_name }}</td>
                            <td>{{ $employee->department }}</td>
                            <td><code>{{ $employee->express_username }}</code></td>
                            <td><code>{{ $employee->express_password ?? '[ไม่มี]' }}</code></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div style="text-align: center; color: #666; padding: 20px;">
                ไม่มีพนักงานที่ใช้ระบบ Express
            </div>
        @endif
    @endif

    <!-- Footer -->
    <div class="footer">
        <div>IT Management System (ITMS) - Employee Management v1.4</div>
        <div>สร้างโดย: {{ auth()->user()->first_name_th ?? 'ระบบ' }} {{ auth()->user()->last_name_th ?? '' }}</div>
        <div>วันที่สร้างรายงาน: {{ now()->format('d/m/Y H:i:s น.') }}</div>
        <div>หน้า: <span class="page-number"></span></div>
        
        @if($canViewPassword)
            <div style="color: #dc3545; font-weight: bold; margin-top: 10px;">
                ⚠️ เอกสารนี้มีข้อมูลความปลอดภัย กรุณาเก็บรักษาอย่างปลอดภัย
            </div>
        @endif
    </div>

    <script>
        // Add page numbers
        window.onload = function() {
            var pageNumbers = document.querySelectorAll('.page-number');
            pageNumbers.forEach(function(element) {
                element.textContent = '1';
            });
        };
    </script>
</body>
</html>