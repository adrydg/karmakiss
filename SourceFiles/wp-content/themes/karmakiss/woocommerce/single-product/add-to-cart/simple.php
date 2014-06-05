<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php global $woocommerce, $product; ?>

<?php if ( $product->is_in_stock() ) : ?>

<?php do_action( 'woocommerce_before_add_to_cart_form' ); ?>

<form class="cart" method="post" enctype='multipart/form-data'>
	
	<?php if( !has_term( 'weekly-deals', 'product_tag' ) ) : ?>
 	<div class="quantity-row row">
 	<div class="small-6 columns">
 	<label>Units</label>
 	<?php
 		if ( ! $product->is_sold_individually() )
 			woocommerce_quantity_input( array(
 				'min_value' => apply_filters( 'woocommerce_quantity_input_min', 1, $product ),
 				'max_value' => apply_filters( 'woocommerce_quantity_input_max', $product->backorders_allowed() ? '' : $product->get_stock_quantity(), $product )
 			) );
 	?>
 	</div>
 	<div class="small-6 columns">
 		<p class="shipping-message"><?php the_field( 'shipping-message' ); ?></p>
 	</div>
 	</div>
	<?php wc_get_template( 'single-product/addons.php' ); ?>

 	<?php endif; ?>
 	
 	<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>
 	
 	<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product->id ); ?>" /> 	
 	<button type="submit" class="single_add_to_cart_button button expand alt">Add to shopping cart</button>
 	
	<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>

</form>

<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>

<?php endif; ?>