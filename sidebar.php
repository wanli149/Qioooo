<?php
/**
 * 侧边栏模板
 *
 * @package QIoooo
 */

if (!is_active_sidebar('sidebar-1')) {
    return;
}
?>

<aside id="secondary" class="widget-area">
    <div class="sidebar-container">
        <?php dynamic_sidebar('sidebar-1'); ?>
    </div>
</aside><!-- #secondary --> 