<?php
/**
 * 无内容模板
 *
 * @package QIoooo
 */

?>

<section class="no-results not-found">
    <header class="page-header">
        <h1 class="page-title"><?php esc_html_e('没有找到内容', 'qioooo'); ?></h1>
    </header><!-- .page-header -->

    <div class="page-content">
        <?php
        if (is_home() && current_user_can('publish_posts')) :

            printf(
                '<p>' . wp_kses(
                    /* translators: 1: link to WP admin new post page. */
                    __('准备好发布您的第一篇文章了吗? <a href="%1$s">从这里开始</a>.', 'qioooo'),
                    array(
                        'a' => array(
                            'href' => array(),
                        ),
                    )
                ) . '</p>',
                esc_url(admin_url('post-new.php'))
            );

        elseif (is_search()) :
            ?>

            <p><?php esc_html_e('抱歉，没有找到与您的搜索条件匹配的内容。请尝试使用其他关键词。', 'qioooo'); ?></p>
            <?php
            get_search_form();

        else :
            ?>

            <p><?php esc_html_e('我们似乎找不到您要查找的内容。也许搜索可以帮助。', 'qioooo'); ?></p>
            <?php
            get_search_form();

        endif;
        ?>
    </div><!-- .page-content -->
</section><!-- .no-results --> 