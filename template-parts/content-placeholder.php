<?php
/**
 * 内容占位符模板
 */

// 检查是否直接访问
if (!defined('ABSPATH')) {
    exit;
}

// 获取当前主题模式
$is_dark_mode = get_theme_mod('enable_dark_mode', false);
$dark_mode_class = $is_dark_mode ? 'dark-mode' : '';
?>

<!-- 文章列表占位符 -->
<div class="qioooo-article-list-placeholder">
    <?php for ($i = 0; $i < 3; $i++) : ?>
        <article class="qioooo-article-placeholder <?php echo esc_attr($dark_mode_class); ?>">
            <!-- 文章缩略图占位符 -->
            <div class="qioooo-thumbnail-placeholder">
                <div class="qioooo-placeholder qioooo-placeholder-gradient"></div>
            </div>
            
            <!-- 文章内容占位符 -->
            <div class="qioooo-content-placeholder">
                <!-- 标题占位符 -->
                <div class="qioooo-title-placeholder">
                    <div class="qioooo-placeholder qioooo-placeholder-shape" style="height: 30px; margin-bottom: 10px;"></div>
                </div>
                
                <!-- 摘要占位符 -->
                <div class="qioooo-excerpt-placeholder">
                    <div class="qioooo-placeholder qioooo-placeholder-shape" style="height: 20px; margin-bottom: 8px;"></div>
                    <div class="qioooo-placeholder qioooo-placeholder-shape" style="height: 20px; margin-bottom: 8px;"></div>
                    <div class="qioooo-placeholder qioooo-placeholder-shape" style="height: 20px; width: 60%;"></div>
                </div>
                
                <!-- 元信息占位符 -->
                <div class="qioooo-meta-placeholder">
                    <div class="qioooo-placeholder qioooo-placeholder-shape" style="height: 16px; width: 120px;"></div>
                </div>
            </div>
        </article>
    <?php endfor; ?>
</div>

<!-- 侧边栏小工具占位符 -->
<aside class="qioooo-widget-placeholder <?php echo esc_attr($dark_mode_class); ?>">
    <!-- 小工具标题占位符 -->
    <div class="qioooo-widget-title-placeholder">
        <div class="qioooo-placeholder qioooo-placeholder-shape" style="height: 25px; margin-bottom: 15px;"></div>
    </div>
    
    <!-- 小工具内容占位符 -->
    <div class="qioooo-widget-content-placeholder">
        <div class="qioooo-placeholder qioooo-placeholder-gradient" style="height: 150px;"></div>
    </div>
</aside>

<!-- 页脚小工具占位符 -->
<footer class="qioooo-footer-widgets-placeholder">
    <?php for ($i = 0; $i < 3; $i++) : ?>
        <div class="qioooo-footer-widget-placeholder <?php echo esc_attr($dark_mode_class); ?>">
            <div class="qioooo-placeholder qioooo-placeholder-shape" style="height: 20px; margin-bottom: 10px;"></div>
            <div class="qioooo-placeholder qioooo-placeholder-gradient" style="height: 100px;"></div>
        </div>
    <?php endfor; ?>
</footer> 