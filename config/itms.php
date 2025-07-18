<?php

return [
    /*
    |--------------------------------------------------------------------------
    | ITMS Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration options for IT Management System
    |
    */

    'system' => [
        'name' => 'IT Management System',
        'version' => '1.0.0',
        'timezone' => 'Asia/Bangkok',
    ],

    'roles' => [
        'admin' => 'Administrator',
        'manager' => 'Manager',
        'employee' => 'Employee',
        'technician' => 'Technician',
    ],

    'permissions' => [
        'employee' => [
            'employee.view',
            'employee.create',
            'employee.update',
            'employee.delete',
        ],
        'asset' => [
            'asset.view',
            'asset.create',
            'asset.update',
            'asset.delete',
        ],
        'incident' => [
            'incident.view',
            'incident.create',
            'incident.update',
            'incident.delete',
        ],
        'service_request' => [
            'service_request.view',
            'service_request.create',
            'service_request.update',
            'service_request.delete',
        ],
        'agreement' => [
            'agreement.view',
            'agreement.create',
            'agreement.update',
            'agreement.delete',
        ],
    ],

    'priorities' => [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
        'critical' => 'Critical',
    ],

    'statuses' => [
        'active' => 'Active',
        'inactive' => 'Inactive',
        'pending' => 'Pending',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'cancelled' => 'Cancelled',
        'completed' => 'Completed',
        'in_progress' => 'In Progress',
        'expired' => 'Expired',
    ],

    'asset_types' => [
        'computer' => 'Computer',
        'laptop' => 'Laptop',
        'server' => 'Server',
        'printer' => 'Printer',
        'monitor' => 'Monitor',
        'network_device' => 'Network Device',
        'mobile_device' => 'Mobile Device',
        'software' => 'Software',
        'other' => 'Other',
    ],

    'incident_types' => [
        'hardware_failure' => 'Hardware Failure',
        'software_issue' => 'Software Issue',
        'network_problem' => 'Network Problem',
        'security_incident' => 'Security Incident',
        'user_error' => 'User Error',
        'other' => 'Other',
    ],

    'service_request_types' => [
        'new_account' => 'New Account',
        'password_reset' => 'Password Reset',
        'software_installation' => 'Software Installation',
        'hardware_request' => 'Hardware Request',
        'access_request' => 'Access Request',
        'maintenance' => 'Maintenance',
        'other' => 'Other',
    ],

    'agreement_types' => [
        'sla' => 'Service Level Agreement',
        'license' => 'License Agreement',
        'maintenance' => 'Maintenance Agreement',
        'support' => 'Support Agreement',
        'other' => 'Other',
    ],

    'file_uploads' => [
        'max_size' => 10240, // KB
        'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt'],
        'storage_path' => 'uploads',
    ],

    'pagination' => [
        'default_per_page' => 15,
        'max_per_page' => 100,
    ],

    'notifications' => [
        'email_enabled' => true,
        'sms_enabled' => false,
        'push_enabled' => true,
    ],
];
