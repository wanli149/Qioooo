<?php
/**
 * 404页面模板
 *
 * @package QIoooo
 */

get_header();
?>

<div class="error-404">
    <div class="container">
        <div class="error-content">
            <h1 class="error-title"><?php _e('404', 'qioooo'); ?></h1>
            <h2 class="error-subtitle"><?php _e('页面未找到', 'qioooo'); ?></h2>
            <p class="error-description">
                <?php _e('抱歉，您访问的页面不存在或已被移除。', 'qioooo'); ?>
            </p>
            
            <div class="error-actions">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="button button-primary">
                    <?php _e('返回首页', 'qioooo'); ?>
                </a>
                <a href="javascript:history.back()" class="button">
                    <?php _e('返回上一页', 'qioooo'); ?>
                </a>
            </div>
            
            <div class="error-search">
                <h3><?php _e('搜索网站内容', 'qioooo'); ?></h3>
                <?php get_search_form(); ?>
            </div>
            
            <div class="error-suggestions">
                <h3><?php _e('您可能感兴趣的内容', 'qioooo'); ?></h3>
                <div class="suggestions-grid">
                    <?php
                    // 获取最近发布的小说
                    $recent_novels = new WP_Query(array(
                        'post_type' => 'novel',
                        'posts_per_page' => 3,
                        'orderby' => 'date',
                        'order' => 'DESC'
                    ));
                    
                    if ($recent_novels->have_posts()) :
                        while ($recent_novels->have_posts()) : $recent_novels->the_post();
                            ?>
                            <div class="suggestion-item">
                                <a href="<?php the_permalink(); ?>">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <?php the_post_thumbnail('medium'); ?>
                                    <?php endif; ?>
                                    <h4><?php the_title(); ?></h4>
                                </a>
                            </div>
                            <?php
                        endwhile;
                    endif;
                    wp_reset_postdata();
                    
                    // 获取最近发布的资源
                    $recent_resources = new WP_Query(array(
                        'post_type' => 'resource',
                        'posts_per_page' => 3,
                        'orderby' => 'date',
                        'order' => 'DESC'
                    ));
                    
                    if ($recent_resources->have_posts()) :
                        while ($recent_resources->have_posts()) : $recent_resources->the_post();
                            ?>
                            <div class="suggestion-item">
                                <a href="<?php the_permalink(); ?>">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <?php the_post_thumbnail('medium'); ?>
                                    <?php endif; ?>
                                    <h4><?php the_title(); ?></h4>
                                </a>
                            </div>
                            <?php
                        endwhile;
                    endif;
                    wp_reset_postdata();
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
get_footer(); 