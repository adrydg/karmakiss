<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<div id="main" class="row">
	<div id="content" class="large-12 columns" role="main">
		<div id="no-products" class="border-block">
			<h2><?php _e( 'No products were found matching your selection.', 'woocommerce' ); ?></h2>
			<a class="button wc-backward" href="<?php echo home_url(); ?>"><?php _e( 'Return To Shop', 'woocommerce' ) ?></a>
		</div>
	</div>
</div>