<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php global $woocommerce; ?>

<h3>Billing address</h3>

<?php do_action('woocommerce_before_checkout_billing_form', $checkout); ?>

<div class="woocommerce-billing-fields clearfix">

<?php foreach ( $checkout -> checkout_fields['billing'] as $key => $field ) : ?>
	
	<?php woocommerce_form_field( $key, $field, $checkout -> get_value( $key ) ); ?>
	
<?php endforeach; ?>

</div>

<?php do_action('woocommerce_after_checkout_billing_form', $checkout); ?>