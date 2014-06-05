<?php

// we're firing all out initial functions at the start
add_action('after_setup_theme','karmakiss_start', 16);

function karmakiss_start() {

    // launching operation cleanup
    add_action('init', 'karmakiss_head_cleanup');
    // remove WP version from RSS
    add_filter('the_generator', 'karmakiss_rss_version');
    // remove pesky injected css for recent comments widget
    add_filter( 'wp_head', 'karmakiss_remove_wp_widget_recent_comments_style', 1 );
    // clean up comment styles in the head
    add_action('wp_head', 'karmakiss_remove_recent_comments_style', 1);
    // clean up gallery output in wp
    add_filter('gallery_style', 'karmakiss_gallery_style');

    // enqueue base scripts and styles
    add_action('wp_enqueue_scripts', 'karmakiss_scripts_and_styles', 999);
    // ie conditional wrapper

    // launching this stuff after theme setup
    karmakiss_theme_support();

    // adding sidebars to Wordpress (these are created in functions.php)
    add_action( 'widgets_init', 'karmakiss_register_sidebars' );
    // adding the search form (created in functions.php)

    // cleaning up random code around images
    add_filter('the_content', 'karmakiss_filter_ptags_on_images');
    // cleaning up excerpt
    add_filter('excerpt_more', 'karmakiss_excerpt_more');

}

/*********************
WP_HEAD GOODNESS
The default wordpress head is a mess. 
Let's clean it up by removing all the junk we don't need.
*********************/

function karmakiss_head_cleanup() {
	// category feeds
	remove_action( 'wp_head', 'feed_links_extra', 3 );
	// post and comment feeds
	remove_action( 'wp_head', 'feed_links', 2 );
	// EditURI link
	remove_action( 'wp_head', 'rsd_link' );
	// windows live writer
	remove_action( 'wp_head', 'wlwmanifest_link' );
	// index link
	remove_action( 'wp_head', 'index_rel_link' );
	// previous link
	remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );
	// start link
	remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );
	// links for adjacent posts
	remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
	// WP version
	remove_action( 'wp_head', 'wp_generator' );
  // remove WP version from css
  add_filter( 'style_loader_src', 'karmakiss_remove_wp_ver_css_js', 9999 );
  // remove Wp version from scripts
  add_filter( 'script_loader_src', 'karmakiss_remove_wp_ver_css_js', 9999 );

} /* end head cleanup */

// remove WP version from RSS
function karmakiss_rss_version() { return ''; }

// remove WP version from scripts
function karmakiss_remove_wp_ver_css_js( $src ) {
    if ( strpos( $src, 'ver=' ) )
        $src = remove_query_arg( 'ver', $src );
    return $src;
}

// remove injected CSS for recent comments widget
function karmakiss_remove_wp_widget_recent_comments_style() {
   if ( has_filter('wp_head', 'wp_widget_recent_comments_style') ) {
      remove_filter('wp_head', 'wp_widget_recent_comments_style' );
   }
}

// remove injected CSS from recent comments widget
function karmakiss_remove_recent_comments_style() {
  global $wp_widget_factory;
  if (isset($wp_widget_factory->widgets['WP_Widget_Recent_Comments'])) {
    remove_action('wp_head', array($wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style'));
  }
}

// remove injected CSS from gallery
function karmakiss_gallery_style($css) {
  return preg_replace("!<style type='text/css'>(.*?)</style>!s", '', $css);
}


/*********************
SCRIPTS & ENQUEUEING
*********************/

// loading modernizr and jquery, and reply script
function karmakiss_scripts_and_styles() {
  global $wp_styles; // call global $wp_styles variable to add conditional wrapper around ie stylesheet the WordPress way
  if (!is_admin()) {

    // removes WP version of jQuery
    wp_deregister_script('jquery');
    
   // loads jQuery 2.1.0
    wp_enqueue_script( 'jquery', get_template_directory_uri() . '/js/vendor/jquery.js', array(), '2.1.0', false );
    
    // modernizr (without media query polyfill)
    wp_enqueue_script( 'karmakiss-modernizr', get_template_directory_uri() . '/js/vendor/modernizr.js', array(), '2.5.3', false );
    
    // adding Foundation scripts file in the footer
    wp_enqueue_script( 'foundation-js', get_template_directory_uri() . '/js/foundation.min.js', array( 'jquery' ), '', true );
   
    // register foundation stylesheet
    wp_enqueue_style( 'foundation-stylesheet', get_stylesheet_directory_uri() . '/css/foundation.min.css', array(), '', 'all' );
    
    // register normalize stylesheet
    //wp_enqueue_style( 'normalize-stylesheet', get_stylesheet_directory_uri() . '/css/normalize.css', array(), '', 'all' );
        
    // register main stylesheet
    wp_enqueue_style( 'karmakiss-stylesheet', get_stylesheet_directory_uri() . '/style.css', array(), '', 'all' );

    // comment reply script for threaded comments
    if ( is_singular() AND comments_open() AND (get_option('thread_comments') == 1)) {
      wp_enqueue_script( 'comment-reply' );
    }

    //adding scripts file in the footer
    wp_enqueue_script( 'karmakiss-js', get_stylesheet_directory_uri() . '/js/scripts.js', array( 'jquery' ), '', true );

    /*
    I recommend using a plugin to call jQuery
    using the google cdn. That way it stays cached
    and your site will load faster.
    */
    wp_enqueue_script( 'checkout-js', get_stylesheet_directory_uri() . '/js/jquery.checkout.js', array( 'jquery' ), '', true );

  }
}

/*********************
THEME SUPPORT
*********************/

// Adding WP 3+ Functions & Theme Support
function karmakiss_theme_support() {

	// wp thumbnails (sizes handled in functions.php)
	add_theme_support('post-thumbnails');

	// default thumb size
	set_post_thumbnail_size(125, 125, true);

	// rss 
	add_theme_support('automatic-feed-links');

	// wp menus
	add_theme_support( 'menus' );
	
	//html5 support (http://themeshaper.com/2013/08/01/html5-support-in-wordpress-core/)
	add_theme_support( 'html5', 
	         array( 
	         	'comment-list', 
	         	'comment-form', 
	         	'search-form', 
	         ) 
	);
	

} /* end theme support */

/*********************
RELATED POSTS FUNCTION
*********************/

// Related Posts Function (call using karmakiss_related_posts(); )
function karmakiss_related_posts() {
	echo '<ul id="karmakiss-related-posts">';
	global $post;
	$tags = wp_get_post_tags($post->ID);
	if($tags) {
		foreach($tags as $tag) { $tag_arr .= $tag->slug . ','; }
        $args = array(
        	'tag' => $tag_arr,
        	'numberposts' => 5, /* you can change this to show more */
        	'post__not_in' => array($post->ID)
     	);
        $related_posts = get_posts($args);
        if($related_posts) {
        	foreach ($related_posts as $post) : setup_postdata($post); ?>
	           	<li class="related_post"><a class="entry-unrelated" href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></li>
	        <?php endforeach; }
	    else { ?>
            <?php echo '<li class="no_related_post">' . __( 'No Related Posts Yet!', 'karmakiss' ) . '</li>'; ?>
		<?php }
	}
	wp_reset_query();
	echo '</ul>';
} /* end related posts function */

/*********************
PAGE NAVI
*********************/

// Numeric Page Navi (built into the theme by default)
function karmakiss_page_navi($before = '', $after = '') {
	global $wpdb, $wp_query;
	$request = $wp_query->request;
	$posts_per_page = intval(get_query_var('posts_per_page'));
	$paged = intval(get_query_var('paged'));
	$numposts = $wp_query->found_posts;
	$max_page = $wp_query->max_num_pages;
	if ( $numposts <= $posts_per_page ) { return; }
	if(empty($paged) || $paged == 0) {
		$paged = 1;
	}
	$pages_to_show = 7;
	$pages_to_show_minus_1 = $pages_to_show-1;
	$half_page_start = floor($pages_to_show_minus_1/2);
	$half_page_end = ceil($pages_to_show_minus_1/2);
	$start_page = $paged - $half_page_start;
	if($start_page <= 0) {
		$start_page = 1;
	}
	$end_page = $paged + $half_page_end;
	if(($end_page - $start_page) != $pages_to_show_minus_1) {
		$end_page = $start_page + $pages_to_show_minus_1;
	}
	if($end_page > $max_page) {
		$start_page = $max_page - $pages_to_show_minus_1;
		$end_page = $max_page;
	}
	if($start_page <= 0) {
		$start_page = 1;
	}
	echo $before.'<nav class="page-navigation"><ul class="pagination">'."";
	if ($start_page >= 2 && $pages_to_show < $max_page) {
		$first_page_text = __( "First", 'karmakiss' );
		echo '<li class="bpn-first-page-link"><a href="'.get_pagenum_link().'" title="'.$first_page_text.'">'.$first_page_text.'</a></li>';
	}
	echo '<li class="bpn-prev-link">';
	previous_posts_link('<<');
	echo '</li>';
	for($i = $start_page; $i  <= $end_page; $i++) {
		if($i == $paged) {
			echo '<li class="current"><a href="'.get_pagenum_link($i).'">'.$i.'</a></li>';
		} else {
			echo '<li><a href="'.get_pagenum_link($i).'">'.$i.'</a></li>';
		}
	}
	echo '<li class="bpn-next-link">';
	next_posts_link('>>');
	echo '</li>';
	if ($end_page < $max_page) {
		$last_page_text = __( "Last", 'karmakiss' );
		echo '<li class="bpn-last-page-link"><a href="'.get_pagenum_link($max_page).'" title="'.$last_page_text.'">'.$last_page_text.'</a></li>';
	}
	echo '</ul></nav>'.$after."";
} /* end page navi */

/*********************
ADD FOUNDATION FEATURES TO WORDPRESS
*********************/
// Add "has-dropdown" CSS class to navigation menu items that have children in a submenu.
function nav_menu_item_parent_classing( $classes, $item )
{
    global $wpdb;
    
$has_children = $wpdb -> get_var( "SELECT COUNT(meta_id) FROM {$wpdb->prefix}postmeta WHERE meta_key='_menu_item_menu_item_parent' AND meta_value='" . $item->ID . "'" );
    
    if ( $has_children > 0 )
    {
        array_push( $classes, "has-dropdown" );
    }
    
    return $classes;
}
 
add_filter( "nav_menu_css_class", "nav_menu_item_parent_classing", 10, 2 );

//Deletes empty classes and changes the sub menu class name
    function change_submenu_class($menu) {
        $menu = preg_replace('/ class="sub-menu"/',' class="dropdown"',$menu);
        return $menu;
    }
    add_filter ('wp_nav_menu','change_submenu_class');


//Use the active class of the ZURB Foundation for the current menu item. (From: https://github.com/milohuang/reverie/blob/master/functions.php)
function required_active_nav_class( $classes, $item ) {
    if ( $item->current == 1 || $item->current_item_ancestor == true ) {
        $classes[] = 'active';
    }
    return $classes;
}
add_filter( 'nav_menu_css_class', 'required_active_nav_class', 10, 2 );

/* RANDOM CLEANUP */

// remove extra p tags around imgs
function karmakiss_filter_ptags_on_images( $content ) {
	return preg_replace( '/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content );
}

// custom excerpt read more link
function karmakiss_excerpt_more( $more ) {
	global $post;
	return '...  <a class="read-more" href="' . get_permalink($post->ID) . '" title="Read ' . get_the_title($post->ID) . '">Continue Reading</a>';
}



/*
 * This is a modified the_author_posts_link() which just returns the link.
 *
 * This is necessary to allow usage of the usual l10n process with printf().
 */
function karmakiss_get_the_author_posts_link() {
	global $authordata;
	if ( !is_object( $authordata ) )
		return false;
	$link = sprintf(
		'<a href="%1$s" title="%2$s" rel="author">%3$s</a>',
		get_author_posts_url( $authordata->ID, $authordata->user_nicename ),
		esc_attr( sprintf( __( 'Posts by %s' ), get_the_author() ) ), // No further l10n needed, core will take care of this one
		get_the_author()
	);
	return $link;
}
?>