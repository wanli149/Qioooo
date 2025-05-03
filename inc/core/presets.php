<?php
/**
 * 主题预设管理
 *
 * @package QIoooo
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 添加主题预设
 */
function qioooo_add_theme_presets() {
    $presets = array(
        'default' => array(
            'name' => '默认主题',
            'colors' => array(
                'primary' => '#007bff',
                'secondary' => '#6c757d',
                'success' => '#28a745',
                'danger' => '#dc3545',
                'warning' => '#ffc107',
                'info' => '#17a2b8',
                'light' => '#f8f9fa',
                'dark' => '#343a40'
            ),
            'typography' => array(
                'font_family' => 'system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
                'font_size' => '16px',
                'line_height' => '1.5'
            ),
            'layout' => array(
                'container_width' => '1200px',
                'sidebar_position' => 'right',
                'header_style' => 'default'
            )
        ),
        'minimal' => array(
            'name' => '简约风格',
            'colors' => array(
                'primary' => '#000000',
                'secondary' => '#666666',
                'success' => '#333333',
                'danger' => '#ff0000',
                'warning' => '#ff9900',
                'info' => '#0066cc',
                'light' => '#ffffff',
                'dark' => '#000000'
            ),
            'typography' => array(
                'font_family' => 'Helvetica, Arial, sans-serif',
                'font_size' => '14px',
                'line_height' => '1.6'
            ),
            'layout' => array(
                'container_width' => '1000px',
                'sidebar_position' => 'none',
                'header_style' => 'minimal'
            )
        ),
        'dark' => array(
            'name' => '暗黑模式',
            'colors' => array(
                'primary' => '#00ff00',
                'secondary' => '#666666',
                'success' => '#00cc00',
                'danger' => '#ff0000',
                'warning' => '#ff9900',
                'info' => '#00ccff',
                'light' => '#333333',
                'dark' => '#000000'
            ),
            'typography' => array(
                'font_family' => 'system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
                'font_size' => '16px',
                'line_height' => '1.5'
            ),
            'layout' => array(
                'container_width' => '1200px',
                'sidebar_position' => 'right',
                'header_style' => 'dark'
            )
        ),
        'retro' => array(
            'name' => '复古风格',
            'colors' => array(
                'primary' => '#8b4513',
                'secondary' => '#a0522d',
                'success' => '#556b2f',
                'danger' => '#8b0000',
                'warning' => '#daa520',
                'info' => '#4682b4',
                'light' => '#f5f5dc',
                'dark' => '#2f4f4f'
            ),
            'typography' => array(
                'font_family' => '"Times New Roman", Times, serif',
                'font_size' => '16px',
                'line_height' => '1.6'
            ),
            'layout' => array(
                'container_width' => '900px',
                'sidebar_position' => 'left',
                'header_style' => 'retro'
            )
        ),
        'modern' => array(
            'name' => '现代风格',
            'colors' => array(
                'primary' => '#2196f3',
                'secondary' => '#9e9e9e',
                'success' => '#4caf50',
                'danger' => '#f44336',
                'warning' => '#ff9800',
                'info' => '#00bcd4',
                'light' => '#f5f5f5',
                'dark' => '#212121'
            ),
            'typography' => array(
                'font_family' => '"Roboto", "Helvetica Neue", Arial, sans-serif',
                'font_size' => '16px',
                'line_height' => '1.5'
            ),
            'layout' => array(
                'container_width' => '1400px',
                'sidebar_position' => 'right',
                'header_style' => 'modern'
            )
        )
    );

    return apply_filters('qioooo_theme_presets', $presets);
}

/**
 * 应用主题预设
 */
function qioooo_apply_theme_preset($preset_id) {
    $presets = qioooo_add_theme_presets();
    
    if (!isset($presets[$preset_id])) {
        return false;
    }
    
    $preset = $presets[$preset_id];
    
    // 应用颜色
    foreach ($preset['colors'] as $key => $value) {
        set_theme_mod('color_' . $key, $value);
    }
    
    // 应用排版
    foreach ($preset['typography'] as $key => $value) {
        set_theme_mod('typography_' . $key, $value);
    }
    
    // 应用布局
    foreach ($preset['layout'] as $key => $value) {
        set_theme_mod('layout_' . $key, $value);
    }
    
    return true;
}

/**
 * 添加预设选择器到自定义器
 */
function qioooo_add_preset_selector($wp_customize) {
    $wp_customize->add_section('theme_presets', array(
        'title' => '主题预设',
        'priority' => 1
    ));
    
    $wp_customize->add_setting('theme_preset', array(
        'default' => 'default',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    
    $wp_customize->add_control('theme_preset', array(
        'label' => '选择预设',
        'section' => 'theme_presets',
        'type' => 'select',
        'choices' => array_map(function($preset) {
            return $preset['name'];
        }, qioooo_add_theme_presets())
    ));
}
add_action('customize_register', 'qioooo_add_preset_selector');

/**
 * 监听预设变化
 */
function qioooo_preset_change() {
    if (isset($_POST['theme_preset'])) {
        qioooo_apply_theme_preset($_POST['theme_preset']);
    }
}
add_action('wp_ajax_qioooo_change_preset', 'qioooo_preset_change'); 