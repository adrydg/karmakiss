<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php global $post, $woocommerce, $product; ?>

<div class="images">
	
	<?php if ( has_post_thumbnail() ) : ?>
	<a href="<?php echo wp_get_attachment_url( get_post_thumbnail_id() ); ?>" itemprop="image" class="woocommerce-main-image zoom"><?php echo get_the_post_thumbnail( $post->ID, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ) ); ?></a>
	<?php else : ?>
	<img src="<?php echo wc_placeholder_img_src(); ?>" alt="Placeholder" />
	<?php endif; ?>
	
	<?php if ( ! has_term( 'weekly-deals', 'product_tag' ) ) do_action( 'woocommerce_product_thumbnails' ); ?>
	
</div>
