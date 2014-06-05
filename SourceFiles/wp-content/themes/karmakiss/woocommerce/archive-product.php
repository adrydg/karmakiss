<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php get_header(); ?>

<div id="page-header" class="archive">
	<div class="row">
		<div class="large-12 columns">
			<h1 id="page-title"><?php woocommerce_page_title(); ?></h1>
			<?php do_action( 'woocommerce_archive_description' ); ?>
		</div>
	</div>
</div> <!-- end #page-header -->

<?php do_action( 'woocommerce_before_main_content' ); ?>

<?php if ( have_posts() ) : ?>
<div class="row">
<?php do_action( 'woocommerce_before_shop_loop' ); ?>

<?php woocommerce_product_loop_start(); ?>

<?php while ( have_posts() ) : the_post(); ?>

	<?php wc_get_template_part( 'content', 'product' ); ?>

<?php endwhile; ?>

<?php woocommerce_product_loop_end(); ?>

<?php do_action( 'woocommerce_after_shop_loop' ); ?>
</div>
<?php elseif ( !woocommerce_product_subcategories( array( 'before' => woocommerce_product_loop_start( false ), 'after' => woocommerce_product_loop_end( false ) ) ) ) : ?>

<?php wc_get_template( 'loop/no-products-found.php' ); ?>

<?php endif; ?>

<?php do_action( 'woocommerce_after_main_content' ); ?>

<?php get_footer(); ?>