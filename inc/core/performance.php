<?php
/**
 * 性能优化相关函数
 *
 * @package QIoooo
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 启用资源压缩
 */
function qioooo_enable_compression() {
    if (!is_admin() && !in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'))) {
        ob_start('qioooo_compress_output');
    }
}
add_action('init', 'qioooo_enable_compression');

/**
 * 压缩输出
 */
function qioooo_compress_output($buffer) {
    $search = array(
        '/\>[^\S ]+/s',     // 删除标签后的空白字符
        '/[^\S ]+\</s',     // 删除标签前的空白字符
        '/(\s)+/s',         // 将多个空白字符替换为一个
        '/<!--(.|\s)*?-->/' // 删除HTML注释
    );
    $replace = array('>', '<', '\\1', '');
    $buffer = preg_replace($search, $replace, $buffer);
    return $buffer;
}

/**
 * 启用浏览器缓存
 */
function qioooo_enable_browser_caching() {
    if (!is_admin()) {
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
        header('Cache-Control: public, max-age=31536000');
        header('Pragma: public');
    }
}
add_action('send_headers', 'qioooo_enable_browser_caching');

/**
 * 延迟加载图片
 */
function qioooo_lazy_load_images($content) {
    if (is_feed() || is_preview() || is_admin()) {
        return $content;
    }
    
    $content = preg_replace_callback('/<img([^>]+?)src=[\'"]?([^\'"\s>]+)[\'"]?([^>]*)>/', function($matches) {
        $img = $matches[0];
        $img = str_replace('src=', 'data-src=', $img);
        $img = str_replace('class="', 'class="lazy ', $img);
        return $img;
    }, $content);
    
    return $content;
}
add_filter('the_content', 'qioooo_lazy_load_images');

/**
 * 禁用表情符号
 */
function qioooo_disable_emojis() {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
}
add_action('init', 'qioooo_disable_emojis');

/**
 * 优化数据库
 */
function qioooo_optimize_database() {
    global $wpdb;
    
    // 清理修订版本
    $wpdb->query("DELETE FROM $wpdb->posts WHERE post_type = 'revision'");
    
    // 清理自动草稿
    $wpdb->query("DELETE FROM $wpdb->posts WHERE post_status = 'auto-draft'");
    
    // 清理垃圾评论
    $wpdb->query("DELETE FROM $wpdb->comments WHERE comment_approved = 'spam'");
    $wpdb->query("DELETE FROM $wpdb->comments WHERE comment_approved = 'trash'");
    
    // 优化表
    $tables = $wpdb->get_col("SHOW TABLES");
    foreach ($tables as $table) {
        $wpdb->query("OPTIMIZE TABLE $table");
    }
}
add_action('wp_scheduled_delete', 'qioooo_optimize_database');

/**
 * 添加性能监控
 */
function qioooo_add_performance_monitor() {
    if (!is_admin() && current_user_can('manage_options')) {
        add_action('wp_footer', 'qioooo_show_performance_stats');
    }
}
add_action('init', 'qioooo_add_performance_monitor');

/**
 * 显示性能统计
 */
function qioooo_show_performance_stats() {
    $stats = array(
        'memory_usage' => memory_get_usage(true) / 1024 / 1024,
        'query_count' => get_num_queries(),
        'load_time' => timer_stop(0, 3),
        'db_queries' => $GLOBALS['wpdb']->queries,
    );
    
    echo '<div class="performance-stats" style="display:none;">';
    echo '<pre>' . print_r($stats, true) . '</pre>';
    echo '</div>';
}

/**
 * 优化图片加载
 */
function qioooo_optimize_image_loading() {
    // 添加图片尺寸属性
    add_filter('wp_get_attachment_image_attributes', function($attr, $attachment) {
        if (!isset($attr['sizes'])) {
            $attr['sizes'] = '(max-width: 768px) 100vw, (max-width: 1200px) 50vw, 33vw';
        }
        return $attr;
    }, 10, 2);

    // 添加图片预加载
    add_action('wp_head', function() {
        if (is_singular() && has_post_thumbnail()) {
            $image = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full');
            echo '<link rel="preload" as="image" href="' . esc_url($image[0]) . '">';
        }
    });
}
add_action('init', 'qioooo_optimize_image_loading');

/**
 * 优化CSS加载
 */
function qioooo_optimize_css_loading() {
    // 内联关键CSS
    add_action('wp_head', function() {
        $critical_css = file_get_contents(get_template_directory() . '/css/critical.css');
        echo '<style>' . $critical_css . '</style>';
    });

    // 延迟加载非关键CSS
    add_action('wp_footer', function() {
        wp_enqueue_style('qioooo-non-critical', get_template_directory_uri() . '/css/non-critical.css', array(), null);
        wp_style_add_data('qioooo-non-critical', 'media', 'print');
        wp_style_add_data('qioooo-non-critical', 'onload', 'this.media=\'all\'');
    });
}
add_action('init', 'qioooo_optimize_css_loading');

/**
 * 优化JavaScript加载
 */
function qioooo_optimize_js_loading() {
    // 延迟加载非关键JS
    add_filter('script_loader_tag', function($tag, $handle) {
        if (strpos($handle, 'qioooo-') === 0 && $handle !== 'qioooo-critical') {
            return str_replace(' src', ' defer src', $tag);
        }
        return $tag;
    }, 10, 2);

    // 添加预加载关键JS
    add_action('wp_head', function() {
        echo '<link rel="preload" as="script" href="' . get_template_directory_uri() . '/js/critical.js">';
    });
}
add_action('init', 'qioooo_optimize_js_loading');

/**
 * 优化字体加载
 */
function qioooo_optimize_font_loading() {
    // 添加字体预加载
    add_action('wp_head', function() {
        echo '<link rel="preload" as="font" href="https://fonts.gstatic.com/s/notosanssc/v36/k3kXo84MPvpLmixcA63oeALhLOCT-xWNm8Hqd37g1OkDRZe7lR4sg1IzSy-MNbE9VH8V.0.woff2" crossorigin>';
    });

    // 添加字体显示策略
    add_filter('style_loader_tag', function($tag, $handle) {
        if ($handle === 'qioooo-fonts') {
            return str_replace('>', ' media="print" onload="this.media=\'all\'">', $tag);
        }
        return $tag;
    }, 10, 2);
}
add_action('init', 'qioooo_optimize_font_loading');

/**
 * 优化资源加载
 */
function qioooo_optimize_resource_loading() {
    // 移除不必要的资源
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wp_shortlink_wp_head');
    remove_action('wp_head', 'feed_links_extra', 3);
    remove_action('wp_head', 'feed_links', 2);
    remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
}
add_action('init', 'qioooo_optimize_resource_loading');

/**
 * 优化数据库查询
 */
function qioooo_optimize_database_queries() {
    // 添加查询缓存
    add_filter('query', function($query) {
        if (strpos($query, 'SELECT') === 0) {
            $cache_key = md5($query);
            $cached_result = wp_cache_get($cache_key, 'qioooo_queries');
            if ($cached_result !== false) {
                return $cached_result;
            }
        }
        return $query;
    });

    // 优化文章查询
    add_filter('posts_request', function($sql) {
        if (strpos($sql, 'SELECT') === 0) {
            $sql = str_replace('SELECT', 'SELECT SQL_CALC_FOUND_ROWS', $sql);
        }
        return $sql;
    });

    // 添加索引提示
    add_filter('posts_where', function($where) {
        if (is_search()) {
            $where = str_replace('post_title', 'USE INDEX (post_title)', $where);
        }
        return $where;
    });
}
add_action('init', 'qioooo_optimize_database_queries');

/**
 * 资源预加载策略
 */
function qioooo_preload_resources() {
    if (is_singular()) {
        // 预加载特色图片
        if (has_post_thumbnail()) {
            $image = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full');
            echo '<link rel="preload" as="image" href="' . esc_url($image[0]) . '">';
        }

        // 预加载相关文章
        $related_posts = get_posts(array(
            'post_type' => 'post',
            'posts_per_page' => 3,
            'post__not_in' => array(get_the_ID()),
            'orderby' => 'rand'
        ));
        foreach ($related_posts as $post) {
            if (has_post_thumbnail($post->ID)) {
                $image = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'thumbnail');
                echo '<link rel="preload" as="image" href="' . esc_url($image[0]) . '">';
            }
        }
    }

    // 预加载关键字体
    echo '<link rel="preload" as="font" href="' . get_template_directory_uri() . '/assets/fonts/noto-sans-sc.woff2" crossorigin>';

    // 预加载关键CSS
    echo '<link rel="preload" as="style" href="' . get_template_directory_uri() . '/css/critical.css">';

    // 预加载关键JS
    echo '<link rel="preload" as="script" href="' . get_template_directory_uri() . '/js/critical.js">';
}
add_action('wp_head', 'qioooo_preload_resources');