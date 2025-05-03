<?php
/**
 * 导入导出设置页面模板
 *
 * @package QIoooo
 */

if (!defined('ABSPATH')) {
    exit;
}

$import_export = new QIoooo_Import_Export();
?>

<div class="qioooo-import-export">
    <h2><?php esc_html_e('导入设置', 'qioooo'); ?></h2>
    <p><?php esc_html_e('从 JSON 文件导入主题设置。', 'qioooo'); ?></p>
    
    <form method="post" enctype="multipart/form-data">
        <?php wp_nonce_field('qioooo_import_settings', 'qioooo_import_nonce'); ?>
        <p>
            <input type="file" 
                   name="qioooo_import_file" 
                   accept=".json" 
                   required />
        </p>
        <p>
            <input type="submit" 
                   class="button button-primary" 
                   value="<?php esc_attr_e('导入设置', 'qioooo'); ?>" />
        </p>
    </form>

    <h2><?php esc_html_e('导出设置', 'qioooo'); ?></h2>
    <p><?php esc_html_e('将当前主题设置导出为 JSON 文件。', 'qioooo'); ?></p>
    
    <p>
        <a href="<?php echo esc_url(wp_nonce_url(admin_url('themes.php?page=qioooo-settings&tab=import-export&qioooo_export=1'), 'qioooo_export_settings')); ?>" 
           class="button button-secondary">
            <?php esc_html_e('导出设置', 'qioooo'); ?>
        </a>
    </p>

    <h2><?php esc_html_e('重置设置', 'qioooo'); ?></h2>
    <p><?php esc_html_e('将主题设置重置为默认值。', 'qioooo'); ?></p>
    
    <form method="post" onsubmit="return confirm('<?php esc_attr_e('确定要重置所有设置吗？此操作不可撤销。', 'qioooo'); ?>');">
        <?php wp_nonce_field('qioooo_reset_settings', 'qioooo_reset_nonce'); ?>
        <p>
            <input type="submit" 
                   name="qioooo_reset" 
                   class="button button-secondary" 
                   value="<?php esc_attr_e('重置设置', 'qioooo'); ?>" />
        </p>
    </form>
</div> 