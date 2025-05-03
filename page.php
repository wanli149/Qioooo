<?php
/**
 * 页面模板
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

                get_template_part('template-parts/content', 'page');

                // 如果评论开启且允许评论，显示评论模板
                if (comments_open() || get_comments_number()) :
                    comments_template();
                endif;

            endwhile;
            ?>
        </div><!-- .container -->
    </main><!-- #primary -->

<?php
get_sidebar();
get_footer(); 