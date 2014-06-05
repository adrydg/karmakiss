<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<div class="cross-sells">
	<div class="row">
		<h3>Add a greeting card to your order</h3>
		<?php
		$pf = new WC_Product_Factory();
		$gift_card = $pf -> get_product(544);
		$variations = $gift_card -> get_available_variations();
		?>
		<ul data-orbit data-options="slide_number:false; timer:false; bullets:false;">
			<?php foreach ($variations as $key => $value) : ?>
			
			<?php if ( ( $key + 1 ) % 6 == 1 ) : ?>
			<li>
			<?php endif; ?>	
			
			<div class="small-6 medium-4 large-2 columns left">
				<img src="<?php echo $value['image_src']; ?>" alt="greeting-card" />
				<a href="<?php echo WC()->cart->get_cart_url() . '?add-to-cart=544&variation_id=' . $value['variation_id'] . '&' . key( $value['attributes'] ) . '=' . reset( $value['attributes'] ) . '&quantity=1' ; ?>" class="button expand">Add + <?php echo strip_tags( $value['price_html'] ); ?></a>
			</div>
			
			<?php if ( ( $key + 1 ) % 6 == 0 ) : ?>
			</li>
			<?php endif; ?>
			
			<?php endforeach; ?>
		</ul>
	</div>
</div>