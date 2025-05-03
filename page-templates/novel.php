<?php
/**
 * Template Name: 小说列表页
 * Description: 显示小说列表的页面模板
 */

get_header(); ?>

<div class="content-area">
    <main id="main" class="site-main">
        <div class="novel-archive">
            <header class="page-header">
                <h1 class="page-title"><?php echo get_the_title(); ?></h1>
                <?php if (get_the_excerpt()) : ?>
                    <div class="page-description"><?php echo get_the_excerpt(); ?></div>
                <?php endif; ?>
            </header>

            <div class="novel-filters">
                <form class="novel-filter-form" method="get">
                    <div class="filter-group">
                        <label for="novel-category"><?php _e('分类', 'qioooo'); ?></label>
                        <?php
                        wp_dropdown_categories(array(
                            'show_option_all' => __('所有分类', 'qioooo'),
                            'taxonomy' => 'novel_category',
                            'name' => 'novel-category',
                            'value_field' => 'slug',
                            'selected' => get_query_var('novel-category'),
                        ));
                        ?>
                    </div>
                    <div class="filter-group">
                        <label for="novel-status"><?php _e('状态', 'qioooo'); ?></label>
                        <select name="novel-status" id="novel-status">
                            <option value=""><?php _e('所有状态', 'qioooo'); ?></option>
                            <option value="ongoing" <?php selected(get_query_var('novel-status'), 'ongoing'); ?>><?php _e('连载中', 'qioooo'); ?></option>
                            <option value="completed" <?php selected(get_query_var('novel-status'), 'completed'); ?>><?php _e('已完结', 'qioooo'); ?></option>
                        </select>
                    </div>
                    <button type="submit" class="filter-submit"><?php _e('筛选', 'qioooo'); ?></button>
                </form>
            </div>

            <div class="novel-grid">
                <?php
                $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
                $args = array(
                    'post_type' => 'novel',
                    'posts_per_page' => 12,
                    'paged' => $paged,
                );

                $novel_query = new WP_Query($args);

                if ($novel_query->have_posts()) :
                    while ($novel_query->have_posts()) : $novel_query->the_post();
                        get_template_part('template-parts/content', 'novel');
                    endwhile;

                    // 分页
                    echo '<div class="pagination">';
                    echo paginate_links(array(
                        'total' => $novel_query->max_num_pages,
                        'current' => $paged,
                        'prev_text' => __('上一页', 'qioooo'),
                        'next_text' => __('下一页', 'qioooo'),
                    ));
                    echo '</div>';

                    wp_reset_postdata();
                else :
                    get_template_part('template-parts/content', 'none');
                endif;
                ?>
            </div>
        </div>
    </main>
</div>

<?php get_footer(); ?> 