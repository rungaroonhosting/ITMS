/**
 * Express Management System v2.0 🚀
 * JavaScript สำหรับจัดการระบบ Express
 * รองรับการเปิด/ปิด Express ทุกแผนก
 */

class ExpressManager {
    constructor() {
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        this.baseUrl = window.location.origin;
        
        this.init();
    }

    init() {
        this.bindEvents();
        this.loadExpressStatus();
    }

    // ===================================================
    // Event Binding
    // ===================================================

    bindEvents() {
        // Department selection change
        document.addEventListener('change', (e) => {
            if (e.target.matches('[name="department_id"]')) {
                this.handleDepartmentChange(e.target);
            }
        });

        // Express toggle buttons
        document.addEventListener('click', (e) => {
            if (e.target.matches('.express-toggle-btn')) {
                this.handleExpressToggle(e.target);
            }
            
            if (e.target.matches('.generate-express-credentials')) {
                this.generateExpressCredentials(e.target);
            }
            
            if (e.target.matches('.test-express-connection')) {
                this.testExpressConnection(e.target);
            }
            
            if (e.target.matches('.send-express-credentials')) {
                this.sendExpressCredentials(e.target);
            }
        });

        // Auto-generate Express username when name changes
        document.addEventListener('input', (e) => {
            if (e.target.matches('[name="first_name_en"], [name="last_name_en"]')) {
                this.autoGenerateExpressUsername();
            }
        });

        // Bulk Express actions
        document.addEventListener('click', (e) => {
            if (e.target.matches('.bulk-enable-express')) {
                this.bulkEnableExpress();
            }
            
            if (e.target.matches('.bulk-disable-express')) {
                this.bulkDisableExpress();
            }
        });
    }

    // ===================================================
    // Department Express Management
    // ===================================================

    async handleDepartmentChange(selectElement) {
        const departmentId = selectElement.value;
        const expressSection = document.querySelector('.express-section');
        const expressFields = document.querySelectorAll('.express-field');
        
        if (!departmentId) {
            this.hideExpressFields();
            return;
        }

        try {
            this.showLoading('กำลังตรวจสอบสิทธิ์ Express...');
            
            const response = await this.apiCall('POST', '/api/express/check-eligibility', {
                department_id: departmentId
            });

            if (response.success) {
                if (response.eligible) {
                    this.showExpressFields(response);
                    this.showSuccessMessage(`✅ ${response.department_name} - ${response.message}`);
                } else {
                    this.hideExpressFields();
                    this.showInfoMessage(`ℹ️ ${response.department_name} - ${response.message}`);
                }
            }

        } catch (error) {
            this.showErrorMessage('เกิดข้อผิดพลาดในการตรวจสอบสิทธิ์ Express');
            console.error('Express eligibility check failed:', error);
        } finally {
            this.hideLoading();
        }
    }

    showExpressFields(data) {
        const expressSection = document.querySelector('.express-section');
        const expressFields = document.querySelectorAll('.express-field');
        
        if (expressSection) {
            expressSection.style.display = 'block';
            expressSection.classList.add('fade-in');
        }
        
        expressFields.forEach(field => {
            field.style.display = 'block';
        });

        // Auto-generate credentials if empty
        this.autoGenerateExpressCredentialsIfEmpty();
    }

    hideExpressFields() {
        const expressSection = document.querySelector('.express-section');
        const expressFields = document.querySelectorAll('.express-field');
        
        if (expressSection) {
            expressSection.style.display = 'none';
        }
        
        expressFields.forEach(field => {
            field.style.display = 'none';
            // Clear values
            const input = field.querySelector('input');
            if (input) input.value = '';
        });
    }

    // ===================================================
    // Express Credentials Generation
    // ===================================================

    async autoGenerateExpressUsername() {
        const firstNameEn = document.querySelector('[name="first_name_en"]')?.value || '';
        const lastNameEn = document.querySelector('[name="last_name_en"]')?.value || '';
        const usernameField = document.querySelector('[name="express_username"]');
        
        if (!firstNameEn || !lastNameEn || !usernameField) return;
        
        // Only auto-generate if field is empty
        if (usernameField.value.trim() !== '') return;

        try {
            const response = await this.apiCall('POST', '/api/express/generate-username', {
                first_name: firstNameEn,
                last_name: lastNameEn
            });

            if (response.success) {
                usernameField.value = response.username;
                this.showFieldSuccess(usernameField, 'สร้าง Username อัตโนมัติ');
            }

        } catch (error) {
            console.error('Auto-generate username failed:', error);
        }
    }

    async generateExpressCredentials(button) {
        const employeeId = button.dataset.employeeId;
        
        if (!employeeId) {
            this.showErrorMessage('ไม่พบข้อมูลพนักงาน');
            return;
        }

        try {
            this.showButtonLoading(button);
            
            const response = await this.apiCall('POST', `/employees/${employeeId}/express/generate`);

            if (response.success) {
                // Update UI with new credentials
                const usernameField = document.querySelector('[name="express_username"]');
                const passwordField = document.querySelector('[name="express_password"]');
                
                if (usernameField) usernameField.value = response.username;
                if (passwordField) passwordField.value = response.password;
                
                this.showSuccessMessage('สร้างข้อมูล Express เรียบร้อยแล้ว');
            }

        } catch (error) {
            this.showErrorMessage('เกิดข้อผิดพลาดในการสร้างข้อมูล Express');
        } finally {
            this.hideButtonLoading(button);
        }
    }

    async autoGenerateExpressCredentialsIfEmpty() {
        const usernameField = document.querySelector('[name="express_username"]');
        const passwordField = document.querySelector('[name="express_password"]');
        
        // Generate username if empty
        if (usernameField && !usernameField.value.trim()) {
            await this.autoGenerateExpressUsername();
        }
        
        // Generate password if empty
        if (passwordField && !passwordField.value.trim()) {
            try {
                const response = await this.apiCall('POST', '/api/express/generate-password');
                if (response.success) {
                    passwordField.value = response.password;
                }
            } catch (error) {
                console.error('Auto-generate password failed:', error);
            }
        }
    }

    // ===================================================
    // Express Connection Testing
    // ===================================================

    async testExpressConnection(button) {
        const usernameField = document.querySelector('[name="express_username"]');
        const passwordField = document.querySelector('[name="express_password"]');
        
        if (!usernameField?.value || !passwordField?.value) {
            this.showErrorMessage('กรุณากรอก Username และ Password Express ก่อนทดสอบ');
            return;
        }

        try {
            this.showButtonLoading(button, 'กำลังทดสอบ...');
            
            const response = await this.apiCall('POST', '/api/express/test-connection', {
                username: usernameField.value,
                password: passwordField.value
            });

            if (response.success) {
                this.showSuccessMessage(`✅ ${response.message} (${response.connection_time}ms)`);
            } else {
                this.showErrorMessage(`❌ ${response.message}`);
            }

        } catch (error) {
            this.showErrorMessage('การทดสอบล้มเหลว');
        } finally {
            this.hideButtonLoading(button, 'ทดสอบการเชื่อมต่อ');
        }
    }

    // ===================================================
    // Express Toggle for Departments
    // ===================================================

    async handleExpressToggle(button) {
        const departmentId = button.dataset.departmentId;
        const currentStatus = button.dataset.expressEnabled === 'true';
        const action = currentStatus ? 'disable' : 'enable';
        
        if (!departmentId) {
            this.showErrorMessage('ไม่พบข้อมูลแผนก');
            return;
        }

        // Confirm action
        const actionText = currentStatus ? 'ปิดใช้งาน' : 'เปิดใช้งาน';
        if (!confirm(`ต้องการ${actionText} Express สำหรับแผนกนี้หรือไม่?`)) {
            return;
        }

        try {
            this.showButtonLoading(button);
            
            const response = await this.apiCall('POST', `/departments/${departmentId}/express/${action}`);

            if (response.success) {
                // Update button state
                this.updateExpressToggleButton(button, !currentStatus);
                this.showSuccessMessage(response.message);
                
                // Refresh any related UI
                this.refreshExpressStats();
            }

        } catch (error) {
            this.showErrorMessage(`เกิดข้อผิดพลาดในการ${actionText} Express`);
        } finally {
            this.hideButtonLoading(button);
        }
    }

    updateExpressToggleButton(button, isEnabled) {
        button.dataset.expressEnabled = isEnabled;
        button.textContent = isEnabled ? '✅ ปิดใช้งาน Express' : '⚪ เปิดใช้งาน Express';
        button.className = `btn ${isEnabled ? 'btn-warning' : 'btn-success'} express-toggle-btn`;
    }

    // ===================================================
    // Bulk Operations
    // ===================================================

    async bulkEnableExpress() {
        const selectedDepartments = this.getSelectedDepartments();
        
        if (selectedDepartments.length === 0) {
            this.showErrorMessage('กรุณาเลือกแผนกที่ต้องการเปิดใช้งาน Express');
            return;
        }

        if (!confirm(`ต้องการเปิดใช้งาน Express สำหรับ ${selectedDepartments.length} แผนกหรือไม่?`)) {
            return;
        }

        try {
            this.showLoading('กำลังเปิดใช้งาน Express...');
            
            const response = await this.apiCall('POST', '/api/departments/express/bulk-enable', {
                department_ids: selectedDepartments
            });

            if (response.success) {
                this.showSuccessMessage(`เปิดใช้งาน Express สำเร็จ ${response.count} แผนก`);
                this.refreshDepartmentList();
            }

        } catch (error) {
            this.showErrorMessage('เกิดข้อผิดพลาดในการเปิดใช้งาน Express');
        } finally {
            this.hideLoading();
        }
    }

    async bulkDisableExpress() {
        const selectedDepartments = this.getSelectedDepartments();
        
        if (selectedDepartments.length === 0) {
            this.showErrorMessage('กรุณาเลือกแผนกที่ต้องการปิดใช้งาน Express');
            return;
        }

        if (!confirm(`⚠️ การปิดใช้งาน Express จะลบข้อมูล Express ของพนักงานในแผนกเหล่านี้\n\nต้องการดำเนินการต่อหรือไม่?`)) {
            return;
        }

        try {
            this.showLoading('กำลังปิดใช้งาน Express...');
            
            const response = await this.apiCall('POST', '/api/departments/express/bulk-disable', {
                department_ids: selectedDepartments
            });

            if (response.success) {
                this.showSuccessMessage(`ปิดใช้งาน Express สำเร็จ ${response.count} แผนก`);
                this.refreshDepartmentList();
            }

        } catch (error) {
            this.showErrorMessage('เกิดข้อผิดพลาดในการปิดใช้งาน Express');
        } finally {
            this.hideLoading();
        }
    }

    getSelectedDepartments() {
        const checkboxes = document.querySelectorAll('.department-checkbox:checked');
        return Array.from(checkboxes).map(cb => cb.value);
    }

    // ===================================================
    // API Helper Methods
    // ===================================================

    async apiCall(method, url, data = null) {
        const options = {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': this.csrfToken
            }
        };

        if (data && (method === 'POST' || method === 'PUT' || method === 'PATCH')) {
            options.body = JSON.stringify(data);
        }

        const response = await fetch(this.baseUrl + url, options);
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        return await response.json();
    }

    // ===================================================
    // UI Helper Methods
    // ===================================================

    showLoading(message = 'กำลังโหลด...') {
        const loader = document.querySelector('.loading-overlay') || this.createLoader();
        loader.querySelector('.loading-text').textContent = message;
        loader.style.display = 'flex';
    }

    hideLoading() {
        const loader = document.querySelector('.loading-overlay');
        if (loader) loader.style.display = 'none';
    }

    createLoader() {
        const loader = document.createElement('div');
        loader.className = 'loading-overlay';
        loader.innerHTML = `
            <div class="loading-content">
                <div class="spinner"></div>
                <div class="loading-text">กำลังโหลด...</div>
            </div>
        `;
        document.body.appendChild(loader);
        return loader;
    }

    showButtonLoading(button, text = 'กำลังโหลด...') {
        button.dataset.originalText = button.textContent;
        button.textContent = text;
        button.disabled = true;
        button.classList.add('loading');
    }

    hideButtonLoading(button, defaultText = null) {
        button.textContent = defaultText || button.dataset.originalText || 'บันทึก';
        button.disabled = false;
        button.classList.remove('loading');
    }

    showSuccessMessage(message) {
        this.showMessage(message, 'success');
    }

    showErrorMessage(message) {
        this.showMessage(message, 'error');
    }

    showInfoMessage(message) {
        this.showMessage(message, 'info');
    }

    showMessage(message, type = 'info') {
        // ใช้ toast notification หรือ alert ตามที่มีในระบบ
        const alertClass = {
            success: 'alert-success',
            error: 'alert-danger',
            info: 'alert-info'
        }[type] || 'alert-info';

        const alert = document.createElement('div');
        alert.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
        alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(alert);

        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (alert.parentNode) {
                alert.remove();
            }
        }, 5000);
    }

    showFieldSuccess(field, message) {
        field.classList.add('is-valid');
        
        // Remove existing feedback
        const existingFeedback = field.parentNode.querySelector('.valid-feedback');
        if (existingFeedback) existingFeedback.remove();
        
        // Add success feedback
        const feedback = document.createElement('div');
        feedback.className = 'valid-feedback';
        feedback.textContent = message;
        field.parentNode.appendChild(feedback);
        
        // Remove after 3 seconds
        setTimeout(() => {
            field.classList.remove('is-valid');
            if (feedback.parentNode) feedback.remove();
        }, 3000);
    }

    // ===================================================
    // Data Loading and Refresh
    // ===================================================

    async loadExpressStatus() {
        try {
            const response = await this.apiCall('GET', '/api/dashboard/express');
            if (response.success) {
                this.updateDashboard(response.data);
            }
        } catch (error) {
            console.error('Failed to load Express status:', error);
        }
    }

    updateDashboard(data) {
        // Update dashboard widgets
        const widgets = {
            'total-departments': data.departments.total_departments,
            'express-departments': data.departments.express_enabled_departments,
            'total-employees': data.employees.total,
            'express-users': data.employees.with_express
        };

        Object.entries(widgets).forEach(([id, value]) => {
            const element = document.getElementById(id);
            if (element) element.textContent = value;
        });
    }

    async refreshExpressStats() {
        try {
            const response = await this.apiCall('GET', '/api/departments/express/stats');
            if (response.success) {
                this.updateDashboard({ departments: response.stats });
            }
        } catch (error) {
            console.error('Failed to refresh Express stats:', error);
        }
    }

    refreshDepartmentList() {
        // Reload the current page or specific section
        window.location.reload();
    }
}

// ===================================================
// CSS Styles (ถ้าต้องการ)
// ===================================================

const expressStyles = `
    .express-section {
        border: 2px solid #e3f2fd;
        border-radius: 8px;
        padding: 20px;
        margin: 15px 0;
        background: linear-gradient(135deg, #f8f9fa 0%, #e3f2fd 100%);
    }

    .express-field {
        margin-bottom: 15px;
    }

    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 10000;
    }

    .loading-content {
        background: white;
        padding: 30px;
        border-radius: 10px;
        text-align: center;
    }

    .spinner {
        border: 4px solid #f3f3f3;
        border-top: 4px solid #007bff;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        animation: spin 1s linear infinite;
        margin: 0 auto 15px;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .fade-in {
        animation: fadeIn 0.3s ease-in;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .btn.loading {
        opacity: 0.6;
        cursor: not-allowed;
    }
`;

// ===================================================
// Initialize
// ===================================================

document.addEventListener('DOMContentLoaded', function() {
    // Add styles
    const styleSheet = document.createElement('style');
    styleSheet.textContent = expressStyles;
    document.head.appendChild(styleSheet);
    
    // Initialize Express Manager
    window.expressManager = new ExpressManager();
    
    console.log('🚀 Express Management System v2.0 initialized');
});
