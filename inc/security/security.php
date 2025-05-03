/**
 * CSRF防护
 */
function qioooo_csrf_protection() {
    // 生成CSRF令牌
    if (!session_id()) {
        session_start();
    }
    
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    // 添加CSRF令牌到表单
    add_action('wp_head', function() {
        echo '<meta name="csrf-token" content="' . esc_attr($_SESSION['csrf_token']) . '">';
    });

    // 验证CSRF令牌
    add_action('init', function() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? '';
            if (!hash_equals($_SESSION['csrf_token'], $token)) {
                wp_die('CSRF验证失败', '安全错误', array('response' => 403));
            }
        }
    });
}
add_action('init', 'qioooo_csrf_protection');

/**
 * 文件上传安全
 */
function qioooo_secure_file_upload() {
    // 限制文件类型
    add_filter('upload_mimes', function($mimes) {
        $allowed_types = array(
            'jpg|jpeg|jpe' => 'image/jpeg',
            'gif' => 'image/gif',
            'png' => 'image/png',
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        );
        return $allowed_types;
    });

    // 限制文件大小
    add_filter('upload_size_limit', function($size) {
        return 5 * 1024 * 1024; // 5MB
    });

    // 扫描上传文件
    add_filter('wp_handle_upload_prefilter', function($file) {
        // 检查文件扩展名
        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx');
        
        if (!in_array($file_ext, $allowed_extensions)) {
            $file['error'] = '不允许的文件类型';
            return $file;
        }

        // 检查MIME类型
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        $allowed_mimes = array(
            'image/jpeg',
            'image/png',
            'image/gif',
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        );

        if (!in_array($mime_type, $allowed_mimes)) {
            $file['error'] = '无效的文件类型';
            return $file;
        }

        return $file;
    });
}
add_action('init', 'qioooo_secure_file_upload'); 