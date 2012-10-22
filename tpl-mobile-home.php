<?php
/*
Template Name: Mobile Front Page
*/
//LOADING header.php + actions

$desc = get_bloginfo('description');
if( $homepage = intval(get_option('page_on_front')) ) {
	$args['post_type'] = array('post', 'page');
	$args['p'] = $homepage;
	query_posts($args);
}
else $homepage = false;
remove_action('build_theme_header','get_template_part');
get_header(); 
?>
<div data-role="page" class="type-home">
    <div data-role="header">
        <?php do_action('wlfw_mobile_frontpage_sticky_buttons', 'part.header', 'mobile-nav-sticky-buttons.inc'); ?> 
    </div>
	<div data-role="content">
		<div class="content-secondary">
			<div id="jqm-homeheader">
				<h1 id="jqm-logo"><?php if(has_post_thumbnail()) the_post_thumbnail(); 
				elseif(get_option(SM_SITEOP_PREFIX.'website_logo')) 
					echo $nav_logo = '<img src="'.get_option(SM_SITEOP_PREFIX.'website_logo').'" alt="'.get_bloginfo('name').'" />';
				else echo $nav_logo = get_bloginfo('name'); ?></h1>
				<?php if($desc && !empty($desc)) { echo '<p class="desc">'.$desc.'</p>'; } ?>
			</div>
			<?php if($homepage) while ( have_posts() ) : the_post(); echo '<p class="intro">'.get_the_content().'</p>'; break; endwhile; ?>
			<ul data-role="listview" data-inset="true" data-theme="c" data-dividertheme="f">
				<li data-role="list-divider" class="start"><?php echo sm_get_nav_menu_title('primary', array('default_title'=>'Browse Pages')); ?>
                </li>
				<?php 
					if(has_nav_menu('primary')) { wp_nav_menu(array('theme_location' => 'primary', 'container' => '', 'depth'=>'1', 'items_wrap' => '%3$s')); }
					else { wp_list_pages('depth=1&sort_column=menu_order&depth=1&title_li=');} 
				?>
			</ul>
		</div><!--/content-primary-->
		<div class="content-primary">
        	<?php $menu_html = wp_nav_menu(array('theme_location' => 'secondary', 'depth'=>'1', 'container' => '', 'items_wrap' => '%3$s', 'echo' => false)); ?>
            <?php if(empty($menu_html)) : ?>
			<?php elseif(has_nav_menu('secondary')) : ?>
            <nav>
            	<div id="secondaryLinks" data-role="collapsible" data-collapsed="true" data-theme="b" data-content-theme="d">
                <h3><?php echo sm_get_nav_menu_title('secondary', array('default_title'=>'Blog')); ?></h3>
                    <ul data-role="listview" data-theme="c" data-dividertheme="b">
                        <?php echo $menu_html; ?>
                    </ul>
              </div> 
			</nav>
            <?php else : ?>
            <nav>
            	<div id="secondaryLinks" data-role="collapsible" data-collapsed="true" data-theme="b" data-content-theme="d">
                <h3><?php _e('Blog', 'sm_mobile'); ?></h3>
                    <ul data-role="listview" data-theme="c" data-dividertheme="b">
                        <li><a href="/?s=">Blog Archives</a></li>
                        <li><a href="/?cat=1"><?php _e('Uncategorized Articles', 'sm_mobile'); ?></a></li>
                    </ul>
              </div> 
			</nav>
            <?php endif; ?>
            
            <?php $menu_html = wp_nav_menu(array('theme_location' => 'more', 'container' => '', 'depth'=>'1', 'items_wrap' => '%3$s', 'echo' => false)); ?>
            <?php if(empty($menu_html)) : ?>
            <?php elseif(has_nav_menu('more')) : ?>
			<nav>
            	<div id="moreLinks" data-role="collapsible" data-collapsed="true" data-theme="b" data-content-theme="d">
                <h3><?php echo sm_get_nav_menu_title('more', array('default_title'=>'More')); ?></h3>
                    <ul data-role="listview" data-theme="c" data-dividertheme="b">
                        <?php wp_nav_menu( array( 'theme_location' => 'more', 'container' => '', 'depth'=>'1', 'items_wrap' => '%3$s') ); ?>
                    </ul>
              </div> 
			</nav>
            <?php else : ?>
			<nav>
            	<div id="moreLinks" data-role="collapsible" data-collapsed="true" data-theme="b" data-content-theme="d">
                <h3><?php _e('More', 'sm_mobile'); ?></h3>
                    <ul data-role="listview" data-theme="c" data-dividertheme="b">
                        <li><a href="/<?php echo date('Y'); ?>/"><?php echo date('Y').' '.__('Articles','sm_mobile'); ?></a></li>
                        <li><a href="/feed/">RSS Feed</a></li>
                    </ul>
              </div> 
			</nav>
			<?php endif; ?>
		</div>
	</div>
	<?php get_footer(); ?>	
</div>
</body>
</html>