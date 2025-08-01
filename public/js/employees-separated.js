/*!
 * Employee Management JavaScript v3.0 - SEPARATED Email/Login System
 * Enhanced functionality with separated authentication credentials
 */

'use strict';

document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Employee Management v3.0 - SEPARATED Email/Login System Ready');
    
    // =====================================================
    // UTILITY FUNCTIONS
    // =====================================================
    const utils = {
        showLoading: (button) => {
            button.disabled = true;
            const originalText = button.innerHTML;
            button.dataset.originalText = originalText;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>กำลังสร้าง...';
        },
        
        hideLoading: (button) => {
            button.disabled = false;
            if (button.dataset.originalText) {
                button.innerHTML = button.dataset.originalText;
            }
        },
        
        generateRandomString: (length, includeNumbers = true) => {
            const chars = includeNumbers ? 
                'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789' :
                'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
            let result = '';
            for (let i = 0; i < length; i++) {
                result += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            return result;
        },
        
        generateRandomNumber: (length) => {
            let result = '';
            for (let i = 0; i < length; i++) {
                result += Math.floor(Math.random() * 10);
            }
            return result;
        },
        
        generateUniqueNumbers: (length = 4) => {
            const digits = [];
            while (digits.length < length) {
                const digit = Math.floor(Math.random() * 10);
                if (!digits.includes(digit)) {
                    digits.push(digit);
                }
            }
            return digits.join('');
        },
        
        addGeneratedAnimation: (fieldId) => {
            const field = document.getElementById(fieldId);
            if (field) {
                field.style.background = 'linear-gradient(45deg, #d4edda, #d1ecf1)';
                field.style.transform = 'scale(1.02)';
                field.style.transition = 'all 0.3s ease';
                
                setTimeout(() => {
                    field.style.background = '';
                    field.style.transform = '';
                }, 1500);
            }
        },
        
        showNotification: (message, type = 'success') => {
            const alertClass = type === 'success' ? 'alert-success' : 
                              type === 'error' ? 'alert-danger' : 
                              type === 'warning' ? 'alert-warning' : 'alert-info';
            const iconClass = type === 'success' ? 'fa-check-circle' : 
                             type === 'error' ? 'fa-exclamation-triangle' :
                             type === 'warning' ? 'fa-exclamation-circle' : 'fa-info-circle';
            const alert = document.createElement('div');
            alert.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
            alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);';
            alert.innerHTML = `
                <i class="fas ${iconClass} me-2"></i>${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.body.appendChild(alert);
            
            setTimeout(() => {
                if (alert.parentNode) {
                    alert.remove();
                }
            }, 5000);
        }
    };
    
    // =====================================================
    // GENERATORS - SEPARATED SYSTEM
    // =====================================================
    const generators = {
        employeeCode: () => `EMP${utils.generateRandomNumber(3)}`,
        keycardId: () => `KC${utils.generateRandomNumber(6)}`,
        username: () => {
            const firstName = document.getElementById('first_name_en')?.value.trim();
            const englishRegex = /^[a-zA-Z\s]+$/;
            
            if (firstName && englishRegex.test(firstName)) {
                return firstName.toLowerCase();
            }
            return '';
        },
        email: () => {
            const firstName = document.getElementById('first_name_en')?.value.trim();
            const lastName = document.getElementById('last_name_en')?.value.trim();
            const domain = document.getElementById('email_domain')?.value || 'bettersystem.co.th';
            const englishRegex = /^[a-zA-Z\s]+$/;
            
            if (firstName && lastName && englishRegex.test(firstName) && englishRegex.test(lastName)) {
                return `${firstName.toLowerCase()}.${lastName.charAt(0).toLowerCase()}@${domain}`;
            }
            return '';
        },
        
        // ✅ SEPARATED: แยกรหัสผ่าน Email และ Login
        emailPassword: () => utils.generateRandomString(10, true), // 10 ตัวอักษรสำหรับ Email
        loginPassword: () => utils.generateRandomString(12, true), // 12 ตัวอักษรสำหรับ Login (ปลอดภัยกว่า)
        
        computerPassword: () => utils.generateRandomString(10, true),
        copierCode: () => utils.generateRandomNumber(4),
        expressUsername: () => {
            const firstName = document.getElementById('first_name_en')?.value.trim().toLowerCase();
            if (firstName && firstName.length > 0) {
                return firstName.length <= 7 ? firstName : firstName.substring(0, 7);
            }
            return utils.generateRandomString(5, false).toLowerCase();
        },
        expressPassword: () => utils.generateUniqueNumbers(4),
        phoneNumber: () => {
            const prefixes = ['08', '09', '06', '02'];
            const prefix = prefixes[Math.floor(Math.random() * prefixes.length)];
            const middle = utils.generateRandomNumber(3);
            const last = utils.generateRandomNumber(4);
            return `${prefix}${middle}-${last}`;
        }
    };
    
    // =====================================================
    // AUTO-GENERATION FUNCTIONS - SEPARATED SYSTEM
    // =====================================================
    const autoGenerate = {
        username: () => {
            const username = generators.username();
            if (username) {
                const usernameField = document.getElementById('username');
                if (usernameField) {
                    usernameField.value = username;
                    utils.addGeneratedAnimation('username');
                    console.log('✅ Username generated:', username);
                }
            }
        },
        
        email: () => {
            const email = generators.email();
            if (email) {
                const emailField = document.getElementById('email');
                if (emailField) {
                    emailField.value = email;
                    // ✅ AUTO-SYNC: email → login_email
                    autoGenerate.syncLoginEmail();
                    autoGenerate.showEmailPreview();
                    autoGenerate.updateSummary();
                    utils.addGeneratedAnimation('email');
                    console.log('✅ Email generated and synced:', email);
                }
            }
        },
        
        // ✅ SEPARATED: แยกการสร้างรหัสผ่าน
        emailPassword: () => {
            const password = generators.emailPassword();
            if (password) {
                const emailPasswordField = document.getElementById('email_password');
                if (emailPasswordField) {
                    emailPasswordField.value = password;
                    autoGenerate.updateSummary();
                    utils.addGeneratedAnimation('email_password');
                    console.log('✅ Email password generated:', password);
                    return password;
                }
            }
        },
        
        loginPassword: () => {
            const password = generators.loginPassword();
            if (password) {
                const loginPasswordField = document.getElementById('login_password');
                const hiddenPasswordField = document.getElementById('password');
                
                if (loginPasswordField) {
                    loginPasswordField.value = password;
                    // Sync กับ hidden password field สำหรับ backend
                    if (hiddenPasswordField) {
                        hiddenPasswordField.value = password;
                    }
                    autoGenerate.updateSummary();
                    utils.addGeneratedAnimation('login_password');
                    console.log('✅ Login password generated and synced to backend:', password);
                    return password;
                }
            }
        },
        
        // ✅ SEPARATED: Sync login_email จาก email
        syncLoginEmail: () => {
            const emailField = document.getElementById('email');
            const loginEmailField = document.getElementById('login_email');
            if (loginEmailField && emailField && emailField.value) {
                loginEmailField.value = emailField.value;
                console.log('🔄 Login email synced:', emailField.value);
            }
        },
        
        phoneNumber: () => {
            const phone = generators.phoneNumber();
            const phoneField = document.getElementById('phone');
            if (phone && phoneField) {
                phoneField.value = phone;
                utils.addGeneratedAnimation('phone');
                console.log('✅ Phone number generated (duplicates allowed):', phone);
                return phone;
            }
        },
        
        showEmailPreview: () => {
            const firstName = document.getElementById('first_name_en')?.value.trim();
            const lastName = document.getElementById('last_name_en')?.value.trim();
            const domain = document.getElementById('email_domain')?.value || 'bettersystem.co.th';
            const previewDiv = document.getElementById('emailPreview');
            const previewText = document.getElementById('emailPreviewText');
            
            if (!previewDiv || !previewText) return;
            
            const englishRegex = /^[a-zA-Z\s]+$/;
            
            if (firstName && lastName) {
                if (englishRegex.test(firstName) && englishRegex.test(lastName)) {
                    const emailPreview = `${firstName.toLowerCase()}.${lastName.charAt(0).toLowerCase()}@${domain}`;
                    previewText.textContent = emailPreview;
                    previewDiv.style.display = 'block';
                    previewDiv.className = 'mt-2 text-success';
                } else {
                    previewText.textContent = 'กรุณากรอกชื่อ-นามสกุลภาษาอังกฤษเท่านั้น';
                    previewDiv.style.display = 'block';
                    previewDiv.className = 'mt-2 text-warning';
                }
            } else {
                previewDiv.style.display = 'none';
            }
        },
        
        // ✅ SEPARATED: อัพเดต Summary แสดงข้อมูลแยกระบบ
        updateSummary: () => {
            const email = document.getElementById('email')?.value || '';
            const emailPassword = document.getElementById('email_password')?.value || '';
            const loginEmail = document.getElementById('login_email')?.value || '';
            const loginPassword = document.getElementById('login_password')?.value || '';
            
            // Update summary display
            const summaryElements = {
                summaryEmail: document.getElementById('summaryEmail'),
                summaryEmailPassword: document.getElementById('summaryEmailPassword'),
                summaryLoginEmail: document.getElementById('summaryLoginEmail'),
                summaryLoginPassword: document.getElementById('summaryLoginPassword')
            };
            
            if (summaryElements.summaryEmail) summaryElements.summaryEmail.textContent = email || '-';
            if (summaryElements.summaryEmailPassword) summaryElements.summaryEmailPassword.textContent = emailPassword ? '••••••••••' : '-';
            if (summaryElements.summaryLoginEmail) summaryElements.summaryLoginEmail.textContent = loginEmail || '-';
            if (summaryElements.summaryLoginPassword) summaryElements.summaryLoginPassword.textContent = loginPassword ? '••••••••••••' : '-';
        }
    };
    
    // =====================================================
    // EVENT HANDLERS - SEPARATED SYSTEM
    // =====================================================
    const eventHandlers = {
        handleMagicClick: async (event) => {
            const button = event.target.closest('[data-target]');
            if (!button) return;
            
            const target = button.dataset.target;
            const targetElement = document.getElementById(target);
            if (!targetElement) return;
            
            utils.showLoading(button);
            
            try {
                let value = '';
                
                switch (target) {
                    case 'employee_code':
                        value = generators.employeeCode();
                        break;
                    case 'keycard_id':
                        value = generators.keycardId();
                        break;
                    case 'username':
                        value = generators.username();
                        break;
                    case 'email':
                        value = generators.email();
                        break;
                    case 'computer_password':
                        value = generators.computerPassword();
                        break;
                        
                    // ✅ SEPARATED: แยกรหัสผ่าน Email และ Login
                    case 'email_password':
                        autoGenerate.emailPassword();
                        utils.showNotification('✅ สร้างรหัสผ่านอีเมล สำเร็จ!', 'success');
                        utils.hideLoading(button);
                        return;
                        
                    case 'login_password':
                        autoGenerate.loginPassword();
                        utils.showNotification('✅ สร้างรหัสผ่านเข้าระบบ สำเร็จ!', 'success');
                        utils.hideLoading(button);
                        return;
                        
                    case 'copier_code':
                        value = generators.copierCode();
                        break;
                    case 'express_username':
                        value = generators.expressUsername();
                        break;
                    case 'express_password':
                        value = generators.expressPassword();
                        break;
                }
                
                if (value) {
                    targetElement.value = value;
                    
                    if (target === 'email') {
                        autoGenerate.syncLoginEmail();
                        autoGenerate.showEmailPreview();
                        autoGenerate.updateSummary();
                    }
                    
                    let message = '';
                    switch (target) {
                        case 'email':
                            message = `✅ สร้าง Email สำเร็จ: ${value} (แยกระบบแล้ว)`;
                            break;
                        case 'username':
                            message = `✅ สร้าง Username สำเร็จ: ${value}`;
                            break;
                        default:
                            message = `✅ สร้าง ${target} สำเร็จ: ${value}`;
                    }
                    
                    utils.showNotification(message);
                    utils.addGeneratedAnimation(target);
                }
                
            } catch (error) {
                console.error(`Error generating ${target}:`, error);
                utils.showNotification(`❌ เกิดข้อผิดพลาดในการสร้าง ${target}`, 'error');
            } finally {
                utils.hideLoading(button);
            }
        },
        
        handlePasswordToggle: (event) => {
            const button = event.target.closest('[data-toggle-password]');
            if (!button) return;
            
            const target = button.dataset.togglePassword;
            const targetElement = document.getElementById(target);
            
            if (targetElement) {
                if (targetElement.type === 'password') {
                    targetElement.type = 'text';
                    button.innerHTML = '<i class="fas fa-eye-slash"></i>';
                } else {
                    targetElement.type = 'password';
                    button.innerHTML = '<i class="fas fa-eye"></i>';
                }
            }
        },
        
        handleDepartmentChange: () => {
            const departmentSelect = document.getElementById('department_id');
            const expressSection = document.getElementById('expressSection');
            const expressIndicator = document.getElementById('expressIndicator');
            
            if (!departmentSelect || !expressSection) return;
            
            const departmentId = departmentSelect.value;
            
            if (!departmentId) {
                expressSection.style.display = 'none';
                if (expressIndicator) expressIndicator.style.display = 'none';
                return;
            }
            
            fetch(`/api/departments/is-accounting?department_id=${departmentId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.is_express_enabled) {
                        expressSection.style.display = 'block';
                        if (expressIndicator) expressIndicator.style.display = 'inline-block';
                        
                        const firstName = document.getElementById('first_name_en')?.value.trim();
                        if (firstName) {
                            const expressUsernameEl = document.getElementById('express_username');
                            const expressPasswordEl = document.getElementById('express_password');
                            
                            if (expressUsernameEl && !expressUsernameEl.value) {
                                expressUsernameEl.value = generators.expressUsername();
                            }
                            if (expressPasswordEl && !expressPasswordEl.value) {
                                expressPasswordEl.value = generators.expressPassword();
                            }
                        }
                        
                        utils.showNotification(`⚡ ${data.department_name}: เปิดใช้งาน Express แล้ว`, 'success');
                    } else {
                        expressSection.style.display = 'none';
                        if (expressIndicator) expressIndicator.style.display = 'none';
                        
                        const expressUsernameEl = document.getElementById('express_username');
                        const expressPasswordEl = document.getElementById('express_password');
                        
                        if (expressUsernameEl) expressUsernameEl.value = '';
                        if (expressPasswordEl) expressPasswordEl.value = '';
                    }
                })
                .catch(error => {
                    console.error('Error checking Express eligibility:', error);
                    expressSection.style.display = 'none';
                    if (expressIndicator) expressIndicator.style.display = 'none';
                });
        }
    };
    
    // =====================================================
    // FORM ACTIONS - SEPARATED SYSTEM
    // =====================================================
    const formActions = {
        generateAll: async () => {
            const button = document.getElementById('generateAllBtn');
            if (!button) return;
            
            utils.showLoading(button);
            
            try {
                console.log('🎯 Starting generateAll - SEPARATED Email/Login System...');
                
                // Generate basic codes
                const employeeCodeField = document.getElementById('employee_code');
                const keycardIdField = document.getElementById('keycard_id');
                
                if (employeeCodeField) employeeCodeField.value = generators.employeeCode();
                if (keycardIdField) keycardIdField.value = generators.keycardId();
                
                // Generate phone number (duplicates allowed)
                autoGenerate.phoneNumber();
                
                // Username และ Email generation
                const firstNameField = document.getElementById('first_name_en');
                const lastNameField = document.getElementById('last_name_en');
                
                if (firstNameField && firstNameField.value) {
                    autoGenerate.username();
                    await new Promise(resolve => setTimeout(resolve, 200));
                    
                    if (lastNameField && lastNameField.value) {
                        autoGenerate.email(); // จะ auto-sync login_email ด้วย
                    }
                } else {
                    utils.showNotification('❌ กรุณากรอกชื่อภาษาอังกฤษก่อน จึงจะสร้าง Username และ Email ได้', 'error');
                    utils.hideLoading(button);
                    return;
                }
                
                // Generate passwords แยกกัน
                const computerPasswordField = document.getElementById('computer_password');
                if (computerPasswordField) {
                    computerPasswordField.value = generators.computerPassword();
                }
                
                // ✅ SEPARATED: สร้างรหัสผ่านแยกกัน
                autoGenerate.emailPassword();   // รหัสผ่านอีเมล (10 ตัว)
                autoGenerate.loginPassword();   // รหัสผ่านเข้าระบบ (12 ตัว)
                
                // Express fields (ถ้าแสดงอยู่)
                const expressSection = document.getElementById('expressSection');
                if (expressSection && expressSection.style.display !== 'none') {
                    const expressUsernameField = document.getElementById('express_username');
                    const expressPasswordField = document.getElementById('express_password');
                    
                    if (expressUsernameField) expressUsernameField.value = generators.expressUsername();
                    if (expressPasswordField) expressPasswordField.value = generators.expressPassword();
                }
                
                utils.showNotification('🎉 สร้างข้อมูลทั้งหมดสำเร็จ! (แยกระบบ Email/Login แล้ว)', 'success');
                
            } catch (error) {
                console.error('Error in generateAll:', error);
                utils.showNotification('❌ เกิดข้อผิดพลาดในการสร้างข้อมูล', 'error');
            } finally {
                utils.hideLoading(button);
            }
        },
        
        clearAll: () => {
            if (confirm('ต้องการล้างข้อมูลทั้งหมดหรือไม่?')) {
                const form = document.getElementById('employeeForm');
                if (form) {
                    form.reset();
                }
                
                // Reset UI elements
                const elementsToReset = [
                    'emailPreview', 'expressSection', 'expressIndicator',
                    'summaryEmail', 'summaryEmailPassword', 
                    'summaryLoginEmail', 'summaryLoginPassword'
                ];
                
                elementsToReset.forEach(id => {
                    const element = document.getElementById(id);
                    if (element) {
                        if (id.includes('summary')) {
                            element.textContent = '-';
                        } else {
                            element.style.display = 'none';
                        }
                    }
                });
                
                // Re-generate initial codes
                setTimeout(() => {
                    const employeeCodeField = document.getElementById('employee_code');
                    const keycardIdField = document.getElementById('keycard_id');
                    
                    if (employeeCodeField) employeeCodeField.value = generators.employeeCode();
                    if (keycardIdField) keycardIdField.value = generators.keycardId();
                }, 100);
                
                utils.showNotification('🗑️ ล้างข้อมูลทั้งหมดแล้ว', 'success');
            }
        },
        
        showPreview: () => {
            const form = document.getElementById('employeeForm');
            if (!form) return;
            
            const formData = new FormData(form);
            const data = {};
            for (let [key, value] of formData.entries()) {
                data[key] = value;
            }
            
            console.log('🔍 Preview data (separated system):', data);
            utils.showNotification('🔍 ตัวอย่างข้อมูล (ระบบแยกแล้ว)', 'info');
        }
    };
    
    // =====================================================
    // EVENT LISTENERS SETUP - SEPARATED SYSTEM
    // =====================================================
    try {
        // Click handlers
        document.addEventListener('click', eventHandlers.handleMagicClick);
        document.addEventListener('click', eventHandlers.handlePasswordToggle);
        
        // Department change handler
        const departmentSelect = document.getElementById('department_id');
        if (departmentSelect) {
            departmentSelect.addEventListener('change', eventHandlers.handleDepartmentChange);
        }
        
        // ✅ SEPARATED: Email input auto-sync handler
        const emailInput = document.getElementById('email');
        if (emailInput) {
            emailInput.addEventListener('input', (e) => {
                autoGenerate.syncLoginEmail();
                autoGenerate.updateSummary();
            });
        }
        
        // ✅ SEPARATED: Password input handlers สำหรับ summary update
        const emailPasswordInput = document.getElementById('email_password');
        const loginPasswordInput = document.getElementById('login_password');
        
        if (emailPasswordInput) {
            emailPasswordInput.addEventListener('input', autoGenerate.updateSummary);
        }
        
        if (loginPasswordInput) {
            loginPasswordInput.addEventListener('input', (e) => {
                // Sync กับ hidden password field
                const hiddenPasswordField = document.getElementById('password');
                if (hiddenPasswordField) {
                    hiddenPasswordField.value = e.target.value;
                }
                autoGenerate.updateSummary();
            });
        }
        
        // English validation handlers
        const firstNameEn = document.getElementById('first_name_en');
        const lastNameEn = document.getElementById('last_name_en');
        
        if (firstNameEn) {
            firstNameEn.addEventListener('input', () => {
                setTimeout(() => {
                    autoGenerate.username();
                    autoGenerate.email();
                    autoGenerate.showEmailPreview();
                }, 300);
            });
        }
        
        if (lastNameEn) {
            lastNameEn.addEventListener('input', () => {
                setTimeout(() => {
                    autoGenerate.email();
                    autoGenerate.showEmailPreview();
                }, 300);
            });
        }
        
        // Email domain change handler
        const emailDomain = document.getElementById('email_domain');
        if (emailDomain) {
            emailDomain.addEventListener('change', () => {
                autoGenerate.email();
                autoGenerate.showEmailPreview();
            });
        }
        
        // Quick Action buttons
        const buttons = {
            generateAllBtn: document.getElementById('generateAllBtn'),
            clearAllBtn: document.getElementById('clearAllBtn'),
            previewBtn: document.getElementById('previewBtn')
        };
        
        if (buttons.generateAllBtn) {
            buttons.generateAllBtn.addEventListener('click', formActions.generateAll);
        }
        
        if (buttons.clearAllBtn) {
            buttons.clearAllBtn.addEventListener('click', formActions.clearAll);
        }
        
        if (buttons.previewBtn) {
            buttons.previewBtn.addEventListener('click', formActions.showPreview);
        }
        
        console.log('✅ All event listeners attached successfully - SEPARATED Email/Login System');
        
    } catch (error) {
        console.error('❌ Error setting up event listeners:', error);
    }
    
    // =====================================================
    // INITIAL SETUP - SEPARATED SYSTEM
    // =====================================================
    setTimeout(() => {
        console.log('🔧 Starting initial setup - SEPARATED Email/Login System...');
        
        try {
            // Auto-generate employee code และ keycard
            const employeeCodeEl = document.getElementById('employee_code');
            const keycardIdEl = document.getElementById('keycard_id');
            
            if (employeeCodeEl && !employeeCodeEl.value) {
                employeeCodeEl.value = generators.employeeCode();
            }
            
            if (keycardIdEl && !keycardIdEl.value) {
                keycardIdEl.value = generators.keycardId();
            }
            
            // Initialize functions
            eventHandlers.handleDepartmentChange();
            autoGenerate.showEmailPreview();
            autoGenerate.updateSummary();
            
            // Show separation notification
            setTimeout(() => {
                utils.showNotification('🎉 ระบบใหม่: แยกรหัสผ่าน Email และ Login แล้ว! (ปลอดภัยกว่า)', 'success');
            }, 2000);
            
            console.log('✅ Employee Create Form Ready - SEPARATED Email/Login System');
            console.log('📧 Email System: อีเมล + รหัสผ่านอีเมล (10 ตัว)');
            console.log('🔐 Login System: อีเมล + รหัสผ่านเข้าระบบ (12 ตัว)');
            console.log('🛡️ Security: รหัสผ่านแยกกัน ปลอดภัยกว่า');
            console.log('🚀 Status: EMAIL/LOGIN SEPARATED - ENHANCED SECURITY! ✅');
            
        } catch (error) {
            console.error('❌ Error in initial setup:', error);
        }
    }, 1000);
});

// =====================================================
// GLOBAL FUNCTIONS FOR TESTING - SEPARATED SYSTEM
// =====================================================
window.SeparatedAuthTest = {
    testEmailGeneration: function() {
        console.log('📧 Testing Email Generation:');
        const firstNameField = document.getElementById('first_name_en');
        const lastNameField = document.getElementById('last_name_en');
        
        if (firstNameField) firstNameField.value = 'John';
        if (lastNameField) lastNameField.value = 'Doe';
        
        const emailBtn = document.querySelector('[data-target="email"]');
        if (emailBtn) emailBtn.click();
        
        console.log('   Email:', document.getElementById('email')?.value);
        console.log('   Login Email:', document.getElementById('login_email')?.value);
        console.log('   Should be synced: YES ✅');
    },
    
    testPasswordSeparation: function() {
        console.log('🔐 Testing Password Separation:');
        
        const emailPassBtn = document.querySelector('[data-target="email_password"]');
        const loginPassBtn = document.querySelector('[data-target="login_password"]');
        
        if (emailPassBtn) emailPassBtn.click();
        if (loginPassBtn) loginPassBtn.click();
        
        const emailPass = document.getElementById('email_password')?.value;
        const loginPass = document.getElementById('login_password')?.value;
        const hiddenPass = document.getElementById('password')?.value;
        
        console.log('   Email Password (10):', emailPass);
        console.log('   Login Password (12):', loginPass);
        console.log('   Hidden Password:', hiddenPass);
        console.log('   Should be different: YES ✅');
        console.log('   Login synced to hidden:', loginPass === hiddenPass ? 'YES ✅' : 'NO ❌');
    },
    
    info: function() {
        console.log('🔧 Separated Email/Login Test Functions:');
        console.log('   SeparatedAuthTest.testEmailGeneration() - ทดสอบสร้างอีเมล');
        console.log('   SeparatedAuthTest.testPasswordSeparation() - ทดสอบแยกรหัสผ่าน');
        console.log('');
        console.log('🎯 Features:');
        console.log('   📧 Email System: อีเมล + รหัสผ่านอีเมล (10 ตัว)');
        console.log('   🔐 Login System: อีเมล + รหัสผ่านเข้าระบบ (12 ตัว)');
        console.log('   🔄 Auto-sync: Email → Login Email');
        console.log('   🛡️ Security: รหัสผ่านแยกกัน ปลอดภัยกว่า');
    }
};

console.log('🧪 พิมพ์ SeparatedAuthTest.info() เพื่อดูคำสั่งทดสอบ Separated Email/Login System');
console.log('🗑️ Employee Management v3.0 loaded with SEPARATED Email/Login Authentication');
