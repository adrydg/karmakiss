<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php global $woocommerce; ?>

<div id="order_review">

	<h3 class="order-subtotal">Items subtotal <span><?php wc_cart_totals_subtotal_html(); ?></span></h3>
	
	<?php if ( WC()->cart->get_coupons( 'order' ) ) : ?>
	<?php foreach ( WC()->cart->get_coupons( 'order' ) as $code => $coupon ) : ?>
	<p><strong>Coupon:</strong> <?php echo $code; ?> <span><?php wc_cart_totals_coupon_html( $coupon ); ?></span></p>
	<?php endforeach; ?>
	<?php else : ?>
	<p><strong>Coupon:</strong> None <span></span></p>
	<?php endif; ?>
	<h3 class="shipping">Shipping <span>$ <?php echo WC()->cart->shipping_total; ?>.00</span></h3>
	
	<h3 class="total">Total <span><?php wc_cart_totals_order_total_html(); ?></span></h3>
	
	<div class="submit">
	<?php wc_get_template( 'checkout/form-place-order.php' ); ?>				
	</div>
	
</div>