<?php
/**
 * 自定义小工具
 *
 * @package QIoooo
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 小工具注册和缓存
 */

/**
 * 小工具缓存类
 */
class QIoooo_Widget_Cache {
    private static $instance = null;
    private $cache_time = 3600; // 缓存时间1小时

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('widgets_init', array($this, 'init'));
    }

    public function init() {
        // 注册小工具
        register_widget('QIoooo_Recent_Posts_Widget');
        register_widget('QIoooo_Popular_Posts_Widget');
        register_widget('QIoooo_Categories_Widget');
        register_widget('QIoooo_Tags_Widget');
        register_widget('QIoooo_Search_Widget');
        register_widget('QIoooo_About_Widget');
        register_widget('QIoooo_Social_Links_Widget');
        register_widget('QIoooo_Newsletter_Widget');
        register_widget('QIoooo_Archives_Widget');
        register_widget('QIoooo_Calendar_Widget');
        register_widget('QIoooo_Recent_Comments_Widget');
        register_widget('QIoooo_Random_Posts_Widget');
    }

    /**
     * 获取缓存的小工具内容
     */
    public function get_cached_widget($widget_id, $args) {
        $cache_key = 'qioooo_widget_' . $widget_id . '_' . md5(serialize($args));
        $cache = get_transient($cache_key);
        
        if (false !== $cache) {
            return $cache;
        }
        
        return false;
    }

    /**
     * 缓存小工具内容
     */
    public function cache_widget($widget_id, $args, $content) {
        $cache_key = 'qioooo_widget_' . $widget_id . '_' . md5(serialize($args));
        set_transient($cache_key, $content, $this->cache_time);
    }

    /**
     * 清除小工具缓存
     */
    public function clear_cache($widget_id = '') {
        if (empty($widget_id)) {
            // 清除所有小工具缓存
            global $wpdb;
            $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_qioooo_widget_%'");
            $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_timeout_qioooo_widget_%'");
        } else {
            // 清除特定小工具缓存
            $cache_key = 'qioooo_widget_' . $widget_id;
            delete_transient($cache_key);
        }
    }
}

// 初始化小工具缓存
add_action('widgets_init', function() {
    QIoooo_Widget_Cache::get_instance();
});

// 小工具基类
abstract class QIoooo_Base_Widget extends WP_Widget {
    protected $cache;

    public function __construct($id_base, $name, $widget_options = array(), $control_options = array()) {
        parent::__construct($id_base, $name, $widget_options, $control_options);
        $this->cache = QIoooo_Widget_Cache::get_instance();
    }

    protected function get_cached_widget($args) {
        if (!$this->is_preview()) {
            $cache = $this->cache->get($this->id);
            if ($cache) {
                echo $cache;
                return true;
            }
        }
        return false;
    }

    protected function cache_widget($args, $content) {
        if (!$this->is_preview()) {
            $this->cache->set($this->id, $content);
        }
    }

    public function flush_widget_cache() {
        $this->cache->delete($this->id);
    }
}

/**
 * 注册自定义小工具
 */
function qioooo_register_widgets() {
    register_widget('QIoooo_Recent_Posts_Widget');
    register_widget('QIoooo_Popular_Posts_Widget');
    register_widget('QIoooo_Categories_Widget');
    register_widget('QIoooo_Tags_Widget');
    register_widget('QIoooo_Search_Widget');
    register_widget('QIoooo_About_Widget');
    register_widget('QIoooo_Social_Links_Widget');
    register_widget('QIoooo_Newsletter_Widget');
    register_widget('QIoooo_Archives_Widget');
    register_widget('QIoooo_Calendar_Widget');
    register_widget('QIoooo_Recent_Comments_Widget');
    register_widget('QIoooo_Random_Posts_Widget');
}
add_action('widgets_init', 'qioooo_register_widgets');

/**
 * 注册小工具区域
 */
function qioooo_register_sidebars() {
    // 主侧边栏
    register_sidebar(array(
        'name'          => __('主侧边栏', 'qioooo'),
        'id'            => 'sidebar-1',
        'description'   => __('主侧边栏小工具区域', 'qioooo'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));

    // 页脚小工具区域
    register_sidebar(array(
        'name'          => __('页脚小工具区域', 'qioooo'),
        'id'            => 'footer-1',
        'description'   => __('页脚小工具区域', 'qioooo'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));

    // 首页小工具区域
    register_sidebar(array(
        'name'          => __('首页小工具区域', 'qioooo'),
        'id'            => 'home-1',
        'description'   => __('首页小工具区域', 'qioooo'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));
}
add_action('widgets_init', 'qioooo_register_sidebars');

/**
 * 最近文章小工具
 */
class QIoooo_Recent_Posts_Widget extends QIoooo_Base_Widget {
    public function __construct() {
        parent::__construct(
            'qioooo_recent_posts',
            __('最近文章', 'qioooo'),
            array('description' => __('显示最近发布的文章', 'qioooo'))
        );
    }

    public function widget($args, $instance) {
        if ($this->get_cached_widget($args)) {
            return;
        }

        $title = apply_filters('widget_title', $instance['title']);
        $number = (!empty($instance['number'])) ? absint($instance['number']) : 5;

        $query_args = array(
            'posts_per_page' => $number,
            'no_found_rows'  => true,
            'post_status'    => 'publish',
            'ignore_sticky_posts' => true
        );

        $r = new WP_Query($query_args);

        if ($r->have_posts()) {
            ob_start();
            echo $args['before_widget'];
            if ($title) {
                echo $args['before_title'] . $title . $args['after_title'];
            }
            ?>
            <ul class="recent-posts-list">
                <?php while ($r->have_posts()) : $r->the_post(); ?>
                    <li>
                        <a href="<?php the_permalink(); ?>">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('thumbnail'); ?>
                            <?php endif; ?>
                            <span class="post-title"><?php get_the_title() ? the_title() : the_ID(); ?></span>
                            <span class="post-date"><?php echo get_the_date(); ?></span>
                        </a>
                    </li>
                <?php endwhile; ?>
            </ul>
            <?php
            echo $args['after_widget'];
            wp_reset_postdata();
            $this->cache_widget($args, ob_get_clean());
        }
    }

    public function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = sanitize_text_field($new_instance['title']);
        $instance['number'] = absint($new_instance['number']);
        $this->flush_widget_cache();
        return $instance;
    }

    public function form($instance) {
        $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
        $number = isset($instance['number']) ? absint($instance['number']) : 5;
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('标题:', 'qioooo'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('显示数量:', 'qioooo'); ?></label>
            <input class="tiny-text" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="number" step="1" min="1" value="<?php echo $number; ?>" size="3" />
        </p>
        <?php
    }
}

/**
 * 热门文章小工具
 */
class QIoooo_Popular_Posts_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'qioooo_popular_posts',
            '热门文章',
            array(
                'description' => '显示最受欢迎的文章，基于评论数和浏览量'
            )
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];
        
        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }
        
        $posts = get_posts(array(
            'numberposts' => $instance['number'] ?? 5,
            'post_status' => 'publish',
            'orderby' => 'comment_count',
            'order' => 'DESC'
        ));
        
        echo '<ul class="popular-posts-list">';
        foreach ($posts as $post) {
            echo '<li class="popular-post-item">';
            if (has_post_thumbnail($post->ID)) {
                echo '<a href="' . get_permalink($post->ID) . '" class="post-thumbnail">';
                echo get_the_post_thumbnail($post->ID, 'thumbnail');
                echo '</a>';
            }
            echo '<div class="post-content">';
            echo '<h3 class="post-title"><a href="' . get_permalink($post->ID) . '">' . get_the_title($post->ID) . '</a></h3>';
            echo '<div class="post-meta">';
            echo '<span class="post-views">' . get_post_meta($post->ID, 'views', true) . ' 浏览</span>';
            echo '<span class="post-comments">' . get_comments_number($post->ID) . ' 评论</span>';
            echo '</div>';
            echo '</div>';
            echo '</li>';
        }
        echo '</ul>';
        
        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = $instance['title'] ?? '热门文章';
        $number = $instance['number'] ?? 5;
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">标题：</label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('number'); ?>">显示数量：</label>
            <input class="tiny-text" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="number" step="1" min="1" value="<?php echo esc_attr($number); ?>">
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['number'] = (!empty($new_instance['number'])) ? absint($new_instance['number']) : 5;
        return $instance;
    }
}

/**
 * 分类目录小工具
 */
class QIoooo_Categories_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'qioooo_categories',
            '分类目录',
            array(
                'description' => '显示分类目录，支持显示文章数量'
            )
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];
        
        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }
        
        $categories = get_categories(array(
            'orderby' => 'count',
            'order' => 'DESC',
            'hide_empty' => true
        ));
        
        echo '<ul class="categories-list">';
        foreach ($categories as $category) {
            echo '<li class="category-item">';
            echo '<a href="' . get_category_link($category->term_id) . '">';
            echo '<span class="category-name">' . $category->name . '</span>';
            if ($instance['show_count']) {
                echo '<span class="category-count">' . $category->count . '</span>';
            }
            echo '</a>';
            echo '</li>';
        }
        echo '</ul>';
        
        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = $instance['title'] ?? '分类目录';
        $show_count = $instance['show_count'] ?? true;
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">标题：</label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_count); ?> id="<?php echo $this->get_field_id('show_count'); ?>" name="<?php echo $this->get_field_name('show_count'); ?>" />
            <label for="<?php echo $this->get_field_id('show_count'); ?>">显示文章数量</label>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['show_count'] = isset($new_instance['show_count']) ? (bool) $new_instance['show_count'] : false;
        return $instance;
    }
}

/**
 * 标签云小工具
 */
class QIoooo_Tags_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'qioooo_tags',
            '标签云',
            array(
                'description' => '显示标签云，支持自定义显示数量'
            )
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];
        
        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }
        
        $tags = get_tags(array(
            'number' => $instance['number'] ?? 45,
            'orderby' => 'count',
            'order' => 'DESC'
        ));
        
        echo '<div class="tagcloud">';
        foreach ($tags as $tag) {
            $link = get_tag_link($tag->term_id);
            $size = min(22, max(8, $tag->count));
            echo '<a href="' . esc_url($link) . '" class="tag-link-' . $tag->term_id . '" style="font-size: ' . $size . 'pt;">';
            echo $tag->name;
            if ($instance['show_count']) {
                echo '<span class="tag-count">' . $tag->count . '</span>';
            }
            echo '</a>';
        }
        echo '</div>';
        
        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = $instance['title'] ?? '标签云';
        $number = $instance['number'] ?? 45;
        $show_count = $instance['show_count'] ?? true;
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">标题：</label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('number'); ?>">显示数量：</label>
            <input class="tiny-text" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="number" step="1" min="1" value="<?php echo esc_attr($number); ?>">
        </p>
        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_count); ?> id="<?php echo $this->get_field_id('show_count'); ?>" name="<?php echo $this->get_field_name('show_count'); ?>" />
            <label for="<?php echo $this->get_field_id('show_count'); ?>">显示文章数量</label>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['number'] = (!empty($new_instance['number'])) ? absint($new_instance['number']) : 45;
        $instance['show_count'] = isset($new_instance['show_count']) ? (bool) $new_instance['show_count'] : false;
        return $instance;
    }
}

/**
 * 搜索小工具
 */
class QIoooo_Search_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'qioooo_search',
            '搜索框',
            array(
                'description' => '显示搜索框，支持自定义样式'
            )
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];
        
        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }
        
        echo '<form role="search" method="get" class="search-form" action="' . esc_url(home_url('/')) . '">';
        echo '<div class="search-box">';
        echo '<input type="search" class="search-field" placeholder="' . esc_attr($instance['placeholder'] ?? '搜索...') . '" value="' . get_search_query() . '" name="s" />';
        echo '<button type="submit" class="search-submit">';
        echo '<i class="icon-search"></i>';
        echo '</button>';
        echo '</div>';
        echo '</form>';
        
        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = $instance['title'] ?? '搜索';
        $placeholder = $instance['placeholder'] ?? '搜索...';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">标题：</label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('placeholder'); ?>">占位符：</label>
            <input class="widefat" id="<?php echo $this->get_field_id('placeholder'); ?>" name="<?php echo $this->get_field_name('placeholder'); ?>" type="text" value="<?php echo esc_attr($placeholder); ?>">
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['placeholder'] = (!empty($new_instance['placeholder'])) ? strip_tags($new_instance['placeholder']) : '';
        return $instance;
    }
}

/**
 * 关于我们小工具
 */
class QIoooo_About_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'qioooo_about',
            '关于我们',
            array(
                'description' => '显示网站简介和作者信息'
            )
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];
        
        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }
        
        echo '<div class="about-widget">';
        if (!empty($instance['avatar'])) {
            echo '<div class="about-avatar">';
            echo '<img src="' . esc_url($instance['avatar']) . '" alt="' . esc_attr($instance['name']) . '">';
            echo '</div>';
        }
        echo '<div class="about-content">';
        if (!empty($instance['name'])) {
            echo '<h3 class="about-name">' . esc_html($instance['name']) . '</h3>';
        }
        if (!empty($instance['description'])) {
            echo '<div class="about-description">' . wpautop($instance['description']) . '</div>';
        }
        echo '</div>';
        echo '</div>';
        
        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = $instance['title'] ?? '关于我们';
        $name = $instance['name'] ?? '';
        $avatar = $instance['avatar'] ?? '';
        $description = $instance['description'] ?? '';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">标题：</label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('name'); ?>">名称：</label>
            <input class="widefat" id="<?php echo $this->get_field_id('name'); ?>" name="<?php echo $this->get_field_name('name'); ?>" type="text" value="<?php echo esc_attr($name); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('avatar'); ?>">头像URL：</label>
            <input class="widefat" id="<?php echo $this->get_field_id('avatar'); ?>" name="<?php echo $this->get_field_name('avatar'); ?>" type="text" value="<?php echo esc_attr($avatar); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('description'); ?>">简介：</label>
            <textarea class="widefat" id="<?php echo $this->get_field_id('description'); ?>" name="<?php echo $this->get_field_name('description'); ?>" rows="5"><?php echo esc_textarea($description); ?></textarea>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['name'] = (!empty($new_instance['name'])) ? strip_tags($new_instance['name']) : '';
        $instance['avatar'] = (!empty($new_instance['avatar'])) ? esc_url_raw($new_instance['avatar']) : '';
        $instance['description'] = (!empty($new_instance['description'])) ? wp_kses_post($new_instance['description']) : '';
        return $instance;
    }
}

/**
 * 社交媒体链接小工具
 */
class QIoooo_Social_Links_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'qioooo_social_links',
            '社交媒体链接',
            array(
                'description' => '显示社交媒体链接和图标'
            )
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];
        
        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }
        
        $social_links = array(
            'wechat' => get_theme_mod('social_wechat'),
            'weibo' => get_theme_mod('social_weibo'),
            'qq' => get_theme_mod('social_qq'),
            'douban' => get_theme_mod('social_douban')
        );
        
        echo '<ul class="social-links">';
        foreach ($social_links as $platform => $url) {
            if (!empty($url)) {
                echo '<li class="social-link-item">';
                echo '<a href="' . esc_url($url) . '" target="_blank" class="social-link ' . esc_attr($platform) . '">';
                echo '<i class="icon-' . esc_attr($platform) . '"></i>';
                echo '</a>';
                echo '</li>';
            }
        }
        echo '</ul>';
        
        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = $instance['title'] ?? '关注我们';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">标题：</label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>社交媒体链接在主题设置中配置</p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        return $instance;
    }
}

/**
 * 订阅小工具
 */
class QIoooo_Newsletter_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'qioooo_newsletter',
            '邮件订阅',
            array(
                'description' => '显示邮件订阅表单'
            )
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];
        
        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }
        
        echo '<div class="newsletter-widget">';
        if (!empty($instance['description'])) {
            echo '<div class="newsletter-description">' . wpautop($instance['description']) . '</div>';
        }
        echo '<form class="newsletter-form" method="post">';
        echo '<div class="newsletter-input">';
        echo '<input type="email" name="newsletter_email" placeholder="' . esc_attr($instance['placeholder'] ?? '输入您的邮箱') . '" required>';
        echo '<button type="submit" class="newsletter-submit">订阅</button>';
        echo '</div>';
        echo '</form>';
        echo '</div>';
        
        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = $instance['title'] ?? '邮件订阅';
        $description = $instance['description'] ?? '';
        $placeholder = $instance['placeholder'] ?? '输入您的邮箱';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">标题：</label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('description'); ?>">描述：</label>
            <textarea class="widefat" id="<?php echo $this->get_field_id('description'); ?>" name="<?php echo $this->get_field_name('description'); ?>" rows="5"><?php echo esc_textarea($description); ?></textarea>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('placeholder'); ?>">占位符：</label>
            <input class="widefat" id="<?php echo $this->get_field_id('placeholder'); ?>" name="<?php echo $this->get_field_name('placeholder'); ?>" type="text" value="<?php echo esc_attr($placeholder); ?>">
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['description'] = (!empty($new_instance['description'])) ? wp_kses_post($new_instance['description']) : '';
        $instance['placeholder'] = (!empty($new_instance['placeholder'])) ? strip_tags($new_instance['placeholder']) : '';
        return $instance;
    }
}

/**
 * 归档小工具
 */
class QIoooo_Archives_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'qioooo_archives',
            '文章归档',
            array(
                'description' => '显示文章归档列表，支持按月归档'
            )
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];
        
        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }
        
        $archives = wp_get_archives(array(
            'type' => 'monthly',
            'format' => 'html',
            'show_post_count' => $instance['show_count'] ?? true,
            'echo' => false
        ));
        
        echo '<ul class="archives-list">';
        echo $archives;
        echo '</ul>';
        
        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = $instance['title'] ?? '文章归档';
        $show_count = $instance['show_count'] ?? true;
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">标题：</label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_count); ?> id="<?php echo $this->get_field_id('show_count'); ?>" name="<?php echo $this->get_field_name('show_count'); ?>" />
            <label for="<?php echo $this->get_field_id('show_count'); ?>">显示文章数量</label>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['show_count'] = isset($new_instance['show_count']) ? (bool) $new_instance['show_count'] : false;
        return $instance;
    }
}

/**
 * 日历小工具
 */
class QIoooo_Calendar_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'qioooo_calendar',
            '日历',
            array(
                'description' => '显示文章发布日历'
            )
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];
        
        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }
        
        echo '<div class="calendar-widget">';
        get_calendar();
        echo '</div>';
        
        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = $instance['title'] ?? '日历';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">标题：</label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        return $instance;
    }
}

/**
 * 最新评论小工具
 */
class QIoooo_Recent_Comments_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'qioooo_recent_comments',
            '最新评论',
            array(
                'description' => '显示最新的评论列表'
            )
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];
        
        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }
        
        $comments = get_comments(array(
            'number' => $instance['number'] ?? 5,
            'status' => 'approve',
            'post_status' => 'publish'
        ));
        
        echo '<ul class="recent-comments-list">';
        foreach ($comments as $comment) {
            echo '<li class="recent-comment-item">';
            echo '<div class="comment-avatar">';
            echo get_avatar($comment, 40);
            echo '</div>';
            echo '<div class="comment-content">';
            echo '<div class="comment-author">' . $comment->comment_author . '</div>';
            echo '<div class="comment-excerpt">' . wp_trim_words($comment->comment_content, 10) . '</div>';
            echo '<div class="comment-meta">';
            echo '<a href="' . get_comment_link($comment) . '">' . get_the_title($comment->comment_post_ID) . '</a>';
            echo '</div>';
            echo '</div>';
            echo '</li>';
        }
        echo '</ul>';
        
        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = $instance['title'] ?? '最新评论';
        $number = $instance['number'] ?? 5;
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">标题：</label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('number'); ?>">显示数量：</label>
            <input class="tiny-text" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="number" step="1" min="1" value="<?php echo esc_attr($number); ?>">
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['number'] = (!empty($new_instance['number'])) ? absint($new_instance['number']) : 5;
        return $instance;
    }
}

/**
 * 随机文章小工具
 */
class QIoooo_Random_Posts_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'qioooo_random_posts',
            '随机文章',
            array(
                'description' => '显示随机文章列表'
            )
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];
        
        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }
        
        $posts = get_posts(array(
            'numberposts' => $instance['number'] ?? 5,
            'orderby' => 'rand',
            'post_status' => 'publish'
        ));
        
        echo '<ul class="random-posts-list">';
        foreach ($posts as $post) {
            echo '<li class="random-post-item">';
            if (has_post_thumbnail($post->ID)) {
                echo '<a href="' . get_permalink($post->ID) . '" class="post-thumbnail">';
                echo get_the_post_thumbnail($post->ID, 'thumbnail');
                echo '</a>';
            }
            echo '<div class="post-content">';
            echo '<h3 class="post-title"><a href="' . get_permalink($post->ID) . '">' . get_the_title($post->ID) . '</a></h3>';
            echo '<div class="post-meta">';
            echo '<span class="post-date">' . get_the_date('', $post->ID) . '</span>';
            echo '</div>';
            echo '</div>';
            echo '</li>';
        }
        echo '</ul>';
        
        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = $instance['title'] ?? '随机文章';
        $number = $instance['number'] ?? 5;
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">标题：</label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('number'); ?>">显示数量：</label>
            <input class="tiny-text" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="number" step="1" min="1" value="<?php echo esc_attr($number); ?>">
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['number'] = (!empty($new_instance['number'])) ? absint($new_instance['number']) : 5;
        return $instance;
    }
} 