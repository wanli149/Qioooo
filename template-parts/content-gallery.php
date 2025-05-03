<?php
/**
 * 相册格式的文章模板部件
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <header class="entry-header">
        <?php
        if (is_singular()) :
            the_title('<h1 class="entry-title">', '</h1>');
        else :
            the_title('<h2 class="entry-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></h2>');
        endif;

        if ('post' === get_post_type()) :
            ?>
            <div class="entry-meta">
                <?php
                qioooo_posted_on();
                qioooo_posted_by();
                ?>
            </div>
        <?php endif; ?>
    </header>

    <?php qioooo_post_thumbnail(); ?>

    <div class="entry-content">
        <?php
        if (is_singular()) :
            the_content();

            wp_link_pages(array(
                'before' => '<div class="page-links">' . esc_html__('页面:', 'qioooo'),
                'after'  => '</div>',
            ));
        else :
            ?>
            <div class="gallery-preview">
                <?php
                $gallery = get_post_gallery(get_the_ID(), false);
                if ($gallery) :
                    $ids = explode(',', $gallery['ids']);
                    $count = count($ids);
                    $display_count = min(3, $count);
                    for ($i = 0; $i < $display_count; $i++) :
                        $image = wp_get_attachment_image($ids[$i], 'thumbnail');
                        if ($image) :
                            echo '<div class="gallery-preview-item">' . $image . '</div>';
                        endif;
                    endfor;
                    if ($count > 3) :
                        echo '<div class="gallery-preview-more">+' . ($count - 3) . '</div>';
                    endif;
                endif;
                ?>
            </div>
            <div class="entry-summary">
                <?php the_excerpt(); ?>
            </div>
        <?php endif; ?>
    </div>

    <footer class="entry-footer">
        <?php qioooo_entry_footer(); ?>
    </footer>
</article>