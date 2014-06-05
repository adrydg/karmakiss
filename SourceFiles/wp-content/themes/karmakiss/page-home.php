<?php /* Template Name: Home Page */ 
 error_reporting(0);
?>
<?php get_header(); ?>

<div id="page-header" class="home">
	
	<div class="row">
		
		<?php if ( have_rows( 'homepage-slider' ) ) : ?>
		<div class="small-12 columns banner large">
			
			<?php if ( count( get_field( 'homepage-slider' ) ) > 1 ) : ?>
			<div class="preloader"></div>
			<ul data-orbit data-options="slide_number:false; timer:false; bullets:false;">
				<?php while ( have_rows( 'homepage-slider' ) ) : the_row(); ?>
				<li><a href="<?php the_sub_field( 'homepage-slide-link' ); ?>"><?php echo wp_get_attachment_image( get_sub_field( 'homepage-slide' ), 'homepage-slide' ); ?></a></li>
				<?php endwhile; ?>
			</ul>
			<?php else : ?>
			<?php while ( have_rows( 'homepage-slider' ) ) : the_row(); ?>
				<a href="<?php the_sub_field( 'homepage-slide-link' ); ?>"><?php echo wp_get_attachment_image( get_sub_field( 'homepage-slide' ), 'homepage-slide' ); ?></a>
			<?php endwhile; ?>
			<?php endif; ?>
			
		</div>
		<?php endif; ?>
		
		<div class="medium-4 columns">
			<div class="banner small">
				<a href="<?php the_field( 'homepage-small-banner-1-link' ); ?>"><?php echo wp_get_attachment_image( get_field( 'homepage-small-banner-1' ), 'homepage-banner-small' ); ?></a>
				<h2><a href="<?php the_field( 'homepage-small-banner-1-link' ); ?>"><?php the_field( 'homepage-small-banner-1-text' ); ?></a></h2>
			</div>
                    <?php
                    if( trim(strtolower(get_field( 'homepage-shipping-option' )) ) == 'upload' && trim(get_field('homepage-shipping-banner') != '' )){
                    ?>
			<div class="banner small">
				<?php echo wp_get_attachment_image( get_field( 'homepage-shipping-banner' ), 'homepage-banner-small' ); ?>
				<h2><?php the_field( 'homepage-shipping-title' ); ?> <small><?php the_field( 'homepage-shipping-text' ); ?></small></h2>
			</div>                    
                    <?php
                    }
                    else{
                    ?>
			<div class="banner shipping">
				<div class="inner">
					<h2><?php the_field( 'homepage-shipping-title' ); ?> <small><?php the_field( 'homepage-shipping-text' ); ?></small></h2>
				</div>
			</div>
                    <?php                        
                    }
                    ?>
		</div>
		
		<div class="medium-4 columns banner medium">
				<a href="<?php the_field( 'homepage-medium-banner-link' ); ?>"><?php echo wp_get_attachment_image( get_field( 'homepage-medium-banner' ), 'homepage-banner-medium' ); ?></a>
				<h2><a href="<?php the_field( 'homepage-medium-banner-link' ); ?>"><?php the_field( 'homepage-medium-banner-text' ); ?></a></h2>
		</div>
		
		<div class="medium-4 columns">
			<div class="banner small">
				<a href="<?php the_field( 'homepage-small-banner-2-link' ); ?>"><?php echo wp_get_attachment_image( get_field( 'homepage-small-banner-2' ), 'homepage-banner-small' ); ?></a>
				<h2><a href="<?php the_field( 'homepage-small-banner-2-link' ); ?>"><?php the_field( 'homepage-small-banner-2-text' ); ?></a></h2>
			</div>

                    <?php
                    if( trim(strtolower(get_field( 'homepage-returns-box' )) ) == 'upload' && trim(get_field('homepage-shipping-banner') != '' )){
                    ?>
			<div class="banner small">
				<?php echo wp_get_attachment_image( get_field( 'homepage-returns-banner' ), 'homepage-banner-small' ); ?>
				<h2><?php the_field( 'homepage-returns-title' ); ?> <small><?php the_field( 'homepage-returns-text' ); ?></small></h2>
			</div>                    
                    <?php
                    }
                    else{
                    ?>
			<div class="banner returns">
				<div class="inner">
					<h2><?php the_field( 'homepage-returns-title' ); ?> <small><?php the_field( 'homepage-returns-text' ); ?></small></h2>
				</div>
			</div>
                    <?php                        
                    }
                    ?>
                    
		</div>
				
	</div>
</div> <!-- end #page-header -->

<div id="content" role="main">
		
	<div id="latest-products">
		
		<?php 
		$products_args = array( 
			'post_type' => 'product',
			'meta_query' => array( 
				array(
					'key' => '_visibility',
	                'value' => array( 'catalog', 'visible' ),
	                'compare' => 'IN'
	            ),
				array(
					'key' => '_sale_price',
					'value' => '',
					'compare' => '='
				) ),
			'orderby' => 'date',
			'order' => 'DESC',
			'posts_per_page' => 4
		);
		$products = new WP_Query();
		$products -> query( $products_args );
		if ( $products -> have_posts() ) : ?>
		
		<div class="row title">
			<div class="large-8 columns">
				<h2><?php the_field( 'homepage-arrivals-title' ); ?></h2>
			</div>
			<div class="large-4 columns">
				<ul class="button-group right">
					<li><a href="<?php echo home_url( '/fresh-arrivals/' ); ?>" class="button more" data-type="fresh-arrivals" data-offset="0" data-total="<?php echo $products->found_posts; ?>">More</a></li>
					<li><a href="<?php echo home_url( '/fresh-arrivals/' ); ?>" class="button">View all fresh arrivals</a></li>
				</ul>
			</div>
		</div>
		
		<div class="row products">
			<?php while( $products -> have_posts() ) : $products -> the_post(); ?>
				<?php wc_get_template_part( 'content', 'product' ); ?>
			<?php endwhile; ?>
		</div>
		
		<?php endif; wp_reset_postdata(); ?>
		
	</div> <!-- end #latest-products -->
	
	<div class="call-to-action green">
		<div class="row">
			<div class="large-12 columns">
				<h1><?php the_field( 'homepage-call-to-action' ); ?></h1>
				<a href="<?php the_field( 'homepage-call-to-action-link' ); ?>" class="large button"><?php the_field( 'homepage-call-to-action-button' ); ?></a>
			</div>
		</div>
	</div> <!-- end .call-to-action -->
	
	<div id="sale-products">
		
		<?php 
		$products_args = array(	
			'post_type' => 'product',
			'meta_query' => array(
				array(
					'key' => '_visibility',
	                'value' => array( 'catalog', 'visible' ),
	                'compare' => 'IN'
	            ),
				array(
					'key' => '_sale_price',
					'value' =>  0,
					'compare' => '>',
					'type' => 'NUMERIC'
				) ),
			'orderby' => 'date',
			'order' => 'DESC',
			'posts_per_page' => 8
		);
		$products = new WP_Query( $products_args );
		if ( $products -> have_posts() ) : ?>
		
		<div class="row">
			<div class="large-8 columns">
				<h2><?php the_field( 'homepage-sale-title' ); ?></h2>
			</div>
			<div class="large-4 columns">
				<ul class="button-group right">
					<li><a href="<?php echo home_url( '/on-sale/' ); ?>" class="button more" data-type="on-sale" data-offset="0" data-total="<?php echo $products->found_posts; ?>">More</a></li>
					<li><a href="<?php echo home_url( '/on-sale/' ); ?>" class="button">View all on sale</a></li>
				</ul>
			</div>
		</div>
		
		<div class="row products">
			<?php while( $products -> have_posts() ) : $products -> the_post(); ?>
				<?php wc_get_template_part( 'content', 'product' ); ?>
			<?php endwhile; ?>
		</div>
		<?php endif; wp_reset_postdata(); ?>
		
	</div> <!-- end #sale-products -->

</div> <!-- end #main -->

<?php get_footer(); ?>