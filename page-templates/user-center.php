<?php
/**
 * 用户中心页面模板
 *
 * @package QIoooo
 */

get_header();
?>

<div class="user-center">
    <div class="container">
        <div class="user-center__sidebar">
            <div class="user-profile">
                <?php echo get_avatar(get_current_user_id(), 100); ?>
                <h2><?php echo wp_get_current_user()->display_name; ?></h2>
                <p class="user-role"><?php echo ucfirst(wp_get_current_user()->roles[0]); ?></p>
            </div>
            
            <nav class="user-menu">
                <ul>
                    <li class="active"><a href="#dashboard"><?php _e('控制面板', 'qioooo'); ?></a></li>
                    <li><a href="#profile"><?php _e('个人资料', 'qioooo'); ?></a></li>
                    <li><a href="#novels"><?php _e('我的小说', 'qioooo'); ?></a></li>
                    <li><a href="#resources"><?php _e('我的资源', 'qioooo'); ?></a></li>
                    <li><a href="#favorites"><?php _e('我的收藏', 'qioooo'); ?></a></li>
                    <li><a href="#comments"><?php _e('我的评论', 'qioooo'); ?></a></li>
                    <li><a href="#messages"><?php _e('我的消息', 'qioooo'); ?></a></li>
                    <li><a href="#settings"><?php _e('账户设置', 'qioooo'); ?></a></li>
                </ul>
            </nav>
        </div>
        
        <div class="user-center__content">
            <div id="dashboard" class="user-section active">
                <h2><?php _e('控制面板', 'qioooo'); ?></h2>
                <div class="dashboard-stats">
                    <div class="stat-item">
                        <h3><?php _e('我的小说', 'qioooo'); ?></h3>
                        <p class="stat-number"><?php echo count_user_posts(get_current_user_id(), 'novel'); ?></p>
                    </div>
                    <div class="stat-item">
                        <h3><?php _e('我的资源', 'qioooo'); ?></h3>
                        <p class="stat-number"><?php echo count_user_posts(get_current_user_id(), 'resource'); ?></p>
                    </div>
                    <div class="stat-item">
                        <h3><?php _e('我的收藏', 'qioooo'); ?></h3>
                        <p class="stat-number">0</p>
                    </div>
                    <div class="stat-item">
                        <h3><?php _e('我的评论', 'qioooo'); ?></h3>
                        <p class="stat-number"><?php echo get_comments(array('user_id' => get_current_user_id(), 'count' => true)); ?></p>
                    </div>
                </div>
                
                <div class="recent-activities">
                    <h3><?php _e('最近活动', 'qioooo'); ?></h3>
                    <ul>
                        <?php
                        $activities = array();
                        
                        // 获取最近发布的小说
                        $recent_novels = get_posts(array(
                            'post_type' => 'novel',
                            'author' => get_current_user_id(),
                            'posts_per_page' => 5
                        ));
                        
                        foreach ($recent_novels as $novel) {
                            $activities[] = array(
                                'type' => 'novel',
                                'title' => $novel->post_title,
                                'date' => $novel->post_date,
                                'link' => get_permalink($novel->ID)
                            );
                        }
                        
                        // 获取最近发布的资源
                        $recent_resources = get_posts(array(
                            'post_type' => 'resource',
                            'author' => get_current_user_id(),
                            'posts_per_page' => 5
                        ));
                        
                        foreach ($recent_resources as $resource) {
                            $activities[] = array(
                                'type' => 'resource',
                                'title' => $resource->post_title,
                                'date' => $resource->post_date,
                                'link' => get_permalink($resource->ID)
                            );
                        }
                        
                        // 获取最近评论
                        $recent_comments = get_comments(array(
                            'user_id' => get_current_user_id(),
                            'number' => 5
                        ));
                        
                        foreach ($recent_comments as $comment) {
                            $activities[] = array(
                                'type' => 'comment',
                                'title' => get_comment_excerpt($comment->comment_ID),
                                'date' => $comment->comment_date,
                                'link' => get_comment_link($comment)
                            );
                        }
                        
                        // 按日期排序
                        usort($activities, function($a, $b) {
                            return strtotime($b['date']) - strtotime($a['date']);
                        });
                        
                        // 显示最近10个活动
                        $activities = array_slice($activities, 0, 10);
                        
                        foreach ($activities as $activity) {
                            echo '<li class="activity-item activity-' . esc_attr($activity['type']) . '">';
                            echo '<span class="activity-date">' . date_i18n(get_option('date_format'), strtotime($activity['date'])) . '</span>';
                            echo '<a href="' . esc_url($activity['link']) . '">' . esc_html($activity['title']) . '</a>';
                            echo '</li>';
                        }
                        ?>
                    </ul>
                </div>
            </div>
            
            <div id="profile" class="user-section">
                <h2><?php _e('个人资料', 'qioooo'); ?></h2>
                <?php echo do_shortcode('[qioooo_profile_form]'); ?>
            </div>
            
            <div id="novels" class="user-section">
                <h2><?php _e('我的小说', 'qioooo'); ?></h2>
                <?php
                $novels = new WP_Query(array(
                    'post_type' => 'novel',
                    'author' => get_current_user_id(),
                    'posts_per_page' => 10,
                    'paged' => get_query_var('paged') ? get_query_var('paged') : 1
                ));
                
                if ($novels->have_posts()) :
                    echo '<div class="novel-list">';
                    while ($novels->have_posts()) : $novels->the_post();
                        get_template_part('template-parts/content', 'novel');
                    endwhile;
                    echo '</div>';
                    
                    echo '<div class="pagination">';
                    echo paginate_links(array(
                        'total' => $novels->max_num_pages,
                        'current' => max(1, get_query_var('paged')),
                        'prev_text' => __('&laquo; 上一页', 'qioooo'),
                        'next_text' => __('下一页 &raquo;', 'qioooo')
                    ));
                    echo '</div>';
                else :
                    echo '<p class="no-posts">' . __('您还没有发布任何小说。', 'qioooo') . '</p>';
                endif;
                
                wp_reset_postdata();
                ?>
            </div>
            
            <div id="resources" class="user-section">
                <h2><?php _e('我的资源', 'qioooo'); ?></h2>
                <?php
                $resources = new WP_Query(array(
                    'post_type' => 'resource',
                    'author' => get_current_user_id(),
                    'posts_per_page' => 10,
                    'paged' => get_query_var('paged') ? get_query_var('paged') : 1
                ));
                
                if ($resources->have_posts()) :
                    echo '<div class="resource-list">';
                    while ($resources->have_posts()) : $resources->the_post();
                        get_template_part('template-parts/content', 'resource');
                    endwhile;
                    echo '</div>';
                    
                    echo '<div class="pagination">';
                    echo paginate_links(array(
                        'total' => $resources->max_num_pages,
                        'current' => max(1, get_query_var('paged')),
                        'prev_text' => __('&laquo; 上一页', 'qioooo'),
                        'next_text' => __('下一页 &raquo;', 'qioooo')
                    ));
                    echo '</div>';
                else :
                    echo '<p class="no-posts">' . __('您还没有发布任何资源。', 'qioooo') . '</p>';
                endif;
                
                wp_reset_postdata();
                ?>
            </div>
            
            <div id="favorites" class="user-section">
                <h2><?php _e('我的收藏', 'qioooo'); ?></h2>
                <p class="no-favorites"><?php _e('您还没有收藏任何内容。', 'qioooo'); ?></p>
            </div>
            
            <div id="comments" class="user-section">
                <h2><?php _e('我的评论', 'qioooo'); ?></h2>
                <?php
                $comments = get_comments(array(
                    'user_id' => get_current_user_id(),
                    'number' => 10,
                    'paged' => get_query_var('paged') ? get_query_var('paged') : 1
                ));
                
                if ($comments) :
                    echo '<div class="comment-list">';
                    foreach ($comments as $comment) :
                        ?>
                        <div class="comment-item">
                            <div class="comment-meta">
                                <span class="comment-date"><?php echo date_i18n(get_option('date_format'), strtotime($comment->comment_date)); ?></span>
                                <span class="comment-post">
                                    <?php _e('评论于：', 'qioooo'); ?>
                                    <a href="<?php echo get_comment_link($comment); ?>"><?php echo get_the_title($comment->comment_post_ID); ?></a>
                                </span>
                            </div>
                            <div class="comment-content">
                                <?php echo wp_kses_post($comment->comment_content); ?>
                            </div>
                        </div>
                        <?php
                    endforeach;
                    echo '</div>';
                else :
                    echo '<p class="no-comments">' . __('您还没有发表任何评论。', 'qioooo') . '</p>';
                endif;
                ?>
            </div>
            
            <div id="messages" class="user-section">
                <h2><?php _e('我的消息', 'qioooo'); ?></h2>
                <p class="no-messages"><?php _e('您还没有收到任何消息。', 'qioooo'); ?></p>
            </div>
            
            <div id="settings" class="user-section">
                <h2><?php _e('账户设置', 'qioooo'); ?></h2>
                <?php echo do_shortcode('[qioooo_settings_form]'); ?>
            </div>
        </div>
    </div>
</div>

<?php
get_footer(); 