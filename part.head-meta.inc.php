<title><?php wp_title(''); ?></title>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<?php if( $grid_system = get_option(SM_SITEOP_PREFIX.'grid_system') && get_option(SM_SITEOP_PREFIX.'grid_system') == 'inuit' )
	echo '<meta content="width=device-width, initial- scale=1.0" name="viewport">';
?>
<?php do_action('append_meta_tags'); ?>
<link href="<?php echo get_stylesheet_directory_uri(); ?>/favicon.ico" rel="shortcut icon" />
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
