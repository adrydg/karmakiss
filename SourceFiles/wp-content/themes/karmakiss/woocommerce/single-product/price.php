<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php global $post, $product; ?>
<ul id="price" class="small-4 columns">
	<?php if( !has_term( 'weekly-deals', 'product_tag' ) ) : ?>
	<li><?php echo $product->get_price_html(); ?></li>
	<?php endif; ?>
	<?php if ( $product->sale_price ) : ?><span id="sale-percentage"><li><?php echo ' - ' .  round( ( ( $product->regular_price - $product->sale_price ) / $product->regular_price ) * 100 ) . ' % '; ?></span></li><?php endif; ?>
</ul>
</div>

<?php if( !has_term( 'weekly-deals', 'product_tag' ) ) : ?>
<div class="status row">
	<div class="small-2 columns">
		<strong>Code</strong>
		<p><?php echo $product->get_sku(); ?></p>
	</div>
	<div class="small-2 columns">
		<strong>Status</strong>
		<?php
		$availability = $product->get_availability();
		if( $availability['availability'] )
			echo '<p class="stock">' . str_replace( 'left in stock', 'left', $availability['availability'] ) . '</p>';
		else
			echo '<p class="stock">In Stock</p>';
		?>		
	</div>
	<div class="small-2 columns">
		<strong>Points</strong>
		<p><?php do_action( 'woocommerce_single_product_points' ); ?></p>
	</div>
	<div class="small-6 columns">
		<p class="promotional"><?php the_field( 'promotional' ); ?></p>
	</div>
</div>
<?php else : ?>
<div class="deal">
	<div class="price row">
		<div class="small-4 columns">
			<p>Before <span class="old">$<?php echo $product->regular_price; ?></span></p>
		</div>
		<div class="small-4 columns">
			<p>You Save <span>$<?php echo $product->regular_price - $product->sale_price; ?></span></p>
		</div>
		<div class="small-4 columns">
			<h1>$<?php echo $product->sale_price; ?>
		</div>
	</div>
	<?php woocommerce_template_single_sharing(); ?>
</div>
<?php endif; ?>