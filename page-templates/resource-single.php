<?php
/**
 * Template Name: 资源详情页
 * Description: 显示资源详情的页面模板
 */

get_header(); ?>

<div class="content-area">
    <main id="main" class="site-main">
        <div class="resource-single">
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <header class="entry-header">
                    <h1 class="entry-title"><?php the_title(); ?></h1>
                    <div class="entry-meta">
                        <span class="author"><?php _e('作者：', 'qioooo'); ?><?php the_author(); ?></span>
                        <span class="date"><?php _e('发布时间：', 'qioooo'); ?><?php echo get_the_date(); ?></span>
                        <span class="type"><?php _e('类型：', 'qioooo'); ?><?php echo get_post_meta(get_the_ID(), 'resource_type', true); ?></span>
                    </div>
                </header>

                <div class="entry-content">
                    <div class="resource-cover">
                        <?php if (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('large'); ?>
                        <?php endif; ?>
                    </div>

                    <div class="resource-info">
                        <div class="resource-description">
                            <?php the_content(); ?>
                        </div>

                        <div class="resource-meta">
                            <div class="resource-categories">
                                <?php
                                $categories = get_the_terms(get_the_ID(), 'resource_category');
                                if ($categories && !is_wp_error($categories)) :
                                    echo '<span class="meta-label">' . __('分类：', 'qioooo') . '</span>';
                                    foreach ($categories as $category) {
                                        echo '<a href="' . esc_url(get_term_link($category)) . '">' . $category->name . '</a>';
                                    }
                                endif;
                                ?>
                            </div>

                            <div class="resource-tags">
                                <?php
                                $tags = get_the_terms(get_the_ID(), 'resource_tag');
                                if ($tags && !is_wp_error($tags)) :
                                    echo '<span class="meta-label">' . __('标签：', 'qioooo') . '</span>';
                                    foreach ($tags as $tag) {
                                        echo '<a href="' . esc_url(get_term_link($tag)) . '">' . $tag->name . '</a>';
                                    }
                                endif;
                                ?>
                            </div>
                        </div>

                        <div class="resource-download">
                            <?php
                            $download_url = get_post_meta(get_the_ID(), 'download_url', true);
                            $price = get_post_meta(get_the_ID(), 'resource_price', true);
                            $is_free = get_post_meta(get_the_ID(), 'resource_type', true) === 'free';

                            if ($is_free || current_user_can('read')) :
                                echo '<a href="' . esc_url($download_url) . '" class="download-button">';
                                echo $is_free ? __('免费下载', 'qioooo') : __('下载资源', 'qioooo');
                                echo '</a>';
                            else :
                                echo '<div class="purchase-info">';
                                echo '<span class="price">' . sprintf(__('价格：%s', 'qioooo'), $price) . '</span>';
                                echo '<a href="' . esc_url(get_permalink(get_option('woocommerce_checkout_page_id'))) . '" class="purchase-button">';
                                echo __('立即购买', 'qioooo');
                                echo '</a>';
                                echo '</div>';
                            endif;
                            ?>
                        </div>
                    </div>
                </div>

                <footer class="entry-footer">
                    <?php
                    // 显示相关资源
                    $related_resources = get_posts(array(
                        'post_type' => 'resource',
                        'posts_per_page' => 3,
                        'post__not_in' => array(get_the_ID()),
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'resource_category',
                                'field' => 'term_id',
                                'terms' => wp_get_post_terms(get_the_ID(), 'resource_category', array('fields' => 'ids')),
                            ),
                        ),
                    ));

                    if ($related_resources) :
                        echo '<div class="related-resources">';
                        echo '<h3>' . __('相关资源', 'qioooo') . '</h3>';
                        echo '<ul>';
                        foreach ($related_resources as $related) :
                            echo '<li><a href="' . get_permalink($related->ID) . '">' . $related->post_title . '</a></li>';
                        endforeach;
                        echo '</ul>';
                        echo '</div>';
                    endif;
                    ?>
                </footer>
            </article>
        </div>
    </main>
</div>

<?php get_footer(); ?> 