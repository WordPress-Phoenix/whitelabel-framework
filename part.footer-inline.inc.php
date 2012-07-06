<div id="bottomWrapper">
    <div id="footer" class="container_16">
    	<div class="grid_16">
        	<div class="grey-frame">
				<div class="grid_6"><?php bloginfo('name'); ?><br  />Copyright &copy; <?php echo date('Y'); ?></div>
                <div class="grid_6 alignright">
				<?php
				function wlfw_footer_gpl() { echo '<p>GPL-V2 License - WordPress.org Friendly</p>'; }
				wp_nav_menu( array( 'container_class' => 'menu-header', 'theme_location' => 'footer_nav',  'fallback_cb' => 'wlfw_footer_gpl' ) );
				?>
				<p><?php echo get_bloginfo ( 'description' ); ?></p>
				</div>
                <div class="clear"></div>
            </div>
        </div>
    </div><!--/footer -->
    <div class="clear"></div>
</div><!--/bottomWrapper -->
<div id="printFooter" class="container_16 print">
    Print Footer. Copyright &copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>
</div><!--/printFooter -->