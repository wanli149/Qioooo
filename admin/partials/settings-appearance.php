<?php
/**
 * 外观设置页面模板
 *
 * @package QIoooo
 */

if (!defined('ABSPATH')) {
    exit;
}

$options = get_option('qioooo_options', array());
?>

<form method="post" action="options.php">
    <?php
    settings_fields('qioooo_settings');
    do_settings_sections('qioooo-settings');
    ?>

    <table class="form-table">
        <tr>
            <th scope="row">
                <label for="qioooo_primary_color"><?php esc_html_e('主题主色', 'qioooo'); ?></label>
            </th>
            <td>
                <input type="color" 
                       id="qioooo_primary_color" 
                       name="qioooo_options[primary_color]" 
                       value="<?php echo esc_attr($options['primary_color'] ?? '#0073aa'); ?>" />
                <p class="description">
                    <?php esc_html_e('选择主题的主要颜色。', 'qioooo'); ?>
                </p>
            </td>
        </tr>

        <tr>
            <th scope="row">
                <label for="qioooo_layout"><?php esc_html_e('页面布局', 'qioooo'); ?></label>
            </th>
            <td>
                <select id="qioooo_layout" name="qioooo_options[layout]">
                    <option value="right-sidebar" <?php selected($options['layout'] ?? '', 'right-sidebar'); ?>>
                        <?php esc_html_e('右侧边栏', 'qioooo'); ?>
                    </option>
                    <option value="left-sidebar" <?php selected($options['layout'] ?? '', 'left-sidebar'); ?>>
                        <?php esc_html_e('左侧边栏', 'qioooo'); ?>
                    </option>
                    <option value="no-sidebar" <?php selected($options['layout'] ?? '', 'no-sidebar'); ?>>
                        <?php esc_html_e('无边栏', 'qioooo'); ?>
                    </option>
                </select>
                <p class="description">
                    <?php esc_html_e('选择网站的布局方式。', 'qioooo'); ?>
                </p>
            </td>
        </tr>

        <tr>
            <th scope="row">
                <label for="qioooo_header_style"><?php esc_html_e('头部样式', 'qioooo'); ?></label>
            </th>
            <td>
                <select id="qioooo_header_style" name="qioooo_options[header_style]">
                    <option value="default" <?php selected($options['header_style'] ?? '', 'default'); ?>>
                        <?php esc_html_e('默认', 'qioooo'); ?>
                    </option>
                    <option value="centered" <?php selected($options['header_style'] ?? '', 'centered'); ?>>
                        <?php esc_html_e('居中', 'qioooo'); ?>
                    </option>
                    <option value="minimal" <?php selected($options['header_style'] ?? '', 'minimal'); ?>>
                        <?php esc_html_e('简约', 'qioooo'); ?>
                    </option>
                </select>
                <p class="description">
                    <?php esc_html_e('选择网站的头部样式。', 'qioooo'); ?>
                </p>
            </td>
        </tr>

        <tr>
            <th scope="row">
                <label for="qioooo_footer_style"><?php esc_html_e('底部样式', 'qioooo'); ?></label>
            </th>
            <td>
                <select id="qioooo_footer_style" name="qioooo_options[footer_style]">
                    <option value="default" <?php selected($options['footer_style'] ?? '', 'default'); ?>>
                        <?php esc_html_e('默认', 'qioooo'); ?>
                    </option>
                    <option value="minimal" <?php selected($options['footer_style'] ?? '', 'minimal'); ?>>
                        <?php esc_html_e('简约', 'qioooo'); ?>
                    </option>
                    <option value="widgets" <?php selected($options['footer_style'] ?? '', 'widgets'); ?>>
                        <?php esc_html_e('小工具', 'qioooo'); ?>
                    </option>
                </select>
                <p class="description">
                    <?php esc_html_e('选择网站的底部样式。', 'qioooo'); ?>
                </p>
            </td>
        </tr>

        <tr>
            <th scope="row">
                <label for="qioooo_custom_css"><?php esc_html_e('自定义 CSS', 'qioooo'); ?></label>
            </th>
            <td>
                <textarea id="qioooo_custom_css" 
                          name="qioooo_options[custom_css]" 
                          rows="10" 
                          class="large-text code"><?php echo esc_textarea($options['custom_css'] ?? ''); ?></textarea>
                <p class="description">
                    <?php esc_html_e('添加自定义 CSS 代码。', 'qioooo'); ?>
                </p>
            </td>
        </tr>
    </table>

    <?php submit_button(); ?>
</form> 