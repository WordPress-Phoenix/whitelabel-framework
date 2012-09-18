<?php
/*
Current Theme Addon: Theme Updater for Whitelabel Framework on GitHub
Original Plugin Name: Theme Updater
Original Plugin URI: https://github.com/UCF/Theme-Updater
Description: A theme updater for GitHub hosted Wordpress themes.  This Wordpress plugin automatically checks GitHub for theme updates and enables automatic install.  For more information read <a href="https://github.com/UCF/Theme-Updater/blob/master/readme.markdown">plugin documentation</a>.
Original Author: Douglas Beck
Original Version: 1.3.4
First Modified: 7/12/2012
*/

global $WLFW_UPDATE_DATA;
if(!empty($_GET['action']) && ($_GET['action'] == 'do-core-reinstall' || $_GET['action'] == 'do-core-upgrade')); else {
	if(!function_exists('github_theme_update_row'))require_once('updater-assets.php');
	add_filter('site_transient_update_themes', 'wlfw_transient_update_themes_filter');
}

add_filter('theme_action_links', 'wlfw_append_theme_actions', 10, 2);
function wlfw_append_theme_actions($actions, $theme = NULL) {
	if($theme->template != 'whitelabel-framework') return $actions;
	
		if ( eregi("MSIE", getenv( "HTTP_USER_AGENT" ) ) || eregi("Internet Explorer", getenv("HTTP_USER_AGENT" ) ) ) {
?>		
	<script src="<?php echo get_template_directory_uri(); ?>/js/buster.js" type="text/javascript"></script>
    <script>jQuery(function($){buster.wait("iframe");});</script>
<?php	
		}
	?>
    <p id="wlfw_extended_options" style="display:none;">
    <?php echo apply_filters('wlfw_append_theme_actions_content', wlfw_append_theme_actions_content()); ?>
	</p>
	<script type="text/javascript">
	jQuery(document).ready(function($) {
		jQuery('#wlfw_extended_options').siblings('.action-links').find('p').replaceWith(jQuery('#wlfw_extended_options'));
		jQuery('#wlfw_extended_options').show();
	});
    </script>
	<?php
	return $actions;	
}

function wlfw_append_theme_actions_content(){
	ob_start();
?>
<strong>There is a new version of Whitelabel Framework available. <a href="<?php echo get_template_directory_uri().'/inc/localize-remote-content.php'; ?>?remote=<?php echo urlencode( 'http://github.com/WordPress-Phoenix/whitelabel-framework/issues?milestone=&page=1&state=closed'); ?>&TB_iframe=true&amp;width=80%&amp;height=506" class="thickbox" title="Whitelabel Framework">View version 1.4 details</a> or <a href="update.php?action=upgrade-theme&amp;theme=whitelabel-framework&amp;_wpnonce=f6e9acc04a" onclick="if ( confirm('Updating this theme will lose any customizations you have made. \'Cancel\' to stop, \'OK\' to update.') ) {return true;}return false;">update now</a>.</strong>
<?php
	return trim(ob_end_flush(), '1');
}

function wlfw_transient_update_themes_filter($data){

	global $WLFW_UPDATE_DATA;
	if(!empty($WLFW_UPDATE_DATA)) return $WLFW_UPDATE_DATA;
	
	if( function_exists('wp_get_theme') )
		$theme_data = wp_get_theme();
	else
		$theme_data = get_theme_data( get_stylesheet_directory() . '/style.css' );
	
	$theme_key = $theme_data->template;
	if(!empty($theme_key))  $theme_data = wp_get_theme($theme_key);
	$theme = $theme_data;
	$github_username = 'WordPress-Phoenix';
	$github_repo = 'whitelabel-framework';
	$github_theme_uri = 'https://github.com/'.$github_username.'/'.$github_repo.'.git';
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
		return $data;
	}

	if( !isset($response) || count($response) < 1 || (!empty($response->message) && $response->message == 'Not Found') ){
		$data->response[$theme_key]['error'] = "Github theme does not have any tags";
		return $data;
	}
	
	//set cache, just 60 seconds
	set_transient(md5($url), $response, 300);

	// Sort and get latest tag
	$tags = array();
	foreach($response as $num => $tag) {
		if(isset($tag->name)) $tags[] = $tag->name;
	}
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
	
	$WLFW_UPDATE_DATA = $data;
	return $WLFW_UPDATE_DATA;
}

/*	Github delivers zip files as <Username>-<TagName>-<Hash>.zip
 *	must rename this zip file to the accurate theme folder
 */
add_filter('upgrader_source_selection', 'wlfw_upgrader_source_selection_filter', 10, 3);
function wlfw_upgrader_source_selection_filter($source, $remote_source=NULL, $upgrader=NULL){
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

/* https://github.com/UCF/Theme-Updater/issues/3 */
add_action('http_request_args', 'wlfw_no_ssl_http_request_args', 10, 2);
function wlfw_no_ssl_http_request_args($args, $url) {
	$args['sslverify'] = false;
	return $args;
}