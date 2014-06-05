<?php

class EasySocialShareButtons {
	
	protected $version = ESSB_VERSION;
	protected $plugin_name = "Easy Social Share Buttons for WordPress";
	protected $plugin_slug = "easy-social-share-buttons";
	
	protected $update_notify_address = "http://fb.creoworx.com/u/";
	
	protected $print_links_position = "top";
	
	public static $plugin_settings_name = "easy-social-share-buttons";
	
	public static $instance = null;
	public $stats = null;
	public $love = null;
	
	protected $pinjs_registered = false;
	protected $mailjs_registered = false;
	protected $css_minifier = false;
	protected $js_minifier = false;
	protected $counter_included = false;
	protected $skinned_social = false;
	protected $skinned_social_selected_skin = "";
	protected $twitter_api_added = false;
	protected $shortcode_like_css_included = false;
	
	public function __construct($stats_instance, $love_instance) {
		global $opengraph, $twitter_cards;
		
		$this->stats = $stats_instance;
		$this->love = $love_instance;
		
		// register admin page
		add_action ( 'admin_menu', array ($this, 'init_menu' ) );
		
		//add_filter('cron_schedules', array($this, 'addCronSchedule'));
				
		//$this->essb_updater_setup_schedule();
		//add_action('essb_update', array($this, 'checkForUpdates'));
		
		if (is_admin ()) {
			add_action ( 'admin_enqueue_scripts', array ($this, 'register_admin_assets' ), 1 );
			add_action ( 'admin_init', array ($this, 'register_vk' ), 1 );
				
		} else {
			add_action ( 'wp_enqueue_scripts', array ($this, 'register_front_assets' ), 1 );
		}
		
		// update notify
		if(is_admin()){
			$info = get_option($this->plugin_slug . '_update', null);
			if(is_array($info) and $info['update']){
				add_action('admin_notices', array($this, 'addAdminNotice'));
			}
		}
		
		// @since 1.0.1 - Facebook Like Button
		$option = get_option ( self::$plugin_settings_name );
		
		$skin_native = isset($option['skin_native']) ? $option['skin_native'] : 'false';
		$skin_native_skin = isset($option['skin_native_skin']) ? $option['skin_native_skin'] : '';
		if ($skin_native == 'true') {
			$this->skinned_social = true;
			$this->skinned_social_selected_skin = $skin_native_skin;			
		}
		
		
		add_action ( 'the_content', array ($this, 'print_share_links' ), 10, 1 );
		$is_excerpt_active = isset($option['display_excerpt']) ? $option['display_excerpt'] : 'false';
		
		// @since 1.1.9
		if ($is_excerpt_active == "true") {
			add_action ( 'the_excerpt', array ($this, 'print_share_links' ), 10, 1 );
		}
		
		// @since 1.3.7.1 - removed 1.3.7.2 - makes issue with some themes
		//add_filter( 'get_the_excerpt', array( $this, 'remove_buttons_excerpts'), -999);
		
		add_shortcode ( 'essb', array ($this, 'handle_essb_shortcode' ) );
		add_shortcode ( 'easy-share', array ($this, 'handle_essb_shortcode' ) );
		add_shortcode ( 'easy-social-share-buttons', array ($this, 'handle_essb_shortcode' ) );
		
		// @since 1.3.9.2
		add_shortcode ('easy-social-like', array($this, 'handle_essb_like_buttons_shortcode'));
		add_shortcode ('easy-social-like-simple', array($this, 'handle_essb_like_buttons_shortcode'));
		add_shortcode ( 'easy-social-share', array ($this, 'handle_essb_shortcode_vk' ) );
		
		// @since 1.3.9.3
		add_shortcode ('easy-total-shares', array($this, 'handle_essb_total_shortcode'));
		
		add_action('add_meta_boxes', array ($this, 'handle_essb_metabox' ) );
		
		add_action('save_post',  array ($this, 'handle_essb_save_metabox'));
		//add_filter( 'plugin_action_links', array( $this, 'action_links' ) );
				
		$included_fb_api = isset($option['facebook_like_button_api']) ? $option['facebook_like_button_api'] : '';
		$facebook_advanced_sharing = isset($option['facebookadvanced']) ? $option['facebookadvanced'] : 'false';
		
		if ($included_fb_api != 'true') {
			add_action ( 'wp_footer', array ($this, 'init_fb_script' ) );
		}
		
		// @since 1.0.4
		$include_vk_api = isset($option['vklike']) ? $option['vklike'] : '';
		
		if ($include_vk_api == 'true') {
			add_action('wp_footer', array($this, 'init_vk_script'));
		}		
			
		// @since 1.0.7 fix for mobile devices don't to pop network names
		$hidden_network_names = (isset($option['hide_social_name']) && $option['hide_social_name']==1) ? true : false;
		
		// @since 1.3.9.2
		$always_hide_names_mobile = (isset($option['always_hide_names_mobile'])) ? $option['always_hide_names_mobile'] : 'false';
		if ($always_hide_names_mobile == 'true' && $this->isMobile()) {
			$hidden_network_names = true;
		}
		
		if ($hidden_network_names && $this->isMobile()){
			add_action('wp_head', array($this, 'fix_css_mobile_hidden_network_names'));
		}
		
		if ($hidden_network_names && !$this->isMobile()){
			$force_hide = isset($option['force_hide_social_name']) ? $option['force_hide_social_name'] : 'false';
			
			if ($force_hide == 'true') {
				add_action('wp_head', array($this, 'fix_css_mobile_hidden_network_names'));
			} 
		}
		
		$hidden_buttons_on_lowres_mobile = isset($option['force_hide_buttons_on_mobile']) ? $option['force_hide_buttons_on_mobile'] : 'false';
		if ($hidden_buttons_on_lowres_mobile == 'true' && $this->isMobile()){
			add_action('wp_head', array($this, 'fix_css_mobile_hidden_buttons'));
		}
		
		// @since 1.3.9.3
		$force_hide_buttons_on_all_mobile = isset($option['force_hide_buttons_on_all_mobile']) ? $option['force_hide_buttons_on_all_mobile'] : 'false';
		if ($force_hide_buttons_on_all_mobile == "true") {
			$this->deactivate();
		}
		
		// @since 1.1.6
		$custom_float_top = isset($option['float_top']) ? $option['float_top'] : '';
		$custom_float_bg = isset($option['float_bg']) ? $option['float_bg'] : '';
		$custom_float_full = isset($option['float_full']) ? $option['float_full'] : '';
		$custom_button_pos = isset($option['buttons_pos']) ? $option['buttons_pos'] : '';
		$custom_float_js = isset($option['float_js']) ? $option['float_js'] : 'false';
		if ($custom_float_top != '' || $custom_float_bg != '' || $custom_float_full != '' || $custom_button_pos != '' || $custom_float_js == "true") {
			add_action('wp_head', array($this, 'fix_css_float_from_top'));
		}
		
		// @since 1.1
		add_action ( 'wp_ajax_nopriv_essb_action', array ($this, 'send_email' ) );
		add_action ( 'wp_ajax_essb_action', array ($this, 'send_email' ) );

		// @since 1.3.1
		add_action ( 'wp_ajax_nopriv_essb_counts', array ($this, 'get_share_counts' ) );
		add_action ( 'wp_ajax_essb_counts', array ($this, 'get_share_counts' ) );
		
		$woocommerce_share = isset($option['woocommece_share']) ? $option['woocommece_share'] : 'false';
		
		if ($woocommerce_share == "true") {
			add_action('woocommerce_share', array($this, 'handle_woocommerce_share'));
		}
		
		//add_action('init', array( &$this, 'vc_entender_init' ) );
		// @since 1.1.7 - wp e-commerce
		//add_action('wpsc_product_before_description',array($this,'handle_wp_ecommerce'));
		
		// @since 1.2.6
		$bbpress_forum = isset($option['bbpress_forum']) ? $option['bbpress_forum'] : 'false'; 
		$bbpress_topic = isset($option['bbpress_topic']) ? $option['bbpress_topic'] : 'false';
		$buddypress_group = isset($option['buddypress_group']) ? $option['buddypress_group'] : 'false';
		$buddypress_activity = isset($option['buddypress_activity']) ? $option['buddypress_activity'] : 'false';
		$display_where = isset($option['display_where']) ? $option['display_where'] : '';
		
		if ($bbpress_topic == 'true') {
			if( 'top' == $display_where || 'both' == $display_where || 'float' == $display_where || 'sidebar' == $display_where || 'likeshare' == $display_where || 'sharelike' == $display_where) {
				add_action('bbp_template_before_replies_loop', array($this,'bbp_show_before_replies'));
			}
			
			if( 'bottom' == $display_where || 'both' == $display_where || 'popup' == $display_where || 'likeshare' == $display_where || 'sharelike' == $display_where) {
				add_action('bbp_template_after_replies_loop', array($this,'bbp_show_after_replies'));
			}
		}
		
		if ($bbpress_forum == "true") {
			if( 'top' == $display_where || 'both' == $display_where || 'float' == $display_where || 'sidebar' == $display_where || 'likeshare' == $display_where || 'sharelike' == $display_where) {
				add_action('bbp_template_before_topics_loop', array($this,'bbp_show_before_topics'));
			}
			if( 'bottom' == $display_where || 'both' == $display_where || 'popup' == $display_where || 'likeshare' == $display_where || 'sharelike' == $display_where) {
				add_action('bbp_template_after_topics_loop', array($this,'bbp_show_after_topics'));
			}
		}
		
		if ($buddypress_group == 'true') { 
			add_action('bp_before_group_home_content', array($this,'buddy_social_button_group_filter') );
		}
		if ($buddypress_activity == 'true') {
			add_action('bp_activity_entry_meta', array($this,'buddy_social_button_activity_filter'), 999);
		}
		
		// @since 1.2.7
		$opengraph_tags = isset($option['opengraph_tags']) ? $option['opengraph_tags'] : 'false';

		if ($opengraph_tags == 'true') {
			// @since 1.3.7.3
			$opengraph->fbadmins = isset($option['opengraph_tags_fbadmins']) ? $option['opengraph_tags_fbadmins'] : '';
			$opengraph->fbpage = isset($option['opengraph_tags_fbpage']) ? $option['opengraph_tags_fbpage'] : '';
			$opengraph->fbapp = isset($option['opengraph_tags_fbapp']) ? $option['opengraph_tags_fbapp'] : '';
			$opengraph->default_image = isset($option['sso_default_image']) ? $option['sso_default_image'] : '';
			
			$opengraph->activate_opengraph_metatags();
		}
		
		// @since 1.3.6
		$twitter_card_active = isset($option['twitter_card']) ? $option['twitter_card'] : 'false';
		
		if ($twitter_card_active == "true") {
			$twitter_cards->card_type = isset($option['twitter_card_type']) ? $option['twitter_card_type'] : '';
			$twitter_cards->twitter_user = isset($option['twitter_card_user']) ? $option['twitter_card_user'] : '';
			$twitter_cards->default_image = isset($option['sso_default_image']) ? $option['sso_default_image'] : '';
			
			$twitter_cards->activate_twittercards_metatags();
		}
		
		add_action('wp_head', array($this, 'customizer_compile_css'));
	}
	
	function register_vk() {
		require_once ESSB_PLUGIN_ROOT.'/lib/extensions/essb-vk.php';
	}
	
	function remove_buttons_excerpts($text) {
		$this->temporary_deactivate_content_filter();
		return $text;
	}
	
	function buddy_social_button_activity_filter() {
		// buddypress activity
		$activity_type = bp_get_activity_type();
		$activity_link = bp_get_activity_thread_permalink();
		$activity_title = bp_get_activity_feed_item_title();
		
		echo '<div style="clear: both;\"></div>';
		$options = get_option(  EasySocialShareButtons::$plugin_settings_name );
		
		$essb_networks = $options['networks'];
		$buttons = "";
		foreach($essb_networks as $k => $v) {
			if( $v[0] == 1 ) {
				if ($buttons != '') { $buttons .= ","; }
				$buttons .= $k;
			}
			
		}
		$activity_title = str_replace ('[&#8230;]', '', $activity_title);
		$need_counters = $options['show_counter'] ? 1 : 0;
		$links = do_shortcode('[easy-share buttons="'.$buttons.'" counters=0 native="no" url="'.urlencode($activity_link).'" text="'.htmlspecialchars($activity_title).'" nostats="yes" hide_names="yes"]');
		
		echo $links .'<div style="clear: both;\"></div>';
	}
	
	function buddy_social_button_group_filter() {
		// buddypress activity	
		$activity_link = bp_get_group_permalink();
		$activity_title =  bp_get_group_name();
		$options = get_option(  EasySocialShareButtons::$plugin_settings_name );
		
		$essb_networks = $options['networks'];
		$buttons = "";
		foreach($essb_networks as $k => $v) {
			if( $v[0] == 1 ) {
				if ($buttons != '') {
					$buttons .= ",";
				}
				$buttons .= $k;
			}
			
		}
		$hidden_name_class = (isset($options['hide_social_name']) && $options['hide_social_name']==1) ? ' hide_names="yes" ' : '';
		$hidden_name_class = 'hide_names="yes"';
		$need_counters = $options['show_counter'] ? 1 : 0;
		//$need_counters = 0;
		$links = do_shortcode('[easy-share buttons="'.$buttons.'" counters='.$need_counters.' native="no"  nostats="yes"  url="'.$activity_link.'" text="'.$activity_title.'" '.$hidden_name_class.']');
		
		echo $links .'<div style="clear: both;\"></div>';
	}		
	
	function bbp_show_before_replies() {
		$topic_id = bbp_get_topic_id();

		$this->print_links_position = "top";
		
		$options = get_option(  EasySocialShareButtons::$plugin_settings_name );
		$need_counters = $options['show_counter'] ? 1 : 0;
		$links = $this->generate_share_snippet(array(), $need_counters);
		echo $links .'<div style="clear: both;\"></div>';
	}
	
	function bbp_show_after_replies() {
		$topic_id = bbp_get_topic_id();
		
		$this->print_links_position = "bottom";
		$options = get_option(  EasySocialShareButtons::$plugin_settings_name );
		$need_counters = $options['show_counter'] ? 1 : 0;
		$links = $this->generate_share_snippet(array(), $need_counters);
	
		echo $links .'<div style="clear: both;\"></div>';
	}
	
	function bbp_show_before_topics() {
		$this->print_links_position = "top";
		$options = get_option(  EasySocialShareButtons::$plugin_settings_name );
		$need_counters = $options['show_counter'] ? 1 : 0;
		$links = $this->generate_share_snippet(array(), $need_counters);
	
		echo $links .'<div style="clear: both;\"></div>';
	}
	
	function bbp_show_after_topics() {
		$this->print_links_position = "bottom";
		$options = get_option(  EasySocialShareButtons::$plugin_settings_name );
		$need_counters = $options['show_counter'] ? 1 : 0;
		$links = $this->generate_share_snippet(array(), $need_counters);
	
		echo $links .'<div style="clear: both;\"></div>';
	}
	
	public static function get_instance() {
		
		// If the single instance hasn't been set, set it now.
		if (null == self::$instance)
			self::$instance = new self ();
		
		return self::$instance;
	
	}
	
	/**
	 * Activate plugin
	 */
	public static function activate() {
		$option = get_option ( self::$plugin_settings_name );
		if (! $option || empty ( $option ))
			update_option ( self::$plugin_settings_name, self::default_options () );
	}
	
	public static function deactivate() {
		//delete_option ( self::$plugin_settings_name );
		// remove schedule update check
		wp_clear_scheduled_hook('essb_update');
		
	}
	
	public static function default_options() {
		return array ('style' => 1, 'networks' => array ("facebook" => array (1, "Facebook" ), "twitter" => array (1, "Twitter" ), "google" => array (0, "Google+" ), "pinterest" => array (0, "Pinterest" ), "linkedin" => array (0, "LinkedIn" ), "digg" => array (0, "Digg" ), "stumbleupon" => array (0, "StumbleUpon" ), "vk" => array (0, "VKontakte" ), "tumblr" => array(0, "Tumblr"), "print" => array(0, "Print"), "mail" => array (1, "E-mail" ) ), 'show_counter' => 0, 'hide_social_name' => 0, 'target_link' => 1, 'twitter_user' => '', 'display_in_types' => array ('post' ), 'display_where' => 'bottom', 'mail_subject' => __ ( 'Visit this site %%siteurl%%', ESSB_TEXT_DOMAIN ), 'mail_body' => __ ( 'Hi, this may be intersting you: "%%title%%"! This is the link: %%permalink%%', ESSB_TEXT_DOMAIN ), 'colors' => array ("bg_color" => '', "txt_color" => '', 'facebook_like_button' => 'false' ) );
	}
	
	
	public function action_links( $links ) {
	
		$plugin_links = array(
				'<a href="' . admin_url( 'admin.php?page=essb_settings' ) . '">' . __( 'Settings', ESSB_TEXT_DOMAIN ) . '</a>',
		);
	
		return array_merge( $plugin_links, $links );
	}
	
	public function init_menu() {
		$option = get_option ( self::$plugin_settings_name );
		$menu_pos = isset($option['register_menu_under_settings']) ? $option['register_menu_under_settings'] : 'false';
		if ($menu_pos == "true") {
			add_options_page ( "Easy Social Share Buttons", "Easy Social Share Buttons", 'edit_pages', "essb_settings", array ($this, 'essb_settings_load' ), ESSB_PLUGIN_URL . '/assets/images/essb_16.png', 113 );
		}
		else {
			add_menu_page ( "Easy Social Share Buttons", "Easy Social Share Buttons", 'edit_pages', "essb_settings", array ($this, 'essb_settings_load' ), ESSB_PLUGIN_URL . '/assets/images/essb_16.png', 113 );
		}
	}
	
	public function essb_settings_load() {
		include (ESSB_PLUGIN_ROOT . 'lib/admin/essb-settings.php');
	}
	
	public function register_admin_assets() {
		wp_register_style ( 'essb-admin', ESSB_PLUGIN_URL . '/assets/css/essb-admin.css', array (), $this->version );
		wp_enqueue_style ( 'essb-admin' );
		wp_enqueue_script ( 'essb-admin', ESSB_PLUGIN_URL . '/assets/js/essb-admin.js', array ('jquery' ), $this->version, true );
		
		wp_register_style ( 'essb-fontawsome', ESSB_PLUGIN_URL . '/assets/css/font-awesome.min.css', array (), $this->version );
		wp_enqueue_style ( 'essb-fontawsome' );
		
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_style( 'wp-color-picker');
		wp_enqueue_script( 'wp-color-picker');
				
		wp_enqueue_style ( 'essb-morris-styles', ESSB_PLUGIN_URL.'/assets/css/morris.min.css',array (), $this->version );

		wp_enqueue_script ( 'essb-morris', ESSB_PLUGIN_URL . '/assets/js/morris.min.js', array ('jquery' ), $this->version );
		wp_enqueue_script ( 'essb-raphael', ESSB_PLUGIN_URL . '/assets/js/raphael-min.js', array ('jquery' ), $this->version );
		
	}
	
	public function register_front_assets() {
		global $post;		
		
		$options = get_option ( EasySocialShareButtons::$plugin_settings_name );
		if (is_array ( $options )) {
			
			$essb_networks = $options['networks'];
			$mail_active = false;
			$love_active = false;
			
			$use_minified_css = isset($options['use_minified_css']) ? $options['use_minified_css'] : 'false';
			$minifier = ($use_minified_css == "true") ? true : false;
			$this->css_minifier = $minifier;
			
			$use_minified_js = isset($options['use_minified_js']) ? $options['use_minified_js'] : 'false';
			$minifier_js = ($use_minified_js == "true") ? true : false;
			$this->js_minifier = $minifier_js;
			
			$scripts_in_head = isset($options['scripts_in_head']) ? $options['scripts_in_head'] : 'false';
			
			$load_footer = ($scripts_in_head == 'true') ? false : true;
			
			$post_native_skinned =  get_post_meta($post->ID,'essb_activate_nativeskinned',true);
			
			foreach($essb_networks as $k => $v) {
				if( $v[0] == 1 && $k == 'mail') {
					$mail_active = true;
				}
				
				if( $v[0] == 1 && $k == 'love') {
					$love_active = true;
				}
			}
			
			if (is_numeric ( $options ['style'] )) {
				
				$post_theme =  get_post_meta($post->ID,'essb_theme',true);
				if ($post_theme != "" && is_numeric($post_theme)) {
					$options['style'] = intval($post_theme);
				}
				
				$folder = "default";
				
				if ($options ['style'] == 1) { $folder = "default"; }
				if ($options ['style'] == 2) { $folder = "metro"; }
				if ($options ['style'] == 3) { $folder = "modern"; }
				if ($options ['style'] == 4) {
					$folder = "round";
				}
				if ($options ['style'] == 5) {
					$folder = "big";
				}
				if ($options ['style'] == 6) {
					$folder = "metro-retina";
				}
				if ($options ['style'] == 7) {
					$folder = "big-retina";
				}
				if ($options ['style'] == 8) {
					$folder = "light-retina";
				}
				if ($options ['style'] == 9) {
					$folder = "flat-retina";
				}
				
				if ($minifier) {
					wp_enqueue_style ( 'easy-social-share-buttons', ESSB_PLUGIN_URL . '/assets/css/' . $folder . '/' . 'easy-social-share-buttons.min.css', false, $this->version, 'all' );
				}
				else {
					wp_enqueue_style ( 'easy-social-share-buttons', ESSB_PLUGIN_URL . '/assets/css/' . $folder . '/' . 'easy-social-share-buttons.css', false, $this->version, 'all' );
				}
			}
			
			$post_counters =  get_post_meta($post->ID,'essb_counter',true);
			
			if ($post_counters != '') {
				$options ['show_counter'] = intval($post_counters);
			}
			
			if (is_numeric ( $options ['show_counter'] ) && $options ['show_counter'] == 1) {
				$this->counter_included = true;
				if ($minifier_js) {
					wp_enqueue_script ( 'essb-counter-script', ESSB_PLUGIN_URL . '/assets/js/easy-social-share-buttons.min.js', array ( 'jquery' ), $this->version, $load_footer );						
				}
				else {
					wp_enqueue_script ( 'essb-counter-script', ESSB_PLUGIN_URL . '/assets/js/easy-social-share-buttons.js', array ( 'jquery' ), $this->version, $load_footer );
				}
			}
			
			$display_where = isset($options['display_where']) ? $options['display_where'] : '';		
			
			$display_float_js = isset($options['float_js']) ? $options['float_js'] : '';
			$post_display_where = get_post_meta($post->ID,'essb_position',true);
			if ($post_display_where != "") { $display_where = $post_display_where; }			
			
			// @since 1.3.8.2 - mobile display render in alternative way
			if ($this->isMobile()){
				$display_position_mobile = isset($options['display_position_mobile']) ? $options['display_position_mobile'] : '';
					
				if ($display_position_mobile != '') {
					$display_where = $display_position_mobile;
				}
			}
				
			
			if ($display_where == "float") {
				if ($display_float_js == "true") {
					if ($minifier_js) {
						wp_enqueue_script ( 'essb-float-script', ESSB_PLUGIN_URL . '/assets/js/essb-float-js.min.js', array ('jquery' ), $this->version, $load_footer );
					}
					else {
						wp_enqueue_script ( 'essb-float-script', ESSB_PLUGIN_URL . '/assets/js/essb-float-js.js', array ('jquery' ), $this->version, $load_footer );
					}
				}
				else {
					if ($minifier_js) {
						wp_enqueue_script ( 'essb-float-script', ESSB_PLUGIN_URL . '/assets/js/essb-float.min.js', array ('jquery' ), $this->version, $load_footer );
					}
					else {
						wp_enqueue_script ( 'essb-float-script', ESSB_PLUGIN_URL . '/assets/js/essb-float.js', array ('jquery' ), $this->version, $load_footer );
					}
				}
			}
			
			if ($display_where == "sidebar") {
				if ($minifier) {
					wp_enqueue_style ( 'easy-social-share-buttons-sidebar', ESSB_PLUGIN_URL . '/assets/css/essb-sidebar.min.css', false, $this->version, 'all' );
				}
				else {
					wp_enqueue_style ( 'easy-social-share-buttons-sidebar', ESSB_PLUGIN_URL . '/assets/css/essb-sidebar.css', false, $this->version, 'all' );
				}
			}

			if ($display_where == "popup") {
				if ($minifier) {
					wp_enqueue_style ( 'easy-social-share-buttons-popup', ESSB_PLUGIN_URL . '/assets/css/essb-popup.min.css', false, $this->version, 'all' );
				}
				else {
					wp_enqueue_style ( 'easy-social-share-buttons-popup', ESSB_PLUGIN_URL . '/assets/css/essb-popup.css', false, $this->version, 'all' );
				}
				
				if ($minifier_js) {
					wp_enqueue_script ( 'essb-popup-script', ESSB_PLUGIN_URL . '/assets/js/essb-popup.min.js', array ('jquery' ), $this->version, $load_footer );
				}
				else {
					wp_enqueue_script ( 'essb-popup-script', ESSB_PLUGIN_URL . '/assets/js/essb-popup.js', array ('jquery' ), $this->version, $load_footer );
				}
			}
				
			$plusbutton = isset($options['googleplus']) ? $options['googleplus'] : 'false';
			
			if ($plusbutton == 'true') {
				wp_enqueue_script ( 'essb-google-plusone', 'https://apis.google.com/js/plusone.js', array ('jquery' ), $this->version, $load_footer );
			}

			$youtube_button = isset($options['youtubesub']) ? $options['youtubesub'] : 'false';
			if ($youtube_button == 'true') {
				//https://apis.google.com/js/platform.js
				wp_enqueue_script ( 'essb-youtube-subscribe', 'https://apis.google.com/js/platform.js', array ('jquery' ), $this->version, $load_footer );
			}	
			
			$pinfollow = isset($options['pinterestfollow']) ? $options['pinterestfollow'] : 'false';
			if ($pinfollow == "true") {
				wp_enqueue_script ( 'essb-pinterest-follow', '//assets.pinterest.com/js/pinit.js', array ('jquery' ), $this->version, $load_footer );
				$this->pinjs_registered = true;
			}
			
			
			if ($mail_active) {
				// @since 1.1 mail contact form
				// @thanks nextpulse - performace move
				if ($minifier) {
					wp_enqueue_style ( 'easy-social-share-buttons-mailform', ESSB_PLUGIN_URL . '/assets/css/essb-mailform.min.css', false, $this->version, 'all' );
				}
				else {
					wp_enqueue_style ( 'easy-social-share-buttons-mailform', ESSB_PLUGIN_URL . '/assets/css/essb-mailform.css', false, $this->version, 'all' );
				}
				wp_enqueue_script ( 'easy-social-share-buttons-mailform', ESSB_PLUGIN_URL . '/assets/js/essb-mailform.js', array ('jquery' ), $this->version, true );
				$this->mailjs_registered = true;
			}		

			$include_twitter = isset($options['twitterfollow']) ? $options['twitterfollow'] : 'false';
			if ($include_twitter == 'true') {
				//wp_enqueue_script ( 'twitter-essb', 'http://platform.twitter.com/widgets.js', array ('jquery' ) );
			}

			
			
			// @since 1.2.7 stats inject js is moved to footer
			$stats_active = isset($options['stats_active']) ? $options['stats_active'] : 'false';
			if ($stats_active ==  'true') {
				add_action ( 'wp_footer', array ($this->stats, 'generate_log_js_code' ) );				
			}
			
			// @since 1.3.9.1 only if love you button is active to insert code
			if ($love_active) {
				add_action('wp_footer', array ($this->love, 'generate_loveyou_js_code' ) );
			}
			
			$sidebar_sticky = isset($options['sidebar_sticky']) ? $options['sidebar_sticky'] : 'false';
			if ($sidebar_sticky == 'true') {
				if ($minifier_js) {
					wp_enqueue_script ( 'essb-sticky-sidebar', ESSB_PLUGIN_URL . '/assets/js/essb-sticky-sidebar.min.js', array ('jquery' ), $this->version, $load_footer );
				}
				else {
					wp_enqueue_script ( 'essb-sticky-sidebar', ESSB_PLUGIN_URL . '/assets/js/essb-sticky-sidebar.js', array ('jquery' ), $this->version, $load_footer );
				}
			}
			
			/// @test
			$skin_native = isset($options['skin_native']) ? $options['skin_native'] : 'false';
			
			if ($post_native_skinned != '') {
				if ($post_native_skinned == "yes") { 
					$skin_native = "true";
				}

				if ($post_native_skinned == "no") {
					$skin_native = "false";
				}
			}
			
			if ($skin_native == 'true') { 
				$this->skinned_social = true; 
				ESSB_Skinned_Native_Button::registerCSS();
			}
			else {
				$this->skinned_social = false;
			}
		}
		
	}
	
	public function get_current_url($mode = 'base') {
		
		$url = 'http' . (is_ssl () ? 's' : '') . '://' . $_SERVER ['HTTP_HOST'] . $_SERVER ['REQUEST_URI'];
		
		switch ($mode) {
			case 'raw' :
				return $url;
				break;
			case 'base' :
				return reset ( explode ( '?', $url ) );
				break;
			case 'uri' :
				$exp = explode ( '?', $url );
				return trim ( str_replace ( home_url (), '', reset ( $exp ) ), '/' );
				break;
			default :
				return false;
		}
	}
	
	public function generate_share_snippet($networks = array(), $counters = 0, $is_current_page_url = 0, $is_shortcode = 0, $custom_share_text = '', $custom_share_address = '', 
			$shortcode_native = 'yes', $shortcode_sidebar = 'no', $shortcode_messages = 'no', $shortcode_popup = 'no', $shortcode_popafter = '', 
			$shortcode_custom_shareimage = '', $shortcode_custom_sharemessage = '', $shortcode_custom_fblike_url = '', $shortcode_custom_pluson_url = '',
			$shortcode_native_show_fblike = 'no', $shortcode_native_show_twitter = 'no', $shortcode_native_show_plusone = 'no', $shortcode_native_show_vk = 'no',
			$shortcode_hide_network_names = 'no', $shortcode_counter_pos = '', $shortcode_sidebar_pos = '', $shortcode_native_show_youtube = 'no', $shortcode_native_show_pinfollow = 'no', $shortcode_force_nostats = 'no',
			$shortcode_hide_total_count = 'no', $shortcode_total_count_pos = '', $shortcode_full_width = 'no', $shortcode_fullwidth_fix = '', $shortcode_native_show_wpmanaged = 'no', $shortcode_custom_button_texts = array(), 
			$shortcode_float = 'no', $shortcode_fixed_width = 'no', $shortcode_fixed_width_value = '') {
	
		global $post;
		$essb_off = get_post_meta($post->ID,'essb_off',true);
		
		if ($essb_off == "true") { $show_me = false; } else {$show_me = true;}
		//		$show_me =  (get_post_meta($post->ID,'essb_off',true)== 1) ? false : true;			
		$show_me = 	$is_shortcode ? true : $show_me;
		//print $show_me;
		$post_display_where = get_post_meta($post->ID,'essb_position',true);
		$post_hide_network_names = get_post_meta($post->ID,'essb_names',true);
		
		$post_hide_fb = get_post_meta($post->ID,'essb_hidefb',true);
		$post_hide_plusone = get_post_meta($post->ID,'essb_hideplusone',true);
		$post_hide_vk = get_post_meta($post->ID,'essb_hidevk',true);
		$post_hide_twitter = get_post_meta($post->ID, 'essb_hidetwitter', true);
		
		// @since 1.2.3
		$post_hide_youtube = get_post_meta($post->ID, 'essb_hideyoutube', true);
		$post_hide_pinfollow = get_post_meta($post->ID, 'essb_hidepinfollow', true);
		
		$post_hide_wpmanaged = "";
		
		// @since 1.2.1
		$shortcode_force_fblike = false;
		$shortcode_force_twitter = false;
		$shortcode_force_vk = false;
		$shortcode_force_plusone = false;
		$shortcode_force_youtube = false;
		$shortcode_force_pinfollow = false;
		$shortcode_force_wpamanged = false;
		if ($is_shortcode) {
			if ($shortcode_native_show_fblike == "yes") { $post_hide_fb = "no"; $shortcode_force_fblike = true; }
			if ($shortcode_native_show_plusone == "yes") { $post_hide_plusone = "no"; $shortcode_force_plusone = true; }
			if ($shortcode_native_show_twitter == "yes") { $post_hide_twitter = "no"; $shortcode_force_twitter = true; }
			if ($shortcode_native_show_vk == "yes") { $post_hide_vk = "no"; $shortcode_force_vk = true; }
			
			// @since 1.2.3
			if ($shortcode_native_show_youtube == "yes") { $post_hide_youtube = "no"; $shortcode_force_youtube = true; }
			if ($shortcode_native_show_pinfollow == "yes") { $post_hide_pinfollow = "no";  $shortcode_force_pinfollow = true; }
			
			if ($shortcode_native_show_wpmanaged == "yes") { $post_hide_wpmanaged = "no"; $shortcode_force_wpamanged = true; }
			
			if ($shortcode_hide_network_names == "yes") { $post_hide_network_names = '1'; }
		}
		
		$post_sidebar_pos = get_post_meta($post->ID, 'essb_sidebar_pos', true);
		$post_counter_pos = get_post_meta($post->ID, 'essb_counter_pos', true);
		$post_total_counter_pos = get_post_meta($post->ID, 'essb_total_counter_pos', true);
		
		if ($is_shortcode && $shortcode_total_count_pos != '') {
			$post_total_counter_pos = $shortcode_total_count_pos;
		}
		
		// @since 1.2.1
		if ($is_shortcode && $shortcode_counter_pos != '' ) {
			$post_counter_pos = $shortcode_counter_pos;
		}
		
		if ($is_shortcode && $shortcode_sidebar_pos != '') {
			$post_sidebar_pos = $shortcode_sidebar_pos;
		}
		
		$cookie_loved_page = isset($_COOKIE['essb_love_'. $post->ID]) ? true : false;
		
		// custom_share_message_address		
		$post_essb_post_share_message = get_post_meta($post->ID, 'essb_post_share_message', true);
		$post_essb_post_share_url = get_post_meta($post->ID, 'essb_post_share_url', true);
		$post_essb_post_share_image = get_post_meta($post->ID, 'essb_post_share_image', true);
		$post_essb_post_share_text = get_post_meta($post->ID, 'essb_post_share_text', true);
		$post_essb_post_fb_url = get_post_meta($post->ID, 'essb_post_fb_url', true);
		$post_essb_post_plusone_url = get_post_meta($post->ID, 'essb_sidebar_pos', true);
		
		$post_essb_twitter_username = get_post_meta($post->ID, 'essb_post_twitter_username', true);
		$post_essb_twitter_hastags = get_post_meta($post->ID, 'essb_post_twitter_hashtags', true);
		
		$post_essb_as = get_post_meta($post->ID, 'essb_as', true);
		
		$salt = mt_rand ();
		
		// show buttons only if post meta don't ask to hide it, and if it's not a shortcode.
		if ( $show_me ) {
	
			// texts, URL and image to share
			$text = esc_attr(urlencode($post->post_title));
			$url = $post ? get_permalink() : $this->get_current_url( 'raw' );
			//$url = urlencode(get_permalink());
			if ( $is_current_page_url ) {
				$url = $this->get_current_url( 'raw' );
			}
			$url = apply_filters('essb_the_shared_permalink', $url);
			$image = has_post_thumbnail( $post->ID ) ? wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' ) : '';
	
			$pinterest_image = ($image != '') ? $image[0] : '';
			$pinterest_desc = $post->post_excerpt;
			$post_image = ($image != '') ? $image[0] : '';
			$post_desc = $post->post_excerpt;
			
			// some markup filters
			$hide_intro_phrase 			= apply_filters('eesb_network_name', true);
			$share_the_post_sentence 	= apply_filters('eesb_intro_phrase_text', __('Share the post',ESSB_TEXT_DOMAIN) );
			$before_the_sps_content 	= apply_filters('eesb_before_the_snippet', '');
			$after_the_sps_content 		= apply_filters('eesb_after_the_snippet', '');
			$before_the_list 			= apply_filters('eesb_before_the_list', '');
			$after_the_list 			= apply_filters('eesb_after_the_list', '');
			$before_first_i 			= apply_filters('eesb_before_first_item', '');
			$after_last_i 				= apply_filters('eesb_after_last_item', '');
			$container_classes 			= apply_filters('eesb_container_classes', '');
			$rel_nofollow 				= apply_filters('eesb_links_nofollow', 'rel="nofollow"');
	
			// markup filters
			$div 	= apply_filters('eesb_container_tag', 'div');
			$p 		= apply_filters('eesb_phrase_tag', 'p');
			$ul 	= apply_filters('eesb_list_container_tag', 'ul');
			$li 	= apply_filters('eesb_list_of_item_tag', 'li');
	
	
			// get the plugin options
			$options = get_option(  EasySocialShareButtons::$plugin_settings_name );
	
			// @since 1.2.6
			$stats_active = isset($options['stats_active']) ? $options['stats_active'] : 'false';
			
			if ($is_shortcode && $shortcode_force_nostats == 'yes') { $stats_active = 'false'; }
			
			// classes and attributes options
			$target_link = (isset($options['target_link']) && $options['target_link']==1) ? ' target="_blank"' : '';
			$hidden_name_class = (isset($options['hide_social_name']) && $options['hide_social_name']==1) ? ' essb_hide_name' : '';
			
			//@since 1.3.9.2
			$always_hide_names_mobile = (isset($options['always_hide_names_mobile'])) ? $options['always_hide_names_mobile'] : 'false';
			if ($always_hide_names_mobile == 'true' && $this->isMobile()) {
				$hidden_name_class = ' essb_hide_name';
			}
			
			$container_classes .= (intval($counters)==1) ? ' essb_counters' : '';
			$counter_pos = isset($options['counter_pos']) ? $options['counter_pos'] : '';
			$total_counter_pos = isset($options['total_counter_pos']) ? $options['total_counter_pos'] : '';
			
			if ($post_counter_pos != '') { $counter_pos = $post_counter_pos; }
			if ($post_total_counter_pos != '') { $total_counter_pos = $post_total_counter_pos; }
			
			$url_short_native = isset($options['url_short_native']) ? $options['url_short_native'] : 'false';
			$url_short_google = isset($options['url_short_google']) ? $options['url_short_google'] : 'false';
			
			$url_short_bitly =  isset($options['url_short_bitly']) ? $options['url_short_bitly'] : 'false';
			$url_short_bitly_user =  isset($options['url_short_bitly_user']) ? $options['url_short_bitly_user'] : '';
			$url_short_bitly_api =  isset($options['url_short_bitly_api']) ? $options['url_short_bitly_api'] : '';
				
			$facebook_simplesharing = 'true';//isset($options['facebooksimple']) ? $options['facebooksimple'] : 'false';
			$facebook_totalcount = isset($options['facebooktotal']) ? $options['facebooktotal'] : 'false';
			
			// @ from 1.2.9
			$facebook_advanced_sharing = isset($options['facebookadvanced']) ? $options['facebookadvanced'] : 'false';
			$facebook_advanced_sharing_appid = isset($options['facebookadvancedappid']) ? $options['facebookadvancedappid'] : '';
				
			$custom_like_url_active = (isset($options['custom_url_like'])) ? $options['custom_url_like'] : 'false'; 
			$custom_like_url = (isset($options['custom_url_like_address'])) ? $options['custom_url_like_address'] : '';
			$custom_plusone_address = (isset($options['custom_url_plusone_address'])) ? $options['custom_url_plusone_address'] : '';//custom_url_plusone_address

			$native_counters = (isset($options['native_social_counters'])) ? $options['native_social_counters'] : 'false'; 
			$native_counters_fb = (isset($options['native_social_counters_fb'])) ? $options['native_social_counters_fb'] : 'false';
			$native_fb_width = (isset($options['facebook_like_button_width'])) ? $options['facebook_like_button_width'] : '';
			$native_counters_g = (isset($options['native_social_counters_g'])) ? $options['native_social_counters_g'] : 'false';
			$native_counters_t = (isset($options['native_social_counters_t'])) ? $options['native_social_counters_t'] : 'false';
			$native_counters_big = (isset($options['native_social_counters_boxes'])) ? $options['native_social_counters_boxes'] : 'false';
			$native_counters_youtube = (isset($options['native_social_counters_youtube'])) ? $options['native_social_counters_youtube'] : 'false';			
				
			$force_hide_total_count = isset($options['force_hide_total_count']) ? $options['force_hide_total_count'] : 'false';
			// @since 1.3.1
			$force_counter_adminajax = isset($options['force_counters_admin']) ? $options['force_counters_admin'] : 'false';
			
			if ($post_total_counter_pos == "hidden") { $force_hide_total_count = "true";  }
			if ($is_shortcode && $shortcode_hide_total_count == 'yes') { $force_hide_total_count = 'true'; }
			
			$native_lang = isset($options['native_social_language']) ? $options['native_social_language'] : "en";
				
			// @since 1.1.5 popup
			$popup_window_title = (isset($options['popup_window_title'])) ? $options['popup_window_title'] : '';
			$popup_window_close = (isset($options['popup_window_close_after'])) ? $options['popup_window_close_after'] : '';
			
			// @since 1.1.7
			$custom_sidebar_pos = (isset($options['sidebar_pos'])) ? $options['sidebar_pos'] : '';
			if ($post_sidebar_pos != '') { $custom_sidebar_pos = $post_sidebar_pos; }
			if ($custom_sidebar_pos == "left") { $custom_sidebar_pos = ""; }
			
			// @since 1.1.6 popafter
			$popup_popafter = (isset($options['popup_window_popafter'])) ? $options['popup_window_popafter'] : '';
 			if ($is_shortcode && $shortcode_popafter != '') {
				$popup_popafter = $shortcode_popafter;
			}

			// @since 1.2.1
			
			if ($post_essb_post_fb_url != '' || $post_essb_post_plusone_url != '') { 
				$custom_like_url_active = "true";
				if ($post_essb_post_fb_url != '') { $custom_like_url = $post_essb_post_fb_url; }
				if ($post_essb_post_plusone_url != '' ) { $custom_plusone_address = $post_essb_post_plusone_url; }
			}
			
			if ($is_shortcode && ($shortcode_custom_fblike_url != '' || $shortcode_custom_pluson_url != '')) { $custom_like_url_active = "true"; }
			if ($is_shortcode && $shortcode_custom_fblike_url != '') { $custom_like_url = $shortcode_custom_fblike_url; }
			if ($is_shortcode && $shortcode_custom_pluson_url != '') {
				$custom_plusone_address = $shortcode_custom_pluson_url;
			}
			
			if ($custom_like_url_active == 'false') { $custom_like_url = ""; $custom_plusone_address = ""; }
			
			if ($url_short_native == 'true') {
				$short_url = wp_get_shortlink();
				if ($short_url != '') { $url = $short_url;}
			}
			
			if ($url_short_google == 'true') { 
				$url = $this->google_shorten($url);
			}
			
			if ($url_short_bitly == "true") {
				$url = $this->bitly_shorten($url, $url_short_bitly_user, $url_short_bitly_api);
			}
	
			// custom share message
			$active_custom_message =isset($options['customshare']) ? $options['customshare'] : 'false';
			$is_from_customshare = false;
			
			if ($facebook_advanced_sharing == 'true')  { $is_from_customshare = true; } 
			
			$custom_share_imageurl = isset($options['customshare_imageurl']) ? $options['customshare_imageurl'] : '';
			$custom_share_description = isset($options['customshare_description']) ? $options['customshare_description'] : '';
				
			// @since 1.2.1
			if ($post_essb_post_share_image != '' || $post_essb_post_share_message != '' || $post_essb_post_share_text != '' || $post_essb_post_share_url != '') {
				$active_custom_message = "true";
				
				if ($post_essb_post_share_image != '') { $custom_share_imageurl = $post_essb_post_share_image; }
				if ($post_essb_post_share_message != '') { $custom_share_text = $post_essb_post_share_message; }
				if ($post_essb_post_share_text != '') { $custom_share_description = $post_essb_post_share_text; }
				if ($post_essb_post_share_url != '') { $custom_share_address = $post_essb_post_share_url; }
			}
			
			
			if ($is_shortcode && $shortcode_custom_sharemessage != '') {
				$custom_share_description = $shortcode_custom_sharemessage;
			}
			if ($is_shortcode && $shortcode_custom_shareimage != '') {
				$custom_share_imageurl = $shortcode_custom_shareimage;
			}
			
			if ($is_shortcode && $shortcode_custom_sharemessage != '') {
			
			}
			
			$pinterest_sniff_disable = isset($options['pinterest_sniff_disable']) ? $options['pinterest_sniff_disable'] : 'false';
			
			$include_twitter = isset($options['twitterfollow']) ? $options['twitterfollow'] : 'false';
			$include_twitter_user = isset($options['twitterfollowuser']) ? $options['twitterfollowuser'] : '';
			
			$include_twitter_type = isset($options['twitter_tweet']) ? $options['twitter_tweet'] : '';
			
			// @since 1.2.3
			$include_youtube = isset($options['youtubesub']) ? $options['youtubesub'] : 'false';
			$include_youtube_channel = isset($options['youtubechannel']) ? $options['youtubechannel'] : '';
			
			$include_pinfollow = isset($options['pinterestfollow']) ? $options['pinterestfollow'] : 'false';
			$include_pinfollow_disp = isset($options['pinterestfollow_disp']) ? $options['pinterestfollow_disp'] : '';
			$include_pinfollow_url = isset($options['pinterestfollow_url']) ? $options['pinterestfollow_url'] : '';
			$include_pintype = isset($options['pinterest_native_type']) ? $options['pinterest_native_type'] : '';
			
			$include_managedwp = isset($options['managedwp_button']) ? $options['managedwp_button'] : 'false';
			
			$append_twitter_user_to_message = isset($options['twitteruser']) ? $options['twitteruser'] : '';
			$append_twitter_hashtags = isset($options['twitterhashtags']) ? $options['twitterhashtags'] : '';
			$twitter_nojspop = isset($options['twitter_nojspop']) ? $options['twitter_nojspop'] : 'false';
			$using_yoast_ga = isset($options['using_yoast_ga']) ? $options['using_yoast_ga'] : 'false';
			
			if ($post_essb_twitter_username != '') {
				$append_twitter_user_to_message = $post_essb_twitter_username;
			}
			if ($post_essb_twitter_hastags != '') {
				$append_twitter_hashtags = $post_essb_twitter_hastags;
			} 
			
			$twitter_shareshort = isset($options['twitter_shareshort']) ? $options['twitter_shareshort'] : 'false';
			$twitter_shareshort_service = isset($options['twitter_shareshort_service']) ? $options['twitter_shareshort_service'] : '';
				
			//$append_facebook_hashtags = isset($options['facebookhashtags']) ? $options['facebookhashtags'] : '';
			$append_facebook_hashtags = "";
			// @since 1.1.1
			$otherbuttons_sameline = isset($options['otherbuttons_sameline']) ? $options['otherbuttons_sameline'] : 'false';
			
			if ($custom_share_text == '' && $active_custom_message == 'true') {
				$custom_share_text = isset($options['customshare_text']) ? $options['customshare_text'] : '';
				
			}
			if ($custom_share_text != '') {
				$text = $custom_share_text;
				$is_from_customshare = true;
			}
				
			if ($custom_share_address == '' && $active_custom_message == 'true') {
				$custom_share_address = isset($options['customshare_url']) ? $options['customshare_url'] : '';
				
			}
			if ($custom_share_address != '') {
				$url = $custom_share_address;
			}
			
			if ($custom_share_description != '' && $active_custom_message == 'true') {
				$pinterest_desc = $custom_share_description;
			}
				
			if ($custom_share_imageurl != '' && $active_custom_message == 'true') {
				$pinterest_image = $custom_share_imageurl;
			}
			
			// other options
			$display_where = isset($options['display_where']) ? $options['display_where'] : '';
			
			if ($post_display_where != '') { $display_where = $post_display_where; }
			
			// @since 1.3.8.2 - mobile display render in alternative way
			if ($this->isMobile()){
				$display_position_mobile = isset($options['display_position_mobile']) ? $options['display_position_mobile'] : '';
					
				if ($display_position_mobile != '') {
					$display_where = $display_position_mobile;
				}
			}
				
			
			if ($post_hide_network_names == '1') {
				$hidden_name_class = ' essb_hide_name';
			}
			
			// @since 1.1.3
			if ($is_shortcode) { $display_where = "shortcode"; }
			if ($is_shortcode && $shortcode_sidebar == 'yes') { $display_where = "sidebar"; }
			if ($is_shortcode && $shortcode_popup == 'yes') { $display_where = "popup"; }
			
			// @since 1.3.9.3
			if ($is_shortcode && $shortcode_float == "yes") {
				$display_where = "float";
			}
			
			if ($display_where == "popup") {
				$container_classes = "essb_popup_counters";
			}
			
			if ($display_where != "sidebar") {
				$custom_sidebar_pos = "";
			}
			else {
				if ($custom_sidebar_pos != '') {
					$custom_sidebar_pos = "_".$custom_sidebar_pos;
				}
			}
			
			//print "display where = " . $display_where;
			//print $native_counters_g;
			
			$force_pinterest_snif = 1;
			if ($pinterest_sniff_disable == 'true') { $force_pinterest_snif = 0; }

			if ($custom_like_url == "") { $custom_like_url = $url; }
			if ($custom_plusone_address == "") { $custom_plusone_address = $url; }
			
			$user_network_messages = isset($options['network_message']) ? $options['network_message'] : '';
			$user_advanced_share = isset($options['advanced_share']) ? $options['advanced_share'] : '';
			
			if ($post_essb_as != '') {
				$user_advanced_share = $post_essb_as;
			}
				
			$message_above_share = isset($options['message_share_buttons']) ? $options['message_share_buttons'] : '';
			$message_above_like = isset($options['message_like_buttons']) ? $options['message_like_buttons'] : '';
			
			$message_above_share = preg_replace(array('#%%title%%#', '#%%siteurl%%#', '#%%permalink%%#'), array(get_the_title(), get_site_url(), get_permalink()), $message_above_share);
			$message_above_like = preg_replace(array('#%%title%%#', '#%%siteurl%%#', '#%%permalink%%#'), array(get_the_title(), get_site_url(), get_permalink()), $message_above_like);
				
			// @since 1.2.2 where are available likeshare and sharelike display methods
			$is_forced_hidden_networks = false;
			$like_share_display_position = "";
			if (!$is_shortcode) {
				//print $display_where;
				if ($display_where == "likeshare" && $this->print_links_position == "top") {
					$networks = array("no"); $is_forced_hidden_networks = true;
					$like_share_display_position = " essb-like-buttons";
				}
				if ($display_where == "likeshare" && $this->print_links_position == "bottom") {
					$shortcode_native = "no";
					$like_share_display_position = " essb-share-buttons";
						
				}
				if ($display_where == "sharelike" && $this->print_links_position == "bottom") {
					$networks = array("no"); $is_forced_hidden_networks = true;
					$like_share_display_position = " essb-like-buttons";
				}
				if ($display_where == "sharelike" && $this->print_links_position == "top") {
					$shortcode_native = "no";
					$like_share_display_position = " essb-share-buttons";
				}
			}
				
			
			if ($message_above_share != "" && !$is_shortcode && !$is_forced_hidden_networks) { $before_the_list .= '<div class="essb_message_above_share">'.stripslashes($message_above_share)."</div>";}
			if ($message_above_share != "" && $is_shortcode && $shortcode_messages == "yes") {  $before_the_list .= '<div class="essb_message_above_share">'.stripslashes($message_above_share)."</div>"; }
			
			// @developer fix to attach class for template
			$loaded_template_id = isset($options ['style']) ? $options ['style']  : '';
				
			$post_theme =  get_post_meta($post->ID,'essb_theme',true);
			if ($post_theme != "" && is_numeric($post_theme)) {
				$loaded_template_id = intval($post_theme);
			}
				
			$loaded_template_id = intval($loaded_template_id);
			$loaded_template = "default";
				
			if ($loaded_template_id == 1) {
				$loaded_template = "default";
			}
			if ($loaded_template_id == 2) {
				$loaded_template = "metro";
			}
			if ($loaded_template_id == 3) {
				$loaded_template = "modern";
			}
			if ($loaded_template_id == 4) {
				$loaded_template = "round";
			}
			if ($loaded_template_id == 5) {
				$loaded_template = "big";
			}
			if ($loaded_template_id == 6) {
				$loaded_template = "metro-retina";
			}
			if ($loaded_template_id == 7) {
				$loaded_template = "big-retina";
			}
			if ($loaded_template_id == 8) {
				$loaded_template = "light-retina";
			}
			if ($loaded_template_id == 9) {
				$loaded_template = "flat-retina";
			}
				
			
			// networks to display
			// 2 differents results by :
			// -- using hook (options from admin panel)
			// -- using shortcode/template-function (the array $networks in parameter of this function)
			$essb_networks = array();
			
			if ( count($networks) > 0 ) {
				$essb_networks = array();
				foreach($options['networks'] as $k => $v) {
					if(in_array($k, $networks)) {
						$essb_networks[$k]=$v;
						$essb_networks[$k][0]=1; //set its visible value to 1 (visible)
					}
				}
			
			}
			else {
				$essb_networks = $options['networks'];
			}
			
			
			// full width fix starts here;
			$is_fullwidth_mode = false;
			$css_fullwidth_container = "";
			$css_fullwidth_list = "";
			$css_fullwidth_item = "";
			$css_fullwidth_item_link = "";
			if ($is_shortcode && $shortcode_full_width == "yes") {
				$is_fullwidth_mode = true;
				$css_fullwidth_container = ' style="width: 100% !important;"';
				$css_fullwidth_list = ' style="width: 100% !important;"';
				
				$cnt = 0;
				foreach($essb_networks as $k => $v) {
					if( $v[0] == 1 ) {
						$cnt++;
					}
				}
				
				// @since 1.3.9.3
				if ($cnt > 0) {
					$item_width = 100 / $cnt;
				}
				else {
					$item_width = 100;
				}
				
				if ($shortcode_fullwidth_fix == '') { $shortcode_fullwidth_fix = "80"; }
				
				$css_fullwidth_item = ' style="width: '.intval($item_width).'% !important;"';
				$css_fullwidth_item_link = ' style="width: '.$shortcode_fullwidth_fix.'% !important; text-align: center;"';
			}
			
			// full width from settings
			$fullwidth_share_buttons = isset($options['fullwidth_share_buttons']) ? $options['fullwidth_share_buttons'] : '';
			$fullwidth_share_buttons_correction = isset($options['fullwidth_share_buttons_correction']) ? $options['fullwidth_share_buttons_correction'] : '';
			
			$post_fullwidth_share_buttons = get_post_meta($post->ID, 'essb_activate_fullwidth', true);
			if ($post_fullwidth_share_buttons != '') {
				if ($post_fullwidth_share_buttons == "yes") {
					$fullwidth_share_buttons = "true";
				}
				else {
					$fullwidth_share_buttons = "false";
				}
			}
			
			if (!$is_shortcode && $fullwidth_share_buttons == "true" && $display_where != "sidebar" && $display_where != "popup") {
				$is_fullwidth_mode = true;
				$css_fullwidth_container = ' style="width: 100% !important;"';
				$css_fullwidth_list = ' style="width: 100% !important;"';
				
				$cnt = 0;
				foreach($essb_networks as $k => $v) {
					if( $v[0] == 1 ) {
						$cnt++;
					}
				}
				$item_width = 100 / $cnt;
				
				if ($fullwidth_share_buttons_correction == '') {
					$fullwidth_share_buttons_correction = "80";
				}
				
				$css_fullwidth_item = ' style="width: '.intval($item_width).'% !important;"';
				$css_fullwidth_item_link = ' style="width: '.$fullwidth_share_buttons_correction.'% !important; text-align: center;"';
			}
			
			// @since 1.3.9.3
			$fixed_width_active = isset($options['fixed_width_active']) ? $options['fixed_width_active'] : '';
			$fixed_width_value = isset($options['fixed_width_value']) ? $options['fixed_width_value'] : '';
			
			if ($is_shortcode && $shortcode_fixed_width == 'yes') {
				$width_value = ($shortcode_fixed_width_value != '' ) ? $shortcode_fixed_width_value : '80';
				$css_fullwidth_item_link = ' style="width: '.$width_value.'px !important; text-align: center;"';
				$css_fullwidth_item_link = ' style="width: '.$width_value.'px !important; text-align: center;"';
			}
			if (!$is_shortcode && $fixed_width_active == 'true') {
				$width_value = ($fixed_width_value != '' ) ? $fixed_width_value : '80';
				$css_fullwidth_item_link = ' style="width: '.$width_value.'px !important; text-align: center;"';
				$css_fullwidth_item_link = ' style="width: '.$width_value.'px !important; text-align: center;"';
			}				
			
			// beginning markup
			$block_content = $before_the_sps_content;
			$block_content .= "\n".'<'.$div.' class="essb_links '.$container_classes.' essb_displayed_'.$display_where.$custom_sidebar_pos.$like_share_display_position.' essb_template_'.$loaded_template.'" id="essb_displayed_'.$display_where.'" '.$css_fullwidth_container.'>';
			$block_content .= $hide_intro_phrase ? '' : "\n".'<'.$p.' class="screen-reader-text essb_maybe_hidden_text">'.$share_the_post_sentence.' "'.get_the_title().'"</'.$p.'>'."\n";
			$block_content .= $before_the_list;
			$block_content .= "\n\t".'<'.$ul.' class="essb_links_list'.$hidden_name_class.'" '.$css_fullwidth_list.'>';
			$block_content .= $before_first_i;
	
			// @since 1.3.0
			$general_counters = (isset($options['show_counter']) && $options['show_counter']==1) ? 1 : 0;
			if ($is_forced_hidden_networks) {
				$general_counters = 0; $counters = 0;
			}
				
			if (($general_counters==1 && intval($counters)==1) || ($general_counters==0 && intval($counters)==1)) {
				if ($total_counter_pos == 'left' || $total_counter_pos == "leftbig") {
					if ($total_counter_pos == "leftbig") {
						$block_content .= '<li class="essb_item essb_totalcount_item" '.($force_hide_total_count == 'true' ? 'style="display: none !important;"' : '').'><span class="essb_totalcount essb_t_l_big" title="" title_after=""><span class="essb_t_nb"></span></span></li>';
					}
					else {
						$block_content .= '<li class="essb_item essb_totalcount_item" '.($force_hide_total_count == 'true' ? 'style="display: none !important;"' : '').'><span class="essb_totalcount essb_t_l" title_after="" title="'.__('Total: ', ESSB_TEXT_DOMAIN).'"><span class="essb_t_nb"></span></span></li>';
					}
				}
					
			}
				
	
			$active_fb = false;		
			$active_pinsniff = false;		
			$active_mail = false;	
			$message_body = "";
			$message_subject = "";		

			// each links (come from options or manual array)
			foreach($essb_networks as $k => $v) {
				if( $v[0] == 1 ) {
					$api_link = $api_text = '';
					$url = apply_filters('essb_the_shared_permalink_for_'.$k, $url);
	
					$twitter_user = '';

					if ($append_twitter_user_to_message != '' ) { $twitter_user .= '&amp;related='.$append_twitter_user_to_message.'&amp;via='.$append_twitter_user_to_message; }
					//$twitter_user .= '&amp;hashtags=demo,demo1,demo2';
					if ($append_twitter_hashtags != '') {
						$twitter_user .= '&amp;hashtags='.$append_twitter_hashtags;
					}
					
					switch ($k) {
						case "twitter" :
							//$api_link = 'https://twitter.com/intent/tweet?source=webclient&amp;original_referer='.$url.'&amp;text='.$text.'&amp;url='.$url.$twitter_user;
							// @since 1.3.9.3 to allow usage of # in message
							$twitter_message = $text;
							$twitter_message = str_replace('#', '%23', $twitter_message);
														
							$api_link = 'https://twitter.com/intent/tweet?source=webclient&amp;original_referer='.$url.'&amp;text='.$twitter_message.'&amp;url='.$url.$twitter_user;
							
							if ($twitter_shareshort == 'true' && !$is_from_customshare) {
								
								$short_twitter = wp_get_shortlink();
								if ($twitter_shareshort_service == 'goo.gl') {
									$short_twitter = $this->google_shorten($url);
								}
								if ($twitter_shareshort_service == "bit.ly") {
									$short_twitter = $this->bitly_shorten($url, $url_short_bitly_user, $url_short_bitly_api);
								}
								
								$api_link = 'https://twitter.com/intent/tweet?source=webclient&amp;original_referer='.$url.'&amp;text='.$text.'&amp;url='.$short_twitter.'&amp;counturl='.$url.$twitter_user;
							}
							
							$api_text = apply_filters('essb_share_text_for_'.$k, __('Share this article on Twitter',ESSB_TEXT_DOMAIN));
							
							if ($user_network_messages != '') {
								$custom_text = isset($user_network_messages[$k]) ? $user_network_messages[$k] : '';
								if ($custom_text != "") { $api_text = $custom_text; }
							}
							
							// @since 1.3.2
							if ($user_advanced_share != '') {
								$user_advanced_share_message_pass = isset ( $user_advanced_share [$k . '_t'] ) ? $user_advanced_share [$k . '_t'] : '';
								$user_advanced_share_url_pass = isset ( $user_advanced_share [$k . '_u'] ) ? $user_advanced_share [$k . '_u'] : '';
								$user_advanced_share_image_pass = isset ( $user_advanced_share [$k . '_i'] ) ? $user_advanced_share [$k . '_i'] : '';
								$user_advanced_share_desc_pass = isset ( $user_advanced_share [$k . '_d'] ) ? $user_advanced_share [$k . '_d'] : '';
								
								if ($user_advanced_share_message_pass != '' || $user_advanced_share_url_pass != '') {
									
									if ($user_advanced_share_url_pass == '') { $user_advanced_share_url_pass = $url; }
									if ($user_advanced_share_message_pass == '') { $user_advanced_share_message_pass = $text; }
									
									$api_link = 'https://twitter.com/intent/tweet?source=webclient&amp;original_referer='.$user_advanced_share_url_pass.'&amp;text='.$user_advanced_share_message_pass.'&amp;url='.$user_advanced_share_url_pass.$twitter_user;
								}
							}
							
							break;
	
						case "facebook" :
							//https://www.facebook.com/dialog/feed?app_id=145634995501895&display=popup&caption=An%20example%20caption&link=https%3A%2F%2Fdevelopers.facebook.com%2Fdocs%2Fdialogs%2F&redirect_uri=https://developers.facebook.com/tools/explorer&description=
							
							$api_link = 'https://www.facebook.com/dialog/feed?app_id='.$facebook_advanced_sharing_appid.'&display=popup&name='.($text).'&link='.urlencode($url).'&redirect_uri=https://www.facebook.com';//'https://www.facebook.com/sharer/sharer.php?s=100&p[url]='.$url.'&p&#91;title]='.$text.$append_facebook_hashtags;
							if ($post_image != '') {
								$api_link .= '&picture='.$post_image;
							}
							if (!$post_desc != '') {
								$api_link .= '&description='.urlencode($post_desc);
							}
							
							if ($facebook_simplesharing == 'true') {
								$api_link = 'http://www.facebook.com/sharer/sharer.php?u='.$url;
							}
							
							if ($is_from_customshare) {
								//$api_link = 'https://www.facebook.com/sharer/sharer.php?s=100&p[url]='.$url.'&p&#91;title]='.$text.$append_facebook_hashtags;
								$api_link = 'https://www.facebook.com/dialog/feed?app_id='.$facebook_advanced_sharing_appid.'&display=popup&name='.($text).'&link='.urlencode($url).'&redirect_uri=https://www.facebook.com';//'https://www.facebook.com/sharer/sharer.php?s=100&p[url]='.$url.'&p&#91;title]='.$text.$append_facebook_hashtags;
								
								if ($custom_share_description != '') {
									//$api_link .= '&p&#91;summary]='.$custom_share_description;
									$api_link .= '&description='.urlencode($custom_share_description);
								}
								// @ fix in 1.0.8
								if ($custom_share_imageurl != '') {
									//$api_link .= '&p&#91;images][0]='.$custom_share_imageurl;
									$api_link .= '&picture='.$custom_share_imageurl;
								}	

								$api_link = preg_replace(array('#%%title%%#', '#%%siteurl%%#', '#%%permalink%%#'), array(get_the_title(), get_site_url(), get_permalink()), $api_link);
								
							}
							
							$api_text = apply_filters('essb_share_text_for_'.$k, __('Share this article on Facebook',ESSB_TEXT_DOMAIN));
							
							if ($user_network_messages != '') {
								$custom_text = isset($user_network_messages[$k]) ? $user_network_messages[$k] : '';
								if ($custom_text != "") {
									$api_text = $custom_text;
								}
							}
							
							// @since 1.3.2
							if ($user_advanced_share != '') {
								$user_advanced_share_message_pass = isset ( $user_advanced_share [$k . '_t'] ) ? $user_advanced_share [$k . '_t'] : '';
								$user_advanced_share_url_pass = isset ( $user_advanced_share [$k . '_u'] ) ? $user_advanced_share [$k . '_u'] : '';
								$user_advanced_share_image_pass = isset ( $user_advanced_share [$k . '_i'] ) ? $user_advanced_share [$k . '_i'] : '';
								$user_advanced_share_desc_pass = isset ( $user_advanced_share [$k . '_d'] ) ? $user_advanced_share [$k . '_d'] : '';
							
								if ($user_advanced_share_message_pass != '' || $user_advanced_share_url_pass != '') {
										
									if ($user_advanced_share_url_pass == '') {
										$user_advanced_share_url_pass = $url;
									}
									if ($user_advanced_share_message_pass == '') {
										$user_advanced_share_message_pass = $text;
									}
									
									if ($user_advanced_share_image_pass == '') {
										$user_advanced_share_image_pass = $custom_share_imageurl;	
									}
									
									if ($user_advanced_share_desc_pass == '') {
										$user_advanced_share_desc_pass = $custom_share_description;
									}
										
									if ($facebook_simplesharing == 'true') {
										$api_link = 'http://www.facebook.com/sharer/sharer.php?u='.$user_advanced_share_url_pass;
									}
									else {
										$api_link = 'https://www.facebook.com/dialog/feed?app_id='.$facebook_advanced_sharing_appid.'&display=popup&name='.urlencode($user_advanced_share_message_pass).'&link='.urlencode($user_advanced_share_url_pass).'&redirect_uri=https://www.facebook.com';//'https://www.facebook.com/sharer/sharer.php?s=100&p[url]='.$url.'&p&#91;title]='.$text.$append_facebook_hashtags;
								
										if ($user_advanced_share_desc_pass != '') {
									
											$api_link .= '&description='.urlencode($user_advanced_share_desc_pass);
										}
										if ($user_advanced_share_image_pass != '') {
											//$api_link .= '&p&#91;images][0]='.$custom_share_imageurl;
											$api_link .= '&picture='.$user_advanced_share_image_pass;
										}	

										$api_link = preg_replace(array('#%%title%%#', '#%%siteurl%%#', '#%%permalink%%#'), array(get_the_title(), get_site_url(), get_permalink()), $api_link);
									}
								}
							}
							
							break;
	
						case "google" :
							$api_link = 'https://plus.google.com/share?url='.$url;
							$api_text = apply_filters('essb_share_text_for_'.$k, __('Share this article on Google+',ESSB_TEXT_DOMAIN));
							if ($user_network_messages != '') {
								$custom_text = isset($user_network_messages[$k]) ? $user_network_messages[$k] : '';
								if ($custom_text != "") {
									$api_text = $custom_text;
								}
							}
							
							// @since 1.3.2
							if ($user_advanced_share != '') {
								$user_advanced_share_url_pass = isset ( $user_advanced_share [$k . '_u'] ) ? $user_advanced_share [$k . '_u'] : '';
							
								if ($user_advanced_share_url_pass != '') {
									$api_link = 'https://plus.google.com/share?url='.$user_advanced_share_url_pass;
								}
							}
							
							break;
	
						case "pinterest" :
							if ( $pinterest_image != '' && $force_pinterest_snif==0 ) {
								$api_link = 'http://pinterest.com/pin/create/bookmarklet/?media='.$pinterest_image.'&amp;url='.$url.'&amp;title='.$text.'&amp;description='.$pinterest_desc;
								$api_link = preg_replace(array('#%%title%%#', '#%%siteurl%%#', '#%%permalink%%#'), array(get_the_title(), get_site_url(), get_permalink()), $api_link);								
							}
							else {
								//$api_link = "javascript:void((function(){var%20e=document.createElement('script');e.setAttribute('type','text/javascript');e.setAttribute('charset','UTF-8');e.setAttribute('src','http://assets.pinterest.com/js/pinmarklet.js?r='+Math.random()*99999999);document.body.appendChild(e)})());";
								$api_link = "javascript:void(0);";
								$target_link = "";
								$active_pinsniff = true;
							}
							$api_text = apply_filters('essb_share_text_for_'.$k, __('Share an image of this article on Pinterest',ESSB_TEXT_DOMAIN));
							
							if ($user_network_messages != '') {
								$custom_text = isset($user_network_messages[$k]) ? $user_network_messages[$k] : '';
								if ($custom_text != "") {
									$api_text = $custom_text;
								}
							}
							
							// @since 1.3.2
							if ($user_advanced_share != '') {
								$user_advanced_share_message_pass = isset ( $user_advanced_share [$k . '_t'] ) ? $user_advanced_share [$k . '_t'] : '';
								$user_advanced_share_url_pass = isset ( $user_advanced_share [$k . '_u'] ) ? $user_advanced_share [$k . '_u'] : '';
								$user_advanced_share_image_pass = isset ( $user_advanced_share [$k . '_i'] ) ? $user_advanced_share [$k . '_i'] : '';
								$user_advanced_share_desc_pass = isset ( $user_advanced_share [$k . '_d'] ) ? $user_advanced_share [$k . '_d'] : '';
									
								if ($user_advanced_share_message_pass != '' || $user_advanced_share_url_pass != '' || $user_advanced_share_image_pass != '') {
							
									if ($user_advanced_share_url_pass == '') {
										$user_advanced_share_url_pass = $url;
									}
									if ($user_advanced_share_message_pass == '') {
										$user_advanced_share_message_pass = $text;
									}
										
									if ($user_advanced_share_image_pass == '') {
										$user_advanced_share_image_pass = $pinterest_image;
									}
										
									if ($user_advanced_share_desc_pass == '') {
										$user_advanced_share_desc_pass = $pinterest_desc;
									}
							
									$api_link = 'http://pinterest.com/pin/create/bookmarklet/?media='.$user_advanced_share_image_pass.'&amp;url='.$user_advanced_share_url_pass.'&amp;title='.$user_advanced_share_message_pass.'&amp;description='.$user_advanced_share_desc_pass;
								}
							}
						break;
	
	
						case 'linkedin':
							$api_link = "http://www.linkedin.com/shareArticle?mini=true&amp;ro=true&amp;trk=EasySocialShareButtons&amp;title=".$text."&amp;url=".$url;
							$api_text = apply_filters('essb_share_text_for_'.$k, __('Share this article on LinkedIn',ESSB_TEXT_DOMAIN));
							
							if ($user_network_messages != '') {
								$custom_text = isset($user_network_messages[$k]) ? $user_network_messages[$k] : '';
								if ($custom_text != "") {
									$api_text = $custom_text;
								}
							}
							
							// @since 1.3.2
							if ($user_advanced_share != '') {
								$user_advanced_share_url_pass = isset ( $user_advanced_share [$k . '_u'] ) ? $user_advanced_share [$k . '_u'] : '';
								$user_advanced_share_message_pass = isset ( $user_advanced_share [$k . '_t'] ) ? $user_advanced_share [$k . '_t'] : '';
								
								if ($user_advanced_share_message_pass == '') {
									$user_advanced_share_message_pass = $text;
								}
								
								if ($user_advanced_share_url_pass != '') {
									$api_link = "http://www.linkedin.com/shareArticle?mini=true&amp;ro=true&amp;trk=EasySocialShareButtons&amp;title=".$user_advanced_share_message_pass."&amp;url=".$user_advanced_share_url_pass;
								}
							}
								
							break;
	
						case 'digg':
							$api_link = "http://digg.com/submit?phase=2%20&amp;url=".$url."&amp;title=".$text;
							$api_text = apply_filters('essb_share_text_for_'.$k, __('Share this article on Digg',ESSB_TEXT_DOMAIN));
							
							if ($user_network_messages != '') {
								$custom_text = isset($user_network_messages[$k]) ? $user_network_messages[$k] : '';
								if ($custom_text != "") {
									$api_text = $custom_text;
								}
							}
							
							// @since 1.3.2
							if ($user_advanced_share != '') {
								$user_advanced_share_url_pass = isset ( $user_advanced_share [$k . '_u'] ) ? $user_advanced_share [$k . '_u'] : '';
								$user_advanced_share_message_pass = isset ( $user_advanced_share [$k . '_t'] ) ? $user_advanced_share [$k . '_t'] : '';
								
								if ($user_advanced_share_message_pass == '') {
									$user_advanced_share_message_pass = $text;
								}	
								if ($user_advanced_share_url_pass != '') {
									$api_link = "http://digg.com/submit?phase=2%20&amp;url=".$user_advanced_share_url_pass."&amp;title=".$user_advanced_share_message_pass;
								}
							}
							break;

							case 'reddit':
								$api_link = "http://reddit.com/submit?url=".$url."&amp;title=".$text;
								$api_text = apply_filters('essb_share_text_for_'.$k, __('Share this article on Reddit',ESSB_TEXT_DOMAIN));
									
								if ($user_network_messages != '') {
									$custom_text = isset($user_network_messages[$k]) ? $user_network_messages[$k] : '';
									if ($custom_text != "") {
										$api_text = $custom_text;
									}
								}
									
								// @since 1.3.2
								if ($user_advanced_share != '') {
									$user_advanced_share_url_pass = isset ( $user_advanced_share [$k . '_u'] ) ? $user_advanced_share [$k . '_u'] : '';
									$user_advanced_share_message_pass = isset ( $user_advanced_share [$k . '_t'] ) ? $user_advanced_share [$k . '_t'] : '';
							
									if ($user_advanced_share_message_pass == '') {
										$user_advanced_share_message_pass = $text;
									}
									if ($user_advanced_share_url_pass != '') {
										$api_link = "http://reddit.com/submit?url=".$user_advanced_share_url_pass."&amp;title=".$user_advanced_share_message_pass;
									}
								}
								break;
								
								case 'del':
									$api_link = "https://delicious.com/save?v=5&noui&jump=close&url=".$url."&title=".$text;
									$api_text = apply_filters('essb_share_text_for_'.$k, __('Share this article on Delicious',ESSB_TEXT_DOMAIN));
										
									if ($user_network_messages != '') {
										$custom_text = isset($user_network_messages[$k]) ? $user_network_messages[$k] : '';
										if ($custom_text != "") {
											$api_text = $custom_text;
										}
									}
										
									// @since 1.3.2
									if ($user_advanced_share != '') {
										$user_advanced_share_url_pass = isset ( $user_advanced_share [$k . '_u'] ) ? $user_advanced_share [$k . '_u'] : '';
										$user_advanced_share_message_pass = isset ( $user_advanced_share [$k . '_t'] ) ? $user_advanced_share [$k . '_t'] : '';
											
										if ($user_advanced_share_message_pass == '') {
											$user_advanced_share_message_pass = $text;
										}
										if ($user_advanced_share_url_pass != '') {
											$api_link = "https://delicious.com/save?v=5&noui&jump=close&url=".$user_advanced_share_url_pass."&amp;title=".$user_advanced_share_message_pass;
										}
									}
									break;		
									case 'buffer':
										$api_link = "https://bufferapp.com/add?url=".$url."&text=".$text."&via=&picture=&count=horizontal&source=button";
										$api_text = apply_filters('essb_share_text_for_'.$k, __('Share this article on Buffer',ESSB_TEXT_DOMAIN));
									
										if ($user_network_messages != '') {
											$custom_text = isset($user_network_messages[$k]) ? $user_network_messages[$k] : '';
											if ($custom_text != "") {
												$api_text = $custom_text;
											}
										}
									
										// @since 1.3.2
										if ($user_advanced_share != '') {
											$user_advanced_share_url_pass = isset ( $user_advanced_share [$k . '_u'] ) ? $user_advanced_share [$k . '_u'] : '';
											$user_advanced_share_message_pass = isset ( $user_advanced_share [$k . '_t'] ) ? $user_advanced_share [$k . '_t'] : '';
												
											if ($user_advanced_share_message_pass == '') {
												$user_advanced_share_message_pass = $text;
											}
											if ($user_advanced_share_url_pass != '') {
												$api_link = "https://bufferapp.com/add?url=".$user_advanced_share_url_pass."&text=".$user_advanced_share_message_pass."&via=&picture=&count=horizontal&source=button";
												
											}
										}
										break;
										case 'love':
											$api_link = "javascript:void(0);";
											$api_text = apply_filters('essb_share_text_for_'.$k, __('Love This!',ESSB_TEXT_DOMAIN));
																							
											break;										
						case 'stumbleupon':
							$api_link = "http://www.stumbleupon.com/badge/?url=".$url;
							$api_text = apply_filters('essb_share_text_for_'.$k, __('Share this article on StumbleUpon',ESSB_TEXT_DOMAIN));
							
							if ($user_network_messages != '') {
								$custom_text = isset($user_network_messages[$k]) ? $user_network_messages[$k] : '';
								if ($custom_text != "") {
									$api_text = $custom_text;
								}
							}
							
							// @since 1.3.2
							if ($user_advanced_share != '') {
								$user_advanced_share_url_pass = isset ( $user_advanced_share [$k . '_u'] ) ? $user_advanced_share [$k . '_u'] : '';
									
								if ($user_advanced_share_url_pass != '') {
										$api_link = "http://www.stumbleupon.com/badge/?url=".$user_advanced_share_url_pass;
								}
							}
							break;
	
						case 'tumblr':
							$api_link = "http://tumblr.com/share?s=&v=3&t=".$text."&u=".urlencode($url);
							$api_text = apply_filters('essb_share_text_for_'.$k, __('Share this article on Tumblr',ESSB_TEXT_DOMAIN));
							
							if ($user_network_messages != '') {
								$custom_text = isset($user_network_messages[$k]) ? $user_network_messages[$k] : '';
								if ($custom_text != "") {
									$api_text = $custom_text;
								}
							}
							
							
							// @since 1.3.2
							if ($user_advanced_share != '') {
								$user_advanced_share_url_pass = isset ( $user_advanced_share [$k . '_u'] ) ? $user_advanced_share [$k . '_u'] : '';
								$user_advanced_share_message_pass = isset ( $user_advanced_share [$k . '_t'] ) ? $user_advanced_share [$k . '_t'] : '';
							
								if ($user_advanced_share_message_pass == '') {
									$user_advanced_share_message_pass = $text;
								}
								if ($user_advanced_share_url_pass != '') {
									$api_link = "http://tumblr.com/share?s=&v=3&t=".$user_advanced_share_message_pass."&u=".urlencode($user_advanced_share_url_pass);
								}
							}
							break;
									
	
						case 'vk':
							$api_link = "http://vkontakte.ru/share.php?url=".$url;
							$api_text = apply_filters('essb_share_text_for_'.$k, __('Share this article on VKontakte',ESSB_TEXT_DOMAIN));
							
							if ($user_network_messages != '') {
								$custom_text = isset($user_network_messages[$k]) ? $user_network_messages[$k] : '';
								if ($custom_text != "") {
									$api_text = $custom_text;
								}
							}
							
							// @since 1.3.2
							if ($user_advanced_share != '') {
								$user_advanced_share_url_pass = isset ( $user_advanced_share [$k . '_u'] ) ? $user_advanced_share [$k . '_u'] : '';
								$user_advanced_share_message_pass = isset ( $user_advanced_share [$k . '_t'] ) ? $user_advanced_share [$k . '_t'] : '';
									
								if ($user_advanced_share_message_pass == '') {
									$user_advanced_share_message_pass = $text;
								}
								if ($user_advanced_share_url_pass != '') {
									$api_link = "http://vkontakte.ru/share.php?url=".$user_advanced_share_url_pass;
								}
							}
							break;
						case 'ok':
							$api_link = "http://www.odnoklassniki.ru/dk?st.cmd=addShare&st.s=1&st._surl=".$url;
							$api_text = apply_filters('essb_share_text_for_'.$k, __('Share this article on Odnoklassniki',ESSB_TEXT_DOMAIN));
							
							if ($user_network_messages != '') {
								$custom_text = isset($user_network_messages[$k]) ? $user_network_messages[$k] : '';
								if ($custom_text != "") {
									$api_text = $custom_text;
								}
							}
							
							// @since 1.3.2
							if ($user_advanced_share != '') {
								$user_advanced_share_url_pass = isset ( $user_advanced_share [$k . '_u'] ) ? $user_advanced_share [$k . '_u'] : '';
								$user_advanced_share_message_pass = isset ( $user_advanced_share [$k . '_t'] ) ? $user_advanced_share [$k . '_t'] : '';
									
								if ($user_advanced_share_message_pass == '') {
									$user_advanced_share_message_pass = $text;
								}
								if ($user_advanced_share_url_pass != '') {
									$api_link = "http://www.odnoklassniki.ru/dk?st.cmd=addShare&st.s=1&st._surl=".$user_advanced_share_url_pass;
								}
							}
							break;
							case 'weibo':
								$api_link = "http://service.weibo.com/share/share.php?url=".$url;
								$api_text = apply_filters('juiz_sps_share_text_for_'.$k, __('Share this article on Weibo',ESSB_TEXT_DOMAIN));
								
								if ($user_network_messages != '') {
									$custom_text = isset($user_network_messages[$k]) ? $user_network_messages[$k] : '';
									if ($custom_text != "") {
										$api_text = $custom_text;
									}
								}
									
								// @since 1.3.2
								if ($user_advanced_share != '') {
									$user_advanced_share_url_pass = isset ( $user_advanced_share [$k . '_u'] ) ? $user_advanced_share [$k . '_u'] : '';
									$user_advanced_share_message_pass = isset ( $user_advanced_share [$k . '_t'] ) ? $user_advanced_share [$k . '_t'] : '';
										
									if ($user_advanced_share_message_pass == '') {
										$user_advanced_share_message_pass = $text;
									}
									if ($user_advanced_share_url_pass != '') {
										$api_link = "http://service.weibo.com/share/share.php?url=".$user_advanced_share_url_pass;
									}
								}								
							break;	
							case 'xing':
								$api_link = "https://www.xing.com/social_plugins/share?h=1;url=".$url;
								$api_text = apply_filters('juiz_sps_share_text_for_'.$k, __('Share this article on Xing',ESSB_TEXT_DOMAIN));
							
								if ($user_network_messages != '') {
									$custom_text = isset($user_network_messages[$k]) ? $user_network_messages[$k] : '';
									if ($custom_text != "") {
										$api_text = $custom_text;
									}
								}
									
								// @since 1.3.2
								if ($user_advanced_share != '') {
									$user_advanced_share_url_pass = isset ( $user_advanced_share [$k . '_u'] ) ? $user_advanced_share [$k . '_u'] : '';
									$user_advanced_share_message_pass = isset ( $user_advanced_share [$k . '_t'] ) ? $user_advanced_share [$k . '_t'] : '';
							
									if ($user_advanced_share_message_pass == '') {
										$user_advanced_share_message_pass = $text;
									}
									if ($user_advanced_share_url_pass != '') {
										$api_link = "https://www.xing.com/social_plugins/share?h=1;url=".$user_advanced_share_url_pass;
									}
								}
								break;
									
							case 'pocket':
								$api_link = "https://getpocket.com/save?title=".$text."&url=".urlencode($url);
								$api_text = apply_filters('essb_share_text_for_'.$k, __('Share this article on Pocket',ESSB_TEXT_DOMAIN));
									
								if ($user_network_messages != '') {
									$custom_text = isset($user_network_messages[$k]) ? $user_network_messages[$k] : '';
									if ($custom_text != "") {
										$api_text = $custom_text;
									}
								}
									
									
								// @since 1.3.2
								if ($user_advanced_share != '') {
									$user_advanced_share_url_pass = isset ( $user_advanced_share [$k . '_u'] ) ? $user_advanced_share [$k . '_u'] : '';
									$user_advanced_share_message_pass = isset ( $user_advanced_share [$k . '_t'] ) ? $user_advanced_share [$k . '_t'] : '';
										
									if ($user_advanced_share_message_pass == '') {
										$user_advanced_share_message_pass = $text;
									}
									if ($user_advanced_share_url_pass != '') {
										$api_link = "https://getpocket.com/save?title=".$user_advanced_share_message_pass."&url=".urlencode($user_advanced_share_url_pass);
									}
								}
								break;
									
						case 'print':
							$api_link = "window.print(); return false;";
							
							if ($stats_active == 'true') {
								$api_link .= " essb_handle_stats('print');";
							}
							$api_text = apply_filters('essb_share_text_for_'.$k, __('Print this article',ESSB_TEXT_DOMAIN));
							if ($user_network_messages != '') {
								$custom_text = isset($user_network_messages[$k]) ? $user_network_messages[$k] : '';
								if ($custom_text != "") {
									$api_text = $custom_text;
								}
							}
							break;	
						case 'flattr' :
							// currently Flattr does not support cusomize of share options
							$api_link = ESSB_Extension_Flattr::getStaticFlattrUrl();
							break;
							
						case 'mail' :
							if (strpos($options['mail_body'], '%%') || strpos($options['mail_subject'], '%%') ) {
								$api_link = esc_attr('mailto:?subject='.$options['mail_subject'].'&amp;body='.$options['mail_body']);
								$api_link = preg_replace(array('#%%title%%#', '#%%siteurl%%#', '#%%permalink%%#'), array(get_the_title(), get_site_url(), get_permalink()), $api_link);
								
							}
							else {
								$api_link = 'mailto:?subject='.$options['mail_subject'].'&amp;body='.$options['mail_body']." : ".$url;
							}
							$message_subject = $options['mail_subject'];
							$message_body = $options['mail_body'];
							$message_subject = preg_replace(array('#%%title%%#', '#%%siteurl%%#', '#%%permalink%%#'), array(get_the_title(), get_site_url(), get_permalink()), $message_subject);
							$message_body = preg_replace(array('#%%title%%#', '#%%siteurl%%#', '#%%permalink%%#'), array(get_the_title(), get_site_url(), get_permalink()), $message_body);
							
							$api_link = "javascript:void(0);";
							
							$api_text = apply_filters('essb_share_text_for_'.$k, __('Share this article with a friend (email)',ESSB_TEXT_DOMAIN));
							if ($user_network_messages != '') {
								$custom_text = isset($user_network_messages[$k]) ? $user_network_messages[$k] : '';
								if ($custom_text != "") {
									$api_text = $custom_text;
								}
							}
							$active_mail = true;
							break;
					}
	
					$network_name = isset($v[1]) ? $v[1] : $k;
					
					if ($is_shortcode && is_array($shortcode_custom_button_texts)) {
						$custom_shortcode_netowrk_name = isset($shortcode_custom_button_texts[$k]) ? $shortcode_custom_button_texts[$k] : '';
						if ($custom_shortcode_netowrk_name != '') {
							$network_name = $custom_shortcode_netowrk_name;
						}
					}
					
					$network_name = esc_attr(stripslashes($network_name));
					// @since 1.3.9.2 - fix problem with ' in share text
					$api_link= str_replace("'", "\'", $api_link);
					
					if ($counter_pos == "inside") { $network_name = ""; }
					
					if ($k != 'mail' && $k != 'pinterest') {
						if ($k == "print") {
							$block_content .= '<'.$li.' class="essb_item essb_link_'.$k.' nolightbox" '.$css_fullwidth_item.'><a href="#" onclick="'.$api_link.'" '.$rel_nofollow.' title="'.$api_text.'"'.$target_link.' '.$css_fullwidth_item_link.'><span class="essb_icon"></span><span class="essb_network_name">'.$network_name.'</span></a></'.$li.'>';								
						}
						else if ($k == "love") {
							// @since 1.3.7 - love button handle stats
							if ($stats_active == "true") {
								// essb_handle_stats('love');								
								$block_content .= '<'.$li.' class="essb_item essb_link_'.$k.' nolightbox" '.$css_fullwidth_item.'><a href="'.$api_link.'" onclick="essb_handle_loveyou(\''.$cookie_loved_page.'\', this); essb_handle_stats(\'love\'); return false;" '.$rel_nofollow.' title="'.$api_text.'"'.$target_link.' '.$css_fullwidth_item_link.' '.($cookie_loved_page ? 'disabled="disabled"': '').'><span class="essb_icon"></span><span class="essb_network_name">'.$network_name.'</span></a></'.$li.'>';
							}
							else {
								$block_content .= '<'.$li.' class="essb_item essb_link_'.$k.' nolightbox" '.$css_fullwidth_item.'><a href="'.$api_link.'" onclick="essb_handle_loveyou(\''.$cookie_loved_page.'\', this); return false;" '.$rel_nofollow.' title="'.$api_text.'"'.$target_link.' '.$css_fullwidth_item_link.' '.($cookie_loved_page ? 'disabled="disabled"': '').'><span class="essb_icon"></span><span class="essb_network_name">'.$network_name.'</span></a></'.$li.'>';
							}							
						}
						else {
							
							if ($k == "twitter") {
								if ($twitter_nojspop == 'true') {
									$block_content .= '<'.$li.' class="essb_item essb_link_'.$k.' nolightbox" '.$css_fullwidth_item.'><a href="'.$api_link.'" '.$rel_nofollow.' title="'.$api_text.'"'.$target_link.' '.$css_fullwidth_item_link.'><span class="essb_icon"></span><span class="essb_network_name">'.$network_name.'</span></a></'.$li.'>';
								}
								else {
									if ($using_yoast_ga == "true") {
										$block_content .= '<'.$li.' class="essb_item essb_link_'.$k.' nolightbox" '.$css_fullwidth_item.'><a href="'.'#'.'" '.$rel_nofollow.' title="'.$api_text.'"'.$target_link.' onclick="essb_window'.$salt.'(&#39;'.$api_link.'&#39;, &#39;'.$k.'&#39;); return false;" '.$css_fullwidth_item_link.'><span class="essb_icon"></span><span class="essb_network_name">'.$network_name.'</span></a></'.$li.'>';
									}
									else {
										$block_content .= '<'.$li.' class="essb_item essb_link_'.$k.' nolightbox" '.$css_fullwidth_item.'><a href="'.'#'.'" '.$rel_nofollow.' title="'.$api_text.'"'.$target_link.' onclick="essb_window'.$salt.'(\''.$api_link.'\', \''.$k.'\'); return false;" '.$css_fullwidth_item_link.'><span class="essb_icon"></span><span class="essb_network_name">'.$network_name.'</span></a></'.$li.'>';
									}
								}
							}
							else {
								if ($using_yoast_ga == "true") {
									$block_content .= '<'.$li.' class="essb_item essb_link_'.$k.' nolightbox" '.$css_fullwidth_item.'><a href="'.$api_link.'" '.$rel_nofollow.' title="'.$api_text.'"'.$target_link.' onclick="essb_window'.$salt.'(&#39;'.$api_link.'&#39;, &#39;'.$k.'&#39;); return false;" '.$css_fullwidth_item_link.'><span class="essb_icon"></span><span class="essb_network_name">'.$network_name.'</span></a></'.$li.'>';
								}
								else {
									$block_content .= '<'.$li.' class="essb_item essb_link_'.$k.' nolightbox" '.$css_fullwidth_item.'><a href="'.$api_link.'" '.$rel_nofollow.' title="'.$api_text.'"'.$target_link.' onclick="essb_window'.$salt.'(\''.$api_link.'\', \''.$k.'\'); return false;" '.$css_fullwidth_item_link.'><span class="essb_icon"></span><span class="essb_network_name">'.$network_name.'</span></a></'.$li.'>';
								}
							}
						}						
					}
					else {
						if ($k == 'pinterest') {
							if (! $active_pinsniff) {
								if ($using_yoast_ga == "true") {
									$block_content .= '<' . $li . ' class="essb_item essb_link_' . $k . ' nolightbox" '.$css_fullwidth_item.'><a href="' . $api_link . '" ' . $rel_nofollow . ' title="' . $api_text . '"' . $target_link . ' onclick="essb_window'.$salt.'(&#39;' . $api_link . '&#39;, &#39;'.$k.'&#39;); return false;" '.$css_fullwidth_item_link.'><span class="essb_icon"></span><span class="essb_network_name">' . $network_name . '</span></a></' . $li . '>';
								}
								else {
									$block_content .= '<' . $li . ' class="essb_item essb_link_' . $k . ' nolightbox" '.$css_fullwidth_item.'><a href="' . $api_link . '" ' . $rel_nofollow . ' title="' . $api_text . '"' . $target_link . ' onclick="essb_window'.$salt.'(\'' . $api_link . '\', \''.$k.'\'); return false;" '.$css_fullwidth_item_link.'><span class="essb_icon"></span><span class="essb_network_name">' . $network_name . '</span></a></' . $li . '>';
								}
							} else {
								$block_content .= '<' . $li . ' class="essb_item essb_link_' . $k . ' nolightbox" '.$css_fullwidth_item.'><a href="' . $api_link . '" ' . $rel_nofollow . ' title="' . $api_text . '"' . $target_link . ' '.$css_fullwidth_item_link.' onclick="essb_pinterenst(); return false;"><span class="essb_icon"></span><span class="essb_network_name">' . $network_name . '</span></a></' . $li . '>';
							}
						} else {
							$block_content .= '<' . $li . ' class="essb_item essb_link_' . $k . ' nolightbox" '.$css_fullwidth_item.'><a id="essb-mailform'.$salt.'" href="' . $api_link . '" ' . $rel_nofollow . ' title="' . $api_text . '" '.$css_fullwidth_item_link.'><span class="essb_icon"></span><span class="essb_network_name">' . $network_name . '</span></a></' . $li . '>';
						}
					}
	
				}
			}
			
			
			$post_counters =  get_post_meta($post->ID,'essb_counter',true);
				
			if ($post_counters != '') {
				$options ['show_counter'] = $post_counters;
			}			
			
			$include_plusone_button = isset($options['googleplus']) ? $options['googleplus'] : 'false';
			$include_fb_likebutton = isset($options['facebook_like_button']) ? $options['facebook_like_button'] : '';
			$include_vklike = isset($options['vklike']) ? $options['vklike'] : '';
		
			// @since 1.2.1
			if ($shortcode_force_fblike) { $include_fb_likebutton = "true"; }
			if ($shortcode_force_plusone) { $include_plusone_button = "true"; }
			if ($shortcode_force_twitter) { $include_twitter = "true"; }
			if ($shortcode_force_vk) { $include_vklike = "true"; }
			
			if ($shortcode_force_youtube) { $include_youtube = "true"; }
			if ($shortcode_force_pinfollow) { $include_pinfollow = "true"; }
			if ($shortcode_force_wpamanged) { $include_managedwp = "true"; }
			
			if ($post_hide_fb == 'yes') {
				$include_fb_likebutton = 'false';
			}
			if ($post_hide_plusone == 'yes') {
				$include_plusone_button = 'false';
			}
			if ($post_hide_vk == 'yes') {
				$include_vklike = 'false';
			}
			
			if ($post_hide_twitter == 'yes') { $include_twitter = 'false'; $include_twitter_user = ''; }
			if ($post_hide_youtube == "yes") { $include_youtube = 'false';  $include_youtube_channel = ''; }
			
			if ($post_hide_pinfollow == "yes") { $include_pinfollow = 'false'; $include_pinfollow_disp = ""; $include_pinfollow_url = ""; }
				
			if ($post_hide_wpmanaged == "yes") { $include_managedwp = "false";}
			
			if ($shortcode_native == 'no') {
				
				$include_fb_likebutton = 'false';
				$include_plusone_button = 'false';
				$include_vklike = 'false';
				$include_twitter = 'false'; $include_twitter_user = '';
				$include_youtube = 'false';  $include_youtube_channel = '';
				$include_pinfollow = 'false'; $include_pinfollow_disp = ""; $include_pinfollow_url = "";
				$include_managedwp = 'false';				
			}
			
			if ($shortcode_native == "selected") {
				if (!$shortcode_force_fblike) { $include_fb_likebutton = 'false'; }
				if (!$shortcode_force_plusone) { $include_plusone_button = 'false'; }
				if (!$shortcode_force_vk) { $include_vklike = 'false'; }
				if (!$shortcode_force_twitter) { $include_twitter = 'false'; $include_twitter_user = ''; }
				if (!$shortcode_force_youtube) { $include_youtube = 'false';  $include_youtube_channel = ''; }
				if (!$shortcode_force_pinfollow) { $include_pinfollow = 'false'; $include_pinfollow_disp = ""; $include_pinfollow_url = ""; }
				if (!$shortcode_force_wpamanged) { $include_managedwp = "false"; }
			}
			
			$general_counters = (isset($options['show_counter']) && $options['show_counter']==1) ? 1 : 0;
			if ($is_forced_hidden_networks) { $general_counters = 0; $counters = 0; }
			$hidden_info = '<input type="hidden" class="essb_info_plugin_url" value="'.ESSB_PLUGIN_URL.'" /><input type="hidden" class="essb_info_permalink" value="'.$url.'" />';
			$hidden_info .= '<input type="hidden" class="essb_fb_total_count" value="'.$facebook_totalcount.'" />';
			$hidden_info .= '<input type="hidden" class="essb_counter_ajax" value="'.$force_counter_adminajax.'"/>';
			// counter_pos
			if (($general_counters==1 && intval($counters)==1) || ($general_counters==0 && intval($counters)==1)) {
				$hidden_info .= '<input type="hidden" class="essb_info_counter_pos" value="'.$counter_pos.'" />';
			}
			
			$block_content .= $after_last_i;
			if (($general_counters==1 && intval($counters)==1) || ($general_counters==0 && intval($counters)==1)) {
				if ($total_counter_pos == 'right' || $total_counter_pos == "rightbig" || $total_counter_pos == "") {
					if ($total_counter_pos == "rightbig") {
						$block_content .= '<li class="essb_item essb_totalcount_item" '.($force_hide_total_count == 'true' ? 'style="display: none !important;"' : '').'><span class="essb_totalcount essb_t_r_big" title="" title_after=""><span class="essb_t_nb"></span></span></li>';
					}
					else {
						$block_content .= '<li class="essb_item essb_totalcount_item" '.($force_hide_total_count == 'true' ? 'style="display: none !important;"' : '').'><span class="essb_totalcount" title_after="" title="'.__('Total: ', ESSB_TEXT_DOMAIN).'"><span class="essb_t_nb"></span></span></li>';
					}
				}
			
			}
			
			// @since 1.1.1
			if ($otherbuttons_sameline == 'true') {

				if ($include_plusone_button == 'true') {
					$block_content .= '<li class="essb_item essb_native_item essb_native_plusone_item"><div>'.$this->print_plusone_button($custom_plusone_address, $native_counters_g, $native_lang).'</div></li>';
				}
				
				if ($include_twitter == 'true') {
					if ($include_twitter_type == 'tweet') {
						$block_content .= '<li class="essb_item essb_native_item essb_native_twitter_item"><div>'.$this->print_twitter_tweet_button($include_twitter_user, $native_counters_t, $native_lang).'</div></li>';
					}
					else {
						$block_content .= '<li class="essb_item essb_native_item essb_native_twitter_item"><div>'.$this->print_twitter_follow_button($include_twitter_user, $native_counters_t, $native_lang).'</div></li>';
					}
				}
												
				if ($include_fb_likebutton == 'true') {
					$block_content .= '<li class="essb_item essb_native_item essb_native_facebook_item"><div>'.$this->print_fb_likebutton($custom_like_url, $native_counters_fb, $native_fb_width).'</div></li>';
				}

				if ($include_youtube == 'true') {
					$block_content .= '<li class="essb_item essb_native_item essb_native_youtube_item"><div>'.$this->print_youtube_button($include_youtube_channel, $native_counters_youtube).'</div></li>';
				}
				
				if ($include_pinfollow == 'true') {
					$block_content .= '<li class="essb_item essb_native_item essb_native_pinfollow_item"><div>'.$this->print_pinterest_follow($include_pinfollow_disp, $include_pinfollow_url, $include_pintype).'</div></li>';
				}
				if ($include_managedwp == "true") {
					$block_content .= '<li class="essb_item essb_native_item essb_native_managedwp_item"><div>'.$this->print_managedwp_button($url, $text).'</div></li>';						
				}

				if ($include_vklike == 'true') {
					$block_content .= '<li class="essb_item essb_native_item essb_native_vk_item"><div>'.$this->print_vklike_button($custom_like_url, $native_counters).'</div></li>';
				}
				
			}
			
			$block_content .= '</'.$ul.'>'."\n\t";
			$block_content .= $after_the_list;
			$block_content .= ( ($general_counters==1 && intval($counters)==1) || ($general_counters==0 && intval($counters)==1))  ? $hidden_info : '';
							
			if ($otherbuttons_sameline != 'true') {
				if ($include_fb_likebutton == 'true' || $include_plusone_button == 'true' || $include_vklike == 'true' || $include_twitter == 'true') {
					
					if ($message_above_like != "" && !$is_shortcode) {
						$block_content .= '<div class="essb_message_above_like">'.stripslashes($message_above_like)."</div>";
					}
					
					if ($message_above_like != "" && $is_shortcode && $shortcode_messages == "yes") {
						$block_content .= '<div class="essb_message_above_share">'.stripslashes($message_above_like)."</div>";
					}
						
						
					if ($this->skinned_social) {
						$block_content .= '<div style="display: inline-block; width: 100%; padding-top: 3px !important;" class="essb_native_skinned">';						
					}
					else {
						$block_content .= '<div style="display: inline-block; width: 100%; padding-top: 3px !important; overflow: hidden; padding-right: 10px;" class="essb_native">';
					}				
				}
				
				if ($include_plusone_button == 'true') {
					//$block_content .= '<'.$div.' class="" style="position: relative; float: left;">'.$this->print_plusone_button($url).'</'.$div.'>';		
					$block_content .= $this->print_plusone_button($custom_plusone_address, $native_counters_g, $native_lang);		
				}

				if ($include_twitter == 'true') {
					if ($include_twitter_type == 'tweet') {
						$block_content .= $this->print_twitter_tweet_button($include_twitter_user, $native_counters_t, $native_lang);
					}
					else {
						$block_content .= $this->print_twitter_follow_button($include_twitter_user, $native_counters_t, $native_lang);						
					}
				}
				
			
				if ($include_fb_likebutton == 'true') {
					//$block_content .= '<'.$div.' class="" style="postion: relative; float: left; padding-top:3px !important;">'.$this->print_fb_likebutton($url).'</'.$div.'>';
					$block_content .= $this->print_fb_likebutton($custom_like_url, $native_counters_fb, $native_fb_width);
				}
				
				if ($include_youtube == 'true' ){ 
					$block_content .= $this->print_youtube_button($include_youtube_channel, $native_counters_youtube);
				
				}
				
				if ($include_pinfollow == 'true') {
					$block_content .= $this->print_pinterest_follow($include_pinfollow_disp, $include_pinfollow_url, $include_pintype);
				}
				if ($include_managedwp == "true") {
					$block_content .= ''.$this->print_managedwp_button($url, $text).'';
				}
				
				if ($include_vklike == 'true') {
					$block_content .= $this->print_vklike_button($custom_like_url, $native_counters);
				}
				
				// @since 1.1.1 added vklike
				if ($include_fb_likebutton == 'true' || $include_plusone_button == 'true' || $include_vklike == 'true' || $include_twitter == 'true' || $include_youtube == 'true' || $include_pinfollow == 'true' || $include_managedwp == "true") {
					$block_content .= '</div>';
				}
			}
				
			$block_content .= '</'.$div.'>'."\n\n";
			$block_content .= $after_the_sps_content;
	
			$block_content .= '<script type="text/javascript">';
			if ($stats_active == 'true') {
				$block_content .= 'function essb_window'.$salt.'(oUrl, oService) { if (oService == "twitter") { window.open( oUrl, "essb_share_window", "height=300,width=500,resizable=1,scrollbars=yes" ); }  else { window.open( oUrl, "essb_share_window", "height=500,width=800,resizable=1,scrollbars=yes" ); } essb_handle_stats(oService);  }; ';
				$block_content .= " function essb_pinterenst() { essb_handle_stats('pinterest'); var e=document.createElement('script');e.setAttribute('type','text/javascript');e.setAttribute('charset','UTF-8');e.setAttribute('src','//assets.pinterest.com/js/pinmarklet.js?r='+Math.random()*99999999);document.body.appendChild(e)};";
			}
			else {
				$block_content .= 'function essb_window'.$salt.'(oUrl, oService) { if (oService == "twitter") { window.open( oUrl, "essb_share_window", "height=300,width=500,resizable=1,scrollbars=yes" ); }  else { window.open( oUrl, "essb_share_window", "height=500,width=800,resizable=1,scrollbars=yes" ); }  }; ';
				$block_content .= " function essb_pinterenst() { var e=document.createElement('script');e.setAttribute('type','text/javascript');e.setAttribute('charset','UTF-8');e.setAttribute('src','//assets.pinterest.com/js/pinmarklet.js?r='+Math.random()*99999999);document.body.appendChild(e)};";
			}
			
			$sidebar_sticky = isset($options['sidebar_sticky']) ? $options['sidebar_sticky'] : 'false';
			if ($display_where == "sidebar" && $sidebar_sticky == 'true') {
				$block_content .= 'jQuery(document).ready(function() {
				jQuery(\'#essb_displayed_sidebar\').stickySidebar({				
				footerThreshold: 100
			});
				
				});';
			}
			//$block_content .= 'jQuery(document).ready(function() {
            //jQuery(\'.essb_link_facebook\').tooltipster({interactive: true});
			//jQuery(\'.essb_link_facebook\').tooltipster(\'update\', jQuery(\'#essb_fb_commands\').html());
			//
			//});';
			
			// @since 1.3.1 - moved to option for admin-ajax call
			$block_content .= "var essb_count_data = {
    'ajax_url': '" . admin_url ('admin-ajax.php') . "'
};";
			
			$block_content .= '</script>';
				
			if ($active_mail) {
				$block_content .= $this->print_popup_mailform($message_subject, $message_body, $salt, $stats_active);
			}
			
			// @since 1.3.9.1 - popup information is rendered only when popup method is really active
			if ($display_where == "popup") {
				$block_content .= '<input type="hidden" name="essb_settings_popup_title" id="essb_settings_popup_title" value="'.stripslashes($popup_window_title).'"/>';
				$block_content .= '<input type="hidden" name="essb_settings_popup_close" id="essb_settings_popup_close" value="'.$popup_window_close.'"/>';
				$block_content .= '<input type="hidden" name="essb_settings_popup_template" id="essb_settings_popup_template" value="'.$loaded_template.'"/>';
				if ($popup_popafter != "") {
					$block_content .= '<input type="hidden" name="essb_settings_popup_popafter" id="essb_settings_popup_popafter" value="'.$popup_popafter.'"/>';
					$block_content .= '<div style="display: none;" id="essb_settings_popafter_counter"></div>';
				}
			
				if ((intval($counters)==1) && $display_where == "popup") {
					$block_content .= '<input type="hidden" name="essb_settings_popup_counters" id="essb_settings_popup_counters" value="yes"/>';
				}
			}

			//@since 1.2.7 this is moved to wp_footer
			//if ($stats_active == 'true') {
			//	$block_content .= $this->stats->generate_log_js_code();
			//}
			
			//$block_content .= '<div id="essb_fb_commands" style="display: block;">'.$this->print_fb_sharebutton($url).$this->print_fb_likebutton($url). '</div>';
	
			// final markup
	
			return $block_content;
	
		} // end of if post meta hide sharing buttons
	
	} 
	
	public function print_managedwp_button($url, $text) {
		$output = '<div class="essb_managedwp" style="display: inline-block; overflow: hidden;"><script src="http://managewp.org/share.js" data-type="small" data-title="'.$text.'" data-url="'.$url.'"></script></div>';
		
		return $output;
	}
	
	public function print_pinterest_follow($disp, $url, $type, $skinned_text = '', $skinned_width = '') {
		if ($this->skinned_social) {
			if ($type == 'pin') {
				$code = '<a href="//www.pinterest.com/pin/create/button/" data-pin-do="buttonBookmark" ><img src="//assets.pinterest.com/images/pidgets/pinit_fg_en_rect_gray_20.png" /></a>';
			}
			else {
				$code = '<a data-pin-do="buttonFollow" href="'.$url.'">'.$disp.'</a>';
			}
			$output = ESSB_Skinned_Native_Button::generateButton('pinterest', $code, "follow", $skinned_text, $skinned_width, $this->skinned_social_selected_skin);
			//$output = ESSB_Skinned_Native_Button::generateCircleButton("google", $code);
		}
		else {
			if ($type == 'pin') {
				$output = '<div class="essb_pinterest_follow"  style="display: inline-block; overflow: hidden; vertical-align: top;margin-right: 5px;"><a href="//www.pinterest.com/pin/create/button/" data-pin-do="buttonBookmark" ><img src="//assets.pinterest.com/images/pidgets/pinit_fg_en_rect_gray_20.png" /></a></div>';
			}
			else {
 				$output = '<div class="essb_pinterest_follow"  style="display: inline-block; overflow: hidden; vertical-align: top;margin-right: 5px;"><a data-pin-do="buttonFollow" href="'.$url.'">'.$disp.'</a></div>';
			}
		}
		return $output;
	}
	
	public function print_youtube_button($channel, $native_counters, $skinned_text = '', $skinned_width = '') {
		$output = "";
		if ($this->skinned_social) {
			if ($native_counters == "false") {
				$code = '<div class="g-ytsubscribe" data-channel="'.$channel.'" data-layout="default" data-count="hidden"></div>';
			}
			else {
				$code = '<div class="g-ytsubscribe" data-channel="'.$channel.'" data-layout="default" data-count="default"></div>';
			}
			$output = ESSB_Skinned_Native_Button::generateButton('youtube', $code, "subscribe", $skinned_text, $skinned_width, $this->skinned_social_selected_skin);
			//$output = ESSB_Skinned_Native_Button::generateCircleButton("google", $code);
		}
		else {
		
		//@Emiel add float left for correct display on Safari
		if ($native_counters == "false") {
			$output = '<div style="display: inline-block; overflow: hidden; vertical-align:top;margin-right: 5px; margin-left: 5px;" class="essb_youtube_subscribe"><div class="g-ytsubscribe" data-channel="'.$channel.'" data-layout="default" data-count="hidden"></div></div>';
		}
		else {
			$output = '<div style="display: inline-block; overflow: hidden; vertical-align: top;margin-right: 5px; margin-left: 5px;" class="essb_youtube_subscribe"><div class="g-ytsubscribe" data-channel="'.$channel.'" data-layout="default" data-count="default"></div></div>';
		}
		}
		
		return $output;
	}
	
	public function print_vklike_button($address, $native_counters, $skinned_text = '', $skinned_width = '') {
		if ($this->skinned_social) {
			$code = '<div id="vk_like" style="float: left; poistion: relative;"></div>';
			$output = ESSB_Skinned_Native_Button::generateButton('vk', $code, "like", $skinned_text, $skinned_width, $this->skinned_social_selected_skin);
			//$output = ESSB_Skinned_Native_Button::generateCircleButton("google", $code);
		}
		else {
		
		
		$output = '<div class="essb-vk" style="display: inline-block;vertical-align: top;overflow: hidden;height: 20px; margin-right: 5px;"><div id="vk_like" style="float: left; poistion: relative;"></div></div>';
		}
		return $output;
	}
	
	function print_plusone_button($address, $native_counters, $native_lang, $skinned_text = '', $skinned_width = '') {
		if ($this->skinned_social) {
			if ($native_counters == "false") {
				$code = '<div class="g-plusone" data-size="medium" data-href="' . $address . '" data-annotation="none"></div>';
			}
			else {
				$code = '<div class="g-plusone" data-size="medium" data-href="' . $address . '"></div>';
			}
			$output = ESSB_Skinned_Native_Button::generateButton('google', $code, "+1", $skinned_text, $skinned_width, $this->skinned_social_selected_skin);
			//$output = ESSB_Skinned_Native_Button::generateCircleButton("google", $code);
		} 
		else {
			if ($native_counters == "false") {
				$output = '<div style="display: inline-block; overflow: hidden; height: 24px; max-height: 24px; margin-left: 5px; margin-right: 5px;  vertical-align: top;" class="essb_google_plusone"><div class="g-plusone" data-size="medium" data-href="' . $address . '" data-annotation="none"></div></div>';
			} else {
				$output = '<div style="display: inline-block; overflow: hidden; height: 24px; max-height: 24px; margin-left: 5px; margin-right: 5px; vertical-align: top;" class="essb_google_plusone"><div class="g-plusone" data-size="medium" data-href="' . $address . '"></div></div>';
			}
		}
		
		return $output;
	}
	
	function print_fb_likebutton($address, $native_counters, $native_fb_width, $skinned_text = '', $skinned_width = '') {
		if ($this->skinned_social) {
			if ($native_counters == "false") {
				if (trim($native_fb_width) == "") {
					$native_fb_width = "30";
				}
				$code = '<div style="display: inline-block; overflow: hidden; height: 24px; max-height: 24px; padding-right: 20px; width: '.$native_fb_width.'px !important; vertical-align: top;"><div class="fb-like" data-href="'.$address.'" data-layout="button" data-action="like" data-show-faces="false" data-share="false" data-width="292"></div></div>';				
			}
			else {
				if (trim($native_fb_width) != '') {
					$code = '<div style="display: inline-block; overflow: hidden; height: 24px; max-height: 24px; padding-right: 20px; width: '.$native_fb_width.'px !important; vertical-align: top;"><div class="fb-like" data-href="'.$address.'" data-layout="button_count" data-action="like" data-show-faces="false" data-share="false" data-width="292"></div></div>';
				}
				else {
					$code = '<div class="fb-like" data-href="'.$address.'" data-layout="button_count" data-action="like" data-show-faces="false" data-share="false" data-width="292"></div>';						
				}
			}
			$output = ESSB_Skinned_Native_Button::generateButton('facebook', $code, "like", $skinned_text, $skinned_width, $this->skinned_social_selected_skin);
			//$output = ESSB_Skinned_Native_Button::generateCircleButton("google", $code);
		}
		else {
		
			if ($native_counters == "false") {
				if (trim($native_fb_width) == "") { $native_fb_width = "30"; }
				$output = '<div style="display: inline-block; overflow: hidden; height: 24px; max-height: 24px; padding-right: 20px; width: '.$native_fb_width.'px !important; vertical-align: top; margin-right: 5px;" class="essb_fb_like"><div class="fb-like" data-href="'.$address.'" data-layout="button" data-action="like" data-show-faces="false" data-share="false" data-width="292"></div></div>';				
			}
			else {
				$fix_native_width = "";
				if (trim($native_fb_width) != '') {
					$fix_native_width = 'width:'.$native_fb_width.'px !important;';
				}
				$output = '<div style="display: inline-block; overflow: hidden; height: 24px; max-height: 24px; margin-right: 5px; vertical-align: top;'.$fix_native_width.'" class="essb_fb_like"><div class="fb-like" data-href="'.$address.'" data-layout="button_count" data-action="like" data-show-faces="false" data-share="false" data-width="292"></div></div>';
			}
		}
		return $output;
	}
	
	function print_twitter_follow_button($user, $native_counters, $native_lang, $skinned_text = '', $skinned_width = '') {
		//$output = '<a href="https://twitter.com/'.$user.'" class="twitter-follow-button" data-show-count="false" data-show-screen-name="false" data-size="small">Follow @'.$user.'</a>';
		// data counter false = 65
		if ($this->skinned_social) {
			if ($native_counters == "false") {
				$code = '<iframe allowtransparency="true" frameborder="0" scrolling="no" src="//platform.twitter.com/widgets/follow_button.html?screen_name='.$user.'&show_count=false&show_screen_name=false&lang='.$native_lang.'" style="width:65px; height:20px;"></iframe>';
			}
			else {
				$code = '<iframe allowtransparency="true" frameborder="0" scrolling="no" src="//platform.twitter.com/widgets/follow_button.html?screen_name='.$user.'&show_count=true&show_screen_name=false&lang='.$native_lang.'" style="width:155px; height:20px;"></iframe>';
			}
			$output = ESSB_Skinned_Native_Button::generateButton('twitter', $code, "follow", $skinned_text, $skinned_width, $this->skinned_social_selected_skin);
			//$output = ESSB_Skinned_Native_Button::generateCircleButton("google", $code);
		}
		else {
			if ($native_counters == "false") {
				$output = '<div style="display: inline-block; overflow: hidden; height: 24px; max-height: 24px; margin-right: 5px; vertical-align: top;" class="essb_twitter_follow"><iframe allowtransparency="true" frameborder="0" scrolling="no" src="//platform.twitter.com/widgets/follow_button.html?screen_name='.$user.'&show_count=false&show_screen_name=false&lang='.$native_lang.'" style="width:65px; height:20px;"></iframe></div>';
			}
			else {
				$output = '<div style="display: inline-block; overflow: hidden; height: 24px; max-height: 24px; margin-right: 5px; vertical-align: top;" class="essb_twitter_follow"><iframe allowtransparency="true" frameborder="0" scrolling="no" src="//platform.twitter.com/widgets/follow_button.html?screen_name='.$user.'&show_count=true&show_screen_name=false&lang='.$native_lang.'" style="width:155px; height:20px;"></iframe></div>';				
			}
		}
	
		return $output;
	}
	
	function print_twitter_tweet_button($user, $native_counters, $native_lang, $custom_message = '', $skinned_text = '', $skinned_width = '') {
		//$output = '<a href="https://twitter.com/'.$user.'" class="twitter-follow-button" data-show-count="false" data-show-screen-name="false" data-size="small">Follow @'.$user.'</a>';
		// data counter false = 65
		if ($this->skinned_social) {
			if ($native_counters == "false") {
				$code = '<a href="https://twitter.com/share" class="twitter-share-button" data-via="'.$user.'" data-lang="'.$native_lang.'" data-count="none" '.($custom_message != '' ? 'data-text="'.$custom_message.'"' : '').'>Tweeter</a>';
			}
			else {
				$code = '<a href="https://twitter.com/share" class="twitter-share-button" data-via="'.$user.'" data-lang="'.$native_lang.'" '.($custom_message != '' ? 'data-text="'.$custom_message.'"' : '').'>Tweeter</a>';
			}
			$output = ESSB_Skinned_Native_Button::generateButton('twitter', $code, "follow", $skinned_text, $skinned_width, $this->skinned_social_selected_skin);
			//$output = ESSB_Skinned_Native_Button::generateCircleButton("google", $code);
		}
		else {
			if ($native_counters == "false") {
				$output = '<div style="display: inline-block; overflow: hidden; height: 24px; max-height: 24px; margin-right: 5px; vertical-align: top;" class="essb_twitter_follow"><a href="https://twitter.com/share" class="twitter-share-button" data-via="'.$user.'" data-lang="'.$native_lang.'" data-count="none" '.($custom_message != '' ? 'data-text="'.$custom_message.'"' : '').'>Tweeter</a></div>';
			}
			else {
				$output = '<div style="display: inline-block; overflow: hidden; height: 24px; max-height: 24px; margin-right: 5px; vertical-align: top;" class="essb_twitter_follow"><a href="https://twitter.com/share" class="twitter-share-button" data-via="'.$user.'" data-lang="'.$native_lang.'" '.($custom_message != '' ? 'data-text="'.$custom_message.'"' : '').'>Tweeter</a></div>';
			}
		}
		
		if (!$this->twitter_api_added) {
			$this->twitter_api_added = true;
			$output .= "<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>";
		}
			
		return $output;
	}
	
	function print_fb_sharebutton($address) {
		$output = '<div class="fb-share-button" data-href="'.$address.'" data-type="button"></div>';
		
		return $output;
	}
	
	public function print_popup_mailform($title, $text, $salt_parent, $stats_ative) {
		$salt = mt_rand ();
		$mailform_id = 'essb_mail_from_'.$salt;
		$stats_callback = "";
		
		if ($stats_ative == 'true') {
			$stats_callback = "essb_handle_stats('mail');";
		}
		
		$options = get_option ( self::$plugin_settings_name );
		$translate_mail_title = isset($options['translate_mail_title']) ? $options['translate_mail_title'] : '';
		$translate_mail_email = isset($options['translate_mail_email']) ? $options['translate_mail_email'] : '';
		$translate_mail_recipient = isset($options['translate_mail_recipient']) ? $options['translate_mail_recipient'] : '';
		$translate_mail_subject = isset($options['translate_mail_subject']) ? $options['translate_mail_subject'] : '';
		$translate_mail_message = isset($options['translate_mail_message']) ? $options['translate_mail_message'] : '';
		$translate_mail_cancel = isset($options['translate_mail_cancel']) ? $options['translate_mail_cancel'] : '';
		$translate_mail_send = isset($options['translate_mail_send']) ? $options['translate_mail_send'] : '';

		$mail_captcha = isset($options['mail_captcha']) ? $options['mail_captcha'] : '';
		$mail_captcha_answer = isset($options['mail_captcha_answer']) ? $options['mail_captcha_answer'] : '';

		$captcha_html = '';
		if ($mail_captcha != '' && $mail_captcha_answer != '') {
			$captcha_html = '\'<div class="vex-custom-field-wrapper"><strong>'.$mail_captcha.'</strong></div><input name="captchacode" type="text" placeholder="Captcha Code" />\'+';
		}
		
		$text =nl2br($text);
		$text = str_replace("\r", "", $text);
		$text = str_replace("\n", "", $text);
		$siteurl = ESSB_PLUGIN_URL. '/';
		//$open = 'javascript:PopupContact_OpenForm("PopupContact_BoxContainer","PopupContact_BoxContainerBody","PopupContact_BoxContainerFooter");';
		$site_title = get_the_title();
		$url = get_site_url();
		$permalink = get_permalink();
$html = '<script type="text/javascript">jQuery(function() {
        vex.defaultOptions.className = \'vex-theme-os\';
	    jQuery(\'#essb-mailform'.$salt_parent.'\').click(function(){
	    '.$stats_callback.'
        vex.dialog.open({
            message: \''.($translate_mail_title != '' ? $translate_mail_title : 'Share this with a friend').'\',
            input: \'\' +
                \'<div class="vex-custom-field-wrapper"><strong>'. ($translate_mail_email != '' ? $translate_mail_email : 'Your Email').'</strong></div>\'+
                \'<input name="emailfrom" type="text" placeholder="Your Email" required />\' +
                \'<div class="vex-custom-field-wrapper"><strong>'.($translate_mail_recipient != '' ? $translate_mail_recipient : ''). '</strong></div>\'+
                \'<input name="emailto" type="text" placeholder="Recipient Email" required />\' +
                \'<div class="vex-custom-field-wrapper" style="border-bottom: 1px solid #aaa !important; margin-top: 10px;"><h3></h3></div>\'+
                \'<div class="vex-custom-field-wrapper" style="margin-top: 10px;"><strong>'.($translate_mail_subject != '' ? $translate_mail_subject : 'Subject').'</strong></div>\'+
                \'<input name="emailsubject" type="text" placeholder="Subject" required value="'.$title.'" />\' +
                \'<div class="vex-custom-field-wrapper" style="margin-top: 10px;"><strong>'.($translate_mail_message != '' ? $translate_mail_message : 'Message').'</strong></div>\'+
                \'<textarea name="emailmessage" placeholder="Message" required" rows="6">'.$text.' </textarea>\' +
                '.$captcha_html. '
            \'\',
            buttons: [
                jQuery.extend({}, vex.dialog.buttons.YES, { text: \''.($translate_mail_send != '' ? $translate_mail_send : 'Send').'\' }),
                jQuery.extend({}, vex.dialog.buttons.NO, { text: \''.($translate_mail_cancel != '' ? $translate_mail_cancel : 'Cancel').'\' })
            ],
            callback: function (data) {
				if (data.emailfrom && typeof(data.emailfrom) != "undefined") {
					var c = typeof(data.captchacode) != "undefined" ? data.captchacode : "";
					essb_sendmail_ajax'.$salt.'(data.emailfrom, data.emailto, data.emailsubject, data.emailmessage, c);
				}
	}
        });
    });
});
		function essb_sendmail_ajax'.$salt.'(emailfrom, emailto, emailsub, emailmessage, c) {
			//alert(emailfrom + "|" + emailto);
			
			var get_address = "' . ESSB_PLUGIN_URL . '/public/essb-mail.php?from="+emailfrom+"&to="+emailto+"&sub="+emailsub+"&message="+emailmessage+"&t='.urlencode ($site_title).'&u='.urlencode ($url).'&p='.urlencode ($permalink).'&c="+c;
			//prompt(get_address, get_address);
			//alert(get_address);
			jQuery.getJSON(get_address)
					.done(function(data){
						alert(data.message);
					});
		}
		
		</script>';
		
		return $html;
	}
	
	function print_share_links($content) {
		global $post;
			
		$options = get_option(  EasySocialShareButtons::$plugin_settings_name );
	
		if( isset($options['display_in_types']) ) {
	
			// write buttons only if administrator checked this type
			$is_all_lists = in_array('all_lists', $options['display_in_types']);
			$singular_options = $options['display_in_types'];
			
			$is_set_list = count($singular_options) > 0 ?  true: false;	
			
			unset($singular_options['all_lists']);
			
			$is_lists_authorized = (is_archive() || is_front_page() || is_search() || is_tag() || is_post_type_archive() || is_home()) && $is_all_lists ? true : false;
			$is_singular = is_singular($singular_options);

			if ($is_singular && !$is_set_list) { $is_singular = false; }
			
			$excule_from = isset($options['display_exclude_from']) ? $options['display_exclude_from'] : '';
			
			// @since 1.3.8.2
			if ($excule_from != '') {
				$excule_from = explode(',', $excule_from);
				
				$excule_from = array_map('trim', $excule_from);
				
				if (in_array($post->ID, $excule_from, false)) {
					$is_singular = false;
					$is_lists_authorized = false;
				}
			}
			
			if ( $is_singular || $is_lists_authorized ) {
	
				$post_counters =  get_post_meta($post->ID,'essb_counter',true);
					
				if ($post_counters != '') {
					$options ['show_counter'] = $post_counters;
				}
				
				$need_counters = $options['show_counter'] ? 1 : 0;	
							
				$this->print_links_position = "top";
				$links = $this->generate_share_snippet(array(), $need_counters);
	
				$display_where = isset($options['display_where']) ? $options['display_where'] : '';
				$post_position =  get_post_meta($post->ID,'essb_position',true);
				if ($post_position != '' ) { $display_where = $post_position; }
				
				// @since 1.3.8.2 - mobile display render in alternative way
				if ($this->isMobile()){
					$display_position_mobile = isset($options['display_position_mobile']) ? $options['display_position_mobile'] : '';
					
					if ($display_position_mobile != '') { $display_where = $display_position_mobile; }
				}
				
				// @since 1.3.1 sidebar is moved to bottom render to avoid pop in excerpts
				$this->print_links_position = "top";
				
				if ('sidebar' == $display_where) {
					$content = '<div class="essb_sidebar_start_scroll"></div>'.$content;
				}
				
				if( 'top' == $display_where || 'both' == $display_where || 'float' == $display_where || 'likeshare' == $display_where || 'sharelike' == $display_where)
					$content = $links.$content;
				if( 'bottom' == $display_where || 'both' == $display_where || 'popup' == $display_where || 'likeshare' == $display_where || 'sharelike' == $display_where || 'sidebar' == $display_where) {
					$this->print_links_position = "bottom";
					if ('both' == $display_where || 'likeshare' == $display_where || 'sharelike' == $display_where) {
						$links = $this->generate_share_snippet(array(), $need_counters);
					}
					$content = $content.$links;
				}
	
				// @since 1.3.9.1
				if ('sidebar' == $display_where) {
					$content .= '<div class="essb_sidebar_break_scroll"></div>';
				}
				
				// @since 1.3.5
				$another_display_sidebar = isset($options['another_display_sidebar']) ? $options['another_display_sidebar'] : 'false';
				$another_display_popup = isset($options['another_display_popup']) ? $options['another_display_popup'] : 'false';
				
				$post_another_display_sidebar =  get_post_meta($post->ID,'essb_another_display_sidebar',true);
				$post_another_display_popup =  get_post_meta($post->ID,'essb_another_display_popup',true);
				
				$another_display_deactivate_mobile = isset($options['another_display_deactivate_mobile']) ? $options['another_display_deactivate_mobile'] : 'false';
				
				if ($post_another_display_sidebar != '') {
					if ($post_another_display_sidebar == "yes") { $another_display_sidebar = "true"; }
					else { $another_display_sidebar = "false"; }
				}
				if ($post_another_display_popup != '') {
					if ($post_another_display_popup == "yes") {
						$another_display_popup = "true";
					}
					else { $another_display_popup = "false";
					}
				}
				
				if ($this->isMobile() && $another_display_deactivate_mobile == "true") {
					$another_display_popup = "false";
					$another_display_sidebar = "false";
				}
				
				if ($another_display_sidebar == "true") { $content = $content.$this->render_sidebar_code(); }
				if ($another_display_popup == "true") {
					$content = $content.$this->render_popup_code();
				}
				
				return $content;
			}
			else
				return $content;
		}
		else
			return $content;
	
	} // end function
	
	function handle_essb_total_shortcode ($atts) {
		global $post;
		
		$atts = shortcode_atts(array(
				'message' => '',
				'align' => '',
				'url' => '',
				'share_text' => '',
				'fullnumber' => 'no',
				'networks' => ''
		), $atts);
		
		$align = isset($atts['align']) ? $atts['align'] : '';
		$message = isset($atts['message']) ? $atts['message'] : '';
		$url = isset($atts['url']) ? $atts['url'] : '';
		$share_text = isset($atts['share_text']) ? $atts['share_text'] : '';
		$fullnumber = isset($atts['fullnumber']) ? $atts['fullnumber'] : 'no';
		$networks = isset($atts['networks']) ? $atts['networks'] : 'no';
		
		$data_full_number = "false";
		if ($fullnumber == 'yes') { $data_full_number = "true"; } 
		
		// init global options
		$options = get_option(  EasySocialShareButtons::$plugin_settings_name );

		$essb_networks = $options['networks'];
		$buttons = "";
		foreach($essb_networks as $k => $v) {
			if ($buttons != '') {
				$buttons .= ",";
			}
			$buttons .= $k;
				
		}
		
		if ($networks != '') { $buttons = $networks; }
		
		$facebook_totalcount = isset($options['facebooktotal']) ? $options['facebooktotal'] : 'false';
		$force_counter_adminajax = isset($options['force_counters_admin']) ? $options['force_counters_admin'] : 'false';
		
		
		
		$css_class_align = "";

		$data_url = $post ? get_permalink() : $this->get_current_url( 'raw' );
		if ($url != '' ) { $data_url = $url; }
		
		
		if ($align == "right" || $align == "center") {
			$css_class_align = $align;
		}
		
		
		if (!$this->shortcode_like_css_included) {
			$this->shortcode_like_css_included = true;
			wp_enqueue_style ( 'essb-social-like', ESSB_PLUGIN_URL . '/assets/css/essb-social-like-buttons.css', false, $this->version, 'all' );
		
		}
		
		//if( $this->$counter_included ) {
			if ($this->js_minifier) {
				wp_enqueue_script ( 'essb-counter-script', ESSB_PLUGIN_URL . '/assets/js/easy-social-share-buttons.min.js', array ('jquery' ), $this->version, true );				
			}
			else {
				wp_enqueue_script ( 'essb-counter-script', ESSB_PLUGIN_URL . '/assets/js/easy-social-share-buttons.js', array ('jquery' ), $this->version, true );
			}
		//}
		
		$ajax_count_url = admin_url ('admin-ajax.php');
		
		$output = "";
		$output .= '<div class="essb-total '.$css_class_align.'" data-network-list="'.$buttons.'" data-url="'.$data_url.'" data-fb-total="'.$facebook_totalcount.'" data-counter-url="'.ESSB_PLUGIN_URL.'" data-ajax-url="'.$ajax_count_url.'" data-force-ajax="'.$force_counter_adminajax.'" data-full-number="'.$data_full_number.'">';
		
		if ($message != '') {
			$output .= '<div class="essb-message essb-block">'.$message.'</div>';
		}

		$output .= '<div class="essb-total-value essb-block">0</div>';
		if ($share_text != '') {
			$output .= '<div class="essb-total-text essb-block">'.$share_text.'</div>';
		}
				
		$output .= '</div>';
		
		
		return $output;
		
	}
	
	function handle_essb_like_buttons_shortcode($atts) {
		global $post;
		
		$atts = shortcode_atts(array(
				'facebook' => 'false',
				'facebook_url'	=> '',
				'facebook_width' => '',
				'facebook_skinned_text' => '',
				'facebook_skinned_width' => '',
				'twitter_follow'	=> 'false',
				'twitter_follow_user' => '',
				'twitter_follow_skinned_text' => '',
				'twitter_follow_skinned_width' => '',
				'twitter_tweet' => 'false',
				'twitter_tweet_message' => '',
				'twitter_tweet_skinned_text' => '',
				'twitter_tweet_skinned_width' => '',
				'google' => 'false',
				'google_url'=> '',
				'google_skinned_text' => '',
				'google_skinned_width' => '',
				'vk' => 'false',
				'vk_skinned_text' => '',
				'vk_skinned_width' => '',
				'youtube' => 'false',
				'youtube_chanel' => '',
				'youtube_skinned_text' => '',
				'youtube_skinned_width' => '',
				'pinterest_pin' => 'false',
				'pinterest_pin_skinned_text' => '',
				'pinterest_pin_skinned_width' => '',
				'pinterest_follow' => 'false',
				'pinterest_follow_display' => '',
				'pinterest_follow_url' => '',				
				'pinterest_follow_skinned_text' => '',
				'pinterest_follow_skinned_width' => '',
				'skinned' => 'false',
				'skin' => 'flat',
				'message' => '',
				'align' => '',
				'counters' => 'false'
		), $atts);
		
		$align = isset($atts['align']) ? $atts['align'] : '';
		$facebook = isset($atts['facebook']) ? $atts['facebook'] : '';
		$facebook_url = isset($atts['facebook_url']) ? $atts['facebook_url'] : '';
		$facebook_width = isset($atts['facebook_width']) ? $atts['facebook_width'] : '';
		$facebook_skinned_text = isset($atts['facebook_skinned_text']) ? $atts['facebook_skinned_text'] : '';
		$facebook_skinned_width = isset($atts['facebook_skinned_width']) ? $atts['facebook_skinned_width'] : '';
		
		$twitter_follow = isset($atts['twitter_follow']) ? $atts['twitter_follow'] : '';
		$twitter_follow_user = isset($atts['twitter_follow_user']) ? $atts['twitter_follow_user'] : '';
		$twitter_follow_skinned_text = isset($atts['twitter_follow_skinned_text']) ? $atts['twitter_follow_skinned_text'] : '';
		$twitter_follow_skinned_width = isset($atts['twitter_follow_skinned_width']) ? $atts['twitter_follow_skinned_width'] : '';
		
		$twitter_tweet = isset($atts['twitter_tweet']) ? $atts['twitter_tweet'] : '';
		$twitter_tweet_message = isset($atts['twitter_tweet_message']) ? $atts['twitter_tweet_message'] : '';
		$twitter_tweet_skinned_text = isset($atts['twitter_tweet_skinned_text']) ? $atts['twitter_tweet_skinned_text'] : '';
		$twitter_tweet_skinned_width = isset($atts['twitter_tweet_skinned_width']) ? $atts['twitter_tweet_skinned_width'] : '';
		
		$google = isset($atts['google']) ? $atts['google'] : '';
		$google_url = isset($atts['google_url']) ? $atts['google_url'] : '';
		$google_skinned_text = isset($atts['google_skinned_text']) ? $atts['google_skinned_text'] : '';
		$google_skinned_width = isset($atts['google_skinned_width']) ? $atts['google_skinned_width'] : '';
		
		$vk = isset($atts['vk']) ? $atts['vk'] : '';
		$vk_skinned_text = isset($atts['vk_skinned_text']) ? $atts['vk_skinned_text'] : '';
		$vk_skinned_width = isset($atts['vk_skinned_width']) ? $atts['vk_skinned_width'] : '';
		
		$youtube = isset($atts['youtube']) ? $atts['youtube'] : '';
		$youtube_chanel = isset($atts['youtube_chanel']) ? $atts['youtube_chanel'] : '';
		$youtube_skinned_text = isset($atts['youtube_skinned_text']) ? $atts['youtube_skinned_text'] : '';
		$youtube_skinned_width = isset($atts['youtube_skinned_width']) ? $atts['youtube_skinned_width'] : '';
		
		$pinterest_pin = isset($atts['pinterest_pin']) ? $atts['pinterest_pin'] : '';
		$pinterest_pin_skinned_text = isset($atts['pinterest_pin_skinned_text']) ? $atts['pinterest_pin_skinned_text'] : '';
		$pinterest_pin_skinned_width = isset($atts['pinterest_pin_skinned_width']) ? $atts['pinterest_pin_skinned_width'] : '';
		
		$pinterest_follow = isset($atts['pinterest_follow']) ? $atts['pinterest_follow'] : '';
		$pinterest_follow_display = isset($atts['pinterest_follow_display']) ? $atts['pinterest_follow_display'] : '';
		$pinterest_follow_url = isset($atts['pinterest_follow_url']) ? $atts['pinterest_follow_url'] : '';
		$pinterest_follow_skinned_text = isset($atts['pinterest_follow_skinned_text']) ? $atts['pinterest_follow_skinned_text'] : '';
		$pinterest_follow_skinned_width = isset($atts['pinterest_follow_skinned_width']) ? $atts['pinterest_follow_skinned_width'] : '';
		
		$skinned = isset($atts['skinned']) ? $atts['skinned'] : 'false';
		$skin = isset($atts['skin']) ? $atts['skin'] : '';
		$message = isset($atts['message']) ? $atts['message'] : '';
		$counters = isset($atts['counters']) ? $atts['counters'] : 'false';
		
		// init global options
		$options = get_option(  EasySocialShareButtons::$plugin_settings_name );
		$native_lang = isset($options['native_social_language']) ? $options['native_social_language'] : "en";
		
		$css_class_align = "";
		$css_class_noskin = ($skinned != 'true') ? ' essb-noskin' : '';
		
		$current_state_skinned = $this->skinned_social;
		$current_state_selected_skin = $this->skinned_social_selected_skin;
		
		if ($skinned != 'true') { $this->skinned_social = false; } else {$this->skinned_social = true; $this->skinned_social_selected_skin = $skin; }
		
		if (!$current_state_skinned && $skinned == 'true') {
			ESSB_Skinned_Native_Button::registerCSS();
		}
		
		$text = esc_attr(urlencode($post->post_title));
		$url = $post ? get_permalink() : $this->get_current_url( 'raw' );
		
		
		if ($align == "right" || $align == "center") {
			$css_class_align = $align;
		}
		
		if (!$this->shortcode_like_css_included) {
			$this->shortcode_like_css_included = true;
			wp_enqueue_style ( 'essb-social-like', ESSB_PLUGIN_URL . '/assets/css/essb-social-like-buttons.css', false, $this->version, 'all' );
				
		}
				
		$output = "";
		$output .= '<div class="essb-like '.$css_class_align.'">';
		
		if ($message != '') {
			$output .= '<div class="essb-message">'.$message.'</div>';
		}
		
		if ($facebook == 'true') {
			if ($facebook_url == "") { $facebook_url = $url; }
			
			$output .= '<div class="essb-like-facebook essb-block'.$css_class_noskin.'">';
			$output .= $this->print_fb_likebutton($facebook_url, $counters, $facebook_width, $facebook_skinned_text, $facebook_skinned_width);
			$output .= '</div>'; 
		}
		
		if ($twitter_tweet == 'true') {
			$output .= '<div class="essb-like-twitter essb-block'.$css_class_noskin.'">';
			$output .= $this->print_twitter_tweet_button($twitter_follow_user, $counters, $native_lang, $twitter_tweet_message, $twitter_tweet_skinned_text, $twitter_tweet_skinned_width);
			$output .= '</div>';
		}
		
		if ($twitter_follow == 'true') {
			$output .= '<div class="essb-like-twitter-follow essb-block'.$css_class_noskin.'">';
			$output .= $this->print_twitter_follow_button($twitter_follow_user, $counters, $native_lang, $twitter_follow_skinned_text, $twitter_follow_skinned_width);
			$output .= '</div>';
		}
		
		if ($google == 'true') {
			if ($google_url == "") { $google_url =  $url; }
			$output .= '<div class="essb-like-google essb-block'.$css_class_noskin.'">';
			$output .= $this->print_plusone_button($google_url, $counters, $native_lang, $google_skinned_text, $google_skinned_width);
			$output .= '</div>';
		}

		if ($vk == 'true') {
			$output .= '<div class="essb-like-vk essb-block'.$css_class_noskin.'">';
			$output .= $this->print_vklike_button($url, $counters, $vk_skinned_text, $vk_skinned_width);
			$output .= '</div>';
		}
		if ($youtube == 'true') {
			$output .= '<div class="essb-like-youtube essb-block'.$css_class_noskin.'">';
			$output .= $this->print_youtube_button($youtube_chanel, $counters, $youtube_skinned_text, $youtube_skinned_width);
			$output .= '</div>';				
		}
		if ($pinterest_pin == 'true') {
			$output .= '<div class="essb-like-pin essb-block'.$css_class_noskin.'">';
			$output .= $this->print_pinterest_follow($pinterest_follow_display, $pinterest_follow_url, 'pin', $pinterest_pin_skinned_text, $pinterest_pin_skinned_width);
			$output .= '</div>';				
		}
		if ($pinterest_follow == 'true') {
			$output .= '<div class="essb-like-pin-follow essb-block'.$css_class_noskin.'">';
			$output .= $this->print_pinterest_follow($pinterest_follow_display, $pinterest_follow_url, 'follow', $pinterest_follow_skinned_text, $pinterest_follow_skinned_width);
			$output .= '</div>';
		}
		
		$output .= '</div>';
		
		$this->skinned_social = $current_state_skinned;
		$this->skinned_social_selected_skin = $current_state_selected_skin;
		
		return $output;
	}
	
	
	function handle_essb_shortcode_vk($atts) {
		$atts['native'] = "no";
		
		$total_counter_pos = isset($atts['total_counter_pos']) ? $atts['total_counter_pos'] : '';		
		if ($total_counter_pos == "none") {
			$atts['hide_total'] = "yes";
		}

		$counter_pos = isset($atts['counter_pos']) ? $atts['counter_pos'] : '';		
		if ($counter_pos == "none") {
			$atts['counter_pos'] = "hidden";
		}
		
		return $this->handle_essb_shortcode($atts);
	}
	
	function handle_essb_shortcode($atts) {
		
		// start prepare custom display texts
		$options = get_option ( EasySocialShareButtons::$plugin_settings_name );
		$shortcode_custom_display_texts = array();
		
		if (is_array ( $options )) {
			foreach ( $options ['networks'] as $k => $v ) {
				$key = $k.'_text';

				$value = isset($atts[$key]) ? $atts[$key] : '';
				
				if ($value != '') {
					$shortcode_custom_display_texts[$k] = $value;
				}		
			}
		}
		
		$atts = shortcode_atts(array(
				//'buttons' 	=> 'facebook,twitter,mail,google,stumbleupon,linkedin,pinterest,digg,vk',
			    'buttons' => '',
				'counters'	=> 0,
				'current'	=> 1,
				'text' => '',
				'url' => '',
				'native' => 'yes',
				'sidebar' => 'no',
				'popup'=> 'no',
				'popafter' => '',
				'message' => 'no',
				'description' => '',
				'image' => '',
				'fblike' => '',
				'plusone' => '',
				'show_fblike' => 'no',
				'show_twitter' => 'no',
				'show_plusone' => 'no',
				'show_vk' => 'no',
				'hide_names' => 'no',
				'counters_pos' => '',
				'counter_pos' => '',
				'sidebar_pos' => '',
				'show_youtube' => 'no',
				'show_pinfollow' => 'no',
				'nostats' => 'no',
				'hide_total' => 'no',
				'total_counter_pos' => '',
				'fullwidth' =>  'no',
				'fullwidth_fix' => '',
				'fixedwidth' => 'no',
				'fixedwidth_px' => '',
				'show_managedwp' => 'no',
				'float' => 'no'
		), $atts); 
			

		$exist_mail = strpos($atts['buttons'], 'mail');
		
		//print "shortcode handle";
		// buttons become array ("digg,mail", "digg ,mail", "digg, mail", "digg , mail", are right syntaxes)
		if ( $atts['buttons'] == '') {
			$networks = array();
		}
		else {
			$networks = preg_split('#[\s+,\s+]#', $atts['buttons']);
		}
		$counters = intval($atts['counters']);
		$current_page = intval($atts['current']);
		
		$text = isset($atts['text']) ? $atts['text'] : '';
		$url = isset($atts['url']) ? $atts['url'] : '';
		$native = isset($atts['native']) ? $atts['native'] : 'no';		
		$sidebar = isset($atts['sidebar']) ? $atts['sidebar'] : 'no'; 
		$popup = isset($atts['popup']) ? $atts['popup'] : 'no';
		$message = isset($atts['message']) ? $atts['message'] : 'no';
		$popafter = isset($atts['popafter']) ? $atts['popafter'] : '';
		$description = isset($atts['description']) ? $atts['description'] : '';
		$image = isset($atts['image']) ? $atts['image'] : '';
		$fblike = isset($atts['fblike']) ? $atts['fblike'] : '';
		$plusone = isset($atts['plusone']) ? $atts['plusone'] : '';

		$show_fblike = isset($atts['show_fblike']) ? $atts['show_fblike'] : 'no';
		$show_twitter = isset($atts['show_twitter']) ? $atts['show_twitter'] : 'no';
		$show_plusone = isset($atts['show_plusone']) ? $atts['show_plusone'] : 'no';
		$show_vk = isset($atts['show_vk']) ? $atts['show_vk'] : 'no';
		$hide_names = isset($atts['hide_names']) ? $atts['hide_names'] : 'no';
		$counters_pos = isset($atts['counters_pos']) ? $atts['counters_pos'] : '';
		$counter_pos = isset($atts['counter_pos']) ? $atts['counter_pos'] : '';
		$sidebar_pos = isset($atts['sidebar_pos']) ? $atts['sidebar_pos'] : '';
		
		$show_youtube = isset($atts['show_youtube']) ? $atts['show_youtube'] : 'no';
		$show_pinfollow = isset($atts['show_pinfollow']) ? $atts['show_pinfollow'] : 'no';
		$total_counter_pos = isset($atts['total_counter_pos'])  ? $atts['total_counter_pos'] : '';
		$fullwidth = isset($atts['fullwidth']) ? $atts['fullwidth'] : 'no';
		$fullwidth_fix = isset($atts['fullwidth_fix']) ? $atts['fullwidth_fix'] : '';

		$fixedwidth = isset($atts['fixedwidth']) ? $atts['fixedwidth'] : 'no';
		$fixedwidth_px = isset($atts['fixedwidth_px']) ? $atts['fixedwidth_px'] : '';
		
		$float = isset($atts['float']) ? $atts['float'] : 'no';
		
		$show_managedwp = isset($atts['show_managedwp']) ? $atts['show_managedwp'] : 'no';
		
		if ($show_pinfollow == "yes" && !$this->pinjs_registered) {
			wp_enqueue_script ( 'essb-pinterest-follow', '//assets.pinterest.com/js/pinit.js', array ('jquery' ), $this->version, true );
			$this->pinjs_registered = true;				
		}
		
		if ($exist_mail != false && !$this->mailjs_registered) {
			if ($this->css_minifier) {
				wp_enqueue_style ( 'easy-social-share-buttons-mailform', ESSB_PLUGIN_URL . '/assets/css/essb-mailform.min.css', false, $this->version, 'all' );
			}
			else {
				wp_enqueue_style ( 'easy-social-share-buttons-mailform', ESSB_PLUGIN_URL . '/assets/css/essb-mailform.css', false, $this->version, 'all' );
			}
			wp_enqueue_script ( 'easy-social-share-buttons-mailform', ESSB_PLUGIN_URL . '/assets/js/essb-mailform.js', array ('jquery' ), $this->version, true );
			$this->mailjs_registered = true;
		}
		
		$nostats = isset($atts['nostats']) ? $atts['nostats'] : 'no';
		$hide_total = isset($atts['hide_total']) ? $atts['hide_total'] : 'no';
		
		if( $counters == 1 ) {
			wp_enqueue_script ( 'essb-counter-script', ESSB_PLUGIN_URL . '/assets/js/easy-social-share-buttons.js', array ('jquery' ), $this->version, true );
		}
			
		if ($sidebar == "yes") {
			if ($this->css_minifier) {
				wp_enqueue_style ( 'easy-social-share-buttons-sidebar', ESSB_PLUGIN_URL . '/assets/css/essb-sidebar.min.css', false, $this->version, 'all' );
			}				
			else {
				wp_enqueue_style ( 'easy-social-share-buttons-sidebar', ESSB_PLUGIN_URL . '/assets/css/essb-sidebar.css', false, $this->version, 'all' );
			}
		}
		
		if ($popup == "yes") {
			if ($this->css_minifier) {
				wp_enqueue_style ( 'easy-social-share-buttons-popup', ESSB_PLUGIN_URL . '/assets/css/essb-popup.min.css', false, $this->version, 'all' );
			}
			else {
				wp_enqueue_style ( 'easy-social-share-buttons-popup', ESSB_PLUGIN_URL . '/assets/css/essb-popup.css', false, $this->version, 'all' );
			}
			
			if ($this->js_minifier) {
				wp_enqueue_script ( 'essb-popup-script', ESSB_PLUGIN_URL . '/assets/js/essb-popup.min.js', array ('jquery' ), $this->version, true );
			}
			else {
				wp_enqueue_script ( 'essb-popup-script', ESSB_PLUGIN_URL . '/assets/js/essb-popup.js', array ('jquery' ), $this->version, true );
			}				
		}
		if ($counters_pos == "" && $counter_pos != '') { $counters_pos = $counter_pos; }
		
		//ob_start();
		$output = $this->generate_share_snippet($networks, $counters, $current_page, 1, $text, $url, $native, $sidebar, $message, $popup, $popafter, $image, $description, 
				$fblike, $plusone, $show_fblike, $show_twitter, $show_plusone, $show_vk, $hide_names, $counters_pos, $sidebar_pos, $show_youtube, $show_pinfollow, $nostats, $hide_total,
				$total_counter_pos, $fullwidth, $fullwidth_fix, $show_managedwp, $shortcode_custom_display_texts, $float, $fixedwidth, $fixedwidth_px); //do an echo
		//$output = ob_get_contents();
		//ob_end_clean();
			
		
		
		return $output;
	}
	
	public function handle_essb_metabox() {
		$options = get_option(  EasySocialShareButtons::$plugin_settings_name );
		$pts	 = get_post_types( array('public'=> true, 'show_ui' => true, '_builtin' => true) );
		$cpts	 = get_post_types( array('public'=> true, 'show_ui' => true, '_builtin' => false) );
		
		foreach ( $pts as $pt ) {
			if (in_array($pt, $options['display_in_types'])) {
				add_meta_box('essb_metabox', __('Easy Social Share Buttons', ESSB_TEXT_DOMAIN), 'essb_register_settings_metabox', $pt, 'side', 'high');
				add_meta_box ( "essb_advanced", "Easy Social Share Buttons Advanced", "essb_register_advanced_metabox", $pt, "normal", "high" );
				
			}
		}
		foreach ( $cpts as $cpt ) {
			if (in_array($cpt, $options['display_in_types'])) {
				add_meta_box('essb_metabox', __('Easy Social Share Buttons', ESSB_TEXT_DOMAIN), 'essb_register_settings_metabox', $cpt, 'side', 'high');
				add_meta_box ( "essb_advanced", "Easy Social Share Buttons Advanced", "essb_register_advanced_metabox", $cpt, "normal", "high" );
			}
		}
	
	}
	
	public function handle_essb_save_metabox() {
		global $post, $post_id;
		
		if (! $post) {
			return $post_id;
		}
		
		if (! $post_id)
			$post_id = $post->ID;
			
			// if (! wp_verify_nonce ( @$_POST ['essb_nonce'],
			// 'essb_metabox_handler' ))
			// return $post_id;
			// if (defined ( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE)
			// return $post_id;
			
		// "essb_off"
		if (isset ( $_POST ['essb_off'] )) {
			if ($_POST ['essb_off'] != '')
				update_post_meta ( $post_id, 'essb_off', $_POST ['essb_off'] );
			else
				delete_post_meta ( $post_id, 'essb_off' );
		}
		
		if (isset ( $_POST ['essb_position'] )) {
			if ($_POST ['essb_position'] != '')
				update_post_meta ( $post_id, 'essb_position', $_POST ['essb_position'] );
			else
				delete_post_meta ( $post_id, 'essb_position' );
		}
		
		if (isset ( $_POST ['essb_theme'] )) {
			if ($_POST ['essb_theme'] != '')
				update_post_meta ( $post_id, 'essb_theme', $_POST ['essb_theme'] );
			else
				delete_post_meta ( $post_id, 'essb_theme' );
		}
		
		if (isset ( $_POST ['essb_names'] )) {
			if ($_POST ['essb_names'] != '')
				update_post_meta ( $post_id, 'essb_names', $_POST ['essb_names'] );
			else
				delete_post_meta ( $post_id, 'essb_names' );
		}
		if (isset ( $_POST ['essb_counter'] )) {
			if ($_POST ['essb_counter'] != '')
				update_post_meta ( $post_id, 'essb_counter', $_POST ['essb_counter'] );
			else
				delete_post_meta ( $post_id, 'essb_counter' );
		}
		
		if (isset ( $_POST ['essb_hidefb'] )) {
			if ($_POST ['essb_hidefb'] != '')
				update_post_meta ( $post_id, 'essb_hidefb', $_POST ['essb_hidefb'] );
			else
				delete_post_meta ( $post_id, 'essb_hidefb' );
		}
		
		if (isset ( $_POST ['essb_hideplusone'] )) {
			if ($_POST ['essb_hideplusone'] != '')
				update_post_meta ( $post_id, 'essb_hideplusone', $_POST ['essb_hideplusone'] );
			else
				delete_post_meta ( $post_id, 'essb_hideplusone' );
		}
		
		if (isset ( $_POST ['essb_hidevk'] )) {
			if ($_POST ['essb_hidevk'] != '')
				update_post_meta ( $post_id, 'essb_hidevk', $_POST ['essb_hidevk'] );
			else
				delete_post_meta ( $post_id, 'essb_hidevk' );
		}
		
		if (isset ( $_POST ['essb_hidetwitter'] )) {
			if ($_POST ['essb_hidetwitter'] != '')
				update_post_meta ( $post_id, 'essb_hidetwitter', $_POST ['essb_hidetwitter'] );
			else
				delete_post_meta ( $post_id, 'essb_hidetwitter' );
		}
		
		if (isset ( $_POST ['essb_counter_pos'] )) {
			if ($_POST ['essb_counter_pos'] != '')
				update_post_meta ( $post_id, 'essb_counter_pos', $_POST ['essb_counter_pos'] );
			else
				delete_post_meta ( $post_id, 'essb_counter_pos' );
		}
		
		if (isset ( $_POST ['essb_sidebar_pos'] )) {
			if ($_POST ['essb_sidebar_pos'] != '')
				update_post_meta ( $post_id, 'essb_sidebar_pos', $_POST ['essb_sidebar_pos'] );
			else
				delete_post_meta ( $post_id, 'essb_sidebar_pos' );
		}
		
		if (isset ( $_POST ['essb_post_share_message'] )) {
			if ($_POST ['essb_post_share_message'] != '')
				update_post_meta ( $post_id, 'essb_post_share_message', $_POST ['essb_post_share_message'] );
			else
				delete_post_meta ( $post_id, 'essb_post_share_message' );
		}
		
		if (isset ( $_POST ['essb_post_share_url'] )) {
			if ($_POST ['essb_post_share_url'] != '')
				update_post_meta ( $post_id, 'essb_post_share_url', $_POST ['essb_post_share_url'] );
			else
				delete_post_meta ( $post_id, 'essb_post_share_url' );
		}
		
		if (isset ( $_POST ['essb_post_share_image'] )) {
			if ($_POST ['essb_post_share_image'] != '')
				update_post_meta ( $post_id, 'essb_post_share_image', $_POST ['essb_post_share_image'] );
			else
				delete_post_meta ( $post_id, 'essb_post_share_image' );
		}
		
		if (isset ( $_POST ['essb_post_share_text'] )) {
			if ($_POST ['essb_post_share_text'] != '')
				update_post_meta ( $post_id, 'essb_post_share_text', $_POST ['essb_post_share_text'] );
			else
				delete_post_meta ( $post_id, 'essb_post_share_text' );
		}
		
		if (isset ( $_POST ['essb_post_fb_url'] )) {
			if ($_POST ['essb_post_fb_url'] != '')
				update_post_meta ( $post_id, 'essb_post_fb_url', $_POST ['essb_post_fb_url'] );
			else
				delete_post_meta ( $post_id, 'essb_post_fb_url' );
		}
		
		if (isset ( $_POST ['essb_post_plusone_url'] )) {
			if ($_POST ['essb_post_plusone_url'] != '')
				update_post_meta ( $post_id, 'essb_post_plusone_url', $_POST ['essb_post_plusone_url'] );
			else
				delete_post_meta ( $post_id, 'essb_post_plusone_url' );
		
		}
		if (isset ( $_POST ['essb_hideyoutube'] )) {
			if ($_POST ['essb_hideyoutube'] != '')
				update_post_meta ( $post_id, 'essb_hideyoutube', $_POST ['essb_hideyoutube'] );
			else
				delete_post_meta ( $post_id, 'essb_hideyoutube' );
		}
		if (isset ( $_POST ['essb_hidepinfollow'] )) {
			if ($_POST ['essb_hidepinfollow'] != '')
				update_post_meta ( $post_id, 'essb_hidepinfollow', $_POST ['essb_hidepinfollow'] );
			else
				delete_post_meta ( $post_id, 'essb_hidepinfollow' );
			}
		
			if (isset ( $_POST ['essb_post_og_desc'] )) {
				if ($_POST ['essb_post_og_desc'] != '')
					update_post_meta ( $post_id, 'essb_post_og_desc', $_POST ['essb_post_og_desc'] );
				else
					delete_post_meta ( $post_id, 'essb_post_og_desc' );
			}
				
			if (isset ( $_POST ['essb_post_og_title'] )) {
				if ($_POST ['essb_post_og_title'] != '')
					update_post_meta ( $post_id, 'essb_post_og_title', $_POST ['essb_post_og_title'] );
				else
					delete_post_meta ( $post_id, 'essb_post_og_title' );
			}
				
			if (isset ( $_POST ['essb_post_og_image'] )) {
				if ($_POST ['essb_post_og_image'] != '')
					update_post_meta ( $post_id, 'essb_post_og_image', $_POST ['essb_post_og_image'] );
				else
					delete_post_meta ( $post_id, 'essb_post_og_image' );
			}
			if (isset ( $_POST ['essb_post_og_video'] )) {
				if ($_POST ['essb_post_og_video'] != '')
					update_post_meta ( $post_id, 'essb_post_og_video', $_POST ['essb_post_og_video'] );
				else
					delete_post_meta ( $post_id, 'essb_post_og_video' );
			}
				
			if (isset ( $_POST ['essb_total_counter_pos'] )) {
				if ($_POST ['essb_total_counter_pos'] != '')
					update_post_meta ( $post_id, 'essb_total_counter_pos', $_POST ['essb_total_counter_pos'] );
				else
					delete_post_meta ( $post_id, 'essb_total_counter_pos' );
			}
				
			if (isset ( $_POST ['essb_post_twitter_hashtags'] )) {
				if ($_POST ['essb_post_twitter_hashtags'] != '')
					update_post_meta ( $post_id, 'essb_post_twitter_hashtags', $_POST ['essb_post_twitter_hashtags'] );
				else
					delete_post_meta ( $post_id, 'essb_post_twitter_hashtags' );
			}
				
			if (isset ( $_POST ['essb_post_twitter_username'] )) {
				if ($_POST ['essb_post_twitter_username'] != '')
					update_post_meta ( $post_id, 'essb_post_twitter_username', $_POST ['essb_post_twitter_username'] );
				else
					delete_post_meta ( $post_id, 'essb_post_twitter_username' );
			}
				
			if (isset ( $_POST ['essb_as'] )) {
				$value =  $_POST ['essb_as'];
				$value = array_filter($value);
				if (count($value) != 0)
					update_post_meta ( $post_id, 'essb_as', $value );
				else
					delete_post_meta ( $post_id, 'essb_as' );
			}			
			
			if (isset ( $_POST ['essb_post_twitter_desc'] )) {
				if ($_POST ['essb_post_twitter_desc'] != '')
					update_post_meta ( $post_id, 'essb_post_twitter_desc', $_POST ['essb_post_twitter_desc'] );
				else
					delete_post_meta ( $post_id, 'essb_post_twitter_desc' );
			}
			
			if (isset ( $_POST ['essb_post_twitter_title'] )) {
				if ($_POST ['essb_post_twitter_title'] != '')
					update_post_meta ( $post_id, 'essb_post_twitter_title', $_POST ['essb_post_twitter_title'] );
				else
					delete_post_meta ( $post_id, 'essb_post_twitter_title' );
			}
			
			if (isset ( $_POST ['essb_post_twitter_image'] )) {
				if ($_POST ['essb_post_twitter_image'] != '')
					update_post_meta ( $post_id, 'essb_post_twitter_image', $_POST ['essb_post_twitter_image'] );
				else
					delete_post_meta ( $post_id, 'essb_post_twitter_image' );
			}
						
			if (isset ( $_POST ['essb_another_display_popup'] )) {
				if ($_POST ['essb_another_display_popup'] != '')
					update_post_meta ( $post_id, 'essb_another_display_popup', $_POST ['essb_another_display_popup'] );
				else
					delete_post_meta ( $post_id, 'essb_another_display_popup' );
			}	

			if (isset ( $_POST ['essb_another_display_sidebar'] )) {
				if ($_POST ['essb_another_display_sidebar'] != '')
					update_post_meta ( $post_id, 'essb_another_display_sidebar', $_POST ['essb_another_display_sidebar'] );
				else
					delete_post_meta ( $post_id, 'essb_another_display_sidebar' );
			}			

			if (isset ( $_POST ['essb_activate_customizer'] )) {
				if ($_POST ['essb_activate_customizer'] != '')
					update_post_meta ( $post_id, 'essb_activate_customizer', $_POST ['essb_activate_customizer'] );
				else
					delete_post_meta ( $post_id, 'essb_activate_customizer' );
			}
				
			
			if (isset ( $_POST ['essb_activate_fullwidth'] )) {
				if ($_POST ['essb_activate_fullwidth'] != '')
					update_post_meta ( $post_id, 'essb_activate_fullwidth', $_POST ['essb_activate_fullwidth'] );
				else
					delete_post_meta ( $post_id, 'essb_activate_fullwidth' );
			}			

			if (isset ( $_POST ['essb_activate_nativeskinned'] )) {
				if ($_POST ['essb_activate_nativeskinned'] != '')
					update_post_meta ( $post_id, 'essb_activate_nativeskinned', $_POST ['essb_activate_nativeskinned'] );
				else
					delete_post_meta ( $post_id, 'essb_activate_nativeskinned' );
			}
				
	}
	
	public function init_fb_script() {
	
		$option = get_option ( self::$plugin_settings_name );
		$lang = isset($option['native_social_language']) ? $option['native_social_language'] : "en";
		
		$fb_appid = isset($option['facebookadvancedappid']) ? $option['facebookadvancedappid'] : "";
		
		if ($lang == "") { $lang = "en"; }
		
		$code = $lang ."_" . strtoupper($lang);
		if ($lang == "en") { $code = "en_US"; }
	
	
		echo <<<EOFb
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/$code/all.js#xfbml=1$fb_appid"
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
	
EOFb;
	}
	
	public function init_vk_script() {
		$option = get_option ( self::$plugin_settings_name );
		
		$vkapp_id = isset($option['vklikeappid']) ? $option['vklikeappid'] : '';
		
		echo <<<EOFb
<script type="text/javascript" src="//vk.com/js/api/openapi.js?105"></script>
<script type="text/javascript">
window.onload = function () { 
  VK.init({apiId: $vkapp_id, onlyWidgets: true});
  VK.Widgets.Like("vk_like", {type: "button", height: 20});
}
</script>
EOFb;
	}
	
	public function handle_woocommerce_share() {
		$options = get_option(  EasySocialShareButtons::$plugin_settings_name );
		
		
		
				$need_counters = $options['show_counter'] ? 1 : 0;
					
		
				$links = $this->generate_share_snippet(array(), $need_counters);
		
		echo $links .'<div style="clear: both;\"></div>';		
	}
	
	// @since 1.0.7 - disable network name popup on mobile devices
	public function isMobile() {
		$user_agent = strtolower ( $_SERVER['HTTP_USER_AGENT'] );

		if ( preg_match ( "/phone|iphone|itouch|ipod|symbian|android|htc_|htc-|palmos|blackberry|opera mini|iemobile|windows ce|nokia|fennec|hiptop|kindle|mot |mot-|webos\/|samsung|sonyericsson|^sie-|nintendo/", $user_agent ) ) {
			// these are the most common
			return true;
		} else if ( preg_match ( "/mobile|pda;|avantgo|eudoraweb|minimo|netfront|brew|teleca|lg;|lge |wap;| wap /", $user_agent ) ) {
			// these are less common, and might not be worth checking
			return true;
		}
		
		return false;
	}
	
	public function fix_css_mobile_hidden_network_names() {	
		echo '<style type="text/css">';
		
		echo '.essb_hide_name a:hover .essb_network_name, .essb_hide_name a:focus .essb_network_name { display: none !important; } ';
		echo '.essb_hide_name a:hover .essb_icon, .essb_hide_name a:focus .essb_icon { margin-right: 0px !important; }';
		
		// @since 1.1.2 - update for mobile devices to make float bar stick on left 
		echo '@media only screen and (max-width: 767px) { .essb_fixed { left: 5px !important; } }';
		echo '@media only screen and (max-width: 479px) { .essb_fixed { left: 5px !important; } }';		
		
		echo '</style>';
	
	}
	
	public function fix_css_mobile_hidden_buttons() {
		echo '<style type="text/css">';
		
		echo '@media only screen and (max-width: 767px) { .essb_links { display: none !important; } }';
		echo '@media only screen and (max-width: 479px) { .essb_links { display: none !important; } }';
		
		echo '</style>';
		
	}
	
	public function fix_css_float_from_top() {
		$options = get_option(  EasySocialShareButtons::$plugin_settings_name );
		
		$top_pos = isset($options['float_top']) ? $options['float_top'] : '';
		$bg_color = isset($options['float_bg']) ? $options['float_bg'] : '';
		$float_full = isset($options['float_full']) ? $options['float_full'] : '';
		$button_pos = isset($options['buttons_pos']) ? $options['buttons_pos'] : '';
		
		$custom_float_js = isset($options['float_js']) ? $options['float_js'] : 'false';
		
		if ($top_pos != '' || $bg_color != '' || $float_full != '' || $button_pos != '' || $custom_float_js == 'true') {
			echo '<style type="text/css">';
			
			if ($top_pos != '') {
			echo '.essb_fixed { top: '.$top_pos.'px !important; }';
			}
			if ($bg_color != '') {
			echo '.essb_fixed { background: '.$bg_color.' !important; }';
			}
			
			if ($float_full == 'true') {
				echo '.essb_fixed { left: 0; width: 100%; min-width: 100%; padding-left: 10px; }';
			}					
			
			if ($button_pos != '') {
				if ($button_pos == "right") {
					echo '.essb_links { text-align: right;}';
				}
				if ($button_pos == "center") {
					echo '.essb_links { text-align: center;}';
				}
			}
			
			if ($custom_float_js == 'true') {
				echo '.essb_float_absolute { position: absolute !important; z-index: 999 !important; }';
			}
			echo '</style>';
		}
	}
	
	public function send_email() {
		global $_POST;
	
		$options = get_option(  EasySocialShareButtons::$plugin_settings_name );
		
		$from = $_POST['from'];
		$to = $_POST['to'];
							$message_subject = $options['mail_subject'];
							$message_body = $options['mail_body'];
							$message_subject = preg_replace(array('#%%title%%#', '#%%siteurl%%#', '#%%permalink%%#'), array(get_the_title(), get_site_url(), get_permalink()), $message_subject);
							$message_body = preg_replace(array('#%%title%%#', '#%%siteurl%%#', '#%%permalink%%#'), array(get_the_title(), get_site_url(), get_permalink()), $message_body);
							
							$headers = "MIME-Version: 1.0" . "\r\n";
							$headers .= "Content-type:text/html;charset=utf-8" . "\r\n";
							$headers .= "From: <$from>\n";
							$headers .= "Return-Path: <" . mysql_real_escape_string(trim($from)) . ">\n";
							$message_body = str_replace("\r\n", "<br />", $message_body);
							@wp_mail($to, $message_subject, $message_body, $headers);
							
							sleep(1);
							die( "Message sent!");
								
	}
	
	public function google_shorten($url) {
		$result = wp_remote_post ( 'https://www.googleapis.com/urlshortener/v1/url', array ('body' => json_encode ( array ('longUrl' => esc_url_raw ( $url ) ) ), 'headers' => array ('Content-Type' => 'application/json' ) ) );
	
		// Return the URL if the request got an error.
		if (is_wp_error ( $result ))
			return $url;
	
		$result = json_decode ( $result ['body'] );
		$shortlink = $result->id;
		if ($shortlink)
			return $shortlink;
	
		return $url;
	}
	
	public function bitly_shorten($url, $user, $api) {
		$params = http_build_query(
				array(
						'login' => $user,
						'apiKey' => $api,
						'longUrl' => $url,
						'format' => 'json',
				)
		);
			
		$result = $url;
		
		$rest_url = 'https://api-ssl.bitly.com/v3/shorten?' . $params;
		
		$response = wp_remote_get( $rest_url );
		//print_r($response); 
		// if we get a valid response, save the url as meta data for this post
		if( !is_wp_error( $response ) ) {
		
			$json = json_decode( wp_remote_retrieve_body( $response ) );
		
			if( isset( $json->data->url ) ) {
				
				$result = $json->data->url;
			}
		}
		
		return $result;
	}
	
	function add_class_to_image($class, $id, $align, $size){
		global $post;
			$class  .= ' essb-esh';
		return $class;
	} // add_class_to_image
	
	
	function handle_wp_ecommerce() {
		$options = get_option(  EasySocialShareButtons::$plugin_settings_name );
		
		
		
				$need_counters = $options['show_counter'] ? 1 : 0;
					
		
				$links = $this->generate_share_snippet(array(), $need_counters);
		
		echo $links .'<div style="clear: both;\"></div>';		
	}
	
	/// updates
	public function checkForUpdates() {		
		$query = array(
				'slug' => $this->plugin_slug,
				'version' => $this->version
		);
		
		$url = $this->update_notify_address . '?' . http_build_query($query);		
				
		$result = wp_remote_get($url);		
		if(is_wp_error($result) or (wp_remote_retrieve_response_code($result) != 200)){
			$info = array(
					'update' => false,
				    'error' => 'Remote Get'					
			);
		}
			
		/* Check for incorrect data */
		$info = unserialize(wp_remote_retrieve_body($result));
		if(!is_array($info) or isset($info['error']) or !isset($info['update'])){
			$info = array(
					'update' => false,
					'error' => 'Serializing'
			);
		}
		$info['query'] = $url;
		
		update_option($this->plugin_slug . '_update', $info);
	}
	
	public function essb_updater_setup_schedule() {
			if ( ! wp_next_scheduled( 'essb_update' ) ) {
				wp_schedule_event( time(), 'twelvehours',  'essb_update');
			}
	}
	public function addCronSchedule($schedules){
			
		$schedules['twelvehoursdebug'] = array(
				'interval' => 60, //43200
				'display' => __('Every Twelve Hours Debug')
		);
		
		$schedules['twelvehours'] = array(
				'interval' => 43200, //43200
				'display' => __('Every Twelve Hours')
		);
		return $schedules;
			
	}
	
	public function addAdminNotice() {
		
		$info = get_option ( $this->plugin_slug . '_update' );
		
		$check_user = $this->version;
		$check_update = $info['version'];
		
		$check_user = str_replace('.', '', $check_user);
		$check_update = str_replace('.', '', $check_update);
		
		$exist_update = false;
		if ((float)($check_update) > (float)($check_user)) {
			$exist_update = true;
		}
		$released = isset ( $info ["released"] ) ? $info ["released"] : false;
		
		$released_text = ""; // (!$released) ? " - [Pending approval] " : "";
		
		if ($exist_update) {
		
		?>

<div id="message" class="updated">
	<p>
		<strong><?php _e('Easy Social Share Buttons for WordPress update available!', $this->plugin_slug); ?></strong>
						<?php printf(__('New Version: %s (%s)%s. Info and download at the <a href="%s" target="_blank"><strong>plugin page</strong></a> (<em><a href="%s" target="_blank">view changes in version</a></em>).', $this->plugin_slug), $info['version'], $info['date'], $released_text, $info['link'], $info["changelog"]); ?>
					</p>
</div>

<?php
		}	
	}	
	
	public function temporary_deactivate_content_filter() {
		remove_action ( 'the_content', array ($this, 'print_share_links' ), 10, 1 );
	}
	
	public function reactivate_content_filter() {
		add_action ( 'the_content', array ($this, 'print_share_links' ), 10, 1 );
	}
	
	public function vc_entender_init() {
		if ( function_exists('vc_map')) {
			
			vc_map( array(
					"name" => __("Easy Social Share Buttons", 'essb'),
					"base" => "easy-share",
					"class" => "easy-share",
					"custom_markup" => "easy-share",						
					"controls" => "full",
					"icon" => "icon-wpb-row",
					"category" => __('Social', 'js_composer'),
					"show_settings_on_create" => true,						
					//'admin_enqueue_js' => array(plugins_url('vc_extend_admin.js', __FILE__)),
					//'admin_enqueue_css' => array(plugins_url('vc_extend_admin.css', __FILE__)),
					"params" => array(
							array(
									"type" => "textfield",
									"holder" => "div",
									"class" => "",
									"heading" => __("Text", 'vc_extend'),
									"param_name" => "foo",
									"value" => __("Default params value", 'vc_extend'),
									"description" => __("Description for foo param.", 'vc_extend')
							),
							array(
									"type" => "colorpicker",
									"holder" => "div",
									"class" => "",
									"heading" => __("Text color", 'vc_extend'),
									"param_name" => "color",
									"value" => '#FF0000', //Default Red color
									"description" => __("Choose text color", 'vc_extend')
							),
							array(
									"type" => "textarea_html",
									"holder" => "div",
									"class" => "",
									"heading" => __("Content", 'vc_extend'),
									"param_name" => "content",
									"value" => __("<p>I am test text block. Click edit button to change this text.</p>", 'vc_extend'),
									"description" => __("Enter your content.", 'vc_extend')
							)
					)
			) );
				
		}
	}
	
	public function get_share_counts() {
		header('content-type: application/json');
		
		
		$json = array('url'=>'','count'=>0);
		$url = $_GET['url'];
		$json['url'] = $url;
		$network = $_GET['nw'];
		
		
		
		if ( filter_var($url, FILTER_VALIDATE_URL) || $network == "love" ) {
		
			if ( $network == 'google2' ) {
					
				// http://www.helmutgranda.com/2011/11/01/get-a-url-google-count-via-php/
				$content = $this->parse("https://plusone.google.com/u/0/_/+1/fastbutton?url=".$url."&count=true");
				$dom = new DOMDocument;
				$dom->preserveWhiteSpace = false;
				@$dom->loadHTML($content);
				$domxpath = new DOMXPath($dom);
				$newDom = new DOMDocument;
				$newDom->formatOutput = true;
		
				$filtered = $domxpath->query("//div[@id='aggregateCount']");
		
				if ( isset( $filtered->item(0)->nodeValue ) ) {
					$cars = array("u00c2", "u00a", 'Ã‚Â ', 'Ã‚Â', 'Ã', ',', 'Â', 'Â ');
					$count = str_replace($cars, '', $filtered->item(0)->nodeValue );
					$json['count'] = preg_replace( '#([0-9])#', '$1', $count );
				}
		
			}
		
			elseif ( $network == 'stumble' ) {
					
				$content = $this->parse("http://www.stumbleupon.com/services/1.01/badge.getinfo?url=$url");
		
				$result = json_decode($content);
				if ( isset($result->result->views )) {
					$json['count'] = $result->result->views;
				}
		
			}
		
			elseif ($network == "google") {
				$json['count'] = $this->getGplusShares($url);
		
			}
			elseif ($network == "reddit") {
				$json['count'] = $this->getRedditScore($url);
			
			}
			
			elseif ($network == "love") {
				$json['count'] = $this->getLoveCount($url);
					
			}
			
			elseif ($network == "ok") {
				$json['count'] = get_counter_number_odnoklassniki($url);
			}
			elseif ($network == 'vk') {
				$json['count'] = $this->get_counter_number__vk($url);
		
			}
		}
		echo str_replace('\\/','/',json_encode($json));
		
		die();
	}
	
	function getLoveCount($postID) {
		if (!is_numeric($postID)) { return -1; }
		
		$love_count = get_post_meta($postID, '_essb_love', true);
		
		if( !$love_count ){
			$love_count = 0;
			add_post_meta($postID, '_essb_love', $love_count, true);
		}
		
		return $love_count;
	}
	
	function getGplusShares($url)
	{
		$buttonUrl = sprintf('https://plusone.google.com/u/0/_/+1/fastbutton?url=%s', urlencode($url));
		//$htmlData  = file_get_contents($buttonUrl);
		$htmlData  = $this->parse($buttonUrl);
	
		@preg_match_all('#{c: (.*?),#si', $htmlData, $matches);
		$ret = isset($matches[1][0]) && strlen($matches[1][0]) > 0 ? trim($matches[1][0]) : 0;
		if(0 != $ret) {
			$ret = str_replace('.0', '', $ret);
		}
	
		return ($ret);
	}
	
	function get_counter_number_odnoklassniki( $url ) {
		$CHECK_URL_PREFIX = 'http://www.odnoklassniki.ru/dk?st.cmd=extLike&uid=odklcnt0&ref=';
	
		$check_url = $CHECK_URL_PREFIX . $url;
	
		$data   = parse( $check_url );
		$shares = array();
	
		preg_match( '/^ODKL\.updateCount\(\'odklcnt0\',\'(\d+)\'\);$/i', $data, $shares );
	
		return (int)$shares[ 1 ];
	}
	
	function get_counter_number__vk( $url ) {
		$CHECK_URL_PREFIX = 'http://vk.com/share.php?act=count&url=';
	
		$check_url = $CHECK_URL_PREFIX . $url;
	
		$data   = $this->parse( $check_url );
		$shares = array();
	
		preg_match( '/^VK\.Share\.count\(\d, (\d+)\);$/i', $data, $shares );
	
		return $shares[ 1 ];
	}
	
	function getRedditScore($url) {
		$reddit_url = 'http://www.reddit.com/api/info.json?url='.$url;
		$format = "json";
		$score = $ups = $downs = 0; //initialize
	
		/* action */
		$content = $this->parse( $reddit_url );
		if($content) {
			if($format == 'json') {
				$json = json_decode($content,true);
				foreach($json['data']['children'] as $child) { // we want all children for this example
					$ups+= (int) $child['data']['ups'];
					$downs+= (int) $child['data']['downs'];
					//$score+= (int) $child['data']['score']; //if you just want to grab the score directly
				}
				$score = $ups - $downs;
			}
		}
	
		return $score;
	}
	

	
	function parse( $encUrl ) {
	
		$options = array(
				CURLOPT_RETURNTRANSFER	=> true, 	// return web page
				CURLOPT_HEADER 			=> false, 	// don't return headers
				//CURLOPT_FOLLOWLOCATION	=> true, 	// follow redirects
				CURLOPT_ENCODING	 	=> "", 		// handle all encodings
				CURLOPT_USERAGENT	 	=> 'essb', 	// who am i
				CURLOPT_AUTOREFERER 	=> true, 	// set referer on redirect
				CURLOPT_CONNECTTIMEOUT 	=> 5, 		// timeout on connect
				CURLOPT_TIMEOUT 		=> 10, 		// timeout on response
				CURLOPT_MAXREDIRS 		=> 3, 		// stop after 3 redirects
				CURLOPT_SSL_VERIFYHOST 	=> 0,
				CURLOPT_SSL_VERIFYPEER 	=> false,
		);
		$ch = curl_init();
	
		if (ini_get('open_basedir') == '' && ini_get('safe_mode' == 'Off')) {
			$options[CURLOPT_FOLLOWLOCATION] = true;
		}
				
		$options[CURLOPT_URL] = $encUrl;
		curl_setopt_array($ch, $options);
		// force ip v4 - uncomment this
		curl_setopt( $ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
			
		$content	= curl_exec( $ch );
		$err 		= curl_errno( $ch );
		$errmsg 	= curl_error( $ch );
	
		curl_close( $ch );
	
		if ($errmsg != '' || $err != '') {
			print_r($errmsg);
		}
		return $content;
	}
		
	
	function generate_sidebar_shortcode_from_settings() {	
		$options = get_option(  EasySocialShareButtons::$plugin_settings_name );
	
		$essb_networks = $options['networks'];
		$buttons = "";
		foreach($essb_networks as $k => $v) {
			if( $v[0] == 1 ) {
				if ($buttons != '') {
					$buttons .= ",";
				}
				$buttons .= $k;
			}
			
		}
		$hidden_name_class = (isset($options['hide_social_name']) && $options['hide_social_name']==1) ? ' hide_names="yes" ' : '';
		$hidden_name_class = 'hide_names="yes"';
		$need_counters = $options['show_counter'] ? 0 : 0;
		$sidebar_pos = isset ( $options ['sidebar_pos'] ) ? $options ['sidebar_pos'] : 'left';
		
		//$need_counters = 0;
		$links = ('[easy-share buttons="'.$buttons.'" counters='.$need_counters.' native="no" '.$hidden_name_class.' sidebar="yes" sidebar_pos="'.$sidebar_pos.'"]');
	
		return $links;
	}
	
	function render_sidebar_code() {
		$shortcode = $this->generate_sidebar_shortcode_from_settings();

		return do_shortcode($shortcode);
	}
	
	function generate_popup_shortcode_from_settings() {		
		$options = get_option(  EasySocialShareButtons::$plugin_settings_name );
	
		$essb_networks = $options['networks'];
		$buttons = "";
		foreach($essb_networks as $k => $v) {
			if( $v[0] == 1 ) {
				if ($buttons != '') {
					$buttons .= ",";
				}
				$buttons .= $k;
			}
			
		}
		$hidden_name_class = (isset($options['hide_social_name']) && $options['hide_social_name']==1) ? ' hide_names="yes" ' : '';

		$need_counters = $options['show_counter'] ? 0 : 0;
		$popafter = isset ( $options ['popup_window_popafter'] ) ? $options ['popup_window_popafter'] : '';
		
		if ($popafter != '') {
			$popafter = ' popafter="'.$popafter.'"';
		}
	
		//$need_counters = 0;
		$links = ('[easy-share buttons="'.$buttons.'" counters='.$need_counters.' '.$hidden_name_class.' popup="yes" '.$popafter.']');
	
		return $links;
	}
	
	function render_popup_code() {
		$shortcode = $this->generate_popup_shortcode_from_settings();
		//print $shortcode;
		return do_shortcode($shortcode);
	}
	
	function customizer_compile_css() {
		global $post;
		$options = get_option(  EasySocialShareButtons::$plugin_settings_name );
		
		$is_active = isset($options['customizer_is_active']) ? $options['customizer_is_active'] : 'false';		

		if (isset($post)) {
			$post_activate_customizer =  get_post_meta($post->ID,'essb_activate_customizer',true);
			
			if ($post_activate_customizer != '') {
				if ($post_activate_customizer == "yes") { $is_active = "true"; }
				else {$is_active = "false"; }
			}
		}
		$global_bgcolor = isset($options['customizer_bgcolor']) ? $options['customizer_bgcolor'] : '';
		$global_textcolor = isset($options['customizer_textcolor']) ? $options['customizer_textcolor'] : '';
		$global_hovercolor = isset($options['customizer_hovercolor']) ? $options['customizer_hovercolor'] : '';
		$global_hovertextcolor = isset($options['customizer_hovertextcolor']) ? $options['customizer_hovertextcolor'] : '';
		
		$global_remove_bg_effects = isset($options['customizer_remove_bg_hover_effects']) ? $options['customizer_remove_bg_hover_effects'] : '';
		$css = "";
		
		if ($global_remove_bg_effects == "true") {
			$css .= ' .essb_links a:hover, .essb_links a:focus { background: none !important; }';
		}
		
		$essb_networks = $options['networks'];
				
		if ($global_bgcolor != '' || $global_textcolor != '' || $global_hovercolor != '' || $global_hovertextcolor != '') {
			foreach ( $essb_networks as $k => $v ) {
				if ($v [0] == 1) {
					if ($global_bgcolor != '' || $global_textcolor != '') {
						$css .= '.essb_links .essb_link_'.$k.' a { ';
						if ($global_bgcolor != '') {
							$css .= 'background-color:'.$global_bgcolor.'!important;';
						}
						if ($global_textcolor != '') {
							$css .= 'color:'.$global_bgcolor.'!important;';
						}
						$css .= '}';
					}
					if ($global_hovercolor != '' || $global_hovertextcolor != '') {
						$css .= '.essb_links .essb_link_'.$k.' a:hover, .essb_links .essb_link_'.$k.' a:focus { ';
						if ($global_hovercolor != '') {
							$css .= 'background-color:'.$global_hovercolor.'!important;';
						}
						if ($global_hovertextcolor != '') {
							$css .= 'color:'.$global_hovertextcolor.'!important;';
						}
						$css .= '}';
					}
				}
			
			}
		}
		
		// single network color customization
		foreach ( $essb_networks as $k => $v ) {
			if ($v [0] == 1) {
				$network_bgcolor = isset ( $options ['customizer_' . $k . '_bgcolor'] ) ? $options ['customizer_' . $k . '_bgcolor'] : '';
				$network_textcolor = isset ( $options ['customizer_' . $k . '_textcolor'] ) ? $options ['customizer_' . $k . '_textcolor'] : '';
				$network_hovercolor = isset ( $options ['customizer_' . $k . '_hovercolor'] ) ? $options ['customizer_' . $k . '_hovercolor'] : '';
				$network_hovertextcolor = isset ( $options ['customizer_' . $k . '_hovertextcolor'] ) ? $options ['customizer_' . $k . '_hovertextcolor'] : '';

				$network_icon = isset ( $options ['customizer_' . $k . '_icon'] ) ? $options ['customizer_' . $k . '_icon'] : '';
				$network_hovericon = isset ( $options ['customizer_' . $k . '_hovericon'] ) ? $options ['customizer_' . $k . '_hovericon'] : '';
				$network_iconbgsize = isset ( $options ['customizer_' . $k . '_iconbgsize'] ) ? $options ['customizer_' . $k . '_iconbgsize'] : '';
				$network_hovericonbgsize = isset ( $options ['customizer_' . $k . '_hovericonbgsize'] ) ? $options ['customizer_' . $k . '_hovericonbgsize'] : '';
				
				if ($network_bgcolor != '' || $network_textcolor != '') {
					$css .= '.essb_links .essb_link_' . $k . ' a { ';
					if ($network_bgcolor != '') {
						$css .= 'background-color:' . $network_bgcolor . '!important;';
					}
					if ($network_textcolor != '') {
						$css .= 'color:' . $network_textcolor . '!important;';
					}
					$css .= '}';
				}
				if ($network_hovercolor != '' || $network_hovertextcolor != '') {
					$css .= '.essb_links .essb_link_' . $k . ' a:hover, .essb_links .essb_link_' . $k . ' a:focus { ';
					if ($network_hovercolor != '') {
						$css .= 'background-color:' . $network_hovercolor . '!important;';
					}
					if ($network_hovertextcolor != '') {
						$css .= 'color:' . $network_hovertextcolor . '!important;';
					}
					$css .= '}';
				}
				
				if ($network_icon != '') {
					$css .= '.essb_links .essb_link_' . $k . ' .essb_icon { background: url("'.$network_icon.'") !important; }';
					
					if ($network_iconbgsize != '') {
						$css .= '.essb_links .essb_link_' . $k . ' .essb_icon { background-size: '.$network_iconbgsize.'!important; }';
					}
				}
				if ($network_hovericon != '') {
					$css .= '.essb_links .essb_link_' . $k . ' a:hover .essb_icon { background: url("'.$network_hovericon.'") !important; }';

					if ($network_hovericonbgsize != '') {
						$css .= '.essb_links .essb_link_' . $k . ' a:hover .essb_icon { background-size: '.$network_hovericonbgsize.'!important; }';
					}
				}
			}
		
		}

		if ($is_active != 'true') {
			$css = "";
		}
		
		$global_user_defined_css = isset ( $options ['customizer_css'] ) ? $options ['customizer_css'] : '';
		$global_user_defined_css = stripslashes ( $global_user_defined_css );
		
		if ($global_user_defined_css != '') { $css .= $global_user_defined_css; }
		
		if ($this->skinned_social) {
			$css .= ESSB_Skinned_Native_Button::generateStyleCustomizerCSS($options);
		}
		
		if ($css != '') {			
			echo '<style type="text/css">';

			echo $css;
			
			echo '</style>';
		}
	}
}

class EasySocialShareButtons_AdminMenu {
	function EasySocialShareButtons_AdminMenu() {
		// @since 1.2.0
		add_action ( 'admin_bar_menu', array ($this, "attach_admin_barmenu" ), 89 );
	}
	
	public function attach_admin_barmenu() {
		$this->add_root_menu ( "Easy Social Share Buttons", "essb", get_admin_url () . 'index.php?page=essb_settings&tab=general' );
		$this->add_sub_menu ( "Main Settings", get_admin_url () . 'index.php?page=essb_settings&tab=general', "essb", "essb_p1" );
		$this->add_sub_menu ( "Display Settings", get_admin_url () . 'index.php?page=essb_settings&tab=display', "essb", "essb_p2" );
		$this->add_sub_menu ( "Shortcode Generator", get_admin_url () . 'index.php?page=essb_settings&tab=shortcode', "essb", "essb_p3" );
	}
	
	function add_root_menu($name, $id, $href = FALSE) {
		global $wp_admin_bar;
		if (! is_super_admin () || ! is_admin_bar_showing ())
			return;
		
		$wp_admin_bar->add_menu ( array ('id' => $id, 'meta' => array (), 'title' => $name, 'href' => $href ) );
	}
	
	function add_sub_menu($name, $link, $root_menu, $id, $meta = FALSE) {
		global $wp_admin_bar;
		if (! is_super_admin () || ! is_admin_bar_showing ())
			return;
		
		$wp_admin_bar->add_menu ( array ('parent' => $root_menu, 'id' => $id, 'title' => $name, 'href' => $link, 'meta' => $meta ) );
	}

}

?>