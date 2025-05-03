<?php
/**
 * SEO优化相关函数
 *
 * @package QIoooo
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * SEO优化和浏览器兼容性支持
 */

// SEO优化
class QIoooo_SEO {
    public function __construct() {
        add_action('wp_head', array($this, 'add_meta_tags'));
        add_action('wp_head', array($this, 'add_schema_markup'));
        add_filter('document_title_parts', array($this, 'optimize_title'));
        add_filter('wpseo_title', array($this, 'override_yoast_title'));
    }

    // 添加基础meta标签
    public function add_meta_tags() {
        if (is_single() || is_page()) {
            $post_id = get_the_ID();
            $description = get_post_meta($post_id, '_yoast_wpseo_metadesc', true);
            if (!$description) {
                $description = wp_trim_words(get_the_excerpt(), 30);
            }
            ?>
            <meta name="description" content="<?php echo esc_attr($description); ?>" />
            <meta name="keywords" content="<?php echo esc_attr($this->get_keywords()); ?>" />
            <meta property="og:title" content="<?php echo esc_attr(get_the_title()); ?>" />
            <meta property="og:description" content="<?php echo esc_attr($description); ?>" />
            <meta property="og:type" content="article" />
            <meta property="og:url" content="<?php echo esc_url(get_permalink()); ?>" />
            <?php if (has_post_thumbnail()) : ?>
                <meta property="og:image" content="<?php echo esc_url(get_the_post_thumbnail_url($post_id, 'full')); ?>" />
            <?php endif; ?>
            <meta name="twitter:card" content="summary_large_image" />
            <?php
        }
    }

    // 添加Schema标记
    public function add_schema_markup() {
        if (is_single()) {
            $schema = array(
                '@context' => 'https://schema.org',
                '@type' => 'Article',
                'headline' => get_the_title(),
                'datePublished' => get_the_date('c'),
                'dateModified' => get_the_modified_date('c'),
                'author' => array(
                    '@type' => 'Person',
                    'name' => get_the_author()
                ),
                'publisher' => array(
                    '@type' => 'Organization',
                    'name' => get_bloginfo('name'),
                    'logo' => array(
                        '@type' => 'ImageObject',
                        'url' => get_site_icon_url()
                    )
                )
            );
            ?>
            <script type="application/ld+json">
                <?php echo json_encode($schema); ?>
            </script>
            <?php
        }
    }

    // 优化标题
    public function optimize_title($title) {
        if (is_front_page()) {
            $title['title'] = get_bloginfo('name');
            $title['tagline'] = get_bloginfo('description');
        }
        return $title;
    }

    // 覆盖Yoast SEO标题
    public function override_yoast_title($title) {
        if (is_single()) {
            $title = get_the_title() . ' - ' . get_bloginfo('name');
        }
        return $title;
    }

    // 获取关键词
    private function get_keywords() {
        if (is_single()) {
            $post_id = get_the_ID();
            $keywords = get_post_meta($post_id, '_yoast_wpseo_focuskw', true);
            if (!$keywords) {
                $tags = get_the_tags();
                if ($tags) {
                    $keywords = implode(', ', wp_list_pluck($tags, 'name'));
                }
            }
            return $keywords;
        }
        return '';
    }
}

// 浏览器兼容性支持
class QIoooo_Browser_Support {
    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'add_polyfills'));
        add_action('wp_head', array($this, 'add_browser_specific_styles'));
    }

    // 添加polyfills
    public function add_polyfills() {
        // 添加Modernizr检测
        wp_enqueue_script('modernizr', get_template_directory_uri() . '/js/vendor/modernizr.min.js', array(), '3.11.7', false);
        
        // 添加polyfills
        wp_enqueue_script('polyfills', get_template_directory_uri() . '/js/vendor/polyfills.min.js', array(), '1.0.0', false);
        
        // 添加条件注释支持
        wp_enqueue_script('html5shiv', 'https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js', array(), '3.7.3', false);
        wp_script_add_data('html5shiv', 'conditional', 'lt IE 9');
        
        wp_enqueue_script('respond', 'https://oss.maxcdn.com/respond/1.4.2/respond.min.js', array(), '1.4.2', false);
        wp_script_add_data('respond', 'conditional', 'lt IE 9');
    }

    // 添加浏览器特定样式
    public function add_browser_specific_styles() {
        ?>
        <!--[if IE]>
        <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/ie.css">
        <![endif]-->
        <!--[if lt IE 9]>
        <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/ie8.css">
        <![endif]-->
        <?php
    }
}

// 初始化
new QIoooo_SEO();
new QIoooo_Browser_Support();

/**
 * 添加SEO元标签
 */
function qioooo_add_seo_meta_tags() {
    // 获取当前页面信息
    $title = wp_get_document_title();
    $description = get_bloginfo('description');
    $url = get_permalink();
    $site_name = get_bloginfo('name');
    
    // 如果是文章或页面
    if (is_singular()) {
        $post = get_post();
        $description = get_the_excerpt($post);
        if (empty($description)) {
            $description = wp_trim_words(strip_shortcodes($post->post_content), 30);
        }
    }
    
    // 输出基本SEO标签
    echo '<meta name="description" content="' . esc_attr($description) . '">';
    echo '<meta property="og:title" content="' . esc_attr($title) . '">';
    echo '<meta property="og:description" content="' . esc_attr($description) . '">';
    echo '<meta property="og:url" content="' . esc_url($url) . '">';
    echo '<meta property="og:site_name" content="' . esc_attr($site_name) . '">';
    
    // 如果是文章或页面且有特色图片
    if (is_singular() && has_post_thumbnail()) {
        $image = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full');
        echo '<meta property="og:image" content="' . esc_url($image[0]) . '">';
        echo '<meta property="og:image:width" content="' . esc_attr($image[1]) . '">';
        echo '<meta property="og:image:height" content="' . esc_attr($image[2]) . '">';
    }
    
    // 添加结构化数据
    if (is_singular('post')) {
        qioooo_add_article_schema();
    }
}
add_action('wp_head', 'qioooo_add_seo_meta_tags');

/**
 * 添加文章结构化数据
 */
function qioooo_add_article_schema() {
    $post = get_post();
    $author = get_the_author_meta('display_name', $post->post_author);
    $publisher = get_bloginfo('name');
    $logo = get_theme_mod('custom_logo');
    $logo_url = wp_get_attachment_image_url($logo, 'full');
    
    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'Article',
        'headline' => get_the_title(),
        'description' => get_the_excerpt(),
        'datePublished' => get_the_date('c'),
        'dateModified' => get_the_modified_date('c'),
        'author' => array(
            '@type' => 'Person',
            'name' => $author
        ),
        'publisher' => array(
            '@type' => 'Organization',
            'name' => $publisher,
            'logo' => array(
                '@type' => 'ImageObject',
                'url' => $logo_url
            )
        ),
        'mainEntityOfPage' => array(
            '@type' => 'WebPage',
            '@id' => get_permalink()
        )
    );
    
    if (has_post_thumbnail()) {
        $image = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full');
        $schema['image'] = array(
            '@type' => 'ImageObject',
            'url' => $image[0],
            'width' => $image[1],
            'height' => $image[2]
        );
    }
    
    echo '<script type="application/ld+json">' . wp_json_encode($schema) . '</script>';
}

/**
 * 优化标题标签
 */
function qioooo_optimize_title_tag($title) {
    if (is_front_page()) {
        $title = get_bloginfo('name') . ' - ' . get_bloginfo('description');
    } elseif (is_singular()) {
        $title = get_the_title() . ' - ' . get_bloginfo('name');
    } elseif (is_category() || is_tag()) {
        $title = single_term_title('', false) . ' - ' . get_bloginfo('name');
    }
    return $title;
}
add_filter('pre_get_document_title', 'qioooo_optimize_title_tag');

/**
 * 优化面包屑导航
 */
function qioooo_add_breadcrumbs() {
    if (is_front_page()) {
        return;
    }
    
    echo '<nav class="breadcrumbs" aria-label="面包屑导航">';
    echo '<ol itemscope itemtype="https://schema.org/BreadcrumbList">';
    
    // 首页
    echo '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
    echo '<a itemprop="item" href="' . esc_url(home_url()) . '">';
    echo '<span itemprop="name">首页</span>';
    echo '</a>';
    echo '<meta itemprop="position" content="1" />';
    echo '</li>';
    
    if (is_singular()) {
        // 分类
        $categories = get_the_category();
        if ($categories) {
            $category = $categories[0];
            echo '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
            echo '<a itemprop="item" href="' . esc_url(get_category_link($category->term_id)) . '">';
            echo '<span itemprop="name">' . esc_html($category->name) . '</span>';
            echo '</a>';
            echo '<meta itemprop="position" content="2" />';
            echo '</li>';
        }
        
        // 文章标题
        echo '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
        echo '<span itemprop="name">' . get_the_title() . '</span>';
        echo '<meta itemprop="position" content="3" />';
        echo '</li>';
    } elseif (is_category()) {
        // 分类页面
        echo '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
        echo '<span itemprop="name">' . single_cat_title('', false) . '</span>';
        echo '<meta itemprop="position" content="2" />';
        echo '</li>';
    }
    
    echo '</ol>';
    echo '</nav>';
}
add_action('qioooo_before_content', 'qioooo_add_breadcrumbs');

/**
 * 优化图片ALT标签
 */
function qioooo_optimize_image_alt($html, $id) {
    $alt = get_post_meta($id, '_wp_attachment_image_alt', true);
    if (empty($alt)) {
        $alt = get_the_title($id);
    }
    return str_replace('alt=""', 'alt="' . esc_attr($alt) . '"', $html);
}
add_filter('wp_get_attachment_image', 'qioooo_optimize_image_alt', 10, 2);

/**
 * 添加XML站点地图
 */
function qioooo_add_xml_sitemap() {
    if (!is_admin()) {
        return;
    }
    
    $sitemap = new QIoooo_Sitemap();
    $sitemap->generate();
}
add_action('admin_init', 'qioooo_add_xml_sitemap');

/**
 * 站点地图类
 */
class QIoooo_Sitemap {
    public function generate() {
        $posts = get_posts(array(
            'post_type' => array('post', 'page'),
            'post_status' => 'publish',
            'numberposts' => -1
        ));
        
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        
        foreach ($posts as $post) {
            $xml .= '<url>';
            $xml .= '<loc>' . esc_url(get_permalink($post->ID)) . '</loc>';
            $xml .= '<lastmod>' . get_the_modified_date('c', $post) . '</lastmod>';
            $xml .= '<changefreq>weekly</changefreq>';
            $xml .= '<priority>0.8</priority>';
            $xml .= '</url>';
        }
        
        $xml .= '</urlset>';
        
        file_put_contents(ABSPATH . 'sitemap.xml', $xml);
    }
}