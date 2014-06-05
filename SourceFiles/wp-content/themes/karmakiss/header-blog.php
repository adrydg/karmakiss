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
				<?php else : ?>
				<li><a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>" class="button">Login</a></li>
				<li><a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>" class="button register">Register</a></li>
				<?php endif; ?>
			</ul>
		</div>
	</div>
</div> <!-- end #top -->
<header id="header" class="blog" role="banner">
	
	<div class="row">
		<div class="medium-5 columns">
			<h1 id="logo"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><img src="<?php echo get_stylesheet_directory_uri() ?>/img/logo.png" alt="<?php bloginfo( 'name' ); ?>" /></a></h1>
		</div>
		<div class="medium-7 columns">
			<nav id="blog-nav" class="top-bar" data-topbar>
				<ul class="title-area">
					<li class="name"></li>
					<li class="toggle-topbar menu-icon"><a href="#"><span>Menu</span></a></li>
				</ul>		
				<section class="top-bar-section clearfix">
					<?php wp_nav_menu( array( 'container' => false, 'theme_location' => 'blog-nav', 'depth' => '1' ) ); ?>
				</section>
			</nav>
		</div>
	</div>
	
</header> <!-- end #header -->