<?php
/**
 * Template Name: 资源归档页
 * Description: 显示资源列表的页面模板
 */

get_header(); ?>

<div class="content-area">
    <main id="main" class="site-main">
        <div class="resource-archive">
            <header class="page-header">
                <h1 class="page-title"><?php echo get_the_title(); ?></h1>
                <?php if (get_the_excerpt()) : ?>
                    <div class="page-description"><?php echo get_the_excerpt(); ?></div>
                <?php endif; ?>
            </header>

            <div class="resource-filters">
                <form class="resource-filter-form" method="get">
                    <div class="filter-group">
                        <label for="resource-category"><?php _e('分类', 'qioooo'); ?></label>
                        <?php
                        wp_dropdown_categories(array(
                            'show_option_all' => __('所有分类', 'qioooo'),
                            'taxonomy' => 'resource_category',
                            'name' => 'resource-category',
                            'value_field' => 'slug',
                            'selected' => get_query_var('resource-category'),
                        ));
                        ?>
                    </div>
                    <div class="filter-group">
                        <label for="resource-type"><?php _e('类型', 'qioooo'); ?></label>
                        <select name="resource-type" id="resource-type">
                            <option value=""><?php _e('所有类型', 'qioooo'); ?></option>
                            <option value="free" <?php selected(get_query_var('resource-type'), 'free'); ?>><?php _e('免费', 'qioooo'); ?></option>
                            <option value="premium" <?php selected(get_query_var('resource-type'), 'premium'); ?>><?php _e('付费', 'qioooo'); ?></option>
                        </select>
                    </div>
                    <button type="submit" class="filter-submit"><?php _e('筛选', 'qioooo'); ?></button>
                </form>
            </div>

            <div class="resource-grid">
                <?php
                $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
                $args = array(
                    'post_type' => 'resource',
                    'posts_per_page' => 12,
                    'paged' => $paged,
                );

                $resource_query = new WP_Query($args);

                if ($resource_query->have_posts()) :
                    while ($resource_query->have_posts()) : $resource_query->the_post();
                        get_template_part('template-parts/content', 'resource');
                    endwhile;

                    // 分页
                    echo '<div class="pagination">';
                    echo paginate_links(array(
                        'total' => $resource_query->max_num_pages,
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