<?php
/**
 * 主模板文件
 *
 * @package QIoooo
 */

get_header();
?>

    <main id="primary" class="site-main">
        <div class="container">
            <?php
            if (have_posts()) :

                if (is_home() && !is_front_page()) :
                    ?>
                    <header>
                        <h1 class="page-title screen-reader-text"><?php single_post_title(); ?></h1>
                    </header>
                <?php
                endif;

                /* 开始文章循环 */
                while (have_posts()) :
                    the_post();

                    /*
                     * 包含文章内容的模板部分
                     */
                    get_template_part('template-parts/content', get_post_type());

                endwhile;

                the_posts_navigation();

            else :

                get_template_part('template-parts/content', 'none');

            endif;
            ?>
        </div><!-- .container -->
    </main><!-- #primary -->

<?php
get_sidebar();
get_footer(); 