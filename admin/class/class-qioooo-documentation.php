<?php
/**
 * 主题文档类
 *
 * @package QIoooo
 */

if (!defined('ABSPATH')) {
    exit;
}

class QIoooo_Documentation {
    /**
     * 文档页面 slug
     */
    private $page_slug = 'qioooo-documentation';

    /**
     * 文档部分
     */
    private $sections = array();

    /**
     * 构造函数
     */
    public function __construct() {
        $this->init_sections();
        $this->init_hooks();
        $this->load_templates();
    }

    /**
     * 加载文档页面模板
     */
    private function load_templates() {
        require_once QIOOOO_DIR . '/admin/partials/documentation.php';
    }

    /**
     * 初始化文档部分
     */
    private function init_sections() {
        $this->sections = array(
            'getting-started' => array(
                'title' => __('开始使用', 'qioooo'),
                'content' => $this->get_getting_started_content()
            ),
            'customization' => array(
                'title' => __('主题定制', 'qioooo'),
                'content' => $this->get_customization_content()
            ),
            'features' => array(
                'title' => __('主题功能', 'qioooo'),
                'content' => $this->get_features_content()
            ),
            'faq' => array(
                'title' => __('常见问题', 'qioooo'),
                'content' => $this->get_faq_content()
            ),
            'support' => array(
                'title' => __('技术支持', 'qioooo'),
                'content' => $this->get_support_content()
            )
        );
    }

    /**
     * 初始化钩子
     */
    private function init_hooks() {
        add_action('admin_menu', array($this, 'add_documentation_page'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    /**
     * 注册文档页面样式和脚本
     */
    public function enqueue_scripts() {
        wp_register_style('qioooo-documentation', get_template_directory_uri() . '/admin/assets/css/documentation.css', array(), QIOOOO_VERSION);
        wp_register_script('qioooo-documentation', get_template_directory_uri() . '/admin/assets/js/documentation.js', array('jquery'), QIOOOO_VERSION, true);
        
        wp_enqueue_style('qioooo-documentation');
        wp_enqueue_script('qioooo-documentation');
    }

    /**
     * 添加文档页面
     */
    public function add_documentation_page() {
        add_submenu_page(
            'themes.php',
            __('QIoooo 文档', 'qioooo'),
            __('QIoooo 文档', 'qioooo'),
            'manage_options',
            $this->page_slug,
            array($this, 'render_documentation_page')
        );
    }

    /**
     * 渲染文档页面
     */
    public function render_documentation_page() {
        ?>
        <div class="wrap qioooo-documentation">
            <h1><?php echo esc_html__('QIoooo 主题文档', 'qioooo'); ?></h1>
            
            <div class="documentation-tabs">
                <?php foreach ($this->sections as $id => $section) : ?>
                    <div class="tab" id="tab-<?php echo esc_attr($id); ?>">
                        <h2><?php echo esc_html($section['title']); ?></h2>
                        <div class="tab-content">
                            <?php echo wp_kses_post($section['content']); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }

    /**
     * 获取"开始使用"内容
     */
    private function get_getting_started_content() {
        return '
            <h3>' . __('安装主题', 'qioooo') . '</h3>
            <ol>
                <li>' . __('通过 WordPress 后台上传主题文件', 'qioooo') . '</li>
                <li>' . __('激活主题', 'qioooo') . '</li>
                <li>' . __('访问主题设置页面进行配置', 'qioooo') . '</li>
            </ol>

            <h3>' . __('基本设置', 'qioooo') . '</h3>
            <p>' . __('在主题设置页面中，您可以配置：', 'qioooo') . '</p>
            <ul>
                <li>' . __('网站 Logo 和图标', 'qioooo') . '</li>
                <li>' . __('主题颜色和布局', 'qioooo') . '</li>
                <li>' . __('社交链接', 'qioooo') . '</li>
            </ul>
        ';
    }

    /**
     * 获取"主题定制"内容
     */
    private function get_customization_content() {
        return '
            <h3>' . __('外观定制', 'qioooo') . '</h3>
            <p>' . __('您可以通过以下方式自定义主题外观：', 'qioooo') . '</p>
            <ul>
                <li>' . __('使用主题设置页面', 'qioooo') . '</li>
                <li>' . __('添加自定义 CSS', 'qioooo') . '</li>
                <li>' . __('使用子主题', 'qioooo') . '</li>
            </ul>

            <h3>' . __('布局选项', 'qioooo') . '</h3>
            <p>' . __('主题提供多种布局选项：', 'qioooo') . '</p>
            <ul>
                <li>' . __('右侧边栏', 'qioooo') . '</li>
                <li>' . __('左侧边栏', 'qioooo') . '</li>
                <li>' . __('无边栏', 'qioooo') . '</li>
            </ul>
        ';
    }

    /**
     * 获取"主题功能"内容
     */
    private function get_features_content() {
        return '
            <h3>' . __('核心功能', 'qioooo') . '</h3>
            <ul>
                <li>' . __('响应式设计', 'qioooo') . '</li>
                <li>' . __('SEO 优化', 'qioooo') . '</li>
                <li>' . __('社交媒体集成', 'qioooo') . '</li>
                <li>' . __('自定义小工具', 'qioooo') . '</li>
            </ul>

            <h3>' . __('高级功能', 'qioooo') . '</h3>
            <ul>
                <li>' . __('主题更新系统', 'qioooo') . '</li>
                <li>' . __('自定义文章类型', 'qioooo') . '</li>
                <li>' . __('多语言支持', 'qioooo') . '</li>
            </ul>
        ';
    }

    /**
     * 获取"常见问题"内容
     */
    private function get_faq_content() {
        return '
            <h3>' . __('常见问题解答', 'qioooo') . '</h3>
            <div class="faq-item">
                <h4>' . __('如何更新主题？', 'qioooo') . '</h4>
                <p>' . __('主题会自动检查更新，您可以在主题设置页面手动检查更新。', 'qioooo') . '</p>
            </div>
            <div class="faq-item">
                <h4>' . __('如何添加自定义 CSS？', 'qioooo') . '</h4>
                <p>' . __('您可以在主题设置页面的"外观"部分添加自定义 CSS。', 'qioooo') . '</p>
            </div>
            <div class="faq-item">
                <h4>' . __('如何创建子主题？', 'qioooo') . '</h4>
                <p>' . __('请参考 WordPress 官方文档关于创建子主题的说明。', 'qioooo') . '</p>
            </div>
        ';
    }

    /**
     * 获取"技术支持"内容
     */
    private function get_support_content() {
        return '
            <h3>' . __('获取帮助', 'qioooo') . '</h3>
            <p>' . __('如果您遇到问题，可以通过以下方式获取支持：', 'qioooo') . '</p>
            <ul>
                <li>' . __('查看在线文档', 'qioooo') . '</li>
                <li>' . __('访问支持论坛', 'qioooo') . '</li>
                <li>' . __('联系技术支持', 'qioooo') . '</li>
            </ul>

            <h3>' . __('报告问题', 'qioooo') . '</h3>
            <p>' . __('如果您发现主题存在 bug，请通过以下方式报告：', 'qioooo') . '</p>
            <ul>
                <li>' . __('在 GitHub 上提交 issue', 'qioooo') . '</li>
                <li>' . __('发送邮件到支持邮箱', 'qioooo') . '</li>
            </ul>
        ';
    }
} 