<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<div class="large-3 columns">
	<h3>What you need to know</h3>
	<?php the_field( 'product-description-1' ); ?>
</div>
<div class="large-3 columns">
	<h3>Why we like it</h3>
	<?php the_field( 'product-description-2' ); ?>
	<?php echo do_shortcode( '[yith_wcwl_add_to_wishlist]' ); ?>
</div>
