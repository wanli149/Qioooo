<?php
/**
 * Template Name: 个人中心
 *
 * @package QIoooo
 */

get_header();

// 检查用户是否已登录
if (!is_user_logged_in()) {
    wp_redirect(wp_login_url(get_permalink()));
    exit;
}

// 获取当前用户信息
$current_user = wp_get_current_user();
?>

<div class="profile-container">
    <div class="profile-sidebar">
        <div class="profile-avatar">
            <?php echo get_avatar($current_user->ID, 120); ?>
            <h2><?php echo esc_html($current_user->display_name); ?></h2>
            <p class="user-role"><?php echo esc_html(ucfirst($current_user->roles[0])); ?></p>
        </div>
        
        <nav class="profile-nav">
            <ul>
                <li class="active"><a href="#profile-info">个人信息</a></li>
                <li><a href="#my-resources">我的资源</a></li>
                <li><a href="#download-history">下载记录</a></li>
                <li><a href="#favorites">我的收藏</a></li>
                <li><a href="#account-settings">账号设置</a></li>
            </ul>
        </nav>
    </div>
    
    <div class="profile-content">
        <div id="profile-info" class="profile-section active">
            <h3>个人信息</h3>
            <form class="profile-form" method="post">
                <?php wp_nonce_field('update_profile', 'profile_nonce'); ?>
                
                <div class="form-group">
                    <label for="display_name">显示名称</label>
                    <input type="text" id="display_name" name="display_name" value="<?php echo esc_attr($current_user->display_name); ?>">
                </div>
                
                <div class="form-group">
                    <label for="user_email">电子邮箱</label>
                    <input type="email" id="user_email" name="user_email" value="<?php echo esc_attr($current_user->user_email); ?>">
                </div>
                
                <div class="form-group">
                    <label for="user_url">个人网站</label>
                    <input type="url" id="user_url" name="user_url" value="<?php echo esc_attr($current_user->user_url); ?>">
                </div>
                
                <div class="form-group">
                    <label for="description">个人简介</label>
                    <textarea id="description" name="description"><?php echo esc_textarea($current_user->description); ?></textarea>
                </div>
                
                <div class="form-submit">
                    <button type="submit" class="button">保存更改</button>
                </div>
            </form>
        </div>
        
        <div id="my-resources" class="profile-section">
            <h3>我的资源</h3>
            <?php
            $args = array(
                'post_type' => 'resource',
                'author' => $current_user->ID,
                'posts_per_page' => 10,
                'paged' => get_query_var('paged') ? get_query_var('paged') : 1
            );
            $resources = new WP_Query($args);
            
            if ($resources->have_posts()) :
                while ($resources->have_posts()) : $resources->the_post();
                    get_template_part('template-parts/content', 'resource');
                endwhile;
                
                echo '<div class="pagination">';
                echo paginate_links(array(
                    'total' => $resources->max_num_pages,
                    'current' => max(1, get_query_var('paged')),
                    'prev_text' => '&laquo; 上一页',
                    'next_text' => '下一页 &raquo;'
                ));
                echo '</div>';
            else :
                echo '<p>您还没有发布任何资源。</p>';
            endif;
            wp_reset_postdata();
            ?>
        </div>
        
        <div id="download-history" class="profile-section">
            <h3>下载记录</h3>
            <?php
            $downloads = get_user_meta($current_user->ID, 'resource_downloads', true);
            if (!empty($downloads)) :
                echo '<ul class="download-list">';
                foreach ($downloads as $download) :
                    $resource = get_post($download['resource_id']);
                    if ($resource) :
                        ?>
                        <li>
                            <a href="<?php echo get_permalink($resource->ID); ?>"><?php echo esc_html($resource->post_title); ?></a>
                            <span class="download-date"><?php echo date('Y-m-d H:i', $download['time']); ?></span>
                        </li>
                        <?php
                    endif;
                endforeach;
                echo '</ul>';
            else :
                echo '<p>您还没有下载记录。</p>';
            endif;
            ?>
        </div>
        
        <div id="favorites" class="profile-section">
            <h3>我的收藏</h3>
            <?php
            $favorites = get_user_meta($current_user->ID, 'resource_favorites', true);
            if (!empty($favorites)) :
                $args = array(
                    'post_type' => 'resource',
                    'post__in' => $favorites,
                    'posts_per_page' => 10,
                    'paged' => get_query_var('paged') ? get_query_var('paged') : 1
                );
                $favorite_resources = new WP_Query($args);
                
                if ($favorite_resources->have_posts()) :
                    while ($favorite_resources->have_posts()) : $favorite_resources->the_post();
                        get_template_part('template-parts/content', 'resource');
                    endwhile;
                    
                    echo '<div class="pagination">';
                    echo paginate_links(array(
                        'total' => $favorite_resources->max_num_pages,
                        'current' => max(1, get_query_var('paged')),
                        'prev_text' => '&laquo; 上一页',
                        'next_text' => '下一页 &raquo;'
                    ));
                    echo '</div>';
                endif;
                wp_reset_postdata();
            else :
                echo '<p>您还没有收藏任何资源。</p>';
            endif;
            ?>
        </div>
        
        <div id="account-settings" class="profile-section">
            <h3>账号设置</h3>
            <form class="account-form" method="post">
                <?php wp_nonce_field('update_account', 'account_nonce'); ?>
                
                <div class="form-group">
                    <label for="current_password">当前密码</label>
                    <input type="password" id="current_password" name="current_password">
                </div>
                
                <div class="form-group">
                    <label for="new_password">新密码</label>
                    <input type="password" id="new_password" name="new_password">
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">确认新密码</label>
                    <input type="password" id="confirm_password" name="confirm_password">
                </div>
                
                <div class="form-submit">
                    <button type="submit" class="button">更新密码</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php get_footer(); ?> 