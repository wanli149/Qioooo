<?php
/**
 * 主题底部模板
 *
 * @package QIoooo
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

    <footer id="colophon" class="site-footer">
        <div class="site-info">
            <?php
            if (has_nav_menu('footer')) :
                wp_nav_menu(array(
                    'theme_location' => 'footer',
                    'menu_id'       => 'footer-menu',
                    'container'     => false,
                ));
            endif;
            ?>
            <div class="copyright">
                <?php
                printf(
                    esc_html__('© %1$s %2$s. 保留所有权利。', 'qioooo'),
                    date('Y'),
                    get_bloginfo('name')
                );
                ?>
            </div>
        </div>
    </footer>
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html> 