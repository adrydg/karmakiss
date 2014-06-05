<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php global $product; ?>

<?php if ( $product->post->comment_status == 'open' ) : $product_data = wc_yotpo_get_product_data( $product ); ?>
<div class="yotpo bottomLine" 
	data-appkey="<?php echo $product_data['app_key']; ?>"
	data-domain="<?php echo $product_data['shop_domain']; ?>"
	data-product-id="<?php echo $product_data['id']; ?>"
	data-product-models="<?php echo $product_data['product-models']; ?>"
	data-name="<?php echo $product_data['title']; ?>"
	data-url="<?php echo $product_data['url']; ?>"
	data-image-url="<?php echo $product_data['image-url']; ?>"
	data-description="<?php echo $product_data['description']; ?>"
	data-bread-crumbs="Make My Review"
	data-lang="<?php echo $product_data['lang']; ?>"
	data-images-star_empty="<?php echo get_template_directory_uri() . '/img/icon-star-empty.png'; ?>"
	data-images-star_half="<?php echo get_template_directory_uri() . '/img/icon-star-half.png'; ?>"
	data-images-star_full="<?php echo get_template_directory_uri() . '/img/icon-star-full.png'; ?>"
	>
</div>
<?php endif; ?>