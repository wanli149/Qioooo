# QIoooo WordPress 主题

一个现代化的 WordPress 主题，支持暗黑模式和自定义主题设置。

## 功能特点

- 🎨 支持暗黑模式
- 🎯 响应式设计
- 🔧 主题自定义设置
- 🌐 多语言支持
- 🚀 性能优化
- 🔒 安全性增强
- ♿ 无障碍支持
- 📱 移动端优化

## 系统要求

- WordPress 5.9+
- PHP 7.4+
- MySQL 5.6+ 或 MariaDB 10.1+
- 现代浏览器支持

## 安装说明

1. 下载主题包
2. 上传到 WordPress 主题目录：`wp-content/themes/qioooo/`
3. 在 WordPress 后台启用主题
4. 进入"外观" > "主题设置"进行配置

## 主题设置

### 基本设置
- 主题颜色
- 暗黑模式
- 布局选项
- 字体设置

### 高级设置
- 自定义 CSS
- 自定义 JavaScript
- SEO 设置
- 性能优化

## 模板文件

```
qioooo/
├── style.css              # 主题样式和基本信息
├── functions.php          # 主题功能
├── header.php            # 头部模板
├── footer.php            # 底部模板
├── index.php             # 主页模板
├── sidebar.php           # 侧边栏模板
├── page.php              # 页面模板
├── single.php            # 文章模板
├── archive.php           # 归档模板
├── search.php            # 搜索模板
├── 404.php               # 404模板
├── inc/                  # 功能文件目录
│   ├── template-functions.php
│   ├── template-tags.php
│   └── customizer.php
├── template-parts/       # 模板部件
│   ├── content.php
│   ├── content-page.php
│   └── content-none.php
├── js/                   # JavaScript 文件
│   ├── main.js
│   ├── theme/
│   └── utils/
├── css/                  # CSS 文件
│   ├── style.css
│   └── animations.css
└── languages/            # 语言文件
    └── qioooo.pot
```

## 自定义开发

### 添加自定义样式
在 `style.css` 中添加自定义样式，或使用 WordPress 自定义器。

### 添加自定义功能
在 `functions.php` 中添加自定义功能，或创建子主题。

### 添加自定义模板
在 `template-parts` 目录中添加自定义模板。

## 性能优化

- 使用 Webpack 打包和压缩资源
- 延迟加载图片
- 缓存优化
- 代码分割

## 安全性

- CSRF 保护
- XSS 防护
- 安全的数据存储
- 输入验证和清理

## 更新日志

### 1.0.0
- 初始版本发布
- 支持暗黑模式
- 响应式设计
- 主题自定义设置

## 贡献指南

1. Fork 项目
2. 创建特性分支
3. 提交更改
4. 推送到分支
5. 创建 Pull Request

## 许可证

GNU General Public License v2 or later

## 支持

- 文档：[主题文档](https://your-domain.com/qioooo/docs)
- 问题反馈：[GitHub Issues](https://github.com/your-username/qioooo/issues)
- 联系邮箱：support@your-domain.com 