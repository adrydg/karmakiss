<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'Smart_Coupons_WC_Compatibility' ) ) {
	/**
	 * Class to check for WooCommerce version & return variables accordingly
	 *
	 */
	class Smart_Coupons_WC_Compatibility {

		public static function global_wc() {
			if ( self::is_wc_21() ) {
				return WC();
			} else {
				global $woocommerce;
				return $woocommerce;
			}
		}

		public static function is_wc_21() {
			return self::is_wc_greater_than( '2.0.20' );
		}

		public static function wc_price( $price ) {
			if ( self::is_wc_21() ) {
				return wc_price( $price );
			} else {
				return woocommerce_price( $price );
			}
		}

		public static function get_wc_version() {
			if (defined('WC_VERSION') && WC_VERSION)
				return WC_VERSION;
			if (defined('WOOCOMMERCE_VERSION') && WOOCOMMERCE_VERSION)
				return WOOCOMMERCE_VERSION;
			return null;
		}

		public static function is_wc_greater_than( $version ) {
			return version_compare( self::get_wc_version(), $version, '>' );
		}

	}
}