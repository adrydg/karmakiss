<?php foreach ( $addon['options'] as $key => $option ) :
	$addon_key     = 'addon-' . sanitize_title( $addon['field-name'] );
	$option_key    = empty( $option['label'] ) ? $key : sanitize_title( $option['label'] );
	$current_value = isset( $_POST[ $addon_key ] ) && isset( $_POST[ $addon_key ][ $option_key ] ) ? $_POST[ $addon_key ][ $option_key ] : '';
	$price         = $option['price'] > 0 ? '(' . woocommerce_price( $option['price'] ) . ')' : '';
	?>

	<p class="form-row form-row-wide addon-wrap-<?php echo sanitize_title( $addon['field-name'] ); ?>">
		<?php if ( ! empty( $option['label'] ) ) : ?>
			<label><?php echo wptexturize( $option['label'] ) . ' ' . $price; ?></label>
		<?php endif; ?>	
		<input type="number" step="" class="input-text addon addon-input_multiplier" data-price="<?php echo $option['price']; ?>" name="<?php echo $addon_key ?>[<?php echo $option_key; ?>]" value="<?php echo esc_attr( $current_value ); ?>" <?php if ( ! empty( $option['min'] ) || $option['min'] === '0' ) echo 'min="' . $option['min'] .'"'; ?> <?php if ( ! empty( $option['max'] ) ) echo 'max="' . $option['max'] .'"'; ?> />
		<span class="addon-alert"><?php _e( 'This must be a number!', 'woocommerce-product-addons' ); ?></span>
	</p>

<?php endforeach; ?>