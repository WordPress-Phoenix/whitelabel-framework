<div id="bottomWrapper">
    <div id="footer" class="<?php wlfw_grid_row_class(16); ?>">
  	<?php if ( ! dynamic_sidebar( 'footer-sidebar' ) ) : ?>
        	<div class="<?php wlfw_grid_col_class(16); ?> inner">
				<div class="<?php wlfw_grid_col_class(8); ?>"><?php bloginfo('name'); ?><br  />Copyright &copy; <?php echo date('Y'); ?></div>
                <div class="<?php wlfw_grid_col_class(8); ?>">
				<?php
				function wlfw_footer_gpl() { echo '<p>GPL-V2 License - WordPress.org Friendly</p>'; }
				wp_nav_menu( array( 'container_class' => 'menu-header', 'theme_location' => 'footer_nav',  'fallback_cb' => 'wlfw_footer_gpl' ) );
				?>
				<p><?php echo get_bloginfo ( 'description' ); ?></p>
				</div>
                <div class="clear"></div>
            </div>
    <?php endif; // logo-aside ?>
    </div><!--/footer -->
    <div class="clear"></div>
</div><!--/bottomWrapper -->
<div id="printFooter" class="<?php wlfw_grid_row_class(16); ?> print">
    Print Footer. Copyright &copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>
</div><!--/printFooter -->