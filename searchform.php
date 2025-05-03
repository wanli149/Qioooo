<?php
/**
 * 搜索表单模板
 *
 * @package QIoooo
 */

?>

<form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
    <label>
        <span class="screen-reader-text"><?php echo _x('搜索:', 'label', 'qioooo'); ?></span>
        <input type="search" class="search-field" placeholder="<?php echo esc_attr_x('搜索...', 'placeholder', 'qioooo'); ?>" value="<?php echo get_search_query(); ?>" name="s" />
    </label>
    <button type="submit" class="search-submit">
        <span class="screen-reader-text"><?php echo _x('搜索', 'submit button', 'qioooo'); ?></span>
        <i class="fas fa-search"></i>
    </button>
</form> 