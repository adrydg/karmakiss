<footer id="footer" role="contentinfo">
	
	<div class="call-to-action <?php echo is_front_page() ? 'orange' : 'green'; ?>">
	<div class="row">
		<div class="small-12 columns">
			<h1><?php the_field( 'footer-call-to-action', 'options' ); ?></h1>
			<a href="<?php the_field( 'footer-call-to-action-link', 'options' ); ?>" class="button large"><?php the_field( 'footer-call-to-action-button', 'options' ); ?></a>	
		</div>
	</div>
	</div> <!-- end .call-to-action -->

	<?php if ( is_front_page() ) : ?>
	<div class="call-to-action image">
	<div class="row">
		<div class="small-12 medium-9 large-10 columns">
		<h2><?php the_field( 'footer-call-to-action-2', 'options' ); ?></h2>
		</div>
		<div class="small-12 medium-3 large-2 columns">
		<a href="<?php the_field( 'footer-call-to-action-2-link', 'options' ); ?>" class="button large expand"><?php the_field( 'footer-call-to-action-2-button', 'options' ); ?></a>	
		</div>
	</div>
	</div> <!-- end .call-to-action -->

	<div id="footer-brands">
	<div class="row">
		<div class="large-12 columns">
			<ul data-orbit data-options="slide_number:false; timer:false; bullets:false;">
				<?php $brand_no = 1; ?>
				<?php while ( have_rows( 'footer-brands', 'options' ) ): the_row(); ?>
				<?php if ( $brand_no % 7 == 1 ) : ?>
				<li>
				<?php endif; ?>	
				<a href="<?php the_sub_field( 'footer-brand-link' ); ?>" target="_blank"><img src="<?php the_sub_field( 'footer-brand' ); ?>" alt="footer-brand" /></a>
				<?php if ( $brand_no % 7 == 0 ) : ?>
				</li>
				<?php endif; ?>
				<?php $brand_no++; endwhile; ?>
			</ul>
		</div>
	</div>
	</div> <!-- end #footer-brands -->

	<div id="footer-social">
	<div class="row">
		<div class="small-12 large-7 large-centered columns">
			<h1><?php the_field( 'footer-social', 'options' ); ?></h1>
			<a class="facebook" href="<?php the_field( 'facebook-link', 'options' ); ?>" target="_blank"></a>
			<a class="twitter" href="<?php the_field( 'twitter-link', 'options' ); ?>" target="_blank"></a>
			<a class="path" href="<?php the_field( 'path-link', 'options' ); ?>" target="_blank"></a>
			<a class="youtube" href="<?php the_field( 'youtube-link', 'options' ); ?>" target="_blank"></a>
			<a class="google" href="<?php the_field( 'google-link', 'options' ); ?>" target="_blank"></a>
			<a class="rss" href="<?php the_field( 'rss-link', 'options' ); ?>" target="_blank"></a>
		</div>
	</div>
	</div> <!-- end #footer-social -->
	<?php endif; ?>

	<div id="footer-links">
		<div class="row">
			<div class="small-12 columns">
					<?php wp_nav_menu( array( 'container' => false, 'theme_location' => 'footer', 'items_wrap' => '<ul>%3$s</ul>', 'depth' => '1' ) ); ?>
			</div>
		</div>
	</div> <!-- end #footer-links -->
	
	<div id="footer-info">
	<div class="row">
		<div class="small-12 columns">
			<?php the_field( 'footer-info', 'options' ); ?>
		</div>
	</div>
	</div> <!-- end #footer-info -->
	
	<div id="footer-logos">
	<div class="row">
		<div class="small-12 medium-7 medium-centered columns">
			<ul>
				<?php while ( have_rows( 'footer-logos', 'options' ) ): the_row(); ?>
				<li><a href="<?php the_sub_field( 'footer-logo-link' ); ?>" target="_blank"><img src="<?php the_sub_field( 'footer-logo' ); ?>" alt="" ></a></li>
				<?php endwhile; ?>
			</ul>
		</div>
	</div>
	</div> <!-- end #footer-logos -->
	
	<div id="copyright">
	<div class="row">
		<div class="small-12 columns">		
			<p>&copy; 2004 - <?php echo date('Y'); ?> <?php bloginfo('name'); ?>.</p>
		</div>
	</div>
	</div> <!-- end #copyright -->
	
</footer> <!-- end #footer -->

<div id="quick-view" class="reveal-modal" data-reveal>
</div>

<?php wp_footer(); ?>

</body>

</html> <!-- end page -->