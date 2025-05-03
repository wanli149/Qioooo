<?php
/**
 * 聊天格式的文章模板部件
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
            ?>
            <div class="chat-content">
                <?php
                $content = get_the_content();
                $chat_lines = qioooo_get_chat_lines($content);
                
                if ($chat_lines) :
                    foreach ($chat_lines as $line) :
                        ?>
                        <div class="chat-line">
                            <span class="chat-speaker"><?php echo esc_html($line['speaker']); ?>:</span>
                            <span class="chat-message"><?php echo wp_kses_post($line['message']); ?></span>
                        </div>
                        <?php
                    endforeach;
                else :
                    the_content();
                endif;
                ?>
            </div>

            <?php
            wp_link_pages(array(
                'before' => '<div class="page-links">' . esc_html__('页面:', 'qioooo'),
                'after'  => '</div>',
            ));
        else :
            ?>
            <div class="chat-preview">
                <?php
                $content = get_the_content();
                $chat_lines = qioooo_get_chat_lines($content);
                
                if ($chat_lines) :
                    $preview_lines = array_slice($chat_lines, 0, 3);
                    foreach ($preview_lines as $line) :
                        ?>
                        <div class="chat-line">
                            <span class="chat-speaker"><?php echo esc_html($line['speaker']); ?>:</span>
                            <span class="chat-message"><?php echo wp_kses_post($line['message']); ?></span>
                        </div>
                        <?php
                    endforeach;
                    if (count($chat_lines) > 3) :
                        echo '<div class="chat-more">+' . (count($chat_lines) - 3) . ' 条消息</div>';
                    endif;
                else :
                    the_excerpt();
                endif;
                ?>
            </div>
        <?php endif; ?>
    </div>

    <footer class="entry-footer">
        <?php qioooo_entry_footer(); ?>
    </footer>
</article> 