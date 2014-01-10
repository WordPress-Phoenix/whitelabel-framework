<div class="<?php wlfw_grid_col_class(16); ?>" id="nav_below_header">
    <?php
    // only show first level of nav items on mobile layout
    $depth = '0';
    if( get_option(SM_SITEOP_PREFIX.'grid_system') == 'mobile' )
        $depth = '1';

    $primary_menu = wp_nav_menu( array( 'container_class' => 'menu-header', 'theme_location' => 'primary_nav', 'depth'=>$depth, 'echo'=>false, 'items_wrap' => '<ul id="%1$s" class="%2$s" data-dividertheme="d" data-theme="c" data-role="listview">%3$s</ul>', 'fallback_cb' => 'wlfw_wp_list_top_pages' ) );

    $primary_menu = apply_filters('wlfw_primary_nav_output', $primary_menu);
    echo '<div data-role="collapsible" data-collapsed="false" data-theme="b" data-content-theme="d">'.PHP_EOL.$primary_menu.PHP_EOL.'</div>'.PHP_EOL;
    ?>
    <div class="clear after_nav"></div>
</div>