<?php foreach ( $addon['options'] as $i => $option ) : ?>

	<p class="large-4 columns addon-item addon-wrap-<?php echo sanitize_title( $addon['field-name'] ) . '-' . $i; ?>">
		<label>
			<span class="check"></span>
			<?php echo wp_get_attachment_image( $option['image'], 'product-addon', 0, array( 'class' => 'th' ) ); ?>
			<input type="checkbox" class="addon addon-checkbox" name="addon-<?php echo sanitize_title( $addon['field-name'] ); ?>[]" data-price="<?php echo $option['price']; ?>" value="<?php echo sanitize_title( $option['label'] ); ?>" />
			<span class="addon-title"><?php echo $option['label'];  ?></span>
			<span class="addon-price"><?php echo '+ $' . $option['price']; ?></span>
		</label>
	</p>

<?php endforeach; ?>