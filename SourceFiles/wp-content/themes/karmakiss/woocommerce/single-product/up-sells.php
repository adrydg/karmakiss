<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php global $product; ?>

<?php

$upsells = $product->get_upsells();

if ( sizeof( $upsells ) == 0 ) return;

$meta_query = WC()->query->get_meta_query();

$args = array(
	'post_type'           => 'product',
	'ignore_sticky_posts' => 1,
	'no_found_rows'       => 1,
	'posts_per_page'      => $posts_per_page,
	'orderby'             => $orderby,
	'post__in'            => $upsells,
	'post__not_in'        => array( $product->id ),
	'meta_query'          => $meta_query
);

$products = new WP_Query( $args );

?>

<?php if ( $products->have_posts() ) : ?>
<div id="similar">
	
	<div class="row">
		<div class="large-12 columns">
			<h2>Similar Products You May Want</h2>
		</div>
	</div>
	
	<div class="row products">
		
	<?php while ( $products->have_posts() ) : $products->the_post(); ?>
	
		<?php wc_get_template_part( 'content', 'product' ); ?>
	
	<?php endwhile; ?>
		
	</div>
	
</div> <!-- end #related -->
<?php endif; wp_reset_postdata(); ?>