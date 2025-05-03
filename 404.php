<?php
/**
 * 404页面模板
 *
 * @package QIoooo
 */

get_header();
?>

    <main id="primary" class="site-main">
        <div class="container">
            <section class="error-404 not-found">
                <header class="page-header">
                    <h1 class="page-title"><?php esc_html_e('页面未找到', 'qioooo'); ?></h1>
                </header><!-- .page-header -->

                <div class="page-content">
                    <p><?php esc_html_e('抱歉，您访问的页面不存在。请尝试搜索或返回首页。', 'qioooo'); ?></p>

                    <?php get_search_form(); ?>

                    <div class="error-404-links">
                        <a href="<?php echo esc_url(home_url('/')); ?>" class="button">
                            <?php esc_html_e('返回首页', 'qioooo'); ?>
                        </a>
                    </div>
                </div><!-- .page-content -->
            </section><!-- .error-404 -->
        </div><!-- .container -->
    </main><!-- #primary -->

<?php
get_footer(); 