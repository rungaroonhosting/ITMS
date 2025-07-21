<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Employee Management Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options for the Employee Management
    | System in ITMS v1.4
    |
    */

    /*
    |--------------------------------------------------------------------------
    | System Information
    |--------------------------------------------------------------------------
    */
    
    'system' => [
        'name' => 'IT Management System',
        'abbreviation' => 'ITMS',
        'version' => '1.4.0',
        'module' => 'Employee Management',
        'author' => 'ITMS Development Team',
        'last_updated' => '2025-01-21',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Settings
    |--------------------------------------------------------------------------
    */
    
    'defaults' => [
        'employee_id_prefix' => 'EMP',
        'employee_status' => 'active',
        'pagination_count' => 10,
        'password_min_length' => 6,
        'express_username_length' => 7,
        'express_password_length' => 4,
    ],

    /*
    |--------------------------------------------------------------------------
    | Departments
    |--------------------------------------------------------------------------
    */
    
    'departments' => [
        'available' => [
            'แผนกบริหาร',
            'แผนกการเงิน', 
            'แผนกบัญชี',
            'แผนกบัญชีและการเงิน',
            'แผนกขาย',
            'แผนกการตลาด',
            'แผนกผลิต',
            'แผนกควบคุมคุณภาพ',
            'แผนกจัดซื้อ',
            'แผนกทรัพยากรบุคคล',
            'แผนกเทคโนโลยีสารสนเทศ',
            'แผนกกฎหมาย',
            'แผนกวิจัยและพัฒนา'
        ],
        
        'accounting_departments' => [
            'แผนกบัญชี',
            'แผนกบัญชีและการเงิน',
            'แผนกการเงิน'
        ],
        
        'codes' => [
            'แผนกบริหาร' => 'ADM',
            'แผนกการเงิน' => 'FIN',
            'แผนกบัญชี' => 'ACC',
            'แผนกบัญชีและการเงิน' => 'AF',
            'แผนกขาย' => 'SAL',
            'แผนกการตลาด' => 'MKT',
            'แผนกผลิต' => 'PRD',
            'แผนกควบคุมคุณภาพ' => 'QC',
            'แผนกจัดซื้อ' => 'PUR',
            'แผนกทรัพยากรบุคคล' => 'HR',
            'แผนกเทคโนโลยีสารสนเทศ' => 'IT',
            'แผนกกฎหมาย' => 'LEG',
            'แผนกวิจัยและพัฒนา' => 'RD'
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Roles & Permissions
    |--------------------------------------------------------------------------
    */
    
    'roles' => [
        'super_admin' => [
            'name' => 'Super Administrator',
            'description' => 'Full system access including password viewing and trash management',
            'permissions' => [
                'view_all_employees',
                'create_employee',
                'edit_employee',
                'delete_employee',
                'view_password',
                'manage_trash',
                'bulk_actions',
                'export_data',
                'reset_password',
                'manage_express',
                'force_delete',
                'empty_trash'
            ]
        ],
        
        'it_admin' => [
            'name' => 'IT Administrator',
            'description' => 'IT department admin with most privileges except password viewing',
            'permissions' => [
                'view_all_employees',
                'create_employee',
                'edit_employee',
                'delete_employee',
                'bulk_actions',
                'export_data',
                'reset_password',
                'manage_express'
            ]
        ],
        
        'hr' => [
            'name' => 'Human Resources',
            'description' => 'HR staff can manage employee data but cannot view passwords',
            'permissions' => [
                'view_all_employees',
                'create_employee',
                'edit_employee',
                'bulk_actions',
                'export_data'
            ]
        ],
        
        'express' => [
            'name' => 'Express User',
            'description' => 'Can manage accounting department employees with Express credentials',
            'permissions' => [
                'view_accounting_employees',
                'create_employee',
                'edit_employee',
                'manage_express'
            ]
        ],
        
        'employee' => [
            'name' => 'Employee',
            'description' => 'Regular employee can only view their own information',
            'permissions' => [
                'view_own_profile'
            ]
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Express System Configuration
    |--------------------------------------------------------------------------
    */
    
    'express' => [
        'enabled' => true,
        'username_pattern' => '/^[a-z0-9]{7}$/',
        'password_pattern' => '/^[a-z0-9]{4}$/',
        'require_number_in_password' => true,
        'check_username_uniqueness' => true,
        'auto_generate' => true,
        'allowed_departments' => [
            'แผนกบัญชี',
            'แผนกบัญชีและการเงิน',
            'แผนกการเงิน'
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Trash Management
    |--------------------------------------------------------------------------
    */
    
    'trash' => [
        'enabled' => true,
        'auto_delete_after_days' => 30,
        'super_admin_only' => true,
        'bulk_operations' => true,
        'confirmation_required' => [
            'force_delete' => true,
            'empty_trash' => true,
            'bulk_force_delete' => true
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Export Configuration
    |--------------------------------------------------------------------------
    */
    
    'export' => [
        'formats' => ['excel', 'csv', 'pdf'],
        'include_password' => [
            'super_admin' => true,
            'it_admin' => false,
            'hr' => false,
            'express' => false,
            'employee' => false
        ],
        'filename_format' => 'employees_{date}_{time}',
        'pdf' => [
            'paper_size' => 'A4',
            'orientation' => 'landscape',
            'include_statistics' => true,
            'watermark' => 'ITMS',
            'confidential_footer' => true
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation Rules
    |--------------------------------------------------------------------------
    */
    
    'validation' => [
        'employee_id' => 'required|string|max:20|unique:employees,employee_id',
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'english_name' => 'nullable|string|max:255',
        'email' => 'required|email|unique:employees,email',
        'phone' => 'nullable|string|max:20',
        'department' => 'required|string|max:255',
        'position' => 'required|string|max:255',
        'hire_date' => 'required|date',
        'salary' => 'nullable|numeric|min:0',
        'status' => 'required|in:active,inactive',
        'password' => 'required|string|min:6',
        'express_username' => 'nullable|string|size:7|regex:/^[a-z0-9]+$/',
        'express_password' => 'nullable|string|size:4|regex:/^[a-z0-9]+$/',
    ],

    /*
    |--------------------------------------------------------------------------
    | Feature Flags
    |--------------------------------------------------------------------------
    */
    
    'features' => [
        'soft_delete' => true,
        'trash_management' => true,
        'express_auto_generation' => true,
        'bulk_actions' => true,
        'preview_mode' => true,
        'password_reset' => true,
        'data_export' => true,
        'role_based_access' => true,
        'audit_logging' => false, // Future feature
        'email_notifications' => false, // Future feature
        'api_access' => true,
        'mobile_responsive' => true
    ],

    /*
    |--------------------------------------------------------------------------
    | UI Configuration
    |--------------------------------------------------------------------------
    */
    
    'ui' => [
        'theme' => 'bootstrap5',
        'items_per_page' => [10, 25, 50, 100],
        'default_items_per_page' => 10,
        'show_statistics' => true,
        'show_department_summary' => true,
        'enable_search' => true,
        'enable_filters' => true,
        'enable_sorting' => true,
        'pagination_style' => 'default'
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    */
    
    'security' => [
        'password_hashing' => 'bcrypt',
        'require_strong_passwords' => false,
        'session_timeout' => 120, // minutes
        'max_login_attempts' => 5,
        'lockout_duration' => 15, // minutes
        'audit_trail' => false, // Future feature
        'two_factor_auth' => false, // Future feature
    ],

    /*
    |--------------------------------------------------------------------------
    | API Configuration
    |--------------------------------------------------------------------------
    */
    
    'api' => [
        'enabled' => true,
        'rate_limiting' => true,
        'max_requests_per_minute' => 60,
        'authentication_required' => true,
        'endpoints' => [
            'employees' => true,
            'search' => true,
            'generate_express' => true,
            'statistics' => true,
            'export' => false // Disabled for security
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    */
    
    'logging' => [
        'enabled' => true,
        'log_channel' => 'daily',
        'log_employee_actions' => [
            'create' => true,
            'update' => true,
            'delete' => true,
            'restore' => true,
            'force_delete' => true,
            'export' => true,
            'password_reset' => true
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    */
    
    'cache' => [
        'enabled' => true,
        'ttl' => 3600, // 1 hour
        'tags' => ['employees', 'departments', 'statistics'],
        'clear_on_update' => true
    ],

];