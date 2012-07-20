<?php
/*
Current Theme Addon: Theme Updater for Whitelabel Framework on GitHub
Original Plugin Name: Theme Updater
Original Plugin URI: https://github.com/UCF/Theme-Updater
Description: A theme updater for GitHub hosted Wordpress themes.  This Wordpress plugin automatically checks GitHub for theme updates and enables automatic install.  For more information read <a href="https://github.com/UCF/Theme-Updater/blob/master/readme.markdown">plugin documentation</a>.
Original Author: Douglas Beck
Original Version: 1.3.4
Modified: 7/12/2012
*/

//MODIFIED
require_once('updater-assets.php');

add_filter('site_transient_update_themes', 'transient_update_themes_filter');
function transient_update_themes_filter($data){
	
	if( function_exists('wp_get_theme') )
		$theme_data = wp_get_theme();
	else
		$theme_data = get_theme_data( get_stylesheet_directory() . '/style.css' );
	
	$theme_key = $theme_data->template;
	if(!empty($theme_key))  $theme_data = wp_get_theme($theme_key);
	$theme = $theme_data;
	$github_username = 'scarstens';
	$github_repo = 'whitelabel-framework';
	$github_theme_uri = 'https://github.com/scarstens/whitelabel-framework.git';
	$github_api_repo_uri =  'https://api.github.com/repos/'.$github_username.'/'.$github_repo;
		
	// Add Github Theme Updater to return $data and hook into admin
	remove_action( "after_theme_row_" . $theme_key, 'wp_theme_update_row');
	add_action( "after_theme_row_" . $theme_key, 'github_theme_update_row', 11, 2 );

	$url = $github_api_repo_uri.'/tags';
	$raw_response = wp_remote_get($url, array('sslverify' => false, 'timeout' => 10));
	if ( is_wp_error( $raw_response ) ){
		$data->response[$theme_key]['error'] = "Error response from " . $url;
		return $data;
	}
	$response = json_decode($raw_response['body']);

	if(isset($response->error)){
		if(is_array($response->error)){
			$errors = '';
			foreach ( $response->error as $error) {
				$errors .= ' ' . $error;
			}
		} else {
			$errors = print_r($response->error, true);
		}
		$data->response[$theme_key]['error'] = sprintf('While <a href="%s">fetching tags</a> api error</a>: <span class="error">%s</span>', $url, $errors);
		var_export($data); exit;
	}
	
	if(!isset($response) || count($response) < 1){
		$data->response[$theme_key]['error'] = "Github theme does not have any tags";
		var_export($data); exit;
	}
	
	//set cache, just 60 seconds
	set_transient(md5($url), $response, 30);

	// Sort and get latest tag
	$tags = array();
	foreach($response as $num => $tag)
		$tags[] = $tag->name;
	usort($tags, "version_compare");
	
	
	// check for rollback
	if(isset($_GET['rollback'])){
		$data->response[$theme_key]['package'] = 
			$github_theme_uri . '/zipball/' . urlencode($_GET['rollback']);
	}
	
	
	// check and generate download link
	$newest_tag = array_pop($tags);
	if(version_compare($theme->version,  $newest_tag, '>=')){
		// up-to-date!
		$data->up_to_date[$theme_key]['rollback'] = $tags;
		return $data;
	}
	
	
	// new update available, add to $data
	$download_link = $github_api_repo_uri . '/zipball/' . $newest_tag;
	$update = array();
	$update['new_version'] = $newest_tag;
	$update['url']         = $github_theme_uri;
	$update['package']     = $download_link;
	$data->response[$theme_key] = $update;

	return $data;
}


add_filter('upgrader_source_selection', 'upgrader_source_selection_filter', 10, 3);
function upgrader_source_selection_filter($source, $remote_source=NULL, $upgrader=NULL){
	/*
		Github delivers zip files as <Username>-<TagName>-<Hash>.zip
		must rename this zip file to the accurate theme folder
	*/
	if( isset($_GET['action']) && stristr($_GET['action'], 'theme') ){
		$upgrader->skin->feedback("Trying to customize theme folder name...");
		if( isset($source, $remote_source) && stristr($source, 'whitelabel-framework') ){
			$corrected_source = $remote_source . '/whitelabel-framework/';
			if(@rename($source, $corrected_source)){
				$upgrader->skin->feedback("Theme folder name corrected to: whitelabel-framework");
				return $corrected_source;
			} else {
				$upgrader->skin->feedback("Unable to rename downloaded theme.");
				return new WP_Error();
			}
		}
	}
	return $source;
}

/*
   Function to address the issue that users in a standalone WordPress installation
   were receiving SSL errors and were unable to install themes.
   https://github.com/UCF/Theme-Updater/issues/3
*/
add_action('http_request_args', 'no_ssl_http_request_args', 10, 2);
function no_ssl_http_request_args($args, $url) {
	$args['sslverify'] = false;
	return $args;
}
