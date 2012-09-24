<?php $header_image = get_header_image(); ?>
<div id="topWrapper">
  <div id="header" class="<?php wlfw_grid_row_class(16); ?>">
    <div class="<?php wlfw_grid_col_class(6); ?> logo"><a href="<?php echo site_url(); ?>"><?php if(empty($header_image)) { bloginfo('name'); } else{ ?><img class="clear" src="<?php echo $header_image; ?>" alt="<?php if(defined('HEADER_IMAGE_ALT')) echo HEADER_IMAGE_ALT; ?>" /><?php } ?></a></div>
    <div class="<?php wlfw_grid_col_class(10); ?> logo-sibling right">
  	<?php if ( ! dynamic_sidebar( 'logo-aside' ) ) : ?>
    <?php endif; // logo-aside ?>
    </div>
    <div class="clear after_logo_aside"></div>
    <div class="<?php wlfw_grid_col_class(16); ?>" id="nav_below_header">
        <?php
			wp_nav_menu( array( 'container_class' => 'menu-header', 'theme_location' => 'primary_nav',  'fallback_cb' => 'wlfw_wp_list_top_pages' ) );
			function wlfw_wp_list_top_pages() {	
				$wlfw_list_pages = wp_list_pages('title_li=&sort_column=menu_order&echo=0'); 
				echo '<div class="menu-header"><ul id="menu-primary" class="menu">'.$wlfw_list_pages.'</ul></div>';
			}
		?>
      <div class="clear after_nav"></div>
    </div>
  </div>
  <div id="printHeader" class="<?php wlfw_grid_row_class(16); ?> print">
    <div>Print White Label Header</div>
    <div class="clear after_print_header_content"></div>
  </div>
  <div class="clear after_print_header"></div>
  
  <div id="breadCrumbs" class="<?php wlfw_grid_row_class(16); ?>">
    <?php if ( function_exists('yoast_breadcrumb') ) {
			$crumbs = yoast_breadcrumb('<div class="'.wlfw_grid_col_class(16,true).'"><span class="crumbsStart">&nbsp;</span>','<span class="crumbsEnd">&nbsp;</span></div>', false);
			echo apply_filters( 'wlfw_breadcrumbs', $crumbs );
		} ?>
    <div class="clear after_bread_crumbs_content"></div>
  </div>
  <div class="clear after_bread_crumbs"></div>
</div>
<!-- topWrapper -->