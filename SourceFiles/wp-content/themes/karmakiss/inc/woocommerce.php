<?php

/* WOOCOMMERCE SUPPORT */

add_action( 'after_setup_theme', 'karmakiss_woocommerce_support' );

function karmakiss_woocommerce_support() {
    add_theme_support( 'woocommerce' );
}

/* DISABLE CSS */

add_filter( 'woocommerce_enqueue_styles', '__return_false' );

/* ADD IMAGE SIZES */

//hook in on activation
global $pagenow;
if ( is_admin() && isset( $_GET['activated'] ) && $pagenow == 'themes.php' ) add_action( 'init', 'karmakiss_woocommerce_image_dimensions', 1 );
 
// define image sizes
function karmakiss_woocommerce_image_dimensions() {
  	$catalog = array(
		'width' 	=> '280',
		'height'	=> '280',
		'crop'		=> 1
	);
 
	$single = array(
		'width' 	=> '560',
		'height'	=> '350',
		'crop'		=> 0
	);
 
	$thumbnail = array(
		'width' 	=> '80',
		'height'	=> '80',
		'crop'		=> 1
	);
 
	// Image sizes
	update_option( 'shop_catalog_image_size', $catalog ); 		// product category thumbs
	update_option( 'shop_single_image_size', $single ); 		// single product image
	update_option( 'shop_thumbnail_image_size', $thumbnail ); 	// smage gallery thumbs
}

/* UNHOOKS */

remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );

// single product
remove_action ( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
add_action( 'woocommerce_after_single_product', 'woocommerce_upsell_display', 10 );

//checkout
remove_action( 'register_form', 'addthis_ssi_render_buttons' );
remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_login_form', 10 ); 

/* CHECKOUT FIELDS */

add_filter( 'woocommerce_checkout_fields' , 'karmakiss_override_checkout_fields' );

function karmakiss_override_checkout_fields( $fields ) {
	
	$fields['billing']['billing_email']['placeholder'] = 'E-mail';
	$fields['billing']['billing_email']['class'] = array('small-12 columns');
	$fields['billing']['billing_first_name']['placeholder'] = 'First Name';
	$fields['billing']['billing_first_name']['class'] = array('small-12 columns');
	$fields['billing']['billing_last_name']['placeholder'] = 'Last Name';
	$fields['billing']['billing_last_name']['class'] = array('small-12 columns');
	$fields['billing']['billing_address_1']['placeholder'] = 'Address';
	$fields['billing']['billing_address_1']['class'] = array('small-12 medium-8 columns');
	$fields['billing']['billing_postcode']['placeholder'] = 'Zip Code';
	$fields['billing']['billing_postcode']['class'] = array('small-12 medium-4 columns');
	$fields['billing']['billing_city']['placeholder'] = 'City';
	$fields['billing']['billing_city']['class'] = array('small-12 medium-4 columns');
	$fields['billing']['billing_state']['placeholder'] = 'State';
	$fields['billing']['billing_state']['class'] = array('small-12 medium-4 columns');
	$fields['billing']['billing_country']['placeholder'] = 'Country';
	$fields['billing']['billing_country']['class'] = array('small-12 medium-4 columns');
	
	$fields['shipping']['shipping_first_name']['placeholder'] = 'First Name';
	$fields['shipping']['shipping_first_name']['class'] = array('small-12 columns');
	$fields['shipping']['shipping_last_name']['placeholder'] = 'Last Name';
	$fields['shipping']['shipping_last_name']['class'] = array('small-12 columns');
	$fields['shipping']['shipping_address_1']['placeholder'] = 'Address';
	$fields['shipping']['shipping_address_1']['class'] = array('small-12 medium-8 columns');
	$fields['shipping']['shipping_postcode']['placeholder'] = 'Zip Code';
	$fields['shipping']['shipping_postcode']['class'] = array('small-12 medium-4 columns');
	$fields['shipping']['shipping_city']['placeholder'] = 'City';
	$fields['shipping']['shipping_city']['class'] = array('small-12 medium-4 columns');
	$fields['shipping']['shipping_state']['placeholder'] = 'State';
	$fields['shipping']['shipping_state']['class'] = array('small-12 medium-4 columns');
	$fields['shipping']['shipping_country']['placeholder'] = 'Country';
	$fields['shipping']['shipping_country']['class'] = array('small-12 medium-4 columns');
    
    $fields['order']['order_comments']['label'] = '';
	$fields['order']['order_comments']['placeholder'] = '';
	$fields['order']['order_comments']['class'] = array('small-12 columns');
	
	$fields2['billing']['billing_email'] = $fields['billing']['billing_email'];
	$fields2['billing']['billing_first_name'] = $fields['billing']['billing_first_name'];
	$fields2['billing']['billing_last_name'] = $fields['billing']['billing_last_name'];
	$fields2['billing']['billing_address_1'] = $fields['billing']['billing_address_1'];
	$fields2['billing']['billing_postcode'] = $fields['billing']['billing_postcode'];
	$fields2['billing']['billing_city'] = $fields['billing']['billing_city'];
	$fields2['billing']['billing_state'] = $fields['billing']['billing_state'];
	$fields2['billing']['billing_country'] = $fields['billing']['billing_country'];
	
	$fields2['shipping']['shipping_first_name'] = $fields['shipping']['shipping_first_name'];
	$fields2['shipping']['shipping_last_name'] = $fields['shipping']['shipping_last_name'];
	$fields2['shipping']['shipping_address_1'] = $fields['shipping']['shipping_address_1'];
	$fields2['shipping']['shipping_postcode'] = $fields['shipping']['shipping_postcode'];
	$fields2['shipping']['shipping_city'] = $fields['shipping']['shipping_city'];
	$fields2['shipping']['shipping_state'] = $fields['shipping']['shipping_state'];
	$fields2['shipping']['shipping_country'] = $fields['shipping']['shipping_country'];
	
	$fields2['account'] = $fields['account'];
	$fields2['order'] = $fields['order'];
	
	return $fields2;
	
}

// Disable shipping costs from cart page
function karmakiss_block_shipping_estimate_cart( $show_shipping ) {
	if( is_cart() )
		return false;
	return true;
}

add_filter( 'woocommerce_cart_ready_to_calc_shipping', 'karmakiss_block_shipping_estimate_cart' );

// update global product add-ons for gift wrap/card
function karmakiss_update_global_custom_addons( $post_id ) {
	
	if ( wp_is_post_revision( $post_id ) || $_POST['post_type']  != 'product' || ( $_POST['post_title'] != 'Gift Wrap' && $_POST['post_title'] != 'Greeting Card' ) )
		return;
	
	$addon = $_POST['post_title'];
	$variations = $_POST['attribute_design'];
	$variations_prices = $_POST['variable_regular_price'];
	$variations_images = $_POST['upload_image_id'];
	$addons_post = get_page_by_title( 'Gift Wraps & Greeting Cards', 'OBJECT', 'global_product_addon' );
	$addons_id = $addons_post->ID;
	$addons = get_post_meta( $addons_id, '_product_addons', true );
	$updated_addon_options = array();
	
	foreach( $variations as $index => $variation )
		$updated_addon_options[] = array( 'label' => ucwords( str_replace( '-', ' ', $variation) ), 'price' => $variations_prices[$index], 'image' => $variations_images[$index], 'min' => '', 'max' => '' );
	
	if ( $addon == 'Gift Wrap')
		$addons[0]['options'] = $updated_addon_options;
	elseif ( $addon == 'Greeting Card' )
		$addons[1]['options'] = $updated_addon_options;
	
	update_post_meta( $addons_id, '_product_addons', $addons );
	
}
add_action( 'save_post', 'karmakiss_update_global_custom_addons' );

function wc_get_product_ids_bestseller() {
	
	// Load from cache
	$bestselling_product_ids = get_transient( 'wc_bestselling_products' );
	
	// Valid cache found
	if ( false !== $bestselling_product_ids )
		return $bestselling_product_ids;
	
	$bestseller = get_posts( array(
		'post_type'      => array( 'product', 'product_variation' ),
		'posts_per_page' => 50,
		'post_status'    => 'publish',
		'meta_key' => 'total_sales',
		'orderby' => 'meta_value_num',
		'fields' => 'id=>parent'
	) );
	$product_ids = array_keys( $bestseller );
	$parent_ids = array_values( $bestseller );
	$bestselling_product_ids = array_unique( array_merge( $product_ids, $parent_ids ) ); 
	set_transient( 'wc_bestselling_products', $bestselling_product_ids, YEAR_IN_SECONDS );
	
	return $bestselling_product_ids;
	
}

// PERMALINKS

?>