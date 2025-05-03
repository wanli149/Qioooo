<?php
/**
 * 社交媒体集成相关函数
 *
 * @package QIoooo
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 添加社交媒体分享按钮
 */
function qioooo_add_social_share_buttons() {
    if (!is_singular()) {
        return;
    }
    
    $url = urlencode(get_permalink());
    $title = urlencode(get_the_title());
    $excerpt = urlencode(get_the_excerpt());
    
    echo '<div class="social-share-buttons">';
    echo '<h3>分享到：</h3>';
    echo '<ul>';
    
    // 微信分享
    echo '<li class="wechat-share">';
    echo '<a href="#" class="wechat-share-btn" data-title="' . esc_attr($title) . '" data-url="' . esc_attr($url) . '">';
    echo '<i class="icon-wechat"></i>';
    echo '<span>微信</span>';
    echo '</a>';
    echo '</li>';
    
    // 微博分享
    echo '<li class="weibo-share">';
    echo '<a href="https://service.weibo.com/share/share.php?url=' . $url . '&title=' . $title . '" target="_blank">';
    echo '<i class="icon-weibo"></i>';
    echo '<span>微博</span>';
    echo '</a>';
    echo '</li>';
    
    // QQ空间分享
    echo '<li class="qzone-share">';
    echo '<a href="https://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url=' . $url . '&title=' . $title . '&desc=' . $excerpt . '" target="_blank">';
    echo '<i class="icon-qzone"></i>';
    echo '<span>QQ空间</span>';
    echo '</a>';
    echo '</li>';
    
    // 豆瓣分享
    echo '<li class="douban-share">';
    echo '<a href="https://www.douban.com/share/service?url=' . $url . '&title=' . $title . '" target="_blank">';
    echo '<i class="icon-douban"></i>';
    echo '<span>豆瓣</span>';
    echo '</a>';
    echo '</li>';
    
    echo '</ul>';
    echo '</div>';
}
add_action('qioooo_after_content', 'qioooo_add_social_share_buttons');

/**
 * 添加社交媒体关注按钮
 */
function qioooo_add_social_follow_buttons() {
    $social_links = array(
        'wechat' => get_theme_mod('social_wechat'),
        'weibo' => get_theme_mod('social_weibo'),
        'qq' => get_theme_mod('social_qq'),
        'douban' => get_theme_mod('social_douban')
    );
    
    echo '<div class="social-follow-buttons">';
    echo '<h3>关注我们：</h3>';
    echo '<ul>';
    
    foreach ($social_links as $platform => $url) {
        if (!empty($url)) {
            echo '<li class="' . esc_attr($platform) . '-follow">';
            echo '<a href="' . esc_url($url) . '" target="_blank">';
            echo '<i class="icon-' . esc_attr($platform) . '"></i>';
            echo '<span>' . esc_html(ucfirst($platform)) . '</span>';
            echo '</a>';
            echo '</li>';
        }
    }
    
    echo '</ul>';
    echo '</div>';
}
add_action('qioooo_footer', 'qioooo_add_social_follow_buttons');

/**
 * 添加微信二维码弹窗
 */
function qioooo_add_wechat_qrcode_modal() {
    echo '<div class="wechat-qrcode-modal" style="display: none;">';
    echo '<div class="modal-content">';
    echo '<span class="close-modal">&times;</span>';
    echo '<h3>扫描二维码关注我们</h3>';
    echo '<img src="' . esc_url(get_theme_mod('wechat_qrcode')) . '" alt="微信二维码">';
    echo '</div>';
    echo '</div>';
}
add_action('wp_footer', 'qioooo_add_wechat_qrcode_modal');

/**
 * 添加社交媒体自定义设置
 */
function qioooo_add_social_customizer_settings($wp_customize) {
    // 添加社交媒体链接设置
    $wp_customize->add_section('social_links', array(
        'title' => '社交媒体链接',
        'priority' => 30
    ));
    
    // 微信二维码
    $wp_customize->add_setting('wechat_qrcode', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw'
    ));
    
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'wechat_qrcode', array(
        'label' => '微信二维码',
        'section' => 'social_links',
        'settings' => 'wechat_qrcode'
    )));
    
    // 微博链接
    $wp_customize->add_setting('social_weibo', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw'
    ));
    
    $wp_customize->add_control('social_weibo', array(
        'label' => '微博链接',
        'section' => 'social_links',
        'type' => 'url'
    ));
    
    // QQ链接
    $wp_customize->add_setting('social_qq', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw'
    ));
    
    $wp_customize->add_control('social_qq', array(
        'label' => 'QQ链接',
        'section' => 'social_links',
        'type' => 'url'
    ));
    
    // 豆瓣链接
    $wp_customize->add_setting('social_douban', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw'
    ));
    
    $wp_customize->add_control('social_douban', array(
        'label' => '豆瓣链接',
        'section' => 'social_links',
        'type' => 'url'
    ));
}
add_action('customize_register', 'qioooo_add_social_customizer_settings'); 