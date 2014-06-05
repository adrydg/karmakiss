<?php get_header( 'blog' ); ?>

<div id="main" class="row">
	
	<div id="content" class="medium-9 columns blog" role="main">
		<div id="inner-content" class="clearfix">
			
			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
			
			<article <?php post_class( 'single clearfix' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
				
				<?php if ( has_post_thumbnail() ) : ?>
				<div id="entry-image" class="small-12 columns">
					<?php the_post_thumbnail( 'blog-large' ); ?>
				</div>
				<?php endif; ?>
				
				<header class="medium-4 columns">
					<div id="entry-meta">
						<h3>Post Details</h3>
						<div class="row collapse">
							<p class="title small-6 medium-5 columns">Date</p>
							<p class="small-6 medium-7 columns"><?php the_time( 'd F Y' ); ?></p>
						</div>
						<div class="row collapse">
							<p class="title small-6 medium-5 columns">Author</p>
							<p class="small-6 medium-7 columns"><?php the_author_meta( 'first_name' ); ?></p>
						</div>
						<div class="row collapse">
							<p class="title small-6 medium-5 columns">Category</p>
							<p class="small-6 medium-7 columns">
							<?php $cats = wp_get_post_categories( $post -> ID ); 
							foreach ( $cats as $cat ) : $c = get_category( $cat ); ?>
							<span class="label"><?php echo $c -> name; ?></span>
							<?php endforeach; ?>
							</p>
						</div>
						<div class="row collapse">
							<p class="title small-6 medium-5 columns">Tags</p>
							<p class="small-6 medium-7 columns"><?php $tags = wp_get_post_tags( $post -> ID ); foreach ( $tags as $tag ) { $t = get_tag( $tag ); echo $t -> name . ' '; } ?></p>
						</div>
					</div>
					<div id="entry-share">
						<h3>Share with friends</h3>
						<?php echo do_shortcode( '[easy-share]' ); ?>
					</div>
				</header>
				
				<section class="entry-content medium-8 columns">
					<h2 id="single-title"><?php the_title(); ?></h2>
					<?php the_content(); ?>
				</section>
				
			</article> <!-- end article -->
			
			<?php endwhile; ?>
						
			<?php endif; ?>
			
		</div> <!-- end #inner-content -->
	</div> <!-- end #content -->
	
	<?php get_sidebar(); ?>
	
</div> <!-- end #main -->

<?php get_footer(); ?>
