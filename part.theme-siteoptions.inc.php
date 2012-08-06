<?php 
//Get options using this example: get_option(SM_SITEOP_PREFIX.'my_field')
global $jquery_version;
function wlfw_get_jquery_version() {
	global $wp_scripts, $jquery_version;
	$registered_scripts = $wp_scripts->registered;
	$jquery_version = $registered_scripts['jquery']->ver;
	
	// shwo warning if you will be changing versions of jQuery
	if( !stristr( get_option(SM_SITEOP_PREFIX.'jquery_source'), $jquery_version  )) {
		global $errors;
		$errors->add('Warning', __(sprintf('You are currently using an older version of jQuery. Setting an option in the script sources tab will cause your site to start loading the most recent version ('.$jquery_version.')'), THEME_PREFIX));
	}
	
	// create admin page
$whiteLabelOptions = new sm_options_page(array('theme_page' => TRUE, 'parent_id' => 'themes.php', 'page_title' => 'Configure Theme Customizations', 'menu_title' => 'Theme Options','id' => 'whitelabel-appearance-options'));
	$whiteLabelOptions->add_part($pageMeta = new sm_section('page_meta', array('title'=>'Page Meta')) );
		$pageMeta->add_part($genTag = new sm_checkbox('page_meta_generator', array('label'=>'Include Generator Meta Tag', 'value'=>'true', 'classes'=>array('onOffSwitch') )));
		
	$whiteLabelOptions->add_part($urls = new sm_section('urls', array('title'=>'URLs')) );
		$urls->add_part($caseSensative = new sm_checkbox('case_insensitive_urls', array('label'=>'Make URLS Case-Insensitive', 'value'=>'true', 'classes'=>array('onOffSwitch'))));
		$urls->add_part($disable_404_permalink_guessing = new sm_checkbox('disable_404_permalink_guessing', array('label'=>'Disable 404 permalink guessing', 'value'=>'true', 'description'=>'When you turn this option on WordPress will no longer try to redirect pages that don\'t exist to pages that do exists using its "best guess" algorithm.', 'classes'=>array('onOffSwitch'))));
				
	$whiteLabelOptions->add_part($texturize = new sm_section('texturize', array('title'=>'Texturize Input')) );
		$texturize->add_part($texturize_title = new sm_checkbox('texturize_title', array('label'=>'Title', 'value'=>'true', 'classes'=>array('onOffSwitch'))));
		$texturize->add_part($texturize_content = new sm_checkbox('texturize_content', array('label'=>'Content', 'value'=>'true', 'classes'=>array('onOffSwitch'))));
		$texturize->add_part($texturize_exercpt = new sm_checkbox('texturize_excerpt', array('label'=>'Excerpt', 'value'=>'true', 'classes'=>array('onOffSwitch'))));
		$texturize->add_part($texturize_comments = new sm_checkbox('texturize_comments', array('label'=>'Comments', 'value'=>'true', 'classes'=>array('onOffSwitch'))));
				
	$whiteLabelOptions->add_part($scriptSrources = new sm_section('script_sources', array('title'=>'Script Sources')) );
		$scriptSrources->add_part($jQuery = new sm_radio_buttons('jquery_source', array('Default (local but can be overridden by plugins)'=>'default', 'Local (cannot be overridden by plugins)'=>'local', 'Google Ajax API CDN'=>'http://ajax.googleapis.com/ajax/libs/jquery/'.$jquery_version.'/jquery.min.js', 'Microsoft CDN'=>'http://ajax.aspnetcdn.com/ajax/jQuery/jquery-'.$jquery_version.'.min.js', 'jQuery CDN'=>'http://code.jquery.com/jquery-'.$jquery_version.'.min.js' )));
			$jQuery->label ='jQuery ('.$jquery_version.')';
			$jQuery->default_value = 'default';
			
	$whiteLabelOptions->add_part($fourOFourPage = new sm_section('404', array('title'=>'404 Page')) ); 
		$fourOFourPage->add_part($autoSearch = new sm_checkbox('autosearch', array('label'=>'Auto Search', 'value'=>'true', 'classes'=>array('onOffSwitch'))));
		$fourOFourPage->add_part($defaultHTML = new sm_textfield('autosearch_default_content_id', array('label'=>'Page ID of default content', 'description'=>'Dispalyed when auto search is turned off ')));
		$fourOFourPage->add_part($maxResults = new sm_textfield('autosearch_max_results', array('label'=>'Max number of results returned')));
		$fourOFourPage->add_part($resultsPerPage = new sm_textfield('autosearch_per_page', array('label'=>'Number of results per page')));
		$fourOFourPage->add_part($paginate = new sm_checkbox('autosearch_paginate', array('label'=>'Paginate Results', 'value'=>'true', 'classes'=>array('onOffSwitch'))));
	
	$whiteLabelOptions->add_part($social = new sm_section('social', array('title'=>'Social Media')) );
		$social->add_part($floating_social = new sm_checkbox('floating_social', array('label'=>'Enable Floating Social Bar', 'value'=>'true', 'classes'=>array('onOffSwitch'))));
		$social->add_part($floating_social_embed = new sm_checkbox('floating_social_embed', array('label'=>'Auto-Embed Floating Social Bar', 'value'=>'true', 'classes'=>array('onOffSwitch'))));
		$social->add_part($share_this_key = new sm_textfield('share_this_key', array('label'=>'ShareThis Publisher Key', 'description'=>'Get your key here <a href="http://sharethis.com/" target="_blank">sharethis.com</a>' )));
	
	$whiteLabelOptions->add_part($mobile = new sm_section('mobile', array('title'=>'Mobile Redirection')) );
		$mobile->add_part($mobile_redirect = new sm_textfield('redirect_mobile_devices', array('label'=>'URL of Mobile Website', 'description'=>'Enter the URL of your mobile website to automatically redirect mobile users to the URL. Leave blank to turn off mobile redirection.')));
		//dynamic description
		$mobile_redirect_disabler_value = get_option(SM_SITEOP_PREFIX.'mobile_redirect_disabler');
		if(!empty($mobile_redirect_disabler_value))
			$mobile_redirect_disabler_desc = 'Add the name of the parameter and then use this link to allow mobile visitors to view the "desktop website": <a href="http://'.$_SERVER['HTTP_HOST'].'/?'.$mobile_redirect_disabler_value.'=true">http://'.$_SERVER['HTTP_HOST'].'/?'.$mobile_redirect_disabler_value.'=true</a>';
		else $mobile_redirect_disabler_desc = 'Recommended that you use <strong>viewFullSite</strong>. This is usefull when you want to offer a link on the mobile site to "view the full desktop site". After you save a paramter name in this field, this discription will give you an example link for use with your site.';
		$mobile->add_part($mobile_redirect_disabler = new sm_textfield('mobile_redirect_disabler', array('label'=>'Redrection Disabled URL Parameter', 'description'=>$mobile_redirect_disabler_desc)));
	
	$whiteLabelOptions->add_part($layout = new sm_section('layout', array('title'=>'Layout')) );
		$layout->add_part($grid_system = new sm_radio_buttons('grid_system', array( '960.gs <a href="http://960.gs/" target="_blank">Documentation</a>'=>'960gs', '960.gs Responsive <a href="http://csswizardry.com/inuitcss/#grid-builder" target="_blank">Documentation</a>'=>'inuit' )));
		$grid_system->label ='Grid System';
		$grid_system->default_value = '960gs';
		$grid_system->description = 'Grid Systems streamline web development work flow by providing a structural framework of commonly used dimensions. Whitelable Framework provides you with 2 grid options. 960.gs, a static width 16 column grid and a modified version of Inuit, a fluid 16 column grid. This allows you to make your website responsive by simply selecting the 960.gs Responsive option above.';
		
		$layout->add_part($sidebar_position = new sm_radio_buttons('sidebar_position', array( 'Left'=>'left', 'Right'=>'right' )));
		$sidebar_position->label ='Sidebar Position';
		$sidebar_position->default_value = 'left';
		
//build the options menu!
$whiteLabelOptions->build();
}
add_action('init', 'wlfw_get_jquery_version');




// remove page genereated meta tag if option turned on in site options
if(get_option(SM_SITEOP_PREFIX.'page_meta_generator') != 'true' ) {
	remove_action('wp_head', 'wp_generator');
}

//fix case sensative url issue
if(get_option(SM_SITEOP_PREFIX.'insensitive_urls') == 'true' ) {
	add_action('init', 'desensitize_url');
}
function desensitize_url() {
    if (preg_match('/[A-Z]/', $_SERVER['REQUEST_URI'])) {
        $_SERVER['REQUEST_URI'] = strtolower($_SERVER['REQUEST_URI']);
        $_SERVER['PATH_INFO']   = strtolower($_SERVER['PATH_INFO']);
    }
}

// modify texturize settings
// prevents transformations of quotes to smart quotes, apostrophes, dashes, ellipses, the trademark symbol, and the multiplication symbol. 
function sm_modify_texturize_options() { 
	if (get_option(SM_SITEOP_PREFIX.'sm_texturize_title') == 'false') { remove_filter('the_title', 'wptexturize'); }
	if (get_option(SM_SITEOP_PREFIX.'sm_texturize_content') == 'false') { remove_filter('the_content', 'wptexturize'); }
	if (get_option(SM_SITEOP_PREFIX.'sm_texturize_excerpt') == 'false') { remove_filter('the_excerpt', 'wptexturize'); }
	if (get_option(SM_SITEOP_PREFIX.'sm_texturize_comments') == 'false') { remove_filter('comment_text', 'wptexturize'); }
}
add_action('init', 'sm_modify_texturize_options');