import { security } from '../utils/security';
import config from '../../config/config';

// API 请求模块
export const api = {
    // 发送GET请求
    async get(endpoint, params = {}) {
        const url = new URL(`${config.api.baseUrl}${endpoint}`);
        Object.keys(params).forEach((key) =>
            url.searchParams.append(key, security.sanitize(params[key]))
        );

        try {
            const response = await fetch(url, {
                headers: {
                    'X-CSRF-Token': security.generateCSRFToken(),
                },
                timeout: config.api.timeout,
            });
            
            if (!response.ok) {
                throw new Error('网络请求失败');
            }
            
            return await response.json();
        } catch (error) {
            console.error('API请求错误:', error);
            throw error;
        }
    },

    // 发送POST请求
    async post(endpoint, data = {}) {
        try {
            const response = await fetch(`${config.api.baseUrl}${endpoint}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': security.generateCSRFToken(),
                },
                body: JSON.stringify(data),
                timeout: config.api.timeout,
            });
            
            if (!response.ok) {
                throw new Error('网络请求失败');
            }
            
            return await response.json();
        } catch (error) {
            console.error('API请求错误:', error);
            throw error;
        }
    }
}; 