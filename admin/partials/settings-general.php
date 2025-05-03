<?php
/**
 * 常规设置页面模板
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
                <label for="qioooo_site_title"><?php esc_html_e('网站标题', 'qioooo'); ?></label>
            </th>
            <td>
                <input type="text" 
                       id="qioooo_site_title" 
                       name="qioooo_options[site_title]" 
                       value="<?php echo esc_attr($options['site_title'] ?? ''); ?>" 
                       class="regular-text" />
                <p class="description">
                    <?php esc_html_e('显示在网站顶部的标题。', 'qioooo'); ?>
                </p>
            </td>
        </tr>

        <tr>
            <th scope="row">
                <label for="qioooo_site_description"><?php esc_html_e('网站描述', 'qioooo'); ?></label>
            </th>
            <td>
                <textarea id="qioooo_site_description" 
                          name="qioooo_options[site_description]" 
                          rows="3" 
                          class="large-text"><?php echo esc_textarea($options['site_description'] ?? ''); ?></textarea>
                <p class="description">
                    <?php esc_html_e('网站的简短描述，用于 SEO。', 'qioooo'); ?>
                </p>
            </td>
        </tr>

        <tr>
            <th scope="row">
                <label for="qioooo_logo"><?php esc_html_e('网站 Logo', 'qioooo'); ?></label>
            </th>
            <td>
                <input type="text" 
                       id="qioooo_logo" 
                       name="qioooo_options[logo]" 
                       value="<?php echo esc_attr($options['logo'] ?? ''); ?>" 
                       class="regular-text" />
                <button type="button" 
                        class="button" 
                        id="upload_logo_button">
                    <?php esc_html_e('上传 Logo', 'qioooo'); ?>
                </button>
                <p class="description">
                    <?php esc_html_e('上传网站的 Logo 图片。', 'qioooo'); ?>
                </p>
                <div id="logo_preview" class="image-preview">
                    <?php if (!empty($options['logo'])) : ?>
                        <img src="<?php echo esc_url($options['logo']); ?>" alt="<?php esc_attr_e('Logo 预览', 'qioooo'); ?>" />
                    <?php endif; ?>
                </div>
            </td>
        </tr>

        <tr>
            <th scope="row">
                <label for="qioooo_favicon"><?php esc_html_e('网站图标', 'qioooo'); ?></label>
            </th>
            <td>
                <input type="text" 
                       id="qioooo_favicon" 
                       name="qioooo_options[favicon]" 
                       value="<?php echo esc_attr($options['favicon'] ?? ''); ?>" 
                       class="regular-text" />
                <button type="button" 
                        class="button" 
                        id="upload_favicon_button">
                    <?php esc_html_e('上传图标', 'qioooo'); ?>
                </button>
                <p class="description">
                    <?php esc_html_e('上传网站的 favicon 图标。', 'qioooo'); ?>
                </p>
                <div id="favicon_preview" class="image-preview">
                    <?php if (!empty($options['favicon'])) : ?>
                        <img src="<?php echo esc_url($options['favicon']); ?>" alt="<?php esc_attr_e('图标预览', 'qioooo'); ?>" />
                    <?php endif; ?>
                </div>
            </td>
        </tr>
    </table>

    <?php submit_button(); ?>
</form>