<?php
/**
 * 作者页面模板
 *
 * @package QIoooo
 */

get_header();
?>

<div class="author-page">
    <div class="container">
        <div class="author-header">
            <div class="author-avatar">
                <?php echo get_avatar(get_the_author_meta('ID'), 150); ?>
            </div>
            
            <div class="author-info">
                <h1 class="author-name"><?php the_author(); ?></h1>
                
                <div class="author-meta">
                    <?php if (get_the_author_meta('description')) : ?>
                        <div class="author-bio">
                            <?php the_author_meta('description'); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="author-stats">
                        <div class="stat-item">
                            <span class="stat-label"><?php _e('文章', 'qioooo'); ?></span>
                            <span class="stat-number"><?php echo count_user_posts(get_the_author_meta('ID'), 'post'); ?></span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label"><?php _e('小说', 'qioooo'); ?></span>
                            <span class="stat-number"><?php echo count_user_posts(get_the_author_meta('ID'), 'novel'); ?></span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label"><?php _e('资源', 'qioooo'); ?></span>
                            <span class="stat-number"><?php echo count_user_posts(get_the_author_meta('ID'), 'resource'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="author-content">
            <div class="author-tabs">
                <ul class="tab-nav">
                    <li class="active"><a href="#posts"><?php _e('文章', 'qioooo'); ?></a></li>
                    <li><a href="#novels"><?php _e('小说', 'qioooo'); ?></a></li>
                    <li><a href="#resources"><?php _e('资源', 'qioooo'); ?></a></li>
                </ul>
                
                <div class="tab-content">
                    <div id="posts" class="tab-pane active">
                        <?php
                        $posts = new WP_Query(array(
                            'post_type' => 'post',
                            'author' => get_the_author_meta('ID'),
                            'posts_per_page' => 10,
                            'paged' => get_query_var('paged') ? get_query_var('paged') : 1
                        ));
                        
                        if ($posts->have_posts()) :
                            echo '<div class="post-list">';
                            while ($posts->have_posts()) : $posts->the_post();
                                get_template_part('template-parts/content', 'post');
                            endwhile;
                            echo '</div>';
                            
                            echo '<div class="pagination">';
                            echo paginate_links(array(
                                'total' => $posts->max_num_pages,
                                'current' => max(1, get_query_var('paged')),
                                'prev_text' => __('&laquo; 上一页', 'qioooo'),
                                'next_text' => __('下一页 &raquo;', 'qioooo')
                            ));
                            echo '</div>';
                        else :
                            echo '<p class="no-posts">' . __('该作者还没有发布任何文章。', 'qioooo') . '</p>';
                        endif;
                        
                        wp_reset_postdata();
                        ?>
                    </div>
                    
                    <div id="novels" class="tab-pane">
                        <?php
                        $novels = new WP_Query(array(
                            'post_type' => 'novel',
                            'author' => get_the_author_meta('ID'),
                            'posts_per_page' => 10,
                            'paged' => get_query_var('paged') ? get_query_var('paged') : 1
                        ));
                        
                        if ($novels->have_posts()) :
                            echo '<div class="novel-list">';
                            while ($novels->have_posts()) : $novels->the_post();
                                get_template_part('template-parts/content', 'novel');
                            endwhile;
                            echo '</div>';
                            
                            echo '<div class="pagination">';
                            echo paginate_links(array(
                                'total' => $novels->max_num_pages,
                                'current' => max(1, get_query_var('paged')),
                                'prev_text' => __('&laquo; 上一页', 'qioooo'),
                                'next_text' => __('下一页 &raquo;', 'qioooo')
                            ));
                            echo '</div>';
                        else :
                            echo '<p class="no-posts">' . __('该作者还没有发布任何小说。', 'qioooo') . '</p>';
                        endif;
                        
                        wp_reset_postdata();
                        ?>
                    </div>
                    
                    <div id="resources" class="tab-pane">
                        <?php
                        $resources = new WP_Query(array(
                            'post_type' => 'resource',
                            'author' => get_the_author_meta('ID'),
                            'posts_per_page' => 10,
                            'paged' => get_query_var('paged') ? get_query_var('paged') : 1
                        ));
                        
                        if ($resources->have_posts()) :
                            echo '<div class="resource-list">';
                            while ($resources->have_posts()) : $resources->the_post();
                                get_template_part('template-parts/content', 'resource');
                            endwhile;
                            echo '</div>';
                            
                            echo '<div class="pagination">';
                            echo paginate_links(array(
                                'total' => $resources->max_num_pages,
                                'current' => max(1, get_query_var('paged')),
                                'prev_text' => __('&laquo; 上一页', 'qioooo'),
                                'next_text' => __('下一页 &raquo;', 'qioooo')
                            ));
                            echo '</div>';
                        else :
                            echo '<p class="no-posts">' . __('该作者还没有发布任何资源。', 'qioooo') . '</p>';
                        endif;
                        
                        wp_reset_postdata();
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
get_footer(); 