<?php
/**
 * 小工具测试页面
 */

// 退出如果直接访问
if (!defined('ABSPATH')) {
    exit;
}

function qioooo_widgets_test_page() {
    ?>
    <div class="wrap">
        <h1>小工具测试</h1>
        
        <div class="card">
            <h2>测试说明</h2>
            <p>此页面将自动测试小工具的各项功能，包括：</p>
            <ul>
                <li>小工具注册状态</li>
                <li>小工具缓存功能</li>
                <li>小工具样式和脚本加载</li>
                <li>小工具区域注册</li>
                <li>小工具选项功能</li>
            </ul>
        </div>
        
        <div class="card">
            <h2>运行测试</h2>
            <p>
                <button id="run-tests" class="button button-primary">运行测试</button>
                <span class="spinner" style="float: none; margin-left: 10px;"></span>
            </p>
        </div>
        
        <div id="test-results" class="card" style="display: none;">
            <h2>测试结果</h2>
            <div id="results-content"></div>
        </div>
    </div>
    
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        $('#run-tests').on('click', function() {
            var $button = $(this);
            var $spinner = $button.next('.spinner');
            var $results = $('#test-results');
            var $content = $('#results-content');
            
            $button.prop('disabled', true);
            $spinner.addClass('is-active');
            $content.empty();
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'qioooo_widgets_test',
                    nonce: '<?php echo wp_create_nonce('qioooo_widgets_test_nonce'); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        var results = response.data;
                        var html = '';
                        
                        for (var test in results) {
                            var testData = results[test];
                            var statusClass = testData.status === 'success' ? 'updated' : 'error';
                            
                            html += '<div class="' + statusClass + '">';
                            html += '<h3>' + testData.message + '</h3>';
                            html += '<ul>';
                            
                            for (var i = 0; i < testData.details.length; i++) {
                                html += '<li>' + testData.details[i] + '</li>';
                            }
                            
                            html += '</ul>';
                            html += '</div>';
                        }
                        
                        $content.html(html);
                        $results.show();
                    } else {
                        $content.html('<div class="error"><p>测试失败：' + response.data + '</p></div>');
                        $results.show();
                    }
                },
                error: function() {
                    $content.html('<div class="error"><p>测试请求失败</p></div>');
                    $results.show();
                },
                complete: function() {
                    $button.prop('disabled', false);
                    $spinner.removeClass('is-active');
                }
            });
        });
    });
    </script>
    <?php
}

// 添加测试页面到菜单
function qioooo_add_widgets_test_page() {
    add_theme_page(
        '小工具测试',
        '小工具测试',
        'manage_options',
        'qioooo-widgets-test',
        'qioooo_widgets_test_page'
    );
}
add_action('admin_menu', 'qioooo_add_widgets_test_page'); 