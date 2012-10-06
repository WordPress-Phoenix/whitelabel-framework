<?php 
//Get options using this example: get_option(SM_SITEOP_PREFIX.'my_field')
//setup some variables used in the logic
$websiteLogoArgs = array('label'=>'Website Logo');
if((get_option('show_on_front')) != 'posts') {
	$websiteLogoArgs['description'] = __('<a href="options-reading.php">Static homepage</a> is currently setup on your website. For a unique homepage logo use a featured image on <a href="post.php?post='.get_option('page_on_front').'&action=edit">this page.</a>', 'sm');
}
else {
	$websiteLogoArgs['description'] = __('<a href="options-reading.php">Static homepage</a> must be set for a unique homepage logo to be set.', 'sm');
}
$websiteLogoArgs['description'] = $websiteLogoArgs['description'].' '.__('Logo size should be 36px tall by 150px-200px wide.', 'sm');

// create admin page
$mobops = new sm_options_page(array('parent_id' => 'themes.php', 'page_title' => 'Configure Mobile Theme Customizations', 'menu_title' => 'Mobile Options','id' => 'mobile-appearance-options'));
	$mobops->add_part($mobops_brand = new sm_section('branding_options', array('title'=>'Brand It')) );
		$mobops_brand->add_part($site_favicon = new sm_media_upload('website_favicon', array('label'=>'Favicon', 'description'=>'Website icon to be used for your website. Must be 16x16 or 32x32 and .ico format. Leaving this field blank will load the favicon.ico file from the themes folder or fallback to the generic favicon.ico file.')));
		$mobops_brand->add_part($site_logo = new sm_media_upload('website_logo', $websiteLogoArgs));
		$mobops_brand->add_part($site_primary_color = new sm_color_picker('site_primary_color', array('label'=>'Primary Color')));
		$mobops_brand->add_part($site_secondary_color = new sm_color_picker('site_secondary_color', array('label'=>'Secondary Color')));
		$mobops_brand->add_part($site_cta_color = new sm_color_picker('site_cta_color', array('label'=>'Call To Action Color')));
	$mobops->add_part($mobops_company = new sm_section('company_info', array('title'=>'Company Info')) );
		$mobops_company->add_part($company_phone = new sm_textfield('company_phone', array('label'=>'Phone Number', 'description'=>'Enables click to call mobile buttons.')));
		$mobops_company->add_part($company_form = new sm_textfield('company_form', array('label'=>'Contact Form URL', 'description'=>'Enables request information button.')));
		$mobops_company->add_part($company_twitter = new sm_textfield('company_twitter', array('label'=>'Twitter ID', 'description'=>'Example: for facebook.com/google enter "google."')));
		$mobops_company->add_part($company_facebook = new sm_textfield('company_facebook', array('label'=>'Facebook Page', 'description'=>'Example: for twitter.com/google enter "google".')));
	$mobops->add_part($mobops_utilities = new sm_section('utilities', array('title'=>'Utilities')) );
		$mobops_utilities->add_part($fullsite_url = new sm_textfield('fullsite_url', array('label'=>'Full Site URL', 'description'=>'The url to the full version of your website')));
		$mobops_utilities->add_part($fullsite_query = new sm_textfield('fullsite_query', array('label'=>'Full Site Query', 'description'=>'The url parameters like ?view=fullsite')));
		$mobops_utilities->add_part($vcard_author = new sm_checkbox('vcard_author', array('label'=>'vCard Author Pages','description'=>'Turn on vCard author pages when you want author contact details to be easily shared just like a digital business card.', 'value'=>'true', 'classes'=>array('onOffSwitch'))));
		$mobops_utilities->add_part($auto_redirect = new sm_checkbox('auto_redirect', array('label'=>'404 Auto Redirect','description'=>'Automatically redirect vistors to page on full site if it exists', 'value'=>'true', 'classes'=>array('onOffSwitch'))));
	$mobops->add_part($mobops_404 = new sm_section('404', array('title'=>'404 Page')) );
		$mobops_404->add_part($autoSearch = new sm_checkbox('autosearch', array('label'=>'Auto Search', 'value'=>'true', 'classes'=>array('onOffSwitch'))));
		$mobops_404->add_part($defaultHTML = new sm_textfield('autosearch_default_content_id', array('label'=>'Page ID of default content', 'description'=>'Dispalyed when auto search is turned off ')));
		$mobops_404->add_part($maxResults = new sm_textfield('autosearch_max_results', array('label'=>'Max number of results returned')));
		$mobops_404->add_part($resultsPerPage = new sm_textfield('autosearch_per_page', array('label'=>'Number of results per page')));
		$mobops_404->add_part($paginate = new sm_checkbox('autosearch_paginate', array('label'=>'Paginate Results', 'value'=>'true', 'classes'=>array('onOffSwitch'))));
		
$mobops->build();

?>