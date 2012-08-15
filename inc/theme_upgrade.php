<?php
add_action( 'admin_init','wlfw_post_upgrade_functions' );
unlink(dirname(__FILE__).'/theme_upgrade.php');

/* Things to do when WLFW is updated oe activated */
function wlfw_post_upgrade_functions() {
		
	$jquery_source = get_option(SM_SITEOP_PREFIX.'jquery_source');
	$jquery_version = wlfw_get_jquery_version();
	
	// check to see if jQuery option needs updated
	if( stristr($jquery_source, 'http') && !stristr($jquery_source, $jquery_version) ) {
		//Google CDN
		if( stristr($jquery_source, 'ajax.googleapis.com') )
			update_option( SM_SITEOP_PREFIX.'jquery_source', 'http://ajax.googleapis.com/ajax/libs/jquery/'.$jquery_version.'/jquery.min.js' );
		//Microsoft CDN
		elseif( stristr($jquery_source, 'ajax.aspnetcdn.com') )
			update_option( SM_SITEOP_PREFIX.'jquery_source', 'http://ajax.aspnetcdn.com/ajax/jQuery/jquery-'.$jquery_version.'.min.js' );
		//jQuery CDN
		elseif( stristr($jquery_source, 'code.jquery.com') )
			update_option( SM_SITEOP_PREFIX.'jquery_source', 'http://code.jquery.com/jquery-'.$jquery_version.'.min.js' );
	
	}
}