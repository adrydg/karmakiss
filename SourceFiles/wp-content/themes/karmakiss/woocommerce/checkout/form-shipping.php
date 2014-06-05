<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php global $woocommerce; ?>

<?php if ( (  WC()->cart->needs_shipping() || get_option('woocommerce_require_shipping_address') == 'yes' ) && !  WC()->cart->ship_to_billing_address_only() ) : ?>
    
	<h3 id="shippingaddress-title"><?php _e('Shipping Address', 'yit'); ?></h3>
	
    <?php
			if ( empty( $_POST ) ) :
	
				$shiptobilling = (get_option('woocommerce_ship_to_same_address')=='yes') ? 1 : 0;
				$shiptobilling = apply_filters('woocommerce_shiptobilling_default', $shiptobilling);
	
			else :
	
				$shiptobilling = $checkout->get_value('shiptobilling');
	
			endif;
		?>
	
		<p class="form-row" id="shiptobilling_bill">
			<input id="shiptobilling_bill-checkbox" class="input-checkbox" <?php checked($shiptobilling, 1); ?> type="checkbox" name="shiptobilling" value="1" />
			<label for="shiptobilling_bill-checkbox" class="checkbox"><?php _e('Ship to billing address?', 'yit'); ?></label>
		</p>
		
		<p class="form-row" id="ship-to-different-address">
		<input id="shiptobilling-checkbox" class="input-checkbox" <?php checked(1, 1); ?> type="checkbox" name="ship_to_different_address" value="1" data-woocommerce-version="<?php echo WC_VERSION ?>"/>
		<label for="shiptobilling-checkbox" class="checkbox"><?php _e('Ship to different address?', 'yit'); ?></label>
	</p>
       

	<div class="shipping_address">

		<?php do_action('woocommerce_before_checkout_shipping_form', $checkout); ?>

		<?php foreach ($checkout->checkout_fields['shipping'] as $key => $field) : ?>

			<?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>

		<?php endforeach; ?>

		<?php do_action('woocommerce_after_checkout_shipping_form', $checkout); ?>

	</div>

<?php endif; ?>