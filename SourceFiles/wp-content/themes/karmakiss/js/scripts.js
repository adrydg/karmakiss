jQuery(document).ready(function($) {
	
	// image gallery
	$( '.gallery .thumbnails a' ).click( function(e) {
		e.preventDefault();
		$( '.gallery .woocommerce-main-image img' ).attr( 'src', $(this).attr( 'href' ) );
	});
	
	// product add-ons
	var summary_other = $( '.panel.summary .title, .panel.summary .status, .panel.summary .product-addons-preview, .panel.summary .variations, .panel.summary .quantity-row, .panel.summary button' );
	
	// hide add-ons
	$( '.product-addon' ).hide();
	$( '.product-gift-wrap .remove, .product-greeting-card .remove' ).hide();
	
	// display add-ons
	$( '.panel.summary .product-gift-wrap .button, .panel.summary .product-gift-wrap .th' ).click( function(e) {
		e.preventDefault();
		summary_other.hide();
		$( '.panel.summary .product-addon-gift-wrap' ).show();
	});

	$( '.panel.summary .product-greeting-card .button, .panel.summary .product-greeting-card .th' ).click( function(e) {
		e.preventDefault();
		summary_other.hide();
		$( '.panel.summary .product-addon-greeting-card' ).show();
	});
	
	// select add-on
	$( '.product-addon-gift-wrap label' ).click( function() {
		$( '.product-addon-gift-wrap label.checked input' ).click();
		$( '.product-addon-gift-wrap label.checked' ).removeClass('checked');
		$(this).addClass('checked');
	});
	
	$( '.product-addon-greeting-card label' ).click( function() {
		$( '.product-addon-greeting-card label.checked input' ).click();
		$( '.product-addon-greeting-card label.checked' ).removeClass('checked');
		$(this).addClass('checked');
	});
	
	// submit / exit
	$( '.product-addon-gift-wrap .submit-addon' ).click( function(e) {
		e.preventDefault();
		$image = $( '.product-addon-gift-wrap label.checked img' ).attr( 'src' );
		$title = $( '.product-addon-gift-wrap label.checked .addon-title' ).text();
		$price = $( '.product-addon-gift-wrap label.checked .addon-price' ).text();
		$( '.product-gift-wrap .th' ).addClass('default').css('background-image', 'url(' + $image + ')');
		$( '.product-gift-wrap .title' ).text( $title );
		$( '.product-gift-wrap .price' ).text( $price );
		$( '.product-gift-wrap .remove' ).show();
		$( '.product-gift-wrap .button' ).text( 'Change Gift Wrap' );
		$( '.product-addon-gift-wrap' ).hide();
		summary_other.show();
	});
	
	$( '.product-addon-greeting-card .submit-addon' ).click( function(e) {
		e.preventDefault();
		$image = $( '.product-addon-greeting-card label.checked img' ).attr( 'src' );
		$title = $( '.product-addon-greeting-card label.checked .addon-title' ).text();
		$price = $( '.product-addon-greeting-card label.checked .addon-price' ).text();
		$( '.product-greeting-card .th' ).addClass('default').css('background-image', 'url(' + $image + ')');
		$( '.product-greeting-card .title' ).text( $title );
		$( '.product-greeting-card .price' ).text( $price );
		$( '.product-greeting-card .remove' ).show();
		$( '.product-greeting-card .button' ).text( 'Change Gift Wrap' );
		$( '.product-addon-greeting-card' ).hide();
		summary_other.show();
	});

	$( '.product-addon-gift-wrap .addon-exit' ).click( function(e) {
		e.preventDefault();
		$( '.product-addon-gift-wrap label.checked input' ).click();
		$( '.product-addon-gift-wrap label.checked' ).removeClass( 'checked' );
		$( '.product-addon-gift-wrap' ).hide();
		summary_other.show();
	});
	
	$( '.product-addon-greeting-card .addon-exit' ).click( function(e) {
		e.preventDefault();
		$( '.product-addon-greeting-card label.checked input' ).click();
		$( '.product-addon-greeting-card label.checked' ).removeClass( 'checked' );
		$( '.product-addon-greeting-card' ).hide();
		summary_other.show();
	});
	
	// remove
	$( '.product-gift-wrap .remove' ).click( function() {
		$( '.product-addon-gift-wrap label.checked input' ).click();
		$( '.product-addon-gift-wrap label.checked' ).removeClass( 'checked' );
		$( '.product-gift-wrap .th' ).removeClass( 'default' ).css( 'background-image', '' );
		$( '.product-gift-wrap .title' ).text( 'Please, select a Gift Wrap' );
		$( '.product-gift-wrap .price' ).text( '' );
		$( '.product-gift-wrap .remove' ).hide();
		$( '.product-gift-wrap .button' ).text( 'Add Gift Wrap' );
	});
	
	$( '.product-greeting-card .remove' ).click( function() {
		$( '.product-addon-greeting-card label.checked input' ).click();
		$( '.product-addon-greeting-card label.checked' ).removeClass( 'checked' );
		$( '.product-greeting-card .th' ).removeClass( 'default' ).css( 'background-image', '' );
		$( '.product-greeting-card .title' ).text( 'Please, select a Greeting Card' );
		$( '.product-greeting-card .price' ).text( '' );
		$( '.product-greeting-card .remove' ).hide();
		$( '.product-greeting-card .button' ).text( 'Add Greeting Card' );
	});
	
	// 


			$( '.single_variation' ).hide();
			$( '.single_variation .stock' ).appendTo('.stock-status');
	
	// checkout
	
	$( '#guest_email' ).on( 'change', function() {
		$( '#billing_email' ).attr( 'value', $(this).val() );
	});
	
});

(function(jQuery) {
	jQuery(document).foundation({
	  accordion: {
	    // specify the class used for active (or open) accordion panels
	    active_class: 'active',
	    // allow multiple accordion panels to be active at the same time
	    multi_expand: true,
	    // allow accordion panels to be closed by clicking on their headers
	    // setting to false only closes accordion panels when another is opened
	    toggleable: true
	  }
	});
})(jQuery);