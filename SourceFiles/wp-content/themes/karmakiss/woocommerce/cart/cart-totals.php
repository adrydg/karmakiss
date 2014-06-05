<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php global $woocommerce; ?>

<div class="cart-totals">

	<?php do_action( 'woocommerce_before_cart_totals' ); ?>

	<h3 class="cart-subtotal">Items subtotal <span><?php wc_cart_totals_subtotal_html(); ?></span></h3>
	
	<?php foreach ( WC()->cart->get_coupons( 'cart' ) as $code => $coupon ) : ?>
	<p class="cart-discount coupon-<?php echo esc_attr( $code ); ?>">
		<strong><?php wc_cart_totals_coupon_label( $coupon ); ?></strong>
		<span><?php wc_cart_totals_coupon_html( $coupon ); ?></span>
	</p>
	<?php endforeach; ?>
	
	<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
	<p class="fee">
		<strong><?php echo esc_html( $fee->name ); ?></strong>
		<span><?php wc_cart_totals_fee_html( $fee ); ?></span>
	</p>
	<?php endforeach; ?>
	
	<?php if ( WC()->cart->tax_display_cart == 'excl' ) : ?>
	
	<?php if ( get_option( 'woocommerce_tax_total_display' ) == 'itemized' ) : ?>
	
	<?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : ?>
	<p class="tax-rate tax-rate-<?php echo sanitize_title( $code ); ?>">
		<strong><?php echo esc_html( $tax->label ); ?></strong>
		<span><?php echo wp_kses_post( $tax->formatted_amount ); ?></span>
	</p>
	<?php endforeach; ?>
	
	<?php else : ?>
	<p class="tax-total">
		<strong><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></strong>
		<span><?php echo wc_cart_totals_taxes_total_html(); ?></span>
	</p>
	<?php endif; ?>
	
	<?php endif; ?>
	
	<?php foreach ( WC()->cart->get_coupons( 'order' ) as $code => $coupon ) : ?>
	<p class="order-discount coupon-<?php echo esc_attr( $code ); ?>">
		<strong><?php wc_cart_totals_coupon_label( $coupon ); ?></strong>
		<span><?php wc_cart_totals_coupon_html( $coupon ); ?></span>
	</p>
	<?php endforeach; ?>
	
	<?php do_action( 'woocommerce_cart_totals_before_order_total' ); ?>
	
	<h3 class="order-total">Subtotal <span><?php wc_cart_totals_order_total_html(); ?></span></h3>
		
	<?php do_action( 'woocommerce_cart_totals_after_order_total' ); ?>
				
	<?php if ( WC()->cart->get_cart_tax() ) : ?>
	<p><small><?php

		$estimated_text = WC()->customer->is_customer_outside_base() && ! WC()->customer->has_calculated_shipping()
			? sprintf( ' ' . __( ' (taxes estimated for %s)', 'woocommerce' ), WC()->countries->estimated_for_prefix() . __( WC()->countries->countries[ WC()->countries->get_base_country() ], 'woocommerce' ) )
			: '';

		printf( __( 'Note: Shipping and taxes are estimated%s and will be updated during checkout based on your billing and shipping information.', 'woocommerce' ), $estimated_text );

	?></small></p>
	<?php endif; ?>
	
	<?php do_action( 'woocommerce_after_cart_totals' ); ?>
	
	<div class="submit">
		<input type="submit" class="checkout-button button alt wc-forward" name="proceed" value="<?php the_field( 'cart-button', 'options' ); ?>" />
	</div>
	
</div> <!-- end .cart-totals -->