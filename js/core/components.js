// 组件基类
class BaseComponent {
    constructor(element, options = {}) {
        this.element = element;
        this.options = options;
        this.init();
    }

    init() {
        this.bindEvents();
        this.onMount();
    }

    bindEvents() {
        // 基础事件绑定
    }

    onMount() {
        // 组件挂载时执行
    }

    onUnmount() {
        // 组件卸载时执行
    }

    update() {
        // 更新组件状态
    }
}

// 主题管理器组件
class ThemeManager extends BaseComponent {
    constructor(element, options) {
        super(element, options);
        this.currentTheme = null;
        this.initTheme();
    }

    initTheme() {
        const colorScheme = security.secureStorage.get('colorScheme') || 'default';
        const darkMode = security.secureStorage.get('darkMode') === 'true';
        this.applyTheme(colorScheme, darkMode);
    }

    applyTheme(colorScheme, darkMode) {
        const root = document.documentElement;
        root.setAttribute('data-theme', colorScheme);
        root.setAttribute('data-dark-mode', darkMode);
        this.currentTheme = { colorScheme, darkMode };
    }

    toggleDarkMode() {
        const darkMode = !this.currentTheme.darkMode;
        security.secureStorage.set('darkMode', darkMode);
        this.applyTheme(this.currentTheme.colorScheme, darkMode);
    }
}

// 导航组件
class Navigation extends BaseComponent {
    constructor(element, options) {
        super(element, options);
        this.menuToggle = element.querySelector('.menu-toggle');
        this.menu = element.querySelector('.main-navigation');
    }

    bindEvents() {
        this.menuToggle.addEventListener('click', () => this.toggleMenu());
        window.addEventListener('resize', debounce(() => this.handleResize(), 100));
    }

    toggleMenu() {
        this.menu.classList.toggle('toggled');
        this.menuToggle.setAttribute('aria-expanded', 
            this.menu.classList.contains('toggled'));
    }

    handleResize() {
        if (window.innerWidth > 768) {
            this.menu.classList.remove('toggled');
            this.menuToggle.setAttribute('aria-expanded', 'false');
        }
    }
}

// 搜索组件
class Search extends BaseComponent {
    constructor(element, options) {
        super(element, options);
        this.form = element.querySelector('.search-form');
        this.input = element.querySelector('.search-field');
    }

    bindEvents() {
        this.form.addEventListener('submit', (e) => this.handleSubmit(e));
        this.input.addEventListener('input', debounce(() => this.handleInput(), 300));
    }

    handleSubmit(e) {
        e.preventDefault();
        const query = this.input.value.trim();
        if (query) {
            window.location.href = `/search?q=${encodeURIComponent(query)}`;
        }
    }

    handleInput() {
        const query = this.input.value.trim();
        if (query.length >= 2) {
            this.searchSuggestions(query);
        }
    }

    async searchSuggestions(query) {
        try {
            const response = await api.get(`/search/suggestions?q=${query}`);
            this.showSuggestions(response.data);
        } catch (error) {
            errorHandler.handle(error);
        }
    }
}

// 导出组件
export const Components = {
    ThemeManager,
    Navigation,
    Search
}; 