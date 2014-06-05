<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div id="filters" class="large-3 columns left">
	
	<?php if ( is_product_category() ) : ?>
	<h3>Explore Categories</h3>
	<ul class="side-nav">
		<?php
		global $wp_query;
		$term = $wp_query->get_queried_object();
		if ($term->parent == 0)  
			wp_list_categories('taxonomy=product_cat&depth=1&show_count=0&hide_empty=0&title_li=&child_of=' . $term->term_id);
		else
			wp_list_categories('taxonomy=product_cat&show_count=0&hide_empty=0&title_li=&child_of=' . $term->parent);	
		?>
	</ul>
	<?php endif; ?>
	
	<h3>Sort By</h3>
	
	<dl class="accordion" data-accordion>
		<?php dynamic_sidebar( 'shop' ); ?>
	</dl>
	
	<script type="text/javascript">
		var filter_no = 1;
		$( "#filters .content").each( function() {
			$( this ).attr( "id", "panel" + filter_no );
			$( this ).parent().find ( ".title" ).attr( "href", "#panel" + filter_no );
			filter_no++;
		});
	</script>
	
</div> <!-- end #filters -->