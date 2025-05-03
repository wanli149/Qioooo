# QIoooo 主题开发者手册 (V1.0)

## 欢迎使用 QIoooo 主题开发文档！

本手册面向开发者，提供主题的详细技术文档、钩子列表和扩展指南。通过本手册，您可以了解主题的核心架构、自定义开发方法和最佳实践。

## 目录

1. [主题架构](#1-主题架构)
   - [1.1 文件结构](#11-文件结构)
   - [1.2 核心功能模块](#12-核心功能模块)
   - [1.3 主题设置系统](#13-主题设置系统)

2. [开发环境](#2-开发环境)
   - [2.1 环境要求](#21-环境要求)
   - [2.2 开发工具](#22-开发工具)
   - [2.3 调试模式](#23-调试模式)

3. [主题钩子](#3-主题钩子)
   - [3.1 Action 钩子](#31-action-钩子)
   - [3.2 Filter 钩子](#32-filter-钩子)

4. [自定义开发](#4-自定义开发)
   - [4.1 添加自定义功能](#41-添加自定义功能)
   - [4.2 修改主题样式](#42-修改主题样式)
   - [4.3 创建子主题](#43-创建子主题)

5. [性能优化](#5-性能优化)
   - [5.1 代码优化](#51-代码优化)
   - [5.2 资源加载](#52-资源加载)
   - [5.3 缓存策略](#53-缓存策略)

6. [安全指南](#6-安全指南)
   - [6.1 数据验证](#61-数据验证)
   - [6.2 权限检查](#62-权限检查)
   - [6.3 安全最佳实践](#63-安全最佳实践)

## 1. 主题架构

### 1.1 文件结构

```
QIoooo/
├── assets/              # 静态资源
│   ├── css/            # CSS 文件
│   ├── js/             # JavaScript 文件
│   └── images/         # 图片资源
├── inc/                # 功能模块
│   ├── performance.php # 性能优化
│   ├── security.php    # 安全设置
│   └── seo.php         # SEO 优化
├── template-parts/     # 模板部件
├── functions.php       # 主题函数
├── style.css          # 主题样式
└── index.php          # 主模板文件
```

### 1.2 核心功能模块

- **性能优化模块** (`inc/performance.php`)
  - 资源压缩
  - 浏览器缓存
  - 图片懒加载
  - 数据库优化

- **安全设置模块** (`inc/security.php`)
  - 防止目录浏览
  - 限制登录尝试
  - 防止 SQL 注入
  - 防止 XSS 攻击

- **SEO 优化模块** (`inc/seo.php`)
  - 结构化数据
  - Meta 标签优化
  - 站点地图生成
  - 面包屑导航

### 1.3 主题设置系统

主题设置系统基于 WordPress Customizer API 和主题选项框架构建，提供以下功能：

- 实时预览
- 设置导入/导出
- 模块化配置
- 响应式控制

## 2. 开发环境

### 2.1 环境要求

- PHP 7.4+
- MySQL 5.6+
- WordPress 5.8+
- 内存限制：256M+
- 最大执行时间：60s+

### 2.2 开发工具

推荐使用以下工具进行开发：

- 代码编辑器：VS Code、PHPStorm
- 版本控制：Git
- 调试工具：Xdebug、Query Monitor
- 构建工具：Webpack、Gulp

### 2.3 调试模式

在 `wp-config.php` 中启用调试模式：

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

## 3. 主题钩子

### 3.1 Action 钩子

```php
// 主题初始化
do_action('qioooo_init');

// 主题设置保存前
do_action('qioooo_before_save_settings');

// 主题设置保存后
do_action('qioooo_after_save_settings');

// 文章内容渲染前
do_action('qioooo_before_content');

// 文章内容渲染后
do_action('qioooo_after_content');
```

### 3.2 Filter 钩子

```php
// 修改主题设置默认值
apply_filters('qioooo_default_settings', $defaults);

// 修改文章标题
apply_filters('qioooo_post_title', $title);

// 修改文章内容
apply_filters('qioooo_post_content', $content);

// 修改侧边栏内容
apply_filters('qioooo_sidebar_content', $content);
```

## 4. 自定义开发

### 4.1 添加自定义功能

1. 创建功能文件：
```php
// inc/custom-feature.php
function qioooo_custom_feature() {
    // 功能代码
}
add_action('init', 'qioooo_custom_feature');
```

2. 在 `functions.php` 中引入：
```php
require_once get_template_directory() . '/inc/custom-feature.php';
```

### 4.2 修改主题样式

1. 创建子主题
2. 在子主题的 `style.css` 中添加自定义样式
3. 使用 `!important` 覆盖父主题样式（谨慎使用）

### 4.3 创建子主题

1. 创建子主题目录：
```
QIoooo-child/
├── style.css
└── functions.php
```

2. 在 `style.css` 中添加：
```css
/*
Theme Name: QIoooo Child
Template: QIoooo
*/
```

3. 在 `functions.php` 中添加：
```php
add_action('wp_enqueue_scripts', 'qioooo_child_enqueue_styles');
function qioooo_child_enqueue_styles() {
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('child-style', get_stylesheet_uri());
}
```

## 5. 性能优化

### 5.1 代码优化

- 使用缓存
- 优化数据库查询
- 减少 HTTP 请求
- 压缩资源文件

### 5.2 资源加载

```php
// 延迟加载非关键 CSS
add_action('wp_enqueue_scripts', 'qioooo_defer_css');
function qioooo_defer_css() {
    wp_enqueue_style('non-critical-css', get_template_directory_uri() . '/assets/css/non-critical.css', array(), null);
    wp_style_add_data('non-critical-css', 'defer', true);
}

// 延迟加载非关键 JavaScript
add_action('wp_enqueue_scripts', 'qioooo_defer_js');
function qioooo_defer_js() {
    wp_enqueue_script('non-critical-js', get_template_directory_uri() . '/assets/js/non-critical.js', array(), null, true);
    wp_script_add_data('non-critical-js', 'defer', true);
}
```

### 5.3 缓存策略

```php
// 设置浏览器缓存
add_action('send_headers', 'qioooo_browser_caching');
function qioooo_browser_caching() {
    header('Cache-Control: public, max-age=31536000');
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
}
```

## 6. 安全指南

### 6.1 数据验证

```php
// 输入验证
function qioooo_validate_input($input) {
    return sanitize_text_field($input);
}

// 输出转义
function qioooo_escape_output($output) {
    return esc_html($output);
}
```

### 6.2 权限检查

```php
// 检查用户权限
function qioooo_check_permission() {
    if (!current_user_can('manage_options')) {
        wp_die(__('您没有足够的权限执行此操作。'));
    }
}
```

### 6.3 安全最佳实践

- 使用 WordPress 安全函数
- 定期更新主题和插件
- 使用强密码
- 限制登录尝试
- 启用 SSL
- 定期备份

## 贡献指南

欢迎为 QIoooo 主题贡献代码！请遵循以下步骤：

1. Fork 主题仓库
2. 创建特性分支
3. 提交更改
4. 推送到分支
5. 创建 Pull Request

## 许可证

QIoooo 主题采用 GPL v2 或更高版本许可证。 