@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">สร้างพนักงานใหม่</h1>
            <p class="text-gray-600">ระบบจัดการข้อมูลพนักงาน IT Management</p>
        </div>

        <!-- Quick Action Bar -->
        <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
            <div class="flex flex-wrap gap-3 justify-center">
                <button type="button" class="btn-quick-action" id="generateAllBtn">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 7.172V5L8 4z"></path>
                    </svg>
                    สร้างทั้งหมดอัตโนมัติ
                </button>
                <button type="button" class="btn-quick-action" id="previewBtn">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    ดูตัวอย่าง
                </button>
                <button type="button" class="btn-quick-action" id="clearAllBtn">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1-1H9a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    ล้างทั้งหมด
                </button>
            </div>
        </div>

        <!-- Main Form -->
        <form id="employeeForm" action="{{ route('employees.store') }}" method="POST">
            @csrf
            
            <!-- Error Messages -->
            @if ($errors->any())
                <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 rounded-r-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">กรุณาแก้ไขข้อผิดพลาดต่อไปนี้:</h3>
                            <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Personal Information -->
            <div class="card-section mb-6">
                <div class="card-header">
                    <div class="flex items-center">
                        <div class="step-number">1</div>
                        <div>
                            <h3 class="card-title">ข้อมูลส่วนตัว</h3>
                            <p class="card-subtitle">ชื่อ-นามสกุล และข้อมูลพื้นฐาน</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Employee Code (Admin Only) -->
                        @if(auth()->user()->role === 'super_admin' || auth()->user()->role === 'it_admin')
                        <div class="form-group">
                            <label for="employee_code" class="form-label">
                                รหัสพนักงาน
                                <span class="admin-badge">Admin Only</span>
                            </label>
                            <div class="input-group">
                                <input type="text" 
                                       id="employee_code" 
                                       name="employee_code" 
                                       value="{{ old('employee_code') }}"
                                       class="form-input @error('employee_code') error @enderror" 
                                       placeholder="EMP001 (ปล่อยว่างเพื่อสร้างอัตโนมัติ)">
                                <button type="button" class="btn-magic" data-target="employee_code">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 7.172V5L8 4z"></path>
                                    </svg>
                                </button>
                            </div>
                            @error('employee_code')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Keycard ID (Admin Only) -->
                        <div class="form-group">
                            <label for="keycard_id" class="form-label">
                                รหัสคีย์การ์ด
                                <span class="admin-badge">Admin Only</span>
                            </label>
                            <div class="input-group">
                                <input type="text" 
                                       id="keycard_id" 
                                       name="keycard_id" 
                                       value="{{ old('keycard_id') }}"
                                       class="form-input @error('keycard_id') error @enderror" 
                                       placeholder="KEY001 (ปล่อยว่างเพื่อสร้างอัตโนมัติ)">
                                <button type="button" class="btn-magic" data-target="keycard_id">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m0 0a2 2 0 012-2m-2 2v5a2 2 0 01-4 0v-5a2 2 0 012-2zm4 0V9a6 6 0 10-12 0v2m0 0a2 2 0 00-2 2v5a2 2 0 002 2h8a2 2 0 002-2v-5a2 2 0 00-2-2z"></path>
                                    </svg>
                                </button>
                            </div>
                            @error('keycard_id')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>
                        @endif

                        <!-- Thai First Name -->
                        <div class="form-group {{ (auth()->user()->role === 'super_admin' || auth()->user()->role === 'it_admin') ? '' : 'md:col-span-2' }}">
                            <label for="first_name_th" class="form-label required">ชื่อภาษาไทย</label>
                            <input type="text" 
                                   id="first_name_th" 
                                   name="first_name_th" 
                                   value="{{ old('first_name_th') }}"
                                   class="form-input @error('first_name_th') error @enderror" 
                                   placeholder="กรอกชื่อภาษาไทย"
                                   required>
                            @error('first_name_th')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Thai Last Name -->
                        <div class="form-group">
                            <label for="last_name_th" class="form-label required">นามสกุลภาษาไทย</label>
                            <input type="text" 
                                   id="last_name_th" 
                                   name="last_name_th" 
                                   value="{{ old('last_name_th') }}"
                                   class="form-input @error('last_name_th') error @enderror" 
                                   placeholder="กรอกนามสกุลภาษาไทย"
                                   required>
                            @error('last_name_th')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- English First Name -->
                        <div class="form-group">
                            <label for="first_name_en" class="form-label required">ชื่อภาษาอังกฤษ</label>
                            <input type="text" 
                                   id="first_name_en" 
                                   name="first_name_en" 
                                   value="{{ old('first_name_en') }}"
                                   class="form-input @error('first_name_en') error @enderror" 
                                   placeholder="First Name"
                                   required>
                            @error('first_name_en')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- English Last Name -->
                        <div class="form-group">
                            <label for="last_name_en" class="form-label required">นามสกุลภาษาอังกฤษ</label>
                            <input type="text" 
                                   id="last_name_en" 
                                   name="last_name_en" 
                                   value="{{ old('last_name_en') }}"
                                   class="form-input @error('last_name_en') error @enderror" 
                                   placeholder="Last Name"
                                   required>
                            @error('last_name_en')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div class="form-group">
                            <label for="phone" class="form-label required">เบอร์โทรศัพท์</label>
                            <input type="tel" 
                                   id="phone" 
                                   name="phone" 
                                   value="{{ old('phone') }}"
                                   class="form-input @error('phone') error @enderror" 
                                   placeholder="08x-xxx-xxxx"
                                   required>
                            @error('phone')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Nickname -->
                        <div class="form-group">
                            <label for="nickname" class="form-label">ชื่อเล่น</label>
                            <input type="text" 
                                   id="nickname" 
                                   name="nickname" 
                                   value="{{ old('nickname') }}"
                                   class="form-input @error('nickname') error @enderror" 
                                   placeholder="กรอกชื่อเล่น">
                            @error('nickname')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Computer System Information -->
            <div class="card-section mb-6">
                <div class="card-header">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="step-number">2</div>
                            <div>
                                <h3 class="card-title">ระบบคอมพิวเตอร์</h3>
                                <p class="card-subtitle">Username และรหัสผ่านสำหรับคอมพิวเตอร์</p>
                            </div>
                        </div>
                        <button type="button" class="btn-quick-action" id="generateAllComputerBtn">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            สร้างระบบคอมฯ
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Username -->
                        <div class="form-group">
                            <label for="username" class="form-label">Username (เปิดคอมพิวเตอร์)</label>
                            <div class="input-group">
                                <input type="text" 
                                       id="username" 
                                       name="username" 
                                       value="{{ old('username') }}"
                                       class="form-input @error('username') error @enderror" 
                                       placeholder="สร้างจากชื่ออังกฤษ">
                                <button type="button" class="btn-magic" data-target="username">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </button>
                            </div>
                            <p class="form-hint">จะถูกสร้างจากชื่อ.นามสกุล ภาษาอังกฤษ</p>
                            @error('username')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Computer Password -->
                        <div class="form-group">
                            <label for="computer_password" class="form-label">Password (เปิดคอมพิวเตอร์)</label>
                            <div class="input-group">
                                @if(auth()->user()->role === 'super_admin' || auth()->user()->role === 'it_admin')
                                    <input type="password" 
                                           id="computer_password" 
                                           name="computer_password" 
                                           value="{{ old('computer_password') }}"
                                           class="form-input @error('computer_password') error @enderror" 
                                           placeholder="Random 10 ตัวอักษร">
                                    <button type="button" class="btn-magic" data-target="computer_password">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                    </button>
                                    <button type="button" class="btn-toggle-password" data-target="computer_password">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                @else
                                    <input type="password" 
                                           id="computer_password" 
                                           name="computer_password" 
                                           value="{{ old('computer_password') }}"
                                           class="form-input bg-gray-100 cursor-not-allowed" 
                                           placeholder="สร้างอัตโนมัติ"
                                           readonly>
                                    <button type="button" class="btn-magic" data-target="computer_password">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                            <p class="form-hint">
                                รหัสผ่านสำหรับเปิดคอมพิวเตอร์
                                @if(!(auth()->user()->role === 'super_admin' || auth()->user()->role === 'it_admin'))
                                    <span class="text-orange-600">(มองไม่เห็น - เฉพาะ Admin)</span>
                                @endif
                            </p>
                            @error('computer_password')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Copier Code -->
                        <div class="form-group md:col-span-2">
                            <label for="copier_code" class="form-label">รหัสเครื่องถ่ายเอกสาร</label>
                            <div class="input-group max-w-md">
                                <input type="text" 
                                       id="copier_code" 
                                       name="copier_code" 
                                       value="{{ old('copier_code') }}"
                                       class="form-input @error('copier_code') error @enderror" 
                                       placeholder="Random 4 หลัก" 
                                       maxlength="4">
                                <button type="button" class="btn-magic" data-target="copier_code">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                    </svg>
                                </button>
                            </div>
                            <p class="form-hint">รหัส 4 หลักตัวเลขสำหรับใช้เครื่องถ่ายเอกสาร</p>
                            @error('copier_code')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Email System -->
            <div class="card-section mb-6">
                <div class="card-header">
                    <div class="flex items-center">
                        <div class="step-number">3</div>
                        <div>
                            <h3 class="card-title">ระบบอีเมล</h3>
                            <p class="card-subtitle">อีเมลและรหัสผ่านสำหรับระบบอีเมล</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Email -->
                        <div class="form-group md:col-span-2">
                            <label for="email" class="form-label required">อีเมล</label>
                            <div class="input-group">
                                <input type="email" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email') }}"
                                       class="form-input @error('email') error @enderror" 
                                       placeholder="สร้างจาก Username"
                                       required>
                                <select class="form-select max-w-[200px]" id="email_domain">
                                    <option value="bettersystem.co.th">@bettersystem.co.th</option>
                                    <option value="better-groups.com">@better-groups.com</option>
                                </select>
                                <button type="button" class="btn-magic" data-target="email">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                    </svg>
                                </button>
                            </div>
                            <p class="form-hint">รูปแบบ: ชื่อ.ตัวแรกของนามสกุล@โดเมน</p>
                            @error('email')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email Password -->
                        <div class="form-group">
                            <label for="email_password" class="form-label">Password อีเมล</label>
                            <div class="input-group">
                                @if(auth()->user()->role === 'super_admin' || auth()->user()->role === 'it_admin')
                                    <input type="password" 
                                           id="email_password" 
                                           name="email_password" 
                                           value="{{ old('email_password') }}"
                                           class="form-input @error('email_password') error @enderror" 
                                           placeholder="Random 10 ตัวอักษร">
                                    <button type="button" class="btn-magic" data-target="email_password">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                    </button>
                                    <button type="button" class="btn-toggle-password" data-target="email_password">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                @else
                                    <input type="password" 
                                           id="email_password" 
                                           name="email_password" 
                                           value="{{ old('email_password') }}"
                                           class="form-input bg-gray-100 cursor-not-allowed" 
                                           placeholder="สร้างอัตโนมัติ"
                                           readonly>
                                    <button type="button" class="btn-magic" data-target="email_password">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                            <p class="form-hint">
                                @if(!(auth()->user()->role === 'super_admin' || auth()->user()->role === 'it_admin'))
                                    <span class="text-orange-600">(มองไม่เห็น - เฉพาะ Admin)</span>
                                @endif
                            </p>
                            @error('email_password')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Express Program (Conditional) -->
            <div class="card-section mb-6" id="expressSection" style="display: none;">
                <div class="card-header">
                    <div class="flex items-center">
                        <div class="step-number">4</div>
                        <div>
                            <h3 class="card-title">โปรแกรม Express</h3>
                            <p class="card-subtitle">เฉพาะแผนกบัญชี</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-blue-800">โปรแกรม Express จะถูกลงทะเบียนเฉพาะพนักงานในแผนกบัญชีเท่านั้น</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Express Username -->
                        <div class="form-group">
                            <label for="express_username" class="form-label">Username Express (7 ตัวอักษร)</label>
                            <div class="input-group">
                                <input type="text" 
                                       id="express_username" 
                                       name="express_username" 
                                       value="{{ old('express_username') }}"
                                       class="form-input @error('express_username') error @enderror" 
                                       placeholder="7 ตัวอักษร" 
                                       maxlength="7">
                                <button type="button" class="btn-magic" data-target="express_username">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                </button>
                            </div>
                            <p class="form-hint">สร้างจากชื่ออังกฤษ 7 ตัวอักษร</p>
                            @error('express_username')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Express Code -->
                        <div class="form-group">
                            <label for="express_code" class="form-label">รหัสโปรแกรม Express</label>
                            <div class="input-group">
                                @if(auth()->user()->role === 'super_admin' || auth()->user()->role === 'it_admin')
                                    <input type="password" 
                                           id="express_code" 
                                           name="express_code" 
                                           value="{{ old('express_code') }}"
                                           class="form-input @error('express_code') error @enderror" 
                                           placeholder="Random 4 หลัก" 
                                           maxlength="4">
                                    <button type="button" class="btn-magic" data-target="express_code">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                    </button>
                                    <button type="button" class="btn-toggle-password" data-target="express_code">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                @else
                                    <input type="password" 
                                           id="express_code" 
                                           name="express_code" 
                                           value="{{ old('express_code') }}"
                                           class="form-input bg-gray-100 cursor-not-allowed" 
                                           placeholder="สร้างอัตโนมัติ"
                                           readonly>
                                    <button type="button" class="btn-magic" data-target="express_code">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                            <p class="form-hint">
                                รหัส 4 หลักตัวเลขสำหรับโปรแกรม Express
                                @if(!(auth()->user()->role === 'super_admin' || auth()->user()->role === 'it_admin'))
                                    <span class="text-orange-600">(มองไม่เห็น - เฉพาะ Admin)</span>
                                @endif
                            </p>
                            @error('express_code')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Department and Role -->
            <div class="card-section mb-6">
                <div class="card-header">
                    <div class="flex items-center">
                        <div class="step-number">5</div>
                        <div>
                            <h3 class="card-title">แผนกและสิทธิ์</h3>
                            <p class="card-subtitle">แผนกการทำงานและสิทธิ์การใช้งาน</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Department -->
                        <div class="form-group">
                            <label for="department_id" class="form-label required">แผนกการทำงาน</label>
                            <select id="department_id" 
                                    name="department_id" 
                                    class="form-select @error('department_id') error @enderror" 
                                    required>
                                <option value="">เลือกแผนก</option>
                                @php
                                    // Define departments array as fallback
                                    $departmentsList = [
                                        '1' => 'บัญชี',
                                        '2' => 'IT',
                                        '3' => 'ฝ่ายขาย',
                                        '4' => 'การตลาด',
                                        '5' => 'บุคคล',
                                        '6' => 'ผลิต',
                                        '7' => 'คลังสินค้า',
                                        '8' => 'บริหาร'
                                    ];
                                    
                                    // Use passed departments if available, otherwise use fallback
                                    if (isset($departments) && is_object($departments)) {
                                        $deptCollection = $departments;
                                    } elseif (isset($departments) && is_array($departments)) {
                                        $deptCollection = collect($departments);
                                    } else {
                                        $deptCollection = collect($departmentsList)->map(function($name, $id) {
                                            return (object)['id' => $id, 'name' => $name];
                                        });
                                    }
                                @endphp
                                
                                @if(auth()->user()->role === 'express')
                                    @foreach($deptCollection->where('name', 'บัญชี') as $department)
                                        <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                @else
                                    @foreach($deptCollection as $department)
                                        <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('department_id')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                            @if(auth()->user()->role === 'express')
                                <p class="form-hint text-blue-600">Express: สามารถเลือกเฉพาะแผนกบัญชี</p>
                            @endif
                        </div>

                        <!-- Position -->
                        <div class="form-group">
                            <label for="position" class="form-label required">ตำแหน่ง</label>
                            <input type="text" 
                                   id="position" 
                                   name="position" 
                                   value="{{ old('position') }}"
                                   class="form-input @error('position') error @enderror" 
                                   placeholder="เช่น Developer, Accountant"
                                   required>
                            @error('position')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Role -->
                        <div class="form-group">
                            <label for="role" class="form-label required">สิทธิ์การใช้งาน</label>
                            <select id="role" 
                                    name="role" 
                                    class="form-select @error('role') error @enderror" 
                                    required>
                                <option value="">เลือกสิทธิ์</option>
                                <option value="employee" {{ old('role') == 'employee' ? 'selected' : '' }}>พนักงานทั่วไป (Employee)</option>
                                @if(auth()->user()->role === 'super_admin' || auth()->user()->role === 'it_admin')
                                    <option value="hr" {{ old('role') == 'hr' ? 'selected' : '' }}>ฝ่ายบุคคล (HR)</option>
                                    <option value="manager" {{ old('role') == 'manager' ? 'selected' : '' }}>ผู้จัดการ (Manager)</option>
                                    <option value="express" {{ old('role') == 'express' ? 'selected' : '' }}>Express</option>
                                    @if(auth()->user()->role === 'super_admin')
                                        <option value="it_admin" {{ old('role') == 'it_admin' ? 'selected' : '' }}>IT Admin</option>
                                    @endif
                                @endif
                            </select>
                            @error('role')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="form-group">
                            <label for="status" class="form-label required">สถานะ</label>
                            <select id="status" 
                                    name="status" 
                                    class="form-select @error('status') error @enderror" 
                                    required>
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>ใช้งาน (Active)</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>ไม่ใช้งาน (Inactive)</option>
                            </select>
                            @error('status')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Login -->
            <div class="card-section mb-6">
                <div class="card-header">
                    <div class="flex items-center">
                        <div class="step-number">6</div>
                        <div>
                            <h3 class="card-title">ระบบ Login</h3>
                            <p class="card-subtitle">ข้อมูลสำหรับเข้าสู่ระบบ IT Management</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Login Email (Auto-sync) -->
                        <div class="form-group">
                            <label for="login_email" class="form-label">
                                Email สำหรับ Login
                                <span class="auto-sync-badge">Auto Sync</span>
                            </label>
                            <input type="email" 
                                   id="login_email" 
                                   name="login_email" 
                                   value="{{ old('login_email') }}"
                                   class="form-input bg-gray-100 cursor-not-allowed" 
                                   placeholder="จะใช้อีเมลเดียวกันข้างต้น"
                                   readonly>
                            <p class="form-hint text-green-600">จะใช้อีเมลเดียวกันกับที่สร้างข้างต้น</p>
                        </div>

                        <!-- System Password -->
                        <div class="form-group">
                            <label for="password" class="form-label required">Password สำหรับ Login ระบบ</label>
                            <div class="input-group">
                                @if(auth()->user()->role === 'super_admin' || auth()->user()->role === 'it_admin')
                                    <input type="password" 
                                           id="password" 
                                           name="password" 
                                           value="{{ old('password') }}"
                                           class="form-input @error('password') error @enderror" 
                                           placeholder="Random 10 ตัวอักษร"
                                           required>
                                    <button type="button" class="btn-magic" data-target="password">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                    </button>
                                    <button type="button" class="btn-toggle-password" data-target="password">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                @else
                                    <input type="hidden" name="password" value="Bettersystem123">
                                    <input type="password" 
                                           id="password_display" 
                                           class="form-input bg-gray-100 cursor-not-allowed" 
                                           value="Bettersystem123"
                                           placeholder="รหัสผ่านเริ่มต้น"
                                           readonly>
                                    <button type="button" class="btn-disabled">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                            <p class="form-hint">
                                รหัสผ่านสำหรับเข้าระบบ IT Management
                                @if(!(auth()->user()->role === 'super_admin' || auth()->user()->role === 'it_admin'))
                                    <span class="text-orange-600">(ใช้รหัสเริ่มต้น - เปลี่ยนได้ภายหลัง)</span>
                                @endif
                            </p>
                            @error('password')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="card-section">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <a href="{{ route('employees.index') }}" 
                           class="inline-flex items-center px-6 py-3 border border-gray-300 shadow-sm text-base font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            ยกเลิก
                        </a>
                        
                        <button type="submit" 
                                id="submitBtn"
                                class="inline-flex items-center px-8 py-3 border border-transparent text-base font-medium rounded-lg shadow-sm text-white bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transform hover:scale-105 transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            สร้างพนักงาน
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <!-- Preview Modal -->
        <div id="previewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-lg bg-white">
                <div class="modal-header flex justify-between items-center pb-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">ตัวอย่างข้อมูลพนักงาน</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closePreviewModal()">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="modal-body py-4" id="previewContent">
                    <!-- Preview content will be inserted here -->
                </div>
                <div class="modal-footer flex justify-end space-x-3 pt-4 border-t">
                    <button type="button" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600" onclick="closePreviewModal()">
                        ปิด
                    </button>
                    <button type="button" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600" onclick="submitForm()">
                        ยืนยันและบันทึก
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom CSS for Enhanced Form */
.btn-quick-action {
    @apply inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hover:border-blue-300 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 space-x-2;
}

.card-section {
    @apply bg-white rounded-xl shadow-lg overflow-hidden;
}

.card-header {
    @apply bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-4;
}

.card-title {
    @apply text-xl font-semibold text-white;
}

.card-subtitle {
    @apply text-blue-100 text-sm mt-1;
}

.card-body {
    @apply p-6;
}

.step-number {
    @apply flex items-center justify-center w-8 h-8 bg-white text-blue-600 rounded-full text-sm font-bold mr-4;
}

.form-group {
    @apply space-y-2;
}

.form-label {
    @apply block text-sm font-medium text-gray-700;
}

.form-label.required::after {
    @apply text-red-500 ml-1;
    content: '*';
}

.form-input {
    @apply w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200;
}

.form-input.error {
    @apply border-red-500 focus:ring-red-500 focus:border-red-500;
}

.form-select {
    @apply w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white;
}

.form-select.error {
    @apply border-red-500 focus:ring-red-500 focus:border-red-500;
}

.input-group {
    @apply flex;
}

.input-group .form-input {
    @apply rounded-r-none border-r-0;
}

.input-group .form-select {
    @apply rounded-none border-l-0 border-r-0;
}

.btn-magic {
    @apply px-3 py-2 bg-blue-500 text-white border border-blue-500 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200;
}

.btn-toggle-password {
    @apply px-3 py-2 bg-gray-100 text-gray-600 border border-gray-300 rounded-r-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200;
}

.btn-disabled {
    @apply px-3 py-2 bg-gray-100 text-gray-400 border border-gray-300 rounded-r-lg cursor-not-allowed;
}

.input-group .btn-magic:last-child {
    @apply rounded-r-lg;
}

.form-hint {
    @apply text-xs text-gray-500 mt-1;
}

.error-message {
    @apply text-xs text-red-600 mt-1;
}

.admin-badge {
    @apply ml-2 px-2 py-1 text-xs bg-orange-100 text-orange-800 rounded-full;
}

.auto-sync-badge {
    @apply ml-2 px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full;
}

/* Custom animations */
.form-input:focus {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
}

.card-section {
    animation: fadeInUp 0.6s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Phone number formatting */
input[type="tel"]:focus {
    @apply ring-2 ring-blue-500 border-blue-500;
}

/* Loading state */
.loading {
    @apply opacity-75 cursor-not-allowed;
}

.loading::after {
    content: '';
    @apply absolute top-1/2 left-1/2 w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin;
    transform: translate(-50%, -50%);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .card-header {
        @apply px-4 py-3;
    }
    
    .card-body {
        @apply p-4;
    }
    
    .step-number {
        @apply w-6 h-6 text-xs mr-3;
    }
}
</style>

<script>
// Modern JavaScript for Employee Form
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Enhanced Employee Form Loaded');
    
    // Configuration
    const config = {
        baseUrl: '/employees',
        endpoints: {
            generateData: '/employees/generate-data'
        }
    };
    
    // Utility Functions
    const utils = {
        // Show loading state
        showLoading: (button) => {
            button.disabled = true;
            button.classList.add('loading');
            const originalText = button.innerHTML;
            button.dataset.originalText = originalText;
            button.innerHTML = `
                <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                กำลังสร้าง...
            `;
        },
        
        // Hide loading state
        hideLoading: (button) => {
            button.disabled = false;
            button.classList.remove('loading');
            if (button.dataset.originalText) {
                button.innerHTML = button.dataset.originalText;
            }
        },
        
        // Generate random string
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
        
        // Generate random number
        generateRandomNumber: (length) => {
            let result = '';
            for (let i = 0; i < length; i++) {
                result += Math.floor(Math.random() * 10);
            }
            return result;
        },
        
        // Show notification
        showNotification: (message, type = 'success') => {
            // Simple notification system
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 ${
                type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
            }`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }
    };
    
    // Generator Functions
    const generators = {
        // Generate Employee Code
        employeeCode: async () => {
            try {
                const response = await fetch(`${config.endpoints.generateData}?type=employee_code`);
                const data = await response.json();
                return data.employee_code || `EMP${utils.generateRandomNumber(3)}`;
            } catch (error) {
                console.error('Error generating employee code:', error);
                return `EMP${utils.generateRandomNumber(3)}`;
            }
        },
        
        // Generate Keycard ID
        keycardId: async () => {
            try {
                const response = await fetch(`${config.endpoints.generateData}?type=keycard_id`);
                const data = await response.json();
                return data.keycard_id || `KEY${utils.generateRandomNumber(6)}`;
            } catch (error) {
                console.error('Error generating keycard ID:', error);
                return `KEY${utils.generateRandomNumber(6)}`;
            }
        },
        
        // Generate Username from English names
        username: () => {
            const firstName = document.getElementById('first_name_en').value.trim().toLowerCase();
            const lastName = document.getElementById('last_name_en').value.trim().toLowerCase();
            
            if (firstName && lastName) {
                return `${firstName}.${lastName}`;
            }
            return '';
        },
        
        // Generate Email from Username
        email: () => {
            const username = document.getElementById('username').value.trim();
            const domain = document.getElementById('email_domain').value;
            
            if (username && domain) {
                return `${username}@${domain}`;
            }
            
            // Fallback: generate from English names
            const firstName = document.getElementById('first_name_en').value.trim().toLowerCase();
            const lastName = document.getElementById('last_name_en').value.trim().toLowerCase();
            
            if (firstName && lastName) {
                return `${firstName}.${lastName.charAt(0)}@${domain}`;
            }
            
            return '';
        },
        
        // Generate Password
        password: () => {
            return utils.generateRandomString(10, true);
        },
        
        // Generate Copier Code
        copierCode: () => {
            return utils.generateRandomNumber(4);
        },
        
        // Generate Express Username (7 chars)
        expressUsername: () => {
            const firstName = document.getElementById('first_name_en').value.trim().toLowerCase();
            if (firstName.length >= 7) {
                return firstName.substring(0, 7);
            } else if (firstName.length > 0) {
                return firstName.padEnd(7, 'x');
            }
            return utils.generateRandomString(7, false).toLowerCase();
        },
        
        // Generate Express Code (4 digits)
        expressCode: () => {
            return utils.generateRandomNumber(4);
        }
    };
    
    // Auto-generation functions
    const autoGenerate = {
        // Auto-generate username when English names change
        username: () => {
            const username = generators.username();
            if (username) {
                document.getElementById('username').value = username;
                // Auto-generate email after username
                setTimeout(() => autoGenerate.email(), 100);
            }
        },
        
        // Auto-generate email when username or domain changes
        email: () => {
            const email = generators.email();
            if (email) {
                document.getElementById('email').value = email;
                document.getElementById('login_email').value = email;
            }
        }
    };
    
    // Event Handlers
    const eventHandlers = {
        // Handle magic button clicks
        handleMagicClick: async (event) => {
            const button = event.target.closest('.btn-magic');
            if (!button) return;
            
            const target = button.dataset.target;
            if (!target) return;
            
            const targetElement = document.getElementById(target);
            if (!targetElement) return;
            
            utils.showLoading(button);
            
            try {
                let value = '';
                
                switch (target) {
                    case 'employee_code':
                        value = await generators.employeeCode();
                        break;
                    case 'keycard_id':
                        value = await generators.keycardId();
                        break;
                    case 'username':
                        value = generators.username();
                        break;
                    case 'email':
                        value = generators.email();
                        break;
                    case 'computer_password':
                    case 'email_password':
                    case 'password':
                        value = generators.password();
                        break;
                    case 'copier_code':
                        value = generators.copierCode();
                        break;
                    case 'express_username':
                        value = generators.expressUsername();
                        break;
                    case 'express_code':
                        value = generators.expressCode();
                        break;
                }
                
                if (value) {
                    targetElement.value = value;
                    
                    // Auto-sync login email if generating main email
                    if (target === 'email') {
                        document.getElementById('login_email').value = value;
                    }
                    
                    utils.showNotification(`สร้าง ${target} สำเร็จ: ${value}`);
                }
                
            } catch (error) {
                console.error(`Error generating ${target}:`, error);
                utils.showNotification(`เกิดข้อผิดพลาดในการสร้าง ${target}`, 'error');
            } finally {
                utils.hideLoading(button);
            }
        },
        
        // Handle password toggle
        handlePasswordToggle: (event) => {
            const button = event.target.closest('.btn-toggle-password');
            if (!button) return;
            
            const target = button.dataset.target;
            const targetElement = document.getElementById(target);
            
            if (targetElement) {
                if (targetElement.type === 'password') {
                    targetElement.type = 'text';
                    button.innerHTML = `
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L8.464 8.464M9.878 9.878A3 3 0 109.88 9.88m4.242 4.242L15.536 15.536M14.12 14.12a3 3 0 01-4.242-4.242m4.242 4.242L17.657 17.657"></path>
                        </svg>
                    `;
                } else {
                    targetElement.type = 'password';
                    button.innerHTML = `
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    `;
                }
            }
        },
        
        // Handle department change (show/hide Express section)
        handleDepartmentChange: () => {
            const departmentSelect = document.getElementById('department_id');
            const expressSection = document.getElementById('expressSection');
            
            if (departmentSelect && expressSection) {
                const selectedOption = departmentSelect.options[departmentSelect.selectedIndex];
                const departmentName = selectedOption ? selectedOption.textContent.trim() : '';
                
                if (departmentName === 'บัญชี') {
                    expressSection.style.display = 'block';
                } else {
                    expressSection.style.display = 'none';
                    // Clear Express fields
                    document.getElementById('express_username').value = '';
                    document.getElementById('express_code').value = '';
                }
            }
        },
        
        // Handle phone number formatting
        handlePhoneFormat: (event) => {
            let value = event.target.value.replace(/\D/g, '');
            if (value.length >= 3 && value.length <= 6) {
                value = value.slice(0, 3) + '-' + value.slice(3);
            } else if (value.length > 6) {
                value = value.slice(0, 3) + '-' + value.slice(3, 6) + '-' + value.slice(6, 10);
            }
            event.target.value = value;
        }
    };
    
    // Form Actions
    const formActions = {
        // Generate all fields automatically
        generateAll: async () => {
            const button = document.getElementById('generateAllBtn');
            utils.showLoading(button);
            
            try {
                // Generate basic codes
                document.getElementById('employee_code').value = await generators.employeeCode();
                document.getElementById('keycard_id').value = await generators.keycardId();
                
                // Generate computer system data
                if (document.getElementById('first_name_en').value && document.getElementById('last_name_en').value) {
                    autoGenerate.username();
                    await new Promise(resolve => setTimeout(resolve, 200));
                    autoGenerate.email();
                }
                
                // Generate passwords
                document.getElementById('computer_password').value = generators.password();
                document.getElementById('email_password').value = generators.password();
                document.getElementById('password').value = generators.password();
                
                // Generate copier code
                document.getElementById('copier_code').value = generators.copierCode();
                
                // Generate Express data if department is accounting
                const departmentSelect = document.getElementById('department_id');
                const selectedOption = departmentSelect.options[departmentSelect.selectedIndex];
                if (selectedOption && selectedOption.textContent.trim() === 'บัญชี') {
                    document.getElementById('express_username').value = generators.expressUsername();
                    document.getElementById('express_code').value = generators.expressCode();
                }
                
                utils.showNotification('สร้างข้อมูลทั้งหมดสำเร็จ!', 'success');
                
            } catch (error) {
                console.error('Error in generateAll:', error);
                utils.showNotification('เกิดข้อผิดพลาดในการสร้างข้อมูล', 'error');
            } finally {
                utils.hideLoading(button);
            }
        },
        
        // Clear all fields
        clearAll: () => {
            if (confirm('ต้องการล้างข้อมูลทั้งหมดหรือไม่?')) {
                document.getElementById('employeeForm').reset();
                utils.showNotification('ล้างข้อมูลทั้งหมดแล้ว', 'success');
            }
        },
        
        // Show preview
        showPreview: () => {
            const formData = new FormData(document.getElementById('employeeForm'));
            const data = {};
            for (let [key, value] of formData.entries()) {
                data[key] = value;
            }
            
            const previewContent = document.getElementById('previewContent');
            previewContent.innerHTML = `
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <h4 class="font-semibold">ข้อมูลส่วนตัว</h4>
                            <p>ชื่อ: ${data.first_name_th || '-'} ${data.last_name_th || '-'}</p>
                            <p>English: ${data.first_name_en || '-'} ${data.last_name_en || '-'}</p>
                            <p>เบอร์โทร: ${data.phone || '-'}</p>
                        </div>
                        <div>
                            <h4 class="font-semibold">ระบบคอมพิวเตอร์</h4>
                            <p>Username: ${data.username || '-'}</p>
                            <p>Email: ${data.email || '-'}</p>
                            <p>รหัสถ่ายเอกสาร: ${data.copier_code || '-'}</p>
                        </div>
                    </div>
                    ${data.express_username ? `
                        <div>
                            <h4 class="font-semibold">ข้อมูล Express</h4>
                            <p>Username: ${data.express_username}</p>
                        </div>
                    ` : ''}
                </div>
            `;
            
            document.getElementById('previewModal').classList.remove('hidden');
        }
    };
    
    // Event Listeners
    
    // Magic button clicks
    document.addEventListener('click', eventHandlers.handleMagicClick);
    
    // Password toggle clicks
    document.addEventListener('click', eventHandlers.handlePasswordToggle);
    
    // Department change
    document.getElementById('department_id')?.addEventListener('change', eventHandlers.handleDepartmentChange);
    
    // Phone formatting
    document.getElementById('phone')?.addEventListener('input', eventHandlers.handlePhoneFormat);
    
    // English name changes (auto-generate username)
    document.getElementById('first_name_en')?.addEventListener('blur', () => {
        setTimeout(autoGenerate.username, 300);
    });
    
    document.getElementById('last_name_en')?.addEventListener('blur', () => {
        setTimeout(autoGenerate.username, 300);
    });
    
    // Email domain change (auto-generate email)
    document.getElementById('email_domain')?.addEventListener('change', autoGenerate.email);
    
    // Email change (auto-sync login email)
    document.getElementById('email')?.addEventListener('input', (e) => {
        document.getElementById('login_email').value = e.target.value;
    });
    
    // Quick action buttons
    document.getElementById('generateAllBtn')?.addEventListener('click', formActions.generateAll);
    document.getElementById('clearAllBtn')?.addEventListener('click', formActions.clearAll);
    document.getElementById('previewBtn')?.addEventListener('click', formActions.showPreview);
    
    // Generate computer system button
    document.getElementById('generateAllComputerBtn')?.addEventListener('click', async () => {
        const button = event.target;
        utils.showLoading(button);
        
        try {
            autoGenerate.username();
            await new Promise(resolve => setTimeout(resolve, 200));
            autoGenerate.email();
            
            document.getElementById('computer_password').value = generators.password();
            document.getElementById('copier_code').value = generators.copierCode();
            
            utils.showNotification('สร้างข้อมูลระบบคอมพิวเตอร์สำเร็จ!');
        } finally {
            utils.hideLoading(button);
        }
    });
    
    // Form submission
    document.getElementById('employeeForm')?.addEventListener('submit', function(e) {
        const submitBtn = document.getElementById('submitBtn');
        utils.showLoading(submitBtn);
    });
    
    // Initial setup
    setTimeout(() => {
        // Auto-generate initial codes if empty
        if (!document.getElementById('employee_code').value) {
            generators.employeeCode().then(code => {
                document.getElementById('employee_code').value = code;
            });
        }
        
        if (!document.getElementById('keycard_id').value) {
            generators.keycardId().then(id => {
                document.getElementById('keycard_id').value = id;
            });
        }
        
        // Check department for Express section
        eventHandlers.handleDepartmentChange();
        
        console.log('✅ Enhanced Employee Form Ready');
    }, 1000);
});

// Modal functions (global scope)
function closePreviewModal() {
    document.getElementById('previewModal').classList.add('hidden');
}

function submitForm() {
    document.getElementById('employeeForm').submit();
}
</script>
@endsection