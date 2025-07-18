/**
 * Dashboard JavaScript Utilities
 * IT Management System - Darker Theme
 */

class ITMSDashboard {
    constructor() {
        this.refreshInterval = 30000; // 30 seconds
        this.refreshTimer = null;
        this.userRole = this.getUserRole();
        this.colorTheme = {
            primaryRed: '#B54544',
            primaryOrange: '#E6952A',
            accentRed: '#A33E3D',
            accentOrange: '#CC7F1F',
            success: '#1E7E34',
            warning: '#E0A800',
            danger: '#C82333',
            info: '#138496',
            textPrimary: '#1A252F',
            textSecondary: '#5A6C7D'
        };
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.initializeAnimations();
        this.initializeTooltips();
        this.setupAutoRefresh();
        this.setupKeyboardNavigation();
        this.initializeRoleSpecificFeatures();
        this.setupThemeHandling();
        console.log(`🚀 ITMS Dashboard initialized for ${this.userRole} with darker theme`);
    }

    getUserRole() {
        // Get user role from various sources
        const roleElement = document.querySelector('[data-user-role]');
        const metaRole = document.querySelector('meta[name="user-role"]');
        const bodyClass = document.body.classList;
        
        if (roleElement) return roleElement.dataset.userRole;
        if (metaRole) return metaRole.content;
        if (bodyClass.contains('super-admin')) return 'super_admin';
        if (bodyClass.contains('it-admin')) return 'it_admin';
        return 'employee';
    }

    setupEventListeners() {
        // Stat card click handlers with enhanced animation
        document.querySelectorAll('.stat-card').forEach(card => {
            card.addEventListener('click', this.handleStatCardClick.bind(this));
            card.addEventListener('mouseenter', this.handleStatCardHover.bind(this));
            card.addEventListener('mouseleave', this.handleStatCardLeave.bind(this));
        });

        // Quick action button handlers
        document.querySelectorAll('.dashboard-quick-action, .btn-outline-primary').forEach(btn => {
            btn.addEventListener('click', this.handleQuickActionClick.bind(this));
        });

        // Notification handlers
        document.querySelectorAll('[data-notification]').forEach(element => {
            element.addEventListener('click', this.handleNotificationClick.bind(this));
        });

        // Enhanced hover effects for cards
        document.querySelectorAll('.card').forEach(card => {
            card.addEventListener('mouseenter', this.enhanceCardHover.bind(this));
            card.addEventListener('mouseleave', this.resetCardHover.bind(this));
        });

        // Role-specific event listeners
        this.setupRoleSpecificListeners();

        // Window resize handler
        window.addEventListener('resize', this.handleWindowResize.bind(this));
    }

    setupRoleSpecificListeners() {
        switch (this.userRole) {
            case 'super_admin':
                this.setupSuperAdminListeners();
                break;
            case 'it_admin':
                this.setupITAdminListeners();
                break;
            case 'employee':
                this.setupEmployeeListeners();
                break;
        }
    }

    setupSuperAdminListeners() {
        // System status monitoring with enhanced visuals
        document.querySelectorAll('.system-status-item').forEach(item => {
            item.addEventListener('click', this.handleSystemStatusClick.bind(this));
            this.addSystemStatusPulse(item);
        });

        // User management shortcuts
        document.querySelectorAll('[data-admin-action]').forEach(btn => {
            btn.addEventListener('click', this.handleAdminAction.bind(this));
        });

        // Enhanced password viewing functionality
        document.querySelectorAll('[data-view-password]').forEach(btn => {
            btn.addEventListener('click', this.handlePasswordView.bind(this));
        });
    }

    setupITAdminListeners() {
        // Task management with priority indicators
        document.querySelectorAll('.pending-task-item, .list-group-item').forEach(item => {
            item.addEventListener('click', this.handleTaskClick.bind(this));
            this.addTaskPriorityIndicator(item);
        });

        // Quick approval buttons with confirmation
        document.querySelectorAll('[data-quick-approve]').forEach(btn => {
            btn.addEventListener('click', this.handleQuickApproval.bind(this));
        });

        // Performance metrics refresh
        document.querySelectorAll('[data-refresh-metrics]').forEach(btn => {
            btn.addEventListener('click', this.refreshPerformanceMetrics.bind(this));
        });
    }

    setupEmployeeListeners() {
        // Personal request tracking with status updates
        document.querySelectorAll('.my-request-item, .list-group-item').forEach(item => {
            item.addEventListener('click', this.handleRequestClick.bind(this));
            this.addRequestStatusIndicator(item);
        });

        // Help and support with enhanced feedback
        document.querySelectorAll('[data-help-action]').forEach(btn => {
            btn.addEventListener('click', this.handleHelpAction.bind(this));
        });

        // Profile management shortcuts
        document.querySelectorAll('[data-profile-action]').forEach(btn => {
            btn.addEventListener('click', this.handleProfileAction.bind(this));
        });
    }

    initializeAnimations() {
        // Enhanced progress bar animations
        this.animateProgressBars();
        
        // Enhanced counter animations
        this.animateCounters();
        
        // Setup intersection observer for scroll animations
        this.setupScrollAnimations();

        // Initialize card entrance animations
        this.animateCardEntrance();

        // Setup theme-aware animations
        this.setupThemeAnimations();
    }

    animateProgressBars() {
        const progressBars = document.querySelectorAll('.progress-bar');
        progressBars.forEach((bar, index) => {
            const width = bar.style.width || bar.getAttribute('data-width') || '0%';
            bar.style.width = '0%';
            
            setTimeout(() => {
                bar.style.transition = 'width 2s cubic-bezier(0.4, 0, 0.2, 1)';
                bar.style.width = width;
                
                // Add completion effect
                setTimeout(() => {
                    this.addProgressCompletionEffect(bar);
                }, 2000);
            }, index * 300 + 600);
        });
    }

    animateCounters() {
        const counters = document.querySelectorAll('[data-counter]');
        counters.forEach((counter, index) => {
            const target = parseInt(counter.getAttribute('data-counter')) || parseInt(counter.textContent);
            const duration = 2500;
            const step = target / (duration / 16);
            let current = 0;

            const updateCounter = () => {
                current += step;
                if (current < target) {
                    counter.textContent = Math.floor(current);
                    requestAnimationFrame(updateCounter);
                } else {
                    counter.textContent = target;
                    this.addCounterCompletionEffect(counter);
                }
            };

            setTimeout(updateCounter, index * 200 + 400);
        });
    }

    setupScrollAnimations() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                    
                    // Add specific animations based on element type
                    if (entry.target.classList.contains('stat-card')) {
                        setTimeout(() => {
                            entry.target.style.transform = 'translateY(0)';
                        }, 100);
                    }
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });

        document.querySelectorAll('.card, .dashboard-quick-action').forEach(element => {
            observer.observe(element);
        });
    }

    animateCardEntrance() {
        const cards = document.querySelectorAll('.card');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            
            setTimeout(() => {
                card.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 150 + 200);
        });
    }

    handleStatCardClick(event) {
        const card = event.currentTarget;
        const cardType = card.getAttribute('data-type');
        
        // Enhanced click animation with darker theme colors
        card.style.transform = 'scale(0.95)';
        card.style.boxShadow = `0 8px 25px rgba(181, 69, 68, 0.3)`;
        
        setTimeout(() => {
            card.style.transform = '';
            card.style.boxShadow = '';
        }, 200);

        // Add ripple effect
        this.createRippleEffect(card, event, this.colorTheme.primaryOrange);

        // Role-based navigation
        this.navigateBasedOnRole(cardType);
    }

    handleStatCardHover(event) {
        const card = event.currentTarget;
        const icon = card.querySelector('i');
        
        if (icon) {
            icon.style.color = this.colorTheme.accentRed;
            icon.style.transform = 'scale(1.15) rotate(5deg)';
        }
    }

    handleStatCardLeave(event) {
        const card = event.currentTarget;
        const icon = card.querySelector('i');
        
        if (icon) {
            icon.style.color = this.colorTheme.primaryOrange;
            icon.style.transform = 'scale(1) rotate(0deg)';
        }
    }

    handleQuickActionClick(event) {
        const button = event.currentTarget;
        const action = button.getAttribute('data-action') || 
                     button.href?.split('/').pop() || 
                     'default';
        
        // Enhanced loading state with darker theme
        this.setButtonLoading(button, true);
        
        // Add click effect
        this.createRippleEffect(button, event, this.colorTheme.primaryRed);
        
        // Handle action based on user role
        setTimeout(() => {
            this.setButtonLoading(button, false);
            this.handleRoleAction(action);
        }, 800);
    }

    enhanceCardHover(event) {
        const card = event.currentTarget;
        card.style.borderColor = `rgba(181, 69, 68, 0.3)`;
    }

    resetCardHover(event) {
        const card = event.currentTarget;
        card.style.borderColor = '';
    }

    createRippleEffect(element, event, color = this.colorTheme.primaryOrange) {
        const ripple = document.createElement('span');
        const rect = element.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = event.clientX - rect.left - size / 2;
        const y = event.clientY - rect.top - size / 2;

        ripple.style.cssText = `
            position: absolute;
            width: ${size}px;
            height: ${size}px;
            left: ${x}px;
            top: ${y}px;
            background: radial-gradient(circle, ${color}33 0%, transparent 70%);
            border-radius: 50%;
            transform: scale(0);
            animation: ripple 0.6s linear;
            pointer-events: none;
            z-index: 1;
        `;

        element.style.position = 'relative';
        element.style.overflow = 'hidden';
        element.appendChild(ripple);

        // Add ripple animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(2);
                    opacity: 0;
                }
            }
        `;
        
        if (!document.querySelector('#ripple-styles')) {
            style.id = 'ripple-styles';
            document.head.appendChild(style);
        }

        setTimeout(() => {
            if (ripple.parentNode) {
                ripple.parentNode.removeChild(ripple);
            }
        }, 600);
    }

    handleRoleAction(action) {
        switch (this.userRole) {
            case 'super_admin':
            case 'it_admin':
                this.handleAdminAction(action);
                break;
            case 'employee':
                this.handleEmployeeAction(action);
                break;
        }
    }

    handleAdminAction(action) {
        const actionMap = {
            'manage-employees': '/employees',
            'manage-equipment': '/equipment',
            'manage-agreements': '/agreements',
            'manage-repairs': '/repairs',
            'manage-service-requests': '/service-requests',
            'manage-departments': '/departments',
            'view-reports': '/reports',
            'system-settings': '/admin/settings',
            'view-passwords': this.showPasswordModal.bind(this)
        };

        const handler = actionMap[action];
        if (typeof handler === 'function') {
            handler();
        } else if (handler) {
            this.navigateTo(handler);
        } else {
            this.showNotification('ฟีเจอร์นี้กำลังพัฒนา', 'info');
        }
    }

    handleEmployeeAction(action) {
        const actionMap = {
            'view-profile': '/my/profile',
            'sign-agreements': '/my/agreements',
            'report-repair': '/my/repairs/create',
            'request-service': '/my/service-requests/create',
            'track-status': '/my/status',
            'view-history': '/my/history',
            'get-help': '/help',
            'contact-it': '/help/contact'
        };

        const url = actionMap[action];
        if (url) {
            this.navigateTo(url);
        } else {
            this.showNotification('ฟีเจอร์นี้กำลังพัฒนา', 'info');
        }
    }

    async refreshDashboardData() {
        try {
            this.showNotification('กำลังอัปเดตข้อมูล...', 'info');
            
            const response = await fetch('/api/dashboard/stats', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            
            const data = await response.json();
            
            if (data.success) {
                this.updateStatsDisplay(data.data);
                this.showNotification('ข้อมูลอัปเดตแล้ว', 'success');
            } else {
                throw new Error(data.message || 'Unknown error');
            }
        } catch (error) {
            console.error('Error refreshing dashboard data:', error);
            this.showNotification('ไม่สามารถอัปเดตข้อมูลได้', 'error');
        }
    }

    updateStatsDisplay(newStats) {
        Object.keys(newStats).forEach(key => {
            const element = document.querySelector(`[data-stat="${key}"] [data-counter]`);
            if (element) {
                const currentValue = parseInt(element.textContent);
                const newValue = newStats[key].count;
                
                if (currentValue !== newValue) {
                    this.animateValueChange(element, currentValue, newValue);
                }
            }
        });
    }

    animateValueChange(element, from, to) {
        const duration = 1500;
        const steps = 30;
        const stepValue = (to - from) / steps;
        let current = from;
        let step = 0;

        const interval = setInterval(() => {
            step++;
            current += stepValue;
            
            if (step >= steps) {
                element.textContent = to;
                clearInterval(interval);
                
                // Add highlight effect with darker theme colors
                const parent = element.closest('.stat-card') || element.parentElement;
                const originalBorder = parent.style.borderColor;
                parent.style.borderColor = this.colorTheme.primaryOrange;
                parent.style.boxShadow = `0 0 20px rgba(230, 149, 42, 0.4)`;
                
                setTimeout(() => {
                    parent.style.borderColor = originalBorder;
                    parent.style.boxShadow = '';
                }, 2000);
            } else {
                element.textContent = Math.round(current);
            }
        }, duration / steps);
    }

    setButtonLoading(button, loading = true) {
        if (loading) {
            button.disabled = true;
            button.classList.add('loading');
            const originalText = button.innerHTML;
            button.setAttribute('data-original-text', originalText);
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>กำลังโหลด...';
            button.style.background = `linear-gradient(45deg, ${this.colorTheme.primaryRed}, ${this.colorTheme.primaryOrange})`;
            button.style.color = '#FFFFFF';
        } else {
            button.disabled = false;
            button.classList.remove('loading');
            const originalText = button.getAttribute('data-original-text');
            if (originalText) {
                button.innerHTML = originalText;
            }
            button.style.background = '';
            button.style.color = '';
        }
    }

    navigateTo(url) {
        // Add enhanced transition effect
        const overlay = document.createElement('div');
        overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, ${this.colorTheme.primaryRed}, ${this.colorTheme.primaryOrange});
            opacity: 0;
            z-index: 9999;
            transition: opacity 0.3s ease;
            pointer-events: none;
        `;
        
        document.body.appendChild(overlay);
        
        setTimeout(() => {
            overlay.style.opacity = '0.3';
        }, 10);
        
        setTimeout(() => {
            window.location.href = url;
        }, 300);
    }

    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        const icons = {
            success: 'check-circle',
            error: 'exclamation-circle', 
            warning: 'exclamation-triangle',
            info: 'info-circle'
        };
        
        const colors = {
            success: this.colorTheme.success,
            error: this.colorTheme.danger,
            warning: this.colorTheme.warning,
            info: this.colorTheme.primaryRed
        };

        notification.className = `alert alert-${type} notification-toast position-fixed`;
        notification.innerHTML = `
            <i class="fas fa-${icons[type]} me-2"></i>
            ${message}
            <button type="button" class="btn-close btn-close-white ms-2" style="font-size: 0.8rem;"></button>
        `;
        
        notification.style.cssText = `
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 320px;
            max-width: 400px;
            opacity: 0;
            transform: translateX(100%);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border-left: 5px solid ${colors[type]};
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        `;
        
        document.body.appendChild(notification);
        
        // Close button functionality
        const closeBtn = notification.querySelector('.btn-close');
        closeBtn.addEventListener('click', () => {
            this.hideNotification(notification);
        });
        
        // Animate in
        setTimeout(() => {
            notification.style.opacity = '1';
            notification.style.transform = 'translateX(0)';
        }, 100);
        
        // Auto remove
        setTimeout(() => {
            this.hideNotification(notification);
        }, type === 'error' ? 5000 : 3000);
    }

    hideNotification(notification) {
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (document.body.contains(notification)) {
                document.body.removeChild(notification);
            }
        }, 400);
    }

    setupAutoRefresh() {
        if (this.userRole === 'super_admin' || this.userRole === 'it_admin') {
            this.refreshTimer = setInterval(() => {
                this.refreshDashboardData();
            }, this.refreshInterval);
            
            // Add refresh indicator
            this.addRefreshIndicator();
        }
    }

    addRefreshIndicator() {
        const indicator = document.createElement('div');
        indicator.id = 'refresh-indicator';
        indicator.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 12px;
            height: 12px;
            background: ${this.colorTheme.success};
            border-radius: 50%;
            opacity: 0.7;
            animation: pulse 2s infinite;
            z-index: 1000;
        `;
        
        document.body.appendChild(indicator);
    }

    setupKeyboardNavigation() {
        document.addEventListener('keydown', (event) => {
            // Enhanced keyboard shortcuts with darker theme feedback
            if ((event.ctrlKey || event.metaKey) && event.key === 'k') {
                event.preventDefault();
                this.focusSearchInput();
            }
            
            if (event.key === 'Escape') {
                this.closeModals();
            }
            
            if (event.target.classList.contains('stat-card')) {
                this.handleCardNavigation(event);
            }

            this.handleRoleShortcuts(event);
        });
    }

    setupThemeHandling() {
        // Detect theme preference
        const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        this.handleThemeChange(mediaQuery);
        mediaQuery.addListener(this.handleThemeChange.bind(this));
        
        // Handle system theme changes
        if (mediaQuery.matches) {
            this.applyDarkTheme();
        }
    }

    handleThemeChange(mediaQuery) {
        if (mediaQuery.matches) {
            this.applyDarkTheme();
        } else {
            this.applyLightTheme();
        }
    }

    applyDarkTheme() {
        document.body.classList.add('dark-theme');
        this.colorTheme.primaryRed = '#D47573';
        this.colorTheme.primaryOrange = '#FFC947';
    }

    applyLightTheme() {
        document.body.classList.remove('dark-theme');
        this.colorTheme.primaryRed = '#B54544';
        this.colorTheme.primaryOrange = '#E6952A';
    }

    addProgressCompletionEffect(progressBar) {
        const sparkle = document.createElement('div');
        sparkle.style.cssText = `
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            width: 8px;
            height: 8px;
            background: #FFFFFF;
            border-radius: 50%;
            animation: sparkle 0.6s ease-out;
        `;
        
        progressBar.style.position = 'relative';
        progressBar.appendChild(sparkle);
        
        setTimeout(() => {
            if (sparkle.parentNode) {
                sparkle.parentNode.removeChild(sparkle);
            }
        }, 600);
    }

    addCounterCompletionEffect(counter) {
        counter.style.textShadow = `0 0 10px ${this.colorTheme.primaryOrange}`;
        setTimeout(() => {
            counter.style.textShadow = '';
        }, 1000);
    }

    setupThemeAnimations() {
        const style = document.createElement('style');
        style.textContent = `
            @keyframes sparkle {
                0% { transform: translateY(-50%) scale(0); opacity: 1; }
                50% { transform: translateY(-50%) scale(1.5); opacity: 0.8; }
                100% { transform: translateY(-50%) scale(0); opacity: 0; }
            }
        `;
        document.head.appendChild(style);
    }

    addSystemStatusPulse(item) {
        const status = item.querySelector('.status-indicator');
        if (status) {
            status.style.position = 'relative';
        }
    }

    addTaskPriorityIndicator(item) {
        const badge = item.querySelector('.badge');
        if (badge) {
            const priority = parseInt(badge.textContent) || 0;
            if (priority > 10) {
                badge.style.animation = 'pulse 1s infinite';
            }
        }
    }

    addRequestStatusIndicator(item) {
        const statusText = item.textContent.toLowerCase();
        if (statusText.includes('รอ') || statusText.includes('pending')) {
            item.style.borderLeft = `4px solid ${this.colorTheme.warning}`;
        }
    }

    handleWindowResize() {
        // Adjust layout for mobile
        if (window.innerWidth <= 768) {
            document.querySelectorAll('.stat-card').forEach(card => {
                card.style.marginBottom = '15px';
            });
        }
    }

    showPasswordModal() {
        this.showNotification('ฟีเจอร์การดูรหัสผ่านสำหรับ Super Admin เท่านั้น', 'warning');
    }

    initializeRoleSpecificFeatures() {
        switch (this.userRole) {
            case 'super_admin':
                this.initializeSuperAdminFeatures();
                break;
            case 'it_admin':
                this.initializeITAdminFeatures();
                break;
            case 'employee':
                this.initializeEmployeeFeatures();
                break;
        }
    }

    initializeSuperAdminFeatures() {
        console.log('🔧 Super Admin features initialized');
        this.monitorSystemStatus();
    }

    initializeITAdminFeatures() {
        console.log('🛠️ IT Admin features initialized');
        this.setupTaskQueueMonitoring();
    }

    initializeEmployeeFeatures() {
        console.log('👤 Employee features initialized');
        this.setupPersonalDashboard();
    }

    monitorSystemStatus() {
        // Implementation for system monitoring
        setInterval(() => {
            // Simulate system check
            const indicators = document.querySelectorAll('.status-indicator');
            indicators.forEach(indicator => {
                if (Math.random() > 0.95) { // 5% chance of status change
                    this.updateSystemStatus(indicator);
                }
            });
        }, 10000);
    }

    updateSystemStatus(indicator) {
        const statuses = ['online', 'maintenance', 'offline'];
        const currentStatus = indicator.className.split(' ').find(c => statuses.includes(c));
        const newStatus = statuses[Math.floor(Math.random() * statuses.length)];
        
        if (currentStatus !== newStatus) {
            indicator.classList.remove(currentStatus);
            indicator.classList.add(newStatus);
            
            const statusText = {
                online: 'ออนไลน์',
                maintenance: 'บำรุงรักษา', 
                offline: 'ออฟไลน์'
            };
            
            this.showNotification(`สถานะระบบเปลี่ยนเป็น: ${statusText[newStatus]}`, 'info');
        }
    }

    initializeTooltips() {
        // Initialize enhanced tooltips with darker theme
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl, {
                delay: { show: 500, hide: 100 },
                customClass: 'tooltip-dark-theme'
            });
        });
    }

    // Utility methods
    formatThaiDate(date) {
        const thaiMonths = [
            'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
            'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
        ];
        
        const d = new Date(date);
        return `${d.getDate()} ${thaiMonths[d.getMonth()]} ${d.getFullYear() + 543}`;
    }

    formatNumber(num) {
        return new Intl.NumberFormat('th-TH').format(num);
    }

    // Cleanup
    destroy() {
        if (this.refreshTimer) {
            clearInterval(this.refreshTimer);
        }
        
        // Remove event listeners
        document.removeEventListener('keydown', this.handleKeyDown);
        window.removeEventListener('resize', this.handleWindowResize);
        
        console.log('🧹 ITMS Dashboard cleaned up');
    }
}

// Initialize dashboard when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Check if we're on dashboard page
    if (document.querySelector('.dashboard-content') || 
        window.location.pathname === '/dashboard' ||
        document.body.classList.contains('dashboard-page')) {
        
        window.itmsDashboard = new ITMSDashboard();
        
        // Add global CSS for enhanced animations
        const style = document.createElement('style');
        style.textContent = `
            .tooltip-dark-theme {
                --bs-tooltip-bg: ${window.itmsDashboard?.colorTheme?.primaryRed || '#B54544'};
                --bs-tooltip-color: #FFFFFF;
            }
            
            @keyframes pulse {
                0% { opacity: 1; }
                50% { opacity: 0.7; }
                100% { opacity: 1; }
            }
        `;
        document.head.appendChild(style);
    }
    
    // Global error handling with enhanced notifications
    window.addEventListener('error', function(e) {
        console.error('ITMS Dashboard Error:', e.error);
        if (window.itmsDashboard) {
            window.itmsDashboard.showNotification('เกิดข้อผิดพลาดในระบบ', 'error');
        }
    });
    
    // Handle unhandled promise rejections
    window.addEventListener('unhandledrejection', function(e) {
        console.error('Unhandled Promise Rejection:', e.reason);
        if (window.itmsDashboard) {
            window.itmsDashboard.showNotification('เกิดข้อผิดพลาดในการเชื่อมต่อ', 'error');
        }
    });
});

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ITMSDashboard;
}
