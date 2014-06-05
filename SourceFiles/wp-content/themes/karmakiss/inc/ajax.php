<?php

add_action( 'wp_enqueue_scripts', 'karmakiss_ajax_scripts' );  
add_action( 'wp_ajax_karmakiss_quick_view', 'karmakiss_quick_view' );
add_action( 'wp_ajax_nopriv_karmakiss_quick_view', 'karmakiss_quick_view' );
add_action( 'wp_ajax_karmakiss_frontpage_more', 'karmakiss_frontpage_more' );
add_action( 'wp_ajax_nopriv_karmakiss_frontpage_more', 'karmakiss_frontpage_more' );

function karmakiss_ajax_scripts() {
  
    wp_enqueue_script( 'karmakiss_ajax', get_template_directory_uri() . '/js/ajax.js', array( 'jquery' ));	
    wp_localize_script( 'karmakiss_ajax', 'KK_Ajax', array(
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'nonce' => wp_create_nonce( 'nonce' ))
    );
	
}

function karmakiss_quick_view() {
	
	// check nonce
	$nonce = $_POST['nonce'];
	if ( ! wp_verify_nonce( $nonce, 'nonce' ) )
		die();
	
	// display post quick view
	$post_id = $_POST['post_id'];
?>
<div class="row">
	<div class="medium-4 columns">
		<?php echo get_the_post_thumbnail( $post_id, 'shop_single_image_size' ); ?>
	</div>
	<div class="medium-8 columns">
		<h2><?php echo get_the_title( $post_id ) ?></h2>
		<div class="medium-6 columns">
			<h3>What you need to know</h3>
			<?php the_field( 'product-description-1', $post_id ) ?>
		</div>
		<div class="medium-6 columns">
			<h3>Why we like it</h3>
			<?php the_field( 'product-description-2', $post_id ) ?>
		</div>
	</div>
</div>
<a class="close-reveal-modal">&#215;</a>
<?php
	exit;
}

function karmakiss_frontpage_more() {
	
	// check nonce
	$nonce = $_POST['nonce'];
	if ( ! wp_verify_nonce( $nonce, 'nonce' ) )
		die();
	
	// load posts
	$products_args = array(
		'post_type' => 'product',
		'meta_query' => array( 
			array(
				'key' => '_visibility',
                'value' => array( 'catalog', 'visible' ),
                'compare' => 'IN'
            )
		),
		'orderby' => 'date',
		'order' => 'DESC',
        'offset' => $_POST['posts_offset'],
		'posts_per_page' => 4
	);
	if ( $_POST['posts_type'] == 'fresh-arrivals' )
		$products_args['meta_query'][] = array(
				'key' => '_sale_price',
				'value' =>  '',
				'compare'   => '='
			);
	elseif ( $_POST['posts_type'] == 'on-sale' )
		$products_args['meta_query'][] = array(
				'key' => '_sale_price',
				'value' =>  0,
				'compare'   => '>',
				'type'      => 'NUMERIC'
			);
	
	$products = new WP_Query( $products_args );
    while( $products -> have_posts() ) : $products -> the_post();
		wc_get_template_part( 'content', 'product' );
	endwhile;
	
	wp_reset_postdata();
	
	exit;
	
}

?>