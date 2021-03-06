<?php

/*
 * Plugin Name: Easy Social Share Buttons for WordPress
* Description: Easy Social Share Buttons automatically adds share bar to your post or pages with support of Facebook, Twitter, Google+, LinkedIn, Pinterest, Digg, StumbleUpon, VKontakte, Tumblr, Reddit, Print, E-mail. Easy Social Share Buttons for WordPress is compatible with WooCommerce, bbPress and BuddyPress
* Plugin URI: http://codecanyon.net/item/easy-social-share-buttons-for-wordpress/6394476?ref=appscreo
* Version: 1.3.9.3
* Author: CreoApps
* Author URI: http://codecanyon.net/user/appscreo/portfolio?ref=appscreo
*/

if (! defined ( 'WPINC' ))
	die ();

//error_reporting( E_ALL | E_STRICT );

define ( 'ESSB_VERSION', '1.3.9.3' );
define ( 'ESSB_PLUGIN_ROOT', dirname ( __FILE__ ) . '/' );
define ( 'ESSB_PLUGIN_URL', plugins_url () . '/' . basename ( dirname ( __FILE__ ) ) );
define (' ESSB_PLUGIN_BASE_NAME', plugin_basename( __FILE__ ));

global $essb_plugin_base_name;
$essb_plugin_base_name = basename ( dirname ( __FILE__ ) );

define ( 'ESSB_TEXT_DOMAIN', 'essb' );

include (ESSB_PLUGIN_ROOT . 'lib/essb.php');
include (ESSB_PLUGIN_ROOT . 'lib/essb-stats.php');
include (ESSB_PLUGIN_ROOT . 'lib/essb-loveyou.php');
include (ESSB_PLUGIN_ROOT . 'lib/essb-opengraph.php');
include (ESSB_PLUGIN_ROOT . 'lib/essb-twittercards.php');
include (ESSB_PLUGIN_ROOT . 'lib/external/autoupdate/plugin-update-checker.php');
include (ESSB_PLUGIN_ROOT . 'lib/admin/essb-metabox.php');
require_once (ESSB_PLUGIN_ROOT . 'lib/external/TwitterAPIExchange.php');
include (ESSB_PLUGIN_ROOT . 'lib/modules/essb-social-fanscounter.php');

include (ESSB_PLUGIN_ROOT . 'lib/extensions/essb-settings-helper.php');
include (ESSB_PLUGIN_ROOT . 'lib/extensions/essb-flattr.php');
include (ESSB_PLUGIN_ROOT . 'lib/extensions/essb-skinned-native-button.php');

register_activation_hook ( __FILE__, array ('EasySocialShareButtons', 'activate' ) );
register_deactivation_hook ( __FILE__, array ('EasySocialShareButtons', 'deactivate' ) );



add_action( 'init', 'essb_load_translations' );
function essb_load_translations() {
	load_plugin_textdomain( ESSB_TEXT_DOMAIN, false, ESSB_PLUGIN_ROOT.'/languages' );
}

global $opengraph;
$opengraph = new ESSB_OpenGraph();

global $twitter_cards;
$twitter_cards = new ESSB_TwitterCards();

$stats = new EasySocialShareButtons_Stats();

global $essb_love;
$essb_love = new ESSB_LoveYou();

global $essb_fans;
$essb_fans = new EasySocialFansCounter();
$essb_fans->version = ESSB_VERSION;

global $essb;
$essb = new EasySocialShareButtons($stats, $essb_love);


$option = get_option ( EasySocialShareButtons::$plugin_settings_name );
$disable_admin_menu = isset($option['disable_adminbar_menu']) ? $option['disable_adminbar_menu'] : 'false';
$puchase_code = isset($option['purchase_code']) ? $option['purchase_code'] : 'none';

// @since 1.3.3
// autoupdate
// activating autoupdate option
$essb_autoupdate = PucFactory::buildUpdateChecker(
		'http://update.creoworx.com/essb/',
		__FILE__, 'easy-social-share-buttons'
);

// @since 1.3.7.2 - update to avoid issues with other plugins that uses same method
function addSecretKeyESSB($query){
	global $puchase_code;
	$query['license'] = $puchase_code;
	return $query;
}
$essb_autoupdate->addQueryArgFilter('addSecretKeyESSB');

// @since 1.3.1
if ($disable_admin_menu != 'true') {
	add_action( "init", "ESSBAdminMenuInit" );
}

function ESSBAdminMenuInit() {
    global $essb_adminmenu;
    $essb_adminmenu = new EasySocialShareButtons_AdminMenu();
}

if ( !function_exists( 'easy_share_deactivate' ) ) {
	function easy_share_deactivate() {
		global $essb;
		
		$essb->temporary_deactivate_content_filter();
	}
}

if ( !function_exists( 'easy_share_reactivate' ) ) {
	function easy_share_reactivate() {
		global $essb;

		$essb->reactivate_content_filter();
	}
}

if ( !function_exists( 'easy_share_buttons' ) ) {
	function easy_share_buttons($counters = false) {
		global $essb;

		$cnt_flag = ($counters) ? 1: 0;
		$buttons_html = $essb->generate_share_snippet(array(), $cnt_flag);
		echo $buttons_html;
	}
}

?>