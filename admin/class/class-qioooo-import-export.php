<?php
/**
 * 主题设置导入导出类
 *
 * @package QIoooo
 */

if (!defined('ABSPATH')) {
    exit;
}

class QIoooo_Import_Export {
    /**
     * 设置选项名称
     */
    private $option_name = 'qioooo_options';

    /**
     * 构造函数
     */
    public function __construct() {
        $this->init_hooks();
        $this->load_templates();
    }

    /**
     * 加载导入导出页面模板
     */
    private function load_templates() {
        require_once QIOOOO_DIR . '/admin/partials/settings-import-export.php';
    }

    /**
     * 初始化钩子
     */
    private function init_hooks() {
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_notices', array($this, 'display_notices'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    /**
     * 注册导入导出页面样式和脚本
     */
    public function enqueue_scripts() {
        wp_register_style('qioooo-import-export', get_template_directory_uri() . '/admin/assets/css/import-export.css', array(), QIOOOO_VERSION);
        wp_register_script('qioooo-import-export', get_template_directory_uri() . '/admin/assets/js/import-export.js', array('jquery'), QIOOOO_VERSION, true);
        
        wp_enqueue_style('qioooo-import-export');
        wp_enqueue_script('qioooo-import-export');
    }

    /**
     * 处理导入
     */
    public function handle_import() {
        if (!isset($_POST['qioooo_import_nonce']) || !wp_verify_nonce($_POST['qioooo_import_nonce'], 'qioooo_import_settings')) {
            return;
        }

        if (!current_user_can('manage_options')) {
            wp_die(__('您没有足够的权限执行此操作。', 'qioooo'));
        }

        if (!isset($_FILES['qioooo_import_file'])) {
            add_settings_error(
                'qioooo_import',
                'import_error',
                __('请选择要导入的文件。', 'qioooo'),
                'error'
            );
            return;
        }

        $file = $_FILES['qioooo_import_file'];
        
        // 检查文件类型
        $file_type = wp_check_filetype($file['name'], array('json' => 'application/json'));
        if ($file_type['ext'] !== 'json') {
            add_settings_error(
                'qioooo_import',
                'import_error',
                __('请上传有效的 JSON 文件。', 'qioooo'),
                'error'
            );
            return;
        }

        // 读取文件内容
        $content = file_get_contents($file['tmp_name']);
        if ($content === false) {
            add_settings_error(
                'qioooo_import',
                'import_error',
                __('无法读取文件内容。', 'qioooo'),
                'error'
            );
            return;
        }

        // 解析 JSON
        $settings = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            add_settings_error(
                'qioooo_import',
                'import_error',
                __('无效的 JSON 格式。', 'qioooo'),
                'error'
            );
            return;
        }

        // 验证设置
        if (!$this->validate_settings($settings)) {
            add_settings_error(
                'qioooo_import',
                'import_error',
                __('导入的设置无效。', 'qioooo'),
                'error'
            );
            return;
        }

        // 更新设置
        update_option($this->option_name, $settings);

        add_settings_error(
            'qioooo_import',
            'import_success',
            __('设置已成功导入。', 'qioooo'),
            'updated'
        );
    }

    /**
     * 处理导出
     */
    public function handle_export() {
        if (!isset($_GET['qioooo_export']) || !wp_verify_nonce($_GET['_wpnonce'], 'qioooo_export_settings')) {
            return;
        }

        if (!current_user_can('manage_options')) {
            wp_die(__('您没有足够的权限执行此操作。', 'qioooo'));
        }

        $settings = get_option($this->option_name, array());
        
        // 设置文件头
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename=qioooo-settings-' . date('Y-m-d') . '.json');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo json_encode($settings, JSON_PRETTY_PRINT);
        exit;
    }

    /**
     * 验证设置
     */
    private function validate_settings($settings) {
        if (!is_array($settings)) {
            return false;
        }

        // 检查必需字段
        $required_fields = array('logo', 'favicon', 'primary_color', 'layout');
        foreach ($required_fields as $field) {
            if (!isset($settings[$field])) {
                return false;
            }
        }

        return true;
    }

    /**
     * 显示通知
     */
    public function display_notices() {
        settings_errors('qioooo_import');
    }

    /**
     * 渲染导入导出表单
     */
    public function render_form() {
        ?>
        <div class="qioooo-import-export">
            <h3><?php esc_html_e('导入设置', 'qioooo'); ?></h3>
            <form method="post" enctype="multipart/form-data">
                <?php wp_nonce_field('qioooo_import_settings', 'qioooo_import_nonce'); ?>
                <p>
                    <input type="file" name="qioooo_import_file" accept=".json" />
                    <input type="submit" class="button button-primary" value="<?php esc_attr_e('导入设置', 'qioooo'); ?>" />
                </p>
            </form>

            <h3><?php esc_html_e('导出设置', 'qioooo'); ?></h3>
            <p>
                <a href="<?php echo esc_url(wp_nonce_url(admin_url('themes.php?page=qioooo-settings&qioooo_export=1'), 'qioooo_export_settings')); ?>" class="button button-secondary">
                    <?php esc_html_e('导出设置', 'qioooo'); ?>
                </a>
            </p>
        </div>
        <?php
    }
}