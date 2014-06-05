<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<div id="page-header" class="checkout">
	<div class="row">
		<a href="<?php echo home_url( '/cart/' ); ?>" class="large-3 columns current"><span>Your shopping cart</span></a>
		<a href="<?php echo home_url( '/checkout/' ); ?>" class="large-3 columns"><span>Delivery address</span></a>
		<a href="<?php echo home_url( '/checkout/' ); ?>" class="large-3 columns"><span>Payment and shipping</span></a>
		<a href="<?php echo home_url( '/checkout/' ); ?>" class="large-3 columns"><span>Review and place order</span></a>
	</div>
</div> <!-- end #page-header -->

<?php global $woocommerce; ?>

<?php wc_print_notices(); ?>

<div id="main">
	
	<?php do_action( 'woocommerce_before_cart' ); ?>
	
	<div id="cart">
					
		<form action="<?php echo esc_url( WC()->cart->get_cart_url() ); ?>" method="post">
		
		<?php do_action( 'woocommerce_before_cart_table' ); ?>
		
		<div class="row">
			<div class="large-12 columns">
				<h2 id="cart-title">Your cart contains <?php echo sizeof( WC()->cart->get_cart() ); ?> items <small>Subtotal <span><?php echo WC()->cart->get_cart_subtotal(); ?></span></small></h2>
			</div>
		</div>
		
		<?php do_action( 'woocommerce_before_cart_contents' ); ?>
		
		<div class="row clearfix">
			
			<?php foreach ( WC() -> cart -> get_cart() as $cart_item_key => $cart_item ) : ?>
			
			<?php 
			$_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			$product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
			?>
			
			<?php if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) : ?>
			
			<div class="cart-item medium-6 columns left" data-equalizer>
				<div class="small-4 columns left" data-equalizer-watch>
					<?php if ( $_product->is_visible() ) : ?>
					<a href="<?php echo $_product->get_permalink(); ?>"><?php echo apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image( 'cart-item' ), $cart_item, $cart_item_key ); ?></a>
					<?php else : ?> 
					<?php echo apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image( 'cart-item' ), $cart_item, $cart_item_key ); ?>
					<?php endif; ?>
				</div>
				<div class="small-8 columns left">
					<div class="data clearfix" data-equalizer-watch>
					<div class="item-title">
						<?php
							if ( ! $_product->is_visible() )
								echo apply_filters( 'woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key );
							else
								echo apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', $_product->get_permalink(), $_product->get_title() ), $cart_item, $cart_item_key );

							// Meta data
							echo WC()->cart->get_item_data( $cart_item );

               				// Backorder notification
               				if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) )
               					echo '<p class="backorder_notification">' . __( 'Available on backorder', 'woocommerce' ) . '</p>';
						?>
					</div>
					<div class="large-3 columns">
						<span class="title">Quantity</span>
						<?php
						if ( $_product->is_sold_individually() ) {
							$product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
						} else {
							$product_quantity = woocommerce_quantity_input( array(
								'input_name'  => "cart[{$cart_item_key}][qty]",
								'input_value' => $cart_item['quantity'],
								'max_value'   => $_product->backorders_allowed() ? '' : $_product->get_stock_quantity(),
							), $_product, false );
						}
	
						echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key );
						?>
							<input type="submit" name="update_cart" class="update-cart" value="Update Cart" /> 
					</div>
					<div class="large-3 columns">
						<span class="title">Unit Price</span>
						<p><?php echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); ?></p>
					</div>
					<div class="subtotal large-3 columns">
						<span class="title">Subtotal</span>
						<p><?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); ?></p>
					</div>
					<div class="large-3 columns">
						<?php echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf( '<a href="%s" class="button small remove" title="%s">Delete</a>', esc_url( WC()->cart->get_remove_url( $cart_item_key ) ), __( 'Remove this item', 'woocommerce' ) ), $cart_item_key ); ?>
					</div>
					</div>
				</div>
			</div>
			
			<?php endif; ?>
			
			<?php endforeach; ?>
			
		</div>
		
		<?php do_action( 'woocommerce_after_cart_table' ); ?>
		
		<?php do_action( 'woocommerce_cart_collaterals' ); ?>

		<div class="row">
			
			<div class="medium-6 columns">
				<div id="coupon" class="clearfix">
				<h3>Have a coupon?</h3>
				<div class="medium-10 columns">
					<input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="Enter Code" />
				</div>
				<div class="medium-2 columns">
					<input type="submit" class="button expand" name="apply_coupon" value="Apply" />
				</div>
				<?php do_action('woocommerce_cart_coupon'); ?>
				</div>
			</div> <!-- end #coupon -->
			
			<div class="medium-6 columns">
				<?php woocommerce_cart_totals(); ?>
			</div>
		</div>
		
		<?php do_action( 'woocommerce_proceed_to_checkout' ); ?>
		
		<?php wp_nonce_field( 'woocommerce-cart' ); ?>

		</form>
		
	</div> <!-- end #cart -->
	
	<?php do_action( 'woocommerce_after_cart' ); ?>
	
</div> <!-- end #main -->