<?php
/**
 * 文章页面模板
 *
 * @package QIoooo
 */

get_header();
?>

    <main id="primary" class="site-main">
        <div class="container">
            <?php
            while (have_posts()) :
                the_post();

                get_template_part('template-parts/content', 'single');

                // 显示文章内容
                the_content();

                // 如果评论开启且允许评论，显示评论模板
                if (comments_open() || get_comments_number()) :
                    comments_template();
                endif;

                // 显示上一篇/下一篇文章导航
                the_post_navigation(
                    array(
                        'prev_text' => '<span class="nav-subtitle">' . esc_html__('上一篇:', 'qioooo') . '</span> <span class="nav-title">%title</span>',
                        'next_text' => '<span class="nav-subtitle">' . esc_html__('下一篇:', 'qioooo') . '</span> <span class="nav-title">%title</span>',
                    )
                );

            endwhile;
            ?>
        </div><!-- .container -->
    </main><!-- #primary -->

<?php
get_sidebar();
get_footer(); 