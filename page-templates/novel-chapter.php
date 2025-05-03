<?php
/**
 * Template Name: 小说章节页
 * Description: 显示小说章节内容的页面模板
 */

get_header(); ?>

<div class="content-area">
    <main id="main" class="site-main">
        <div class="novel-chapter">
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <header class="entry-header">
                    <h1 class="entry-title"><?php echo get_post_meta(get_the_ID(), 'chapter_title', true); ?></h1>
                    <div class="entry-meta">
                        <?php
                        $novel_id = get_post_meta(get_the_ID(), 'novel_id', true);
                        if ($novel_id) :
                            $novel = get_post($novel_id);
                            echo '<span class="novel-title">';
                            echo '<a href="' . get_permalink($novel_id) . '">' . $novel->post_title . '</a>';
                            echo '</span>';
                        endif;
                        ?>
                        <span class="author"><?php _e('作者：', 'qioooo'); ?><?php the_author(); ?></span>
                        <span class="date"><?php _e('发布时间：', 'qioooo'); ?><?php echo get_the_date(); ?></span>
                    </div>
                </header>

                <div class="entry-content">
                    <div class="chapter-navigation">
                        <?php
                        // 获取上一章和下一章
                        $prev_chapter = get_posts(array(
                            'post_type' => 'novel_chapter',
                            'posts_per_page' => 1,
                            'meta_key' => 'novel_id',
                            'meta_value' => $novel_id,
                            'meta_query' => array(
                                array(
                                    'key' => 'chapter_number',
                                    'value' => get_post_meta(get_the_ID(), 'chapter_number', true),
                                    'compare' => '<',
                                ),
                            ),
                            'orderby' => 'meta_value_num',
                            'meta_key' => 'chapter_number',
                            'order' => 'DESC',
                        ));

                        $next_chapter = get_posts(array(
                            'post_type' => 'novel_chapter',
                            'posts_per_page' => 1,
                            'meta_key' => 'novel_id',
                            'meta_value' => $novel_id,
                            'meta_query' => array(
                                array(
                                    'key' => 'chapter_number',
                                    'value' => get_post_meta(get_the_ID(), 'chapter_number', true),
                                    'compare' => '>',
                                ),
                            ),
                            'orderby' => 'meta_value_num',
                            'meta_key' => 'chapter_number',
                            'order' => 'ASC',
                        ));

                        if ($prev_chapter) :
                            echo '<a href="' . get_permalink($prev_chapter[0]->ID) . '" class="prev-chapter">';
                            echo __('上一章', 'qioooo');
                            echo '</a>';
                        endif;

                        if ($next_chapter) :
                            echo '<a href="' . get_permalink($next_chapter[0]->ID) . '" class="next-chapter">';
                            echo __('下一章', 'qioooo');
                            echo '</a>';
                        endif;
                        ?>
                    </div>

                    <div class="chapter-content">
                        <?php the_content(); ?>
                    </div>

                    <div class="chapter-navigation bottom">
                        <?php
                        if ($prev_chapter) :
                            echo '<a href="' . get_permalink($prev_chapter[0]->ID) . '" class="prev-chapter">';
                            echo __('上一章', 'qioooo');
                            echo '</a>';
                        endif;

                        if ($next_chapter) :
                            echo '<a href="' . get_permalink($next_chapter[0]->ID) . '" class="next-chapter">';
                            echo __('下一章', 'qioooo');
                            echo '</a>';
                        endif;
                        ?>
                    </div>
                </div>

                <footer class="entry-footer">
                    <div class="chapter-options">
                        <button class="font-size-increase"><?php _e('增大字体', 'qioooo'); ?></button>
                        <button class="font-size-decrease"><?php _e('减小字体', 'qioooo'); ?></button>
                        <button class="theme-toggle"><?php _e('切换主题', 'qioooo'); ?></button>
                    </div>
                </footer>
            </article>
        </div>
    </main>
</div>

<?php get_footer(); ?> 