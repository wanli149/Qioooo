<?php
/**
 * 评论模板
 */

if (post_password_required()) {
    return;
}
?>

<div id="comments" class="comments-area">
    <?php if (have_comments()) : ?>
        <h2 class="comments-title">
            <?php
            $comments_number = get_comments_number();
            if ('1' === $comments_number) {
                printf(
                    /* translators: %s: Post title. */
                    esc_html__('一条评论', 'qioooo'),
                    '<span>' . get_the_title() . '</span>'
                );
            } else {
                printf(
                    /* translators: 1: Number of comments, 2: Post title. */
                    esc_html(_n('%1$s 条评论', '%1$s 条评论', $comments_number, 'qioooo')),
                    number_format_i18n($comments_number),
                    '<span>' . get_the_title() . '</span>'
                );
            }
            ?>
        </h2>

        <ol class="comment-list">
            <?php
            wp_list_comments(array(
                'style'      => 'ol',
                'short_ping' => true,
                'avatar_size' => 48,
                'callback'   => 'qioooo_comment',
            ));
            ?>
        </ol>

        <?php
        the_comments_pagination(array(
            'prev_text' => '<i class="fas fa-chevron-left"></i>',
            'next_text' => '<i class="fas fa-chevron-right"></i>',
            'screen_reader_text' => __('评论分页', 'qioooo'),
        ));
        ?>

    <?php endif; ?>

    <?php if (!comments_open() && get_comments_number() && post_type_supports(get_post_type(), 'comments')) : ?>
        <p class="no-comments"><?php esc_html_e('评论已关闭。', 'qioooo'); ?></p>
    <?php endif; ?>

    <?php
    comment_form(array(
        'title_reply'        => __('发表评论', 'qioooo'),
        'title_reply_to'     => __('回复 %s', 'qioooo'),
        'cancel_reply_link'  => __('取消回复', 'qioooo'),
        'label_submit'       => __('提交评论', 'qioooo'),
        'comment_field'      => '<p class="comment-form-comment"><label for="comment">' . _x('评论', 'noun', 'qioooo') . '</label><textarea id="comment" name="comment" cols="45" rows="8" maxlength="65525" required></textarea></p>',
        'must_log_in'        => '<p class="must-log-in">' . sprintf(
            /* translators: %s: Login URL. */
            __('您必须 <a href="%s">登录</a> 后才能发表评论。', 'qioooo'),
            wp_login_url(apply_filters('the_permalink', get_permalink()))
        ) . '</p>',
        'logged_in_as'       => '<p class="logged-in-as">' . sprintf(
            /* translators: 1: Edit user link, 2: Accessibility text, 3: User name, 4: Logout URL. */
            __('以 <a href="%1$s" aria-label="%2$s">%3$s</a> 身份登录。 <a href="%4$s">退出登录</a>', 'qioooo'),
            get_edit_user_link(),
            /* translators: %s: User name. */
            esc_attr(sprintf(__('以 %s 身份登录。编辑您的个人资料。', 'qioooo'), $user_identity)),
            $user_identity,
            wp_logout_url(apply_filters('the_permalink', get_permalink()))
        ) . '</p>',
    ));
    ?>
</div> 