<?php if ( !defined( 'YITH_WCAS' ) ) { exit; } ?>
<?php wp_enqueue_script('yith_wcas_jquery-autocomplete' ); ?>

<div class="yith-ajaxsearchform-container">
<form role="search" method="get" id="yith-ajaxsearchform" action="<?php echo esc_url( home_url( '/'  ) ) ?>">
    <div class="row collapse">
      	<div class="large-10 small-9 columns">
        <label class="screen-reader-text" for="yith-s"><?php _e( 'Search for:', 'yit' ) ?></label>
        <input type="search" value="<?php echo get_search_query() ?>" name="s" id="yith-s" class="small-8 columns" placeholder="<?php echo get_option('yith_wcas_search_input_label') ?>" />
      	</div>
      	<div class="large-2 small-3 columns">
        <input type="submit" id="yith-searchsubmit" class="button expand" value="<?php echo get_option('yith_wcas_search_submit_label') ?>" />
        <input type="hidden" name="post_type" value="product" />
        </div>
    </div>
</form>
</div>

<script type="text/javascript">
jQuery(function($){
    var search_loader_url = <?php echo apply_filters('yith_wcas_ajax_search_icon', 'woocommerce_params.ajax_loader_url') ?>;

    $('#yith-s').autocomplete({
        minChars: <?php echo get_option('yith_wcas_min_chars') * 1; ?>,
        appendTo: '.yith-ajaxsearchform-container',
        serviceUrl: woocommerce_params.ajax_url + '?action=yith_ajax_search_products',
        onSearchStart: function(){
            $(this).css('background-image', 'url('+search_loader_url+') no-repeat right center');
        },
        onSelect: function (suggestion) {
            if( suggestion.id != -1 ) {
                window.location.href = suggestion.url;
            }
        }
    });
});
</script>