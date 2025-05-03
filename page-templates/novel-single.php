<?php
/**
 * Template Name: 小说详情页
 * Description: 显示小说详情的页面模板
 */

get_header(); ?>

<div class="content-area">
    <main id="main" class="site-main">
        <div class="novel-single">
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <header class="entry-header">
                    <h1 class="entry-title"><?php the_title(); ?></h1>
                    <div class="entry-meta">
                        <span class="author"><?php _e('作者：', 'qioooo'); ?><?php the_author(); ?></span>
                        <span class="date"><?php _e('发布时间：', 'qioooo'); ?><?php echo get_the_date(); ?></span>
                        <span class="status"><?php _e('状态：', 'qioooo'); ?><?php echo get_post_meta(get_the_ID(), 'novel_status', true); ?></span>
                    </div>
                </header>

                <div class="entry-content">
                    <div class="novel-cover">
                        <?php if (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('large'); ?>
                        <?php endif; ?>
                    </div>

                    <div class="novel-info">
                        <div class="novel-description">
                            <?php the_content(); ?>
                        </div>

                        <div class="novel-meta">
                            <div class="novel-categories">
                                <?php
                                $categories = get_the_terms(get_the_ID(), 'novel_category');
                                if ($categories && !is_wp_error($categories)) :
                                    echo '<span class="meta-label">' . __('分类：', 'qioooo') . '</span>';
                                    foreach ($categories as $category) {
                                        echo '<a href="' . esc_url(get_term_link($category)) . '">' . $category->name . '</a>';
                                    }
                                endif;
                                ?>
                            </div>

                            <div class="novel-tags">
                                <?php
                                $tags = get_the_terms(get_the_ID(), 'novel_tag');
                                if ($tags && !is_wp_error($tags)) :
                                    echo '<span class="meta-label">' . __('标签：', 'qioooo') . '</span>';
                                    foreach ($tags as $tag) {
                                        echo '<a href="' . esc_url(get_term_link($tag)) . '">' . $tag->name . '</a>';
                                    }
                                endif;
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="novel-chapters">
                        <h2><?php _e('章节列表', 'qioooo'); ?></h2>
                        <?php
                        $chapters = get_posts(array(
                            'post_type' => 'novel_chapter',
                            'posts_per_page' => -1,
                            'meta_key' => 'novel_id',
                            'meta_value' => get_the_ID(),
                            'orderby' => 'meta_value_num',
                            'meta_key' => 'chapter_number',
                            'order' => 'ASC',
                        ));

                        if ($chapters) :
                            echo '<ul class="chapter-list">';
                            foreach ($chapters as $chapter) :
                                echo '<li>';
                                echo '<a href="' . get_permalink($chapter->ID) . '">';
                                echo get_post_meta($chapter->ID, 'chapter_title', true);
                                echo '</a>';
                                echo '</li>';
                            endforeach;
                            echo '</ul>';
                        else :
                            echo '<p>' . __('暂无章节', 'qioooo') . '</p>';
                        endif;
                        ?>
                    </div>
                </div>

                <footer class="entry-footer">
                    <?php
                    // 显示相关小说
                    $related_novels = get_posts(array(
                        'post_type' => 'novel',
                        'posts_per_page' => 3,
                        'post__not_in' => array(get_the_ID()),
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'novel_category',
                                'field' => 'term_id',
                                'terms' => wp_get_post_terms(get_the_ID(), 'novel_category', array('fields' => 'ids')),
                            ),
                        ),
                    ));

                    if ($related_novels) :
                        echo '<div class="related-novels">';
                        echo '<h3>' . __('相关小说', 'qioooo') . '</h3>';
                        echo '<ul>';
                        foreach ($related_novels as $related) :
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