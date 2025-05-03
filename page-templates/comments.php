<?php
/**
 * 评论页面模板
 *
 * @package QIoooo
 */

get_header();
?>

<div class="comments-page">
    <div class="container">
        <div class="comments-header">
            <h1 class="page-title"><?php _e('评论管理', 'qioooo'); ?></h1>
            
            <div class="comments-filters">
                <form class="filter-form" method="get">
                    <select name="comment_type">
                        <option value=""><?php _e('全部评论', 'qioooo'); ?></option>
                        <option value="post" <?php selected(get_query_var('comment_type'), 'post'); ?>><?php _e('文章评论', 'qioooo'); ?></option>
                        <option value="novel" <?php selected(get_query_var('comment_type'), 'novel'); ?>><?php _e('小说评论', 'qioooo'); ?></option>
                        <option value="resource" <?php selected(get_query_var('comment_type'), 'resource'); ?>><?php _e('资源评论', 'qioooo'); ?></option>
                    </select>
                    
                    <select name="comment_status">
                        <option value=""><?php _e('全部状态', 'qioooo'); ?></option>
                        <option value="approved" <?php selected(get_query_var('comment_status'), 'approved'); ?>><?php _e('已审核', 'qioooo'); ?></option>
                        <option value="pending" <?php selected(get_query_var('comment_status'), 'pending'); ?>><?php _e('待审核', 'qioooo'); ?></option>
                        <option value="spam" <?php selected(get_query_var('comment_status'), 'spam'); ?>><?php _e('垃圾评论', 'qioooo'); ?></option>
                    </select>
                    
                    <button type="submit"><?php _e('筛选', 'qioooo'); ?></button>
                </form>
            </div>
        </div>
        
        <div class="comments-content">
            <?php
            $comments = get_comments(array(
                'user_id' => get_current_user_id(),
                'status' => get_query_var('comment_status'),
                'post_type' => get_query_var('comment_type'),
                'number' => 20,
                'paged' => get_query_var('paged') ? get_query_var('paged') : 1
            ));
            
            if ($comments) :
                echo '<div class="comments-list">';
                foreach ($comments as $comment) :
                    $post = get_post($comment->comment_post_ID);
                    ?>
                    <div class="comment-item">
                        <div class="comment-header">
                            <div class="comment-meta">
                                <span class="comment-date"><?php echo date_i18n(get_option('date_format'), strtotime($comment->comment_date)); ?></span>
                                <span class="comment-status status-<?php echo esc_attr($comment->comment_approved); ?>">
                                    <?php
                                    switch ($comment->comment_approved) {
                                        case '1':
                                            _e('已审核', 'qioooo');
                                            break;
                                        case '0':
                                            _e('待审核', 'qioooo');
                                            break;
                                        case 'spam':
                                            _e('垃圾评论', 'qioooo');
                                            break;
                                    }
                                    ?>
                                </span>
                            </div>
                            
                            <div class="comment-post">
                                <?php _e('评论于：', 'qioooo'); ?>
                                <a href="<?php echo get_permalink($post->ID); ?>"><?php echo get_the_title($post); ?></a>
                            </div>
                        </div>
                        
                        <div class="comment-content">
                            <?php echo wp_kses_post($comment->comment_content); ?>
                        </div>
                        
                        <div class="comment-actions">
                            <?php if ($comment->comment_approved === '0') : ?>
                                <a href="<?php echo wp_nonce_url(admin_url("comment.php?action=approve&c=$comment->comment_ID"), 'approve-comment_' . $comment->comment_ID); ?>" class="button button-primary">
                                    <?php _e('通过审核', 'qioooo'); ?>
                                </a>
                            <?php endif; ?>
                            
                            <a href="<?php echo get_edit_comment_link($comment->comment_ID); ?>" class="button">
                                <?php _e('编辑', 'qioooo'); ?>
                                </a>
                            
                            <a href="<?php echo wp_nonce_url(admin_url("comment.php?action=trash&c=$comment->comment_ID"), 'delete-comment_' . $comment->comment_ID); ?>" class="button button-danger">
                                <?php _e('删除', 'qioooo'); ?>
                            </a>
                        </div>
                    </div>
                    <?php
                endforeach;
                echo '</div>';
                
                echo '<div class="pagination">';
                echo paginate_links(array(
                    'total' => ceil(count($comments) / 20),
                    'current' => max(1, get_query_var('paged')),
                    'prev_text' => __('&laquo; 上一页', 'qioooo'),
                    'next_text' => __('下一页 &raquo;', 'qioooo')
                ));
                echo '</div>';
            else :
                ?>
                <div class="no-comments">
                    <p><?php _e('暂无评论。', 'qioooo'); ?></p>
                </div>
                <?php
            endif;
            ?>
        </div>
        
        <div class="comments-sidebar">
            <div class="comments-stats">
                <h3><?php _e('评论统计', 'qioooo'); ?></h3>
                <ul>
                    <li>
                        <span class="stat-label"><?php _e('总评论数', 'qioooo'); ?></span>
                        <span class="stat-number"><?php echo get_comments(array('user_id' => get_current_user_id(), 'count' => true)); ?></span>
                    </li>
                    <li>
                        <span class="stat-label"><?php _e('已审核', 'qioooo'); ?></span>
                        <span class="stat-number"><?php echo get_comments(array('user_id' => get_current_user_id(), 'status' => 'approve', 'count' => true)); ?></span>
                    </li>
                    <li>
                        <span class="stat-label"><?php _e('待审核', 'qioooo'); ?></span>
                        <span class="stat-number"><?php echo get_comments(array('user_id' => get_current_user_id(), 'status' => 'hold', 'count' => true)); ?></span>
                    </li>
                    <li>
                        <span class="stat-label"><?php _e('垃圾评论', 'qioooo'); ?></span>
                        <span class="stat-number"><?php echo get_comments(array('user_id' => get_current_user_id(), 'status' => 'spam', 'count' => true)); ?></span>
                    </li>
                </ul>
            </div>
            
            <div class="recent-comments">
                <h3><?php _e('最近评论', 'qioooo'); ?></h3>
                <ul>
                    <?php
                    $recent_comments = get_comments(array(
                        'user_id' => get_current_user_id(),
                        'number' => 5,
                        'status' => 'approve'
                    ));
                    
                    if ($recent_comments) :
                        foreach ($recent_comments as $comment) :
                            ?>
                            <li>
                                <a href="<?php echo get_comment_link($comment); ?>">
                                    <?php echo wp_trim_words($comment->comment_content, 20); ?>
                                </a>
                                <span class="comment-date"><?php echo date_i18n(get_option('date_format'), strtotime($comment->comment_date)); ?></span>
                            </li>
                            <?php
                        endforeach;
                    else :
                        echo '<li>' . __('暂无最近评论', 'qioooo') . '</li>';
                    endif;
                    ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php
get_footer();