<?php
/**
 * 搜索页面模板
 *
 * @package QIoooo
 */

get_header();
?>

<div class="search-page">
    <div class="container">
        <div class="search-header">
            <h1 class="page-title">
                <?php
                printf(
                    /* translators: %s: 搜索关键词 */
                    esc_html__('搜索结果：%s', 'qioooo'),
                    '<span class="search-query">' . get_search_query() . '</span>'
                );
                ?>
            </h1>
            
            <div class="search-filters">
                <form class="search-form" action="<?php echo esc_url(home_url('/')); ?>" method="get">
                    <input type="text" name="s" value="<?php echo get_search_query(); ?>" placeholder="<?php esc_attr_e('输入关键词搜索...', 'qioooo'); ?>" required>
                    <select name="post_type">
                        <option value=""><?php _e('全部内容', 'qioooo'); ?></option>
                        <option value="post" <?php selected(get_query_var('post_type'), 'post'); ?>><?php _e('文章', 'qioooo'); ?></option>
                        <option value="novel" <?php selected(get_query_var('post_type'), 'novel'); ?>><?php _e('小说', 'qioooo'); ?></option>
                        <option value="resource" <?php selected(get_query_var('post_type'), 'resource'); ?>><?php _e('资源', 'qioooo'); ?></option>
                    </select>
                    <button type="submit"><?php _e('搜索', 'qioooo'); ?></button>
                </form>
            </div>
        </div>
        
        <div class="search-results">
            <?php
            if (have_posts()) :
                while (have_posts()) :
                    the_post();
                    ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('search-result-item'); ?>>
                        <header class="entry-header">
                            <h2 class="entry-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h2>
                            
                            <div class="entry-meta">
                                <span class="post-type">
                                    <?php
                                    $post_type = get_post_type();
                                    switch ($post_type) {
                                        case 'novel':
                                            _e('小说', 'qioooo');
                                            break;
                                        case 'resource':
                                            _e('资源', 'qioooo');
                                            break;
                                        default:
                                            _e('文章', 'qioooo');
                                    }
                                    ?>
                                </span>
                                <span class="post-date"><?php echo get_the_date(); ?></span>
                                <span class="post-author"><?php the_author(); ?></span>
                            </div>
                        </header>
                        
                        <div class="entry-summary">
                            <?php the_excerpt(); ?>
                        </div>
                        
                        <footer class="entry-footer">
                            <?php
                            $categories = get_the_category();
                            if ($categories) :
                                ?>
                                <div class="entry-categories">
                                    <span class="categories-label"><?php _e('分类：', 'qioooo'); ?></span>
                                    <?php
                                    foreach ($categories as $category) {
                                        echo '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . esc_html($category->name) . '</a>';
                                    }
                                    ?>
                                </div>
                                <?php
                            endif;
                            
                            $tags = get_the_tags();
                            if ($tags) :
                                ?>
                                <div class="entry-tags">
                                    <span class="tags-label"><?php _e('标签：', 'qioooo'); ?></span>
                                    <?php
                                    foreach ($tags as $tag) {
                                        echo '<a href="' . esc_url(get_tag_link($tag->term_id)) . '">' . esc_html($tag->name) . '</a>';
                                    }
                                    ?>
                                </div>
                                <?php
                            endif;
                            ?>
                        </footer>
                    </article>
                    <?php
                endwhile;
                
                the_posts_pagination(array(
                    'prev_text' => __('&laquo; 上一页', 'qioooo'),
                    'next_text' => __('下一页 &raquo;', 'qioooo'),
                    'screen_reader_text' => __('分页导航', 'qioooo')
                ));
            else :
                ?>
                <div class="no-results">
                    <p><?php _e('抱歉，没有找到与您的搜索相匹配的内容。', 'qioooo'); ?></p>
                    <p><?php _e('请尝试使用其他关键词搜索。', 'qioooo'); ?></p>
                </div>
                <?php
            endif;
            ?>
        </div>
    </div>
</div>

<?php
get_footer(); 