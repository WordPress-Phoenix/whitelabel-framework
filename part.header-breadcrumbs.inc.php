<?php if( !is_page() && !is_home() ) {  ?>
<div id="breadCrumbs" class="<?php wlfw_grid_row_class(16); ?>">
	<?php 
    if ( function_exists('yoast_breadcrumb') ) {
        $crumbs = yoast_breadcrumb('<div class="'.wlfw_grid_col_class(16,true).'"><span class="crumbsStart">&nbsp;</span>','<span class="crumbsEnd">&nbsp;</span></div>', false);
        echo apply_filters( 'wlfw_breadcrumbs', $crumbs );
    } 
    ?>
    <div class="clear after_bread_crumbs_content"></div>
</div>
<div class="clear after_bread_crumbs"></div>
<?php } ?>