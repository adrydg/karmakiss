<?php /* Template Name: Two Columns Page */ ?>
<?php get_header(); ?>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

<div id="page-header">
	<div class="row">
		<div class="large-12 columns">
			<h1 id="page-title"><?php the_title(); ?></h1>
			<p id="page-description"><?php if ( get_field( 'page-description' ) ) the_field( 'page-description' ); ?></p>
		</div>
	</div>
</div> <!-- end #page-header -->

<div id="main" class="row">
	<div id="content" class="page" role="main">
		<div class="medium-6 columns">
			<div class="inner-content clearfix">
				<?php the_field( 'first-col' ); ?>
			</div> <!-- end .inner-content -->
		</div>
		<div class="medium-6 columns">
			<div class="inner-content clearfix">
				<?php the_field( 'second-col' ); ?>
			</div> <!-- end .inner-content -->
		</div>
	</div> <!-- end #content -->
</div> <!-- end #main -->

<?php endwhile; endif; ?>

<?php get_footer(); ?>