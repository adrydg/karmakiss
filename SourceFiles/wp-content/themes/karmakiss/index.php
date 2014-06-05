<?php get_header( 'blog' ); ?>

<div id="main" class="row">
	
	<div id="content" class="medium-9 columns blog" role="main">
		<div id="inner-content" class="clearfix">
			
			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
			
			<article <?php post_class( 'clearfix' ); ?> role="article">
				
				<?php if ( has_post_thumbnail() ) : ?>
				<div class="medium-5 columns">
					<a href="<?php the_permalink() ?>"><?php the_post_thumbnail( 'blog-small' ); ?></a>
				</div>
				<div class="medium-7 columns">					
				<?php else : ?>
				<div class="small-12 columns">	
				<?php endif; ?>		
					
					<header>
						<p><span class="date"><?php the_time( 'd M Y' ); ?></span> Written by <span class="author"><?php the_author_meta( 'first_name' ); ?></span></p>
						<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
					</header> <!-- end article header -->
					
					<section class="entry-content">
						<?php the_excerpt(); ?>
					</section> <!-- end article section -->
				
				</div>
			
			</article> <!-- end article -->
			
			<?php endwhile; ?>
			
			<ul>
				<li id="next-link"><?php previous_posts_link( 'Previous' ) ?></li>
				<li id="prev-link"><?php next_posts_link( 'Next' ) ?></li>
			</ul>
			
			<?php endif; ?>
			
		</div> <!-- end #inner-content -->
	</div> <!-- end #content -->
	
	<?php get_sidebar(); ?>
	
</div> <!-- end #main -->

<?php get_footer(); ?>