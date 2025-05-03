<?php
/**
 * 主题管理主类
 *
 * @package QIoooo
 */

if (!defined('ABSPATH')) {
    exit;
}

// 加载管理类
require_once QIOOOO_DIR . '/admin/class/class-qioooo-settings.php';
require_once QIOOOO_DIR . '/admin/class/class-qioooo-import-export.php';
require_once QIOOOO_DIR . '/admin/class/class-qioooo-documentation.php';
require_once QIOOOO_DIR . '/admin/class/class-qioooo-updater.php';

class QIoooo_Admin {
    /**
     * 单例实例
     */
    private static $instance = null;

    /**
     * 设置类实例
     */
    private $settings;

    /**
     * 更新类实例
     */
    private $updater;

    /**
     * 获取单例实例
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 构造函数
     */
    private function __construct() {
        $this->settings = new QIoooo_Settings();
        $this->updater = new QIoooo_Updater();
        
        $this->init_hooks();
    }

    /**
     * 初始化钩子
     */
    private function init_hooks() {
        // 添加主题设置页面
        add_action('admin_menu', array($this, 'add_theme_page'));
        
        // 注册设置
        add_action('admin_init', array($this, 'register_settings'));
        
        // 加载管理端资源
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        
        // 显示管理通知
        add_action('admin_notices', array($this, 'display_admin_notices'));
        
        // 主题更新检查
        add_action('admin_init', array($this->updater, 'check_updates'));
        
        // 主题文档链接
        add_action('admin_menu', array($this, 'add_documentation_link'));
        
        // 主题设置导入/导出
        add_action('wp_ajax_qioooo_import_settings', array($this, 'handle_settings_import'));
        add_action('wp_ajax_qioooo_export_settings', array($this, 'handle_settings_export'));
        
        // REST API
        add_action('rest_api_init', array($this, 'register_rest_routes'));

        // 添加权限控制
        add_action('admin_init', array($this, 'add_capabilities'));

        // 注册AJAX处理程序
        add_action('wp_ajax_import_theme_preset', array($this, 'handle_import_preset'));
        add_action('wp_ajax_export_theme_preset', array($this, 'handle_export_preset'));
        add_action('wp_ajax_save_settings_version', array($this, 'handle_save_version'));
        add_action('wp_ajax_restore_settings_version', array($this, 'handle_restore_version'));
    }

    /**
     * 添加主题设置页面
     */
    public function add_theme_page() {
        add_theme_page(
            __('QIoooo 主题设置', 'qioooo'),
            __('QIoooo 设置', 'qioooo'),
            'manage_options',
            'qioooo-settings',
            array($this, 'render_settings_page')
        );
    }

    /**
     * 注册设置
     */
    public function register_settings() {
        $this->settings->register();
    }

    /**
     * 加载管理端资源
     */
    public function enqueue_admin_assets($hook) {
        if ('appearance_page_qioooo-settings' !== $hook) {
            return;
        }

        wp_enqueue_media();
        wp_enqueue_style('qioooo-admin', get_template_directory_uri() . '/admin/assets/css/admin.css');
        wp_enqueue_script('qioooo-admin', get_template_directory_uri() . '/admin/assets/js/admin.js', array('jquery'), '1.0.0', true);
        
        wp_localize_script('qioooo-admin', 'qiooooAdmin', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('qioooo_admin_nonce'),
            'strings' => array(
                'importSuccess' => __('设置导入成功', 'qioooo'),
                'importError' => __('设置导入失败', 'qioooo'),
                'exportSuccess' => __('设置导出成功', 'qioooo'),
                'exportError' => __('设置导出失败', 'qioooo'),
            )
        ));
    }

    /**
     * 显示管理通知
     */
    public function display_admin_notices() {
        $screen = get_current_screen();
        if ('appearance_page_qioooo-settings' === $screen->id) {
            $this->settings->display_notices();
        }
    }

    /**
     * 添加文档链接
     */
    public function add_documentation_link() {
        add_submenu_page(
            'themes.php',
            __('QIoooo 文档', 'qioooo'),
            __('QIoooo 文档', 'qioooo'),
            'manage_options',
            'qioooo-documentation',
            array($this, 'render_documentation_page')
        );
    }

    /**
     * 处理设置导入
     */
    public function handle_settings_import() {
        check_ajax_referer('qioooo_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('权限不足', 'qioooo'));
        }

        $file = $_FILES['settings_file'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            wp_send_json_error(__('文件上传失败', 'qioooo'));
        }

        $settings = file_get_contents($file['tmp_name']);
        $settings = json_decode($settings, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            wp_send_json_error(__('无效的JSON文件', 'qioooo'));
        }

        update_option('qioooo_options', $settings);
        wp_send_json_success(__('设置导入成功', 'qioooo'));
    }

    /**
     * 处理设置导出
     */
    public function handle_settings_export() {
        check_ajax_referer('qioooo_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('权限不足', 'qioooo'));
        }

        $settings = get_option('qioooo_options', array());
        $filename = 'qioooo-settings-' . date('Y-m-d') . '.json';
        
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename=' . $filename);
        
        echo json_encode($settings);
        exit;
    }

    /**
     * 注册 REST API 路由
     */
    public function register_rest_routes() {
        register_rest_route('qioooo/v1', '/settings', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_settings'),
            'permission_callback' => array($this, 'check_permission')
        ));
    }

    /**
     * 获取设置
     */
    public function get_settings() {
        return get_option('qioooo_options', array());
    }

    /**
     * 检查权限
     */
    public function check_permission() {
        return current_user_can('manage_options');
    }

    /**
     * 渲染设置页面
     */
    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        include get_template_directory() . '/admin/partials/settings-page.php';
    }

    /**
     * 渲染文档页面
     */
    public function render_documentation_page() {
        include get_template_directory() . '/admin/partials/documentation-page.php';
    }

    /**
     * 检查用户权限
     */
    private function check_user_capability($capability) {
        if (!current_user_can($capability)) {
            wp_die(__('您没有足够的权限执行此操作。', 'qioooo'));
        }
    }

    /**
     * 添加权限控制
     */
    private function add_capabilities() {
        $role = get_role('administrator');
        
        // 添加主题设置权限
        $role->add_cap('manage_theme_settings');
        $role->add_cap('import_theme_presets');
        $role->add_cap('export_theme_presets');
        $role->add_cap('manage_theme_versions');
    }

    /**
     * 注册AJAX处理程序
     */
    public function register_ajax_handlers() {
        add_action('wp_ajax_import_theme_preset', array($this, 'handle_import_preset'));
        add_action('wp_ajax_export_theme_preset', array($this, 'handle_export_preset'));
        add_action('wp_ajax_save_settings_version', array($this, 'handle_save_version'));
        add_action('wp_ajax_restore_settings_version', array($this, 'handle_restore_version'));
    }

    /**
     * 处理预设导入
     */
    public function handle_import_preset() {
        $this->check_user_capability('import_theme_presets');
        
        if (!isset($_FILES['preset_file'])) {
            wp_send_json_error(__('未找到预设文件', 'qioooo'));
        }
        
        $file = $_FILES['preset_file'];
        $content = file_get_contents($file['tmp_name']);
        $preset = json_decode($content, true);
        
        if (!$preset) {
            wp_send_json_error(__('无效的预设文件', 'qioooo'));
        }
        
        update_option('qioooo_options', $preset);
        wp_send_json_success(__('预设导入成功', 'qioooo'));
    }

    /**
     * 处理预设导出
     */
    public function handle_export_preset() {
        $this->check_user_capability('export_theme_presets');
        
        $settings = get_option('qioooo_options');
        $filename = 'qioooo-preset-' . date('Y-m-d') . '.json';
        
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        echo json_encode($settings);
        exit;
    }

    /**
     * 处理保存版本
     */
    public function handle_save_version() {
        $this->check_user_capability('manage_theme_versions');
        
        $description = sanitize_text_field($_POST['description']);
        $settings = get_option('qioooo_options');
        $versions = get_option('qioooo_settings_versions', array());
        
        $version = time();
        $versions[$version] = array(
            'date' => current_time('mysql'),
            'description' => $description,
            'settings' => $settings
        );
        
        update_option('qioooo_settings_versions', $versions);
        wp_send_json_success(__('版本保存成功', 'qioooo'));
    }

    /**
     * 处理恢复版本
     */
    public function handle_restore_version() {
        $this->check_user_capability('manage_theme_versions');
        
        $version = intval($_POST['version']);
        $versions = get_option('qioooo_settings_versions', array());
        
        if (!isset($versions[$version])) {
            wp_send_json_error(__('版本不存在', 'qioooo'));
        }
        
        update_option('qioooo_options', $versions[$version]['settings']);
        wp_send_json_success(__('版本恢复成功', 'qioooo'));
    }
}

// 初始化管理类
QIoooo_Admin::get_instance();

// 注册管理样式和脚本
function qioooo_admin_scripts() {
    wp_register_style('qioooo-admin', get_template_directory_uri() . '/admin/assets/css/admin.css', array(), QIOOOO_VERSION);
    wp_register_script('qioooo-admin', get_template_directory_uri() . '/admin/assets/js/admin.js', array('jquery'), QIOOOO_VERSION, true);
    
    wp_enqueue_style('qioooo-admin');
    wp_enqueue_script('qioooo-admin');
}
add_action('admin_enqueue_scripts', 'qioooo_admin_scripts'); 