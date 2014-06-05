<!doctype html>

<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" <?php language_attributes(); ?>> <![endif]-->
<!--[if gt IE 8]><!--> <html <?php language_attributes(); ?>> <!--<![endif]-->

<head>

<meta charset="utf-8">

<title><?php echo get_bloginfo('name'); ?><?php wp_title(); ?></title>

<!-- Google Chrome Frame for IE -->
<?php
if (isset($_SERVER['HTTP_USER_AGENT']) &&
	(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false))
		header('X-UA-Compatible: IE=edge,chrome=1'); 
?>

<!-- mobile meta -->
<meta name="HandheldFriendly" content="True">
<meta name="MobileOptimized" content="320">
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>

<!-- icons & favicons -->
<link rel="apple-touch-icon" href="<?php echo get_template_directory_uri(); ?>/library/images/apple-icon-touch.png">
<link rel="icon" href="<?php echo get_template_directory_uri(); ?>/favicon.png">
<!--[if IE]>
<link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/favicon.ico">
<![endif]-->
<meta name="msapplication-TileColor" content="#f01d4f">
<meta name="msapplication-TileImage" content="<?php echo get_template_directory_uri(); ?>/library/images/win8-tile-icon.png">

<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">

<?php wp_head(); ?>

</head>

<body <?php body_class(); ?>>

<div id="top">
	<div class="row">
		<div class="medium-4 columns">
			<p><?php the_field( 'header-top-left', 'options' ); ?></p>
		</div>
		<div class="medium-8 columns">
			<ul class="button-group right">
				<?php if ( is_user_logged_in() ) : ?>
				<li><a href="<?php echo home_url( '/wishlist/' ); ?>" class="button">Wishlist</a></li>
				<li><a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>" class="button">Account</a></li>
				<li><a href="<?php echo wp_logout_url( home_url() ); ?>" class="button">Log Out</a></li>
				<?php else : ?>
				<li><a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>" class="button">Log In</a></li>
				<li><a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>" class="button register">Register</a></li>
				<?php endif; ?>
				<li>
					<a href="<?php echo get_permalink( get_option('woocommerce_cart_page_id') ); ?>" class="button cart <?php if ( sizeof( WC()->cart->get_cart() ) > 0 ) echo 'active'; ?>" data-dropdown="mini-cart" data-options="is_hover:true"><span><?php global $woocommerce; echo sizeof( WC()->cart->get_cart() ); ?></span> Shopping Cart</a>
					<div id="mini-cart" class="f-dropdown content" data-dropdown-content>
					<h3>Items in your shopping cart</h3>
					<?php if ( sizeof( WC() -> cart -> get_cart() ) > 0 ) : ?>
					<ul>
						<li class="row title">
							<span class="large-6 columns">Item</span>
							<span class="large-3 columns quantity">Quantity</span>
							<span class="large-3 columns price">Price</span>
						</li>
						<?php foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) : ?>
						<?php
						$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
						$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
						?>
						<li class="row">
							<span class="large-6 columns"><?php echo $_product->get_title(); ?></span>
							<span class="large-3 columns quantity"><?php echo $cart_item['quantity']; ?></span>
							<span class="large-3 columns price"><?php echo WC()->cart->get_product_price( $_product ); ?></span>
						</li>
						<?php endforeach; ?>
						<li class="row total">
							<span class="large-12 columns">Total: <?php echo WC()->cart->get_cart_subtotal(); ?></span>
						</li>
					</ul>
					<a href="<?php echo WC()->cart->get_cart_url(); ?>" class="button expand">Secure Checkout</a>
					<?php else : ?>
					<p>No products in the cart.</p>
					<?php endif; ?>
					</div>
				</li>
			</ul>
		</div>
	</div>
</div> <!-- end #top -->

<header id="header" role="banner">
	
	<div class="row">
		<div class="medium-7 columns">
			<h1 id="logo"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><img src="<?php echo get_template_directory_uri(); ?>/img/logo.png" alt="<?php bloginfo( 'name' ); ?>" /></a></h1>
		</div>
		<div class="medium-5 columns">
			<?php echo do_shortcode( '[yith_woocommerce_ajax_search]' ); ?>
		</div>
	</div>
	
	<?php if ( ! is_checkout() ) : ?>
	<div class="row">
		<div class="large-12 columns">
			<nav id="main-nav" class="top-bar" data-topbar>
				<ul class="title-area">
					<li class="name"></li>
					<li class="toggle-topbar menu-icon"><a href="#"></a></li>
				</ul>		
				<section class="top-bar-section">
					<?php
					$categories = get_terms( 'product_cat', 'parent=0&hide_empty=0' ); $main_category_no = 1; $main_category_total = sizeof( $categories ); 
						$category_deals = get_option('_s_category_pricing_rules');
						$deals_id = array();
						$deals_percentage = array();
						foreach ( $category_deals as $category_deal )
							if ($category_deal['rules'][0]['type']) {
								$deals_id[] = $category_deal['collector']['args']['cats'][0];
								$deals_percentage[] = $category_deal['rules'][0]['amount'];
							}
					?>
					<ul>
					<li><a href="<?php echo home_url(); ?>">Home</a></li>
					<?php foreach ( $categories as $category ) : ?>
						<?php if ( sizeof( get_term_children( $category -> term_id, 'product_cat' ) ) ) : ?>
						<li class="has-dropdown not-click">
							<a href="<?php echo get_term_link( (int)$category -> term_id, 'product_cat' ); ?>"><?php echo $category -> name; ?></a>
							<ul class="dropdown dropdown-wrapper row collapse <?php if ( $main_category_no > $main_category_total / 2 ) echo 'overflow-fix'; ?>">
								<li>
									<?php
									$categories = get_terms( 'product_cat', array ( 'parent' => $category -> term_id, 'hide_empty' => 0 ) ); 
									$category_no = 1;
									$category_total = sizeof( $categories );
									$show_deal = false;
									$category_deal = false;
									$category_percentage = false;
									$category_thumb = false;
									?>
									<div class="small-8 columns">
									<?php foreach( $categories as $category ) : ?>
										<?php if ( $category_no == 1 || $category_no == round( $category_total / 2 ) + 1 ) : ?>
										<ul class="small-4 columns left">
										<?php endif; ?>
										<li><a href="<?php echo get_term_link( (int)$category -> term_id, 'product_cat' ); ?>"><?php echo $category -> name; ?></a></li>
										<?php
										if ( in_array( $category -> term_id , $deals_id ) ) {
											$show_deal = $category -> term_id;
											$category_deal = $category -> name;
											$category_percentage =$deals_percentage[array_search( $category -> term_id , $deals_id )];
										}
										if ( get_field( 'subcategory-menu-image', 'product_cat_' . $category -> term_id ) ) {
											$category_thumb = $category;
											$thumbnail = get_woocommerce_term_meta( $category -> term_id, 'thumbnail_id', true );
										} 
										?>
										<?php if ( $category_no == round( $category_total / 2 ) || $category_no == $category_total ) : ?>
										</ul>
										<?php endif; ?>
									<?php $category_no++; endforeach; ?>
									<?php if ( $category_no > round( $category_total / 2 ) && $show_deal ) : ?>
									<div class="deal small-12 columns left">
										<p><a href="<?php echo get_term_link( (int)$show_deal, 'product_cat' ); ?>" class="button small">Deal</a>Take <?php echo $category_percentage; ?>% off <?php echo $category_deal; ?></p>
									</div>
									<?php endif; ?>
									</div>
									<?php if ( $category_thumb ) : ?>
										<ul class="small-4 columns image">
											<li><h3><a href="<?php echo get_term_link( (int)$category_thumb -> term_id, 'product_cat' ); ?>"><?php echo $category_thumb -> name; ?></a></h3></li>
											<li><a href="<?php echo get_term_link( (int)$category_thumb -> term_id, 'product_cat' ); ?>"><?php echo wp_get_attachment_image( $thumbnail, 'product-category-menu' ); ?></a></li>
										</ul>
									<?php $category_thumb = false; endif; ?>
								</li>
							</ul>
						</li>
						<?php else : ?>
						<li>
							<a href="<?php echo get_term_link( (int)$category -> term_id, 'product_cat' ); ?>"><?php echo $category -> name; ?></a>
						</li>
						<?php endif; ?>
					<?php $main_category_no++; endforeach; ?>
					</ul>
				</section>
			</nav>
		</div>
	</div>
	<?php endif; ?>
	
</header> <!-- end #header -->