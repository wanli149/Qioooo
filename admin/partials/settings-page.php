<?php
/**
 * 主题设置页面模板
 *
 * @package QIoooo
 */

if (!defined('ABSPATH')) {
    exit;
}

// 获取当前标签页
$current_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'general';
$tabs = array(
    'general' => __('常规设置', 'qioooo'),
    'appearance' => __('外观设置', 'qioooo'),
    'features' => __('功能设置', 'qioooo'),
    'import-export' => __('导入/导出', 'qioooo')
);
?>

<div class="wrap qioooo-settings">
    <h1><?php echo esc_html__('QIoooo 主题设置', 'qioooo'); ?></h1>

    <nav class="nav-tab-wrapper">
        <?php foreach ($tabs as $tab => $name) : ?>
            <a class="nav-tab <?php echo $current_tab === $tab ? 'nav-tab-active' : ''; ?>" 
               href="?page=qioooo-settings&tab=<?php echo esc_attr($tab); ?>">
                <?php echo esc_html($name); ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <div class="tab-content">
        <?php
        switch ($current_tab) {
            case 'general':
                include get_template_directory() . '/admin/partials/settings-general.php';
                break;
            case 'appearance':
                include get_template_directory() . '/admin/partials/settings-appearance.php';
                break;
            case 'features':
                include get_template_directory() . '/admin/partials/settings-features.php';
                break;
            case 'import-export':
                include get_template_directory() . '/admin/partials/settings-import-export.php';
                break;
            default:
                include get_template_directory() . '/admin/partials/settings-general.php';
        }
        ?>
    </div>
</div> 