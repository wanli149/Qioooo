<?php
/**
 * 导航模板部件
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('qioooo_the_posts_navigation')) :
    /**
     * 文章导航
     */
    function qioooo_the_posts_navigation() {
        the_posts_pagination(array(
            'prev_text' => '<i class="fas fa-chevron-left"></i>',
            'next_text' => '<i class="fas fa-chevron-right"></i>',
            'screen_reader_text' => __('分页导航', 'qioooo'),
        ));
    }
endif;

if (!function_exists('qioooo_the_post_navigation')) :
    /**
     * 单篇文章导航
     */
    function qioooo_the_post_navigation() {
        the_post_navigation(array(
            'prev_text' => '<span class="nav-subtitle">' . esc_html__('上一篇:', 'qioooo') . '</span> <span class="nav-title">%title</span>',
            'next_text' => '<span class="nav-subtitle">' . esc_html__('下一篇:', 'qioooo') . '</span> <span class="nav-title">%title</span>',
        ));
    }
endif;

if (!function_exists('qioooo_the_comments_navigation')) :
    /**
     * 评论导航
     */
    function qioooo_the_comments_navigation() {
        the_comments_pagination(array(
            'prev_text' => '<i class="fas fa-chevron-left"></i>',
            'next_text' => '<i class="fas fa-chevron-right"></i>',
            'screen_reader_text' => __('评论分页', 'qioooo'),
        ));
    }
endif;

if (!function_exists('qioooo_the_posts_pagination')) :
    /**
     * 文章分页
     */
    function qioooo_the_posts_pagination() {
        the_posts_pagination(array(
            'mid_size'  => 2,
            'prev_text' => '<i class="fas fa-chevron-left"></i>',
            'next_text' => '<i class="fas fa-chevron-right"></i>',
            'screen_reader_text' => __('分页导航', 'qioooo'),
        ));
    }
endif; 