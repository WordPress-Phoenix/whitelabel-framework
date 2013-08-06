jQuery(document).ready(function($) {
	/* prepend menu icon */
	jQuery('.menu-header').prepend('<div id="menu-icon">Navigation</div>');
	
	/* add class to indicate js is enabled */
	jQuery('#nav_below_header').addClass('js_on');
	
	/* Add Handles to nav items with submenus */
	jQuery('#nav_below_header ul.menu > li:has(ul)').each(function(index) {
		jQuery(this).children('a').after('<a href="#" class="handle"></a>');
	});
	
	jQuery("#menu-icon").on("click", function(e){
		e.preventDefault();
		if(jQuery(this).next('ul').hasClass('visible')) {
			jQuery(this).next('ul').removeClass('visible').slideUp(300, function() {
			 	jQuery(this).css('display', '');
			});
			jQuery(this).removeClass('active');
		}
		else {
	  		jQuery(this).next('ul').addClass('visible').slideDown(300);
			jQuery(this).addClass('active');
		}
	});
	
	jQuery("a.handle").on("click", function(e){
		e.preventDefault();
		if(jQuery(this).next('ul').hasClass('visible')) {
			jQuery(this).next('ul').removeClass('visible').slideUp(300, function() {
			 	jQuery(this).css('display', '');
			});
			jQuery(this).removeClass('active');
		}
		else {
	  		jQuery(this).next('ul').addClass('visible').slideDown(300);
			jQuery(this).addClass('active');
		}
	});
});
