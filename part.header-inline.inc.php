<?php $header_image = get_header_image(); ?>
<div id="topWrapper" class="<?php echo apply_filters( 'top_wrapper_section_class', ''); ?>">
    <div id="header" class="<?php wlfw_grid_row_class(16); ?>" data-role="header" data-theme="b">
		<?php do_action('wlfw_before_logo'); ?>
        <div class="<?php wlfw_grid_col_class(6); ?> logo"><a href="<?php echo site_url(); ?>"><?php if(empty($header_image)) { bloginfo('name'); } else{ ?><img class="clear" src="<?php echo $header_image; ?>" alt="<?php if(defined('HEADER_IMAGE_ALT')) echo HEADER_IMAGE_ALT; ?>" /><?php } ?></a></div>
        <?php do_action('wlfw_after_logo'); ?>
        <div class="<?php wlfw_grid_col_class(10); ?> logo-sibling right">
            <?php if ( ! dynamic_sidebar( 'logo-aside' ) ) : ?>
            <?php endif; // logo-aside ?>
        </div>
        <div class="clear after_logo_aside"></div>
        <?php do_action('after_logo_aside', 'inc/mobile/part.header', 'mobile-nav-sticky-buttons.inc'); ?>
        <div class="<?php wlfw_grid_col_class(16); ?>" id="nav_below_header">
            <?php do_action('wlfw_display_nav'); ?>
            <div class="clear after_nav"></div>
        </div>
    </div>
  
    <div id="printHeader" class="<?php wlfw_grid_row_class(16); ?> print">
    	<div>Print White Label Header</div>
    	<div class="clear after_print_header_content"></div>
    </div>
    <div class="clear after_print_header"></div>
</div><!-- topWrapper -->