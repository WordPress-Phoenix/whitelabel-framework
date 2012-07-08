jQuery(document).ready(function($) {
  	jQuery('.menu-header .menu > li').live('mouseover', function() {
		jQuery(this).children('.sub-menu').show();
	});
	jQuery('.menu-header .menu > li').live('mouseout', function() {
		jQuery(this).children('.sub-menu').hide();
	});
});