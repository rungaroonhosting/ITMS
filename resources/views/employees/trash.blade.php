@extends('layouts.app')

@section('title', 'ถังขยะ - พนักงาน')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('employees.index') }}">จัดการพนักงาน</a>
    </li>
    <li class="breadcrumb-item active">ถังขยะ</li>
@endsection

@push('styles')
<style>
/* ✅ ITMS Theme Colors - Same as index.blade.php */
:root {
    --primary-red: #B54544;
    --primary-orange: #E6952A;
    --accent-red: #A33E3D;
    --accent-orange: #CC7F1F;
    --text-primary: #1A252F;
    --text-secondary: #5A6C7D;
}

/* ✅ Base Layout - Same as index.blade.php */
* {
    box-sizing: border-box;
}

html, body {
    margin: 0;
    padding: 0;
    overflow-x: hidden;
    scroll-behavior: smooth;
}

.container-fluid {
    padding-top: 0 !important;
    margin-top: 0 !important;
}

/* ✅ Fade-in Animation System - Same as index.blade.php */
.fade-in-element {
    opacity: 0;
    transform: translateY(30px);
    transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}

.fade-in-element.visible {
    opacity: 1;
    transform: translateY(0);
}

.fade-in-scale {
    opacity: 0;
    transform: scale(0.95);
    transition: all 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.fade-in-scale.visible {
    opacity: 1;
    transform: scale(1);
}

.fade-in-slide-left {
    opacity: 0;
    transform: translateX(-50px);
    transition: all 0.7s cubic-bezier(0.4, 0, 0.2, 1);
}

.fade-in-slide-left.visible {
    opacity: 1;
    transform: translateX(0);
}

.fade-in-slide-right {
    opacity: 0;
    transform: translateX(50px);
    transition: all 0.7s cubic-bezier(0.4, 0, 0.2, 1);
}

.fade-in-slide-right.visible {
    opacity: 1;
    transform: translateX(0);
}

.fade-in-bounce {
    opacity: 0;
    transform: translateY(50px) scale(0.8);
    transition: all 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

.fade-in-bounce.visible {
    opacity: 1;
    transform: translateY(0) scale(1);
}

.fade-in-rotate {
    opacity: 0;
    transform: rotate(-10deg) scale(0.9);
    transition: all 0.7s cubic-bezier(0.4, 0, 0.2, 1);
}

.fade-in-rotate.visible {
    opacity: 1;
    transform: rotate(0deg) scale(1);
}

/* ✅ Header styling - Exact same as index.blade.php */
.trash-header {
    background: linear-gradient(135deg, var(--primary-red) 0%, var(--primary-orange) 100%);
    border-radius: 12px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 32px rgba(181, 69, 68, 0.4);
    margin-bottom: 1.5rem;
    padding: 1.5rem;
}

.trash-header h1 {
    color: #FFFFFF !important;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
    font-weight: 800 !important;
    font-size: 2.2rem !important;
    margin-bottom: 0.25rem;
    line-height: 1.2;
}

.trash-header p {
    color: #FFFFFF !important;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
    opacity: 0.95 !important;
    font-size: 0.9rem;
    margin-bottom: 0;
}

.header-icon-container {
    position: relative;
    background: rgba(255, 255, 255, 0.25) !important;
    backdrop-filter: blur(10px);
    border: 2px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    border-radius: 50%;
    width: 55px;
    height: 55px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.header-icon-container:hover {
    transform: scale(1.05);
    background: rgba(255, 255, 255, 0.35) !important;
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.2);
}

.header-icon-container i {
    font-size: 1.5rem;
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
}

.trash-header::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
    animation: rotate 20s linear infinite;
    pointer-events: none;
}

@keyframes rotate {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* ✅ Statistics cards - Enhanced fade-in effects */
.trash-stat-card {
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    cursor: pointer;
    border: 2px solid rgba(181, 69, 68, 0.1);
    border-radius: 12px;
    overflow: hidden;
    position: relative;
    background: #ffffff;
    height: 100%;
}

.trash-stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(135deg, var(--primary-red) 0%, var(--primary-orange) 100%);
}

.trash-stat-card:hover {
    box-shadow: 0 12px 35px rgba(181, 69, 68, 0.25);
    transform: translateY(-5px) scale(1.02);
    border-color: rgba(181, 69, 68, 0.3);
}

.stat-icon-container {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 0.75rem;
    transition: all 0.3s ease;
}

.stat-number {
    font-size: 1.75rem;
    font-weight: 800;
    line-height: 1;
    margin-bottom: 0.25rem;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.stat-label {
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--text-secondary);
    margin-bottom: 0;
    line-height: 1.2;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* ✅ Alert Section - Enhanced visibility */
.alert-enhanced {
    border: none;
    border-radius: 12px;
    padding: 1.25rem;
    margin-bottom: 1.5rem;
    position: relative;
    overflow: hidden;
    border-left: 5px solid;
    backdrop-filter: blur(10px);
}

.alert-enhanced.alert-warning {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
    border-left-color: #ffc107;
    border: 2px solid rgba(255, 193, 7, 0.3);
    box-shadow: 0 8px 25px rgba(255, 193, 7, 0.2);
}

.alert-enhanced .alert-icon {
    font-size: 2.5rem;
    margin-right: 1rem;
    color: #f39c12;
}

/* ✅ Search Card - Same style as index.blade.php */
.search-card-enhanced {
    border: 2px solid rgba(181, 69, 68, 0.1);
    border-radius: 12px;
    transition: all 0.3s ease;
    background: #ffffff;
    box-shadow: 0 8px 25px rgba(181, 69, 68, 0.15);
    margin-bottom: 1.5rem;
}

.search-card-enhanced:hover {
    border-color: rgba(181, 69, 68, 0.2);
    box-shadow: 0 8px 25px rgba(181, 69, 68, 0.1);
}

.search-card-enhanced .form-label {
    font-size: 0.85rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: var(--primary-red);
}

.search-card-enhanced .input-group-text {
    background: linear-gradient(135deg, rgba(181, 69, 68, 0.1), rgba(230, 149, 42, 0.1));
    border-color: rgba(181, 69, 68, 0.2);
    color: var(--primary-red);
    font-size: 0.9rem;
}

.search-card-enhanced .form-control,
.search-card-enhanced .form-select {
    font-size: 0.9rem;
    border-color: rgba(181, 69, 68, 0.15);
    transition: all 0.3s ease;
}

.search-card-enhanced .form-control:focus,
.search-card-enhanced .form-select:focus {
    border-color: var(--primary-red);
    box-shadow: 0 0 0 0.2rem rgba(181, 69, 68, 0.15);
}

/* ✅ Table Card - Enhanced fade-in effects */
.trash-table-card {
    border: 2px solid rgba(181, 69, 68, 0.1);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(181, 69, 68, 0.15);
    background: #ffffff;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.trash-table-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 20px 50px rgba(181, 69, 68, 0.25);
    border-color: rgba(181, 69, 68, 0.3);
}

.trash-table-card .card-header {
    background: linear-gradient(135deg, var(--primary-red) 0%, var(--primary-orange) 100%);
    color: white;
    border: none;
    padding: 1rem 1.5rem;
    position: relative;
}

.trash-table-card .card-header::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="2" fill="white" fill-opacity="0.1"/><circle cx="80" cy="80" r="2" fill="white" fill-opacity="0.1"/></svg>');
    pointer-events: none;
}

.trash-table-card .card-header h6 {
    color: white !important;
    font-weight: 800;
    font-size: 1rem;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
    margin: 0;
}

/* ✅ Table - Same style as index.blade.php */
.table-enhanced {
    margin-bottom: 0;
    font-size: 0.9rem;
}

.table-enhanced thead th {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: none;
    font-weight: 800;
    color: var(--text-primary);
    padding: 1rem;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    white-space: nowrap;
    vertical-align: middle;
}

.table-enhanced tbody tr {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: none;
    cursor: pointer;
}

/* ✅ Row Hover Effects - Smooth fade-in */
.table-enhanced tbody tr:hover {
    background: linear-gradient(135deg, rgba(181, 69, 68, 0.08), rgba(230, 149, 42, 0.08)) !important;
    box-shadow: 0 8px 25px rgba(181, 69, 68, 0.15);
    border-radius: 8px;
    transform: translateY(-2px);
}

.table-enhanced tbody tr:hover td:first-child {
    border-radius: 8px 0 0 8px;
}

.table-enhanced tbody tr:hover td:last-child {
    border-radius: 0 8px 8px 0;
}

.table-enhanced tbody td {
    padding: 1.2rem 1rem;
    border-top: 1px solid #f1f3f4;
    vertical-align: middle;
    font-size: 0.85rem;
    line-height: 1.4;
    transition: all 0.3s ease;
}

/* ✅ Deleted User Icon */
.deleted-user-icon {
    position: relative;
    display: inline-block;
}

.deleted-user-icon::after {
    content: '✗';
    position: absolute;
    top: -5px;
    right: -5px;
    background: #dc3545;
    color: white;
    font-size: 12px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    box-shadow: 0 2px 8px rgba(220, 53, 69, 0.4);
}

/* ✅ Badges - Same style as index.blade.php */
.badge-enhanced {
    font-weight: 800;
    padding: 10px 18px;
    border-radius: 25px;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 1.2px;
    transition: all 0.2s ease;
    line-height: 1;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
    border: 2px solid rgba(255, 255, 255, 0.3);
}

.badge-trash-enhanced {
    background: linear-gradient(135deg, #6c757d, #495057);
    color: white;
    box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
}

.badge-danger-enhanced {
    background: linear-gradient(135deg, var(--primary-red), var(--primary-orange));
    color: white;
    box-shadow: 0 4px 15px rgba(181, 69, 68, 0.4);
    animation: glow-red 2s infinite alternate;
}

@keyframes glow-red {
    from { 
        box-shadow: 0 4px 15px rgba(181, 69, 68, 0.4);
        transform: scale(1);
    }
    to { 
        box-shadow: 0 6px 25px rgba(181, 69, 68, 0.7);
        transform: scale(1.05);
    }
}

/* ✅ Action Buttons - Enhanced hover effects */
.action-btn-group {
    display: flex;
    gap: 0.35rem;
    align-items: center;
}

.action-btn {
    width: 35px;
    height: 35px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 2px solid;
    padding: 0;
    text-decoration: none;
    font-weight: 700;
}

.action-btn:hover {
    transform: scale(1.1) translateY(-2px);
    text-decoration: none;
}

.action-btn-success {
    border-color: #28a745;
    color: #28a745;
    background: rgba(40, 167, 69, 0.1);
}

.action-btn-success:hover {
    background: #28a745;
    color: white;
    box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
}

.action-btn-info {
    border-color: #17a2b8;
    color: #17a2b8;
    background: rgba(23, 162, 184, 0.1);
}

.action-btn-info:hover {
    background: #17a2b8;
    color: white;
    box-shadow: 0 6px 20px rgba(23, 162, 184, 0.4);
}

.action-btn-danger {
    border-color: #dc3545;
    color: #dc3545;
    background: rgba(220, 53, 69, 0.1);
}

.action-btn-danger:hover {
    background: #dc3545;
    color: white;
    box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4);
}

/* ✅ Buttons - Enhanced effects */
.btn-primary-red {
    background: linear-gradient(135deg, var(--primary-red), var(--accent-red));
    border: none;
    color: white;
    font-weight: 600;
    position: relative;
    overflow: hidden;
    border-radius: 10px;
    padding: 10px 20px;
    text-transform: uppercase;
    letter-spacing: 1px;
    box-shadow: 0 6px 20px rgba(181, 69, 68, 0.4);
    transition: all 0.3s ease;
}

.btn-primary-red::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: left 0.6s ease;
}

.btn-primary-red:hover {
    background: linear-gradient(135deg, var(--accent-red), var(--primary-red));
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(181, 69, 68, 0.5);
    color: white;
}

.btn-primary-red:hover::before {
    left: 100%;
}

.btn-warning-enhanced {
    background: linear-gradient(135deg, #ffc107 0%, var(--primary-orange) 100%);
    border: none;
    color: #212529;
    font-weight: 600;
    border-radius: 10px;
    padding: 10px 20px;
    text-transform: uppercase;
    letter-spacing: 1px;
    transition: all 0.3s ease;
    box-shadow: 0 6px 20px rgba(255, 193, 7, 0.4);
}

.btn-warning-enhanced:hover {
    background: linear-gradient(135deg, var(--primary-orange), #ffc107);
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(255, 193, 7, 0.5);
    color: #212529;
}

/* ✅ Empty State - Enhanced fade-in */
.empty-state-enhanced {
    background: linear-gradient(135deg, rgba(181, 69, 68, 0.05), rgba(230, 149, 42, 0.05));
    border: 2px solid rgba(181, 69, 68, 0.1);
    border-radius: 15px;
    text-align: center;
    padding: 3rem;
    margin: 2rem 0;
}

.empty-state-enhanced .empty-icon {
    font-size: 5rem;
    color: rgba(181, 69, 68, 0.3);
    margin-bottom: 1.5rem;
}

/* ✅ Selected Items Counter */
.selected-counter {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background: linear-gradient(135deg, var(--primary-red), var(--primary-orange));
    color: white;
    padding: 0.75rem 1.25rem;
    border-radius: 25px;
    box-shadow: 0 6px 20px rgba(181, 69, 68, 0.4);
    z-index: 1000;
    display: none;
    animation: slideUp 0.3s ease;
}

/* ✅ Modal Styling */
.modal-content {
    border: none;
    border-radius: 15px;
    box-shadow: 0 15px 50px rgba(0, 0, 0, 0.2);
}

.modal-header {
    background: linear-gradient(135deg, var(--primary-red) 0%, var(--primary-orange) 100%);
    color: white;
    border: none;
    border-radius: 15px 15px 0 0;
}

.modal-header .btn-close {
    filter: invert(1);
}

/* ✅ Checkbox Selection */
.table-enhanced tbody tr:has(.trashed-employee-checkbox:checked) {
    background: linear-gradient(135deg, rgba(255, 193, 7, 0.15), rgba(230, 149, 42, 0.15)) !important;
    border-left: 4px solid #ffc107;
}

.form-check-input:checked {
    background-color: var(--primary-red);
    border-color: var(--primary-red);
}

.form-check-input:focus {
    border-color: var(--primary-red);
    outline: 0;
    box-shadow: 0 0 0 0.25rem rgba(181, 69, 68, 0.25);
}

/* ✅ Deleted Date Styling */
.deleted-date {
    font-family: 'Courier New', monospace;
    font-weight: 700;
    color: var(--primary-red);
    background: rgba(181, 69, 68, 0.1);
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    border-left: 3px solid var(--primary-red);
}

/* ✅ Mobile Cards - Enhanced fade-in effects */
.employee-mobile-card-enhanced {
    border: 2px solid rgba(181, 69, 68, 0.1);
    border-radius: 12px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    background: #ffffff;
    margin-bottom: 0.75rem;
    overflow: hidden;
    position: relative;
    box-shadow: 0 8px 25px rgba(181, 69, 68, 0.15);
}

.employee-mobile-card-enhanced::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 3px;
    height: 100%;
    background: linear-gradient(135deg, var(--primary-red) 0%, var(--primary-orange) 100%);
}

.employee-mobile-card-enhanced:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 50px rgba(181, 69, 68, 0.25);
    border-color: rgba(181, 69, 68, 0.3);
}

/* ✅ Refresh Button */
.refresh-btn {
    position: fixed;
    bottom: 90px;
    right: 20px;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, #17a2b8, #138496);
    border: none;
    color: white;
    box-shadow: 0 6px 20px rgba(23, 162, 184, 0.4);
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    font-size: 1.2rem;
    opacity: 0;
    transform: translateY(100px);
}

.refresh-btn.visible {
    opacity: 1;
    transform: translateY(0);
}

.refresh-btn:hover {
    background: linear-gradient(135deg, #138496, #117a8b);
    transform: scale(1.1);
    box-shadow: 0 8px 25px rgba(23, 162, 184, 0.5);
    color: white;
}

.refresh-btn:active {
    transform: scale(0.95);
}

/* ✅ Color Utilities */
.text-danger-enhanced { 
    color: var(--primary-red) !important; 
    font-weight: 600; 
}

.text-warning-enhanced { 
    color: var(--primary-orange) !important; 
    font-weight: 600; 
}

.text-success-enhanced { 
    color: #28a745 !important; 
    font-weight: 600; 
}

/* ✅ Mobile Responsive */
@media (max-width: 768px) {
    .trash-header {
        text-align: center;
        padding: 1.25rem;
        flex-direction: column !important;
    }
    
    .trash-header h1 {
        font-size: 1.8rem !important;
    }
    
    .trash-header .btn {
        margin-top: 15px;
    }
    
    .header-icon-container {
        width: 50px;
        height: 50px;
        margin: 0 auto 1rem;
    }
    
    .action-btn {
        width: 30px;
        height: 30px;
        font-size: 0.75rem;
    }
    
    .stat-number {
        font-size: 1.5rem;
    }
    
    .alert-enhanced .alert-icon {
        font-size: 2rem;
        margin-right: 0.75rem;
    }
    
    .employee-mobile-card-enhanced:hover {
        transform: translateY(-3px);
    }
    
    .trash-stat-card:hover {
        transform: translateY(-2px) scale(1.01);
    }
}

@media (max-width: 576px) {
    .trash-header h1 {
        font-size: 1.5rem !important;
    }
    
    .btn-group {
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .header-icon-container {
        padding: 12px !important;
    }
    
    .header-icon-container i {
        font-size: 1.2rem !important;
    }
}

/* ✅ SweetAlert2 Styles */
.swal2-popup {
    border-radius: 15px !important;
    box-shadow: 0 15px 50px rgba(0, 0, 0, 0.2) !important;
}

.swal2-title {
    color: #1a1a1a !important;
    font-weight: 700 !important;
    font-size: 1.4rem !important;
}

.swal2-html-container {
    color: #333333 !important;
    font-size: 1rem !important;
    line-height: 1.5 !important;
}

.swal2-confirm {
    background: linear-gradient(135deg, var(--primary-red), var(--primary-orange)) !important;
    border: none !important;
    border-radius: 10px !important;
    padding: 0.6rem 1.5rem !important;
    font-weight: 600 !important;
    box-shadow: 0 6px 20px rgba(181, 69, 68, 0.4) !important;
}

.swal2-cancel {
    background: #6c757d !important;
    border: none !important;
    border-radius: 10px !important;
    padding: 0.6rem 1.5rem !important;
    font-weight: 600 !important;
}

/* ✅ Animations */
@keyframes slideUp {
    from { opacity: 0; transform: translateY(100%); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes slideInRight {
    from { opacity: 0; transform: translateX(100%); }
    to { opacity: 1; transform: translateX(0); }
}

@keyframes slideOutRight {
    from { opacity: 1; transform: translateX(0); }
    to { opacity: 0; transform: translateX(100%); }
}

/* ✅ Notification Styles */
.notification-toast {
    border-left: 5px solid;
    border-radius: 12px !important;
    box-shadow: 0 8px 25px rgba(181, 69, 68, 0.15);
}

.notification-toast.alert-success {
    border-left-color: #1E7E34;
}

.notification-toast.alert-danger {
    border-left-color: #C82333;
}

.notification-toast.alert-warning {
    border-left-color: #E0A800;
}

.notification-toast.alert-info {
    border-left-color: #138496;
}

/* ✅ Enhanced Stagger Animation for Cards */
.stagger-item {
    animation-fill-mode: both;
}

.stagger-1 { animation-delay: 0.1s; }
.stagger-2 { animation-delay: 0.2s; }
.stagger-3 { animation-delay: 0.3s; }
.stagger-4 { animation-delay: 0.4s; }
.stagger-5 { animation-delay: 0.5s; }
.stagger-6 { animation-delay: 0.6s; }
.stagger-7 { animation-delay: 0.7s; }
.stagger-8 { animation-delay: 0.8s; }

/* ✅ Progressive Enhancement Loading */
.loading-skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 2s infinite;
}

@keyframes loading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

/* ✅ Enhanced Focus States */
.btn:focus,
.form-control:focus,
.form-select:focus {
    outline: 2px solid var(--primary-orange);
    outline-offset: 2px;
}

/* ✅ Smooth Page Transitions */
.page-transition {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.page-fade-in {
    animation: pageSlideUp 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}

/* ✅ Enhanced Card Fade-in - เหมือน branches */
.fade-card {
    opacity: 0;
    transform: translateY(30px);
    transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.fade-card.visible {
    opacity: 1;
    transform: translateY(0);
}

/* ✅ Loading skeleton effect */
.loading-skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

/* ✅ Enhanced Button Loading */
.btn-loading {
    position: relative;
    color: transparent !important;
}

.btn-loading::after {
    content: '';
    position: absolute;
    width: 16px;
    height: 16px;
    top: 50%;
    left: 50%;
    margin-left: -8px;
    margin-top: -8px;
    border: 2px solid #ffffff;
    border-radius: 50%;
    border-top-color: transparent;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

@keyframes pageSlideUp {
    from {
        opacity: 0;
        transform: translateY(50px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
@endpush

@section('content')
<div class="container-fluid page-fade-in">
    <!-- ✅ Header Section - Enhanced Fade-in -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center p-4 rounded-3 text-white trash-header fade-in-element" data-delay="0">
                <div class="d-flex align-items-center">
                    <div class="bg-white bg-opacity-20 rounded-circle p-3 me-3 header-icon-container fade-in-scale" data-delay="100">
                        <i class="fas fa-trash fa-2x text-white"></i>
                    </div>
                    <div class="fade-in-slide-left" data-delay="200">
                        <h1 class="mb-1 font-weight-bold">ถังขยะ - พนักงาน</h1>
                        <p class="mb-0 opacity-90">จัดการพนักงานที่ถูกลบแล้ว พร้อมระบบกู้คืนและลบถาวร (เฉพาะ Super Admin)</p>
                    </div>
                </div>
                <div class="d-none d-md-flex gap-2 fade-in-slide-right" data-delay="300">
                    <a href="{{ route('employees.index') }}" class="btn btn-light btn-lg shadow-sm font-weight-bold">
                        <i class="fas fa-arrow-left me-2"></i>กลับไปรายการพนักงาน
                    </a>
                    @if($trashedEmployees->count() > 0)
                        <button type="button" class="btn btn-warning-enhanced shadow-sm" onclick="openBulkRestoreModal()">
                            <i class="fas fa-undo me-2"></i>กู้คืนหลายรายการ
                            <span class="badge bg-light text-dark ms-1" id="selectedCount" style="display: none;">0</span>
                        </button>
                        <button type="button" class="btn btn-primary-red shadow-sm" id="emptyTrashBtn">
                            <i class="fas fa-fire me-2"></i>ล้างถังขยะ
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- ✅ Statistics Cards - Enhanced Stagger Animation -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm trash-stat-card h-100 fade-in-bounce stagger-item stagger-1">
                <div class="card-body text-center">
                    <div class="stat-icon-container" style="background: linear-gradient(135deg, #dc3545, #c82333);">
                        <i class="fas fa-trash text-white fa-lg"></i>
                    </div>
                    <div class="stat-number text-danger-enhanced">{{ $trashedEmployees->count() }}</div>
                    <div class="stat-label">พนักงานในถังขยะ</div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm trash-stat-card h-100 fade-in-bounce stagger-item stagger-2">
                <div class="card-body text-center">
                    <div class="stat-icon-container" style="background: linear-gradient(135deg, #ffc107, var(--primary-orange));">
                        <i class="fas fa-clock text-white fa-lg"></i>
                    </div>
                    <div class="stat-number text-warning-enhanced">
                        @if($trashedEmployees->count() > 0)
                            {{ (int) $trashedEmployees->first()->deleted_at->diffInDays() }}
                        @else
                            0
                        @endif
                    </div>
                    <div class="stat-label">วันที่ลบล่าสุด</div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm trash-stat-card h-100 fade-in-bounce stagger-item stagger-3">
                <div class="card-body text-center">
                    <div class="stat-icon-container" style="background: linear-gradient(135deg, #28a745, #20c997);">
                        <i class="fas fa-shield-alt text-white fa-lg"></i>
                    </div>
                    <div class="stat-number text-success-enhanced">100%</div>
                    <div class="stat-label">การป้องกัน</div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm trash-stat-card h-100 fade-in-bounce stagger-item stagger-4">
                <div class="card-body text-center">
                    <div class="stat-icon-container" style="background: linear-gradient(135deg, #17a2b8, #138496);">
                        <i class="fas fa-undo text-white fa-lg"></i>
                    </div>
                    <div class="stat-number" style="color: #17a2b8;">Soft</div>
                    <div class="stat-label">Delete Mode</div>
                </div>
            </div>
        </div>
    </div>

    @if($trashedEmployees->count() > 0)
    <!-- ✅ Warning Alert - Enhanced fade-in -->
    <div class="alert alert-enhanced alert-warning d-flex align-items-center mb-4 fade-in-element stagger-5" role="alert">
        <i class="fas fa-exclamation-triangle alert-icon"></i>
        <div>
            <h6 class="mb-1 fw-bold" style="color: #2c3e50; font-size: 1.1rem;">⚠️ คำเตือนสำคัญ</h6>
            <p class="mb-0" style="color: #34495e; font-weight: 600; line-height: 1.5;">
                ข้อมูลในถังขยะยังสามารถ <strong style="color: #27ae60; background: rgba(39, 174, 96, 0.1); padding: 2px 6px; border-radius: 4px;">กู้คืนได้</strong> หากต้องการลบอย่างถาวร ให้ใช้ปุ่ม "ลบถาวร" หรือ "ล้างถังขยะ"
                <br><strong style="color: #ffffff; background: linear-gradient(135deg, var(--primary-red), var(--accent-red)); padding: 4px 8px; border-radius: 6px; margin-top: 4px; display: inline-block;">การลบถาวรจะไม่สามารถกู้คืนได้อีก!</strong>
            </p>
        </div>
    </div>

    <!-- ✅ Search and Filter - Enhanced fade-in -->
    <div class="card search-card-enhanced fade-in-slide-left stagger-6">
        <div class="card-body p-3">
            <div class="row g-2">
                <div class="col-md-6">
                    <label class="form-label">ค้นหาในถังขยะ</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" class="form-control" id="searchTrashInput" placeholder="ค้นหาชื่อ, อีเมล, รหัส...">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">วันที่ลบ</label>
                    <select class="form-select" id="deletedDateFilter">
                        <option value="">วันที่ลบทั้งหมด</option>
                        <option value="today">วันนี้</option>
                        <option value="week">สัปดาห์นี้</option>
                        <option value="month">เดือนนี้</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-1">
                        <button type="button" class="btn btn-primary-red flex-fill" id="clearTrashFilters">
                            <i class="fas fa-times me-1"></i>ล้างตัวกรอง
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ✅ Trashed Employees Table - Enhanced fade-in -->
    <div class="card trash-table-card fade-in-element stagger-7">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="fas fa-list me-2"></i>รายการพนักงานในถังขยะ ({{ $trashedEmployees->count() }} คน)
                </h6>
                <div class="d-flex gap-1">
                    <span class="badge badge-danger-enhanced">{{ $trashedEmployees->count() }} รายการ</span>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <!-- ✅ Desktop Table -->
            <div class="d-none d-lg-block">
                <div class="table-responsive">
                    <table class="table table-enhanced" id="trashedEmployeesTable">
                        <thead>
                            <tr>
                                <th width="50">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="selectAllTrashed">
                                    </div>
                                </th>
                                <th width="120">รูป/รหัส</th>
                                <th>ชื่อ-นามสกุล</th>
                                <th width="120">แผนก</th>
                                <th width="180">ข้อมูลติดต่อ</th>
                                <th width="140">วันที่ลบ</th>
                                <th width="180">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($trashedEmployees as $index => $employee)
                                <tr data-id="{{ $employee->id }}" 
                                    data-deleted-date="{{ $employee->deleted_at->format('Y-m-d') }}"
                                    class="fade-in-element stagger-item"
                                    style="animation-delay: {{ 0.8 + ($index * 0.1) }}s;">
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input trashed-employee-checkbox" type="checkbox" value="{{ $employee->id }}"
                                                   data-name="{{ $employee->full_name_th }}">
                                        </div>
                                    </td>
                                    
                                    <!-- ✅ Photo + Code Column -->
                                    <td class="photo-code-cell text-center">
                                        <!-- รูปภาพ -->
                                        <div class="photo-wrapper mb-2">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center bg-light text-muted deleted-user-icon"
                                                 style="width: 50px; height: 50px; border: 2px solid #dee2e6; margin: 0 auto;">
                                                <i class="fas fa-user-slash"></i>
                                            </div>
                                        </div>
                                        <!-- รหัสพนักงาน -->
                                        <div>
                                            <span class="badge badge-trash-enhanced">{{ $employee->employee_code }}</span>
                                        </div>
                                    </td>
                                    
                                    <!-- ชื่อ-นามสกุล -->
                                    <td>
                                        <div class="employee-name">
                                            <div class="fw-bold text-muted">{{ $employee->full_name_th }}</div>
                                            @if($employee->full_name_en)
                                                <small class="text-muted d-block opacity-75">{{ $employee->full_name_en }}</small>
                                            @endif
                                            @if($employee->nickname)
                                                <div class="mt-1">
                                                    <small class="badge bg-light text-dark opacity-75">"{{ $employee->nickname }}"</small>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    
                                    <!-- แผนก -->
                                    <td>
                                        @if($employee->department)
                                            <div>
                                                <span class="badge badge-trash-enhanced mb-1">{{ $employee->department->code }}</span>
                                                <div class="small text-muted mt-1" style="line-height: 1.2;">{{ Str::limit($employee->department->name, 12) }}</div>
                                            </div>
                                        @else
                                            <span class="text-muted">ไม่ระบุ</span>
                                        @endif
                                    </td>
                                    
                                    <!-- ข้อมูลติดต่อ -->
                                    <td>
                                        <div class="contact-info">
                                            @if($employee->email)
                                                <div class="email-line text-muted mb-1">
                                                    <i class="fas fa-envelope me-1"></i>
                                                    <span>{{ $employee->email }}</span>
                                                </div>
                                            @endif
                                            
                                            @if($employee->phone)
                                                <div class="phone-line text-muted mb-1">
                                                    <i class="fas fa-phone me-1"></i>
                                                    {{ $employee->phone }}
                                                </div>
                                            @endif
                                            
                                            @if($employee->line_id)
                                                <div class="line-line text-muted">
                                                    <i class="fab fa-line me-1"></i>
                                                    {{ $employee->line_id }}
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    
                                    <!-- วันที่ลบ -->
                                    <td>
                                        <div class="deleted-date">
                                            {{ $employee->deleted_at->format('d/m/Y') }}
                                        </div>
                                        <small class="text-muted mt-1 d-block">{{ $employee->deleted_at->diffForHumans() }}</small>
                                    </td>
                                    
                                    <!-- จัดการ -->
                                    <td>
                                        <div class="action-btn-group">
                                            <button type="button" 
                                                    class="action-btn action-btn-success restore-btn" 
                                                    data-id="{{ $employee->id }}" 
                                                    data-name="{{ $employee->full_name_th }}"
                                                    data-bs-toggle="tooltip" title="กู้คืน">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                            
                                            <button type="button" 
                                                    class="action-btn action-btn-info preview-btn" 
                                                    data-id="{{ $employee->id }}"
                                                    data-bs-toggle="tooltip" title="ดูข้อมูล">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            
                                            <button type="button" 
                                                    class="action-btn action-btn-danger force-delete-btn" 
                                                    data-id="{{ $employee->id }}" 
                                                    data-name="{{ $employee->full_name_th }}"
                                                    data-bs-toggle="tooltip" title="ลบถาวร">
                                                <i class="fas fa-fire"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ✅ Mobile Card View -->
            <div class="d-lg-none" id="mobileTrashList">
                @foreach($trashedEmployees as $index => $employee)
                    <div class="employee-mobile-card-enhanced p-3 fade-in-element stagger-item" 
                         data-id="{{ $employee->id }}" 
                         data-deleted-date="{{ $employee->deleted_at->format('Y-m-d') }}"
                         style="animation-delay: {{ 0.8 + ($index * 0.05) }}s;">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-light text-muted deleted-user-icon"
                                             style="width: 45px; height: 45px; border: 2px solid #dee2e6;">
                                            <i class="fas fa-user-slash"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-bold text-muted mb-1">{{ $employee->full_name_th }}</div>
                                        @if($employee->full_name_en)
                                            <small class="text-muted d-block mb-1 opacity-75">{{ $employee->full_name_en }}</small>
                                        @endif
                                        <div class="d-flex flex-wrap gap-1 mb-1">
                                            <span class="badge badge-trash-enhanced">{{ $employee->employee_code }}</span>
                                            @if($employee->department)
                                                <span class="badge badge-trash-enhanced">{{ $employee->department->code }}</span>
                                            @endif
                                        </div>
                                        <div class="small text-muted mb-1">
                                            <i class="fas fa-envelope me-1"></i>{{ Str::limit($employee->email ?? 'ไม่ระบุ', 25) }}
                                        </div>
                                        <div class="small deleted-date">
                                            <i class="fas fa-trash me-1"></i>{{ $employee->deleted_at->format('d/m/Y H:i') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="action-btn-group justify-content-end mb-2">
                                    <button type="button" 
                                            class="action-btn action-btn-success restore-btn" 
                                            data-id="{{ $employee->id }}" 
                                            data-name="{{ $employee->full_name_th }}">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                    
                                    <button type="button" 
                                            class="action-btn action-btn-info preview-btn" 
                                            data-id="{{ $employee->id }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    
                                    <button type="button" 
                                            class="action-btn action-btn-danger force-delete-btn" 
                                            data-id="{{ $employee->id }}" 
                                            data-name="{{ $employee->full_name_th }}">
                                        <i class="fas fa-fire"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    @else
    <!-- ✅ Empty State - Enhanced fade-in -->
    <div class="empty-state-enhanced fade-in-bounce stagger-5">
        <div class="empty-icon">
            <i class="fas fa-trash"></i>
        </div>
        <h4 class="text-muted mb-3">ถังขยะว่างเปล่า</h4>
        <p class="text-muted mb-4">ไม่มีพนักงานในถังขยะ ระบบจัดการข้อมูลที่ลบแล้วจะแสดงที่นี่</p>
        <a href="{{ route('employees.index') }}" class="btn btn-primary-red">
            <i class="fas fa-arrow-left me-2"></i>กลับไปรายการพนักงาน
        </a>
    </div>
    @endif
</div>

<!-- ✅ Bulk Restore Modal -->
<div class="modal fade" id="bulkRestoreModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-undo me-2"></i>กู้คืนหลายรายการ
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info d-flex align-items-center">
                    <i class="fas fa-info-circle me-3 fa-2x"></i>
                    <div>
                        <h6 class="mb-1">ข้อมูลการเลือก</h6>
                        <div id="selectedTrashCount" class="text-muted">กรุณาเลือกพนักงานที่ต้องการกู้คืนจากถังขยะ</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-warning-enhanced" id="executeBulkRestore">
                    <i class="fas fa-undo me-1"></i>กู้คืนที่เลือก
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ✅ Employee Preview Modal -->
<div class="modal fade" id="employeePreviewModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-eye me-2"></i>ดูข้อมูลพนักงานในถังขยะ
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="employeePreviewContent">
                <div class="text-center">
                    <div class="spinner-border text-danger" role="status">
                        <span class="visually-hidden">กำลังโหลด...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

<!-- ✅ Selected Items Counter -->
<div class="selected-counter" id="selectedCounter">
    <div class="d-flex align-items-center">
        <i class="fas fa-check-circle me-2"></i>
        เลือกแล้ว <span id="selectedCountText">0</span> พนักงาน
        <button type="button" class="btn btn-sm btn-light ms-2" onclick="openBulkRestoreModal()">
            <i class="fas fa-undo"></i>
        </button>
    </div>
</div>

<!-- ✅ Refresh Button -->
<button type="button" class="refresh-btn" id="refreshBtn" onclick="location.reload()" title="รีเฟรชหน้า">
    <i class="fas fa-sync-alt"></i>
</button>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🗑️ Enhanced Trash Management System Loading...');
    
    // ✅ Enhanced Fade-in Animation System - เหมือน branches index
function initFadeInAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '50px'
    };

    const cardObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
                cardObserver.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Apply fade-in to cards with stagger delay
    document.querySelectorAll('.trash-stat-card').forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = 'all 0.6s ease';
        
        setTimeout(() => {
            cardObserver.observe(card);
        }, index * 100);
    });

    // Apply to other elements
    document.querySelectorAll('.fade-in-element, .fade-in-scale, .fade-in-slide-left, .fade-in-slide-right, .fade-in-bounce, .fade-in-rotate').forEach((element, index) => {
        const delay = element.dataset.delay || (index * 100);
        setTimeout(() => {
            element.classList.add('visible');
        }, parseInt(delay));
    });

    // Progressive table row loading
    setTimeout(() => {
        progressiveTableLoad();
    }, 800);

    // Show refresh button with delay
    setTimeout(() => {
        const refreshBtn = document.getElementById('refreshBtn');
        if (refreshBtn) {
            refreshBtn.classList.add('visible');
        }
    }, 1500);
}

// ✅ Progressive Table Loading - เหมือน branches
function progressiveTableLoad() {
    const rows = document.querySelectorAll('#trashedEmployeesTable tbody tr');
    const cards = document.querySelectorAll('.employee-mobile-card-enhanced');
    
    // Desktop table rows
    rows.forEach((row, index) => {
        row.style.opacity = '0';
        row.style.transform = 'translateY(20px)';
        row.style.transition = 'all 0.4s ease';
        
        setTimeout(() => {
            row.style.opacity = '1';
            row.style.transform = 'translateY(0)';
        }, index * 50);
    });
    
    // Mobile cards
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateX(-30px)';
        card.style.transition = 'all 0.5s ease';
        
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateX(0)';
        }, index * 80);
    });
}

    // ✅ Initialize animations
    initFadeInAnimations();
    
    // ✅ Global Variables
    let selectedTrashedEmployees = new Set();
    
    // Initialize tooltips
    try {
        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
    } catch (error) {
        console.warn('Tooltip initialization failed:', error);
    }
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    
    // ✅ Filter functionality
    const searchInput = document.getElementById('searchTrashInput');
    const deletedDateFilter = document.getElementById('deletedDateFilter');
    const table = document.getElementById('trashedEmployeesTable');
    const mobileList = document.getElementById('mobileTrashList');
    
    function filterTrashTable() {
        if (!searchInput) return;
        
        const searchTerm = searchInput.value.toLowerCase();
        const dateFilter = deletedDateFilter ? deletedDateFilter.value : '';
        
        const today = new Date();
        const weekAgo = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
        const monthAgo = new Date(today.getTime() - 30 * 24 * 60 * 60 * 1000);
        
        // Filter desktop table
        if (table) {
            const tbody = table.getElementsByTagName('tbody')[0];
            if (tbody) {
                const rows = tbody.getElementsByTagName('tr');
                for (let row of rows) {
                    if (row.querySelector('td')) {
                        try {
                            const nameCell = row.cells[2];
                            const contactCell = row.cells[4];
                            const deletedDateStr = row.dataset.deletedDate;
                            const deletedDate = new Date(deletedDateStr);
                            
                            const name = nameCell ? nameCell.textContent.toLowerCase() : '';
                            const contact = contactCell ? contactCell.textContent.toLowerCase() : '';
                            
                            const matchesSearch = name.includes(searchTerm) || contact.includes(searchTerm);
                            
                            let matchesDate = true;
                            if (dateFilter === 'today') {
                                matchesDate = deletedDate.toDateString() === today.toDateString();
                            } else if (dateFilter === 'week') {
                                matchesDate = deletedDate >= weekAgo;
                            } else if (dateFilter === 'month') {
                                matchesDate = deletedDate >= monthAgo;
                            }
                            
                            row.style.display = matchesSearch && matchesDate ? '' : 'none';
                        } catch (error) {
                            console.warn('Error filtering row:', error);
                        }
                    }
                }
            }
        }
        
        // Filter mobile cards
        if (mobileList) {
            const cards = mobileList.getElementsByClassName('employee-mobile-card-enhanced');
            for (let card of cards) {
                try {
                    const name = card.textContent.toLowerCase();
                    const deletedDateStr = card.dataset.deletedDate;
                    const deletedDate = new Date(deletedDateStr);
                    
                    const matchesSearch = name.includes(searchTerm);
                    
                    let matchesDate = true;
                    if (dateFilter === 'today') {
                        matchesDate = deletedDate.toDateString() === today.toDateString();
                    } else if (dateFilter === 'week') {
                        matchesDate = deletedDate >= weekAgo;
                    } else if (dateFilter === 'month') {
                        matchesDate = deletedDate >= monthAgo;
                    }
                    
                    card.style.display = matchesSearch && matchesDate ? '' : 'none';
                } catch (error) {
                    console.warn('Error filtering mobile card:', error);
                }
            }
        }
        
        updateVisibleTrashCount();
    }
    
    function updateVisibleTrashCount() {
        try {
            let visibleCount = 0;
            
            if (table && window.innerWidth >= 992) {
                const tbody = table.getElementsByTagName('tbody')[0];
                if (tbody) {
                    const visibleRows = Array.from(tbody.getElementsByTagName('tr'))
                                            .filter(row => row.style.display !== 'none' && row.querySelector('td'));
                    visibleCount = visibleRows.length;
                }
            } else if (mobileList) {
                const visibleCards = Array.from(mobileList.getElementsByClassName('employee-mobile-card-enhanced'))
                                        .filter(card => card.style.display !== 'none');
                visibleCount = visibleCards.length;
            }
            
            const headerBadge = document.querySelector('.card-header .badge');
            if (headerBadge) {
                const totalCount = {{ $trashedEmployees->count() }};
                
                if (visibleCount === totalCount) {
                    headerBadge.textContent = `${totalCount} รายการ`;
                } else {
                    headerBadge.textContent = `${visibleCount}/${totalCount} รายการ`;
                }
            }
        } catch (error) {
            console.warn('Error updating visible count:', error);
        }
    }
    
    // Add event listeners
    if (searchInput) searchInput.addEventListener('input', filterTrashTable);
    if (deletedDateFilter) deletedDateFilter.addEventListener('change', filterTrashTable);
    
    // Clear filters
    const clearFiltersBtn = document.getElementById('clearTrashFilters');
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', function() {
            if (searchInput) searchInput.value = '';
            if (deletedDateFilter) deletedDateFilter.value = '';
            filterTrashTable();
            
            showNotification('ล้างตัวกรองเรียบร้อยแล้ว', 'success');
        });
    }
    
    // ✅ Selection Management
    function updateSelectedTrashCounter() {
        const count = selectedTrashedEmployees.size;
        const counter = document.getElementById('selectedCounter');
        const countText = document.getElementById('selectedCountText');
        const headerCount = document.getElementById('selectedCount');
        
        if (countText) countText.textContent = count;
        if (headerCount) {
            headerCount.textContent = count;
            headerCount.style.display = count > 0 ? 'inline' : 'none';
        }
        
        if (counter) {
            counter.style.display = count > 0 ? 'block' : 'none';
        }
        
        // Update bulk modal counter
        const bulkSelectedCount = document.getElementById('selectedTrashCount');
        if (bulkSelectedCount) {
            bulkSelectedCount.textContent = count > 0 ? `เลือกแล้ว ${count} รายการ` : 'กรุณาเลือกพนักงานที่ต้องการกู้คืนจากถังขยะ';
        }
        
        console.log('📊 Selected trashed employees updated:', count, Array.from(selectedTrashedEmployees));
    }
    
    function updateSelectAllCheckbox() {
        const selectAllCheckbox = document.getElementById('selectAllTrashed');
        const allCheckboxes = document.querySelectorAll('.trashed-employee-checkbox');
        const checkedCheckboxes = document.querySelectorAll('.trashed-employee-checkbox:checked');
        
        if (selectAllCheckbox) {
            selectAllCheckbox.checked = allCheckboxes.length > 0 && allCheckboxes.length === checkedCheckboxes.length;
            selectAllCheckbox.indeterminate = checkedCheckboxes.length > 0 && checkedCheckboxes.length < allCheckboxes.length;
        }
    }
    
    // Select all functionality
    const selectAllCheckbox = document.getElementById('selectAllTrashed');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;
            const checkboxes = document.querySelectorAll('.trashed-employee-checkbox');
            
            checkboxes.forEach(checkbox => {
                const row = checkbox.closest('tr, .employee-mobile-card-enhanced');
                if (row && row.style.display !== 'none') {
                    checkbox.checked = isChecked;
                    const employeeId = checkbox.value;
                    
                    if (isChecked) {
                        selectedTrashedEmployees.add(employeeId);
                    } else {
                        selectedTrashedEmployees.delete(employeeId);
                    }
                }
            });
            
            updateSelectedTrashCounter();
        });
    }

    // Individual checkbox handling
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('trashed-employee-checkbox')) {
            const checkbox = e.target;
            const employeeId = checkbox.value;
            
            if (checkbox.checked) {
                selectedTrashedEmployees.add(employeeId);
            } else {
                selectedTrashedEmployees.delete(employeeId);
            }
            
            updateSelectedTrashCounter();
            updateSelectAllCheckbox();
        }
    });
    
    // ✅ Restore individual employee
    document.addEventListener('click', function(e) {
        if (e.target.closest('.restore-btn')) {
            e.preventDefault();
            e.stopPropagation();
            
            const btn = e.target.closest('.restore-btn');
            const employeeId = btn.dataset.id || btn.getAttribute('data-id');
            const employeeName = btn.dataset.name || btn.getAttribute('data-name') || 'ไม่ระบุ';
            
            console.log('🔄 Restore button clicked:', { employeeId, employeeName });
            
            if (employeeId && employeeId !== '0') {
                restoreEmployee(employeeId, employeeName);
            } else {
                showNotification('ไม่พบข้อมูลพนักงานที่ต้องการกู้คืน', 'error');
            }
        }
    });
    
    // ✅ Force delete individual employee
    document.addEventListener('click', function(e) {
        if (e.target.closest('.force-delete-btn')) {
            e.preventDefault();
            e.stopPropagation();
            
            const btn = e.target.closest('.force-delete-btn');
            const employeeId = btn.dataset.id || btn.getAttribute('data-id');
            const employeeName = btn.dataset.name || btn.getAttribute('data-name') || 'ไม่ระบุ';
            
            console.log('🔥 Force delete button clicked:', { employeeId, employeeName });
            
            if (employeeId && employeeId !== '0') {
                forceDeleteEmployee(employeeId, employeeName);
            } else {
                showNotification('ไม่พบข้อมูลพนักงานที่ต้องการลบถาวร', 'error');
            }
        }
    });
    
    // ✅ Preview employee
    document.addEventListener('click', function(e) {
        if (e.target.closest('.preview-btn')) {
            e.preventDefault();
            e.stopPropagation();
            
            const btn = e.target.closest('.preview-btn');
            const employeeId = btn.dataset.id || btn.getAttribute('data-id');
            
            console.log('👁️ Preview button clicked:', { employeeId });
            
            if (employeeId && employeeId !== '0') {
                previewEmployee(employeeId);
            } else {
                showNotification('ไม่พบข้อมูลพนักงานที่ต้องการดู', 'error');
            }
        }
    });

    // ✅ Enhanced Action Button Loading States
document.addEventListener('click', function(e) {
    if (e.target.closest('.action-btn')) {
        const btn = e.target.closest('.action-btn');
        const originalContent = btn.innerHTML;
        const originalClasses = btn.className;
        
        // Add loading state
        btn.classList.add('btn-loading');
        btn.disabled = true;
        
        // Restore after realistic delay
        setTimeout(() => {
            btn.classList.remove('btn-loading');
            btn.disabled = false;
            btn.innerHTML = originalContent;
        }, 1500);
    }
});

    // ✅ Functions
    async function restoreEmployee(employeeId, employeeName) {
        try {
            const result = await Swal.fire({
                title: '🔄 ยืนยันการกู้คืน',
                html: `
                    <div style="text-align: left; color: #333;">
                        <div style="background: linear-gradient(135deg, #d4edda, #c3e6cb); border: 1px solid #28a745; border-radius: 8px; padding: 15px; margin-bottom: 15px;">
                            <div style="display: flex; align-items: center; margin-bottom: 10px;">
                                <i class="fas fa-undo" style="color: #28a745; font-size: 24px; margin-right: 10px;"></i>
                                <strong style="color: #155724; font-size: 16px;">การกู้คืนข้อมูลพนักงาน</strong>
                            </div>
                            <p style="color: #333333; margin: 0; line-height: 1.5;">
                                ต้องการกู้คืนพนักงาน <strong style="color: #28a745;">"${employeeName}"</strong> กลับมาใช้งานหรือไม่?
                            </p>
                        </div>
                        
                        <div style="background: #e7f3ff; border: 1px solid #007acc; border-radius: 6px; padding: 12px; margin: 10px 0;">
                            <div style="color: #004a99;">
                                <strong>ผลลัพธ์การกู้คืน:</strong>
                                <ul style="margin: 5px 0 0 0; padding-left: 15px;">
                                    <li>ข้อมูลจะกลับมาใช้งานได้ปกติ</li>
                                    <li>สามารถเข้าสู่ระบบได้</li>
                                    <li>ประวัติการทำงานจะถูกเก็บไว้</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                `,
                icon: null,
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-undo me-2"></i>กู้คืนพนักงาน',
                cancelButtonText: '<i class="fas fa-times me-2"></i>ยกเลิก',
                reverseButtons: true,
                width: '500px'
            });

            if (result.isConfirmed) {
                showNotification(`กำลังกู้คืนพนักงาน "${employeeName}"...`, 'info', 8000);

                const response = await fetch(`/employees/trash/${employeeId}/restore`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    showNotification(data.message || 'กู้คืนพนักงานเรียบร้อยแล้ว', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification(data.message || 'ไม่สามารถกู้คืนได้', 'error');
                }
            }
        } catch (error) {
            console.error('⚠️ Restore error:', error);
            showNotification('เกิดข้อผิดพลาดในการกู้คืน', 'error');
        }
    }

    async function forceDeleteEmployee(employeeId, employeeName) {
        try {
            const result = await Swal.fire({
                title: '🚨 คำเตือนสูงสุด: การลบถาวร',
                html: `
                    <div style="text-align: left; color: #333;">
                        <div style="background: linear-gradient(135deg, #f8d7da, #f1b0b7); border: 1px solid #dc3545; border-radius: 8px; padding: 15px; margin-bottom: 15px;">
                            <div style="display: flex; align-items: center; margin-bottom: 10px;">
                                <i class="fas fa-fire" style="color: #dc3545; font-size: 24px; margin-right: 10px;"></i>
                                <strong style="color: #721c24; font-size: 16px;">การลบถาวร - ไม่สามารถกู้คืนได้</strong>
                            </div>
                            <p style="color: #333333; margin: 0; line-height: 1.5;">
                                ต้องการลบพนักงาน <strong style="color: #dc3545;">"${employeeName}"</strong> อย่างถาวรหรือไม่?
                            </p>
                        </div>
                        
                        <div style="background: #fff3cd; border: 1px solid #ffc107; border-radius: 6px; padding: 12px; margin: 10px 0;">
                            <div style="color: #856404;">
                                <strong>⚠️ คำเตือน:</strong>
                                <ul style="margin: 5px 0 0 0; padding-left: 15px;">
                                    <li>💀 <strong>ข้อมูลจะหายไปตลอดกาล</strong></li>
                                    <li>🚫 <strong>ไม่สามารถกู้คืนได้อีก</strong></li>
                                    <li>📸 <strong>รูปภาพและไฟล์แนบจะถูกลบ</strong></li>
                                    <li>📊 <strong>ประวัติการทำงานจะหายหมด</strong></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                `,
                icon: null,
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#28a745',
                confirmButtonText: '<i class="fas fa-fire me-2"></i>ลบถาวร',
                cancelButtonText: '<i class="fas fa-shield-alt me-2"></i>ยกเลิก (ปลอดภัย)',
                reverseButtons: true,
                width: '600px'
            });

            if (result.isConfirmed) {
                const confirmResult = await Swal.fire({
                    title: '⚠️ ยืนยันอีกครั้งสุดท้าย',
                    html: `
                        <div style="text-align: center; color: #333;">
                            <div style="background: linear-gradient(135deg, #fee2e2, #fecaca); border: 2px solid #ef4444; border-radius: 12px; padding: 20px; margin: 15px 0;">
                                <div style="font-size: 48px; margin-bottom: 15px;">💀</div>
                                <h3 style="color: #dc2626; margin-bottom: 15px;">การลบถาวร "${employeeName}"</h3>
                                <p style="color: #7f1d1d; font-weight: 600; line-height: 1.5;">
                                    ข้อมูลจะถูกลบออกจากระบบทันที<br>
                                    และไม่สามารถกู้คืนได้อีก
                                </p>
                            </div>
                        </div>
                    `,
                    icon: null,
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#10b981',
                    confirmButtonText: '<i class="fas fa-fire me-2"></i>ลบถาวรทันที',
                    cancelButtonText: '<i class="fas fa-shield-alt me-2"></i>ยกเลิก',
                    reverseButtons: true
                });

                if (confirmResult.isConfirmed) {
                    showNotification(`กำลังลบพนักงาน "${employeeName}" อย่างถาวร...`, 'error', 10000);

                    const response = await fetch(`/employees/trash/${employeeId}/force`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        showNotification(data.message || 'ลบถาวรเรียบร้อยแล้ว', 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showNotification(data.message || 'ไม่สามารถลบถาวรได้', 'error');
                    }
                }
            }
        } catch (error) {
            console.error('⚠️ Force delete error:', error);
            showNotification('เกิดข้อผิดพลาดในการลบถาวร', 'error');
        }
    }

    async function previewEmployee(employeeId) {
        try {
            const modal = new bootstrap.Modal(document.getElementById('employeePreviewModal'));
            const contentDiv = document.getElementById('employeePreviewContent');
            
            contentDiv.innerHTML = `
                <div class="text-center">
                    <div class="spinner-border text-danger" role="status">
                        <span class="visually-hidden">กำลังโหลด...</span>
                    </div>
                </div>
            `;
            
            modal.show();
            
            const trashedEmployee = @json($trashedEmployees->keyBy('id'));
            const employee = trashedEmployee[employeeId];
            
            if (employee) {
                contentDiv.innerHTML = `
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3 deleted-user-icon" style="width: 120px; height: 120px;">
                                <i class="fas fa-user-slash text-muted fa-4x"></i>
                            </div>
                            <span class="badge badge-danger-enhanced">ถูกลบแล้ว</span>
                        </div>
                        <div class="col-md-8">
                            <h6 class="fw-bold text-danger mb-3">
                                <i class="fas fa-trash me-2"></i>ข้อมูลพนักงานในถังขยะ
                            </h6>
                            <table class="table table-sm">
                                <tr>
                                    <td class="fw-bold">รหัสพนักงาน:</td>
                                    <td>${employee.employee_code}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">ชื่อ (ไทย):</td>
                                    <td>${employee.full_name_th}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">ชื่อ (อังกฤษ):</td>
                                    <td>${employee.full_name_en || 'ไม่ระบุ'}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">ชื่อเล่น:</td>
                                    <td>${employee.nickname || 'ไม่ระบุ'}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">อีเมล:</td>
                                    <td>${employee.email}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">เบอร์โทร:</td>
                                    <td>${employee.phone || 'ไม่ระบุ'}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">แผนก:</td>
                                    <td>${employee.department ? employee.department.name : 'ไม่ระบุ'}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">สาขา:</td>
                                    <td>${employee.branch ? employee.branch.name : 'ไม่ระบุ'}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">บทบาท:</td>
                                    <td>${employee.role}</td>
                                </tr>
                                <tr class="table-danger">
                                    <td class="fw-bold">วันที่ลบ:</td>
                                    <td class="text-danger fw-bold">${new Date(employee.deleted_at).toLocaleString('th-TH')}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                `;
            } else {
                throw new Error('ไม่พบข้อมูลพนักงาน');
            }
        } catch (error) {
            contentDiv.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    เกิดข้อผิดพลาดในการดูข้อมูล: ${error.message}
                </div>
            `;
        }
    }

    // ✅ Bulk restore functionality
    function openBulkRestoreModal() {
        const count = selectedTrashedEmployees.size;
        
        if (count === 0) {
            showNotification('กรุณาเลือกพนักงานที่ต้องการกู้คืนก่อน', 'warning');
            return;
        }
        
        const modal = new bootstrap.Modal(document.getElementById('bulkRestoreModal'));
        modal.show();
    }

    const executeBulkRestoreBtn = document.getElementById('executeBulkRestore');
    if (executeBulkRestoreBtn) {
        executeBulkRestoreBtn.addEventListener('click', async function() {
            const selected = Array.from(selectedTrashedEmployees);
            
            if (selected.length === 0) {
                showNotification('ไม่มีพนักงานที่เลือก', 'warning');
                return;
            }
            
            try {
                const result = await Swal.fire({
                    title: '🔄 ยืนยันการกู้คืนหลายรายการ',
                    html: `
                        <div style="text-align: center; color: #333;">
                            <div style="background: linear-gradient(135deg, #d4edda, #c3e6cb); border: 2px solid #28a745; border-radius: 12px; padding: 20px; margin: 15px 0;">
                                <div style="font-size: 48px; margin-bottom: 15px;">🔄</div>
                                <h3 style="color: #155724; margin-bottom: 15px;">กู้คืน ${selected.length} คน</h3>
                                <p style="color: #155724; font-weight: 600; line-height: 1.5;">
                                    ข้อมูลจะกลับมาใช้งานได้ปกติ<br>
                                    และสามารถเข้าสู่ระบบได้
                                </p>
                            </div>
                        </div>
                    `,
                    icon: null,
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: `<i class="fas fa-undo me-2"></i>กู้คืน ${selected.length} คน`,
                    cancelButtonText: 'ยกเลิก'
                });

                if (result.isConfirmed) {
                    this.disabled = true;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>กำลังกู้คืน...';
                    
                    let successCount = 0;
                    let failedCount = 0;
                    
                    for (const employeeId of selected) {
                        try {
                            const response = await fetch(`/employees/trash/${employeeId}/restore`, {
                                method: 'PATCH',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken
                                }
                            });
                            
                            const data = await response.json();
                            if (data.success) {
                                successCount++;
                            } else {
                                failedCount++;
                            }
                        } catch (error) {
                            failedCount++;
                        }
                    }
                    
                    if (successCount > 0) {
                        showNotification(`กู้คืนสำเร็จ ${successCount} คน${failedCount > 0 ? `, ล้มเหลว ${failedCount} คน` : ''}`, 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showNotification('ไม่สามารถกู้คืนได้', 'error');
                    }
                }
            } catch (error) {
                console.error('⚠️ Bulk restore error:', error);
                showNotification('เกิดข้อผิดพลาดในการกู้คืนหลายรายการ', 'error');
            } finally {
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-undo me-1"></i>กู้คืนที่เลือก';
                bootstrap.Modal.getInstance(document.getElementById('bulkRestoreModal'))?.hide();
            }
        });
    }

    // ✅ Empty trash functionality
    const emptyTrashBtn = document.getElementById('emptyTrashBtn');
    if (emptyTrashBtn) {
        emptyTrashBtn.addEventListener('click', async function() {
            const trashCount = {{ $trashedEmployees->count() }};
            
            try {
                const result = await Swal.fire({
                    title: '🚨 คำเตือนสูงสุด: ล้างถังขยะ',
                    html: `
                        <div style="text-align: left; color: #333;">
                            <div style="background: linear-gradient(135deg, #f8d7da, #f1b0b7); border: 2px solid #dc3545; border-radius: 12px; padding: 20px; margin: 15px 0;">
                                <div style="display: flex; align-items: center; margin-bottom: 15px;">
                                    <i class="fas fa-fire" style="color: #dc3545; font-size: 36px; margin-right: 15px;"></i>
                                    <div>
                                        <h3 style="color: #721c24; margin-bottom: 5px;">ล้างถังขยะทั้งหมด</h3>
                                        <p style="color: #721c24; margin: 0;">ลบพนักงาน ${trashCount} คนอย่างถาวร</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div style="background: #fff3cd; border: 1px solid #ffc107; border-radius: 8px; padding: 15px; margin: 15px 0;">
                                <div style="color: #856404;">
                                    <strong>⚠️ คำเตือนสำคัญ:</strong>
                                    <ul style="margin: 5px 0 0 0; padding-left: 15px;">
                                        <li>💀 <strong>ข้อมูลทั้งหมดจะหายไปตลอดกาล</strong></li>
                                        <li>🚫 <strong>ไม่สามารถกู้คืนได้อีก</strong></li>
                                        <li>📸 <strong>รูปภาพและไฟล์แนบจะถูกลบ</strong></li>
                                        <li>📊 <strong>ประวัติการทำงานจะหายหมด</strong></li>
                                        <li>🔗 <strong>ความเชื่อมโยงกับข้อมูลอื่นจะขาด</strong></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    `,
                    icon: null,
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#28a745',
                    confirmButtonText: '<i class="fas fa-fire me-2"></i>ล้างถังขยะ',
                    cancelButtonText: '<i class="fas fa-shield-alt me-2"></i>ยกเลิก (ปลอดภัย)',
                    reverseButtons: true,
                    width: '650px'
                });

                if (result.isConfirmed) {
                    const confirmResult = await Swal.fire({
                        title: '💀 ยืนยันครั้งสุดท้าย',
                        html: `
                            <div style="text-align: center; color: #333;">
                                <div style="background: linear-gradient(135deg, #fee2e2, #fecaca); border: 3px solid #ef4444; border-radius: 15px; padding: 25px; margin: 20px 0;">
                                    <div style="font-size: 64px; margin-bottom: 20px;">🔥</div>
                                    <h2 style="color: #dc2626; margin-bottom: 20px;">ล้างถังขยะ ${trashCount} คน</h2>
                                    <p style="color: #7f1d1d; font-weight: 700; font-size: 18px; line-height: 1.5;">
                                        การกระทำนี้ไม่สามารถยกเลิกได้!<br>
                                        ข้อมูลทั้งหมดจะหายไปตลอดกาล
                                    </p>
                                </div>
                            </div>
                        `,
                        icon: null,
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#10b981',
                        confirmButtonText: '<i class="fas fa-skull-crossbones me-2"></i>ลบทั้งหมดทันที',
                        cancelButtonText: '<i class="fas fa-shield-alt me-2"></i>ยกเลิก',
                        reverseButtons: true
                    });

                    if (confirmResult.isConfirmed) {
                        this.disabled = true;
                        this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>กำลังล้าง...';
                        
                        showNotification(`🔥 กำลังล้างถังขยะ ${trashCount} คน - กรุณารอสักครู่...`, 'error', 15000);
                        
                        const response = await fetch('/employees/trash/empty', {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            }
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            await Swal.fire({
                                title: '✅ ล้างถังขยะสำเร็จ',
                                html: `
                                    <div style="text-align: center; color: #333;">
                                        <div style="background: linear-gradient(135deg, #d4edda, #c3e6cb); border: 2px solid #28a745; border-radius: 12px; padding: 20px; margin: 15px 0;">
                                            <div style="font-size: 48px; margin-bottom: 15px;">✅</div>
                                            <h3 style="color: #155724; margin-bottom: 15px;">ล้างถังขยะเสร็จสิ้น</h3>
                                            <p style="color: #155724; font-weight: 600;">
                                                ลบข้อมูล ${trashCount} คนเรียบร้อยแล้ว
                                            </p>
                                        </div>
                                    </div>
                                `,
                                icon: 'success',
                                confirmButtonText: '<i class="fas fa-home me-2"></i>กลับสู่หน้าหลัก',
                                allowOutsideClick: false
                            });
                            
                            location.reload();
                        } else {
                            showNotification(data.message || 'ไม่สามารถล้างถังขยะได้', 'error');
                            this.disabled = false;
                            this.innerHTML = '<i class="fas fa-fire me-1"></i>ล้างถังขยะ';
                        }
                    }
                }
            } catch (error) {
                console.error('⚠️ Empty trash error:', error);
                showNotification('เกิดข้อผิดพลาดในการล้างถังขยะ', 'error');
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-fire me-1"></i>ล้างถังขยะ';
            }
        });
    }

    // ✅ Notification System - Enhanced fade-in effects
    function showNotification(message, type = 'info', duration = 4000) {
        try {
            const typeConfig = {
                success: { 
                    class: 'alert-success', 
                    icon: 'fa-check-circle', 
                    title: 'สำเร็จ!',
                    color: '#1E7E34'
                },
                error: { 
                    class: 'alert-danger', 
                    icon: 'fa-exclamation-triangle', 
                    title: 'ข้อผิดพลาด!',
                    color: '#C82333'
                },
                warning: { 
                    class: 'alert-warning', 
                    icon: 'fa-exclamation-triangle', 
                    title: 'คำเตือน!',
                    color: '#E0A800'
                },
                info: { 
                    class: 'alert-info', 
                    icon: 'fa-info-circle', 
                    title: 'ข้อมูล!',
                    color: '#138496'
                }
            };
            
            const config = typeConfig[type];
            const notification = document.createElement('div');
            notification.className = `alert ${config.class} alert-dismissible fade show position-fixed notification-toast fade-in-slide-right`;
            notification.style.cssText = `
                top: 20px; 
                right: 20px; 
                z-index: 9999; 
                min-width: 350px; 
                max-width: 500px;
                border-radius: 12px;
                box-shadow: 0 8px 25px rgba(181, 69, 68, 0.15);
                font-size: 0.9rem;
                transform: translateX(100%);
                opacity: 0;
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            `;
            notification.innerHTML = `
                <div class="d-flex align-items-center">
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: ${config.color}; 
                                display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                        <i class="fas ${config.icon} text-white"></i>
                    </div>
                    <div>
                        <strong style="font-size: 1rem;">${config.title}</strong><br>
                        <small>${message}</small>
                    </div>
                    <button type="button" class="btn-close btn-sm ms-2" onclick="this.parentElement.parentElement.remove()"></button>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            // Trigger fade-in animation
            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
                notification.style.opacity = '1';
            }, 100);
            
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.style.transform = 'translateX(100%)';
                    notification.style.opacity = '0';
                    setTimeout(() => notification.remove(), 400);
                }
            }, duration);
        } catch (error) {
            console.warn('Error showing notification:', error);
            alert(message);
        }
    }

    // ✅ Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl+A - Select all visible items
        if (e.ctrlKey && e.key === 'a' && e.target.tagName !== 'INPUT') {
            e.preventDefault();
            const selectAllCheckbox = document.getElementById('selectAllTrashed');
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = !selectAllCheckbox.checked;
                selectAllCheckbox.dispatchEvent(new Event('change'));
            }
        }
        
        // Escape - Clear filters and deselect
        if (e.key === 'Escape') {
            if (searchInput && searchInput.value) {
                searchInput.value = '';
                searchInput.dispatchEvent(new Event('input'));
            }
            
            if (deletedDateFilter && deletedDateFilter.value) {
                deletedDateFilter.value = '';
                deletedDateFilter.dispatchEvent(new Event('change'));
            }
            
            const selectAllCheckbox = document.getElementById('selectAllTrashed');
            if (selectAllCheckbox && selectAllCheckbox.checked) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.dispatchEvent(new Event('change'));
            }
        }
        
        // Ctrl+R - Bulk restore
        if (e.ctrlKey && e.key === 'r') {
            e.preventDefault();
            openBulkRestoreModal();
        }
    });

    // ✅ Enhanced table interactions
    document.querySelectorAll('#trashedEmployeesTable tbody tr, .employee-mobile-card-enhanced').forEach(row => {
        // Double click to preview
        row.addEventListener('dblclick', function() {
            const previewBtn = this.querySelector('.preview-btn');
            if (previewBtn) {
                previewBtn.click();
            }
        });
        
        // Right click context menu
        row.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            const checkbox = this.querySelector('.trashed-employee-checkbox');
            
            if (checkbox && !checkbox.checked) {
                checkbox.checked = true;
                checkbox.dispatchEvent(new Event('change'));
            }
            
            // Show context effect with fade
            this.style.transition = 'all 0.3s ease';
            this.style.backgroundColor = 'rgba(181, 69, 68, 0.1)';
            this.style.transform = 'scale(1.02)';
            setTimeout(() => {
                this.style.backgroundColor = '';
                this.style.transform = '';
            }, 1000);
        });
    });

    // ✅ Touch-friendly interactions for mobile
    if (window.innerWidth <= 768) {
        document.querySelectorAll('.trash-stat-card, .employee-mobile-card-enhanced').forEach(card => {
            card.addEventListener('touchstart', function() {
                this.classList.add('touch-active');
            });
            
            card.addEventListener('touchend', function() {
                this.classList.remove('touch-active');
            });
        });
    }

    // ✅ Progressive loading enhancement
    function addProgressiveLoadingEffect() {
        const elements = document.querySelectorAll('.fade-in-element:not(.visible)');
        elements.forEach((element, index) => {
            setTimeout(() => {
                if (!element.classList.contains('visible')) {
                    element.classList.add('visible');
                }
            }, index * 50);
        });
    }

    // ✅ Initialize
    setTimeout(() => {
        updateVisibleTrashCount();
        addProgressiveLoadingEffect();
    }, 200);

    // ✅ Reset and re-animate filtered results
setTimeout(() => {
    resetFilterAnimations();
}, 100);

// ✅ Reset Filter Animations Function (เพิ่มหลัง updateVisibleTrashCount)
function resetFilterAnimations() {
    // Reset table rows
    const visibleRows = document.querySelectorAll('#trashedEmployeesTable tbody tr[style*="display: none"], #trashedEmployeesTable tbody tr:not([style*="display: none"])');
    visibleRows.forEach((row, index) => {
        if (row.style.display !== 'none') {
            row.style.opacity = '0';
            row.style.transform = 'translateY(10px)';
            setTimeout(() => {
                row.style.opacity = '1';
                row.style.transform = 'translateY(0)';
            }, index * 30);
        }
    });
    
    // Reset mobile cards
    const visibleCards = document.querySelectorAll('.employee-mobile-card-enhanced');
    visibleCards.forEach((card, index) => {
        if (card.style.display !== 'none') {
            card.style.opacity = '0';
            card.style.transform = 'translateX(-20px)';
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateX(0)';
            }, index * 40);
        }
    });
}

    // Focus search input with enhanced effect
if (searchInput) {
    setTimeout(() => {
        try {
            searchInput.focus();
            searchInput.style.boxShadow = '0 0 0 0.2rem rgba(181, 69, 68, 0.25)';
            searchInput.style.borderColor = 'var(--primary-red)';
            searchInput.style.transform = 'scale(1.02)';
            
            setTimeout(() => {
                searchInput.style.boxShadow = '';
                searchInput.style.borderColor = '';
                searchInput.style.transform = '';
            }, 2000);
        } catch (e) {
            console.log('Search input focus failed:', e);
        }
    }, 800);
}
    
    // ✅ Performance monitoring and cleanup
    const startTime = performance.now();
    
    window.addEventListener('beforeunload', function() {
        // Cleanup animations
        document.querySelectorAll('.fade-in-element, .fade-in-scale, .fade-in-slide-left, .fade-in-slide-right, .fade-in-bounce, .fade-in-rotate').forEach(element => {
            element.style.transition = 'none';
        });
    });
    
    // ✅ Final initialization log
    setTimeout(() => {
        const loadTime = performance.now() - startTime;
        console.log('🗑️ Enhanced Trash Management System Ready!');
        console.log('✅ Features: Enhanced Fade-in Effects | Permission System | Bulk Operations');
        console.log(`📊 Total trashed employees: {{ $trashedEmployees->count() }}`);
        console.log('🎨 Theme: ITMS Red-Orange | Enhanced fade-in animations');
        console.log('🔄 Refresh Button: Smooth fade-in effect');
        console.log(`⚡ Initialization completed in ${Math.round(loadTime)}ms`);
    }, 1000);

    // ✅ Add global function for modal opening
    window.openBulkRestoreModal = openBulkRestoreModal;
});

// ✅ Enhanced touch-active style for mobile
const mobileStyle = document.createElement('style');
mobileStyle.textContent = `
    .touch-active {
        transform: scale(0.98) translateY(-2px) !important;
        transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
        box-shadow: 0 8px 25px rgba(181, 69, 68, 0.2) !important;
    }
    
    /* Enhanced loading states */
    .btn:disabled {
        opacity: 0.7;
        transform: none !important;
    }
    
    /* Smooth focus transitions */
    .form-control:focus,
    .form-select:focus {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    /* Enhanced hover states */
    .action-btn:hover {
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
`;
document.head.appendChild(mobileStyle);
</script>
@endpush
