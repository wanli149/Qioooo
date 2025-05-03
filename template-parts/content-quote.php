<?php
/**
 * 引用格式的文章模板部件
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <div class="entry-content">
        <?php
        if (is_singular()) :
            ?>
            <blockquote class="quote-content">
                <?php
                $content = get_the_content();
                $quote = qioooo_get_quote_content($content);
                if ($quote) :
                    echo wp_kses_post($quote);
                else :
                    the_content();
                endif;
                ?>
            </blockquote>
            <?php
            if (has_excerpt()) :
                ?>
                <div class="quote-source">
                    <?php the_excerpt(); ?>
                </div>
            <?php endif; ?>

            <?php
            wp_link_pages(array(
                'before' => '<div class="page-links">' . esc_html__('页面:', 'qioooo'),
                'after'  => '</div>',
            ));
        else :
            ?>
            <div class="quote-preview">
                <blockquote class="quote-content">
                    <?php
                    $content = get_the_content();
                    $quote = qioooo_get_quote_content($content);
                    if ($quote) :
                        echo wp_kses_post($quote);
                    else :
                        the_excerpt();
                    endif;
                    ?>
                </blockquote>
                <?php if (has_excerpt()) : ?>
                    <div class="quote-source">
                        <?php the_excerpt(); ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <footer class="entry-footer">
        <?php qioooo_entry_footer(); ?>
    </footer>
</article> 