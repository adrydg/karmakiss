(function($) {
	$(document).ready(function(){
		
		// product quick view
		$('.products').on( 'click', '.product .quick-view .button', function(e){
			e.preventDefault();
			button = $(this);
			container = $(this).parent().parent();
			button.text('Loading');
			$.post(
				KK_Ajax.ajaxurl,
				{
					action : 'karmakiss_quick_view',
					post_id : button.attr('data-id'),
					nonce : KK_Ajax.nonce
				},
				function( response ) {
					$('#quick-view div').remove();
					$('#quick-view').append( response );
					$('#quick-view').foundation('reveal', 'open');
					button.text('Quick View');
				}
			);
			return false;
		});
		
		// homepage load more
		$( '#latest-products .button-group .button.more, #sale-products .button-group .button.more' ).click( function( e ) {
			e.preventDefault();
			button = $(this);
			posts_type = button.attr('data-type');
			posts_offset = parseInt( button.attr('data-offset') ) + 4;
			posts_total = parseInt( button.attr('data-total') );
			if( posts_offset > posts_total )
				posts_offset = 0;
			button.text('Loading').attr('data-offset', posts_offset);			
			$.post(
				KK_Ajax.ajaxurl,
				{
					action : 'karmakiss_frontpage_more',
					posts_offset : posts_offset,
					posts_type: posts_type,
					nonce : KK_Ajax.nonce
				},
				function( response ) {
					if ( posts_type == 'fresh-arrivals' ) {
						$( '#latest-products .products .product' ).fadeOut( 'slow' ).remove();
						$( '#latest-products .products' ).append( response ).hide().fadeIn( 'slow' );
					}
					if( posts_type == 'on-sale' ) {
						$( '#sale-products .products .product' ).fadeOut( 'slow' ).remove();
						$( '#sale-products .products' ).append( response ).hide().fadeIn( 'slow' );
					}
					button.text('More');
				}
			);
			return false;
		});
		
	});
})(jQuery);