<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php global $post, $product; ?>
<?php if ( $product -> is_on_sale() ) : ?>
	<span class="on-sale">On sale</span>
<?php elseif ( in_array( $post->ID, wc_get_product_ids_bestseller() ) ) : ?>
	<span class="best-seller">Bestseller</span>
<?php endif; ?>