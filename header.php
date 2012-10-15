<?php
/*
 * Theme: SM Developers Framework
 * Package: Wordpress 3+
 *
 * BEFORE YOU CHANGE ANYTHING:
 * Please note that you shouldn't need to change anything in this file. To control how the page is built, 
 * please consider using actions like add_action('build_theme_head', 'new_function_name') to control 
 * elements loaded on the page. Alternatively you can also edit files like part.head-meta.inc.php or 
 * better yet, create that file in yoru child theme to overwrite it completely.
*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html <?php language_attributes(); ?>>
<head>
<?php do_action('build_theme_head', 'part.head', 'meta.inc'); ?>
</head>
<body <?php body_class(); ?>>
<?php echo apply_filters('page_container_div', '<div id="container-fluid">'); ?>
<?php do_action('body_enqueue', 'part.body', 'entry.inc'); ?>
<?php 
	do_action('build_theme_header', 'part.header', 'inline.inc'); 
	do_action('build_theme_breadcrumbs', 'part.header', 'breadcrumbs.inc');
