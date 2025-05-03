<?php
/**
 * 搜索页面模板
 *
 * @package QIoooo
 */

get_header();
?>

    <main id="primary" class="site-main">
        <div class="container">
            <?php if (have_posts()) : ?>

                <header class="page-header">
                    <h1 class="page-title">
                        <?php
                        /* translators: %s: search query. */
                        printf(esc_html__('搜索结果: %s', 'qioooo'), '<span>' . get_search_query() . '</span>');
                        ?>
                    </h1>
                </header><!-- .page-header -->

                <?php
                /* 开始循环 */
                while (have_posts()) :
                    the_post();

                    /**
                     * 运行搜索结果的循环模板
                     */
                    get_template_part('template-parts/content', 'search');

                endwhile;

                the_posts_pagination(
                    array(
                        'prev_text'          => esc_html__('上一页', 'qioooo'),
                        'next_text'          => esc_html__('下一页', 'qioooo'),
                        'before_page_number' => '<span class="meta-nav screen-reader-text">' . esc_html__('第', 'qioooo') . ' </span>',
                    )
                );

            else :

                get_template_part('template-parts/content', 'none');

            endif;
            ?>
        </div><!-- .container -->
    </main><!-- #primary -->

<?php
get_sidebar();
get_footer(); 