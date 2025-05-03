import CryptoJS from 'crypto-js';
import config from '../../config/config';

// 加密密钥（实际使用时应该从环境变量获取）
const ENCRYPTION_KEY = 'your-secure-key-here';

export const security = {
    // 加密数据
    encrypt(data) {
        return CryptoJS.AES.encrypt(JSON.stringify(data), config.encryptionKey).toString();
    },

    // 解密数据
    decrypt(encryptedData) {
        const bytes = CryptoJS.AES.decrypt(encryptedData, config.encryptionKey);
        return JSON.parse(bytes.toString(CryptoJS.enc.Utf8));
    },

    // XSS防护
    sanitize(input) {
        const div = document.createElement('div');
        div.textContent = input;
        return div.innerHTML;
    },

    // 生成CSRF Token
    generateCSRFToken() {
        const token = CryptoJS.lib.WordArray.random(16).toString();
        localStorage.setItem('csrfToken', token);
        return token;
    },

    // 验证CSRF Token
    validateCSRFToken(token) {
        const storedToken = localStorage.getItem('csrfToken');
        return token === storedToken;
    },

    // 安全的localStorage操作
    secureStorage: {
        set(key, value) {
            const encryptedValue = this.encrypt(value);
            localStorage.setItem(key, encryptedValue);
        },

        get(key) {
            const encryptedValue = localStorage.getItem(key);
            if (!encryptedValue) return null;
            return this.decrypt(encryptedValue);
        },

        remove(key) {
            localStorage.removeItem(key);
        }
    }
}; 