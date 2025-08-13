@extends('layouts.app')

@section('title', 'จัดการสาขา')

@push('styles')
<style>
/* Branch Management Specific Styles - ITMS Red-Orange Theme */
:root {
    --primary-red: #B54544;
    --primary-orange: #E6952A;
    --accent-red: #A33E3D;
    --accent-orange: #CC7F1F;
    --text-primary: #1A252F;
    --text-secondary: #5A6C7D;
}

/* ✅ แก้ไข: ปรับหัวข้อให้มองเห็นชัด + เพิ่ม Icon สวยงาม */
.branch-header {
    background: linear-gradient(135deg, var(--primary-red) 0%, var(--primary-orange) 100%);
    border-radius: 12px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 32px rgba(181, 69, 68, 0.4);
}

.branch-header h1 {
    color: #FFFFFF !important;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
    font-weight: 800 !important;
    font-size: 2.2rem !important;
}

.branch-header p {
    color: #FFFFFF !important;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
    opacity: 0.95 !important;
}

/* ✅ เพิ่ม: Header Icon Container สวยงาม */
.header-icon-container {
    position: relative;
    background: rgba(255, 255, 255, 0.25) !important;
    backdrop-filter: blur(10px);
    border: 2px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.header-icon-container:hover {
    transform: scale(1.05);
    background: rgba(255, 255, 255, 0.35) !important;
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.2);
}

.header-icon-container i {
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
}

.branch-header::before {
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

.branch-stat-card {
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    cursor: pointer;
    border: 2px solid rgba(181, 69, 68, 0.1);
    border-radius: 12px;
}

.branch-stat-card:hover {
    box-shadow: 0 12px 35px rgba(181, 69, 68, 0.25);
    transform: translateY(-5px) scale(1.02);
    border-color: rgba(181, 69, 68, 0.3);
}

/* ✅ แก้ไข: ปรับการจัดวางข้อมูลในการ์ดสาขา */
.branch-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
    border-radius: 15px;
    border: 2px solid rgba(181, 69, 68, 0.1);
    box-shadow: 0 8px 25px rgba(181, 69, 68, 0.15);
}

.branch-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 50px rgba(181, 69, 68, 0.25);
    border-color: rgba(181, 69, 68, 0.3);
}

.branch-card-header-active {
    background: linear-gradient(135deg, var(--primary-red) 0%, var(--primary-orange) 100%);
    padding: 20px;
    position: relative;
}

.branch-card-header-active::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="2" fill="white" fill-opacity="0.1"/><circle cx="80" cy="80" r="2" fill="white" fill-opacity="0.1"/></svg>');
    pointer-events: none;
}

.branch-card-header-inactive {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    padding: 20px;
}

/* ✅ ปรับข้อมูลการติดต่อให้ดูดีขึ้น */
.contact-info {
    background: linear-gradient(135deg, rgba(181, 69, 68, 0.05), rgba(230, 149, 42, 0.05));
    border-radius: 12px;
    padding: 15px;
    margin-bottom: 15px;
    border: 1px solid rgba(181, 69, 68, 0.1);
}

.contact-item {
    display: flex;
    align-items: center;
    margin-bottom: 8px;
    transition: all 0.2s ease;
}

.contact-item:hover {
    transform: translateX(5px);
}

.contact-item:last-child {
    margin-bottom: 0;
}

.contact-icon {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-red), var(--primary-orange));
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    margin-right: 12px;
    font-size: 0.9rem;
    box-shadow: 0 4px 12px rgba(181, 69, 68, 0.3);
}

/* ✅ ปรับ Employee Count ให้สวยขึ้น */
.employee-stats {
    background: linear-gradient(135deg, rgba(181, 69, 68, 0.1), rgba(230, 149, 42, 0.1));
    border-radius: 12px;
    padding: 20px;
    border-top: 3px solid var(--primary-orange);
    margin-top: 15px;
}

.stat-item {
    text-align: center;
    position: relative;
}

.stat-number {
    font-size: 2rem;
    font-weight: 800;
    line-height: 1;
    margin-bottom: 5px;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.stat-label {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* ✅ แก้ไข: ปรับ Card Footer ให้มินิมอล */
.card-footer {
    background: linear-gradient(135deg, rgba(248, 249, 250, 0.9), rgba(233, 236, 239, 0.9));
    border-top: 1px solid rgba(181, 69, 68, 0.1);
    padding: 15px;
}

.action-buttons {
    /* ลบ margin-bottom เพราะไม่ใช้แล้ว */
}

.toggle-status-btn {
    /* ลบ styles เพราะไม่ใช้แล้ว */
}

.avatar-sm {
    width: 40px;
    height: 40px;
    font-size: 0.875rem;
    background: linear-gradient(135deg, var(--primary-red), var(--accent-red));
}

.btn-primary-red {
    background: linear-gradient(135deg, var(--primary-red), var(--accent-red));
    border: none;
    color: white;
    font-weight: 600;
    position: relative;
    overflow: hidden;
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
    transform: translateY(-1px);
    box-shadow: 0 4px 15px rgba(181, 69, 68, 0.3);
}

.btn-primary-red:hover::before {
    left: 100%;
}

.search-card {
    border: 2px solid rgba(181, 69, 68, 0.1);
    border-radius: 12px;
    transition: all 0.3s ease;
}

.search-card:hover {
    border-color: rgba(181, 69, 68, 0.2);
    box-shadow: 0 8px 25px rgba(181, 69, 68, 0.1);
}

.input-group-text {
    background: linear-gradient(135deg, rgba(181, 69, 68, 0.1), rgba(230, 149, 42, 0.1));
    border-color: rgba(181, 69, 68, 0.2);
    color: var(--primary-red);
}

.form-control:focus {
    border-color: var(--primary-red);
    box-shadow: 0 0 0 0.2rem rgba(181, 69, 68, 0.15);
}

.btn-outline-info:hover,
.btn-outline-warning:hover,
.btn-outline-danger:hover,
.btn-outline-success:hover,
.btn-outline-secondary:hover {
    transform: scale(1.05);
    transition: all 0.2s ease;
}

.empty-state {
    background: linear-gradient(135deg, rgba(181, 69, 68, 0.05), rgba(230, 149, 42, 0.05));
    border: 2px solid rgba(181, 69, 68, 0.1);
    border-radius: 15px;
}

/* ✅ แก้ไข: สร้าง Pagination ใหม่ที่ควบคุมได้ 100% */
.pagination-container {
    background: linear-gradient(135deg, rgba(248, 249, 250, 0.95), rgba(255, 255, 255, 0.9));
    border: 1px solid rgba(181, 69, 68, 0.12);
    border-radius: 12px;
    padding: 20px 25px;
    box-shadow: 0 4px 15px rgba(181, 69, 68, 0.08);
    backdrop-filter: blur(5px);
}

.pagination-wrapper {
    display: grid;
    grid-template-columns: 1fr auto;
    align-items: center;
    gap: 20px;
    width: 100%;
}

.pagination-info {
    display: flex;
    align-items: center;
    font-size: 0.9rem;
    color: var(--text-secondary);
    font-weight: 500;
    margin: 0;
    justify-self: start;
}

.pagination-info i {
    color: var(--primary-orange);
    margin-right: 8px;
    font-size: 0.85rem;
}

.pagination-controls {
    justify-self: end;
}

/* ✅ สร้าง Custom Pagination ที่ควบคุมได้ 100% */
.custom-pagination {
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0;
    padding: 0;
    list-style: none;
}

.custom-pagination .page-item {
    margin: 0;
    padding: 0;
}

.custom-pagination .page-link {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 42px;
    height: 42px;
    border-radius: 8px;
    border: 1px solid rgba(181, 69, 68, 0.2);
    background: white;
    color: var(--primary-red);
    font-weight: 600;
    font-size: 0.9rem;
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 2px 5px rgba(181, 69, 68, 0.08);
    position: relative;
    overflow: hidden;
}

.custom-pagination .page-link:hover {
    background: var(--primary-red);
    border-color: var(--primary-red);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(181, 69, 68, 0.25);
}

.custom-pagination .page-item.active .page-link {
    background: var(--primary-red);
    border-color: var(--primary-red);
    color: white;
    box-shadow: 0 4px 12px rgba(181, 69, 68, 0.3);
    transform: translateY(-1px);
}

.custom-pagination .page-item.disabled .page-link {
    background: #f8f9fa;
    border-color: #dee2e6;
    color: #6c757d;
    box-shadow: none;
    cursor: not-allowed;
    transform: none;
}

.custom-pagination .page-item.disabled .page-link:hover {
    background: #f8f9fa;
    border-color: #dee2e6;
    color: #6c757d;
    transform: none;
}

/* Previous/Next ปุ่มพิเศษ */
.custom-pagination .page-item.prev .page-link,
.custom-pagination .page-item.next .page-link {
    width: 48px;
    background: linear-gradient(135deg, rgba(181, 69, 68, 0.08), rgba(230, 149, 42, 0.08));
    border: 2px solid rgba(181, 69, 68, 0.15);
    font-weight: 700;
}

.custom-pagination .page-item.prev .page-link:hover,
.custom-pagination .page-item.next .page-link:hover {
    background: linear-gradient(135deg, var(--primary-red), var(--primary-orange));
    border-color: var(--primary-red);
    color: white;
}

/* ซ่อน Laravel pagination เดิมทั้งหมด */
.pagination-controls .pagination {
    display: none !important;
}

.pagination-controls nav {
    display: none !important;
}

.pagination-controls .pagination-links {
    display: none !important;
}

.card {
    border: none;
    border-radius: 12px;
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(181, 69, 68, 0.1);
}

/* Status indicators with ITMS theme */
.status-indicator {
    position: relative;
    font-weight: 600;
}

/* ✅ แก้ไข: เปลี่ยน online status ให้เป็นโทน ITMS แทนสีเขียว */
.status-indicator.online::after {
    content: '';
    position: absolute;
    top: 50%;
    right: -15px;
    transform: translateY(-50%);
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: var(--primary-orange);
    animation: pulse-orange 2s infinite;
}

.status-indicator.offline::after {
    content: '';
    position: absolute;
    top: 50%;
    right: -15px;
    transform: translateY(-50%);
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #6c757d;
    animation: pulse-secondary 2s infinite;
}

/* ✅ แก้ไข: เปลี่ยน animation ให้เป็นสีส้มแทนสีเขียว */
@keyframes pulse-orange {
    0% {
        transform: translateY(-50%) scale(1);
        box-shadow: 0 0 0 0 rgba(230, 149, 42, 0.7);
    }
    70% {
        transform: translateY(-50%) scale(1.3);
        box-shadow: 0 0 0 8px rgba(230, 149, 42, 0);
    }
    100% {
        transform: translateY(-50%) scale(1);
        box-shadow: 0 0 0 0 rgba(230, 149, 42, 0);
    }
}

@keyframes pulse-secondary {
    0% {
        transform: translateY(-50%) scale(1);
        opacity: 1;
    }
    50% {
        transform: translateY(-50%) scale(1.2);
        opacity: 0.5;
    }
    100% {
        transform: translateY(-50%) scale(1);
        opacity: 1;
    }
}

/* Mobile optimizations */
@media (max-width: 768px) {
    .branch-card:hover {
        transform: translateY(-3px);
    }
    
    .branch-stat-card:hover {
        transform: translateY(-2px) scale(1.01);
    }
    
    /* ✅ Mobile header adjustment */
    .branch-header {
        flex-direction: column !important;
        text-align: center;
    }
    
    .branch-header .btn {
        margin-top: 15px;
    }
    
    .header-icon-container {
        margin-bottom: 15px !important;
    }
    
    /* ✅ Mobile pagination - Custom Layout */
    .pagination-container {
        padding: 15px 18px;
    }
    
    .pagination-wrapper {
        grid-template-columns: 1fr;
        gap: 15px;
        text-align: center;
    }
    
    .pagination-info {
        justify-self: center;
        text-align: center;
        font-size: 0.85rem;
        order: 2;
    }
    
    .pagination-controls {
        justify-self: center;
        order: 1;
    }
    
    .custom-pagination {
        justify-content: center;
        flex-wrap: wrap;
        gap: 6px;
    }
    
    .custom-pagination .page-link {
        width: 38px;
        height: 38px;
        font-size: 0.85rem;
    }
    
    .custom-pagination .page-item.prev .page-link,
    .custom-pagination .page-item.next .page-link {
        width: 44px;
    }
    
    /* ✅ Mobile branch card actions */
    .card-footer .row {
        margin: 0 -3px;
    }
    
    .card-footer .col-3 {
        padding: 0 3px;
    }
    
    .card-footer .btn-sm {
        padding: 8px 6px;
        font-size: 0.8rem;
    }
    
    /* ✅ Mobile contact info */
    .contact-info {
        padding: 12px;
    }
    
    .contact-icon {
        width: 30px;
        height: 30px;
        font-size: 0.8rem;
    }
    
    /* ✅ Mobile employee stats */
    .employee-stats {
        padding: 15px;
    }
    
    .stat-number {
        font-size: 1.6rem;
    }
    
    .stat-label {
        font-size: 0.8rem;
    }
}

/* ✅ เพิ่ม responsive ขนาดเล็กมาก */
@media (max-width: 576px) {
    .branch-stat-card .card-body {
        padding: 20px 15px;
    }
    
    .branch-stat-card h3 {
        font-size: 1.8rem;
    }
    
    .search-card .card-body {
        padding: 15px;
    }
    
    .search-card .row {
        margin: 0;
    }
    
    .search-card .col-md-6,
    .search-card .col-md-3 {
        padding: 5px;
        margin-bottom: 10px;
    }
    
    /* ✅ Very small mobile pagination */
    .pagination-container {
        padding: 12px 15px;
        border-radius: 10px;
    }
    
    .pagination-wrapper {
        gap: 12px;
    }
    
    .custom-pagination .page-link {
        width: 32px;
        height: 32px;
        font-size: 0.8rem;
    }
    
    .custom-pagination .page-item.prev .page-link,
    .custom-pagination .page-item.next .page-link {
        width: 38px;
    }
    
    .pagination-info {
        font-size: 0.8rem;
    }
    
    .header-icon-container {
        padding: 12px !important;
    }
    
    .header-icon-container i {
        font-size: 1.5rem !important;
    }
    
    .branch-header h1 {
        font-size: 1.8rem !important;
    }
}

/* Notification enhancement */
.notification-toast {
    border-left: 5px solid var(--primary-red);
    border-radius: 8px;
    box-shadow: 0 8px 25px rgba(181, 69, 68, 0.15);
}

/* ✅ แก้ไข: ปรับปุ่มสถานะให้ชัดขึ้น */
.badge-itms-active {
    background: linear-gradient(135deg, var(--primary-orange), var(--accent-orange));
    color: #FFFFFF !important;
    animation: glow-orange 2s infinite alternate;
    font-weight: 800;
    font-size: 0.9rem;
    padding: 10px 18px;
    border-radius: 25px;
    text-transform: uppercase;
    letter-spacing: 1.2px;
    box-shadow: 0 4px 15px rgba(230, 149, 42, 0.4);
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
    border: 2px solid rgba(255, 255, 255, 0.3);
}

.badge-itms-secondary {
    background: linear-gradient(135deg, #6c757d, #495057);
    color: #FFFFFF !important;
    font-weight: 800;
    font-size: 0.9rem;
    padding: 10px 18px;
    border-radius: 25px;
    text-transform: uppercase;
    letter-spacing: 1.2px;
    box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
    border: 2px solid rgba(255, 255, 255, 0.2);
}

@keyframes glow-orange {
    from { 
        box-shadow: 0 4px 15px rgba(230, 149, 42, 0.4);
        transform: scale(1);
    }
    to { 
        box-shadow: 0 6px 25px rgba(230, 149, 42, 0.7);
        transform: scale(1.05);
    }
}

/* ✅ แก้ไข: ปรับปุ่มควบคุมให้เด่นขึ้น */
.btn-outline-info {
    border: 2px solid #17a2b8;
    color: #17a2b8;
    font-weight: 700;
    border-radius: 10px;
    transition: all 0.3s ease;
}

.btn-outline-info:hover {
    background: #17a2b8;
    border-color: #17a2b8;
    color: white;
    transform: scale(1.1);
    box-shadow: 0 6px 20px rgba(23, 162, 184, 0.4);
}

.btn-outline-warning {
    border: 2px solid #ffc107;
    color: #856404;
    font-weight: 700;
    border-radius: 10px;
    transition: all 0.3s ease;
}

.btn-outline-warning:hover {
    background: #ffc107;
    border-color: #ffc107;
    color: #212529;
    transform: scale(1.1);
    box-shadow: 0 6px 20px rgba(255, 193, 7, 0.4);
}

.btn-outline-danger {
    border: 2px solid #dc3545;
    color: #dc3545;
    font-weight: 700;
    border-radius: 10px;
    transition: all 0.3s ease;
}

.btn-outline-danger:hover {
    background: #dc3545;
    border-color: #dc3545;
    color: white;
    transform: scale(1.1);
    box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4);
}

.btn-outline-secondary {
    border: 2px solid #6c757d;
    color: #6c757d;
    font-weight: 700;
    border-radius: 10px;
    transition: all 0.3s ease;
}

.btn-outline-secondary:hover {
    background: #6c757d;
    border-color: #6c757d;
    color: white;
    transform: scale(1.05);
    box-shadow: 0 6px 20px rgba(108, 117, 125, 0.4);
}

/* ✅ ปุ่มเปิด/ปิดสาขาให้เด่นมาก */
.btn-primary-red {
    background: linear-gradient(135deg, var(--primary-red), var(--accent-red));
    border: none;
    color: white;
    font-weight: 700;
    position: relative;
    overflow: hidden;
    border-radius: 10px;
    padding: 10px 20px;
    text-transform: uppercase;
    letter-spacing: 1px;
    box-shadow: 0 6px 20px rgba(181, 69, 68, 0.4);
    transition: all 0.3s ease;
}

.btn-primary-red:hover {
    background: linear-gradient(135deg, var(--accent-red), var(--primary-red));
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(181, 69, 68, 0.5);
    color: white;
}

/* ✅ เพิ่ม: ITMS Theme Badge Colors */
.badge-itms-primary {
    background: linear-gradient(135deg, var(--primary-red), var(--primary-orange));
    color: white;
}

/* ✅ เพิ่ม: Text utilities */
.text-primary-red {
    color: var(--primary-red) !important;
    font-weight: 600;
}

/* ✅ ป้องกันการซ้อนทับของ pagination */
.pagination-controls .pagination {
    flex-wrap: nowrap;
    white-space: nowrap;
}

/* ซ่อนข้อความซ้ำที่อาจมาจาก Laravel pagination view */
.pagination .sr-only,
.pagination .page-item .page-link .sr-only {
    display: none !important;
}

/* จัดการ pagination text ที่อาจซ้ำ */
.pagination-controls *[aria-label="pagination"] {
    display: none;
}

/* ให้แน่ใจว่า pagination ไม่มีข้อความพิเศษ */
.pagination .page-item .page-link[aria-label]:not([aria-label=""]):after {
    content: none;
}

/* ปรับ layout ให้ชัดเจน */
.pagination-container > .d-flex {
    min-height: 50px;
    align-items: center;
}

@media (max-width: 768px) {
    .pagination-container > .d-flex {
        min-height: auto;
    }
    
    .pagination-controls .pagination {
        flex-wrap: wrap;
        white-space: normal;
    }
}

/* ✅ เพิ่ม: Employee count styling ให้เป็นโทน ITMS */
.employee-count-primary {
    color: var(--primary-red) !important;
    font-weight: 700;
}

.employee-count-active {
    color: var(--primary-orange) !important;
    font-weight: 700;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header Section with ITMS Theme - Enhanced with Icon -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center p-4 rounded-3 text-white branch-header">
                <div class="d-flex align-items-center">
                    <div class="bg-white bg-opacity-20 rounded-circle p-3 me-3 header-icon-container">
                        <i class="fas fa-building fa-2x text-white"></i>
                    </div>
                    <div>
                        <h1 class="mb-1 font-weight-bold">จัดการสาขา</h1>
                        <p class="mb-0 opacity-90">บริหารจัดการสาขาทั้งหมดของบริษัท</p>
                    </div>
                </div>
                <a href="{{ route('branches.create') }}" class="btn btn-light btn-lg shadow-sm font-weight-bold">
                    <i class="fas fa-plus me-2"></i>เพิ่มสาขาใหม่
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards with ITMS Theme -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm branch-stat-card h-100">
                <div class="card-body text-center">
                    <div class="text-primary mb-3">
                        <i class="fas fa-building fa-2x" style="color: var(--primary-red);"></i>
                    </div>
                    <h3 class="font-weight-bold" style="color: var(--primary-red);">{{ $branches->total() }}</h3>
                    <p class="text-muted mb-0 font-weight-500">สาขาทั้งหมด</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm branch-stat-card h-100">
                <div class="card-body text-center">
                    <div class="mb-3" style="color: var(--primary-orange);">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                    <h3 class="font-weight-bold" style="color: var(--primary-orange);">{{ $branches->where('is_active', true)->count() }}</h3>
                    <p class="text-muted mb-0 font-weight-500">สาขาที่เปิดใช้งาน</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm branch-stat-card h-100">
                <div class="card-body text-center">
                    <div class="mb-3" style="color: #6c757d;">
                        <i class="fas fa-pause-circle fa-2x"></i>
                    </div>
                    <h3 class="font-weight-bold" style="color: #6c757d;">{{ $branches->where('is_active', false)->count() }}</h3>
                    <p class="text-muted mb-0 font-weight-500">สาขาที่ปิดชั่วคราว</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm branch-stat-card h-100">
                <div class="card-body text-center">
                    <div class="mb-3" style="color: var(--accent-red);">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                    <h3 class="font-weight-bold" style="color: var(--accent-red);">{{ $branches->sum(function($branch) { return $branch->employees->count(); }) }}</h3>
                    <p class="text-muted mb-0 font-weight-500">พนักงานทั้งหมด</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm search-card">
                <div class="card-body">
                    <form method="GET" action="{{ route('branches.index') }}" class="row align-items-end">
                        <div class="col-md-6">
                            <label for="search" class="form-label font-weight-bold" style="color: var(--text-primary);">ค้นหาสาขา</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text border-0">
                                        <i class="fas fa-search"></i>
                                    </span>
                                </div>
                                <input type="text" 
                                       class="form-control border-left-0" 
                                       id="search" 
                                       name="search" 
                                       value="{{ request('search') }}" 
                                       placeholder="ค้นหาจากชื่อ, รหัส, หรือที่อยู่">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="status" class="form-label font-weight-bold" style="color: var(--text-primary);">สถานะ</label>
                            <select name="status" class="form-control">
                                <option value="">ทั้งหมด</option>
                                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>เปิดใช้งาน</option>
                                <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>ปิดชั่วคราว</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary-red btn-block">
                                <i class="fas fa-search mr-2"></i>ค้นหา
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Branches Grid -->
    <div class="row">
        @forelse($branches as $branch)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100 branch-card">
                    <!-- Card Header -->
                    <div class="card-header border-0 {{ $branch->is_active ? 'branch-card-header-active' : 'branch-card-header-inactive' }} text-white">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h5 class="mb-0 font-weight-bold">{{ $branch->name }}</h5>
                                <small style="opacity: 0.9;">
                                    <i class="mb-0"></i>{{ $branch->code }}
                                </small>
                            </div>
                            <div class="col-4 text-right">
                                <span class="badge {{ $branch->is_active ? 'badge-itms-active' : 'badge-itms-secondary' }} badge-pill font-weight-bold status-indicator {{ $branch->is_active ? 'online' : 'offline' }}">
                                    {{ $branch->is_active ? 'เปิด' : 'ปิด' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body">
                        <!-- Manager Info -->
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <div class="avatar-sm rounded-circle d-flex align-items-center justify-content-center mr-2 text-white">
                                    <i class="fas fa-user-tie"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">ผู้จัดการ</small>
                                    <strong style="color: var(--text-primary);">{{ $branch->manager ? $branch->manager->name : 'ไม่ระบุ' }}</strong>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Info -->
                        <div class="contact-info">
                            @if($branch->phone)
                                <div class="contact-item">
                                    <div class="contact-icon">
                                        <i class="fas fa-phone"></i>
                                    </div>
                                    <small class="text-muted">{{ $branch->phone }}</small>
                                </div>
                            @endif

                            @if($branch->email)
                                <div class="contact-item">
                                    <div class="contact-icon">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <small class="text-muted">{{ $branch->email }}</small>
                                </div>
                            @endif

                            @if($branch->address)
                                <div class="contact-item">
                                    <div class="contact-icon">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    <small class="text-muted">{{ Str::limit($branch->address, 45) }}</small>
                                </div>
                            @endif
                        </div>

                        <!-- Employee Count -->
                        <div class="employee-stats">
                            <div class="row">
                                <div class="col-6">
                                    <div class="stat-item">
                                        <div class="stat-number employee-count-primary">{{ $branch->employees->count() }}</div>
                                        <div class="stat-label">พนักงาน</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stat-item">
                                        <div class="stat-number employee-count-active">{{ $branch->employees->where('is_active', true)->count() }}</div>
                                        <div class="stat-label">ทำงานอยู่</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Footer - Minimal Single Row Design -->
                    <div class="card-footer border-0">
                        <div class="row g-2">
                            <div class="col-3">
                                <a href="{{ route('branches.show', $branch) }}" 
                                   class="btn btn-outline-info btn-sm w-100"
                                   title="ดูรายละเอียด">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                            <div class="col-3">
                                <a href="{{ route('branches.edit', $branch) }}" 
                                   class="btn btn-outline-warning btn-sm w-100"
                                   title="แก้ไข">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                            <div class="col-3">
                                <form action="{{ route('branches.destroy', $branch) }}" 
                                      method="POST" 
                                      class="d-inline delete-form w-100">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-outline-danger btn-sm w-100"
                                            title="ลบ">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                            <div class="col-3">
                                <form action="{{ route('branches.toggle-status', $branch) }}" method="POST" class="d-inline w-100">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                            class="btn btn-{{ $branch->is_active ? 'outline-secondary' : 'primary-red' }} btn-sm w-100"
                                            title="{{ $branch->is_active ? 'ปิดสาขา' : 'เปิดสาขา' }}">
                                        <i class="fas fa-{{ $branch->is_active ? 'times' : 'check' }}"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm empty-state">
                    <div class="card-body text-center py-5">
                        <div class="text-muted mb-3">
                            <i class="fas fa-building fa-3x" style="color: var(--primary-red); opacity: 0.5;"></i>
                        </div>
                        <h4 class="text-muted">ไม่พบข้อมูลสาขา</h4>
                        <p class="text-muted mb-4">ยังไม่มีสาขาในระบบ หรือไม่พบผลการค้นหา</p>
                        <a href="{{ route('branches.create') }}" class="btn btn-primary-red">
                            <i class="fas fa-plus mr-2"></i>เพิ่มสาขาแรก
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination - Custom Clean Design (No Laravel Conflicts) -->
    @if($branches->hasPages())
        <div class="row mt-4">
            <div class="col-12">
                <div class="pagination-container">
                    <div class="pagination-wrapper">
                        <!-- Left: Information -->
                        <div class="pagination-info">
                            <i class="fas fa-info-circle"></i>
                            แสดง <strong class="text-primary-red">{{ $branches->firstItem() }}</strong> ถึง 
                            <strong class="text-primary-red">{{ $branches->lastItem() }}</strong> 
                            จาก <strong class="text-primary-red">{{ $branches->total() }}</strong> รายการ
                        </div>
                        
                        <!-- Right: Custom Pagination -->
                        <div class="pagination-controls">
                            <ul class="custom-pagination">
                                {{-- Previous Page Link --}}
                                @if ($branches->onFirstPage())
                                    <li class="page-item prev disabled">
                                        <span class="page-link">
                                            <i class="fas fa-chevron-left"></i>
                                        </span>
                                    </li>
                                @else
                                    <li class="page-item prev">
                                        <a class="page-link" href="{{ $branches->previousPageUrl() }}" rel="prev">
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                    </li>
                                @endif

                                {{-- Pagination Elements --}}
                                @foreach ($branches->getUrlRange(1, $branches->lastPage()) as $page => $url)
                                    @if ($page == $branches->currentPage())
                                        <li class="page-item active">
                                            <span class="page-link">{{ $page }}</span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                        </li>
                                    @endif
                                @endforeach

                                {{-- Next Page Link --}}
                                @if ($branches->hasMorePages())
                                    <li class="page-item next">
                                        <a class="page-link" href="{{ $branches->nextPageUrl() }}" rel="next">
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </li>
                                @else
                                    <li class="page-item next disabled">
                                        <span class="page-link">
                                            <i class="fas fa-chevron-right"></i>
                                        </span>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-submit search form on Enter
    $('#search').on('keypress', function(e) {
        if (e.which === 13) {
            $(this).closest('form').submit();
        }
    });

    // Enhanced deletion confirmation
    $('.delete-form').on('submit', function(e) {
        e.preventDefault();
        const form = this;
        
        Swal.fire({
            title: 'ยืนยันการลบ',
            text: 'คุณแน่ใจหรือไม่ที่จะลบสาขานี้? การดำเนินการนี้ไม่สามารถยกเลิกได้',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#B54544',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'ลบ',
            cancelButtonText: 'ยกเลิก',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    // Success/Error messages with ITMS style
    @if(session('success'))
        showNotification('success', '{{ session('success') }}');
    @endif

    @if(session('error'))
        showNotification('error', '{{ session('error') }}');
    @endif

    @if(session('warning'))
        showNotification('warning', '{{ session('warning') }}');
    @endif

    function showNotification(type, message) {
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
            }
        };
        
        const config = typeConfig[type];
        const notification = `
            <div class="alert ${config.class} alert-dismissible fade show position-fixed notification-toast" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 350px; max-width: 400px; 
                        animation: slideInRight 0.5s ease; box-shadow: 0 8px 25px rgba(0,0,0,0.15);">
                <div class="d-flex align-items-center">
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: ${config.color}; 
                                display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                        <i class="fas ${config.icon} text-white"></i>
                    </div>
                    <div>
                        <strong style="font-size: 1rem;">${config.title}</strong><br>
                        <small>${message}</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        $('body').append(notification);
        
        // Auto-remove after 6 seconds
        setTimeout(function() {
            $('.notification-toast').fadeOut(500, function() {
                $(this).remove();
            });
        }, 6000);
    }

    // Add slideInRight animation
    if (!document.getElementById('notification-styles')) {
        const style = document.createElement('style');
        style.id = 'notification-styles';
        style.textContent = `
            @keyframes slideInRight {
                from {
                    opacity: 0;
                    transform: translateX(100%);
                }
                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }
            
            .notification-toast {
                border-left: 5px solid;
                border-radius: 12px !important;
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
        `;
        document.head.appendChild(style);
    }

    // Enhanced card hover effects with performance optimization
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '50px'
    };

    const cardObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Apply intersection observer to cards
    $('.branch-card').each(function(index) {
        $(this).css({
            'opacity': '0',
            'transform': 'translateY(30px)',
            'transition': 'all 0.6s ease'
        });
        
        setTimeout(() => {
            cardObserver.observe(this);
        }, index * 100);
    });

    // Smart search with debounce
    let searchTimeout;
    $('#search').on('input', function() {
        clearTimeout(searchTimeout);
        const query = $(this).val();
        
        if (query.length >= 3) {
            searchTimeout = setTimeout(() => {
                // You can implement live search here
                console.log('Searching for:', query);
            }, 500);
        }
    });

    // Touch-friendly interactions for mobile
    if (window.innerWidth <= 768) {
        $('.branch-card').on('touchstart', function() {
            $(this).addClass('touch-active');
        }).on('touchend', function() {
            $(this).removeClass('touch-active');
        });
    }

    // ✅ Custom Pagination loading states
    $('.custom-pagination .page-link').on('click', function(e) {
        // Add loading state untuk navigasi halaman
        if (!$(this).parent().hasClass('disabled') && !$(this).parent().hasClass('active')) {
            const originalContent = $(this).html();
            $(this).html('<i class="fas fa-spinner fa-spin"></i>');
            
            // Restore content setelah 2 detik (fallback)
            setTimeout(() => {
                $(this).html(originalContent);
            }, 2000);
        }
    });

    // ✅ Ensure clean pagination display
    $('.custom-pagination').each(function() {
        // Remove any unwanted text nodes
        $(this).find('.page-item').each(function() {
            const $link = $(this).find('.page-link');
            // Clean up any extra whitespace or unwanted content
            if ($link.length) {
                const content = $link.html().trim();
                if (content && !content.includes('<i class="fas')) {
                    // If it's just a number, keep it clean
                    if (/^\d+$/.test(content)) {
                        $link.html(content);
                    }
                }
            }
        });
    });

    // ITMS Theme console log
    console.log('🏢 ITMS Branch Management - Custom Pagination Perfect');
    console.log('🎨 Theme Colors: Primary Red #B54544, Primary Orange #E6952A');
    console.log('✨ Features: 100% Custom Pagination, No Laravel Conflicts, Clean Layout');
    
    // Performance monitoring
    const loadTime = performance.now();
    console.log(`⚡ Page loaded in ${Math.round(loadTime)}ms`);
});

// Add touch-active style for mobile
const mobileStyle = document.createElement('style');
mobileStyle.textContent = `
    .touch-active {
        transform: scale(0.98) !important;
        transition: transform 0.1s ease !important;
    }
`;
document.head.appendChild(mobileStyle);
</script>
@endpush
@endsection
