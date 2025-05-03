<?php
/**
 * 主题设置类
 *
 * @package QIoooo
 */

if (!defined('ABSPATH')) {
    exit;
}

class QIoooo_Settings {
    /**
     * 设置选项名称
     */
    private $option_name = 'qioooo_options';

    /**
     * 缓存键前缀
     */
    private $cache_prefix = 'qioooo_settings_';

    /**
     * 构造函数
     */
    public function __construct() {
        $this->init_hooks();
        $this->load_templates();
    }

    /**
     * 加载设置页面模板
     */
    private function load_templates() {
        require_once QIOOOO_DIR . '/admin/partials/settings-general.php';
        require_once QIOOOO_DIR . '/admin/partials/settings-appearance.php';
        require_once QIOOOO_DIR . '/admin/partials/settings-features.php';
        require_once QIOOOO_DIR . '/admin/partials/settings-import-export.php';
    }

    /**
     * 初始化钩子
     */
    private function init_hooks() {
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_notices', array($this, 'display_notices'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('update_option_' . $this->option_name, array($this, 'clear_cache'), 10, 2);
    }

    /**
     * 注册设置页面样式和脚本
     */
    public function enqueue_scripts() {
        wp_register_style('qioooo-settings', get_template_directory_uri() . '/admin/assets/css/settings.css', array(), QIOOOO_VERSION);
        wp_register_script('qioooo-settings', get_template_directory_uri() . '/admin/assets/js/settings.js', array('jquery'), QIOOOO_VERSION, true);
        
        wp_enqueue_style('qioooo-settings');
        wp_enqueue_script('qioooo-settings');
        
        // 添加内联脚本
        wp_add_inline_script('qioooo-settings', 'var qiooooSettings = ' . json_encode(array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('qioooo-nonce'),
            'isMobile' => wp_is_mobile(),
            'isRTL' => is_rtl(),
        )), 'before');
    }

    /**
     * 获取设置选项
     */
    public function get_option($key = '', $default = false) {
        $cache_key = $this->cache_prefix . $key;
        $cached_value = wp_cache_get($cache_key);
        
        if (false !== $cached_value) {
            return $cached_value;
        }
        
        $options = get_option($this->option_name, array());
        
        if (empty($key)) {
            wp_cache_set($this->cache_prefix . 'all', $options);
            return $options;
        }
        
        $value = isset($options[$key]) ? $options[$key] : $default;
        wp_cache_set($cache_key, $value);
        
        return $value;
    }

    /**
     * 更新设置选项
     */
    public function update_option($key, $value) {
        $options = get_option($this->option_name, array());
        $options[$key] = $value;
        
        $result = update_option($this->option_name, $options);
        
        if ($result) {
            wp_cache_delete($this->cache_prefix . $key);
            wp_cache_delete($this->cache_prefix . 'all');
        }
        
        return $result;
    }

    /**
     * 清除缓存
     */
    public function clear_cache($old_value, $new_value) {
        wp_cache_delete($this->cache_prefix . 'all');
    }

    /**
     * 注册设置
     */
    public function register() {
        register_setting('qioooo_settings', $this->option_name, array(
            'type' => 'array',
            'sanitize_callback' => array($this, 'sanitize_settings'),
            'default' => array()
        ));

        // 添加设置部分
        $this->add_sections();
        
        // 添加设置字段
        $this->add_fields();
    }

    /**
     * 清理设置数据
     */
    public function sanitize_settings($input) {
        if (!is_array($input)) {
            return array();
        }

        $sanitized = array();
        
        // 清理文本字段
        $text_fields = array('site_title', 'site_description', 'primary_color', 'theme_language');
        foreach ($text_fields as $field) {
            if (isset($input[$field])) {
                $sanitized[$field] = sanitize_text_field($input[$field]);
            }
        }
        
        // 清理URL字段
        $url_fields = array('logo', 'favicon');
        foreach ($url_fields as $field) {
            if (isset($input[$field])) {
                $sanitized[$field] = esc_url_raw($input[$field]);
            }
        }
        
        // 清理布尔字段
        $bool_fields = array(
            'sticky_header', 'back_to_top', 'lazy_load', 'enable_animations',
            'page_transitions', 'scroll_animations', 'hover_effects', 'rtl_support',
            'enable_compression', 'enable_caching', 'disable_emojis', 'optimize_database',
            'prevent_directory_browsing', 'limit_login_attempts', 'disable_xmlrpc',
            'monitor_suspicious_activity', 'auto_backup', 'add_structured_data',
            'optimize_titles', 'add_meta_description', 'add_meta_keywords',
            'add_canonical_url', 'generate_sitemap', 'add_robots_txt'
        );
        foreach ($bool_fields as $field) {
            $sanitized[$field] = isset($input[$field]) ? (bool) $input[$field] : false;
        }
        
        // 清理选择字段
        $select_fields = array('color_scheme', 'layout', 'sidebar_position', 'theme_preset');
        foreach ($select_fields as $field) {
            if (isset($input[$field])) {
                $sanitized[$field] = sanitize_key($input[$field]);
            }
        }
        
        // 清理自定义脚本
        if (isset($input['custom_scripts'])) {
            $sanitized['custom_scripts'] = wp_kses_post($input['custom_scripts']);
        }
        
        return $sanitized;
    }

    /**
     * 添加设置部分
     */
    private function add_sections() {
        // 常规设置部分
        add_settings_section(
            'qioooo_general_section',
            __('常规设置', 'qioooo'),
            array($this, 'render_general_section'),
            'qioooo-settings'
        );

        // 外观设置部分
        add_settings_section(
            'qioooo_appearance_section',
            __('外观设置', 'qioooo'),
            array($this, 'render_appearance_section'),
            'qioooo-settings'
        );

        // 功能设置部分
        add_settings_section(
            'qioooo_features_section',
            __('功能设置', 'qioooo'),
            array($this, 'render_features_section'),
            'qioooo-settings'
        );
    }

    /**
     * 添加设置字段
     */
    private function add_fields() {
        // Logo 字段
        add_settings_field(
            'qioooo_logo',
            __('网站 Logo', 'qioooo'),
            array($this, 'render_logo_field'),
            'qioooo-settings',
            'qioooo_general_section'
        );

        // 网站图标字段
        add_settings_field(
            'qioooo_favicon',
            __('网站图标', 'qioooo'),
            array($this, 'render_favicon_field'),
            'qioooo-settings',
            'qioooo_general_section'
        );

        // 主题颜色字段
        add_settings_field(
            'qioooo_primary_color',
            __('主题主色', 'qioooo'),
            array($this, 'render_color_field'),
            'qioooo-settings',
            'qioooo_appearance_section',
            array('field' => 'primary_color')
        );

        // 颜色方案字段
        add_settings_field(
            'qioooo_color_scheme',
            __('颜色方案', 'qioooo'),
            array($this, 'render_color_scheme_field'),
            'qioooo-settings',
            'qioooo_appearance_section'
        );

        // 布局设置字段
        add_settings_field(
            'qioooo_layout',
            __('布局设置', 'qioooo'),
            array($this, 'render_layout_field'),
            'qioooo-settings',
            'qioooo_appearance_section'
        );

        // 侧边栏位置字段
        add_settings_field(
            'qioooo_sidebar_position',
            __('侧边栏位置', 'qioooo'),
            array($this, 'render_sidebar_position_field'),
            'qioooo-settings',
            'qioooo_appearance_section'
        );

        // 背景特效字段
        add_settings_field(
            'qioooo_background_effect',
            __('背景特效', 'qioooo'),
            array($this, 'render_background_effect_field'),
            'qioooo-settings',
            'qioooo_appearance_section'
        );

        // 性能优化字段
        add_settings_field(
            'qioooo_performance',
            __('性能优化', 'qioooo'),
            array($this, 'render_performance_field'),
            'qioooo-settings',
            'qioooo_features_section'
        );

        // 安全防护字段
        add_settings_field(
            'qioooo_security',
            __('安全防护', 'qioooo'),
            array($this, 'render_security_field'),
            'qioooo-settings',
            'qioooo_features_section'
        );

        // SEO优化字段
        add_settings_field(
            'qioooo_seo',
            __('SEO优化', 'qioooo'),
            array($this, 'render_seo_field'),
            'qioooo-settings',
            'qioooo_features_section'
        );

        // 主题预设字段
        add_settings_field(
            'qioooo_theme_preset',
            __('主题预设', 'qioooo'),
            array($this, 'render_theme_preset_field'),
            'qioooo-settings',
            'qioooo_appearance_section'
        );

        // 社交链接字段
        add_settings_field(
            'qioooo_social_links',
            __('社交链接', 'qioooo'),
            array($this, 'render_social_links_field'),
            'qioooo-settings',
            'qioooo_features_section'
        );

        // 添加主题预设导入导出功能
        $this->add_preset_fields();

        // 添加设置版本控制功能
        $this->add_version_control();
    }

    /**
     * 添加主题预设导入导出功能
     */
    private function add_preset_fields() {
        add_settings_field(
            'qioooo_preset_import',
            __('导入预设', 'qioooo'),
            array($this, 'render_preset_import_field'),
            'qioooo-settings',
            'qioooo_appearance_section'
        );

        add_settings_field(
            'qioooo_preset_export',
            __('导出预设', 'qioooo'),
            array($this, 'render_preset_export_field'),
            'qioooo-settings',
            'qioooo_appearance_section'
        );
    }

    /**
     * 渲染预设导入字段
     */
    public function render_preset_import_field() {
        ?>
        <div class="preset-import">
            <input type="file" id="preset-import" accept=".json">
            <button type="button" class="button" id="import-preset"><?php _e('导入预设', 'qioooo'); ?></button>
            <p class="description"><?php _e('导入主题预设文件(.json)', 'qioooo'); ?></p>
        </div>
        <?php
    }

    /**
     * 渲染预设导出字段
     */
    public function render_preset_export_field() {
        ?>
        <div class="preset-export">
            <button type="button" class="button" id="export-preset"><?php _e('导出预设', 'qioooo'); ?></button>
            <p class="description"><?php _e('导出当前主题设置为预设文件', 'qioooo'); ?></p>
        </div>
        <?php
    }

    /**
     * 添加设置版本控制功能
     */
    private function add_version_control() {
        add_settings_field(
            'qioooo_settings_version',
            __('设置版本', 'qioooo'),
            array($this, 'render_version_control_field'),
            'qioooo-settings',
            'qioooo_general_section'
        );
    }

    /**
     * 渲染版本控制字段
     */
    public function render_version_control_field() {
        $versions = get_option('qioooo_settings_versions', array());
        ?>
        <div class="version-control">
            <select id="settings-version">
                <?php foreach ($versions as $version => $data): ?>
                    <option value="<?php echo esc_attr($version); ?>">
                        <?php echo esc_html($data['date'] . ' - ' . $data['description']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="button" class="button" id="restore-version"><?php _e('恢复版本', 'qioooo'); ?></button>
            <button type="button" class="button" id="save-version"><?php _e('保存版本', 'qioooo'); ?></button>
            <p class="description"><?php _e('管理主题设置的版本历史', 'qioooo'); ?></p>
        </div>
        <?php
    }

    /**
     * 渲染常规设置部分
     */
    public function render_general_section() {
        echo '<p>' . esc_html__('配置主题的基本设置。', 'qioooo') . '</p>';
    }

    /**
     * 渲染外观设置部分
     */
    public function render_appearance_section() {
        echo '<p>' . esc_html__('自定义主题的外观。', 'qioooo') . '</p>';
    }

    /**
     * 渲染功能设置部分
     */
    public function render_features_section() {
        echo '<p>' . esc_html__('启用或禁用主题功能。', 'qioooo') . '</p>';
    }

    /**
     * 渲染 Logo 字段
     */
    public function render_logo_field() {
        $options = get_option($this->option_name);
        $logo = isset($options['logo']) ? $options['logo'] : '';
        ?>
        <input type="text" id="qioooo_logo" name="<?php echo esc_attr($this->option_name); ?>[logo]" value="<?php echo esc_attr($logo); ?>" />
        <button type="button" class="button" id="upload_logo_button"><?php esc_html_e('上传 Logo', 'qioooo'); ?></button>
        <?php
    }

    /**
     * 渲染网站图标字段
     */
    public function render_favicon_field() {
        $options = get_option($this->option_name);
        $favicon = isset($options['favicon']) ? $options['favicon'] : '';
        ?>
        <input type="text" id="qioooo_favicon" name="<?php echo esc_attr($this->option_name); ?>[favicon]" value="<?php echo esc_attr($favicon); ?>" />
        <button type="button" class="button" id="upload_favicon_button"><?php esc_html_e('上传图标', 'qioooo'); ?></button>
        <?php
    }

    /**
     * 渲染颜色字段
     */
    public function render_color_field($args) {
        $options = get_option($this->option_name);
        $color = isset($options[$args['field']]) ? $options[$args['field']] : '';
        ?>
        <input type="color" id="qioooo_<?php echo esc_attr($args['field']); ?>" name="<?php echo esc_attr($this->option_name); ?>[<?php echo esc_attr($args['field']); ?>]" value="<?php echo esc_attr($color); ?>" />
        <?php
    }

    /**
     * 渲染颜色方案字段
     */
    public function render_color_scheme_field() {
        $options = get_option('qioooo_options');
        $color_scheme = isset($options['color_scheme']) ? $options['color_scheme'] : 'light';
        ?>
        <div class="color-schemes">
            <div class="color-scheme-option">
                <input type="radio" id="color-scheme-light" name="qioooo_options[color_scheme]" value="light" <?php checked($color_scheme, 'light'); ?>>
                <label for="color-scheme-light">
                    <span class="color-scheme-preview light"></span>
                    <span class="color-scheme-name"><?php _e('浅色', 'qioooo'); ?></span>
                </label>
            </div>
            <div class="color-scheme-option">
                <input type="radio" id="color-scheme-dark" name="qioooo_options[color_scheme]" value="dark" <?php checked($color_scheme, 'dark'); ?>>
                <label for="color-scheme-dark">
                    <span class="color-scheme-preview dark"></span>
                    <span class="color-scheme-name"><?php _e('深色', 'qioooo'); ?></span>
                </label>
            </div>
            <div class="color-scheme-option">
                <input type="radio" id="color-scheme-auto" name="qioooo_options[color_scheme]" value="auto" <?php checked($color_scheme, 'auto'); ?>>
                <label for="color-scheme-auto">
                    <span class="color-scheme-preview auto"></span>
                    <span class="color-scheme-name"><?php _e('自动', 'qioooo'); ?></span>
                </label>
            </div>
        </div>
        <?php
    }

    /**
     * 渲染布局设置字段
     */
    public function render_layout_field() {
        $options = get_option('qioooo_options');
        $layout = isset($options['layout']) ? $options['layout'] : 'wide';
        ?>
        <div class="layout-options">
            <div class="layout-option">
                <input type="radio" id="layout-wide" name="qioooo_options[layout]" value="wide" <?php checked($layout, 'wide'); ?>>
                <label for="layout-wide"><?php _e('宽屏', 'qioooo'); ?></label>
            </div>
            <div class="layout-option">
                <input type="radio" id="layout-boxed" name="qioooo_options[layout]" value="boxed" <?php checked($layout, 'boxed'); ?>>
                <label for="layout-boxed"><?php _e('盒式', 'qioooo'); ?></label>
            </div>
        </div>
        <?php
    }

    /**
     * 渲染侧边栏位置字段
     */
    public function render_sidebar_position_field() {
        $options = get_option('qioooo_options');
        $sidebar_position = isset($options['sidebar_position']) ? $options['sidebar_position'] : 'right';
        ?>
        <div class="sidebar-options">
            <div class="sidebar-option">
                <input type="radio" id="sidebar-right" name="qioooo_options[sidebar_position]" value="right" <?php checked($sidebar_position, 'right'); ?>>
                <label for="sidebar-right"><?php _e('右侧', 'qioooo'); ?></label>
            </div>
            <div class="sidebar-option">
                <input type="radio" id="sidebar-left" name="qioooo_options[sidebar_position]" value="left" <?php checked($sidebar_position, 'left'); ?>>
                <label for="sidebar-left"><?php _e('左侧', 'qioooo'); ?></label>
            </div>
            <div class="sidebar-option">
                <input type="radio" id="sidebar-none" name="qioooo_options[sidebar_position]" value="none" <?php checked($sidebar_position, 'none'); ?>>
                <label for="sidebar-none"><?php _e('无侧边栏', 'qioooo'); ?></label>
            </div>
        </div>
        <?php
    }

    /**
     * 渲染背景特效字段
     */
    public function render_background_effect_field() {
        $options = get_option('qioooo_options');
        $background_effect = isset($options['background_effect']) ? $options['background_effect'] : 'none';
        ?>
        <div class="background-effects">
            <div class="effect-option">
                <input type="radio" id="bg-effect-none" name="qioooo_options[background_effect]" value="none" <?php checked($background_effect, 'none'); ?>>
                <label for="bg-effect-none">
                    <span class="effect-preview none"></span>
                    <span class="effect-name"><?php _e('无特效', 'qioooo'); ?></span>
                </label>
            </div>
            <div class="effect-option">
                <input type="radio" id="bg-effect-gradient-wave" name="qioooo_options[background_effect]" value="gradient-wave" <?php checked($background_effect, 'gradient-wave'); ?>>
                <label for="bg-effect-gradient-wave">
                    <span class="effect-preview gradient-wave"></span>
                    <span class="effect-name"><?php _e('渐变波纹', 'qioooo'); ?></span>
                </label>
            </div>
            <div class="effect-option">
                <input type="radio" id="bg-effect-grid" name="qioooo_options[background_effect]" value="grid" <?php checked($background_effect, 'grid'); ?>>
                <label for="bg-effect-grid">
                    <span class="effect-preview grid"></span>
                    <span class="effect-name"><?php _e('网格动画', 'qioooo'); ?></span>
                </label>
            </div>
            <div class="effect-option">
                <input type="radio" id="bg-effect-particles" name="qioooo_options[background_effect]" value="particles" <?php checked($background_effect, 'particles'); ?>>
                <label for="bg-effect-particles">
                    <span class="effect-preview particles"></span>
                    <span class="effect-name"><?php _e('粒子效果', 'qioooo'); ?></span>
                </label>
            </div>
        </div>
        <?php
    }

    /**
     * 渲染性能优化字段
     */
    public function render_performance_field() {
        $options = get_option('qioooo_options');
        ?>
        <div class="performance-options">
            <div class="option-group">
                <input type="checkbox" id="enable_compression" name="qioooo_options[enable_compression]" value="1" <?php checked(isset($options['enable_compression']) ? $options['enable_compression'] : 0, 1); ?>>
                <label for="enable_compression"><?php _e('启用资源压缩', 'qioooo'); ?></label>
            </div>
            <div class="option-group">
                <input type="checkbox" id="enable_caching" name="qioooo_options[enable_caching]" value="1" <?php checked(isset($options['enable_caching']) ? $options['enable_caching'] : 0, 1); ?>>
                <label for="enable_caching"><?php _e('启用浏览器缓存', 'qioooo'); ?></label>
            </div>
            <div class="option-group">
                <input type="checkbox" id="disable_emojis" name="qioooo_options[disable_emojis]" value="1" <?php checked(isset($options['disable_emojis']) ? $options['disable_emojis'] : 0, 1); ?>>
                <label for="disable_emojis"><?php _e('禁用表情符号', 'qioooo'); ?></label>
            </div>
            <div class="option-group">
                <input type="checkbox" id="optimize_database" name="qioooo_options[optimize_database]" value="1" <?php checked(isset($options['optimize_database']) ? $options['optimize_database'] : 0, 1); ?>>
                <label for="optimize_database"><?php _e('自动优化数据库', 'qioooo'); ?></label>
            </div>
        </div>
        <?php
    }

    /**
     * 渲染安全防护字段
     */
    public function render_security_field() {
        $options = get_option('qioooo_options');
        ?>
        <div class="security-options">
            <div class="option-group">
                <input type="checkbox" id="prevent_directory_browsing" name="qioooo_options[prevent_directory_browsing]" value="1" <?php checked(isset($options['prevent_directory_browsing']) ? $options['prevent_directory_browsing'] : 0, 1); ?>>
                <label for="prevent_directory_browsing"><?php _e('防止目录浏览', 'qioooo'); ?></label>
            </div>
            <div class="option-group">
                <input type="checkbox" id="limit_login_attempts" name="qioooo_options[limit_login_attempts]" value="1" <?php checked(isset($options['limit_login_attempts']) ? $options['limit_login_attempts'] : 0, 1); ?>>
                <label for="limit_login_attempts"><?php _e('限制登录尝试', 'qioooo'); ?></label>
            </div>
            <div class="option-group">
                <input type="checkbox" id="disable_xmlrpc" name="qioooo_options[disable_xmlrpc]" value="1" <?php checked(isset($options['disable_xmlrpc']) ? $options['disable_xmlrpc'] : 0, 1); ?>>
                <label for="disable_xmlrpc"><?php _e('禁用XML-RPC', 'qioooo'); ?></label>
            </div>
            <div class="option-group">
                <input type="checkbox" id="monitor_suspicious_activity" name="qioooo_options[monitor_suspicious_activity]" value="1" <?php checked(isset($options['monitor_suspicious_activity']) ? $options['monitor_suspicious_activity'] : 0, 1); ?>>
                <label for="monitor_suspicious_activity"><?php _e('监控可疑活动', 'qioooo'); ?></label>
            </div>
            <div class="option-group">
                <input type="checkbox" id="auto_backup" name="qioooo_options[auto_backup]" value="1" <?php checked(isset($options['auto_backup']) ? $options['auto_backup'] : 0, 1); ?>>
                <label for="auto_backup"><?php _e('自动备份数据库', 'qioooo'); ?></label>
            </div>
        </div>
        <?php
    }

    /**
     * 渲染SEO优化字段
     */
    public function render_seo_field() {
        $options = get_option('qioooo_options');
        ?>
        <div class="seo-options">
            <div class="option-group">
                <input type="checkbox" id="add_structured_data" name="qioooo_options[add_structured_data]" value="1" <?php checked(isset($options['add_structured_data']) ? $options['add_structured_data'] : 0, 1); ?>>
                <label for="add_structured_data"><?php _e('添加结构化数据', 'qioooo'); ?></label>
            </div>
            <div class="option-group">
                <input type="checkbox" id="optimize_titles" name="qioooo_options[optimize_titles]" value="1" <?php checked(isset($options['optimize_titles']) ? $options['optimize_titles'] : 0, 1); ?>>
                <label for="optimize_titles"><?php _e('优化标题', 'qioooo'); ?></label>
            </div>
            <div class="option-group">
                <input type="checkbox" id="add_meta_description" name="qioooo_options[add_meta_description]" value="1" <?php checked(isset($options['add_meta_description']) ? $options['add_meta_description'] : 0, 1); ?>>
                <label for="add_meta_description"><?php _e('添加meta描述', 'qioooo'); ?></label>
            </div>
            <div class="option-group">
                <input type="checkbox" id="add_meta_keywords" name="qioooo_options[add_meta_keywords]" value="1" <?php checked(isset($options['add_meta_keywords']) ? $options['add_meta_keywords'] : 0, 1); ?>>
                <label for="add_meta_keywords"><?php _e('添加meta关键词', 'qioooo'); ?></label>
            </div>
            <div class="option-group">
                <input type="checkbox" id="add_canonical_url" name="qioooo_options[add_canonical_url]" value="1" <?php checked(isset($options['add_canonical_url']) ? $options['add_canonical_url'] : 0, 1); ?>>
                <label for="add_canonical_url"><?php _e('添加规范链接', 'qioooo'); ?></label>
            </div>
            <div class="option-group">
                <input type="checkbox" id="generate_sitemap" name="qioooo_options[generate_sitemap]" value="1" <?php checked(isset($options['generate_sitemap']) ? $options['generate_sitemap'] : 0, 1); ?>>
                <label for="generate_sitemap"><?php _e('生成站点地图', 'qioooo'); ?></label>
            </div>
            <div class="option-group">
                <input type="checkbox" id="add_robots_txt" name="qioooo_options[add_robots_txt]" value="1" <?php checked(isset($options['add_robots_txt']) ? $options['add_robots_txt'] : 0, 1); ?>>
                <label for="add_robots_txt"><?php _e('添加robots.txt', 'qioooo'); ?></label>
            </div>
        </div>
        <?php
    }

    /**
     * 渲染主题预设字段
     */
    public function render_theme_preset_field() {
        $options = get_option('qioooo_options');
        $theme_preset = isset($options['theme_preset']) ? $options['theme_preset'] : 'default';
        ?>
        <div class="theme-presets">
            <div class="preset-option">
                <input type="radio" id="preset-default" name="qioooo_options[theme_preset]" value="default" <?php checked($theme_preset, 'default'); ?>>
                <label for="preset-default">
                    <span class="preset-preview default"></span>
                    <span class="preset-name"><?php _e('默认预设', 'qioooo'); ?></span>
                </label>
            </div>
            <div class="preset-option">
                <input type="radio" id="preset-minimal" name="qioooo_options[theme_preset]" value="minimal" <?php checked($theme_preset, 'minimal'); ?>>
                <label for="preset-minimal">
                    <span class="preset-preview minimal"></span>
                    <span class="preset-name"><?php _e('简约风格', 'qioooo'); ?></span>
                </label>
            </div>
            <div class="preset-option">
                <input type="radio" id="preset-dark" name="qioooo_options[theme_preset]" value="dark" <?php checked($theme_preset, 'dark'); ?>>
                <label for="preset-dark">
                    <span class="preset-preview dark"></span>
                    <span class="preset-name"><?php _e('暗黑风格', 'qioooo'); ?></span>
                </label>
            </div>
            <div class="preset-option">
                <input type="radio" id="preset-colorful" name="qioooo_options[theme_preset]" value="colorful" <?php checked($theme_preset, 'colorful'); ?>>
                <label for="preset-colorful">
                    <span class="preset-preview colorful"></span>
                    <span class="preset-name"><?php _e('多彩风格', 'qioooo'); ?></span>
                </label>
            </div>
        </div>
        <?php
    }

    /**
     * 渲染社交链接字段
     */
    public function render_social_links_field() {
        $options = get_option($this->option_name);
        $social_links = isset($options['social_links']) ? $options['social_links'] : array();
        ?>
        <div class="social-links-container">
            <?php
            $social_platforms = array(
                'weibo' => __('微博', 'qioooo'),
                'wechat' => __('微信', 'qioooo'),
                'qq' => __('QQ', 'qioooo'),
                'github' => __('GitHub', 'qioooo'),
            );

            foreach ($social_platforms as $platform => $label) {
                $url = isset($social_links[$platform]) ? $social_links[$platform] : '';
                ?>
                <div class="social-link-field">
                    <label for="qioooo_social_<?php echo esc_attr($platform); ?>"><?php echo esc_html($label); ?></label>
                    <input type="url" id="qioooo_social_<?php echo esc_attr($platform); ?>" name="<?php echo esc_attr($this->option_name); ?>[social_links][<?php echo esc_attr($platform); ?>]" value="<?php echo esc_url($url); ?>" />
                </div>
                <?php
            }
            ?>
        </div>
        <?php
    }

    /**
     * 显示通知
     */
    public function display_notices() {
        settings_errors('qioooo_settings');
    }
} 