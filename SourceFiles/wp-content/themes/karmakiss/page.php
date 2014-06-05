<?php get_header(); ?>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

<div id="page-header">
	<div class="row">
		<div class="small-12 columns">
			<h1 id="page-title"><?php the_title(); ?></h1>
			<?php if ( get_field( 'page-tagline' ) ) : ?><p id="page-description"><?php the_field( 'page-tagline' ); ?></p><?php endif; ?>
		</div>
	</div>
</div> <!-- end #page-header -->

<div id="main" class="row">
	<div id="content" class="small-12 columns" role="main">
		<div id="inner-content">
			<article <?php post_class(); ?> role="article" itemscope itemtype="http://schema.org/WebPage">
				<?php the_content(); ?>
			</article>
		</div> <!-- end #inner-content -->
	</div> <!-- end #content -->
</div> <!-- end #main -->

<?php endwhile; endif; ?>

<?php get_footer(); ?>