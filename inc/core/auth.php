<?php
/**
 * 用户认证和社交登录功能
 *
 * @package QIoooo
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 添加社交登录按钮
 */
function qioooo_social_login_buttons() {
    ?>
    <div class="social-login">
        <h3><?php _e('社交账号登录', 'qioooo'); ?></h3>
        <div class="social-buttons">
            <button type="button" class="social-button wechat" onclick="qioooo_wechat_login()">
                <i class="icon-wechat"></i>
                <?php _e('微信登录', 'qioooo'); ?>
            </button>
            <button type="button" class="social-button qq" onclick="qioooo_qq_login()">
                <i class="icon-qq"></i>
                <?php _e('QQ登录', 'qioooo'); ?>
            </button>
        </div>
    </div>
    <?php
}
add_action('login_form', 'qioooo_social_login_buttons');
add_action('register_form', 'qioooo_social_login_buttons');

/**
 * 处理微信登录
 */
function qioooo_handle_wechat_login() {
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'qioooo_wechat_login')) {
        wp_send_json_error(__('无效的请求', 'qioooo'));
    }

    // 获取微信用户信息
    $user_info = qioooo_get_wechat_user_info($_POST['code']);
    if (is_wp_error($user_info)) {
        wp_send_json_error($user_info->get_error_message());
    }

    // 查找或创建用户
    $user = qioooo_get_user_by_social_id('wechat', $user_info['openid']);
    if (!$user) {
        $user = qioooo_create_user_from_social('wechat', $user_info);
    }

    if (is_wp_error($user)) {
        wp_send_json_error($user->get_error_message());
    }

    // 登录用户
    wp_set_current_user($user->ID);
    wp_set_auth_cookie($user->ID);
    
    wp_send_json_success(array(
        'redirect' => home_url()
    ));
}
add_action('wp_ajax_qioooo_wechat_login', 'qioooo_handle_wechat_login');
add_action('wp_ajax_nopriv_qioooo_wechat_login', 'qioooo_handle_wechat_login');

/**
 * 处理QQ登录
 */
function qioooo_handle_qq_login() {
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'qioooo_qq_login')) {
        wp_send_json_error(__('无效的请求', 'qioooo'));
    }

    // 获取QQ用户信息
    $user_info = qioooo_get_qq_user_info($_POST['code']);
    if (is_wp_error($user_info)) {
        wp_send_json_error($user_info->get_error_message());
    }

    // 查找或创建用户
    $user = qioooo_get_user_by_social_id('qq', $user_info['openid']);
    if (!$user) {
        $user = qioooo_create_user_from_social('qq', $user_info);
    }

    if (is_wp_error($user)) {
        wp_send_json_error($user->get_error_message());
    }

    // 登录用户
    wp_set_current_user($user->ID);
    wp_set_auth_cookie($user->ID);
    
    wp_send_json_success(array(
        'redirect' => home_url()
    ));
}
add_action('wp_ajax_qioooo_qq_login', 'qioooo_handle_qq_login');
add_action('wp_ajax_nopriv_qioooo_qq_login', 'qioooo_handle_qq_login');

/**
 * 获取微信用户信息
 */
function qioooo_get_wechat_user_info($code) {
    // 获取access_token
    $token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token';
    $token_params = array(
        'appid' => QIOOOO_WECHAT_APPID,
        'secret' => QIOOOO_WECHAT_SECRET,
        'code' => $code,
        'grant_type' => 'authorization_code'
    );
    
    $response = wp_remote_get(add_query_arg($token_params, $token_url));
    if (is_wp_error($response)) {
        return $response;
    }
    
    $body = json_decode(wp_remote_retrieve_body($response), true);
    if (isset($body['errcode'])) {
        return new WP_Error('wechat_error', $body['errmsg']);
    }
    
    // 获取用户信息
    $user_url = 'https://api.weixin.qq.com/sns/userinfo';
    $user_params = array(
        'access_token' => $body['access_token'],
        'openid' => $body['openid']
    );
    
    $response = wp_remote_get(add_query_arg($user_params, $user_url));
    if (is_wp_error($response)) {
        return $response;
    }
    
    $user_info = json_decode(wp_remote_retrieve_body($response), true);
    if (isset($user_info['errcode'])) {
        return new WP_Error('wechat_error', $user_info['errmsg']);
    }
    
    return $user_info;
}

/**
 * 获取QQ用户信息
 */
function qioooo_get_qq_user_info($code) {
    // 获取access_token
    $token_url = 'https://graph.qq.com/oauth2.0/token';
    $token_params = array(
        'grant_type' => 'authorization_code',
        'client_id' => QIOOOO_QQ_APPID,
        'client_secret' => QIOOOO_QQ_SECRET,
        'code' => $code,
        'redirect_uri' => home_url()
    );
    
    $response = wp_remote_get(add_query_arg($token_params, $token_url));
    if (is_wp_error($response)) {
        return $response;
    }
    
    $body = wp_remote_retrieve_body($response);
    parse_str($body, $token_data);
    
    if (isset($token_data['error'])) {
        return new WP_Error('qq_error', $token_data['error_description']);
    }
    
    // 获取openid
    $openid_url = 'https://graph.qq.com/oauth2.0/me';
    $openid_params = array(
        'access_token' => $token_data['access_token']
    );
    
    $response = wp_remote_get(add_query_arg($openid_params, $openid_url));
    if (is_wp_error($response)) {
        return $response;
    }
    
    $body = wp_remote_retrieve_body($response);
    $body = str_replace('callback(', '', $body);
    $body = str_replace(');', '', $body);
    $openid_data = json_decode($body, true);
    
    if (isset($openid_data['error'])) {
        return new WP_Error('qq_error', $openid_data['error_description']);
    }
    
    // 获取用户信息
    $user_url = 'https://graph.qq.com/user/get_user_info';
    $user_params = array(
        'access_token' => $token_data['access_token'],
        'oauth_consumer_key' => QIOOOO_QQ_APPID,
        'openid' => $openid_data['openid']
    );
    
    $response = wp_remote_get(add_query_arg($user_params, $user_url));
    if (is_wp_error($response)) {
        return $response;
    }
    
    $user_info = json_decode(wp_remote_retrieve_body($response), true);
    if (isset($user_info['ret']) && $user_info['ret'] !== 0) {
        return new WP_Error('qq_error', $user_info['msg']);
    }
    
    $user_info['openid'] = $openid_data['openid'];
    return $user_info;
}

/**
 * 通过社交ID获取用户
 */
function qioooo_get_user_by_social_id($provider, $social_id) {
    $users = get_users(array(
        'meta_key' => 'qioooo_' . $provider . '_id',
        'meta_value' => $social_id,
        'number' => 1
    ));
    
    return !empty($users) ? $users[0] : false;
}

/**
 * 从社交信息创建用户
 */
function qioooo_create_user_from_social($provider, $user_info) {
    $username = $provider . '_' . $user_info['openid'];
    $email = $username . '@' . $provider . '.com';
    
    // 检查用户名是否已存在
    if (username_exists($username)) {
        $username = $username . '_' . rand(1000, 9999);
    }
    
    // 检查邮箱是否已存在
    if (email_exists($email)) {
        $email = $username . '@' . $provider . '.com';
    }
    
    // 创建用户
    $user_id = wp_create_user($username, wp_generate_password(), $email);
    if (is_wp_error($user_id)) {
        return $user_id;
    }
    
    // 更新用户信息
    $user_data = array(
        'ID' => $user_id,
        'display_name' => $user_info['nickname'],
        'nickname' => $user_info['nickname']
    );
    wp_update_user($user_data);
    
    // 保存社交ID
    update_user_meta($user_id, 'qioooo_' . $provider . '_id', $user_info['openid']);
    
    // 保存头像
    if (!empty($user_info['headimgurl'])) {
        update_user_meta($user_id, 'qioooo_' . $provider . '_avatar', $user_info['headimgurl']);
    }
    
    return get_user_by('id', $user_id);
} 