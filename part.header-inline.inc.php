<?php $header_image = get_header_image(); ?>
<div id="topWrapper">
  <div id="header" class="container_16">
    <div class="grid_6"><a href="/" class="logo"><?php if(empty($header_image)) { ?> White Label WordPress<?php } else{ ?><img class="clear" src="<?php echo $header_image; ?>" alt="<?php if(defined('HEADER_IMAGE_ALT')) echo HEADER_IMAGE_ALT; ?>" /><?php } ?></a></div>
    <div class="grid_10 logo-sibling right">
      <div class="white"><img class="right clear" src="http://s.wordpress.org/about/images/wordpressicon-hanttula3.jpg" />
        <div class="clear"></div>
      </div>
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