<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php get_header(); ?>

<div id="page-header" class="category">
	<div class="row collapse">
	
		<div class="large-4 columns">
			<?php
			global $wp_query;
			$category = $wp_query -> get_queried_object();
			$current_category = $category -> term_id;
			$has_parent = get_ancestors( $category -> term_id, 'product_cat' );
			if ( $has_parent )
				$category = get_term_by( 'id', strval( $has_parent[0] ), 'product_cat' );
			$thumbnail = get_woocommerce_term_meta( $category -> term_id, 'thumbnail_id', true );
			?>
			<?php echo wp_get_attachment_image( $thumbnail, 'product-category' ); ?>
		</div>
		
		<div class="large-4 columns">
			<div id="category-header">
				<div class="inner">
					<h1 class="page-title"><?php echo $category -> name; ?></h1>
					<p><?php echo $category -> description; ?></p>
				</div>
			</div>
		</div>
		
		<div id="category-top-sellers" class="large-4 columns">
			<h2>Top sellers</h2>
			<?php
			$products_args = array(
				'post_type' 			=> 'product',
				'meta_query' 			=> array(
				array(
					'key' 		=> '_visibility',
					'value' 	=> array( 'catalog', 'visible' ),
					'compare' 	=> 'IN'
				) ),
				'tax_query' => array(
				array(
					'taxonomy' => 'product_cat',
					'field' => 'term_id',
					'terms' => $current_category
				) ),
				'meta_key' => 'total_sales',
				'orderby' => 'meta_value_num',
				'posts_per_page' => 5
			);
			$products = new WP_Query( $products_args );
			if ( $products -> have_posts() ) : ?>
			<div class="preloader"></div>
			<ul data-orbit data-options="animation:fade; slide_number:false; navigation_arrows:false; timer:false;">
				<?php while( $products -> have_posts() ) : $products -> the_post(); ?>
				<li><?php the_post_thumbnail( 'cart-item' ); ?></li>
				<?php endwhile; ?>
			</ul>
			<?php endif; wp_reset_postdata(); ?>
		</div>
	
	</div>
</div> <!-- end #page-header -->

<?php do_action( 'woocommerce_before_main_content' ); ?>

<?php if ( have_posts() ) : ?>

<div id="main" class="row">
	
	<div id="content" class="large-9 columns right">
		
		<h3><?php $category = $wp_query->get_queried_object(); echo $category -> name; ?>  <?php woocommerce_catalog_ordering(); ?></h3>
		
		<div id="products" class="row">
			
			<?php while ( have_posts() ) : the_post(); ?>
				
				<?php wc_get_template_part( 'content', 'product' ); ?>
				
			<?php endwhile; ?>
			
			<?php do_action( 'woocommerce_after_shop_loop' ); ?>
			
		</div> <!-- end #inner-content -->
		
	</div> <!-- end #content -->
	
	<?php do_action( 'woocommerce_sidebar' ); ?>
	
</div> <!-- end #main -->

<?php elseif ( ! woocommerce_product_subcategories( array( 'before' => woocommerce_product_loop_start( false ), 'after' => woocommerce_product_loop_end( false ) ) ) ) : ?>
	
	<?php wc_get_template( 'loop/no-products-found.php' ); ?>
	
<?php endif; ?>

<?php do_action( 'woocommerce_after_main_content' ); ?>

<?php get_footer(); ?>