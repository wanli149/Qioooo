/**
 * 小工具测试脚本
 */

(function($) {
    'use strict';

    // 测试小工具前端显示
    function testFrontendDisplay() {
        var $widgets = $('.widget');
        var results = {
            total: $widgets.length,
            visible: $widgets.filter(':visible').length,
            errors: []
        };

        $widgets.each(function() {
            var $widget = $(this);
            var widgetId = $widget.attr('id');
            
            // 检查小工具内容
            if (!$widget.find('.widget-content').length) {
                results.errors.push(widgetId + ': 缺少小工具内容');
            }
            
            // 检查小工具标题
            if (!$widget.find('.widget-title').length) {
                results.errors.push(widgetId + ': 缺少小工具标题');
            }
        });

        return results;
    }

    // 测试小工具缓存
    function testWidgetCache() {
        var results = {
            success: true,
            errors: []
        };

        // 测试缓存设置
        try {
            var testData = 'test_cache_data';
            localStorage.setItem('qioooo_widget_test', testData);
            
            // 测试缓存获取
            var cachedData = localStorage.getItem('qioooo_widget_test');
            if (cachedData !== testData) {
                results.success = false;
                results.errors.push('缓存数据不匹配');
            }
            
            // 测试缓存清理
            localStorage.removeItem('qioooo_widget_test');
            if (localStorage.getItem('qioooo_widget_test') !== null) {
                results.success = false;
                results.errors.push('缓存清理失败');
            }
        } catch (e) {
            results.success = false;
            results.errors.push('缓存测试失败: ' + e.message);
        }

        return results;
    }

    // 测试小工具样式
    function testWidgetStyles() {
        var results = {
            success: true,
            errors: []
        };

        // 检查样式文件是否加载
        var styleSheets = document.styleSheets;
        var widgetsCssLoaded = false;

        for (var i = 0; i < styleSheets.length; i++) {
            if (styleSheets[i].href && styleSheets[i].href.indexOf('widgets.css') !== -1) {
                widgetsCssLoaded = true;
                break;
            }
        }

        if (!widgetsCssLoaded) {
            results.success = false;
            results.errors.push('小工具样式文件未加载');
        }

        return results;
    }

    // 测试小工具区域
    function testWidgetAreas() {
        var results = {
            success: true,
            errors: []
        };

        var requiredAreas = ['sidebar-1', 'footer-1', 'home-1'];
        
        requiredAreas.forEach(function(area) {
            if (!$('#' + area).length) {
                results.success = false;
                results.errors.push(area + ' 小工具区域未找到');
            }
        });

        return results;
    }

    // 运行所有测试
    function runAllTests() {
        var results = {
            frontend: testFrontendDisplay(),
            cache: testWidgetCache(),
            styles: testWidgetStyles(),
            areas: testWidgetAreas()
        };

        return results;
    }

    // 初始化测试
    $(document).ready(function() {
        // 添加测试按钮
        var $testButton = $('<button>')
            .addClass('button')
            .text('运行前端测试')
            .on('click', function() {
                var results = runAllTests();
                console.log('测试结果:', results);
                
                // 显示测试结果
                var $results = $('#test-results');
                var html = '<div class="card"><h2>前端测试结果</h2>';
                
                // 前端显示测试结果
                html += '<div class="' + (results.frontend.errors.length ? 'error' : 'updated') + '">';
                html += '<h3>前端显示测试</h3>';
                html += '<ul>';
                html += '<li>总小工具数: ' + results.frontend.total + '</li>';
                html += '<li>可见小工具数: ' + results.frontend.visible + '</li>';
                if (results.frontend.errors.length) {
                    results.frontend.errors.forEach(function(error) {
                        html += '<li>' + error + '</li>';
                    });
                }
                html += '</ul></div>';
                
                // 缓存测试结果
                html += '<div class="' + (results.cache.success ? 'updated' : 'error') + '">';
                html += '<h3>缓存测试</h3>';
                html += '<ul>';
                if (results.cache.errors.length) {
                    results.cache.errors.forEach(function(error) {
                        html += '<li>' + error + '</li>';
                    });
                } else {
                    html += '<li>缓存功能正常</li>';
                }
                html += '</ul></div>';
                
                // 样式测试结果
                html += '<div class="' + (results.styles.success ? 'updated' : 'error') + '">';
                html += '<h3>样式测试</h3>';
                html += '<ul>';
                if (results.styles.errors.length) {
                    results.styles.errors.forEach(function(error) {
                        html += '<li>' + error + '</li>';
                    });
                } else {
                    html += '<li>样式加载正常</li>';
                }
                html += '</ul></div>';
                
                // 区域测试结果
                html += '<div class="' + (results.areas.success ? 'updated' : 'error') + '">';
                html += '<h3>小工具区域测试</h3>';
                html += '<ul>';
                if (results.areas.errors.length) {
                    results.areas.errors.forEach(function(error) {
                        html += '<li>' + error + '</li>';
                    });
                } else {
                    html += '<li>小工具区域正常</li>';
                }
                html += '</ul></div>';
                
                html += '</div>';
                
                $results.html(html).show();
            });

        $('.wrap').append($testButton);
    });

})(jQuery); 