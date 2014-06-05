<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php global $post, $woocommerce, $product; ?>
<?php $thumbnails = array_merge( array( get_post_thumbnail_id( $post->ID ) ), $product -> get_gallery_attachment_ids() ); ?>

<div class="preloader"></div>
<ul class="thumbnails" data-orbit data-options="slide_number:false; timer:false; bullets:false; next_on_click:false;">
	
	<?php foreach ( $thumbnails as $thumbnail_no => $thumbnail ) : ?>
	
	<?php if ( ( $thumbnail_no + 1 ) % 4 == 1 ) : ?>
	<li class="thumbnail" data-orbit-slide="thumbnail-<?php echo $thumbnail_no / 4 + 1; ?>">
	<?php endif; ?>
	
	<a href="<?php echo array_values( wp_get_attachment_image_src( $thumbnail, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ) ) )[0]; ?>" class="th"><?php echo wp_get_attachment_image( $thumbnail, apply_filters( 'single_product_small_thumbnail_size', 'shop_thumbnail' ) ); ?></a>
	
	<?php if ( ( ( $thumbnail_no + 1 ) % 4 == 0 ) || ( !get_field( 'videos' ) && ( $thumbnail_no + 1 == count( $thumbnails ) ) ) ) : ?>
	</li>
	<?php endif; ?>
	
	<?php endforeach; ?>
	
	<?php if ( have_rows( 'videos' ) ): while ( have_rows( 'videos' ) ) : the_row(); $thumbnail_no++; ?>
	
	<?php if ( ( $thumbnail_no + 1 ) % 4 == 1 ) : ?>
	<li class="thumbnail" data-orbit-slide="thumbnail-<?php echo $thumbnail_no / 4 + 1; ?>">
	<?php endif; ?>
	
	<a data-orbit-link="gallery-<?php echo $thumbnail_no; ?>" class="th"><img src="http://img.youtube.com/vi/<?php parse_str( parse_url( get_sub_field( 'video' ), PHP_URL_QUERY ), $url ); echo $url['v']; ?>/2.jpg" width="50" height="50" alt="thumbnail" /></a>

	<?php if ( ( ( $thumbnail_no + 1 ) % 4 == 0 ) || ( ( $thumbnail_no - count( $thumbnails ) + 1 ) == count( get_field( 'videos' ) ) ) ) : ?>
	</li>
	<?php endif; ?>
	
	<?php endwhile; endif; ?>
	
</ul>