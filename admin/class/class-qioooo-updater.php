<?php
/**
 * 主题更新类
 *
 * @package QIoooo
 */

if (!defined('ABSPATH')) {
    exit;
}

class QIoooo_Updater {
    /**
     * 主题版本
     */
    private $version;

    /**
     * 更新检查间隔（秒）
     */
    private $check_interval = 43200; // 12小时

    /**
     * 构造函数
     */
    public function __construct() {
        $theme = wp_get_theme();
        $this->version = $theme->get('Version');
        
        $this->init_hooks();
        $this->load_templates();
    }

    /**
     * 加载更新页面模板
     */
    private function load_templates() {
        require_once QIOOOO_DIR . '/admin/partials/updater.php';
    }

    /**
     * 初始化钩子
     */
    private function init_hooks() {
        add_action('admin_init', array($this, 'check_for_updates'));
        add_action('admin_notices', array($this, 'display_update_notices'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    /**
     * 注册更新页面样式和脚本
     */
    public function enqueue_scripts() {
        wp_register_style('qioooo-updater', get_template_directory_uri() . '/admin/assets/css/updater.css', array(), QIOOOO_VERSION);
        wp_register_script('qioooo-updater', get_template_directory_uri() . '/admin/assets/js/updater.js', array('jquery'), QIOOOO_VERSION, true);
        
        wp_enqueue_style('qioooo-updater');
        wp_enqueue_script('qioooo-updater');
    }

    /**
     * 检查更新
     */
    public function check_for_updates() {
        $last_check = get_transient('qioooo_update_check');
        
        if (false === $last_check) {
            $this->perform_update_check();
            set_transient('qioooo_update_check', time(), $this->check_interval);
        }
    }

    /**
     * 执行更新检查
     */
    private function perform_update_check() {
        $update_info = $this->get_update_info();
        
        if (is_wp_error($update_info)) {
            $this->log_error($update_info->get_error_message());
            return;
        }

        if (version_compare($this->version, $update_info['version'], '<')) {
            $this->notify_update_available($update_info);
        }
    }

    /**
     * 获取更新信息
     */
    private function get_update_info() {
        $response = wp_remote_get('https://api.qioooo.com/theme/update-check', array(
            'timeout' => 15,
            'body' => array(
                'version' => $this->version,
                'site_url' => home_url(),
            ),
        ));

        if (is_wp_error($response)) {
            return $response;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return new WP_Error('json_error', __('无效的更新信息', 'qioooo'));
        }

        return $data;
    }

    /**
     * 通知有可用更新
     */
    private function notify_update_available($update_info) {
        $message = sprintf(
            __('QIoooo 主题有新版本 %s 可用。 <a href="%s">查看详情</a>', 'qioooo'),
            $update_info['version'],
            admin_url('themes.php?page=qioooo-settings')
        );

        add_settings_error(
            'qioooo_updates',
            'update_available',
            $message,
            'notice-warning'
        );
    }

    /**
     * 更新完成后
     */
    public function after_update($upgrader_object, $options) {
        if ($options['action'] === 'update' && $options['type'] === 'theme') {
            if (isset($options['themes']) && in_array('qioooo', $options['themes'])) {
                $this->clear_update_cache();
                $this->log_update();
            }
        }
    }

    /**
     * 清除更新缓存
     */
    private function clear_update_cache() {
        delete_transient('qioooo_update_check');
    }

    /**
     * 记录更新日志
     */
    private function log_update() {
        $log_entry = sprintf(
            '[%s] 主题更新到版本 %s',
            current_time('mysql'),
            $this->version
        );

        $this->write_log($log_entry);
    }

    /**
     * 记录错误
     */
    private function log_error($message) {
        $log_entry = sprintf(
            '[%s] 更新检查错误: %s',
            current_time('mysql'),
            $message
        );

        $this->write_log($log_entry);
    }

    /**
     * 写入日志
     */
    private function write_log($message) {
        $log_file = WP_CONTENT_DIR . '/qioooo-update.log';
        error_log($message . "\n", 3, $log_file);
    }
} 