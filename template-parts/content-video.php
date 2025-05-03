<?php
/**
 * 视频格式的文章模板部件
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

    <div class="entry-content">
        <?php
        if (is_singular()) :
            // 获取视频内容
            $content = get_the_content();
            $video_url = qioooo_get_video_url($content);
            
            if ($video_url) :
                ?>
                <div class="video-container">
                    <?php echo wp_oembed_get($video_url); ?>
                </div>
                <?php
            endif;

            // 显示文章内容
            echo apply_filters('the_content', $content);

            wp_link_pages(array(
                'before' => '<div class="page-links">' . esc_html__('页面:', 'qioooo'),
                'after'  => '</div>',
            ));
        else :
            // 在列表页显示视频缩略图
            $video_url = qioooo_get_video_url(get_the_content());
            if ($video_url) :
                ?>
                <div class="video-preview">
                    <?php echo wp_oembed_get($video_url, array('width' => 300)); ?>
                </div>
                <?php
            endif;
            ?>
            <div class="entry-summary">
                <?php the_excerpt(); ?>
            </div>
        <?php endif; ?>
    </div>

    <footer class="entry-footer">
        <?php qioooo_entry_footer(); ?>
    </footer>
</article> 