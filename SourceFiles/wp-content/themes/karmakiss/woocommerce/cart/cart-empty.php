<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<?php wc_print_notices(); ?>

<div id="main" class="row">
	
	<div id="content" class="large-12 columns" role="main">
		
		<div id="empty-cart" class="border-block">
			
			<h2><?php _e( 'Your cart is currently empty.', 'woocommerce' ) ?></h2>
			
			<?php do_action( 'woocommerce_cart_is_empty' ); ?>
			
			<a class="button wc-backward" href="<?php echo home_url(); ?>"><?php _e( 'Return To Shop', 'woocommerce' ) ?></a>
			
		</div>
		
	</div>
	
</div>