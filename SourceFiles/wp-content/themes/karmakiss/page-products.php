<?php /* Template Name: Products Page */ ?>
<?php get_header(); ?>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

<div id="page-header">
	<div class="row">
		<div class="small-12 columns">
			<h1 id="page-title"><?php the_title(); ?></h1>
			<p id="page-description"><?php if ( get_field( 'page-description' ) ) the_field( 'page-description' ); ?></p>
		</div>
	</div>
</div> <!-- end #page-header -->

<div id="main" class="row">
	<div id="content" role="main">
		<?php the_content(); ?>
	</div> <!-- end #content -->
</div> <!-- end #main -->

<?php endwhile; endif; ?>

<?php get_footer(); ?>