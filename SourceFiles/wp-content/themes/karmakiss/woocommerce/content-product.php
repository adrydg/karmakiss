<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php $class = is_tax( 'product_cat' ) ? 'medium-4' : 'medium-3'; ?>
<?php if ( is_singular( 'product' ) ) $class = 'medium-2'; ?>
<div class="product <?php echo $class; ?> small-6 columns left">
	<?php do_action( 'woocommerce_before_shop_loop_item' ); ?>
	<div class="row quick-view">
		<a href="#" class="button" data-id="<?php echo get_the_ID(); ?>">Quick View</a>
	</div>
	<a href="<?php the_permalink(); ?>"><?php do_action( 'woocommerce_before_shop_loop_item_title' ); ?></a>
	<div class="panel">
		<p><a href="<?php the_permalink(); ?>"><?php echo short_title( '...', 25 ); ?></a></p>
		<?php do_action( 'woocommerce_after_shop_loop_item_title' ); ?>
	</div>
        <form class="cart upsellProductAddToCartForm" method="post" enctype='multipart/form-data' style="display: none;"> 
 	<input type="hidden" name="add-to-cart" value="<?php echo get_the_ID(); ?>" /> 	
 	<button type="submit" class="single_add_to_cart_button button expand alt upsellProductAddToCartBtn">Add to cart</button>
        </form>        
	<?php do_action( 'woocommerce_after_shop_loop_item' ); ?>
</div>