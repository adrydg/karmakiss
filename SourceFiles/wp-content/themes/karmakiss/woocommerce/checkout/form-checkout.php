<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<?php global $woocommerce; $woocommerce_checkout = $woocommerce->checkout(); ?> 

<?php wc_print_notices(); ?>

<?php do_action('woocommerce_before_checkout_form', $checkout); ?>

<?php $get_checkout_url = apply_filters( 'woocommerce_get_checkout_url', $woocommerce->cart->get_checkout_url() ); ?>

<div id="page-header" class="checkout">
	<div class="row">
		<a href="#" class="large-3 columns <?php echo !is_user_logged_in() ? 'current' : 'user_logged_in'; ?>" data-step="1"><span>Your shopping cart</span></a>
		<a href="#" class="large-3 columns <?php echo is_user_logged_in() ? 'current' : false; ?>" data-step="2"><span>Delivery address</span></a>
		<a href="#" class="large-3 columns" data-step="3"><span>Payment and shipping</span></a>
		<a href="#" class="large-3 columns" data-step="4"><span>Review and place order</span></a>
	</div>
</div> <!-- end #page-header -->

<div id="checkout" class="row">
	
	<?php if ( ! is_user_logged_in() ) : ?>
	<div class="checkout-step current" id="step-1" data-step="1">
		<div class="large-12 columns">
			<h2>Sign in to continue</h2>
		</div>
		<?php wc_get_template('checkout/form-login.php', array('woocommerce'=>$woocommerce)) ?>
	</div>
	<?php else: ?>
	<div class="checkout-step user_logged_in" id="step-1" data-step="1"></div>
	<?php endif ?>
	
	<form name="checkout" method="post" class="checkout" action="<?php echo esc_url( apply_filters( 'woocommerce_get_checkout_url', WC()->cart->get_checkout_url() ) ); ?>">
	
	<?php do_action( 'woocommerce_checkout_before_customer_details'); ?>

	<div class="checkout-step <?php if ( is_user_logged_in() ) echo 'current'; ?>" id="step-2" data-step="2">
		<div class="large-12 columns">
			<h2>Delivery address</h2>
		</div>
		<div class="large-6 columns">
			<div class="billing-shipping clearfix">
				<?php do_action('woocommerce_checkout_billing'); ?>
				<?php do_action('woocommerce_checkout_shipping'); ?>
			</div>
		</div>
		<div class="large-6 columns">
			<div class="order-totals">
				<h3 class="order-subtotal">Items subtotal <span><?php wc_cart_totals_subtotal_html(); ?></span></h3>
				<?php foreach ( WC()->cart->get_coupons( 'order' ) as $code => $coupon ) : ?>
				<p><strong>Coupon:</strong> <?php echo $code; ?> <span><?php wc_cart_totals_coupon_html( $coupon ); ?></span></p>
				<?php endforeach; ?>
				<h3 class="total">Subtotal <span><?php wc_cart_totals_order_total_html(); ?></span></h3>
				<div class="submit">
					<input type="submit" class="button next" value="<?php echo get_field( 'checkout-button', 'options' ) ?>" data-next="3" />
				</div>
			</div>
		</div>
	</div>

	<?php do_action( 'woocommerce_checkout_after_customer_details'); ?>
	
	<div class="checkout-step" id="step-3" data-step="3">
		<div class="large-12 columns">
			<h2>Payment and shipping</h2>
		</div>
		<div class="large-6 columns">
			<div class="payment-shipping clearfix">
			<?php wc_get_template('checkout/form-payment.php'); ?>
			</div>
		</div>
		<div class="large-6 columns">
			<div class="order-totals">
				<h3 class="order-subtotal">Items subtotal <span><?php wc_cart_totals_subtotal_html(); ?></span></h3>
				<?php foreach ( WC()->cart->get_coupons( 'order' ) as $code => $coupon ) : ?>
				<p><strong>Coupon:</strong> <?php echo $code; ?> <span><?php wc_cart_totals_coupon_html( $coupon ); ?></span></p>
				<?php endforeach; ?>
				<h3 class="total">Subtotal <span><?php wc_cart_totals_order_total_html(); ?></span></h3>
				<div class="submit">
					<input type="submit" class="button next" value="<?php echo get_field( 'checkout-button', 'options' ) ?>" data-next="4" />
				</div>
			</div>
		</div>
    </div>
    
	<div class="checkout-step" id="step-4" data-step="4">
		<div class="large-12 columns">
			<h2>Review and place order</h2>
		</div>
		<div class="large-6 columns">
			<div class="review-order clearfix">
				<h3>Products in your order</h3>
				
				<?php do_action( 'woocommerce_review_order_before_cart_contents' ); ?>
				
				<?php foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) : ?>
				
				<?php
			    $_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			    if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) : ?>
			    <p class="row">
			    	<span class="product-name large-8 columns">
						<?php echo apply_filters( 'woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key ); ?>
						<?php echo apply_filters( 'woocommerce_checkout_cart_item_quantity','', $cart_item, $cart_item_key ); ?>
						<?php WC()->cart->get_item_data( $cart_item ); ?>
			    	</span>
			        <span class="product-quantity large-2 columns">
			        	<?php echo $cart_item['price'] . ' x ' . $cart_item['quantity']; ?>
			        </span>
			        <span class="product-total large-2 columns">
			            <?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); ?>
			        </span>
			    </p>
				<?php endif; ?>
				
				<?php endforeach; ?>
				
				<?php do_action( 'woocommerce_review_order_after_cart_contents' ); ?>
				
				<?php $checkout = $woocommerce->checkout(); ?>

				<?php do_action('woocommerce_before_order_notes', $checkout); ?>
					
				<?php if (get_option('woocommerce_enable_order_comments')!='no') : ?>
				
				<h3>Notes on the order</h3>
				<p class="row">If you have any notes on the order, please type them here:</p>
				
				<?php foreach ($checkout->checkout_fields['order'] as $key => $field) : ?>
				
					<?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>
				
				<?php endforeach; ?>
				
				<?php endif; ?>
					
				<?php do_action('woocommerce_after_order_notes', $checkout); ?>
				
			</div>
		</div>
		<div class="large-6 columns">
			<div class="order-totals">
				<?php do_action( 'woocommerce_checkout_order_review' ); ?>
			</div>
		</div>
    </div>
	
	</form>

</div> <!-- end #checkout -->

<?php do_action('woocommerce_after_checkout_form', $checkout); ?>

<script type="text/javascript">
jQuery( document ).ready( function(){
	jQuery( '#checkout' ).yit_checkout();
});
</script>