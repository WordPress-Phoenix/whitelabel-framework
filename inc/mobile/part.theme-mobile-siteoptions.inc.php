<?php 
//Get options using this example: get_option(SM_SITEOP_PREFIX.'my_field')

// create admin page
$mobops = new sm_options_page(array('parent_id' => 'themes.php', 'page_title' => 'Configure Mobile Theme Customizations', 'menu_title' => 'Mobile Options','id' => 'mobile-appearance-options'));
	$mobops->add_part($mobops_brand = new sm_section('branding_options', array('title'=>'Brand It')) );
		$mobops_brand->add_part($site_primary_color = new sm_color_picker('site_primary_color', array('label'=>'Primary Color')));
		$mobops_brand->add_part($site_secondary_color = new sm_color_picker('site_secondary_color', array('label'=>'Secondary Color')));
		$mobops_brand->add_part($site_cta_color = new sm_color_picker('site_cta_color', array('label'=>'Call To Action Color')));
	$mobops->add_part($mobops_company = new sm_section('company_info', array('title'=>'Company Info')) );
		$mobops_company->add_part($company_phone = new sm_textfield('company_phone', array('label'=>'Phone Number', 'description'=>'Enables click to call mobile buttons.')));
		$mobops_company->add_part($company_form = new sm_textfield('company_form', array('label'=>'Contact Form URL', 'description'=>'Enables request information button.')));
		$mobops_company->add_part($company_twitter = new sm_textfield('company_twitter', array('label'=>'Twitter ID', 'description'=>'Example:  Enter "google" for facebook.com/google.')));
		$mobops_company->add_part($company_facebook = new sm_textfield('company_facebook', array('label'=>'Facebook Page', 'description'=>'Example: Enter "google" for twitter.com/google.')));
	$mobops->add_part($mobops_utilities = new sm_section('utilities', array('title'=>'Utilities')) );
		$mobops_utilities->add_part($fullsite_url = new sm_textfield('fullsite_url', array('label'=>'Full Site URL', 'description'=>'The url to the full version of your website')));
		$mobops_utilities->add_part($fullsite_query = new sm_textfield('fullsite_query', array('label'=>'Full Site Query', 'description'=>'The url parameters like ?viewFullSite=true')));
		$mobops_utilities->add_part($vcard_author = new sm_checkbox('vcard_author', array('label'=>'vCard Author Pages','description'=>'Turn on vCard author pages when you want author contact details to be easily shared just like a digital business card.', 'value'=>'true', 'classes'=>array('onOffSwitch'))));
		$mobops_utilities->add_part($auto_redirect = new sm_checkbox('auto_redirect', array('label'=>'404 Auto Redirect','description'=>'Automatically redirect vistors to page on full site if it exists', 'value'=>'true', 'classes'=>array('onOffSwitch'))));

		
$mobops->build();

?>