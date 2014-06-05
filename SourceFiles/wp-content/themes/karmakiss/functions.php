<?php

ob_start();

/* DEPENDENCIES */

require_once( 'inc/core.php' );
require_once( 'inc/woocommerce.php' );
require_once( 'inc/ajax.php' );

/* THUMBNAILS */

add_image_size( 'homepage-slide', 1180, 425, true );
add_image_size( 'homepage-banner-medium', 380, 420, true );
add_image_size( 'homepage-banner-small', 380, 200, true );
add_image_size( 'blog-large', 840, 400, true );
add_image_size( 'blog-small', 350, 250, true );
add_image_size( 'product-category', 393, 390, true );
add_image_size( 'product-category-menu', 210, 200, true );
add_image_size( 'cart-item', 200, 200, true );
add_image_size( 'product-addon', 150, 70, true );

/* MENUS */

register_nav_menus( array(
	'blog-nav' => 'Blog Navigation',
	'footer' => 'Footer Links'
) );

/* SIDEBARS */

function karmakiss_register_sidebars() {
	
    register_sidebar( array(
    	'id' => 'sidebar',
    	'name' => 'Blog sidebar',
    	'description' => 'Sidebar shown on the right side of the blog section.',
    	'before_widget' => '<section class="widget %2$s">',
    	'after_widget' => '</section>',
    	'before_title' => '<h3 class="widget-title">',
    	'after_title' => '</h3>'
    ) );
    
    register_sidebar( array(
    	'id' => 'shop',
    	'name' => 'Shop sidebar',
    	'description' => 'Sidebar shown on the left side of product listings.',
    	'before_widget' => '<dd>',
    	'after_widget' => '</div></dd>',
    	'before_title' => '<a class="title" href="#"><h4>',
    	'after_title' => '</h4></a><div class="content">'
    ) );
    
}

function karmakiss_responsive_embed( $html ) {
    return '<div class="flex-video">' . $html . '</div>';
}
add_filter( 'embed_oembed_html', 'karmakiss_responsive_embed', 10, 3 );

/* TITLE CUSTOM SIZE */

function short_title($after = null, $length) {
	$mytitle = get_the_title();
	$size = strlen($mytitle);
	if($size>$length) {
		$mytitle = substr($mytitle, 0, $length);
		$mytitle = explode(' ',$mytitle);
		array_pop($mytitle);
		$mytitle = implode(" ",$mytitle).$after;
	}
	return $mytitle;
}

/* CUSTOM FIELDS OPTION PAGE */

function my_acf_options_page_settings( $settings )
{
	$settings['title'] = 'Options';
	$settings['pages'] = array( 'Header', 'Footer', 'Product Page', 'Cart & Checkout' );
 
	return $settings;
}
 
add_filter('acf/options_page/settings', 'my_acf_options_page_settings');

add_action( 'pre_get_posts', 'foo_modify_query_exclude_category' );
function foo_modify_query_exclude_category( $query ) {
    if ( ! is_admin() && $query->is_main_query() )
		$query->query_vars['posts_per_page'] = 9;
}

/* DISABLE PLUGIN STYLES */
add_action( 'wp_print_styles', 'karmakiss_deregister_styles', 100 );

function karmakiss_deregister_styles() {
	wp_deregister_style( 'woocommerce-addons-css' );
}

?>