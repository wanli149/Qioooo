// 错误处理类
class ErrorHandler {
    constructor(options = {}) {
        this.options = {
            showUserFriendly: true,
            logToConsole: true,
            ...options
        };
    }

    handle(error, context = '') {
        if (this.options.logToConsole) {
            console.error(`[${context}] Error:`, error);
        }

        if (this.options.showUserFriendly) {
            this.showUserFriendlyError(error);
        }

        // 可以添加错误上报逻辑
        this.reportError(error, context);
    }

    showUserFriendlyError(error) {
        const message = this.getUserFriendlyMessage(error);
        const errorElement = document.createElement('div');
        errorElement.className = 'error-message';
        errorElement.textContent = message;
        
        // 添加到页面
        const container = document.querySelector('.error-container') || document.body;
        const oldError = container.querySelector('.error-message');
        if (oldError) {
            oldError.remove();
        }
        container.insertBefore(errorElement, container.firstChild);
        
        // 自动移除
        setTimeout(() => errorElement.remove(), 5000);
    }

    getUserFriendlyMessage(error) {
        if (error.response) {
            // API 错误
            return error.response.data?.message || '服务器响应错误';
        } else if (error.request) {
            // 网络错误
            return '网络连接错误，请检查您的网络';
        } else {
            // 其他错误
            return error.message || '发生未知错误';
        }
    }

    reportError(error, context) {
        // 可以集成错误上报服务
        if (window.errorReportingService) {
            window.errorReportingService.report({
                error,
                context,
                timestamp: new Date().toISOString(),
                userAgent: navigator.userAgent,
                url: window.location.href
            });
        }
    }
}

// 安全工具类
class SecurityUtils {
    static sanitizeHTML(html) {
        const div = document.createElement('div');
        div.textContent = html;
        return div.innerHTML;
    }

    static validateEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    static validatePassword(password) {
        return password.length >= 8 && 
               /[A-Z]/.test(password) && 
               /[a-z]/.test(password) && 
               /[0-9]/.test(password);
    }

    static generateCSRFToken() {
        return Math.random().toString(36).substring(2) + 
               Date.now().toString(36);
    }

    static validateCSRFToken(token) {
        const storedToken = localStorage.getItem('csrfToken');
        return token === storedToken;
    }

    static escapeRegExp(string) {
        return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    static sanitizeFilename(filename) {
        return filename.replace(/[^a-z0-9.-]/gi, '_').toLowerCase();
    }
}

// 导出工具
export const errorHandler = new ErrorHandler();
export const security = SecurityUtils; 