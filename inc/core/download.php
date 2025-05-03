<?php
/**
 * 资源下载功能
 *
 * @package QIoooo
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 处理资源下载请求
 */
function qioooo_handle_download() {
    // 检查nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'qioooo_nonce')) {
        wp_send_json_error(__('无效的请求', 'qioooo'));
    }
    
    // 检查用户权限
    if (!is_user_logged_in()) {
        wp_send_json_error(__('请先登录', 'qioooo'));
    }
    
    $resource_id = intval($_POST['resource_id']);
    $resource = get_post($resource_id);
    
    // 检查资源是否存在
    if (!$resource || $resource->post_type !== 'resource') {
        wp_send_json_error(__('资源不存在', 'qioooo'));
    }
    
    // 获取下载链接
    $download_url = get_post_meta($resource_id, 'download_url', true);
    if (empty($download_url)) {
        wp_send_json_error(__('下载链接无效', 'qioooo'));
    }
    
    // 更新下载次数
    $download_count = intval(get_post_meta($resource_id, 'download_count', true));
    update_post_meta($resource_id, 'download_count', $download_count + 1);
    
    // 记录用户下载
    qioooo_record_download($resource_id);
    
    wp_send_json_success(array(
        'download_url' => $download_url,
        'message' => __('开始下载', 'qioooo')
    ));
}
add_action('wp_ajax_qioooo_download', 'qioooo_handle_download');
add_action('wp_ajax_nopriv_qioooo_download', 'qioooo_handle_download');

/**
 * 获取资源下载信息
 */
function qioooo_get_download_info($resource_id) {
    $download_count = intval(get_post_meta($resource_id, 'download_count', true));
    $file_size = get_post_meta($resource_id, 'file_size', true);
    $file_format = get_post_meta($resource_id, 'file_format', true);
    $rating = intval(get_post_meta($resource_id, 'rating', true));
    
    return array(
        'download_count' => $download_count,
        'file_size' => $file_size,
        'file_format' => $file_format,
        'rating' => $rating
    );
}

/**
 * 显示资源下载按钮
 */
function qioooo_download_button($resource_id) {
    $download_info = qioooo_get_download_info($resource_id);
    $is_logged_in = is_user_logged_in();
    
    ob_start();
    ?>
    <div class="download-info">
        <div class="download-stats">
            <span class="download-count">
                <i class="fas fa-download"></i>
                <?php echo number_format($download_info['download_count']); ?>
            </span>
            <span class="file-size">
                <i class="fas fa-file"></i>
                <?php echo esc_html($download_info['file_size']); ?>
            </span>
            <span class="file-format">
                <i class="fas fa-file-alt"></i>
                <?php echo esc_html($download_info['file_format']); ?>
            </span>
            <span class="rating">
                <?php for ($i = 1; $i <= 5; $i++) : ?>
                    <i class="fas fa-star<?php echo $i <= $download_info['rating'] ? '' : '-o'; ?>"></i>
                <?php endfor; ?>
            </span>
        </div>
        
        <button class="download-button<?php echo $is_logged_in ? '' : ' disabled'; ?>" 
                data-resource-id="<?php echo $resource_id; ?>"
                <?php echo $is_logged_in ? '' : ' disabled'; ?>>
            <i class="fas fa-download"></i>
            <?php echo $is_logged_in ? __('下载资源', 'qioooo') : __('登录后下载', 'qioooo'); ?>
        </button>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * 添加资源下载按钮到内容
 */
function qioooo_add_download_button($content) {
    if (is_singular('resource')) {
        $resource_id = get_the_ID();
        $download_button = qioooo_download_button($resource_id);
        $content .= $download_button;
    }
    return $content;
}
add_filter('the_content', 'qioooo_add_download_button'); 