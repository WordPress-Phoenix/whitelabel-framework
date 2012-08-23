<?php 
// Library: Floating social for WordPress
// Features: shortcode, widget, auto embed, auto embed per page, and php template inline
// Example: for php template inline the following function must be called before get_header()
// add_floating_social(array('position' => 'the_content'));

add_shortcode( 'floating_social', 'add_floating_social' );
global $old_ie;
if(preg_match('/(?i)msie [1-8]/',$_SERVER['HTTP_USER_AGENT'])) $old_ie = true;	

function add_floating_social($args = array()) {
	global $floating_social_flag, $post_id, $post_meta;
	wp_enqueue_script('share-this', 'http://w.sharethis.com/button/buttons.js', array(), false, true);
	//check for positioning (allow for inline social bars)
	
	$floating_social_auto = get_option(SM_SITEOP_PREFIX.'floating_social_embed');
	$post_meta = get_post_custom($post_id);
	
	if( isset($args['position']) && $args['position'] == 'the_content' ) {
		$floating_social_flag='the_content';
		wlfw_remove_widget_by_base_id();
	}
	elseif( !isset($floating_social_flag) && wlfw_post_has_shortcode('floating_social') ) {
		$floating_social_flag='inline';
		wlfw_remove_widget_by_base_id();
	}
	elseif( !isset($floating_social_flag) && is_active_widget( false, NULL, 'wlfw_floating_social' ))
		$floating_social_flag='widget';
	
	if(isset($args['position']) && $args['position'] ==  'the_content'){ add_filter('the_content','wlfw_inline_social_bar');}
	elseif( isset($args['position']) && ( $args['position'] ==  'inline' && isset($floating_social_flag) && $floating_social_flag=='inline' ) ) { wlfw_inline_social_bar();  }
	elseif( isset($args['position']) && ( $args['position'] ==  'widget'  && isset($floating_social_flag) && $floating_social_flag=='widget' ) ) { wlfw_inline_social_bar(); }
	elseif( !is_active_widget( 'wlfw_floating_social', NULL, 'wlfw_floating_social' )  && !isset($floating_social_flag) ) { 
		if(!$floating_social_auto && (isset($post_meta['_wlfw_floating_social']) && $post_meta['_wlfw_floating_social'][0] == 'enabled'))
			add_action('build_theme_footer', 'wlfw_deterimine_floating_social_bar', 5); 
		elseif($floating_social_auto && (!isset($post_meta['_wlfw_floating_social'])) )
			add_action('build_theme_footer', 'wlfw_deterimine_floating_social_bar', 5); 
	}
}


function wlfw_inline_social_bar($content = '', $media_query=false) { wlfw_display_like_it_bar(); global $old_ie;?>
<style>
#floatingbuttonsWrapper.container_16, .container_16 #floatingbuttons.grid_16 { width:auto; max-width:940px; }
.container_16 #floatingbuttons.grid_16 { margin:0; }
<?php if($media_query && !$old_ie) echo '@media screen and (max-width: 1100px) {';?>
	#floatingbuttonsWrapper, .admin-bar.page #floatingbuttonsWrapper {
		float: left;
		position: relative;
		top: 0px;
		margin-bottom: 20px;
	}
	#middleContainer #floatingbuttons { margin-left:0; width:100% }
	#floatingbuttons .floatbutton {margin:10px 23px 10px 24px; clear:none;}
	#floatingbuttons {border-radius:5px;}
<?php if($media_query && !$old_ie) echo '}';?>
</style>
<?php 
	return $content;
}



function wlfw_display_like_it_bar() { 
?>
<style>

/*styles for wlfw_display_like_it_bar */
#container-fluid {width: 100%;}
#floatingbuttonsWrapper {  
	position:fixed;
	top:200px;
	left:0;
	z-index:1; 	
	background:#F9F9F9;
	background:-webkit-gradient(linear, left top, left bottom, color-stop(0, #fff), color-stop(1, #F9F9F9));
	background:-moz-linear-gradient(top, #fff, #F9F9F9);
}
/*styles for floating social bar (verticle only) */
#floatingbuttons{
	float: right;
	position: relative;	
	border:1px solid #ccc;
	padding:0 0 3px 0;
	box-shadow:2px 2px 5px rgba(0,0,0,0.3);
	border-radius:0 5px 5px 0; 
	right: auto; 
	min-width: 74px;
}



#floatingbuttons .floatbutton {margin:5px 4px 0 4px; clear:both; min-height:66px; background:url(<?php echo get_template_directory_uri(); ?>/appearance/images/ajax-loader-floating-social.gif) no-repeat 25px 10px;}


.floatbuttonEndCap { display: none; }
.st_plusone_vcount { right: 0px; }
#floatingbuttons, #floatingbuttons .floatbutton, .floatbuttonSeperator {float:left; }
#middleWrapper #container{background: white; position: relative; z-index: 2;}
.addbuttons{text-align:center;padding-top:5px;} .addbuttons a span.getfloat, .addbuttons a span.sharebuttons{color:#000;background:none;font-family:arial, sans-serif;display:block;font-size:9px;font-weight:bold;text-decoration:none;line-height:11px;} .addbuttons a:hover span{color:#000;background:none;text-decoration:underline;}
.floatbuttonEndCap {clear:both; margin-bottom: 19px; height: 1px; width: 100%;}

#floatingbuttons .stButton .stBubble {
    line-height: 10px;
	height:38px;
	background:none;
}
body .floatbutton .stBubble_count, body .floatbutton .stBubble_count {
  background:none;
  background-color: #fff;
  font-size: 15px;
  color: #666666;
  font-family: arial,sans-serif; 
  filter:none;
  border:none;
}
.boxplusone .countboxborder {
    background: url(<?php echo get_template_directory_uri();?>/appearance/images/floating-social-bubble.png) no-repeat;
    height: 40px;
    position: absolute;
    width: 63px;
    z-index: 20;
}
#floatingbuttons .boxplusone {
    position: relative;
	margin-top: 10px;
    min-width: 58px;
} 
.st_plusone_vcount  { 
	right:-3px;
	position: relative;
    top: -3px;
}
.st_plusone_vcount>div { width:58px!important; }
#floatingbuttons .stButton .stBubble { margin:2px 0; }
#floatingbuttons .chicklets.reddit, .inline_sharing .chicklets.reddit, #floatingbuttons .chicklets.stumbleupon, .inline_sharing .chicklets.stumbleupon {
    background: url(<?php echo get_template_directory_uri();?>/appearance/images/floating-social-sharing.png) no-repeat scroll 0 -57px ;
    display: block;
	text-indent: -99999px;
}
#floatingbuttons .chicklets.stumbleupon, .inline_sharing .chicklets.stumbleupon {
	background-position: 0 -86px;
}

</style>
<noscript><style>#floatingbuttons{display: none;}</style></noscript>

<div id="floatingbuttonsWrapper" class="<?php wlfw_grid_row_class(16); ?>">
    <div id='floatingbuttons' title="Share this post!" class="<?php wlfw_grid_col_class(16); ?>">   
    <span class='floatbutton st_facebook_vcount' displayText='Facebook'></span>
    <span class='floatbutton st_twitter_vcount' displayText='Tweet'></span>
    <span class='floatbutton st_plusone_vcount' displayText='Google'></span>
    <span class='floatbutton st_stumbleupon_vcount' displayText='Stumble'></span>
    <span class='floatbutton st_reddit_vcount' displayText='Reddit'></span>
    <span class='floatbutton st_pinterest_vcount' displayText='Pinterest'></span>
    </div>
</div>
<div class="floatbuttonEndCap"></div>

<?php
$st_key =  get_option(SM_SITEOP_PREFIX.'share_this_key');
if( !empty($st_key) ) { echo '<script type="text/javascript">jQuery(document).ready(function() { stLight.options({publisher: "'.$st_key.'"}); }); </script>'; }
?>

<!-- end wlfw_display_like_it_bar --> 
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#floatingbuttons span.floatbutton').each(function(){
		jQuery(this).wrap('<div class="floatbutton boxplusone '+jQuery(this).attr('class').replace("st_","")+'" id="'+jQuery(this).attr('class').replace("floatbutton st_","")+'" />').before('<div class="countboxborder"></div>').removeClass('floatbutton');
		});	
	jQuery('#floatingbuttonsWrapper').hover(
	  function () {
		jQuery(this).css('z-index', '3');
	  }, 
	  function () {
	   jQuery(this).css('z-index', '1');
	  }
	);
});
</script>
<?php }

function wlfw_deterimine_floating_social_bar() { wlfw_inline_social_bar('', true); global $old_ie; ?>
<style>

<?php 
	if(!$old_ie) echo '@media screen and (max-width: 1100px) {'; ?>
	#floatingbuttonsWrapper { float:none!important; background:none; }
	#floatingbuttons { background: -moz-linear-gradient(center top , #FFFFFF, #F9F9F9) repeat scroll 0 0 transparent; }
	#floatingbuttonsWrapper.container_16, .container_16 #floatingbuttons.grid_16 { width: inherit; }
	
	.container_16 #floatingbuttons.grid_16 { margin-bottom:20px; }
<?php if(!$old_ie) echo '}'; ?>
</style>

<?php }



function display_floating_social() {
	global $post;
	$floating_social_auto = get_option(SM_SITEOP_PREFIX.'floating_social_embed');
	$post_meta = get_post_custom($post->ID);
	//dbug('$floating_social_auto: '.$floating_social_auto);
	//dbug('_wlfw_floating_social: '.$post_meta['_wlfw_floating_social'][0]);
	
	// if constant defined in page template, do nothing
	if( (defined('FLOATING_SOCIAL_OFF') && FLOATING_SOCIAL_OFF)) return false;
	
	// if floating social auto embed is off and not being enabled on per page basis, do nothing
	if( (!$floating_social_auto && isset($post_meta['_wlfw_floating_social'][0]) && $post_meta['_wlfw_floating_social'][0] != 'enabled' )) return false;
	
	// if floating social auto embed is on but being disabled on per page basis, do nothing
	if( ($floating_social_auto && isset($post_meta['_wlfw_floating_social'][0]) && $post_meta['_wlfw_floating_social'][0] == 'disabled' )) return false;

	if( !has_action('body_enqueue', 'wlfw_display_like_it_bar') && !has_filter('the_content','wlfw_inline_social_bar' ))
		add_action('get_header', 'add_floating_social'); 
}
// if floating social option set to on, add the action
$floating_social = get_option(SM_SITEOP_PREFIX.'floating_social');
if(!$floating_social) {} 
else { 
	add_action('get_header', 'display_floating_social', 20); 
}
 

/* Add Floating Social Options Meta Box  */

// if floating social option set to on, add the action
$floating_social = get_option(SM_SITEOP_PREFIX.'floating_social');
if(!$floating_social) {} 
else { 
	add_action('add_meta_boxes', 'wlfw_floating_social_options', 10);
	add_action('save_post', 'wlfw_floating_social_options_save');
}


function wlfw_floating_social_options(){
	add_meta_box('wlfw_floating_social_options', 'Floating Social Bar Options' , 'wlfw_floating_social_options_form', 'page', 'side', '');
}

function wlfw_floating_social_options_form(){ 
	global $post_id, $post_meta;

	$post_meta = get_post_custom($post_id);
	$floating_social_auto = get_option(SM_SITEOP_PREFIX.'floating_social_embed');
	@dbug('$floating_social_auto: '.$floating_social_auto);
	@dbug('_wlfw_floating_social: '.$post_meta['_wlfw_floating_social'][0]);
	
	if($floating_social_auto)
		echo '<p>Floating Social Auto-Embed is Enabled. <br />Select Disabled to remove it from this page.</p>';
	else
		echo '<p>Floating Social Auto-Embed is Disabled. <br />Select Enabled to add it to this page.</p>';
		
	// Use nonce for verification
	wp_nonce_field( 'wlfw_floating_social', 'wlfw_floating_social_nonce' );
	?>
    
	<select id="wlfw_floating_social" name="wlfw_floating_social" style="margin-bottom:10px;margin-top:5px;width:100%;" >
        <option value="enabled" <?php 
			if( (isset($post_meta['_wlfw_floating_social']) && $post_meta['_wlfw_floating_social'][0] == 'enabled')
			|| ($floating_social_auto && (!isset($post_meta['_wlfw_floating_social']) || $post_meta['_wlfw_floating_social'][0] != 'disabled')) ) { 
				echo ' selected="selected"';
			} 
		?>>Enabled on this page</option>
        <option value="disabled" <?php 
			if( (isset($post_meta['_wlfw_floating_social']) && $post_meta['_wlfw_floating_social'][0] == 'disabled') 
			|| (!$floating_social_auto && (!isset($post_meta['_wlfw_floating_social']) || $post_meta['_wlfw_floating_social'][0] != 'enabled')) ) { 
			echo ' selected="selected"'; } 
		?>>Disabled on this page</option>
    </select>
    
    <?php
}

function wlfw_floating_social_options_save() {
	global $post_id;
	if(!isset($post_id) || empty($post_id)) $post_id = $_POST['post_ID'];
	
	$floating_social_auto = get_option(SM_SITEOP_PREFIX.'floating_social_embed');
		
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return $post_id;
	
	// verify
	if ( !wp_verify_nonce( $_POST['wlfw_floating_social_nonce'], 'wlfw_floating_social' ) ) return;
	if ( !current_user_can( 'edit_page', $post_id ) ) return;
	else if ( !current_user_can( 'edit_post', $post_id ) ) return;
	
	// save floating social options
	foreach($_POST as $key => $value) {
	  	if($key == 'wlfw_floating_social') {
			// if auto embed on only save meta value if its disabled
			if($floating_social_auto) {
				if($value == 'disabled')
					update_post_meta($post_id, '_'.$key, trim($value) );
				else
					delete_post_meta($post_id, '_'.$key);
			}
			// if auto embed off only save meta value if its enabled
			else if(!$floating_social_auto) {
				if($value == 'enabled')
					update_post_meta($post_id, '_'.$key, trim($value) );
				else
					delete_post_meta($post_id, '_'.$key);
			}
		}
	}
	
}
