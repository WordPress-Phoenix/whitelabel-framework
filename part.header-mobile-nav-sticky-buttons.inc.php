<?php

// Get the nav menu based on $menu_name (same as 'theme_location' or 'menu' arg to wp_nav_menu)

// This code based on wp_nav_menu's code to get Menu ID from menu slug



$data_theme = 'e';

$menu_name = 'topbar';



if ( ( $locations = get_nav_menu_locations() ) && isset( $locations[ $menu_name ] ) && $locations[ $menu_name ] !=0 ) :

	// menu object

	$menu = wp_get_nav_menu_object( $locations[ $menu_name ] );

	

	//menu items

	$menu_items = wp_get_nav_menu_items($menu->term_id);

	

	$menu_html = '<div id="stickyButtons" data-role="navbar">';

	$menu_html .= '<ul>';



	//loop through items and spit out links

	foreach ( (array)$menu_items as $key => $menu_item ) {

		$menu_html .= '<li><a href="' . $menu_item->url . '" data-theme="'.$data_theme.'">' . $menu_item->title . '</a></li>';

	}

	

	$menu_html .= '</ul>';

	$menu_html .= '</div>';

	

	echo $menu_html;

else : ?>

<!--Request Info button -->

	<div id="stickyButtons" data-role="navbar">

		<ul class="wrap">

			<?php if(($company_form = get_option(SM_SITEOP_PREFIX.'company_form')) && !empty($company_form)) : ?>

            <li><a href="<?php echo $company_form; ?>" data-theme="e" data-role="button" data-icon="reqInfo">Request<br/>Information</a></li>

            <?php endif; ?>

            <?php if(($company_phone = get_option(SM_SITEOP_PREFIX.'company_phone')) && !empty($company_phone)) : ?>

			<li><a  data-theme="e" data-role="button" data-icon="callUs" href="tel:<?php echo str_replace('-','',$company_phone );?>">Click to call<br/><?php echo $company_phone ?></a></li>

            <?php endif; ?>

		</ul>

	</div><!-- /navbar -->

<!-- End Request Info button -->

<?php endif; ?>

<div data-role="navbar" id="menubox" style="display:none;">

                <ul>

                    <li class="collect"><a id="bookmarkit" title="Bookmark"><img src="<?php bloginfo('template_directory'); ?>/appearance/images/share/bookmark.png" alt="bookmark" width="35" height="35" /></a></li><!-- bookmark collect /-->

                    <li class="rss"><a href="<?php bloginfo('rss2_url');?>" title="RSS"><img src="<?php bloginfo('template_directory'); ?>/appearance/images/share/Rss.png" alt="rss" width="35" height="35" /></a></li><!-- bookmark rss /-->

                    <li class="email"><a href="mailto:<?php echo bloginfo('admin_email'); ?>" title="Email"><img src="<?php bloginfo('template_directory'); ?>/appearance/images/share/email.png" alt="email" width="35" height="35" /></a></li><!-- bookmark email /-->

                    <li class="email"><a href="http://www.facebook.com/<?php echo $fbLink;?>" title="facebook"><img src="<?php bloginfo('template_directory'); ?>/appearance/images/share/facebook.png" alt="facebook" width="35" height="35" /></a></li>

                    <li class="email"><a href="http://twitter.com/<?php echo $twitterLink;?>" title="twitter"><img src="<?php bloginfo('template_directory'); ?>/appearance/images/share/twitter.png" alt="twitter" width="35" height="35" /></a></li>

                </ul>

            </div><!-- /navbar -->