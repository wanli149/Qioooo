<?php
/**
 * 小工具测试加载器
 */

// 退出如果直接访问
if (!defined('ABSPATH')) {
    exit;
}

// 加载测试文件
require_once dirname(__FILE__) . '/widgets-test.php';
require_once dirname(__FILE__) . '/widgets-test-page.php';

// 注册测试样式和脚本
function qioooo_register_widgets_test_assets() {
    // 注册测试样式
    wp_register_style(
        'qioooo-widgets-test',
        get_template_directory_uri() . '/tests/widgets-test.css',
        array(),
        QIOOOO_VERSION
    );

    // 注册测试脚本
    wp_register_script(
        'qioooo-widgets-test',
        get_template_directory_uri() . '/tests/widgets-test.js',
        array('jquery'),
        QIOOOO_VERSION,
        true
    );
}
add_action('init', 'qioooo_register_widgets_test_assets');

// 加载测试样式和脚本
function qioooo_enqueue_widgets_test_assets($hook) {
    if ('appearance_page_qioooo-widgets-test' !== $hook) {
        return;
    }

    wp_enqueue_style('qioooo-widgets-test');
    wp_enqueue_script('qioooo-widgets-test');
}
add_action('admin_enqueue_scripts', 'qioooo_enqueue_widgets_test_assets');

// 添加测试页面到主题设置
function qioooo_add_widgets_test_link($links) {
    $links[] = '<a href="' . admin_url('themes.php?page=qioooo-widgets-test') . '">小工具测试</a>';
    return $links;
}
add_filter('theme_action_links_' . get_stylesheet(), 'qioooo_add_widgets_test_link'); 