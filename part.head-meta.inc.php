<title><?php wp_title(''); ?></title>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<?php do_action('append_meta_tags'); ?>
<?php 
$favicon_url = apply_filters('favicon', get_stylesheet_directory_uri().'/favicon.ico' );
if ( file_exists(get_stylesheet_directory().'/favicon.ico') || $favicon_url != get_stylesheet_directory_uri().'/favicon.ico' ) { 
?>
	<link href="<?php echo $favicon_url;?>" rel="shortcut icon" />
<?php } ?>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
