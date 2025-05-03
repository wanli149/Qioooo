<?php
/**
 * 附件页面模板
 */

get_header(); ?>

<div class="site-container">
    <main class="site-main">
        <div class="content-area">
            <?php
            while (have_posts()) :
                the_post();
                ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <header class="entry-header">
                        <?php the_title('<h1 class="entry-title">', '</h1>'); ?>
                    </header>

                    <div class="entry-content">
                        <?php
                        $image_size = apply_filters('qioooo_attachment_size', 'full');
                        echo wp_get_attachment_image(get_the_ID(), $image_size);

                        if (has_excerpt()) :
                            ?>
                            <div class="entry-caption">
                                <?php the_excerpt(); ?>
                            </div>
                        <?php endif; ?>

                        <?php
                        the_content();

                        wp_link_pages(array(
                            'before' => '<div class="page-links">' . esc_html__('页面:', 'qioooo'),
                            'after'  => '</div>',
                        ));
                        ?>
                    </div>

                    <footer class="entry-footer">
                        <?php
                        // 元信息
                        qioooo_entry_meta();
                        ?>
                    </footer>
                </article>

                <?php
                // 如果评论开启且有评论，显示评论
                if (comments_open() || get_comments_number()) :
                    comments_template();
                endif;

                // 导航到上一个/下一个附件
                the_post_navigation(array(
                    'prev_text' => '<span class="nav-subtitle">' . esc_html__('上一个:', 'qioooo') . '</span> <span class="nav-title">%title</span>',
                    'next_text' => '<span class="nav-subtitle">' . esc_html__('下一个:', 'qioooo') . '</span> <span class="nav-title">%title</span>',
                ));

            endwhile;
            ?>
        </div>

        <?php get_sidebar(); ?>
    </main>
</div>

<?php get_footer(); ?> 
 