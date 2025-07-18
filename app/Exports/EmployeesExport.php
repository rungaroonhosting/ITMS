<?php

namespace App\Exports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EmployeesExport implements FromQuery, WithHeadings, WithMapping
{
    protected $search, $department, $status, $role;

    public function __construct($search = null, $department = null, $status = null, $role = null)
    {
        $this->search = $search;
        $this->department = $department;
        $this->status = $status;
        $this->role = $role;
    }

    public function query()
    {
        $query = Employee::query();

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }
        
        if ($this->department) {
            $query->where('department', $this->department);
        }
        
        if ($this->status) {
            $query->where('status', $this->status);
        }
        
        if ($this->role) {
            $query->where('role', $this->role);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'ID',
            'ชื่อ',
            'แผนก',
            'ตำแหน่ง',
            'สถานะ',
            'วันที่สร้าง'
        ];
    }

    public function map($employee): array
    {
        return [
            $employee->id,
            $employee->name,
            $employee->department,
            $employee->role,
            $employee->status,
            $employee->created_at->format('d/m/Y')
        ];
    }
}
