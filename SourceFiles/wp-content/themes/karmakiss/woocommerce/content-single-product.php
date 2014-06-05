<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<div id="page-header">
	<div class="row">
		<div class="large-12 columns">
			<?php
			if( !has_term( 'weekly-deals', 'product_tag' ) ) : 
			$current_category = wc_get_product_terms( $post->ID, 'product_cat' );
			$has_parent = get_ancestors( $current_category[0] -> term_id, 'product_cat' );
			if ( $has_parent[0] ) {
				$parent_category = get_term_by( 'id', strval( $has_parent[0] ), 'product_cat' );
				$category = $parent_category -> name;
			} else {
				$category = $current_category[0] -> name;
			}
			?>
			<h2><?php echo $category; ?></h2>
			<?php woocommerce_breadcrumb(); ?>
			<?php else : ?>
			<h1 id="page-title">Karma Kiss Weekly Deals</h1>
			<p id="page-description">40% to 80% Off One Product Every Week!</p>
			<?php endif; ?>
		</div>
	</div>
</div> <!-- end #page-header -->
	
<?php do_action( 'woocommerce_before_single_product' ); ?>

<div id="main">
	
	<div itemscope itemtype="<?php echo woocommerce_get_product_schema(); ?>" <?php post_class(); ?>>
		
		<div class="row">
	
			<div class="medium-6 columns">
				<div class="gallery panel">
					<?php do_action( 'woocommerce_before_single_product_summary' ); ?>
					<?php if ( !has_term( 'weekly-deals', 'product_tag' ) ) woocommerce_template_single_sharing(); ?>
				</div>
			</div>
			
			<div class="medium-6 columns">
				<div class="summary panel clearfix">
					<?php do_action( 'woocommerce_single_product_summary' ); ?>
				</div>
			</div>
			
		</div>
						
		<div class="row">
		
			<div class="large-12 columns">
				<div class="panel info clearfix">
					<?php woocommerce_product_description_tab(); ?>
					<?php wc_get_template( 'single-product/tabs/additional-information.php' ); ?>
				</div>
			</div>
		
		</div>
		
		<?php if( !has_term( 'weekly-deals', 'product_tag' ) ) : ?>
		<div class="row">
		
			<div class="large-12 columns">
				<div class="panel info clearfix">
				<div class="large-6 columns">
					<h3>Shipping information</h3>
					<?php the_field( 'single-product-shipping', 'options' ); ?>
				</div>
				<div class="large-6 columns">
					<h3>Shipping calculator</h3>
					<?php do_action( 'woocommerce_after_single_product_summary' ); ?>
				</div>
				</div>
			</div>
		
		</div>
		
		<div class="row">
		
			<div class="large-12 columns">
				<div class="panel reviews clearfix">
					<?php wc_yotpo_show_widget(); ?>
				</div>
			</div>
		
		</div>
		<?php endif; ?>

		<meta itemprop="url" content="<?php the_permalink(); ?>" />

	</div>
	
</div> <!-- end #main -->

<?php do_action( 'woocommerce_after_single_product' ); ?>