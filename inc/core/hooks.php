<?php
/**
 * WordPress钩子
 */

// 注册小工具
add_action('widgets_init', 'qioooo_register_widgets');

// 注册小工具区域
function qioooo_register_sidebar() {
    // 侧边栏
    register_sidebar(array(
        'name' => __('侧边栏', 'qioooo'),
        'id' => 'sidebar-1',
        'description' => __('主侧边栏', 'qioooo'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ));

    // 页脚小工具区域
    register_sidebar(array(
        'name' => __('页脚小工具区域', 'qioooo'),
        'id' => 'footer-1',
        'description' => __('页脚小工具区域', 'qioooo'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ));

    // 首页小工具区域
    register_sidebar(array(
        'name' => __('首页小工具区域', 'qioooo'),
        'id' => 'home-1',
        'description' => __('首页小工具区域', 'qioooo'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ));
}
add_action('widgets_init', 'qioooo_register_sidebar');

/**
 * 注册小工具资源
 */
function qioooo_register_widget_assets() {
    // 注册小工具样式
    wp_register_style(
        'qioooo-widgets',
        get_template_directory_uri() . '/assets/css/widgets.css',
        array(),
        QIOOOO_VERSION
    );

    // 注册小工具脚本
    wp_register_script(
        'qioooo-widgets',
        get_template_directory_uri() . '/assets/js/widgets.js',
        array('jquery'),
        QIOOOO_VERSION,
        true
    );
}
add_action('init', 'qioooo_register_widget_assets');

/**
 * 加载小工具资源
 */
function qioooo_enqueue_widget_assets() {
    // 只在需要时加载小工具资源
    if (is_active_widget(false, false, 'qioooo_recent_posts') ||
        is_active_widget(false, false, 'qioooo_popular_posts') ||
        is_active_widget(false, false, 'qioooo_categories') ||
        is_active_widget(false, false, 'qioooo_tags') ||
        is_active_widget(false, false, 'qioooo_search') ||
        is_active_widget(false, false, 'qioooo_about') ||
        is_active_widget(false, false, 'qioooo_social_links') ||
        is_active_widget(false, false, 'qioooo_newsletter') ||
        is_active_widget(false, false, 'qioooo_archives') ||
        is_active_widget(false, false, 'qioooo_calendar') ||
        is_active_widget(false, false, 'qioooo_recent_comments') ||
        is_active_widget(false, false, 'qioooo_random_posts')) {
        wp_enqueue_style('qioooo-widgets');
        wp_enqueue_script('qioooo-widgets');
    }
}
add_action('wp_enqueue_scripts', 'qioooo_enqueue_widget_assets');

/**
 * 在后台加载小工具资源
 */
function qioooo_admin_enqueue_widget_assets($hook) {
    if ('widgets.php' !== $hook) {
        return;
    }
    wp_enqueue_style('qioooo-widgets');
    wp_enqueue_script('qioooo-widgets');
}
add_action('admin_enqueue_scripts', 'qioooo_admin_enqueue_widget_assets');

// 注册小工具自定义器设置
function qioooo_register_widget_customizer_settings($wp_customize) {
    // 小工具区域设置
    $wp_customize->add_section('qioooo_widgets', array(
        'title' => __('小工具设置', 'qioooo'),
        'priority' => 30,
    ));

    // 侧边栏设置
    $wp_customize->add_setting('qioooo_sidebar_width', array(
        'default' => '300',
        'sanitize_callback' => 'absint',
    ));

    $wp_customize->add_control('qioooo_sidebar_width', array(
        'label' => __('侧边栏宽度(px)', 'qioooo'),
        'section' => 'qioooo_widgets',
        'type' => 'number',
        'input_attrs' => array(
            'min' => 200,
            'max' => 500,
            'step' => 10,
        ),
    ));

    // 页脚小工具区域设置
    $wp_customize->add_setting('qioooo_footer_widgets_columns', array(
        'default' => '3',
        'sanitize_callback' => 'absint',
    ));

    $wp_customize->add_control('qioooo_footer_widgets_columns', array(
        'label' => __('页脚小工具列数', 'qioooo'),
        'section' => 'qioooo_widgets',
        'type' => 'select',
        'choices' => array(
            '1' => __('1列', 'qioooo'),
            '2' => __('2列', 'qioooo'),
            '3' => __('3列', 'qioooo'),
            '4' => __('4列', 'qioooo'),
        ),
    ));

    // 首页小工具区域设置
    $wp_customize->add_setting('qioooo_home_widgets_columns', array(
        'default' => '3',
        'sanitize_callback' => 'absint',
    ));

    $wp_customize->add_control('qioooo_home_widgets_columns', array(
        'label' => __('首页小工具列数', 'qioooo'),
        'section' => 'qioooo_widgets',
        'type' => 'select',
        'choices' => array(
            '1' => __('1列', 'qioooo'),
            '2' => __('2列', 'qioooo'),
            '3' => __('3列', 'qioooo'),
            '4' => __('4列', 'qioooo'),
        ),
    ));
}
add_action('customize_register', 'qioooo_register_widget_customizer_settings');

// 注册小工具AJAX处理
function qioooo_register_widget_ajax() {
    // 订阅处理
    add_action('wp_ajax_qioooo_newsletter_subscribe', 'qioooo_newsletter_subscribe');
    add_action('wp_ajax_nopriv_qioooo_newsletter_subscribe', 'qioooo_newsletter_subscribe');
}
add_action('init', 'qioooo_register_widget_ajax');

// 订阅处理函数
function qioooo_newsletter_subscribe() {
    check_ajax_referer('qioooo_newsletter_nonce', 'nonce');

    $email = sanitize_email($_POST['email']);

    if (!is_email($email)) {
        wp_send_json_error(__('请输入有效的邮箱地址', 'qioooo'));
    }

    // 这里可以添加订阅处理逻辑
    // 例如保存到数据库或发送到邮件服务

    wp_send_json_success(__('订阅成功！', 'qioooo'));
} 