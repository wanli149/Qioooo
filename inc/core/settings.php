// 添加占位符设置
function qioooo_add_placeholder_settings($wp_customize) {
    // 添加占位符设置部分
    $wp_customize->add_section('qioooo_placeholder_settings', array(
        'title'    => __('占位符设置', 'qioooo'),
        'priority' => 30,
    ));

    // 启用占位符
    $wp_customize->add_setting('qioooo_enable_placeholder', array(
        'default'           => true,
        'sanitize_callback' => 'qioooo_sanitize_checkbox',
    ));

    $wp_customize->add_control('qioooo_enable_placeholder', array(
        'label'    => __('启用占位符', 'qioooo'),
        'section'  => 'qioooo_placeholder_settings',
        'type'     => 'checkbox',
    ));

    // 占位符类型
    $wp_customize->add_setting('qioooo_placeholder_type', array(
        'default'           => 'gradient',
        'sanitize_callback' => 'qioooo_sanitize_select',
    ));

    $wp_customize->add_control('qioooo_placeholder_type', array(
        'label'    => __('占位符类型', 'qioooo'),
        'section'  => 'qioooo_placeholder_settings',
        'type'     => 'select',
        'choices'  => array(
            'gradient' => __('渐变动画', 'qioooo'),
            'shape'    => __('形状阴影', 'qioooo'),
        ),
    ));

    // 占位符颜色
    $wp_customize->add_setting('qioooo_placeholder_color', array(
        'default'           => '#f0f0f0',
        'sanitize_callback' => 'sanitize_hex_color',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'qioooo_placeholder_color', array(
        'label'    => __('占位符颜色', 'qioooo'),
        'section'  => 'qioooo_placeholder_settings',
        'settings' => 'qioooo_placeholder_color',
    )));

    // 占位符动画速度
    $wp_customize->add_setting('qioooo_placeholder_speed', array(
        'default'           => '1.5',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('qioooo_placeholder_speed', array(
        'label'    => __('动画速度(秒)', 'qioooo'),
        'section'  => 'qioooo_placeholder_settings',
        'type'     => 'number',
        'input_attrs' => array(
            'min'  => 0.5,
            'max'  => 5,
            'step' => 0.1,
        ),
    ));
}
add_action('customize_register', 'qioooo_add_placeholder_settings'); 