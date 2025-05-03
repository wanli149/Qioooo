<?php
/**
 * 登录页面模板
 */

get_header(); ?>

<div class="site-container">
    <main class="site-main">
        <div class="content-area">
            <div class="login-container">
                <?php if (!is_user_logged_in()) : ?>
                    <div class="login-form">
                        <h2><?php esc_html_e('登录', 'qioooo'); ?></h2>
                        
                        <?php
                        // 显示登录表单
                        wp_login_form(array(
                            'redirect'       => home_url(),
                            'label_username' => __('用户名或邮箱', 'qioooo'),
                            'label_password' => __('密码', 'qioooo'),
                            'label_remember' => __('记住我', 'qioooo'),
                            'label_log_in'   => __('登录', 'qioooo'),
                            'id_username'    => 'user_login',
                            'id_password'    => 'user_pass',
                            'id_remember'    => 'rememberme',
                            'id_submit'      => 'wp-submit',
                            'remember'       => true,
                            'value_remember' => true,
                        ));
                        ?>

                        <div class="login-links">
                            <a href="<?php echo esc_url(wp_lostpassword_url()); ?>"><?php esc_html_e('忘记密码？', 'qioooo'); ?></a>
                            <?php if (get_option('users_can_register')) : ?>
                                <span class="separator">|</span>
                                <a href="<?php echo esc_url(wp_registration_url()); ?>"><?php esc_html_e('注册新账号', 'qioooo'); ?></a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else : ?>
                    <div class="already-logged-in">
                        <p><?php esc_html_e('您已经登录。', 'qioooo'); ?></p>
                        <p>
                            <a href="<?php echo esc_url(home_url()); ?>" class="button"><?php esc_html_e('返回首页', 'qioooo'); ?></a>
                            <a href="<?php echo esc_url(wp_logout_url(home_url())); ?>" class="button"><?php esc_html_e('退出登录', 'qioooo'); ?></a>
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<?php get_footer(); ?> 