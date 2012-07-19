<?php $header_image = get_header_image(); ?>
<div id="topWrapper">
  <div id="header" class="container_16">
    <div class="grid_6 logo"><a href="/"><?php if(empty($header_image)) { bloginfo('name'); } else{ ?><img class="clear" src="<?php echo $header_image; ?>" alt="<?php if(defined('HEADER_IMAGE_ALT')) echo HEADER_IMAGE_ALT; ?>" /><?php } ?></a></div>
    <div class="grid_10 logo-sibling right">
  	<?php if ( ! dynamic_sidebar( 'logo-aside' ) ) : ?>
    <?php endif; // logo-aside ?>
    </div>
    <div class="clear"></div>
    <div class="grid_16" id="nav_below_header">
        <?php
			wp_nav_menu( array( 'container_class' => 'menu-header', 'theme_location' => 'primary_nav',  'fallback_cb' => 'wlfw_wp_list_top_pages' ) );
			function wlfw_wp_list_top_pages() {	
				$wlfw_list_pages = wp_list_pages('title_li=&sort_column=menu_order&echo=0'); 
				echo '<div class="menu-header"><ul id="menu-primary" class="menu">'.$wlfw_list_pages.'</ul></div>';
			}
		?>
      <div class="clear"></div>
    </div>
  </div>
  <div id="printHeader" class="container_16 print">
    <div>Print White Label Header</div>
    <div class="clear"></div>
  </div>
  <div class="clear"></div>
</div>
<!-- topWrapper -->