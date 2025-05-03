<?php
/**
 * 小工具模板
 */

// 侧边栏模板
function qioooo_sidebar() {
    if (is_active_sidebar('sidebar-1')) {
        ?>
        <aside class="sidebar">
            <?php dynamic_sidebar('sidebar-1'); ?>
        </aside>
        <?php
    }
}

// 页脚小工具区域模板
function qioooo_footer_widgets() {
    if (is_active_sidebar('footer-1')) {
        $columns = get_theme_mod('qioooo_footer_widgets_columns', 3);
        ?>
        <div class="footer-widgets columns-<?php echo esc_attr($columns); ?>">
            <?php dynamic_sidebar('footer-1'); ?>
        </div>
        <?php
    }
}

// 首页小工具区域模板
function qioooo_home_widgets() {
    if (is_active_sidebar('home-1')) {
        $columns = get_theme_mod('qioooo_home_widgets_columns', 3);
        ?>
        <div class="home-widgets columns-<?php echo esc_attr($columns); ?>">
            <?php dynamic_sidebar('home-1'); ?>
        </div>
        <?php
    }
}

// 最近文章小工具模板
function qioooo_recent_posts_widget($args, $instance) {
    $title = !empty($instance['title']) ? $instance['title'] : '';
    $number = !empty($instance['number']) ? $instance['number'] : 5;
    $show_thumbnail = !empty($instance['show_thumbnail']) ? true : false;
    $show_date = !empty($instance['show_date']) ? true : false;
    $show_author = !empty($instance['show_author']) ? true : false;

    echo $args['before_widget'];
    if (!empty($title)) {
        echo $args['before_title'] . $title . $args['after_title'];
    }

    $recent_posts = new WP_Query(array(
        'posts_per_page' => $number,
        'post_status' => 'publish',
        'ignore_sticky_posts' => true
    ));

    if ($recent_posts->have_posts()) {
        echo '<ul class="recent-posts-list">';
        while ($recent_posts->have_posts()) {
            $recent_posts->the_post();
            ?>
            <li class="recent-post-item">
                <?php if ($show_thumbnail && has_post_thumbnail()) { ?>
                    <div class="post-thumbnail">
                        <a href="<?php the_permalink(); ?>">
                            <?php the_post_thumbnail('thumbnail'); ?>
                        </a>
                    </div>
                <?php } ?>
                <div class="post-content">
                    <h3 class="post-title">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h3>
                    <?php if ($show_date || $show_author) { ?>
                        <div class="post-meta">
                            <?php if ($show_date) { ?>
                                <span class="post-date">
                                    <i class="fas fa-calendar"></i>
                                    <?php echo get_the_date(); ?>
                                </span>
                            <?php } ?>
                            <?php if ($show_author) { ?>
                                <span class="post-author">
                                    <i class="fas fa-user"></i>
                                    <?php the_author(); ?>
                                </span>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            </li>
            <?php
        }
        echo '</ul>';
        wp_reset_postdata();
    }

    echo $args['after_widget'];
}

// 热门文章小工具模板
function qioooo_popular_posts_widget($args, $instance) {
    $title = !empty($instance['title']) ? $instance['title'] : '';
    $number = !empty($instance['number']) ? $instance['number'] : 5;
    $show_thumbnail = !empty($instance['show_thumbnail']) ? true : false;
    $show_views = !empty($instance['show_views']) ? true : false;
    $show_comments = !empty($instance['show_comments']) ? true : false;

    echo $args['before_widget'];
    if (!empty($title)) {
        echo $args['before_title'] . $title . $args['after_title'];
    }

    $popular_posts = new WP_Query(array(
        'posts_per_page' => $number,
        'post_status' => 'publish',
        'ignore_sticky_posts' => true,
        'orderby' => 'comment_count',
        'order' => 'DESC'
    ));

    if ($popular_posts->have_posts()) {
        echo '<ul class="popular-posts-list">';
        while ($popular_posts->have_posts()) {
            $popular_posts->the_post();
            ?>
            <li class="popular-post-item">
                <?php if ($show_thumbnail && has_post_thumbnail()) { ?>
                    <div class="post-thumbnail">
                        <a href="<?php the_permalink(); ?>">
                            <?php the_post_thumbnail('thumbnail'); ?>
                        </a>
                    </div>
                <?php } ?>
                <div class="post-content">
                    <h3 class="post-title">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h3>
                    <?php if ($show_views || $show_comments) { ?>
                        <div class="post-meta">
                            <?php if ($show_views) { ?>
                                <span class="post-views">
                                    <i class="fas fa-eye"></i>
                                    <?php echo get_post_views(); ?>
                                </span>
                            <?php } ?>
                            <?php if ($show_comments) { ?>
                                <span class="post-comments">
                                    <i class="fas fa-comments"></i>
                                    <?php echo get_comments_number(); ?>
                                </span>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            </li>
            <?php
        }
        echo '</ul>';
        wp_reset_postdata();
    }

    echo $args['after_widget'];
}

// 分类目录小工具模板
function qioooo_categories_widget($args, $instance) {
    $title = !empty($instance['title']) ? $instance['title'] : '';
    $show_count = !empty($instance['show_count']) ? true : false;
    $hierarchical = !empty($instance['hierarchical']) ? true : false;

    echo $args['before_widget'];
    if (!empty($title)) {
        echo $args['before_title'] . $title . $args['after_title'];
    }

    $categories = get_categories(array(
        'orderby' => 'name',
        'order' => 'ASC',
        'hide_empty' => true,
        'hierarchical' => $hierarchical
    ));

    if (!empty($categories)) {
        echo '<ul class="categories-list">';
        foreach ($categories as $category) {
            ?>
            <li class="category-item">
                <a href="<?php echo get_category_link($category->term_id); ?>">
                    <span class="category-name"><?php echo $category->name; ?></span>
                    <?php if ($show_count) { ?>
                        <span class="category-count"><?php echo $category->count; ?></span>
                    <?php } ?>
                </a>
            </li>
            <?php
        }
        echo '</ul>';
    }

    echo $args['after_widget'];
}

// 标签云小工具模板
function qioooo_tags_widget($args, $instance) {
    $title = !empty($instance['title']) ? $instance['title'] : '';
    $number = !empty($instance['number']) ? $instance['number'] : 20;
    $show_count = !empty($instance['show_count']) ? true : false;

    echo $args['before_widget'];
    if (!empty($title)) {
        echo $args['before_title'] . $title . $args['after_title'];
    }

    $tags = get_tags(array(
        'number' => $number,
        'orderby' => 'count',
        'order' => 'DESC'
    ));

    if (!empty($tags)) {
        echo '<div class="tagcloud">';
        foreach ($tags as $tag) {
            ?>
            <a href="<?php echo get_tag_link($tag->term_id); ?>" class="tag-link">
                <?php echo $tag->name; ?>
                <?php if ($show_count) { ?>
                    <span class="tag-count"><?php echo $tag->count; ?></span>
                <?php } ?>
            </a>
            <?php
        }
        echo '</div>';
    }

    echo $args['after_widget'];
}

// 搜索小工具模板
function qioooo_search_widget($args, $instance) {
    $title = !empty($instance['title']) ? $instance['title'] : '';
    $placeholder = !empty($instance['placeholder']) ? $instance['placeholder'] : __('搜索...', 'qioooo');

    echo $args['before_widget'];
    if (!empty($title)) {
        echo $args['before_title'] . $title . $args['after_title'];
    }

    ?>
    <form class="search-form" action="<?php echo esc_url(home_url('/')); ?>" method="get">
        <div class="search-box">
            <div class="search-field">
                <input type="text" name="s" placeholder="<?php echo esc_attr($placeholder); ?>" value="<?php echo get_search_query(); ?>">
            </div>
            <button type="submit" class="search-submit">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </form>
    <?php

    echo $args['after_widget'];
}

// 关于我们小工具模板
function qioooo_about_widget($args, $instance) {
    $title = !empty($instance['title']) ? $instance['title'] : '';
    $avatar = !empty($instance['avatar']) ? $instance['avatar'] : '';
    $name = !empty($instance['name']) ? $instance['name'] : '';
    $description = !empty($instance['description']) ? $instance['description'] : '';

    echo $args['before_widget'];
    if (!empty($title)) {
        echo $args['before_title'] . $title . $args['after_title'];
    }

    ?>
    <div class="about-widget">
        <?php if (!empty($avatar)) { ?>
            <div class="about-avatar">
                <img src="<?php echo esc_url($avatar); ?>" alt="<?php echo esc_attr($name); ?>">
            </div>
        <?php } ?>
        <?php if (!empty($name)) { ?>
            <h3 class="about-name"><?php echo esc_html($name); ?></h3>
        <?php } ?>
        <?php if (!empty($description)) { ?>
            <div class="about-description"><?php echo wp_kses_post($description); ?></div>
        <?php } ?>
    </div>
    <?php

    echo $args['after_widget'];
}

// 社交媒体链接小工具模板
function qioooo_social_links_widget($args, $instance) {
    $title = !empty($instance['title']) ? $instance['title'] : '';
    $wechat = !empty($instance['wechat']) ? $instance['wechat'] : '';
    $weibo = !empty($instance['weibo']) ? $instance['weibo'] : '';
    $qq = !empty($instance['qq']) ? $instance['qq'] : '';
    $douban = !empty($instance['douban']) ? $instance['douban'] : '';

    echo $args['before_widget'];
    if (!empty($title)) {
        echo $args['before_title'] . $title . $args['after_title'];
    }

    ?>
    <ul class="social-links">
        <?php if (!empty($wechat)) { ?>
            <li>
                <a href="<?php echo esc_url($wechat); ?>" class="social-link wechat" target="_blank">
                    <i class="fab fa-weixin"></i>
                </a>
            </li>
        <?php } ?>
        <?php if (!empty($weibo)) { ?>
            <li>
                <a href="<?php echo esc_url($weibo); ?>" class="social-link weibo" target="_blank">
                    <i class="fab fa-weibo"></i>
                </a>
            </li>
        <?php } ?>
        <?php if (!empty($qq)) { ?>
            <li>
                <a href="<?php echo esc_url($qq); ?>" class="social-link qq" target="_blank">
                    <i class="fab fa-qq"></i>
                </a>
            </li>
        <?php } ?>
        <?php if (!empty($douban)) { ?>
            <li>
                <a href="<?php echo esc_url($douban); ?>" class="social-link douban" target="_blank">
                    <i class="fab fa-douban"></i>
                </a>
            </li>
        <?php } ?>
    </ul>
    <?php

    echo $args['after_widget'];
}

// 订阅小工具模板
function qioooo_newsletter_widget($args, $instance) {
    $title = !empty($instance['title']) ? $instance['title'] : '';
    $description = !empty($instance['description']) ? $instance['description'] : '';
    $placeholder = !empty($instance['placeholder']) ? $instance['placeholder'] : __('输入您的邮箱', 'qioooo');

    echo $args['before_widget'];
    if (!empty($title)) {
        echo $args['before_title'] . $title . $args['after_title'];
    }

    ?>
    <div class="newsletter-widget">
        <?php if (!empty($description)) { ?>
            <div class="newsletter-description"><?php echo wp_kses_post($description); ?></div>
        <?php } ?>
        <form class="newsletter-form" method="post">
            <?php wp_nonce_field('qioooo_newsletter_nonce', 'newsletter_nonce'); ?>
            <div class="newsletter-input">
                <input type="email" name="email" placeholder="<?php echo esc_attr($placeholder); ?>" required>
            </div>
            <button type="submit" class="newsletter-submit">
                <i class="fas fa-paper-plane"></i>
            </button>
        </form>
    </div>
    <?php

    echo $args['after_widget'];
} 