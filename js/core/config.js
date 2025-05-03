// 全局配置
export const config = {
    apiBaseUrl: '/api',
    theme: {
        colorScheme: localStorage.getItem('colorScheme') || 'default',
        darkMode: localStorage.getItem('darkMode') === 'true',
    },
    // 主题预设
    presets: {
        default: {
            name: '默认主题',
            colors: {
                primary: '#007bff',
                secondary: '#6c757d',
                success: '#28a745',
                danger: '#dc3545',
                warning: '#ffc107',
                info: '#17a2b8',
                light: '#f8f9fa',
                dark: '#343a40'
            },
            fonts: {
                base: 'system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial',
                heading: 'inherit'
            },
            spacing: {
                base: '1rem',
                small: '0.5rem',
                large: '2rem'
            }
        },
        dark: {
            name: '暗黑主题',
            colors: {
                primary: '#0d6efd',
                secondary: '#6c757d',
                success: '#198754',
                danger: '#dc3545',
                warning: '#ffc107',
                info: '#0dcaf0',
                light: '#f8f9fa',
                dark: '#212529'
            },
            fonts: {
                base: 'system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial',
                heading: 'inherit'
            },
            spacing: {
                base: '1rem',
                small: '0.5rem',
                large: '2rem'
            }
        }
    }
}; 