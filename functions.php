<?php
/**
 * QIoooo 主题函数和定义
 */

// 退出如果直接访问
if (!defined('ABSPATH')) {
    exit;
}

// 定义主题常量
define('QIOOOO_VERSION', '1.0.0');
define('QIOOOO_DIR', get_template_directory());
define('QIOOOO_URI', get_template_directory_uri());

/**
 * 主题设置
 */
function qioooo_setup() {
    // 加载翻译文件
    load_theme_textdomain('qioooo', QIOOOO_DIR . '/languages');

    // 添加默认的帖子和评论 RSS feed 链接
    add_theme_support('automatic-feed-links');

    // 支持标题标签
    add_theme_support('title-tag');

    // 支持特色图片
    add_theme_support('post-thumbnails');

    // 支持 HTML5
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ));

    // 添加主题自定义背景
    add_theme_support('custom-background', array(
        'default-color' => 'ffffff',
    ));

    // 添加主题自定义 logo
    add_theme_support('custom-logo', array(
        'height'      => 250,
        'width'       => 250,
        'flex-width'  => true,
        'flex-height' => true,
    ));

    // 注册菜单
    register_nav_menus(array(
        'primary' => __('主菜单', 'qioooo'),
        'footer'  => __('页脚菜单', 'qioooo'),
    ));

    // 添加文章格式支持
    add_theme_support('post-formats', array(
        'aside',
        'image',
        'video',
        'quote',
        'link',
        'gallery',
        'status',
        'audio',
        'chat',
    ));

    // 添加自定义头部支持
    add_theme_support('custom-header', array(
        'default-image'      => '',
        'default-text-color' => '000000',
        'width'              => 1920,
        'height'             => 400,
        'flex-width'         => true,
        'flex-height'        => true,
    ));

    // 添加编辑器样式支持
    add_theme_support('editor-styles');
    add_editor_style('css/editor-style.css');

    // 添加宽对齐支持
    add_theme_support('align-wide');

    // 添加响应式嵌入支持
    add_theme_support('responsive-embeds');

    // 添加自定义间距支持
    add_theme_support('custom-spacing');

    // 添加自定义行高支持
    add_theme_support('custom-line-height');

    // 添加自定义字体支持
    add_theme_support('custom-font');

    // 添加自定义颜色支持
    add_theme_support('custom-color');

    // 添加自定义布局支持
    add_theme_support('custom-layout');

    // 注册导航菜单
    register_nav_menus(array(
        'primary'   => __('主导航', 'qioooo'),
        'footer'    => __('页脚导航', 'qioooo'),
        'social'    => __('社交链接', 'qioooo'),
    ));
}
add_action('after_setup_theme', 'qioooo_setup');

/**
 * 加载脚本和样式
 */
function qioooo_scripts() {
    // 注册样式
    wp_enqueue_style('qioooo-style', get_stylesheet_uri(), array(), QIOOOO_VERSION);
    
    // 注册脚本
    wp_enqueue_script('qioooo-main', QIOOOO_URI . '/dist/main.min.js', array(), QIOOOO_VERSION, true);

    // 本地化脚本
    wp_localize_script('qioooo-main', 'qiooooSettings', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('qioooo-nonce'),
    ));

    // 如果是单个文章或页面，加载评论回复脚本
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'qioooo_scripts');

/**
 * 自定义主题设置
 */
function qioooo_customize_register($wp_customize) {
    // 添加主题颜色设置
    $wp_customize->add_setting('theme_primary_color', array(
        'default'           => '#2196f3',
        'sanitize_callback' => 'sanitize_hex_color',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'theme_primary_color', array(
        'label'    => __('主题主色调', 'qioooo'),
        'section'  => 'colors',
        'settings' => 'theme_primary_color',
    )));

    // 添加暗黑模式设置
    $wp_customize->add_setting('enable_dark_mode', array(
        'default'           => true,
        'sanitize_callback' => 'qioooo_sanitize_checkbox',
    ));

    $wp_customize->add_control('enable_dark_mode', array(
        'type'    => 'checkbox',
        'section' => 'colors',
        'label'   => __('启用暗黑模式', 'qioooo'),
    ));
}
add_action('customize_register', 'qioooo_customize_register');

/**
 * 清理 checkbox 值
 */
function qioooo_sanitize_checkbox($checked) {
    return ((isset($checked) && true == $checked) ? true : false);
}

/**
 * 添加主题设置页面
 */
function qioooo_add_theme_page() {
    add_theme_page(
        __('主题设置', 'qioooo'),
        __('主题设置', 'qioooo'),
        'edit_theme_options',
        'qioooo-settings',
        'qioooo_theme_settings_page'
    );
}
add_action('admin_menu', 'qioooo_add_theme_page');

/**
 * 渲染主题设置页面
 */
function qioooo_theme_settings_page() {
    // 检查用户权限
    if (!current_user_can('edit_theme_options')) {
        wp_die(__('您没有足够的权限访问此页面。', 'qioooo'));
    }
    
    // 保存设置
    if (isset($_POST['qioooo_save_settings'])) {
        check_admin_referer('qioooo_settings_nonce');
        
        // 保存设置逻辑
        update_option('qioooo_theme_settings', array(
            'enable_dark_mode' => isset($_POST['enable_dark_mode']),
            'primary_color'    => sanitize_hex_color($_POST['primary_color']),
        ));
    }
    
    // 显示设置页面
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form method="post" action="">
            <?php wp_nonce_field('qioooo_settings_nonce'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row"><?php _e('暗黑模式', 'qioooo'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="enable_dark_mode" value="1" <?php checked(get_option('qioooo_theme_settings')['enable_dark_mode']); ?>>
                            <?php _e('启用暗黑模式', 'qioooo'); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('主题主色调', 'qioooo'); ?></th>
                    <td>
                        <input type="color" name="primary_color" value="<?php echo esc_attr(get_option('qioooo_theme_settings')['primary_color']); ?>">
                    </td>
                </tr>
            </table>
            <?php submit_button(__('保存设置', 'qioooo'), 'primary', 'qioooo_save_settings'); ?>
        </form>
    </div>
    <?php
}

/**
 * 自定义评论列表
 */
function qioooo_comment($comment, $args, $depth) {
    if ('div' === $args['style']) {
        $tag       = 'div';
        $add_below = 'comment';
    } else {
        $tag       = 'li';
        $add_below = 'div-comment';
    }
    ?>
    <<?php echo $tag; ?> <?php comment_class(empty($args['has_children']) ? '' : 'parent'); ?> id="comment-<?php comment_ID(); ?>">
    <?php if ('div' != $args['style']) : ?>
    <div id="div-comment-<?php comment_ID(); ?>" class="comment-body">
    <?php endif; ?>
    <div class="comment-author vcard">
        <?php if (0 != $args['avatar_size']) echo get_avatar($comment, $args['avatar_size']); ?>
        <?php printf(__('<cite class="fn">%s</cite> <span class="says">说：</span>', 'qioooo'), get_comment_author_link()); ?>
    </div>
    <?php if ('0' == $comment->comment_approved) : ?>
    <em class="comment-awaiting-moderation"><?php _e('您的评论正在等待审核。', 'qioooo'); ?></em>
    <br />
    <?php endif; ?>

    <div class="comment-meta commentmetadata">
        <a href="<?php echo esc_url(get_comment_link($comment->comment_ID)); ?>">
            <?php
            /* translators: 1: date, 2: time */
            printf(__('%1$s at %2$s', 'qioooo'), get_comment_date(), get_comment_time());
            ?>
        </a>
        <?php edit_comment_link(__('(编辑)', 'qioooo'), '  ', ''); ?>
    </div>

    <?php comment_text(); ?>

    <div class="reply">
        <?php
        comment_reply_link(
            array_merge(
                $args,
                array(
                    'add_below' => $add_below,
                    'depth'     => $depth,
                    'max_depth' => $args['max_depth'],
                )
            )
        );
        ?>
    </div>
    <?php if ('div' != $args['style']) : ?>
    </div>
    <?php endif; ?>
    <?php
}

/**
 * 自定义分页导航
 */
function qioooo_pagination() {
    global $wp_query;
    $big = 999999999;
    echo paginate_links(array(
        'base'      => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
        'format'    => '?paged=%#%',
        'current'   => max(1, get_query_var('paged')),
        'total'     => $wp_query->max_num_pages,
        'prev_text' => __('&laquo; 上一页', 'qioooo'),
        'next_text' => __('下一页 &raquo;', 'qioooo'),
    ));
}

/**
 * 自定义文章导航
 */
function qioooo_post_nav() {
    $previous = (is_attachment()) ? get_post(get_post()->post_parent) : get_adjacent_post(false, '', true);
    $next     = get_adjacent_post(false, '', false);

    if (!$next && !$previous) {
        return;
    }
    ?>
    <nav class="navigation post-navigation" role="navigation">
        <h2 class="screen-reader-text"><?php _e('文章导航', 'qioooo'); ?></h2>
        <div class="nav-links">
            <?php
            if ($previous) {
                previous_post_link('%link', _x('&laquo; %title', '上一篇', 'qioooo'));
            }
            if ($next) {
                next_post_link('%link', _x('%title &raquo;', '下一篇', 'qioooo'));
            }
            ?>
        </div>
    </nav>
    <?php
}

/**
 * 自定义评论表单
 */
function qioooo_comment_form($args = array(), $post_id = null) {
    if (null === $post_id) {
        $post_id = get_the_ID();
    }

    $commenter     = wp_get_current_commenter();
    $user          = wp_get_current_user();
    $user_identity = $user->exists() ? $user->display_name : '';

    $args = wp_parse_args($args);
    if (!isset($args['format'])) {
        $args['format'] = current_theme_supports('html5', 'comment-form') ? 'html5' : 'xhtml';
    }

    $req      = get_option('require_name_email');
    $aria_req = ($req ? " aria-required='true'" : '');
    $html_req = ($req ? " required='required'" : '');
    $html5    = 'html5' === $args['format'];
    ?>
    <form action="<?php echo esc_url(get_site_url() . '/wp-comments-post.php'); ?>" method="post" id="commentform" class="comment-form"<?php echo $html5 ? ' novalidate' : ''; ?>>
        <p class="comment-notes">
            <span id="email-notes"><?php _e('您的电子邮箱地址不会被公开。', 'qioooo'); ?></span>
            <?php if ($req) : ?>
                <span class="required-field-message"><?php _e('必填项已用<span class="required">*</span>标注', 'qioooo'); ?></span>
            <?php endif; ?>
        </p>
        <p class="comment-form-author">
            <label for="author"><?php _e('姓名', 'qioooo'); ?> <?php if ($req) : ?><span class="required">*</span><?php endif; ?></label>
            <input id="author" name="author" type="text" value="<?php echo esc_attr($commenter['comment_author']); ?>" size="30" maxlength="245"<?php echo $aria_req . $html_req; ?> />
        </p>
        <p class="comment-form-email">
            <label for="email"><?php _e('电子邮箱', 'qioooo'); ?> <?php if ($req) : ?><span class="required">*</span><?php endif; ?></label>
            <input id="email" name="email" type="email" value="<?php echo esc_attr($commenter['comment_author_email']); ?>" size="30" maxlength="100" aria-describedby="email-notes"<?php echo $aria_req . $html_req; ?> />
        </p>
        <p class="comment-form-url">
            <label for="url"><?php _e('网站', 'qioooo'); ?></label>
            <input id="url" name="url" type="url" value="<?php echo esc_attr($commenter['comment_author_url']); ?>" size="30" maxlength="200" />
        </p>
        <p class="comment-form-comment">
            <label for="comment"><?php _e('评论', 'qioooo'); ?></label>
            <textarea id="comment" name="comment" cols="45" rows="8" maxlength="65525" required="required"></textarea>
        </p>
        <p class="form-submit">
            <input name="submit" type="submit" id="submit" class="submit" value="<?php esc_attr_e('发表评论', 'qioooo'); ?>" />
            <?php comment_id_fields($post_id); ?>
        </p>
        <?php do_action('comment_form', $post_id); ?>
    </form>
    <?php
}

/**
 * 注册自定义文章类型
 */
function qioooo_register_post_types() {
    // 注册资源文章类型
    register_post_type('resource', array(
        'labels' => array(
            'name'               => __('资源', 'qioooo'),
            'singular_name'      => __('资源', 'qioooo'),
            'add_new'           => __('添加新资源', 'qioooo'),
            'add_new_item'      => __('添加新资源', 'qioooo'),
            'edit_item'         => __('编辑资源', 'qioooo'),
            'new_item'          => __('新资源', 'qioooo'),
            'view_item'         => __('查看资源', 'qioooo'),
            'search_items'      => __('搜索资源', 'qioooo'),
            'not_found'         => __('未找到资源', 'qioooo'),
            'not_found_in_trash'=> __('回收站中未找到资源', 'qioooo'),
            'menu_name'         => __('资源', 'qioooo')
        ),
        'public'              => true,
        'has_archive'         => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
        'menu_position'       => 5,
        'menu_icon'           => 'dashicons-download',
        'supports'            => array('title', 'editor', 'thumbnail', 'excerpt', 'comments'),
        'rewrite'             => array('slug' => 'resource'),
        'capability_type'     => 'post',
        'show_in_rest'        => true,
    ));

    // 注册资源分类
    register_taxonomy('resource_category', 'resource', array(
        'labels' => array(
            'name'              => __('资源分类', 'qioooo'),
            'singular_name'     => __('资源分类', 'qioooo'),
            'search_items'      => __('搜索资源分类', 'qioooo'),
            'all_items'         => __('所有资源分类', 'qioooo'),
            'parent_item'       => __('父级资源分类', 'qioooo'),
            'parent_item_colon' => __('父级资源分类:', 'qioooo'),
            'edit_item'         => __('编辑资源分类', 'qioooo'),
            'update_item'       => __('更新资源分类', 'qioooo'),
            'add_new_item'      => __('添加新资源分类', 'qioooo'),
            'new_item_name'     => __('新资源分类名称', 'qioooo'),
            'menu_name'         => __('资源分类', 'qioooo'),
        ),
        'hierarchical'      => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'resource-category'),
        'show_in_rest'      => true,
    ));
}
add_action('init', 'qioooo_register_post_types');

/**
 * 添加资源元数据框
 */
function qioooo_add_resource_meta_boxes() {
    add_meta_box(
        'resource_details',
        __('资源详情', 'qioooo'),
        'qioooo_resource_meta_box_callback',
        'resource',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'qioooo_add_resource_meta_boxes');

/**
 * 资源元数据框回调函数
 */
function qioooo_resource_meta_box_callback($post) {
    // 添加nonce字段用于安全验证
    wp_nonce_field('qioooo_resource_meta_box', 'qioooo_resource_meta_box_nonce');

    // 获取已保存的元数据
    $download_url = get_post_meta($post->ID, 'download_url', true);
    $download_count = get_post_meta($post->ID, 'download_count', true);
    $rating = get_post_meta($post->ID, 'rating', true);
    $file_size = get_post_meta($post->ID, 'file_size', true);
    $file_format = get_post_meta($post->ID, 'file_format', true);

    // 输出表单字段
    ?>
    <div class="resource-meta-fields">
        <p>
            <label for="download_url"><?php _e('下载链接', 'qioooo'); ?></label>
            <input type="url" id="download_url" name="download_url" value="<?php echo esc_attr($download_url); ?>" class="widefat">
        </p>
        <p>
            <label for="download_count"><?php _e('下载次数', 'qioooo'); ?></label>
            <input type="number" id="download_count" name="download_count" value="<?php echo esc_attr($download_count); ?>" class="widefat">
        </p>
        <p>
            <label for="rating"><?php _e('评分', 'qioooo'); ?></label>
            <select id="rating" name="rating" class="widefat">
                <?php for ($i = 1; $i <= 5; $i++) : ?>
                    <option value="<?php echo $i; ?>" <?php selected($rating, $i); ?>><?php echo $i; ?> 星</option>
                <?php endfor; ?>
            </select>
        </p>
        <p>
            <label for="file_size"><?php _e('文件大小', 'qioooo'); ?></label>
            <input type="text" id="file_size" name="file_size" value="<?php echo esc_attr($file_size); ?>" class="widefat">
        </p>
        <p>
            <label for="file_format"><?php _e('文件格式', 'qioooo'); ?></label>
            <select id="file_format" name="file_format" class="widefat">
                <option value="pdf" <?php selected($file_format, 'pdf'); ?>>PDF</option>
                <option value="word" <?php selected($file_format, 'word'); ?>>Word</option>
                <option value="excel" <?php selected($file_format, 'excel'); ?>>Excel</option>
                <option value="ppt" <?php selected($file_format, 'ppt'); ?>>PPT</option>
            </select>
        </p>
    </div>
    <?php
}

/**
 * 保存资源元数据
 */
function qioooo_save_resource_meta($post_id) {
    // 检查nonce
    if (!isset($_POST['qioooo_resource_meta_box_nonce'])) {
        return;
    }
    if (!wp_verify_nonce($_POST['qioooo_resource_meta_box_nonce'], 'qioooo_resource_meta_box')) {
        return;
    }

    // 检查自动保存
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // 检查权限
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // 保存元数据
    $meta_fields = array('download_url', 'download_count', 'rating', 'file_size', 'file_format');
    foreach ($meta_fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
        }
    }
}
add_action('save_post_resource', 'qioooo_save_resource_meta');

/**
 * 添加主题设置页面
 */
function qioooo_add_theme_settings_page() {
    add_theme_page(
        __('主题设置', 'qioooo'),
        __('主题设置', 'qioooo'),
        'manage_options',
        'qioooo-theme-settings',
        'qioooo_theme_settings_page_callback'
    );
}
add_action('admin_menu', 'qioooo_add_theme_settings_page');

/**
 * 主题设置页面回调函数
 */
function qioooo_theme_settings_page_callback() {
    if (!current_user_can('manage_options')) {
        return;
    }

    if (isset($_POST['qioooo_theme_settings_nonce']) && wp_verify_nonce($_POST['qioooo_theme_settings_nonce'], 'qioooo_theme_settings')) {
        $settings = array(
            // 基础设置
            'color_scheme' => sanitize_text_field($_POST['color_scheme']),
            'layout' => sanitize_text_field($_POST['layout']),
            'sidebar_position' => sanitize_text_field($_POST['sidebar_position']),
            'sticky_header' => isset($_POST['sticky_header']),
            'back_to_top' => isset($_POST['back_to_top']),
            'lazy_load' => isset($_POST['lazy_load']),
            
            // 主题预设
            'theme_preset' => sanitize_text_field($_POST['theme_preset']),
            
            // 交互动画
            'enable_animations' => isset($_POST['enable_animations']),
            'page_transitions' => isset($_POST['page_transitions']),
            'scroll_animations' => isset($_POST['scroll_animations']),
            'hover_effects' => isset($_POST['hover_effects']),
            
            // 语言设置
            'theme_language' => sanitize_text_field($_POST['theme_language']),
            'rtl_support' => isset($_POST['rtl_support']),
            
            // 性能优化
            'enable_compression' => isset($_POST['enable_compression']),
            'enable_caching' => isset($_POST['enable_caching']),
            'disable_emojis' => isset($_POST['disable_emojis']),
            'optimize_database' => isset($_POST['optimize_database']),
            
            // 安全防护
            'prevent_directory_browsing' => isset($_POST['prevent_directory_browsing']),
            'limit_login_attempts' => isset($_POST['limit_login_attempts']),
            'disable_xmlrpc' => isset($_POST['disable_xmlrpc']),
            'monitor_suspicious_activity' => isset($_POST['monitor_suspicious_activity']),
            'auto_backup' => isset($_POST['auto_backup']),
            
            // SEO优化
            'add_structured_data' => isset($_POST['add_structured_data']),
            'optimize_titles' => isset($_POST['optimize_titles']),
            'add_meta_description' => isset($_POST['add_meta_description']),
            'add_meta_keywords' => isset($_POST['add_meta_keywords']),
            'add_canonical_url' => isset($_POST['add_canonical_url']),
            'generate_sitemap' => isset($_POST['generate_sitemap']),
            'add_robots_txt' => isset($_POST['add_robots_txt'])
        );

        update_option('qioooo_theme_settings', $settings);
        add_settings_error('qioooo_theme_settings', 'settings_updated', __('设置已保存。', 'qioooo'), 'updated');
    }

    $settings = get_option('qioooo_theme_settings', array(
        'color_scheme' => 'light',
        'layout' => 'wide',
        'sidebar_position' => 'right',
        'sticky_header' => true,
        'back_to_top' => true,
        'lazy_load' => true,
        'theme_preset' => 'default',
        'enable_animations' => true,
        'page_transitions' => true,
        'scroll_animations' => true,
        'hover_effects' => true,
        'theme_language' => 'zh_CN',
        'rtl_support' => false,
        'enable_compression' => true,
        'enable_caching' => true,
        'disable_emojis' => true,
        'optimize_database' => true,
        'prevent_directory_browsing' => true,
        'limit_login_attempts' => true,
        'disable_xmlrpc' => true,
        'monitor_suspicious_activity' => true,
        'auto_backup' => true,
        'add_structured_data' => true,
        'optimize_titles' => true,
        'add_meta_description' => true,
        'add_meta_keywords' => true,
        'add_canonical_url' => true,
        'generate_sitemap' => true,
        'add_robots_txt' => true
    ));
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form method="post" action="">
            <?php wp_nonce_field('qioooo_theme_settings', 'qioooo_theme_settings_nonce'); ?>
            
            <div class="settings-section">
                <h2><?php _e('颜色方案', 'qioooo'); ?></h2>
                <div class="color-schemes">
                    <div class="color-scheme-option">
                        <input type="radio" id="color-scheme-light" name="color_scheme" value="light" <?php checked($settings['color_scheme'], 'light'); ?>>
                        <label for="color-scheme-light">
                            <span class="color-scheme-preview light"></span>
                            <span class="color-scheme-name"><?php _e('浅色', 'qioooo'); ?></span>
                        </label>
                    </div>
                    <div class="color-scheme-option">
                        <input type="radio" id="color-scheme-dark" name="color_scheme" value="dark" <?php checked($settings['color_scheme'], 'dark'); ?>>
                        <label for="color-scheme-dark">
                            <span class="color-scheme-preview dark"></span>
                            <span class="color-scheme-name"><?php _e('深色', 'qioooo'); ?></span>
                        </label>
                    </div>
                    <div class="color-scheme-option">
                        <input type="radio" id="color-scheme-auto" name="color_scheme" value="auto" <?php checked($settings['color_scheme'], 'auto'); ?>>
                        <label for="color-scheme-auto">
                            <span class="color-scheme-preview auto"></span>
                            <span class="color-scheme-name"><?php _e('自动', 'qioooo'); ?></span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="settings-section">
                <h2><?php _e('布局设置', 'qioooo'); ?></h2>
                <div class="layout-options">
                    <div class="layout-option">
                        <input type="radio" id="layout-wide" name="layout" value="wide" <?php checked($settings['layout'], 'wide'); ?>>
                        <label for="layout-wide"><?php _e('宽屏', 'qioooo'); ?></label>
                    </div>
                    <div class="layout-option">
                        <input type="radio" id="layout-boxed" name="layout" value="boxed" <?php checked($settings['layout'], 'boxed'); ?>>
                        <label for="layout-boxed"><?php _e('盒式', 'qioooo'); ?></label>
                    </div>
                </div>
            </div>

            <div class="settings-section">
                <h2><?php _e('侧边栏位置', 'qioooo'); ?></h2>
                <div class="sidebar-options">
                    <div class="sidebar-option">
                        <input type="radio" id="sidebar-right" name="sidebar_position" value="right" <?php checked($settings['sidebar_position'], 'right'); ?>>
                        <label for="sidebar-right"><?php _e('右侧', 'qioooo'); ?></label>
                    </div>
                    <div class="sidebar-option">
                        <input type="radio" id="sidebar-left" name="sidebar_position" value="left" <?php checked($settings['sidebar_position'], 'left'); ?>>
                        <label for="sidebar-left"><?php _e('左侧', 'qioooo'); ?></label>
                    </div>
                    <div class="sidebar-option">
                        <input type="radio" id="sidebar-none" name="sidebar_position" value="none" <?php checked($settings['sidebar_position'], 'none'); ?>>
                        <label for="sidebar-none"><?php _e('无侧边栏', 'qioooo'); ?></label>
                    </div>
                </div>
            </div>

            <div class="settings-section">
                <h2><?php _e('其他设置', 'qioooo'); ?></h2>
                <div class="other-options">
                    <div class="option-group">
                        <input type="checkbox" id="sticky-header" name="sticky_header" <?php checked($settings['sticky_header']); ?>>
                        <label for="sticky-header"><?php _e('固定顶部导航', 'qioooo'); ?></label>
                    </div>
                    <div class="option-group">
                        <input type="checkbox" id="back-to-top" name="back_to_top" <?php checked($settings['back_to_top']); ?>>
                        <label for="back-to-top"><?php _e('显示返回顶部按钮', 'qioooo'); ?></label>
                    </div>
                    <div class="option-group">
                        <input type="checkbox" id="lazy-load" name="lazy_load" <?php checked($settings['lazy_load']); ?>>
                        <label for="lazy-load"><?php _e('启用图片懒加载', 'qioooo'); ?></label>
                    </div>
                </div>
            </div>

            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

/**
 * 应用主题设置
 */
function qioooo_apply_theme_settings() {
    $settings = get_option('qioooo_theme_settings', array());
    
    // 基础设置
    if (isset($settings['color_scheme'])) {
        add_filter('body_class', function($classes) use ($settings) {
            $classes[] = 'color-scheme-' . $settings['color_scheme'];
            return $classes;
        });
    }
    
    if (isset($settings['layout'])) {
        add_filter('body_class', function($classes) use ($settings) {
            $classes[] = 'layout-' . $settings['layout'];
            return $classes;
        });
    }
    
    if (isset($settings['sidebar_position'])) {
        add_filter('body_class', function($classes) use ($settings) {
            $classes[] = 'sidebar-' . $settings['sidebar_position'];
            return $classes;
        });
    }
    
    if (isset($settings['sticky_header']) && $settings['sticky_header']) {
        add_filter('body_class', function($classes) {
            $classes[] = 'sticky-header';
            return $classes;
        });
    }
    
    // 主题预设
    if (isset($settings['theme_preset'])) {
        add_filter('body_class', function($classes) use ($settings) {
            $classes[] = 'theme-preset-' . $settings['theme_preset'];
            return $classes;
        });
    }
    
    // 交互动画
    if (isset($settings['enable_animations']) && $settings['enable_animations']) {
        add_filter('body_class', function($classes) {
            $classes[] = 'enable-animations';
            return $classes;
        });
        
        if (isset($settings['page_transitions']) && $settings['page_transitions']) {
            add_filter('body_class', function($classes) {
                $classes[] = 'page-transitions';
                return $classes;
            });
        }
        
        if (isset($settings['scroll_animations']) && $settings['scroll_animations']) {
            add_filter('body_class', function($classes) {
                $classes[] = 'scroll-animations';
                return $classes;
            });
        }
        
        if (isset($settings['hover_effects']) && $settings['hover_effects']) {
            add_filter('body_class', function($classes) {
                $classes[] = 'hover-effects';
                return $classes;
            });
        }
    }
    
    // 语言设置
    if (isset($settings['theme_language'])) {
        add_filter('locale', function($locale) use ($settings) {
            return $settings['theme_language'];
        });
    }
    
    if (isset($settings['rtl_support']) && $settings['rtl_support']) {
        add_filter('body_class', function($classes) {
            $classes[] = 'rtl';
            return $classes;
        });
    }
    
    // 性能优化
    if (isset($settings['enable_compression']) && $settings['enable_compression']) {
        add_action('init', 'qioooo_enable_compression');
    }
    
    if (isset($settings['enable_caching']) && $settings['enable_caching']) {
        add_action('send_headers', 'qioooo_enable_browser_caching');
    }
    
    if (isset($settings['disable_emojis']) && $settings['disable_emojis']) {
        add_action('init', 'qioooo_disable_emojis');
    }
    
    if (isset($settings['optimize_database']) && $settings['optimize_database']) {
        add_action('wp_scheduled_delete', 'qioooo_optimize_database');
    }
    
    // 安全防护
    if (isset($settings['prevent_directory_browsing']) && $settings['prevent_directory_browsing']) {
        add_action('send_headers', 'qioooo_prevent_directory_browsing');
    }
    
    if (isset($settings['limit_login_attempts']) && $settings['limit_login_attempts']) {
        add_filter('authenticate', 'qioooo_limit_login_attempts', 30, 3);
    }
    
    if (isset($settings['disable_xmlrpc']) && $settings['disable_xmlrpc']) {
        add_action('init', 'qioooo_disable_xmlrpc');
    }
    
    if (isset($settings['monitor_suspicious_activity']) && $settings['monitor_suspicious_activity']) {
        add_action('init', 'qioooo_monitor_suspicious_activity');
    }
    
    if (isset($settings['auto_backup']) && $settings['auto_backup']) {
        add_action('wp_scheduled_delete', 'qioooo_backup_database');
    }
    
    // SEO优化
    if (isset($settings['add_structured_data']) && $settings['add_structured_data']) {
        add_action('wp_head', 'qioooo_add_structured_data');
    }
    
    if (isset($settings['optimize_titles']) && $settings['optimize_titles']) {
        add_filter('wp_title', 'qioooo_optimize_title');
    }
    
    if (isset($settings['add_meta_description']) && $settings['add_meta_description']) {
        add_action('wp_head', 'qioooo_add_meta_description');
    }
    
    if (isset($settings['add_meta_keywords']) && $settings['add_meta_keywords']) {
        add_action('wp_head', 'qioooo_add_meta_keywords');
    }
    
    if (isset($settings['add_canonical_url']) && $settings['add_canonical_url']) {
        add_action('wp_head', 'qioooo_add_canonical_url');
    }
    
    if (isset($settings['generate_sitemap']) && $settings['generate_sitemap']) {
        add_action('publish_post', 'qioooo_generate_sitemap');
        add_action('save_post', 'qioooo_generate_sitemap');
        add_action('delete_post', 'qioooo_generate_sitemap');
    }
    
    if (isset($settings['add_robots_txt']) && $settings['add_robots_txt']) {
        add_action('init', 'qioooo_add_robots_txt');
    }
}
add_action('wp', 'qioooo_apply_theme_settings');

// 添加主题导入/导出功能
function qioooo_export_theme_settings() {
    if (!current_user_can('manage_options')) {
        wp_send_json_error(__('权限不足。', 'qioooo'));
    }

    $settings = get_option('qioooo_theme_settings', array());
    $filename = 'qioooo-theme-settings-' . date('Y-m-d') . '.json';
    
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    echo json_encode($settings);
    exit;
}
add_action('wp_ajax_qioooo_export_settings', 'qioooo_export_theme_settings');

function qioooo_import_theme_settings() {
    if (!current_user_can('manage_options')) {
        wp_send_json_error(__('权限不足。', 'qioooo'));
    }

    if (!isset($_FILES['settings_file'])) {
        wp_send_json_error(__('未找到导入文件。', 'qioooo'));
    }

    $file = $_FILES['settings_file'];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        wp_send_json_error(__('文件上传失败。', 'qioooo'));
    }

    $content = file_get_contents($file['tmp_name']);
    $settings = json_decode($content, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        wp_send_json_error(__('无效的JSON文件。', 'qioooo'));
    }

    update_option('qioooo_theme_settings', $settings);
    wp_send_json_success(__('设置导入成功。', 'qioooo'));
}
add_action('wp_ajax_qioooo_import_settings', 'qioooo_import_theme_settings');

// 添加主题预览功能
function qioooo_theme_preview() {
    if (!current_user_can('manage_options')) {
        return;
    }

    if (isset($_GET['preview']) && $_GET['preview'] === 'true') {
        $settings = get_option('qioooo_theme_settings', array());
        add_filter('body_class', function($classes) use ($settings) {
            $classes[] = 'preview-mode';
            return $classes;
        });
    }
}
add_action('wp', 'qioooo_theme_preview');

// 注册资源详情页样式
function qioooo_enqueue_resource_single_styles() {
    if (is_singular('resource')) {
        wp_enqueue_style(
            'qioooo-resource-single',
            get_template_directory_uri() . '/assets/css/resource-single.css',
            array(),
            '1.0.0'
        );
    }
}
add_action('wp_enqueue_scripts', 'qioooo_enqueue_resource_single_styles');

/**
 * 注册区块
 */
function qioooo_register_blocks() {
    // 注册区块脚本和样式
    wp_register_script(
        'qioooo-blocks',
        get_template_directory_uri() . '/blocks/index.js',
        array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n'),
        filemtime(get_template_directory() . '/blocks/index.js')
    );

    // 注册区块样式
    wp_register_style(
        'qioooo-blocks-style',
        get_template_directory_uri() . '/blocks/style.css',
        array(),
        filemtime(get_template_directory() . '/blocks/style.css')
    );

    // 注册区块编辑器样式
    wp_register_style(
        'qioooo-blocks-editor-style',
        get_template_directory_uri() . '/blocks/editor.css',
        array('wp-edit-blocks'),
        filemtime(get_template_directory() . '/blocks/editor.css')
    );

    // 注册区块
    register_block_type('qioooo/resource-card', array(
        'editor_script' => 'qioooo-blocks',
        'editor_style' => 'qioooo-blocks-editor-style',
        'style' => 'qioooo-blocks-style',
    ));

    register_block_type('qioooo/novel-display', array(
        'editor_script' => 'qioooo-blocks',
        'editor_style' => 'qioooo-blocks-editor-style',
        'style' => 'qioooo-blocks-style',
    ));

    register_block_type('qioooo/chapter-navigation', array(
        'editor_script' => 'qioooo-blocks',
        'editor_style' => 'qioooo-blocks-editor-style',
        'style' => 'qioooo-blocks-style',
    ));

    register_block_type('qioooo/related-posts', array(
        'editor_script' => 'qioooo-blocks',
        'editor_style' => 'qioooo-blocks-editor-style',
        'style' => 'qioooo-blocks-style',
    ));

    register_block_type('qioooo/comments', array(
        'editor_script' => 'qioooo-blocks',
        'editor_style' => 'qioooo-blocks-editor-style',
        'style' => 'qioooo-blocks-style',
    ));
}
add_action('init', 'qioooo_register_blocks');

// 加载小工具测试
if (defined('WP_DEBUG') && WP_DEBUG) {
    require_once get_template_directory() . '/tests/widgets-test-loader.php';
}

// 注册占位符样式
function qioooo_register_placeholder_styles() {
    wp_register_style(
        'qioooo-placeholder',
        get_template_directory_uri() . '/assets/css/placeholder.css',
        array(),
        QIOOOO_VERSION
    );
}
add_action('wp_enqueue_scripts', 'qioooo_register_placeholder_styles');

// 加载占位符样式
function qioooo_enqueue_placeholder_styles() {
    wp_enqueue_style('qioooo-placeholder');
}
add_action('wp_enqueue_scripts', 'qioooo_enqueue_placeholder_styles');

// 注册占位符模板
function qioooo_register_placeholder_template() {
    // 注册占位符模板
    add_theme_support('template-parts');
    
    // 添加占位符模板路径
    add_filter('template_include', function($template) {
        // 检查是否启用占位符
        if (get_theme_mod('qioooo_enable_placeholder', true)) {
            if (is_home() && !have_posts()) {
                return get_template_directory() . '/template-parts/content-placeholder.php';
            }
        }
        return $template;
    });
}
add_action('after_setup_theme', 'qioooo_register_placeholder_template');

// 添加占位符动态样式
function qioooo_add_placeholder_dynamic_styles() {
    // 检查是否启用占位符
    if (!get_theme_mod('qioooo_enable_placeholder', true)) {
        return;
    }

    $placeholder_type = get_theme_mod('qioooo_placeholder_type', 'gradient');
    $placeholder_color = get_theme_mod('qioooo_placeholder_color', '#f0f0f0');
    $placeholder_speed = get_theme_mod('qioooo_placeholder_speed', '1.5');

    $css = "
        .qioooo-placeholder-gradient {
            background: linear-gradient(45deg, {$placeholder_color} 25%, " . qioooo_adjust_brightness($placeholder_color, -10) . " 25%, " . qioooo_adjust_brightness($placeholder_color, -10) . " 50%, {$placeholder_color} 50%, {$placeholder_color} 75%, " . qioooo_adjust_brightness($placeholder_color, -10) . " 75%);
            animation-duration: {$placeholder_speed}s;
        }

        .qioooo-placeholder-shape {
            background: {$placeholder_color};
        }
    ";

    wp_add_inline_style('qioooo-placeholder', $css);
}
add_action('wp_enqueue_scripts', 'qioooo_add_placeholder_dynamic_styles');

// 辅助函数：调整颜色亮度
function qioooo_adjust_brightness($hex, $steps) {
    $steps = max(-255, min(255, $steps));
    $hex = str_replace('#', '', $hex);
    
    if (strlen($hex) == 3) {
        $hex = str_repeat(substr($hex, 0, 1), 2) . str_repeat(substr($hex, 1, 1), 2) . str_repeat(substr($hex, 2, 1), 2);
    }
    
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    
    $r = max(0, min(255, $r + $steps));
    $g = max(0, min(255, $g + $steps));
    $b = max(0, min(255, $b + $steps));
    
    $r_hex = str_pad(dechex($r), 2, '0', STR_PAD_LEFT);
    $g_hex = str_pad(dechex($g), 2, '0', STR_PAD_LEFT);
    $b_hex = str_pad(dechex($b), 2, '0', STR_PAD_LEFT);
    
    return '#' . $r_hex . $g_hex . $b_hex;
} 