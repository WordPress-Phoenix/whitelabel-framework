<?php
add_filter( 'page_container_div', function($title) { return '<div id="container-fluid" class="type-interior" data-role="page">'; } );
add_action( 'wlfw_before_logo', function($title) { echo '<a data-icon="back" data-rel="back" data-iconpos="notext">Back</a>'; } );
add_action( 'wlfw_after_logo', function($title) { echo ' <a href="#" id="bookmark" data-icon="star" data-iconpos="notext" class="ui-btn-right">Share</a>'; } );
add_action('after_logo_aside', 'get_template_part', 10, 2);

add_action('wlfw_before_dynamic_sidebar', 'wlfw_display_nav'); 





