<div id="bottomWrapper"  class="<?php echo apply_filters( 'bottom_wrapper_section_class', ''); ?>">
    <div id="footer" class="<?php wlfw_grid_row_class(16); ?> footer-docs" data-theme="c" data-role="footer">
    <?php do_action('wlfw_before_footer_sidebar'); ?>
  	<?php if ( ! dynamic_sidebar( 'footer-sidebar' ) ) : ?>
        	<div class="<?php wlfw_grid_col_class(16); ?> inner">
				<div class="<?php wlfw_grid_col_class(8); ?>"><p><?php bloginfo('name'); ?></p><p>Copyright &copy; <?php echo date('Y'); ?></p></div>
                <div class="<?php wlfw_grid_col_class(8); ?> text-right">
				<?php
				function wlfw_footer_gpl() { echo '<p>GPL-V2 License - WordPress.org Friendly</p>'; }
				wp_nav_menu( array( 'container_class' => 'menu-header', 'theme_location' => 'footer_nav',  'fallback_cb' => 'wlfw_footer_gpl' ) );
				?>
				<p><?php echo get_bloginfo ( 'description' ); ?></p>
				</div>
                <div class="clear"></div>
            </div>
    <?php endif; // logo-aside ?>
    <?php do_action('wlfw_after_footer_sidebar'); ?>
    </div><!--/footer -->
    <div class="clear"></div>
</div><!--/bottomWrapper -->
<div id="printFooter" class="<?php wlfw_grid_row_class(16); ?> print">
    Print Footer. Copyright &copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>
</div><!--/printFooter -->