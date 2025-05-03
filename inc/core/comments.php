<?php
/**
 * 评论系统功能
 *
 * @package QIoooo
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 自定义评论列表
 */
function qioooo_comment($comment, $args, $depth) {
    $GLOBALS['comment'] = $comment;
    $comment_class = comment_class('', null, null, false);
    ?>
    <li <?php echo $comment_class; ?> id="comment-<?php comment_ID(); ?>">
        <div class="comment-body">
            <div class="comment-author vcard">
                <?php echo get_avatar($comment, 60); ?>
                <cite class="fn"><?php comment_author_link(); ?></cite>
                <span class="says"><?php _e('说：', 'qioooo'); ?></span>
            </div>
            
            <?php if ($comment->comment_approved == '0') : ?>
                <em class="comment-awaiting-moderation"><?php _e('您的评论正在等待审核。', 'qioooo'); ?></em>
            <?php endif; ?>
            
            <div class="comment-meta commentmetadata">
                <a href="<?php echo esc_url(get_comment_link($comment->comment_ID)); ?>">
                    <?php printf(__('%1$s at %2$s', 'qioooo'), get_comment_date(), get_comment_time()); ?>
                </a>
                <?php edit_comment_link(__('(编辑)', 'qioooo'), '  ', ''); ?>
            </div>
            
            <div class="comment-content">
                <?php comment_text(); ?>
            </div>
            
            <div class="reply">
                <?php comment_reply_link(array_merge($args, array('depth' => $depth, 'max_depth' => $args['max_depth']))); ?>
            </div>
        </div>
    </li>
    <?php
}

/**
 * 自定义评论表单
 */
function qioooo_comment_form($args = array(), $post_id = null) {
    if (null === $post_id) {
        $post_id = get_the_ID();
    }
    
    $commenter = wp_get_current_commenter();
    $req = get_option('require_name_email');
    $aria_req = ($req ? " aria-required='true'" : '');
    $html_req = ($req ? " required='required'" : '');
    $html5 = 'html5' === current_theme_supports('html5', 'comment-form');
    
    $fields = array(
        'author' => sprintf(
            '<p class="comment-form-author">%s %s</p>',
            sprintf(
                '<label for="author">%s%s</label>',
                __('姓名', 'qioooo'),
                ($req ? ' <span class="required">*</span>' : '')
            ),
            sprintf(
                '<input id="author" name="author" type="text" value="%s" size="30" maxlength="245"%s%s />',
                esc_attr($commenter['comment_author']),
                $aria_req,
                $html_req
            )
        ),
        'email' => sprintf(
            '<p class="comment-form-email">%s %s</p>',
            sprintf(
                '<label for="email">%s%s</label>',
                __('邮箱', 'qioooo'),
                ($req ? ' <span class="required">*</span>' : '')
            ),
            sprintf(
                '<input id="email" name="email" %s value="%s" size="30" maxlength="100" aria-describedby="email-notes"%s%s />',
                ($html5 ? 'type="email"' : 'type="text"'),
                esc_attr($commenter['comment_author_email']),
                $aria_req,
                $html_req
            )
        ),
        'url' => sprintf(
            '<p class="comment-form-url">%s %s</p>',
            sprintf(
                '<label for="url">%s</label>',
                __('网站', 'qioooo')
            ),
            sprintf(
                '<input id="url" name="url" %s value="%s" size="30" maxlength="200" />',
                ($html5 ? 'type="url"' : 'type="text"'),
                esc_attr($commenter['comment_author_url'])
            )
        ),
    );
    
    $defaults = array(
        'fields' => $fields,
        'comment_field' => sprintf(
            '<p class="comment-form-comment"><label for="comment">%s</label> <textarea id="comment" name="comment" cols="45" rows="8" maxlength="65525" required="required"></textarea></p>',
            _x('评论', 'noun', 'qioooo')
        ),
        'must_log_in' => sprintf(
            '<p class="must-log-in">%s</p>',
            sprintf(
                __('您必须 <a href="%s">登录</a> 后才能发表评论。', 'qioooo'),
                wp_login_url(apply_filters('the_permalink', get_permalink($post_id)))
            )
        ),
        'logged_in_as' => sprintf(
            '<p class="logged-in-as">%s</p>',
            sprintf(
                __('登录为 <a href="%1$s">%2$s</a>。 <a href="%3$s" title="退出登录">退出？</a>', 'qioooo'),
                get_edit_user_link(),
                $user_identity,
                wp_logout_url(apply_filters('the_permalink', get_permalink($post_id)))
            )
        ),
        'comment_notes_before' => sprintf(
            '<p class="comment-notes"><span id="email-notes">%s</span>%s</p>',
            __('您的邮箱地址不会被公开。', 'qioooo'),
            ($req ? sprintf(
                '<span class="required-field-message">%s</span>',
                __('必填项已用<span class="required">*</span>标注', 'qioooo')
            ) : '')
        ),
        'comment_notes_after' => '',
        'id_form' => 'commentform',
        'id_submit' => 'submit',
        'class_form' => 'comment-form',
        'class_submit' => 'submit',
        'name_submit' => 'submit',
        'title_reply' => __('发表评论', 'qioooo'),
        'title_reply_to' => __('回复 %s', 'qioooo'),
        'title_reply_before' => '<h3 id="reply-title" class="comment-reply-title">',
        'title_reply_after' => '</h3>',
        'cancel_reply_before' => ' <small>',
        'cancel_reply_after' => '</small>',
        'cancel_reply_link' => __('取消回复', 'qioooo'),
        'label_submit' => __('发表评论', 'qioooo'),
        'submit_button' => '<input name="%1$s" type="submit" id="%2$s" class="%3$s" value="%4$s" />',
        'submit_field' => '<p class="form-submit">%1$s %2$s</p>',
        'format' => 'xhtml',
    );
    
    $args = wp_parse_args($args, apply_filters('comment_form_defaults', $defaults));
    
    // 确保评论表单支持HTML5
    if ($html5) {
        $args['class_form'] = 'comment-form';
        $args['class_submit'] = 'submit';
    }
    
    comment_form($args, $post_id);
}

/**
 * 处理评论提交
 */
function qioooo_handle_comment_submit() {
    // 检查nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'qioooo_nonce')) {
        wp_send_json_error(__('无效的请求', 'qioooo'));
    }
    
    // 检查用户权限
    if (!is_user_logged_in()) {
        wp_send_json_error(__('请先登录', 'qioooo'));
    }
    
    // 验证输入
    $post_id = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;
    $comment = isset($_POST['comment']) ? sanitize_textarea_field($_POST['comment']) : '';
    
    if (empty($post_id) || empty($comment)) {
        wp_send_json_error(__('评论内容不能为空', 'qioooo'));
    }
    
    // 检查评论频率限制
    if (qioooo_check_comment_flood()) {
        wp_send_json_error(__('评论提交过于频繁，请稍后再试', 'qioooo'));
    }
    
    $comment_data = array(
        'comment_post_ID' => $post_id,
        'comment_content' => $comment,
        'user_id' => get_current_user_id(),
        'comment_approved' => 1,
        'comment_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field($_SERVER['HTTP_USER_AGENT']) : '',
        'comment_author_IP' => sanitize_text_field($_SERVER['REMOTE_ADDR']),
    );
    
    $comment_id = wp_insert_comment($comment_data);
    
    if (is_wp_error($comment_id)) {
        wp_send_json_error($comment_id->get_error_message());
    }
    
    // 更新评论计数
    wp_update_comment_count($post_id);
    
    wp_send_json_success(array(
        'message' => __('评论已提交', 'qioooo'),
        'comment_id' => $comment_id
    ));
}
add_action('wp_ajax_qioooo_submit_comment', 'qioooo_handle_comment_submit');
add_action('wp_ajax_nopriv_qioooo_submit_comment', 'qioooo_handle_comment_submit');

/**
 * 检查评论洪水攻击
 */
function qioooo_check_comment_flood() {
    $flood_die = apply_filters('comment_flood_filter', false, time(), time());
    if ($flood_die) {
        return true;
    }
    return false;
} 