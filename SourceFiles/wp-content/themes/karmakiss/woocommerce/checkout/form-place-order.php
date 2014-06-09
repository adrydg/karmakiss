

		<div class="form-row place-order">

			<noscript><?php _e('Since your browser does not support JavaScript, or it is disabled, please ensure you click the <em>Update Totals</em> button before placing your order. You may be charged more than the amount stated above if you fail to do so.', 'woocommerce'); ?><br/><input type="submit" class="button alt" name="woocommerce_checkout_update_totals" value="<?php _e('Update totals', 'woocommerce'); ?>" /></noscript>

            <?php wp_nonce_field( 'woocommerce-process_checkout')?>

			<?php do_action( 'woocommerce_review_order_before_submit' ); ?>

			<!-- <input type="submit" class="button alt" name="woocommerce_checkout_place_order" id="place_order" value="<?php echo apply_filters('woocommerce_order_button_text', __('Place order', 'woocommerce')); ?>" />
  -->
            <?php
            $order_button_text = apply_filters( 'woocommerce_order_button_text', __( 'Place order', 'woocommerce' ) );

            echo apply_filters( 'woocommerce_order_button_html', '<input type="submit" class="button alt" name="woocommerce_checkout_place_order" id="place_order" value="' . get_field( 'checkout-final-button', 'options' ) . '" data-value="' . esc_attr( $order_button_text ) . '" />' );
            ?>

			<p id="terms">By clicking “Place Order”, you hereby agree to and accept our <a href="<?php the_field( 'checkout-terms', 'options' ); ?>" target="_blank"><?php _e('Terms of Service', 'woocommerce'); ?></a></p>

			<?php do_action( 'woocommerce_review_order_after_submit' ); ?>

		</div>
<script type="text/javascript">
jQuery( document ).ready( function(){
    /*
        var currentShipping = jQuery('#checkout #order_review h3.shipping span').html();

        //check number of indexOf
        var firstIndex = currentShipping.indexOf(".");
        var secondIndex = currentShipping.indexOf(".", firstIndex+1 );
        var newShipping = '';

        if( secondIndex != -1 ){
           newShipping = currentShipping.substring(0, secondIndex );	
        }

        jQuery('#checkout #order_review h3.shipping span').html(newShipping );  
        */
});
</script>