jQuery(document).ready(function($) {
	/* prepend menu icon */
	jQuery('.menu-header').prepend('<div id="menu-icon">Navigation</div>');
	
	/* add class to indicate js is enabled */
	jQuery('#nav_below_header').addClass('js_on');
	
	/* toggle nav */
	jQuery("#menu-icon").on("click", function(){
		jQuery("#nav_below_header .menu").slideToggle();
		jQuery(this).toggleClass("active");
	});
});