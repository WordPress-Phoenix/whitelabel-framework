<?php
/**
 * The Sidebar containing the primary and secondary widget areas.
 */
?>

<div id="contentNarrow" class="content-secondary">
  <?php
	/* When we call the dynamic_sidebar() function, it'll spit out
	 * the widgets for that widget area. If it instead returns false,
	 * then the sidebar simply doesn't exist, so we'll hard-code in
	 * some default sidebar stuff just in case.
	 */
	do_action('wlfw_before_dynamic_sidebar');
	if ( ! dynamic_sidebar( 'primary-page-widget-area' ) ) :
    	do_action('wlfw_before_empty_dynamic_sidebar', 'primary_nav');
	?>
  
  <?php endif; // end primary widget area ?>
</div>

<?php
function wlfw_default_sidebar() {
	?>
    <div class="widget-container widget_wlfw_menu" id="wlfw_menu">
    	<h3 class="widget-title">Learn More</h3>
    	<?php if(function_exists('get_wlfw_subpages_html')) echo get_wlfw_subpages_html(); ?>
    	<div class="clear"></div>
    </div>
	

  <div id="text-2" class="sidebox widget-container widget_text">
    <h5 class="widget-title">Good to Remember</h5>
    <div class="textwidget">
      <p>Welcome. Its time to take a closer look at the website. Its very important to customize your website with content thats relavent to target audience.</p>
      <p>It would be a good idea to check out the WordPress admin and add some "widgets" to your sidebar. You can find these widgets in the "Appearance" menu.</p>
    </div>
  </div>
<?php } ?>