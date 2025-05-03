<?php
/**
 * 状态格式的文章模板部件
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
            <div class="status-content">
                <div class="status-avatar">
                    <?php echo get_avatar(get_the_author_meta('ID'), 48); ?>
                </div>
                <div class="status-text">
                    <?php the_content(); ?>
                </div>
            </div>

            <?php
            wp_link_pages(array(
                'before' => '<div class="page-links">' . esc_html__('页面:', 'qioooo'),
                'after'  => '</div>',
            ));
        else :
            ?>
            <div class="status-preview">
                <div class="status-avatar">
                    <?php echo get_avatar(get_the_author_meta('ID'), 32); ?>
                </div>
                <div class="status-text">
                    <?php the_excerpt(); ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <footer class="entry-footer">
        <?php qioooo_entry_footer(); ?>
    </footer>
</article> 