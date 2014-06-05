<?php
/**
* WC_Gateway_Amazon_Payments_Advanced
*/
class WC_Gateway_Amazon_Payments_Advanced extends WC_Payment_Gateway {

	private $endpoints = array(
		"sandbox" => array(
			"US" => "https://mws.amazonservices.com/OffAmazonPayments_Sandbox/2013-01-01/",
			"GB" => "https://mws-eu.amazonservices.com/OffAmazonPayments_Sandbox/2013-01-01/",
			"DE" => "https://mws-eu.amazonservices.com/OffAmazonPayments_Sandbox/2013-01-01/",
		),
		"production" => array(
			"US" => "https://mws.amazonservices.com/OffAmazonPayments/2013-01-01/",
			"GB" => "https://mws-eu.amazonservices.com/OffAmazonPayments/2013-01-01/",
			"DE" => "https://mws-eu.amazonservices.com/OffAmazonPayments/2013-01-01/",
		)
	);

	/**
	 * Constructor
	 */
	public function __construct() {
		global $woocommerce;

		$this->method_title = 'Amazon Payments Advanced';
		$this->id           = 'amazon_payments_advanced';
		$this->has_fields   = function_exists( 'is_checkout_pay_page' ) ? is_checkout_pay_page() : is_page( woocommerce_get_page_id( 'pay' ) );
		$this->icon         = apply_filters( 'woocommerce_amazon_pa_logo', plugins_url( basename( dirname( dirname( __FILE__ ) ) ) . '/assets/images/amazon-payments.gif' ) );

		// Load the form fields.
		$this->init_form_fields();

		// Load the settings.
		$this->init_settings();

		// Define user set variables
		$this->title           = $this->settings['title'];
		$this->seller_id       = $this->settings['seller_id'];
		$this->mws_access_key  = $this->settings['mws_access_key'];
		$this->secret_key      = $this->settings['secret_key'];
		$this->sandbox         = $this->settings['sandbox'] == 'yes' ? true : false;
		$this->payment_capture = isset( $this->settings['payment_capture'] ) ? $this->settings['payment_capture'] : '';
		
		// Get endpoint
		$location             = in_array( $woocommerce->countries->get_base_country(), array( 'US', 'GB', 'DE' ) ) ? $woocommerce->countries->get_base_country() : 'US';
		$this->endpoint       = $this->sandbox ? $this->endpoints['sandbox'][ $location ] : $this->endpoints['production'][ $location ];
		
		// Get refererence ID
		$this->reference_id   = ! empty( $_REQUEST['amazon_reference_id'] ) ? $_REQUEST['amazon_reference_id'] : '';

		if ( isset( $_POST['post_data'] ) ) {
			parse_str( $_POST['post_data'], $post_data );

			if ( isset( $post_data['amazon_reference_id'] ) )
				$this->reference_id = $post_data['amazon_reference_id'];
		}

		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
	}

	/**
	 * Check If The Gateway Is Available For Use
	 *
	 * @access public
	 * @return bool
	 */
	function is_available() {
		if ( $this->enabled == "yes" && ! empty( $this->reference_id ) )
			return true;
	}

	/**
	 * Make an api request
	 * @param  args $args
	 * @return wp_error or parsed response array
	 */
	public function api_request( $args ) {
		$defaults = array(
			'AWSAccessKeyId'                                               => $this->mws_access_key,
			'SellerId'                                                     => $this->seller_id
		);

		$args = wp_parse_args( $args, $defaults );

		$response = wp_remote_get( 
			$this->get_signed_amazon_url( $this->endpoint . '?' . http_build_query( $args, '', '&' ), $this->secret_key ),
			array(
				'timeout' => 12
			)
		);

		if ( ! is_wp_error( $response ) )
			$response = $this->xml2Array( $response['body'] );

		return $response;
	}

	/**
     * Payment form on checkout page
     */
	public function payment_fields() {
		if ( $this->has_fields ) {
			?>
			<div id="amazon_wallet_widget"></div>
			<input type="hidden" name="amazon_reference_id" value="<?php echo $this->reference_id; ?>" />
			<?php
		}
	}

    /**
	 * Admin Panel Options
	 * - Options for bits like 'title' and availability on a country-by-country basis
	 */
	function admin_options() {
    	?>
    	<h3><?php echo $this->method_title; ?></h3>

    	<?php if ( ! $this->seller_id ) : ?>
	    	<div class="woocommerce-message"><div class="squeezer">
	    		<h4><?php _e( 'Need an Amazon Payments Advanced account?', 'wc_amazon_payments_advanced' ); ?></h4>
	    		<p class="submit">
	    			<a class="button button-primary" href="<?php echo esc_url( WC_AMAZON_REGISTER_URL ); ?>"><?php _e( 'Signup now', 'wc_amazon_payments_advanced' ); ?></a>
	    		</p>
	    	</div></div><br/>
	    <?php endif; ?>

    	<table class="form-table">
	    	<?php $this->generate_settings_html(); ?>
		</table><!--/.form-table-->
		<?php
    }

    /**
     * init_form_fields function.
     *
     * @access public
     * @return void
     */
    function init_form_fields() {
    	$this->form_fields = array(
			'enabled' => array(
				'title'       => __( 'Enable/Disable', 'wc_amazon_payments_advanced' ),
				'label'       => __( 'Enable Amazon Payments Advanced', 'wc_amazon_payments_advanced' ),
				'type'        => 'checkbox',
				'description' => '',
				'default'     => 'no'
			),
			'title' => array(
				'title'       => __( 'Title', 'wc_amazon_payments_advanced' ),
				'type'        => 'text',
				'description' => __( 'Payment method title that the customer will see on your website.', 'wc_amazon_payments_advanced' ),
				'default'     => __( 'Amazon', 'wc_amazon_payments_advanced' ),
				'desc_tip'    => true
			),
			'seller_id' => array(
				'title'       => __( 'Seller ID', 'wc_amazon_payments_advanced' ),
				'type'        => 'text',
				'description' => __( 'Obtained from your Amazon account. Also known as the "Merchant ID". Usually found under Settings > Integrations after logging into your merchant account.', 'wc_amazon_payments_advanced' ),
				'default'     => '',
				'desc_tip'    => true
			),
			'mws_access_key' => array(
				'title'       => __( 'MWS Access Key', 'wc_amazon_payments_advanced' ),
				'type'        => 'text',
				'description' => __( 'Obtained from your Amazon account. You can get these keys by logging into Seller Central and viewing the MWS Access Key section under the Integration tab.', 'wc_amazon_payments_advanced' ),
				'default'     => '',
				'desc_tip'    => true
			),
			'secret_key' => array(
				'title'       => __( 'Secret Key', 'wc_amazon_payments_advanced' ),
				'type'        => 'text',
				'description' => __( 'Obtained from your Amazon account. You can get these keys by logging into Seller Central and viewing the MWS Access Key section under the Integration tab.', 'wc_amazon_payments_advanced' ),
				'default'     => '',
				'desc_tip'    => true
			),
			'sandbox' => array(
				'title'       => __( 'Use Sandbox', 'wc_amazon_payments_advanced' ),
				'label'       => __( 'Enable sandbox mode during testing and development - live payments will not be taken if enabled.', 'wc_amazon_payments_advanced' ),
				'type'        => 'checkbox',
				'description' => '',
				'default'     => 'no'
			),
			'payment_capture' => array(
				'title'       => __( 'Payment Capture', 'wc_amazon_payments_advanced' ),
				'type'        => 'select',
				'description' => '',
				'default'     => '',
				'options'     => array(
					''          => __( 'Authorize and Capture the payment when the order is placed.', 'wc_amazon_payments_advanced' ),
					'authorize' => __( 'Authorize the payment when the order is placed.', 'wc_amazon_payments_advanced' ),
					'manual'    => __( 'Don’t Authorize the payment when the order is placed (i.e. for pre-orders).', 'wc_amazon_payments_advanced' )
				)
			),
			'cart_button_display_mode' => array(
				'title'       => __( 'Cart login button display', 'wc_amazon_payments_advanced' ),
				'description' => __( 'How the login with Amazon button gets displayed on the cart page.' ),
				'type'        => 'select',
				'options'     => array(
					'button'   => __( 'Button', 'wc_amazon_payments_advanced' ),
					'banner'   => __( 'Banner', 'wc_amazon_payments_advanced' ),
					'disabled' => __( 'Disabled', 'wc_amazon_payments_advanced' ),
				),
				'default'     => 'button',
				'desc_tip'    => true
			),
			'wallet_widget_width' => array(
				'title'       => __( 'Wallet widget width', 'wc_amazon_payments_advanced' ),
				'description'       => __( 'Wallet width in pixels. Use a width which fits nicely inside your checkout template.', 'wc_amazon_payments_advanced' ),
				'type'        => 'text',
				'default'     => '300',
				'desc_tip'    => true
			),
			'wallet_widget_height' => array(
				'title'       => __( 'Wallet widget height', 'wc_amazon_payments_advanced' ),
				'description'       => __( 'Wallet height in pixels. Use a height which fits nicely inside your checkout template.', 'wc_amazon_payments_advanced' ),
				'type'        => 'text',
				'default'     => '300',
				'desc_tip'    => true
			),
			'addressbook_widget_width' => array(
				'title'       => __( 'Addressbook widget width', 'wc_amazon_payments_advanced' ),
				'description'       => __( 'Addressbook width in pixels. Use a width which fits nicely inside your checkout template.', 'wc_amazon_payments_advanced' ),
				'type'        => 'text',
				'default'     => '300',
				'desc_tip'    => true
			),
			'addressbook_widget_height' => array(
				'title'       => __( 'Addressbook widget height', 'wc_amazon_payments_advanced' ),
				'description'       => __( 'Addressbook height in pixels. Use a height which fits nicely inside your checkout template.', 'wc_amazon_payments_advanced' ),
				'type'        => 'text',
				'default'     => '300',
				'desc_tip'    => true
			)
 	   );
    }

	/**
	 * process_payment function.
	 *
	 * @access public
	 * @param mixed $order_id
	 * @return void
	 */
	function process_payment( $order_id ) {
		global $woocommerce, $wc_amazon_pa_order_handler;

		$order = new WC_Order( $order_id );

		$amazon_reference_id = isset( $_POST['amazon_reference_id'] ) ? woocommerce_clean( $_POST['amazon_reference_id'] ) : '';

		try {

			if ( ! $amazon_reference_id )
				throw new Exception( __( 'An Amazon payment method was not chosen.', 'wc_amazon_payments_advanced' ) );

			// Update order reference with amounts
			$response = $this->api_request( array(
				'Action'                                                       => 'SetOrderReferenceDetails',
				'AmazonOrderReferenceId'                                       => $amazon_reference_id,
				'OrderReferenceAttributes.OrderTotal.Amount'                   => $order->get_total(),
				'OrderReferenceAttributes.OrderTotal.CurrencyCode'             => strtoupper( get_woocommerce_currency() ),
				'OrderReferenceAttributes.SellerNote'                          => sprintf( __( 'Order %s from %s.', 'wc_amazon_payments_advanced' ), $order->get_order_number(), urlencode( wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) ) ),
				'OrderReferenceAttributes.SellerOrderAttributes.SellerOrderId' => $order->get_order_number(),
				'OrderReferenceAttributes.SellerOrderAttributes.StoreName'     => wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ),
				'OrderReferenceAttributes.PlatformId'                          => 'A1BVJDFFHQ7US4'
			) );

			if ( is_wp_error( $response ) )
				throw new Exception( $response->get_error_message() );

			if ( isset( $response['Error']['Message'] ) )
				throw new Exception( $response['Error']['Message'] );

			// Confirm order reference
			$response = $this->api_request( array(
				'Action'                 => 'ConfirmOrderReference',
				'AmazonOrderReferenceId' => $amazon_reference_id
			) );

			if ( is_wp_error( $response ) )
				throw new Exception( $response->get_error_message() );

			if ( isset( $response['Error']['Message'] ) )
				throw new Exception( $response['Error']['Message'] );

			// Get FULL address details and save them to the order
			$response = $this->api_request( array(
				'Action'                 => 'GetOrderReferenceDetails',
				'AmazonOrderReferenceId' => $amazon_reference_id
			) );

			if ( ! is_wp_error( $response ) && isset( $response['GetOrderReferenceDetailsResult']['OrderReferenceDetails']['Destination']['PhysicalDestination'] ) ) {
				$buyer          = $response['GetOrderReferenceDetailsResult']['OrderReferenceDetails']['Buyer'];
				$address        = $response['GetOrderReferenceDetailsResult']['OrderReferenceDetails']['Destination']['PhysicalDestination'];
				$billing_name   = explode( ' ' , $buyer['Name'] );
				$shipping_name  = explode( ' ' , $address['Name'] );
				$billing_first  = current( $billing_name );
				$billing_last   = end( $billing_name );
				$shipping_first = current( $shipping_name );
				$shipping_last  = end( $shipping_name );

				if ( $billing_first == $billing_last )
					$billing_last = '';

				if ( $shipping_first == $shipping_last )
					$shipping_last = '';

				update_post_meta( $order_id, '_billing_first_name', $billing_first );
				update_post_meta( $order_id, '_billing_last_name', $billing_last );
				update_post_meta( $order_id, '_billing_email', $buyer['Email'] );

				if ( isset( $buyer['Phone'] ) )
					update_post_meta( $order_id, '_billing_phone', $buyer['Phone'] );
				elseif ( isset( $address['Phone'] ) )
					update_post_meta( $order_id, '_billing_phone', $address['Phone'] );

				update_post_meta( $order_id, '_shipping_first_name', $shipping_first );
				update_post_meta( $order_id, '_shipping_last_name', $shipping_last );

				if ( isset( $address['AddressLine1'] ) )
					update_post_meta( $order_id, '_shipping_address_1', $address['AddressLine1'] );

				if ( isset( $address['AddressLine2'] ) )
					update_post_meta( $order_id, '_shipping_address_2', $address['AddressLine2'] );

				if ( isset( $address['City'] ) )
					update_post_meta( $order_id, '_shipping_city', $address['City'] );

				if ( isset( $address['PostalCode'] ) )
					update_post_meta( $order_id, '_shipping_postcode', $address['PostalCode'] );

				if ( isset( $address['StateOrRegion'] ) )
					update_post_meta( $order_id, '_shipping_state', $address['StateOrRegion'] );

				if ( isset( $address['CountryCode'] ) )
					update_post_meta( $order_id, '_shipping_country', $address['CountryCode'] );
			}

			// Store reference ID in the order
			update_post_meta( $order_id, 'amazon_reference_id', $amazon_reference_id );

			switch ( $this->payment_capture ) {
				case 'manual' :

					// Mark as on-hold
					$order->update_status( 'on-hold', __( 'Amazon order opened. Use the "Amazon Payments Advanced" box to authorize and/or capture payment. Authorized payments must be captured within 7 days.', 'wc_amazon_payments_advanced' ) );

					// Reduce stock levels
					$order->reduce_order_stock();

				break;
				case 'authorize' :

					// Authorize only
					$result = $wc_amazon_pa_order_handler->authorize_payment( $order_id, $amazon_reference_id, false );

					if ( $result ) {
						// Mark as on-hold
						$order->update_status( 'on-hold', __( 'Amazon order opened. Use the "Amazon Payments Advanced" box to authorize and/or capture payment. Authorized payments must be captured within 7 days.', 'wc_amazon_payments_advanced' ) );

						// Reduce stock levels
						$order->reduce_order_stock();
					} else {
						$order->update_status( 'failed', __( 'Could not authorize Amazon payment.', 'wc_amazon_payments_advanced' ) );
					}

				break;
				default :

					// Capture
					$result = $wc_amazon_pa_order_handler->authorize_payment( $order_id, $amazon_reference_id, true );

					if ( $result ) {
						// Payment complete
						$order->payment_complete();
					} else {
						$order->update_status( 'failed', __( 'Could not authorize Amazon payment.', 'wc_amazon_payments_advanced' ) );
					}

				break;
			}

			// Remove cart
			$woocommerce->cart->empty_cart();

			// Return thank you page redirect
			return array(
				'result' 	=> 'success',
				'redirect'	=> $this->get_return_url( $order )
			);

		} catch( Exception $e ) {
			$woocommerce->add_error( __( 'Error:', 'wc_amazon_payments_advanced' ) . ' ' . $e->getMessage() );
			return;
		}
	}

	/**
	 * Sign a url for amazon
	 * @param  string $url
	 * @return string
	 */
	public function get_signed_amazon_url( $url, $secret_key ) {

		$urlparts       = parse_url( $url );

		// Build $params with each name/value pair
	    foreach ( explode( '&', $urlparts['query'] ) as $part ) {
	        if ( strpos( $part, '=' ) ) {
	            list( $name, $value ) = explode( '=', $part, 2 );
	        } else {
	            $name  = $part;
	            $value = '';
	        }
	        $params[ $name ] = $value;
	    }

	    // Include a timestamp if none was provided
	    if ( empty( $params['Timestamp'] ) ) {
	        $params['Timestamp'] = gmdate( 'Y-m-d\TH:i:s\Z' );
	    }

	    $params['SignatureVersion'] = '2';
	    $params['SignatureMethod'] = 'HmacSHA256';

	    // Sort the array by key
	    ksort( $params );

	    // Build the canonical query string
	    $canonical       = '';

	    // Don't encode here - http_build_query already did it.
	    foreach ( $params as $key => $val ) {
	        $canonical  .= $key . "=" . rawurlencode( utf8_decode( urldecode( $val ) ) ) . "&";
	    }

		// Remove the trailing ampersand
		$canonical      = preg_replace( "/&$/", '', $canonical );

		// Some common replacements and ones that Amazon specifically mentions
		$canonical      = str_replace( array( ' ', '+', ',', ';' ), array( '%20', '%20', urlencode(','), urlencode(':') ), $canonical );

		// Build the sign
		$string_to_sign = "GET\n{$urlparts['host']}\n{$urlparts['path']}\n$canonical";

		// Calculate our actual signature and base64 encode it
		$signature      = base64_encode( hash_hmac( 'sha256', $string_to_sign, $secret_key, true ) );

		// Finally re-build the URL with the proper string and include the Signature
		$url            = "{$urlparts['scheme']}://{$urlparts['host']}{$urlparts['path']}?$canonical&Signature=" . rawurlencode( $signature );

	    return $url;
	}

    /**
     * Renvoie le flux xml sous forme de tableau associatif multi dimensionnel
     * php.net Julio Cesar Oliveira
     *
     * @param string $xml
     * @param boolean $recursive
     *
     * @return array
     */
    public function xml2Array($xml, $recursive = false) {
        if( ! $recursive ) {
            $array = (array) simplexml_load_string($xml);
        } else {
            $array = (array) $xml;
        }

        $newArray = array();

        foreach ($array as $key => $value) {
            $value = (array)$value;
            if (isset($value[0])) {
                $newArray[$key] = trim ($value[0]);
            } else {
                $newArray[$key] = self::xml2Array($value, true);
            }
        }
        return $newArray ;
    }
}