<?php
/**
 * 小工具测试脚本
 */

// 退出如果直接访问
if (!defined('ABSPATH')) {
    exit;
}

class QIoooo_Widgets_Test {
    private $widgets = array();
    private $test_results = array();

    public function __construct() {
        $this->init();
    }

    private function init() {
        // 注册测试钩子
        add_action('admin_init', array($this, 'run_tests'));
        add_action('wp_ajax_qioooo_widgets_test', array($this, 'ajax_test'));
    }

    public function run_tests() {
        // 测试小工具注册
        $this->test_widget_registration();
        
        // 测试小工具缓存
        $this->test_widget_caching();
        
        // 测试小工具样式加载
        $this->test_widget_styles();
        
        // 测试小工具区域
        $this->test_widget_areas();
        
        // 测试小工具自定义选项
        $this->test_widget_options();
        
        // 输出测试结果
        $this->output_results();
    }

    private function test_widget_registration() {
        global $wp_widget_factory;
        
        $this->test_results['registration'] = array(
            'status' => 'success',
            'message' => '小工具注册测试',
            'details' => array()
        );

        // 检查小工具是否已注册
        $required_widgets = array(
            'QIoooo_Recent_Posts_Widget',
            'QIoooo_Popular_Posts_Widget',
            'QIoooo_Categories_Widget',
            'QIoooo_Tags_Widget',
            'QIoooo_Search_Widget',
            'QIoooo_About_Widget',
            'QIoooo_Social_Links_Widget',
            'QIoooo_Newsletter_Widget',
            'QIoooo_Archives_Widget',
            'QIoooo_Calendar_Widget',
            'QIoooo_Recent_Comments_Widget',
            'QIoooo_Random_Posts_Widget'
        );

        foreach ($required_widgets as $widget) {
            if (!isset($wp_widget_factory->widgets[$widget])) {
                $this->test_results['registration']['status'] = 'error';
                $this->test_results['registration']['details'][] = sprintf(
                    '错误: %s 小工具未注册',
                    $widget
                );
            } else {
                $this->test_results['registration']['details'][] = sprintf(
                    '成功: %s 小工具已注册',
                    $widget
                );
            }
        }
    }

    private function test_widget_caching() {
        $this->test_results['caching'] = array(
            'status' => 'success',
            'message' => '小工具缓存测试',
            'details' => array()
        );

        // 测试缓存功能
        $cache = QIoooo_Widget_Cache::get_instance();
        
        // 测试缓存设置
        $test_data = 'test_cache_data';
        $cache->cache_widget('test_widget', array(), $test_data);
        
        // 测试缓存获取
        $cached_data = $cache->get_cached_widget('test_widget', array());
        
        if ($cached_data !== $test_data) {
            $this->test_results['caching']['status'] = 'error';
            $this->test_results['caching']['details'][] = '错误: 缓存数据不匹配';
        } else {
            $this->test_results['caching']['details'][] = '成功: 缓存功能正常';
        }
        
        // 测试缓存清理
        $cache->clear_cache('test_widget');
        $cached_data = $cache->get_cached_widget('test_widget', array());
        
        if ($cached_data !== false) {
            $this->test_results['caching']['status'] = 'error';
            $this->test_results['caching']['details'][] = '错误: 缓存清理失败';
        } else {
            $this->test_results['caching']['details'][] = '成功: 缓存清理正常';
        }
    }

    private function test_widget_styles() {
        $this->test_results['styles'] = array(
            'status' => 'success',
            'message' => '小工具样式测试',
            'details' => array()
        );

        // 检查样式文件是否存在
        $style_file = get_template_directory() . '/assets/css/widgets.css';
        if (!file_exists($style_file)) {
            $this->test_results['styles']['status'] = 'error';
            $this->test_results['styles']['details'][] = '错误: 小工具样式文件不存在';
        } else {
            $this->test_results['styles']['details'][] = '成功: 小工具样式文件存在';
        }

        // 检查脚本文件是否存在
        $script_file = get_template_directory() . '/assets/js/widgets.js';
        if (!file_exists($script_file)) {
            $this->test_results['styles']['status'] = 'error';
            $this->test_results['styles']['details'][] = '错误: 小工具脚本文件不存在';
        } else {
            $this->test_results['styles']['details'][] = '成功: 小工具脚本文件存在';
        }
    }

    private function test_widget_areas() {
        global $wp_registered_sidebars;
        
        $this->test_results['areas'] = array(
            'status' => 'success',
            'message' => '小工具区域测试',
            'details' => array()
        );

        // 检查小工具区域是否已注册
        $required_areas = array('sidebar-1', 'footer-1', 'home-1');
        
        foreach ($required_areas as $area) {
            if (!isset($wp_registered_sidebars[$area])) {
                $this->test_results['areas']['status'] = 'error';
                $this->test_results['areas']['details'][] = sprintf(
                    '错误: %s 小工具区域未注册',
                    $area
                );
            } else {
                $this->test_results['areas']['details'][] = sprintf(
                    '成功: %s 小工具区域已注册',
                    $area
                );
            }
        }
    }

    private function test_widget_options() {
        $this->test_results['options'] = array(
            'status' => 'success',
            'message' => '小工具选项测试',
            'details' => array()
        );

        // 测试小工具选项保存和更新
        $widget = new QIoooo_Recent_Posts_Widget();
        
        // 测试选项更新
        $new_instance = array(
            'title' => '测试标题',
            'number' => 5
        );
        
        $old_instance = array();
        $instance = $widget->update($new_instance, $old_instance);
        
        if ($instance['title'] !== $new_instance['title'] || 
            $instance['number'] !== $new_instance['number']) {
            $this->test_results['options']['status'] = 'error';
            $this->test_results['options']['details'][] = '错误: 小工具选项更新失败';
        } else {
            $this->test_results['options']['details'][] = '成功: 小工具选项更新正常';
        }
    }

    private function output_results() {
        echo '<div class="wrap">';
        echo '<h1>小工具测试结果</h1>';
        
        foreach ($this->test_results as $test) {
            $status_class = $test['status'] === 'success' ? 'updated' : 'error';
            echo '<div class="' . $status_class . '">';
            echo '<h3>' . $test['message'] . '</h3>';
            echo '<ul>';
            foreach ($test['details'] as $detail) {
                echo '<li>' . $detail . '</li>';
            }
            echo '</ul>';
            echo '</div>';
        }
        
        echo '</div>';
    }

    public function ajax_test() {
        check_ajax_referer('qioooo_widgets_test_nonce', 'nonce');
        
        $this->run_tests();
        
        wp_send_json_success($this->test_results);
    }
}

// 初始化测试
new QIoooo_Widgets_Test(); 