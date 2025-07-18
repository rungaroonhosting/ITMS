{{-- Credentials Partial View --}}
<div class="space-y-6">
    {{-- Employee Info Header --}}
    <div class="bg-gray-50 rounded-lg p-4">
        <div class="flex items-center">
            <div class="flex-shrink-0 h-12 w-12">
                <div class="h-12 w-12 rounded-full bg-gradient-to-r from-blue-400 to-indigo-500 flex items-center justify-center">
                    <span class="text-white font-semibold text-lg">
                        {{ substr($employee->first_name_th, 0, 1) }}{{ substr($employee->last_name_th, 0, 1) }}
                    </span>
                </div>
            </div>
            <div class="ml-4">
                <h4 class="text-lg font-semibold text-gray-900">
                    {{ $employee->first_name_th }} {{ $employee->last_name_th }}
                </h4>
                <p class="text-sm text-gray-600">{{ $employee->first_name_en }} {{ $employee->last_name_en }}</p>
                <p class="text-xs text-gray-500">{{ $employee->employee_code }} | {{ $employee->department_name }}</p>
            </div>
        </div>
    </div>

    {{-- Basic Credentials --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        {{-- Employee Code --}}
        <div class="credential-item">
            <label class="credential-label">รหัสพนักงาน</label>
            <div class="credential-value-container">
                <input type="text" value="{{ $employee->employee_code }}" class="credential-input" readonly>
                <button type="button" onclick="copyToClipboard('{{ $employee->employee_code }}')" class="copy-btn">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Keycard ID --}}
        <div class="credential-item">
            <label class="credential-label">รหัสคีย์การ์ด</label>
            <div class="credential-value-container">
                <input type="text" value="{{ $employee->keycard_id }}" class="credential-input" readonly>
                <button type="button" onclick="copyToClipboard('{{ $employee->keycard_id }}')" class="copy-btn">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Computer System Credentials --}}
    <div class="credentials-section">
        <h5 class="section-title">ระบบคอมพิวเตอร์</h5>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Username --}}
            <div class="credential-item">
                <label class="credential-label">Username</label>
                <div class="credential-value-container">
                    <input type="text" value="{{ $employee->username ?? '-' }}" class="credential-input" readonly>
                    @if($employee->username)
                    <button type="button" onclick="copyToClipboard('{{ $employee->username }}')" class="copy-btn">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                    </button>
                    @endif
                </div>
            </div>

            {{-- Computer Password --}}
            <div class="credential-item">
                <label class="credential-label">Password คอมพิวเตอร์</label>
                <div class="credential-value-container">
                    <input type="password" value="{{ $employee->computer_password ?? '-' }}" class="credential-input password-field" readonly>
                    <button type="button" onclick="togglePassword(this)" class="toggle-password-btn">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </button>
                    @if($employee->computer_password)
                    <button type="button" onclick="copyToClipboard('{{ $employee->computer_password }}')" class="copy-btn">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                    </button>
                    @endif
                </div>
            </div>

            {{-- Copier Code --}}
            <div class="credential-item">
                <label class="credential-label">รหัสเครื่องถ่ายเอกสาร</label>
                <div class="credential-value-container">
                    <input type="text" value="{{ $employee->copier_code ?? '-' }}" class="credential-input" readonly>
                    @if($employee->copier_code)
                    <button type="button" onclick="copyToClipboard('{{ $employee->copier_code }}')" class="copy-btn">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Email System Credentials --}}
    <div class="credentials-section">
        <h5 class="section-title">ระบบอีเมล</h5>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Email --}}
            <div class="credential-item">
                <label class="credential-label">อีเมล</label>
                <div class="credential-value-container">
                    <input type="text" value="{{ $employee->email }}" class="credential-input" readonly>
                    <button type="button" onclick="copyToClipboard('{{ $employee->email }}')" class="copy-btn">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Email Password --}}
            <div class="credential-item">
                <label class="credential-label">Password อีเมล</label>
                <div class="credential-value-container">
                    <input type="password" value="{{ $employee->email_password ?? '-' }}" class="credential-input password-field" readonly>
                    <button type="button" onclick="togglePassword(this)" class="toggle-password-btn">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </button>
                    @if($employee->email_password)
                    <button type="button" onclick="copyToClipboard('{{ $employee->email_password }}')" class="copy-btn">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Express System (if applicable) --}}
    @if($employee->express_username || $employee->express_code)
    <div class="credentials-section">
        <h5 class="section-title">ระบบ Express</h5>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Express Username --}}
            <div class="credential-item">
                <label class="credential-label">Username Express</label>
                <div class="credential-value-container">
                    <input type="text" value="{{ $employee->express_username ?? '-' }}" class="credential-input" readonly>
                    @if($employee->express_username)
                    <button type="button" onclick="copyToClipboard('{{ $employee->express_username }}')" class="copy-btn">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                    </button>
                    @endif
                </div>
            </div>

            {{-- Express Code --}}
            <div class="credential-item">
                <label class="credential-label">รหัส Express</label>
                <div class="credential-value-container">
                    <input type="password" value="{{ $employee->express_code ?? '-' }}" class="credential-input password-field" readonly>
                    <button type="button" onclick="togglePassword(this)" class="toggle-password-btn">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </button>
                    @if($employee->express_code)
                    <button type="button" onclick="copyToClipboard('{{ $employee->express_code }}')" class="copy-btn">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- System Login Credentials --}}
    <div class="credentials-section">
        <h5 class="section-title">ระบบ IT Management</h5>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Login Email --}}
            <div class="credential-item">
                <label class="credential-label">Login Email</label>
                <div class="credential-value-container">
                    <input type="text" value="{{ $employee->login_email ?? $employee->email }}" class="credential-input" readonly>
                    <button type="button" onclick="copyToClipboard('{{ $employee->login_email ?? $employee->email }}')" class="copy-btn">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- System Password --}}
            <div class="credential-item">
                <label class="credential-label">Password ระบบ</label>
                <div class="credential-value-container">
                    <input type="password" value="********" class="credential-input password-field" readonly>
                    <button type="button" onclick="togglePassword(this)" class="toggle-password-btn">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </button>
                    <button type="button" onclick="resetPassword({{ $employee->id }})" class="reset-btn">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </button>
                </div>
                <p class="text-xs text-gray-500 mt-1">คลิกรีเซ็ตเพื่อสร้างรหัสผ่านใหม่</p>
            </div>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="flex justify-end space-x-3 pt-4 border-t">
        <button type="button" 
                onclick="printCredentials()" 
                class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors duration-200">
            <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            พิมพ์
        </button>
        
        <button type="button" 
                onclick="closeCredentialsModal()" 
                class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors duration-200">
            ปิด
        </button>
    </div>
</div>

<style>
/* Credentials Modal Styles */
.credential-item {
    @apply space-y-2;
}

.credential-label {
    @apply block text-sm font-medium text-gray-700;
}

.credential-value-container {
    @apply flex;
}

.credential-input {
    @apply flex-1 px-3 py-2 border border-gray-300 rounded-l-lg bg-gray-50 text-sm font-mono;
}

.copy-btn, .toggle-password-btn, .reset-btn {
    @apply px-3 py-2 bg-gray-100 border border-l-0 border-gray-300 text-gray-600 hover:bg-gray-200 hover:text-gray-800 transition-colors duration-200;
}

.copy-btn:last-child, .toggle-password-btn:last-child, .reset-btn:last-child {
    @apply rounded-r-lg;
}

.reset-btn {
    @apply bg-orange-100 text-orange-600 hover:bg-orange-200 hover:text-orange-800;
}

.credentials-section {
    @apply space-y-4;
}

.section-title {
    @apply text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2;
}
</style>

<script>
// Toggle password visibility
function togglePassword(button) {
    const input = button.parentElement.querySelector('.password-field');
    
    if (input.type === 'password') {
        input.type = 'text';
        button.innerHTML = `
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L8.464 8.464M9.878 9.878A3 3 0 109.88 9.88m4.242 4.242L15.536 15.536M14.12 14.12a3 3 0 01-4.242-4.242m4.242 4.242L17.657 17.657"></path>
            </svg>
        `;
    } else {
        input.type = 'password';
        button.innerHTML = `
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            </svg>
        `;
    }
}

// Reset password
async function resetPassword(employeeId) {
    if (confirm('ต้องการรีเซ็ตรหัสผ่านระบบหรือไม่?')) {
        try {
            const response = await fetch(`/employees/${employeeId}/reset-password`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                alert(`รีเซ็ตรหัสผ่านสำเร็จ\nรหัสผ่านใหม่: ${data.new_password}`);
                
                // Update the password field
                const passwordInput = document.querySelector('input[value="********"]');
                if (passwordInput) {
                    passwordInput.value = data.new_password;
                }
            } else {
                alert(data.message || 'เกิดข้อผิดพลาด');
            }
        } catch (error) {
            console.error('Error resetting password:', error);
            alert('เกิดข้อผิดพลาดในการเชื่อมต่อ');
        }
    }
}

// Print credentials
function printCredentials() {
    window.print();
}
</script>