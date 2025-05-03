<?php
/**
 * 作者页面模板
 */

get_header(); ?>

<div class="site-container">
    <main class="site-main">
        <div class="content-area">
            <header class="page-header">
                <?php
                $author = get_queried_object();
                ?>
                <div class="author-info">
                    <div class="author-avatar">
                        <?php echo get_avatar($author->ID, 96); ?>
                    </div>
                    <div class="author-details">
                        <h1 class="page-title"><?php echo esc_html($author->display_name); ?></h1>
                        <?php if ($author->description) : ?>
                            <div class="author-bio">
                                <?php echo wp_kses_post($author->description); ?>
                            </div>
                        <?php endif; ?>
                        <div class="author-meta">
                            <?php
                            $post_count = count_user_posts($author->ID);
                            printf(
                                /* translators: %s: Number of posts. */
                                _n('%s 篇文章', '%s 篇文章', $post_count, 'qioooo'),
                                number_format_i18n($post_count)
                            );
                            ?>
                        </div>
                    </div>
                </div>
            </header>

            <?php if (have_posts()) : ?>
                <div class="posts-grid">
                    <?php
                    while (have_posts()) :
                        the_post();
                        get_template_part('template-parts/content', get_post_format());
                    endwhile;
                    ?>
                </div>

                <?php
                the_posts_pagination(array(
                    'prev_text' => '<i class="fas fa-chevron-left"></i>',
                    'next_text' => '<i class="fas fa-chevron-right"></i>',
                    'screen_reader_text' => __('分页导航', 'qioooo'),
                ));
                ?>

            <?php else : ?>
                <?php get_template_part('template-parts/content', 'none'); ?>
            <?php endif; ?>
        </div>

        <?php get_sidebar(); ?>
    </main>
</div>

<?php get_footer(); ?> 