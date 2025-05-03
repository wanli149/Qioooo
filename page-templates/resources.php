<?php
/**
 * 资源页面模板
 *
 * @package QIoooo
 */

get_header();
?>

    <main id="primary" class="site-main">
        <div class="container">
            <div class="resources-page">
                <header class="page-header">
                    <h1 class="page-title"><?php the_title(); ?></h1>
                </header>

                <div class="resources-navigation">
                    <ul class="resources-nav">
                        <li class="active"><a href="#all">全部</a></li>
                        <li><a href="#tutorials">教程</a></li>
                        <li><a href="#templates">模板</a></li>
                        <li><a href="#plugins">插件</a></li>
                    </ul>
                </div>

                <div class="resources-filters">
                    <div class="sort-options">
                        <select id="resource-sort">
                            <option value="latest">最新</option>
                            <option value="popular">最受欢迎</option>
                            <option value="rating">评分最高</option>
                        </select>
                    </div>
                    <div class="format-tags">
                        <span class="tag active">全部</span>
                        <span class="tag">PDF</span>
                        <span class="tag">Word</span>
                        <span class="tag">Excel</span>
                        <span class="tag">PPT</span>
                    </div>
                </div>

                <div class="resources-grid">
                    <?php
                    // 获取资源文章
                    $resources_query = new WP_Query(array(
                        'post_type' => 'resource',
                        'posts_per_page' => 9,
                        'orderby' => 'date',
                        'order' => 'DESC'
                    ));

                    if ($resources_query->have_posts()) :
                        while ($resources_query->have_posts()) : $resources_query->the_post();
                            ?>
                            <div class="resource-card">
                                <div class="resource-thumbnail">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <?php the_post_thumbnail('medium'); ?>
                                    <?php endif; ?>
                                </div>
                                <div class="resource-content">
                                    <h3 class="resource-title">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h3>
                                    <div class="resource-meta">
                                        <span class="resource-date"><?php echo get_the_date(); ?></span>
                                        <span class="resource-downloads"><?php echo get_post_meta(get_the_ID(), 'download_count', true); ?> 次下载</span>
                                    </div>
                                    <div class="resource-rating">
                                        <?php
                                        $rating = get_post_meta(get_the_ID(), 'rating', true);
                                        for ($i = 1; $i <= 5; $i++) {
                                            echo '<span class="star ' . ($i <= $rating ? 'filled' : '') . '"></span>';
                                        }
                                        ?>
                                    </div>
                                    <div class="resource-actions">
                                        <a href="<?php echo get_post_meta(get_the_ID(), 'download_url', true); ?>" class="download-btn">下载</a>
                                        <a href="#" class="preview-btn" data-resource-id="<?php the_ID(); ?>">预览</a>
                                    </div>
                                </div>
                            </div>
                        <?php
                        endwhile;
                        wp_reset_postdata();
                    else :
                        echo '<p>暂无资源</p>';
                    endif;
                    ?>
                </div>

                <div class="load-more">
                    <button id="load-more-resources" class="btn">加载更多</button>
                </div>
            </div>
        </div>
    </main>

<?php
get_footer(); 