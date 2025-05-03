<?php
/**
 * 功能设置页面模板
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
                <?php esc_html_e('启用功能', 'qioooo'); ?>
            </th>
            <td>
                <fieldset>
                    <legend class="screen-reader-text">
                        <span><?php esc_html_e('启用功能', 'qioooo'); ?></span>
                    </legend>
                    <label for="qioooo_enable_seo">
                        <input type="checkbox" 
                               id="qioooo_enable_seo" 
                               name="qioooo_options[enable_seo]" 
                               value="1" 
                               <?php checked($options['enable_seo'] ?? '', '1'); ?> />
                        <?php esc_html_e('SEO 优化', 'qioooo'); ?>
                    </label>
                    <br />
                    <label for="qioooo_enable_social">
                        <input type="checkbox" 
                               id="qioooo_enable_social" 
                               name="qioooo_options[enable_social]" 
                               value="1" 
                               <?php checked($options['enable_social'] ?? '', '1'); ?> />
                        <?php esc_html_e('社交媒体分享', 'qioooo'); ?>
                    </label>
                    <br />
                    <label for="qioooo_enable_related_posts">
                        <input type="checkbox" 
                               id="qioooo_enable_related_posts" 
                               name="qioooo_options[enable_related_posts]" 
                               value="1" 
                               <?php checked($options['enable_related_posts'] ?? '', '1'); ?> />
                        <?php esc_html_e('相关文章', 'qioooo'); ?>
                    </label>
                </fieldset>
            </td>
        </tr>

        <tr>
            <th scope="row">
                <label for="qioooo_social_links"><?php esc_html_e('社交链接', 'qioooo'); ?></label>
            </th>
            <td>
                <div class="social-links-container">
                    <?php
                    $social_platforms = array(
                        'weibo' => __('微博', 'qioooo'),
                        'wechat' => __('微信', 'qioooo'),
                        'qq' => __('QQ', 'qioooo'),
                        'github' => __('GitHub', 'qioooo')
                    );

                    foreach ($social_platforms as $platform => $label) {
                        $url = $options['social_links'][$platform] ?? '';
                        ?>
                        <div class="social-link-field">
                            <label for="qioooo_social_<?php echo esc_attr($platform); ?>">
                                <?php echo esc_html($label); ?>
                            </label>
                            <input type="url" 
                                   id="qioooo_social_<?php echo esc_attr($platform); ?>" 
                                   name="qioooo_options[social_links][<?php echo esc_attr($platform); ?>]" 
                                   value="<?php echo esc_url($url); ?>" 
                                   class="regular-text" />
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <p class="description">
                    <?php esc_html_e('输入您的社交媒体链接。', 'qioooo'); ?>
                </p>
            </td>
        </tr>

        <tr>
            <th scope="row">
                <label for="qioooo_analytics_code"><?php esc_html_e('统计代码', 'qioooo'); ?></label>
            </th>
            <td>
                <textarea id="qioooo_analytics_code" 
                          name="qioooo_options[analytics_code]" 
                          rows="5" 
                          class="large-text code"><?php echo esc_textarea($options['analytics_code'] ?? ''); ?></textarea>
                <p class="description">
                    <?php esc_html_e('添加网站统计代码（如 Google Analytics）。', 'qioooo'); ?>
                </p>
            </td>
        </tr>

        <tr>
            <th scope="row">
                <label for="qioooo_custom_scripts"><?php esc_html_e('自定义脚本', 'qioooo'); ?></label>
            </th>
            <td>
                <textarea id="qioooo_custom_scripts" 
                          name="qioooo_options[custom_scripts]" 
                          rows="5" 
                          class="large-text code"><?php echo esc_textarea($options['custom_scripts'] ?? ''); ?></textarea>
                <p class="description">
                    <?php esc_html_e('添加自定义 JavaScript 代码。', 'qioooo'); ?>
                </p>
            </td>
        </tr>
    </table>

    <?php submit_button(); ?>
</form>