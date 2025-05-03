// 配置文件
const config = {
    // 加密密钥（生产环境需要修改）
    encryptionKey: 'your-secure-key-here',
    
    // API 配置
    api: {
        baseUrl: '/api',
        timeout: 30000
    },
    
    // 安全配置
    security: {
        csrfTokenExpiry: 3600,
        sessionTimeout: 7200
    },
    
    // 日志配置
    logging: {
        level: 'error',
        file: 'logs/error.log'
    },
    
    // 缓存配置
    cache: {
        enabled: true,
        ttl: 3600
    }
};

// 导出配置
export default config; 