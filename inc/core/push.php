<?php
/**
 * 搜索引擎推送支持
 */

class QIoooo_Push {
    private $baidu_token;
    private $google_key;

    public function __construct() {
        $this->baidu_token = get_option('qioooo_baidu_push_token');
        $this->google_key = get_option('qioooo_google_push_key');

        add_action('publish_post', array($this, 'push_to_search_engines'));
        add_action('transition_post_status', array($this, 'handle_post_status_change'), 10, 3);
    }

    // 推送新文章到搜索引擎
    public function push_to_search_engines($post_id) {
        $post = get_post($post_id);
        if (!$post || $post->post_status !== 'publish') {
            return;
        }

        $url = get_permalink($post_id);
        
        // 推送到百度
        if ($this->baidu_token) {
            $this->push_to_baidu($url);
        }

        // 推送到谷歌
        if ($this->google_key) {
            $this->push_to_google($url);
        }
    }

    // 推送到百度
    private function push_to_baidu($url) {
        $api_url = 'http://data.zz.baidu.com/urls?site=' . urlencode(get_site_url()) . '&token=' . $this->baidu_token;
        
        $response = wp_remote_post($api_url, array(
            'body' => $url,
            'headers' => array(
                'Content-Type' => 'text/plain'
            )
        ));

        if (is_wp_error($response)) {
            error_log('百度推送失败: ' . $response->get_error_message());
        } else {
            $body = json_decode(wp_remote_retrieve_body($response), true);
            if (isset($body['error'])) {
                error_log('百度推送错误: ' . $body['error']);
            }
        }
    }

    // 推送到谷歌
    private function push_to_google($url) {
        $api_url = 'https://www.googleapis.com/indexing/v3/urlNotifications:publish';
        
        $response = wp_remote_post($api_url, array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $this->google_key,
                'Content-Type' => 'application/json'
            ),
            'body' => json_encode(array(
                'url' => $url,
                'type' => 'URL_UPDATED'
            ))
        ));

        if (is_wp_error($response)) {
            error_log('谷歌推送失败: ' . $response->get_error_message());
        } else {
            $body = json_decode(wp_remote_retrieve_body($response), true);
            if (isset($body['error'])) {
                error_log('谷歌推送错误: ' . $body['error']['message']);
            }
        }
    }

    // 处理文章状态变化
    public function handle_post_status_change($new_status, $old_status, $post) {
        if ($new_status === 'publish' && $old_status !== 'publish') {
            $this->push_to_search_engines($post->ID);
        }
    }

    // 添加设置页面
    public static function add_settings_page() {
        add_options_page(
            '搜索引擎推送设置',
            '搜索引擎推送',
            'manage_options',
            'qioooo-push-settings',
            array(__CLASS__, 'render_settings_page')
        );
    }

    // 渲染设置页面
    public static function render_settings_page() {
        if (isset($_POST['qioooo_push_settings'])) {
            check_admin_referer('qioooo_push_settings');
            
            update_option('qioooo_baidu_push_token', sanitize_text_field($_POST['baidu_token']));
            update_option('qioooo_google_push_key', sanitize_text_field($_POST['google_key']));
            
            echo '<div class="notice notice-success"><p>设置已保存</p></div>';
        }

        $baidu_token = get_option('qioooo_baidu_push_token');
        $google_key = get_option('qioooo_google_push_key');
        ?>
        <div class="wrap">
            <h1>搜索引擎推送设置</h1>
            <form method="post" action="">
                <?php wp_nonce_field('qioooo_push_settings'); ?>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="baidu_token">百度推送 Token</label>
                        </th>
                        <td>
                            <input type="text" id="baidu_token" name="baidu_token" 
                                   value="<?php echo esc_attr($baidu_token); ?>" class="regular-text">
                            <p class="description">在百度站长平台获取的推送 Token</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="google_key">Google API Key</label>
                        </th>
                        <td>
                            <input type="text" id="google_key" name="google_key" 
                                   value="<?php echo esc_attr($google_key); ?>" class="regular-text">
                            <p class="description">在 Google Search Console 获取的 API Key</p>
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <input type="submit" name="qioooo_push_settings" class="button-primary" value="保存设置">
                </p>
            </form>
        </div>
        <?php
    }
}

// 初始化
add_action('admin_menu', array('QIoooo_Push', 'add_settings_page'));
new QIoooo_Push(); 