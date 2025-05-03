import { config } from '../core/config';
import { debounce, isMobile } from '../utils';
import { security } from '../utils/security';

class ThemeManager {
    constructor() {
        this.currentTheme = null;
        this.isDarkMode = config.theme.darkMode;
        this.colorScheme = config.theme.colorScheme;
        this.init();
    }

    init() {
        const colorScheme = security.secureStorage.get('colorScheme') || 'default';
        const darkMode = security.secureStorage.get('darkMode') === 'true';
        
        this.applyColorScheme(colorScheme);
        this.applyDarkMode(darkMode);
        this.setupEventListeners();
    }

    setupEventListeners() {
        // 监听系统主题变化
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            if (config.theme.autoDarkMode) {
                this.toggleDarkMode(e.matches);
            }
        });

        // 优化滚动性能
        let ticking = false;
        window.addEventListener('scroll', () => {
            if (!ticking) {
                requestAnimationFrame(() => {
                    this.handleScroll();
                    ticking = false;
                });
                ticking = true;
            }
        });

        // 移动端特定事件
        if (isMobile()) {
            this.setupMobileEvents();
        }
    }

    setupMobileEvents() {
        // 优化触摸事件
        document.addEventListener('touchstart', this.handleTouchStart, { passive: true });
        document.addEventListener('touchend', this.handleTouchEnd, { passive: true });

        // 优化滚动体验
        document.documentElement.style.scrollBehavior = 'smooth';
        document.documentElement.style.webkitOverflowScrolling = 'touch';
    }

    handleTouchStart = (e) => {
        const target = e.target.closest('.btn, .option-group, .preset-option');
        if (target) {
            target.classList.add('active');
        }
    };

    handleTouchEnd = (e) => {
        const target = e.target.closest('.btn, .option-group, .preset-option');
        if (target) {
            target.classList.remove('active');
        }
    };

    handleScroll = debounce(() => {
        const sections = document.querySelectorAll('.settings-section');
        sections.forEach(section => {
            const rect = section.getBoundingClientRect();
            const isVisible = rect.top < window.innerHeight && rect.bottom >= 0;
            section.classList.toggle('visible', isVisible);
        });
    }, 100);

    applyColorScheme(scheme) {
        document.documentElement.setAttribute('data-theme', scheme);
        this.colorScheme = scheme;
        security.secureStorage.set('colorScheme', scheme);
    }

    toggleDarkMode(isDark = !this.isDarkMode) {
        document.documentElement.classList.toggle('dark-mode', isDark);
        this.isDarkMode = isDark;
        security.secureStorage.set('darkMode', isDark);
    }

    applyDarkMode(isDark) {
        document.documentElement.classList.toggle('dark-mode', isDark);
    }

    applyThemePreset(presetName) {
        const preset = config.presets[presetName];
        if (!preset) return;

        this.currentTheme = preset;
        this.applyThemeSettings(preset);
    }

    applyThemeSettings(settings) {
        const root = document.documentElement;
        
        // 应用颜色
        Object.entries(settings.colors).forEach(([key, value]) => {
            root.style.setProperty(`--color-${key}`, value);
        });

        // 应用字体
        Object.entries(settings.fonts).forEach(([key, value]) => {
            root.style.setProperty(`--font-${key}`, value);
        });

        // 应用间距
        Object.entries(settings.spacing).forEach(([key, value]) => {
            root.style.setProperty(`--spacing-${key}`, value);
        });
    }

    exportSettings() {
        return {
            colorScheme: this.colorScheme,
            darkMode: this.isDarkMode,
            currentTheme: this.currentTheme
        };
    }

    importSettings(settings) {
        if (settings.colorScheme) {
            this.applyColorScheme(settings.colorScheme);
        }
        if (settings.darkMode !== undefined) {
            this.toggleDarkMode(settings.darkMode);
        }
        if (settings.currentTheme) {
            this.applyThemeSettings(settings.currentTheme);
        }
    }
}

export const themeManager = new ThemeManager(); 