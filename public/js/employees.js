/*!
 * ITMS - Employee Management JavaScript
 * Enhanced functionality for Employee CRUD operations
 */

'use strict';

// Global variables
const EmployeeManager = {
    // Configuration
    config: {
        confirmDeleteTitle: 'ยืนยันการลบ',
        confirmDeleteText: 'คุณต้องการลบข้อมูลนี้หรือไม่?',
        successTitle: 'สำเร็จ!',
        errorTitle: 'เกิดข้อผิดพลาด!',
        loadingTitle: 'กำลังประมวลผล...',
        confirmButton: 'ยืนยัน',
        cancelButton: 'ยกเลิก',
        deleteButton: 'ลบ',
        csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
    },

    // Initialize all functionality
    init() {
        this.initDataTable();
        this.initFormValidation();
        this.initEventListeners();
        this.initTooltips();
        this.initAutoComplete();
        console.log('Employee Manager initialized successfully');
    },

    // Initialize DataTable if present
    initDataTable() {
        if (typeof $.fn.DataTable !== 'undefined' && $('#employeesTable').length) {
            const table = $('#employeesTable').DataTable({
                responsive: true,
                pageLength: 15,
                order: [[0, 'desc']],
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/th.json'
                },
                columnDefs: [
                    {
                        targets: -1, // Last column (Actions)
                        orderable: false,
                        searchable: false
                    }
                ],
                drawCallback: function() {
                    // Re-initialize tooltips after table redraw
                    EmployeeManager.initTooltips();
                }
            });

            // Custom search functionality
            $('.dataTables_filter input').attr('placeholder', 'ค้นหาพนักงาน...');
        }
    },

    // Initialize form validation
    initFormValidation() {
        const forms = document.querySelectorAll('.needs-validation, #employeeForm');
        
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                    
                    // Show first invalid field
                    const firstInvalid = form.querySelector(':invalid');
                    if (firstInvalid) {
                        firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        firstInvalid.focus();
                    }
                }
                form.classList.add('was-validated');
            });
        });

        // Real-time validation
        this.initRealTimeValidation();
    },

    // Initialize real-time validation
    initRealTimeValidation() {
        // Employee ID validation
        const employeeIdInput = document.getElementById('employee_id');
        if (employeeIdInput) {
            employeeIdInput.addEventListener('input', function() {
                const value = this.value.toUpperCase();
                this.value = value;
                
                if (value.length > 0 && !/^[A-Z0-9]+$/.test(value)) {
                    this.setCustomValidity('รหัสพนักงานต้องเป็นตัวอักษรพิมพ์ใหญ่และตัวเลขเท่านั้น');
                } else {
                    this.setCustomValidity('');
                }
            });
        }

        // Username validation
        const usernameInput = document.getElementById('username');
        if (usernameInput) {
            usernameInput.addEventListener('input', function() {
                const value = this.value.toLowerCase();
                this.value = value;
                
                if (value.length > 0 && !/^[a-z0-9._]+$/.test(value)) {
                    this.setCustomValidity('ชื่อผู้ใช้ต้องเป็นตัวอักษรพิมพ์เล็ก ตัวเลข จุด และขีดเส้นใต้เท่านั้น');
                } else {
                    this.setCustomValidity('');
                }
            });
        }

        // Email validation
        const emailInput = document.getElementById('email');
        if (emailInput) {
            emailInput.addEventListener('blur', function() {
                if (this.value && !this.checkValidity()) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });
        }

        // Phone number formatting
        const phoneInputs = document.querySelectorAll('input[type="tel"]');
        phoneInputs.forEach(input => {
            input.addEventListener('input', function() {
                let value = this.value.replace(/\D/g, '');
                if (value.length >= 10) {
                    value = value.replace(/(\d{3})(\d{3})(\d{4})/, '$1-$2-$3');
                }
                this.value = value;
            });
        });
    },

    // Initialize event listeners
    initEventListeners() {
        // Delete buttons
        $(document).on('click', '.delete-btn', this.handleDelete.bind(this));
        
        // Generate buttons
        $(document).on('click', '#generateEmpIdBtn', () => this.generateEmployeeId());
        $(document).on('click', '#generateUsernameBtn', () => this.generateUsername());
        $(document).on('click', '#generatePasswordBtn', () => this.generatePassword());
        $(document).on('click', '#generateAllBtn', () => this.generateAll());
        
        // Toggle password visibility
        $(document).on('click', '#togglePasswordBtn', this.togglePassword);
        
        // Auto-generate on name change
        $('#first_name, #last_name').on('blur', this.autoGenerateUsername.bind(this));
        
        // Form submission with AJAX
        $('#employeeForm').on('submit', this.handleFormSubmit.bind(this));
        
        // Filter change events
        $('.filter-select').on('change', this.handleFilterChange.bind(this));
        
        // Search with debounce
        let searchTimeout;
        $('#search').on('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                EmployeeManager.handleSearch();
            }, 500);
        });

        // Export functionality
        $('#exportBtn').on('click', this.handleExport.bind(this));

        // Print functionality
        $('#printBtn').on('click', () => window.print());

        // Keyboard shortcuts
        $(document).on('keydown', this.handleKeyboardShortcuts.bind(this));
    },

    // Initialize tooltips
    initTooltips() {
        if (typeof bootstrap !== 'undefined') {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"], [title]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
    },

    // Initialize autocomplete functionality
    initAutoComplete() {
        // Position autocomplete (simple implementation)
        const positionInput = document.getElementById('position');
        if (positionInput) {
            const commonPositions = [
                'นักพัฒนาระบบ',
                'วิศวกรซอฟต์แวร์',
                'นักวิเคราะห์ระบบ',
                'ผู้จัดการโครงการ',
                'ผู้ช่วยผู้จัดการ',
                'หัวหน้าแผนก',
                'ผู้อำนวยการ',
                'เลขานุการ',
                'พนักงานบัญชี',
                'พนักงานการเงิน',
                'นักการตลาด',
                'พนักงานขาย',
                'ผู้เชี่ยวชาญด้าน IT',
                'ผู้ดูแลระบบ',
                'นักออกแบบ UI/UX'
            ];

            this.setupAutocomplete(positionInput, commonPositions);
        }
    },

    // Setup autocomplete for input field
    setupAutocomplete(input, suggestions) {
        let currentIndex = -1;
        const dropdown = document.createElement('div');
        dropdown.className = 'autocomplete-dropdown';
        dropdown.style.cssText = `
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #ced4da;
            border-top: none;
            border-radius: 0 0 8px 8px;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
        `;

        input.parentNode.style.position = 'relative';
        input.parentNode.appendChild(dropdown);

        input.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            const matches = suggestions.filter(item => 
                item.toLowerCase().includes(query)
            );

            if (matches.length > 0 && query.length > 0) {
                dropdown.innerHTML = matches.map((item, index) => 
                    `<div class="autocomplete-item" data-index="${index}" style="padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #eee;">${item}</div>`
                ).join('');
                dropdown.style.display = 'block';
                currentIndex = -1;
            } else {
                dropdown.style.display = 'none';
            }
        });

        input.addEventListener('keydown', function(e) {
            const items = dropdown.querySelectorAll('.autocomplete-item');
            
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                currentIndex = Math.min(currentIndex + 1, items.length - 1);
                updateSelection();
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                currentIndex = Math.max(currentIndex - 1, -1);
                updateSelection();
            } else if (e.key === 'Enter' && currentIndex >= 0) {
                e.preventDefault();
                input.value = items[currentIndex].textContent;
                dropdown.style.display = 'none';
            } else if (e.key === 'Escape') {
                dropdown.style.display = 'none';
            }
        });

        dropdown.addEventListener('click', function(e) {
            if (e.target.classList.contains('autocomplete-item')) {
                input.value = e.target.textContent;
                dropdown.style.display = 'none';
            }
        });

        function updateSelection() {
            const items = dropdown.querySelectorAll('.autocomplete-item');
            items.forEach((item, index) => {
                item.style.backgroundColor = index === currentIndex ? '#f8f9fa' : 'white';
            });
        }

        document.addEventListener('click', function(e) {
            if (!input.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.style.display = 'none';
            }
        });
    },

    // Handle delete action
    handleDelete(event) {
        const button = event.currentTarget;
        const employeeId = button.getAttribute('data-id');
        const employeeName = button.getAttribute('data-name');

        if (!employeeId) return;

        Swal.fire({
            title: this.config.confirmDeleteTitle,
            text: `คุณต้องการลบพนักงาน "${employeeName}" หรือไม่?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: this.config.deleteButton,
            cancelButtonText: this.config.cancelButton,
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                this.deleteEmployee(employeeId);
            }
        });
    },

    // Delete employee via AJAX
    async deleteEmployee(employeeId) {
        try {
            const response = await fetch(`/employees/${employeeId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.config.csrfToken
                }
            });

            const data = await response.json();

            if (data.success) {
                Swal.fire({
                    title: this.config.successTitle,
                    text: data.message,
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                throw new Error(data.message || 'ไม่สามารถลบข้อมูลได้');
            }
        } catch (error) {
            Swal.fire({
                title: this.config.errorTitle,
                text: error.message,
                icon: 'error'
            });
        }
    },

    // Generate employee ID
    async generateEmployeeId() {
        try {
            const response = await fetch('/employees/generate/data?type=employee_id');
            const data = await response.json();
            
            if (data.employee_id) {
                $('#employee_id').val(data.employee_id);
                this.showToast('สร้างรหัสพนักงานแล้ว', 'success');
            }
        } catch (error) {
            this.showToast('ไม่สามารถสร้างรหัสพนักงานได้', 'error');
        }
    },

    // Generate username
    async generateUsername() {
        const firstName = $('#first_name').val();
        const lastName = $('#last_name').val();
        
        if (!firstName || !lastName) {
            this.showToast('กรุณากรอกชื่อและนามสกุลก่อน', 'warning');
            return;
        }

        try {
            const response = await fetch(`/employees/generate/data?type=username&first_name=${firstName}&last_name=${lastName}`);
            const data = await response.json();
            
            if (data.username) {
                $('#username').val(data.username);
                if (data.email) {
                    $('#email').val(data.email);
                }
                this.showToast('สร้างชื่อผู้ใช้และอีเมลแล้ว', 'success');
            }
        } catch (error) {
            this.showToast('ไม่สามารถสร้างชื่อผู้ใช้ได้', 'error');
        }
    },

    // Generate password
    async generatePassword() {
        try {
            const response = await fetch('/employees/generate/data?type=password');
            const data = await response.json();
            
            if (data.password) {
                $('#password').val(data.password);
                // Show password temporarily
                $('#password').attr('type', 'text');
                setTimeout(() => {
                    $('#password').attr('type', 'password');
                }, 3000);
                this.showToast('สร้างรหัสผ่านแล้ว (แสดง 3 วินาที)', 'success');
            }
        } catch (error) {
            this.showToast('ไม่สามารถสร้างรหัสผ่านได้', 'error');
        }
    },

    // Generate all fields
    async generateAll() {
        const firstName = $('#first_name').val();
        const lastName = $('#last_name').val();
        
        if (!firstName || !lastName) {
            this.showToast('กรุณากรอกชื่อและนามสกุลก่อน', 'warning');
            return;
        }

        try {
            const response = await fetch(`/employees/generate/data?type=all&first_name=${firstName}&last_name=${lastName}`);
            const data = await response.json();
            
            if (data.employee_id) $('#employee_id').val(data.employee_id);
            if (data.username) $('#username').val(data.username);
            if (data.email) $('#email').val(data.email);
            if (data.password) $('#password').val(data.password);
            
            this.showToast('สร้างข้อมูลทั้งหมดแล้ว', 'success');
        } catch (error) {
            this.showToast('ไม่สามารถสร้างข้อมูลได้', 'error');
        }
    },

    // Auto-generate username when name changes
    autoGenerateUsername() {
        const firstName = $('#first_name').val();
        const lastName = $('#last_name').val();
        const currentUsername = $('#username').val();
        
        if (firstName && lastName && !currentUsername) {
            this.generateUsername();
        }
    },

    // Toggle password visibility
    togglePassword() {
        const passwordField = $('#password');
        const icon = $(this).find('i');
        
        if (passwordField.attr('type') === 'password') {
            passwordField.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            passwordField.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    },

    // Handle form submission
    async handleFormSubmit(event) {
        event.preventDefault();
        
        const form = event.target;
        const submitBtn = $('#submitBtn');
        const originalText = submitBtn.html();
        
        // Show loading state
        submitBtn.prop('disabled', true)
                 .html('<i class="fas fa-spinner fa-spin me-1"></i>กำลังบันทึก...');

        try {
            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: form.method || 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': this.config.csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();

            if (response.ok && data.success) {
                Swal.fire({
                    title: this.config.successTitle,
                    text: data.message,
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        location.reload();
                    }
                });
            } else {
                if (data.errors) {
                    this.displayValidationErrors(data.errors);
                } else {
                    throw new Error(data.message || 'ไม่สามารถบันทึกข้อมูลได้');
                }
            }
        } catch (error) {
            Swal.fire({
                title: this.config.errorTitle,
                text: error.message,
                icon: 'error'
            });
        } finally {
            submitBtn.prop('disabled', false).html(originalText);
        }
    },

    // Display validation errors
    displayValidationErrors(errors) {
        // Clear previous errors
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();

        // Show new errors
        Object.keys(errors).forEach(field => {
            const input = $(`[name="${field}"]`);
            if (input.length) {
                input.addClass('is-invalid');
                input.after(`<div class="invalid-feedback">${errors[field][0]}</div>`);
            }
        });

        // Scroll to first error
        const firstError = $('.is-invalid').first();
        if (firstError.length) {
            firstError[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    },

    // Handle filter changes
    handleFilterChange() {
        // Auto-submit filter form
        $('#filterForm').submit();
    },

    // Handle search
    handleSearch() {
        $('#filterForm').submit();
    },

    // Handle export
    handleExport() {
        const params = new URLSearchParams();
        
        // Collect filter values
        const filters = ['search', 'department', 'status', 'role'];
        filters.forEach(filter => {
            const value = $(`#${filter}`).val();
            if (value) params.append(filter, value);
        });
        
        const url = '/employees/export/excel' + (params.toString() ? '?' + params.toString() : '');
        window.location.href = url;
    },

    // Handle keyboard shortcuts
    handleKeyboardShortcuts(event) {
        // Ctrl/Cmd + S to save form
        if ((event.ctrlKey || event.metaKey) && event.key === 's') {
            event.preventDefault();
            const form = $('#employeeForm');
            if (form.length) {
                form.submit();
            }
        }
        
        // Escape to close modals
        if (event.key === 'Escape') {
            $('.modal.show').modal('hide');
        }

        // Ctrl/Cmd + N to create new employee
        if ((event.ctrlKey || event.metaKey) && event.key === 'n') {
            event.preventDefault();
            const createBtn = $('a[href*="employees/create"]');
            if (createBtn.length) {
                window.location.href = createBtn.attr('href');
            }
        }
    },

    // Show toast notification
    showToast(message, type = 'info') {
        // Create toast container if it doesn't exist
        let toastContainer = document.querySelector('.toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
            toastContainer.style.zIndex = '9999';
            document.body.appendChild(toastContainer);
        }

        const toastId = 'toast-' + Date.now();
        const toastHtml = `
            <div id="${toastId}" class="toast align-items-center text-white bg-${type === 'success' ? 'success' : type === 'error' ? 'danger' : type === 'warning' ? 'warning' : 'info'} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'exclamation-triangle' : type === 'warning' ? 'exclamation-triangle' : 'info'} me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;

        toastContainer.insertAdjacentHTML('beforeend', toastHtml);
        const toastElement = document.getElementById(toastId);

        // Initialize and show toast
        if (typeof bootstrap !== 'undefined') {
            const toast = new bootstrap.Toast(toastElement, {
                autohide: true,
                delay: 3000
            });
            toast.show();

            // Remove toast element after it's hidden
            toastElement.addEventListener('hidden.bs.toast', function() {
                this.remove();
            });
        } else {
            // Fallback if Bootstrap is not available
            setTimeout(() => {
                toastElement.remove();
            }, 3000);
        }
    },

    // Utility function to format currency
    formatCurrency(amount) {
        return new Intl.NumberFormat('th-TH', {
            style: 'currency',
            currency: 'THB'
        }).format(amount);
    },

    // Utility function to format date
    formatDate(date, locale = 'th-TH') {
        return new Intl.DateTimeFormat(locale).format(new Date(date));
    },

    // Utility function to debounce function calls
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },

    // Validate form before submission
    validateForm(form) {
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });

        return isValid;
    },

    // Handle network errors
    handleNetworkError(error) {
        console.error('Network error:', error);
        this.showToast('เกิดข้อผิดพลาดในการเชื่อมต่อ กรุณาลองใหม่อีกครั้ง', 'error');
    },

    // Loading state management
    setLoadingState(element, loading = true) {
        if (loading) {
            element.classList.add('loading');
            element.disabled = true;
        } else {
            element.classList.remove('loading');
            element.disabled = false;
        }
    }
};

// Additional utility functions for global use
window.EmployeeUtils = {
    // Validate Thai phone number
    validateThaiPhone(phone) {
        const phoneRegex = /^(\+66|0)[0-9]{8,9}$/;
        return phoneRegex.test(phone.replace(/[\s-]/g, ''));
    },

    // Validate Thai ID card
    validateThaiID(id) {
        if (id.length !== 13) return false;
        
        let sum = 0;
        for (let i = 0; i < 12; i++) {
            sum += parseInt(id.charAt(i)) * (13 - i);
        }
        
        const checkDigit = (11 - (sum % 11)) % 10;
        return checkDigit === parseInt(id.charAt(12));
    },

    // Generate strong password
    generateStrongPassword(length = 12) {
        const lowercase = 'abcdefghijklmnopqrstuvwxyz';
        const uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        const numbers = '0123456789';
        const symbols = '!@#$%^&*()_+-=[]{}|;:,.<>?';
        
        const allChars = lowercase + uppercase + numbers + symbols;
        let password = '';
        
        // Ensure at least one character from each category
        password += lowercase[Math.floor(Math.random() * lowercase.length)];
        password += uppercase[Math.floor(Math.random() * uppercase.length)];
        password += numbers[Math.floor(Math.random() * numbers.length)];
        password += symbols[Math.floor(Math.random() * symbols.length)];
        
        // Fill the rest randomly
        for (let i = 4; i < length; i++) {
            password += allChars[Math.floor(Math.random() * allChars.length)];
        }
        
        // Shuffle the password
        return password.split('').sort(() => 0.5 - Math.random()).join('');
    },

    // Format file size
    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    },

    // Copy text to clipboard
    copyToClipboard(text) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text).then(() => {
                EmployeeManager.showToast('คัดลอกเรียบร้อยแล้ว', 'success');
            });
        } else {
            // Fallback for older browsers
            const textArea = document.createElement('textarea');
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            EmployeeManager.showToast('คัดลอกเรียบร้อยแล้ว', 'success');
        }
    }
};

// Initialize when DOM is ready
$(document).ready(function() {
    EmployeeManager.init();
});

// Handle page unload
window.addEventListener('beforeunload', function(e) {
    const forms = document.querySelectorAll('form');
    let hasUnsavedChanges = false;

    forms.forEach(form => {
        if (form.classList.contains('was-validated') || form.querySelector('.is-invalid')) {
            hasUnsavedChanges = true;
        }
    });

    if (hasUnsavedChanges) {
        e.preventDefault();
        e.returnValue = '';
    }
});

// Global error handler
window.addEventListener('error', function(e) {
    console.error('Global error:', e.error);
    if (typeof EmployeeManager !== 'undefined') {
        EmployeeManager.showToast('เกิดข้อผิดพลาดในระบบ', 'error');
    }
});
