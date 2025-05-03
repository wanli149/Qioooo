<?php
/**
 * 个人中心功能
 *
 * @package QIoooo
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 处理个人信息更新
 */
function qioooo_update_profile() {
    // 检查nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'update_profile')) {
        wp_send_json_error(__('无效的请求', 'qioooo'));
    }
    
    // 检查用户权限
    if (!is_user_logged_in()) {
        wp_send_json_error(__('请先登录', 'qioooo'));
    }
    
    $user_id = get_current_user_id();
    $user_data = array(
        'ID' => $user_id,
        'display_name' => sanitize_text_field($_POST['display_name']),
        'user_email' => sanitize_email($_POST['user_email']),
        'user_url' => esc_url_raw($_POST['user_url']),
        'description' => sanitize_textarea_field($_POST['description'])
    );
    
    // 更新用户信息
    $result = wp_update_user($user_data);
    
    if (is_wp_error($result)) {
        wp_send_json_error($result->get_error_message());
    }
    
    wp_send_json_success(array(
        'message' => __('个人信息已更新', 'qioooo')
    ));
}
add_action('wp_ajax_qioooo_update_profile', 'qioooo_update_profile');

/**
 * 处理密码更新
 */
function qioooo_update_password() {
    // 检查nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'update_account')) {
        wp_send_json_error(__('无效的请求', 'qioooo'));
    }
    
    // 检查用户权限
    if (!is_user_logged_in()) {
        wp_send_json_error(__('请先登录', 'qioooo'));
    }
    
    $user_id = get_current_user_id();
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    
    // 验证当前密码
    $user = get_user_by('id', $user_id);
    if (!wp_check_password($current_password, $user->user_pass, $user_id)) {
        wp_send_json_error(__('当前密码错误', 'qioooo'));
    }
    
    // 更新密码
    wp_set_password($new_password, $user_id);
    
    // 重新登录用户
    wp_set_current_user($user_id);
    wp_set_auth_cookie($user_id);
    
    wp_send_json_success(array(
        'message' => __('密码已更新', 'qioooo')
    ));
}
add_action('wp_ajax_qioooo_update_password', 'qioooo_update_password');

/**
 * 记录资源下载
 */
function qioooo_record_download($resource_id) {
    if (!is_user_logged_in()) {
        return;
    }
    
    $user_id = get_current_user_id();
    $downloads = get_user_meta($user_id, 'resource_downloads', true);
    
    if (!is_array($downloads)) {
        $downloads = array();
    }
    
    // 添加下载记录
    $downloads[] = array(
        'resource_id' => $resource_id,
        'time' => current_time('timestamp')
    );
    
    // 只保留最近的100条记录
    if (count($downloads) > 100) {
        $downloads = array_slice($downloads, -100);
    }
    
    update_user_meta($user_id, 'resource_downloads', $downloads);
}

/**
 * 添加资源收藏
 */
function qioooo_add_favorite() {
    // 检查nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'qioooo_nonce')) {
        wp_send_json_error(__('无效的请求', 'qioooo'));
    }
    
    // 检查用户权限
    if (!is_user_logged_in()) {
        wp_send_json_error(__('请先登录', 'qioooo'));
    }
    
    $user_id = get_current_user_id();
    $resource_id = intval($_POST['resource_id']);
    
    // 获取用户收藏
    $favorites = get_user_meta($user_id, 'resource_favorites', true);
    if (!is_array($favorites)) {
        $favorites = array();
    }
    
    // 检查是否已收藏
    if (in_array($resource_id, $favorites)) {
        wp_send_json_error(__('该资源已在收藏列表中', 'qioooo'));
    }
    
    // 添加收藏
    $favorites[] = $resource_id;
    update_user_meta($user_id, 'resource_favorites', $favorites);
    
    wp_send_json_success(array(
        'message' => __('收藏成功', 'qioooo')
    ));
}
add_action('wp_ajax_qioooo_add_favorite', 'qioooo_add_favorite');

/**
 * 移除资源收藏
 */
function qioooo_remove_favorite() {
    // 检查nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'qioooo_nonce')) {
        wp_send_json_error(__('无效的请求', 'qioooo'));
    }
    
    // 检查用户权限
    if (!is_user_logged_in()) {
        wp_send_json_error(__('请先登录', 'qioooo'));
    }
    
    $user_id = get_current_user_id();
    $resource_id = intval($_POST['resource_id']);
    
    // 获取用户收藏
    $favorites = get_user_meta($user_id, 'resource_favorites', true);
    if (!is_array($favorites)) {
        $favorites = array();
    }
    
    // 移除收藏
    $favorites = array_diff($favorites, array($resource_id));
    update_user_meta($user_id, 'resource_favorites', $favorites);
    
    wp_send_json_success(array(
        'message' => __('已取消收藏', 'qioooo')
    ));
}
add_action('wp_ajax_qioooo_remove_favorite', 'qioooo_remove_favorite'); 