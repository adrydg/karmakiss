<?php

$msg = "";

$cmd = isset ( $_POST ["cmd"] ) ? $_POST ["cmd"] : "";

if ($cmd == "update") {
	// print_r($_POST);
	$options = $_POST ["general_options"];
	$nn = $_POST ["general_options_nn"];
	$mm = $_POST ["general_options_mm"];
	
	if (! isset ( $options ['force_hide_social_name'] )) {
		$options ['force_hide_social_name'] = 'false;';
	}
	if (! isset ( $options ['woocommece_share'] )) {
		$options ['woocommece_share'] = 'false';
	}
	
	if (! isset ( $options ['native_social_counters_fb'] )) {
		$options ['native_social_counters_fb'] = 'false';
	}
	
	if (! isset ( $options ['native_social_counters_g'] )) {
		$options ['native_social_counters_g'] = 'false';
	}
	
	if (! isset ( $options ['native_social_counters_t'] )) {
		$options ['native_social_counters_t'] = 'false';
	}
	
	if (! isset ( $options ['native_social_counters_youtube'] )) {
		$options ['native_social_counters_youtube'] = 'false';
	}
	
	if (! isset ( $options ['message_share_buttons'] )) {
		$options ['message_share_buttons'] = "";
	}
	if (! isset ( $options ['message_like_buttons'] )) {
		$options ['message_like_buttons'] = "";
	}
	
	if (! isset ( $options ['popup_window_title'] )) {
		$options ['popup_window_title'] = "";
	}
	
	if (! isset ( $options ['popup_window_popafter'] )) {
		$options ['popup_window_popafter'] = "";
	}
	
	if (! isset ( $options ['popup_window_close_after'] )) {
		$options ['popup_window_close_after'] = "";
	}
	
	if (! isset ( $options ['native_social_language'] )) {
		$options ['native_social_language'] = "";
	}
	
	if (! isset ( $options ['float_top'] )) {
		$options ['float_top'] = "";
	}
	if (! isset ( $options ['float_bg'] )) {
		$options ['float_bg'] = "";
	}
	if (! isset ( $options ['sidebar_pos'] )) {
		$options ['sidebar_pos'] = "";
	}
	if (! isset ( $options ['counter_pos'] )) {
		$options ['counter_pos'] = "";
	}
	
	if (! isset ( $options ['force_hide_total_count'] )) {
		$options ['force_hide_total_count'] = 'false';
	}
	
	if (! isset ( $options ['display_excerpt'] )) {
		$options ['display_excerpt'] = 'false';
	}
	if (! isset ( $options ['buddypress_activity'] )) {
		$options ['buddypress_activity'] = 'false';
	}
	if (! isset ( $options ['buddypress_group'] )) {
		$options ['buddypress_group'] = 'false';
	}
	if (! isset ( $options ['bbpress_topic'] )) {
		$options ['bbpress_topic'] = 'false';
	}
	if (! isset ( $options ['bbpress_forum'] )) {
		$options ['bbpress_forum'] = 'false';
	}
	
	if (! isset ( $options ['sidebar_sticky'] )) {
		$options ['sidebar_sticky'] = 'false';
	}
	
	if (! isset ( $options ['float_full'] )) {
		$options ['float_full'] = 'false';
	}

	if (! isset ( $options ['float_js'] )) {
		$options ['float_js'] = 'false';
	}
	
	if (! isset ( $options ['total_counter_pos'] )) {
		$options ['total_counter_pos'] = '';
	}
	
	if (! isset ( $options ['force_counters_admin'] )) {
		$options ['force_counters_admin'] = 'false';
	}
	
	if (!isset($options['force_hide_buttons_on_mobile'])) {
		$options['force_hide_buttons_on_mobile'] = 'false';
	}
	
	if (!isset($options['another_display_popup'])) {
		$options['another_display_popup'] = 'false';
	}
	if (!isset($options['another_display_sidebar'])) {
		$options['another_display_sidebar'] = 'false';
	}
	if (!isset($options['translate_love_thanks'])) {
		$options['translate_love_thanks'] = "";
	}
	if (!isset($options['translate_love_loved'])) {
		$options['translate_love_loved'] = "";
	}
	
	if (!isset($options['display_exclude_from'])) {
		$options['display_exclude_from'] = "";
	}
	if (!isset($options['display_position_mobile'])) {
		$options['display_position_mobile'] = "";
	}
	
	if (!isset($options['another_display_deactivate_mobile'])) {
		$options['another_display_deactivate_mobile'] = 'false';
	}
	
	if (!isset($options['always_hide_names_mobile'])) {
		$options['always_hide_names_mobile'] = 'false';
	}
	if (!isset($options['force_hide_buttons_on_all_mobile'])) {
		$options['force_hide_buttons_on_all_mobile'] = 'false';
	}
	
	$current_options = get_option ( EasySocialShareButtons::$plugin_settings_name );
	
	$current_options ['hide_social_name'] = ( int ) $options ['hide_social_name'] == 1 ? 1 : 0;
	$current_options ['show_counter'] = ( int ) $options ['show_counter'] == 1 ? 1 : 0;
	
	// if ( is_array($options['display_in_types']) &&
	// count($options['display_in_types']) > 0 ) {
	if (! isset ( $options ['display_in_types'] )) {
		$options ['display_in_types'] = array ();
	}
	$current_options ['display_in_types'] = $options ['display_in_types'];
	// }
	$current_options ['display_where'] = in_array ( $options ['display_where'], array ('bottom', 'top', 'both', 'nowhere', 'float', 'sidebar', 'popup', 'likeshare', 'sharelike' ) ) ? $options ['display_where'] : 'bottom';
	$current_options ['force_hide_social_name'] = $options ['force_hide_social_name'];
	$current_options ['native_social_counters_fb'] = $options ['native_social_counters_fb'];
	$current_options ['native_social_counters_g'] = $options ['native_social_counters_g'];
	$current_options ['native_social_counters_t'] = $options ['native_social_counters_t'];
	
	$current_options ['native_social_counters_youtube'] = $options ['native_social_counters_youtube'];
	
	$current_options ['woocommece_share'] = $options ['woocommece_share'];
	
	$current_options ['message_share_buttons'] = $options ['message_share_buttons'];
	$current_options ['message_like_buttons'] = $options ['message_like_buttons'];
	
	$current_options ['popup_window_title'] = $options ['popup_window_title'];
	$current_options ['popup_window_close_after'] = $options ['popup_window_close_after'];
	$current_options ['popup_window_popafter'] = $options ['popup_window_popafter'];
	
	$current_options ['native_social_language'] = $options ['native_social_language'];
	
	$current_options ['float_top'] = $options ['float_top'];
	$current_options ['float_bg'] = $options ['float_bg'];
	$current_options ['sidebar_pos'] = $options ['sidebar_pos'];
	$current_options ['counter_pos'] = $options ['counter_pos'];
	$current_options ['force_hide_total_count'] = $options ['force_hide_total_count'];
	
	$current_options ['display_excerpt'] = $options ['display_excerpt'];
	$current_options ['buddypress_activity'] = $options ['buddypress_activity'];
	$current_options ['buddypress_group'] = $options ['buddypress_group'];
	$current_options ['bbpress_topic'] = $options ['bbpress_topic'];
	$current_options ['bbpress_forum'] = $options ['bbpress_forum'];
	
	$current_options ['sidebar_sticky'] = $options ['sidebar_sticky'];
	$current_options ['float_full'] = $options ['float_full'];
	$current_options ['total_counter_pos'] = $options ['total_counter_pos'];
	$current_options ['force_counters_admin'] = $options ['force_counters_admin'];
	$current_options['force_hide_buttons_on_mobile'] = $options['force_hide_buttons_on_mobile'];
	
	$current_options['another_display_popup'] = $options['another_display_popup'];
	$current_options['another_display_sidebar'] = $options['another_display_sidebar'];
	
	$current_options['float_js'] = $options['float_js'];
	
	$current_options['translate_love_loved'] = $options['translate_love_loved'];
	$current_options['translate_love_thanks'] = $options['translate_love_thanks'];
	
	$current_options['display_exclude_from'] = $options['display_exclude_from'];
	$current_options['display_position_mobile'] = $options['display_position_mobile'];
	$current_options['another_display_deactivate_mobile'] = $options['another_display_deactivate_mobile'];
	
	// @since 1.3.9.2
	$current_options['always_hide_names_mobile'] = $options['always_hide_names_mobile'];
	// @since 1.3.9.3
	$current_options['force_hide_buttons_on_all_mobile'] = $options['force_hide_buttons_on_all_mobile'];
	
	$current_options ['network_message'] = $mm;
	
	// @since 1.1.4
	// update network names
	foreach ( $nn as $k => $v ) {
		$current_options ['networks'] [$k] [1] = $v;
	}
	
	update_option ( EasySocialShareButtons::$plugin_settings_name, $current_options );
	$msg = "Settings are saved.";
}

function essb_select_content_type() {
	$pts = get_post_types ( array ('public' => true, 'show_ui' => true, '_builtin' => true ) );
	$cpts = get_post_types ( array ('public' => true, 'show_ui' => true, '_builtin' => false ) );
	$options = get_option ( EasySocialShareButtons::$plugin_settings_name );
	
	$woocommerce_selected = isset ( $options ['woocommece_share'] ) ? $options ['woocommece_share'] : 'false';
	$is_woocommerce_selected = ($woocommerce_selected == 'true') ? ' checked="checked"' : '';
	
	$excerpt_selected = isset ( $options ['display_excerpt'] ) ? $options ['display_excerpt'] : 'false';
	$is_excerpt_selected = ($excerpt_selected == 'true') ? ' checked="checked"' : '';
	
	$all_lists_selected = '';
	if (is_array ( $options ['display_in_types'] )) {
		$all_lists_selected = in_array ( 'all_lists', $options ['display_in_types'] ) ? 'checked="checked"' : '';
	}
	
	if (is_array ( $options ) && isset ( $options ['display_in_types'] ) && is_array ( $options ['display_in_types'] )) {
		
		global $wp_post_types;
		// classical post type listing
		foreach ( $pts as $pt ) {
			
			$selected = in_array ( $pt, $options ['display_in_types'] ) ? 'checked="checked"' : '';
			
			$icon = "";
			echo '<input type="checkbox" name="general_options[display_in_types][]" id="' . $pt . '" value="' . $pt . '" ' . $selected . '> <label for="' . $pt . '">' . $icon . ' ' . $wp_post_types [$pt]->label . '</label><br />';
		}
		
		// custom post types listing
		if (is_array ( $cpts ) && ! empty ( $cpts )) {
			foreach ( $cpts as $cpt ) {
				
				$selected = in_array ( $cpt, $options ['display_in_types'] ) ? 'checked="checked"' : '';
				
				$icon = "";
				echo '<input type="checkbox" name="general_options[display_in_types][]" id="' . $cpt . '" value="' . $cpt . '" ' . $selected . '> <label for="' . $cpt . '">' . $icon . ' ' . $wp_post_types [$cpt]->label . '</label><br />';
			}
		}
	}
	echo '<input type="checkbox" name="general_options[display_in_types][]" id="all_lists" value="all_lists" ' . $all_lists_selected . '> <label for="all_lists">' . "" . ' ' . sprintf ( __ ( 'Lists of articles <br />%s<span class="label">(blog, archives, search results, etc.)</span>%s', ESSB_TEXT_DOMAIN ), '<em>', '</em>' ) . '</label><br/><br/>';
	echo '<input type="checkbox" name="general_options[woocommece_share]" id="woocommece_share" value="true" ' . $is_woocommerce_selected . '> <label for="woocommece_share">' . "" . ' ' . sprintf ( __ ( 'WooCommence Products <br />%s<span class="label">(Activate share buttons for WooCommerce Products)</span>%s', ESSB_TEXT_DOMAIN ), '<em>', '</em>' ) . '</label><br/><br/>';
	echo '<input type="checkbox" name="general_options[display_excerpt]" id="display_excerpt" value="true" ' . $is_excerpt_selected . '> <label for="display_excerpt">' . "" . ' ' . sprintf ( __ ( 'Display in post excerpt <br />%s<span class="label">(Activate this option if your theme is using excerpts and you wish to display share buttons in excerpts)</span>%s', ESSB_TEXT_DOMAIN ), '<em>', '</em>' ) . '</label>';
}

function essb_setting_radio_where() {
	
	$options = get_option ( EasySocialShareButtons::$plugin_settings_name );
	
	// print_r($options);
	
	$w_bottom = $w_top = $w_both = $w_nowhere = $w_float = $w_sidebar = $w_popup = $w_likeshare = $w_sharelike = "";
	if (is_array ( $options ) && isset ( $options ['display_where'] ))
		${'w_' . $options ['display_where']} = " checked='checked'";
	
	echo '<table border="0" cellpadding="2" cellspacing="0">';
	echo '<col width="33%"/>';
	echo '<col width="33%"/>';
	echo '<col width="33%"/>';
	echo '<tr>';
	echo '<td align="center" valign="top"><img src="' . ESSB_PLUGIN_URL . '/assets/images/icons-position-04.png" style="margin-bottom: 5px;" /><br/><input id="" value="bottom" name="general_options[display_where]" type="radio" ' . $w_bottom . ' />
	<br/><label for="">' . __ ( 'Content bottom', ESSB_TEXT_DOMAIN ) . '</label></td>';
	echo '<td align="center" valign="top" style="background-color: #ffffff !important;"><img src="' . ESSB_PLUGIN_URL . '/assets/images/icons-position-01.png"  style="margin-bottom: 5px;" /><br/><input id="" value="top" name="general_options[display_where]" type="radio" ' . $w_top . ' />
	<br/><label for="">' . __ ( 'Content top', ESSB_TEXT_DOMAIN ) . '</label></td>';
	echo '<td align="center"  valign="top"><img src="' . ESSB_PLUGIN_URL . '/assets/images/icons-position-02.png"  style="margin-bottom: 5px;" /><br/><input id="" value="both" name="general_options[display_where]" type="radio" ' . $w_both . ' />
	<br/><label for="">' . __ ( 'Both (content bottom and top)', ESSB_TEXT_DOMAIN ) . '</label></td>';
	echo '</tr>';
	
	echo '<tr>';
	echo '<td align="center" valign="top" style="border-top: 1px solid #dadada;"><img src="' . ESSB_PLUGIN_URL . '/assets/images/icons-position-03.png" style="margin-bottom: 5px;" /><br/><input id="" value="float" name="general_options[display_where]" type="radio" ' . $w_float . ' />
	<br/><label for="">' . __ ( "Float from content top", ESSB_TEXT_DOMAIN ) . '</label></td>';
	echo '<td align="center" valign="top" style="background-color: #ffffff !important; border-top: 1px solid #dadada;"><img src="' . ESSB_PLUGIN_URL . '/assets/images/icons-position-05.png"  style="margin-bottom: 5px;" /><br/><input id="" value="sidebar" name="general_options[display_where]" type="radio" ' . $w_sidebar . ' />
	<br/><label for="">' . __ ( "Sidebar", ESSB_TEXT_DOMAIN ) . '</label></td>';
	echo '<td align="center"  valign="top"  style="border-top: 1px solid #dadada;"><img src="' . ESSB_PLUGIN_URL . '/assets/images/icons-position-06.png"  style="margin-bottom: 5px;" /><br/><input id="" value="nowhere" name="general_options[display_where]" type="radio" ' . $w_nowhere . ' />
	<br/><label for="">' . __ ( "Via shortcode only", ESSB_TEXT_DOMAIN ) . '</label><br /><strong>[easy-share]</strong> or <strong>[essb]</strong></td>';
	echo '</tr>';
	
	echo '<tr>';
	echo '<td align="center" valign="top" style="border-top: 1px solid #dadada;"><img src="' . ESSB_PLUGIN_URL . '/assets/images/icons-position-07.png" style="margin-bottom: 5px;" /><br/><input id="" value="popup" name="general_options[display_where]" type="radio" ' . $w_popup . ' />
	<br/><label for="">' . __ ( "Pop up", ESSB_TEXT_DOMAIN ) . '</label></td>';
	echo '<td align="center" valign="top" style="background-color: #ffffff !important; border-top: 1px solid #dadada;"><img src="' . ESSB_PLUGIN_URL . '/assets/images/icons-position-02.png"  style="margin-bottom: 5px;" /><br/><input id="" value="likeshare" name="general_options[display_where]" type="radio" ' . $w_likeshare . ' />
	<br/><label for="">' . __ ( 'Like top/Share bottom', ESSB_TEXT_DOMAIN ) . '</label></td>';
	echo '<td align="center"  valign="top"  style="border-top: 1px solid #dadada;"><img src="' . ESSB_PLUGIN_URL . '/assets/images/icons-position-02.png"  style="margin-bottom: 5px;" /><br/><input id="" value="sharelike" name="general_options[display_where]" type="radio" ' . $w_sharelike . ' />
	<br/><label for="">' . __ ( 'Share top/Like bottom', ESSB_TEXT_DOMAIN ) . '</label></td>';
	echo '</tr>';
	
	echo '</table>';
}

function essb_radio_hide_social_name() {
	$y = $n = '';
	$options = get_option ( EasySocialShareButtons::$plugin_settings_name );
	
	if (is_array ( $options ))
		(isset ( $options ['hide_social_name'] ) and $options ['hide_social_name'] == 1) ? $y = " checked='checked'" : $n = " checked='checked'";
	
	echo '<input id="hide_name_yes" value="1" name="general_options[hide_social_name]" type="radio" ' . $y . ' />
	<label for="hide_name_yes">' . __ ( 'Yes', ESSB_TEXT_DOMAIN ) . '</label>
		
	<input id="hide_name_no" value="0" name="general_options[hide_social_name]" type="radio" ' . $n . ' />
	<label for="hide_name_no">' . __ ( 'No', ESSB_TEXT_DOMAIN ) . '</label>';
}

function essb_setting_radio_counter() {
	
	$y = $n = '';
	$options = get_option ( EasySocialShareButtons::$plugin_settings_name );
	
	if (is_array ( $options ))
		(isset ( $options ['show_counter'] ) and $options ['show_counter'] == 1) ? $y = " checked='checked'" : $n = " checked='checked'";
	
	echo '	<input id="" value="1" name="general_options[show_counter]" type="radio" ' . $y . ' />
	<label for="">' . __ ( 'Yes', ESSB_TEXT_DOMAIN ) . '</label>
		
	<input id="" value="0" name="general_options[show_counter]" type="radio" ' . $n . ' />
	<label for="">' . __ ( 'No', ESSB_TEXT_DOMAIN ) . '</label>';
}

function essb_force_hide_name() {
	$options = get_option ( EasySocialShareButtons::$plugin_settings_name );
	if (is_array ( $options )) {
		$exist = isset ( $options ['force_hide_social_name'] ) ? $options ['force_hide_social_name'] : 'false';
		
		$is_checked = ($exist == 'true') ? ' checked="checked"' : '';
		echo '<p style="margin: .2em 5% .2em 0;"><input id="force_hide_social_name" type="checkbox" name="general_options[force_hide_social_name]" value="true" ' . $is_checked . ' /><label for="force_hide_social_name"></label></p>';
	
	}

}

function essb_force_hide_buttons_on_mobile() {
	$options = get_option ( EasySocialShareButtons::$plugin_settings_name );
	if (is_array ( $options )) {
		$exist = isset ( $options ['force_hide_buttons_on_mobile'] ) ? $options ['force_hide_buttons_on_mobile'] : 'false';

		$is_checked = ($exist == 'true') ? ' checked="checked"' : '';
		echo '<p style="margin: .2em 5% .2em 0;"><input id="force_hide_buttons_on_mobile" type="checkbox" name="general_options[force_hide_buttons_on_mobile]" value="true" ' . $is_checked . ' /><label for="force_hide_buttons_on_mobile"></label></p>';

	}

}

function essb_another_display_sidebar() {
	$options = get_option ( EasySocialShareButtons::$plugin_settings_name );
	if (is_array ( $options )) {
		$exist = isset ( $options ['another_display_sidebar'] ) ? $options ['another_display_sidebar'] : 'false';

		$is_checked = ($exist == 'true') ? ' checked="checked"' : '';
		echo '<p style="margin: .2em 5% .2em 0;"><input id="another_display_sidebar" type="checkbox" name="general_options[another_display_sidebar]" value="true" ' . $is_checked . ' /><label for="another_display_sideba"></label></p>';

	}

}

function essb_another_display_popup() {
	$options = get_option ( EasySocialShareButtons::$plugin_settings_name );
	if (is_array ( $options )) {
		$exist = isset ( $options ['another_display_popup'] ) ? $options ['another_display_popup'] : 'false';

		$is_checked = ($exist == 'true') ? ' checked="checked"' : '';
		echo '<p style="margin: .2em 5% .2em 0;"><input id="another_display_popup" type="checkbox" name="general_options[another_display_popup]" value="true" ' . $is_checked . ' /><label for="another_display_popup"></label></p>';

	}

}


function essb_loadcounters_from_admin() {
	$options = get_option ( EasySocialShareButtons::$plugin_settings_name );
	if (is_array ( $options )) {
		$exist = isset ( $options ['force_counters_admin'] ) ? $options ['force_counters_admin'] : 'false';
		
		$is_checked = ($exist == 'true') ? ' checked="checked"' : '';
		echo '<p style="margin: .2em 5% .2em 0;"><input id="force_counters_admin" type="checkbox" name="general_options[force_counters_admin]" value="true" ' . $is_checked . ' /><label for="force_counters_admin"></label></p>';
	
	}

}

function essb_force_hide_total_count() {
	$options = get_option ( EasySocialShareButtons::$plugin_settings_name );
	if (is_array ( $options )) {
		$exist = isset ( $options ['force_hide_total_count'] ) ? $options ['force_hide_total_count'] : 'false';
		
		$is_checked = ($exist == 'true') ? ' checked="checked"' : '';
		echo '<p style="margin: .2em 5% .2em 0;"><input id="force_hide_total_count" type="checkbox" name="general_options[force_hide_total_count]" value="true" ' . $is_checked . ' /><label for="force_hide_total_count"></label></p>';
	
	}

}

function essb_native_social_counters() {
	$options = get_option ( EasySocialShareButtons::$plugin_settings_name );
	if (is_array ( $options )) {
		$exist = isset ( $options ['native_social_counters_fb'] ) ? $options ['native_social_counters_fb'] : 'false';
		
		$is_checked = ($exist == 'true') ? ' checked="checked"' : '';
		echo '<p style="margin: .2em 5% .2em 0;"><input id="native_social_counters_fb" type="checkbox" name="general_options[native_social_counters_fb]" value="true" ' . $is_checked . ' /><label for="native_social_counters_fb">Facebook</label></p>';
		
		$exist = isset ( $options ['native_social_counters_g'] ) ? $options ['native_social_counters_g'] : 'false';
		
		$is_checked = ($exist == 'true') ? ' checked="checked"' : '';
		echo '<p style="margin: .2em 5% .2em 0;"><input id="native_social_counters_g" type="checkbox" name="general_options[native_social_counters_g]" value="true" ' . $is_checked . ' /><label for="native_social_counters_g">Google+</label></p>';
		
		$exist = isset ( $options ['native_social_counters_t'] ) ? $options ['native_social_counters_t'] : 'false';
		
		$is_checked = ($exist == 'true') ? ' checked="checked"' : '';
		echo '<p style="margin: .2em 5% .2em 0;"><input id="native_social_counters_t" type="checkbox" name="general_options[native_social_counters_t]" value="true" ' . $is_checked . ' /><label for="native_social_counters_t">Twitter</label></p>';
		
		$exist = isset ( $options ['native_social_counters_youtube'] ) ? $options ['native_social_counters_youtube'] : 'false';
		
		$is_checked = ($exist == 'true') ? ' checked="checked"' : '';
		echo '<p style="margin: .2em 5% .2em 0;"><input id="native_social_counters_youtube" type="checkbox" name="general_options[native_social_counters_youtube]" value="true" ' . $is_checked . ' /><label for="native_social_counters_youtube">YouTube</label></p>';
	}

}

function essb_message_above_buttons() {
	$options = get_option ( EasySocialShareButtons::$plugin_settings_name );
	if (is_array ( $options )) {
		$exist = isset ( $options ['message_share_buttons'] ) ? $options ['message_share_buttons'] : '';
		$exist = stripslashes ( $exist );
		echo '<textarea id="message_share_buttons" name="general_options[message_share_buttons]" class="input-element stretched" rows="5">' . esc_textarea (  ( $exist ) ) . '</textarea>';
	
	}

}

function essb_message_above_like_buttons() {
	$options = get_option ( EasySocialShareButtons::$plugin_settings_name );
	if (is_array ( $options )) {
		$exist = isset ( $options ['message_like_buttons'] ) ? $options ['message_like_buttons'] : '';
		$exist = stripslashes ( $exist );		
		echo '<textarea id="message_like_buttons" name="general_options[message_like_buttons]" class="input-element stretched" rows="5">' . esc_textarea (  ( $exist ) ) . '</textarea>';
		
	}

}

function essb_setting_rename_network_selection() {
	$y = $n = '';
	$options = get_option ( EasySocialShareButtons::$plugin_settings_name );
	
	if (is_array ( $options )) {
		foreach ( $options ['networks'] as $k => $v ) {
			
			$is_checked = ($v [0] == 1) ? ' checked="checked"' : '';
			$network_name = isset ( $v [1] ) ? $v [1] : $k;
			
			echo '<li><p style="margin: .2em 5% .2em 0;">
			<input id="network_selection_' . $k . '" value="' . esc_attr(stripslashes($network_name)) . '" name="general_options_nn[' . $k . ']" type="text"
			class="input-element" />
			<label for="network_selection_' . $k . '"><span class="essb_icon essb_icon_' . $k . '"></span>' . esc_attr(stripslashes($network_name)) . '</label>
			</p></li>';
		}
	
	}
}

function essb_popup_window_title() {
	$options = get_option ( EasySocialShareButtons::$plugin_settings_name );
	if (is_array ( $options )) {
		$exist = isset ( $options ['popup_window_title'] ) ? $options ['popup_window_title'] : '';
		$exist = stripslashes ( $exist );
		
		echo '<p style="margin: .2em 5% .2em 0;"><input id="popup_window_title" type="text" name="general_options[popup_window_title]" value="' . $exist . '" class="input-element stretched" /></p>';
	
	}

}

function essb_popup_window_close_after() {
	$options = get_option ( EasySocialShareButtons::$plugin_settings_name );
	if (is_array ( $options )) {
		$exist = isset ( $options ['popup_window_close_after'] ) ? $options ['popup_window_close_after'] : '';
		$exist = stripslashes ( $exist );
		
		echo '<p style="margin: .2em 5% .2em 0;"><input id="popup_window_close_after" type="text" name="general_options[popup_window_close_after]" value="' . $exist . '" class="input-element" /></p>';
	
	}
}

// popup_window_popafter
function essb_popup_window_popafter() {
	$options = get_option ( EasySocialShareButtons::$plugin_settings_name );
	if (is_array ( $options )) {
		$exist = isset ( $options ['popup_window_popafter'] ) ? $options ['popup_window_popafter'] : '';
		$exist = stripslashes ( $exist );
		
		echo '<p style="margin: .2em 5% .2em 0;"><input id="popup_window_popafter" type="text" name="general_options[popup_window_popafter]" value="' . $exist . '" class="input-element" /></p>';
	
	}
}

function essb_custom_float_from_top_pos() {
	$options = get_option ( EasySocialShareButtons::$plugin_settings_name );
	if (is_array ( $options )) {
		$exist = isset ( $options ['float_top'] ) ? $options ['float_top'] : '';
		$exist = stripslashes ( $exist );
		
		echo '<p style="margin: .2em 5% .2em 0;"><input id="float_top" type="text" name="general_options[float_top]" value="' . $exist . '" class="input-element" /></p>';
	
	}
}

function essb_custom_float_from_top_fullwidth() {
	$options = get_option ( EasySocialShareButtons::$plugin_settings_name );
	if (is_array ( $options )) {
		$exist = isset ( $options ['float_full'] ) ? $options ['float_full'] : 'false';
		
		$is_checked = ($exist == 'true') ? ' checked="checked"' : '';
		echo '<p style="margin: .2em 5% .2em 0;"><input id="float_full" type="checkbox" name="general_options[float_full]" value="true" ' . $is_checked . ' /><label for="float_full"></label></p>';
	
	}

}

function essb_custom_float_from_top_js() {
	$options = get_option ( EasySocialShareButtons::$plugin_settings_name );
	if (is_array ( $options )) {
		$exist = isset ( $options ['float_js'] ) ? $options ['float_js'] : 'false';

		$is_checked = ($exist == 'true') ? ' checked="checked"' : '';
		echo '<p style="margin: .2em 5% .2em 0;"><input id="float_js" type="checkbox" name="general_options[float_js]" value="true" ' . $is_checked . ' /><label for="float_js"></label></p>';

	}

}

function essb_custom_float_from_top_bgcolor() {
	$options = get_option ( EasySocialShareButtons::$plugin_settings_name );
	if (is_array ( $options )) {
		$exist = isset ( $options ['float_bg'] ) ? $options ['float_bg'] : '';
		$exist = stripslashes ( $exist );
		
		echo '<p style="margin: .2em 5% .2em 0;"><input id="float_bg" type="text" name="general_options[float_bg]" value="' . $exist . '" class="input-element" /></p>';
	
	}
}

function essb_custom_sidebar_pos() {
	$options = get_option ( EasySocialShareButtons::$plugin_settings_name );
	if (is_array ( $options )) {
		$exist = isset ( $options ['sidebar_pos'] ) ? $options ['sidebar_pos'] : '';
		$exist = stripslashes ( $exist );
		
		echo '<p style="margin: .2em 5% .2em 0;"><select id="sidebar_pos" type="text" name="general_options[sidebar_pos]" class="input-element">
		<option value="" ' . ($exist == '' ? ' selected="selected"' : '') . '>Left</option>
		<option value="right" ' . ($exist == 'right' ? ' selected="selected"' : '') . '>Right</option>
		<option value="bottom" ' . ($exist == 'bottom' ? ' selected="selected"' : '') . '>Bottom</option>
		</select>
		</p>';
	
	}
}

function essb_sidebar_sticky() {
	$options = get_option ( EasySocialShareButtons::$plugin_settings_name );
	if (is_array ( $options )) {
		$exist = isset ( $options ['sidebar_sticky'] ) ? $options ['sidebar_sticky'] : 'false';
		
		$is_checked = ($exist == 'true') ? ' checked="checked"' : '';
		echo '<p style="margin: .2em 5% .2em 0;"><input id="sidebar_sticky" type="checkbox" name="general_options[sidebar_sticky]" value="true" ' . $is_checked . ' /><label for="sidebar_sticky"></label></p>';
	
	}

}

function essb_native_social_language() {
	$options = get_option ( EasySocialShareButtons::$plugin_settings_name );
	if (is_array ( $options )) {
		$exist = isset ( $options ['native_social_language'] ) ? $options ['native_social_language'] : '';
		$exist = stripslashes ( $exist );
		
		echo '<p style="margin: .2em 5% .2em 0;"><input id="native_social_language" type="text" name="general_options[native_social_language]" value="' . $exist . '" class="input-element"  /><br/><span class="small">Example: en - English, fr - French, es - Spanish, de - German, tr - Turkish</span></p>';
	
	}

}

function essb_setting_rename_network_messages() {
	$y = $n = '';
	$options = get_option ( EasySocialShareButtons::$plugin_settings_name );
	
	if (is_array ( $options )) {
		foreach ( $options ['networks'] as $k => $v ) {
			
			$is_checked = ($v [0] == 1) ? ' checked="checked"' : '';
			$network_name = isset ( $v [1] ) ? $v [1] : $k;
			
			$message_pass = "";
			if (isset ( $options ['network_message'] )) {
				$settings = $options ['network_message'];
				$message_pass = isset ( $settings [$k] ) ? $settings [$k] : '';
			}
			
			echo '<li><p style="margin: .2em 5% .2em 0;">
			<input id="network_selection_' . $k . '" value="' . $message_pass . '" name="general_options_mm[' . $k . ']" type="text"
			class="input-element" />
			<label for="network_selection_' . $k . '"><span class="essb_icon essb_icon_' . $k . '"></span>' . $network_name . '</label>
			</p></li>';
		}
	
	}
}

function essb_custom_counter_pos() {
	$options = get_option ( EasySocialShareButtons::$plugin_settings_name );
	if (is_array ( $options )) {
		$exist = isset ( $options ['counter_pos'] ) ? $options ['counter_pos'] : '';
		$exist = stripslashes ( $exist );
		
		echo '<p style="margin: .2em 5% .2em 0;"><select id="counter_pos" type="text" name="general_options[counter_pos]" class="input-element">
		<option value="" ' . ($exist == '' ? ' selected="selected"' : '') . '>Left</option>
		<option value="right" ' . ($exist == 'right' ? ' selected="selected"' : '') . '>Right</option>
		<option value="inside" ' . ($exist == 'inside' ? ' selected="selected"' : '') . '>Inside Buttons</option>
		<option value="hidden" ' . ($exist == 'hidden' ? ' selected="selected"' : '') . '>Hidden</option>
		</select>
		</p>';
	
	}
}

function essb_custom_total_counter_pos() {
	$options = get_option ( EasySocialShareButtons::$plugin_settings_name );
	if (is_array ( $options )) {
		$exist = isset ( $options ['total_counter_pos'] ) ? $options ['total_counter_pos'] : '';
		$exist = stripslashes ( $exist );
		
		echo '<p style="margin: .2em 5% .2em 0;"><select id="total_counter_pos" type="text" name="general_options[total_counter_pos]" class="input-element">
		<option value="" ' . ($exist == '' ? ' selected="selected"' : '') . '>Right</option>
		<option value="left" ' . ($exist == 'left' ? ' selected="selected"' : '') . '>Left</option>
		<option value="rightbig" ' . ($exist == 'rightbig' ? ' selected="selected"' : '') . '>Right Big Numbers Only</option>
		<option value="leftbig" ' . ($exist == 'leftbig' ? ' selected="selected"' : '') . '>Left Big Numbers Only</option>
		</select>
		</p>';
	
	}
}

function essb_bbpress_forum() {
	$options = get_option ( EasySocialShareButtons::$plugin_settings_name );
	if (is_array ( $options )) {
		$exist = isset ( $options ['bbpress_forum'] ) ? $options ['bbpress_forum'] : 'false';
		
		$is_checked = ($exist == 'true') ? ' checked="checked"' : '';
		echo '<p style="margin: .2em 5% .2em 0;"><input id="bbpress_forum" type="checkbox" name="general_options[bbpress_forum]" value="true" ' . $is_checked . ' /><label for="bbpress_forum"></label></p>';
	
	}

}

function essb_bbpress_topic() {
	$options = get_option ( EasySocialShareButtons::$plugin_settings_name );
	if (is_array ( $options )) {
		$exist = isset ( $options ['bbpress_topic'] ) ? $options ['bbpress_topic'] : 'false';
		
		$is_checked = ($exist == 'true') ? ' checked="checked"' : '';
		echo '<p style="margin: .2em 5% .2em 0;"><input id="bbpress_topic" type="checkbox" name="general_options[bbpress_topic]" value="true" ' . $is_checked . ' /><label for="bbpress_topic"></label></p>';
	
	}

}

function essb_buddypress_activity() {
	$options = get_option ( EasySocialShareButtons::$plugin_settings_name );
	if (is_array ( $options )) {
		$exist = isset ( $options ['buddypress_activity'] ) ? $options ['buddypress_activity'] : 'false';
		
		$is_checked = ($exist == 'true') ? ' checked="checked"' : '';
		echo '<p style="margin: .2em 5% .2em 0;"><input id="buddypress_activity" type="checkbox" name="general_options[buddypress_activity]" value="true" ' . $is_checked . ' /><label for="buddypress_activity"></label></p>';
	
	}

}

function essb_buddypress_group() {
	$options = get_option ( EasySocialShareButtons::$plugin_settings_name );
	if (is_array ( $options )) {
		$exist = isset ( $options ['buddypress_group'] ) ? $options ['buddypress_group'] : 'false';
		
		$is_checked = ($exist == 'true') ? ' checked="checked"' : '';
		echo '<p style="margin: .2em 5% .2em 0;"><input id="buddypress_group" type="checkbox" name="general_options[buddypress_group]" value="true" ' . $is_checked . ' /><label for="buddypress_group"></label></p>';
	
	}

}

function essb_localize_loveyou_button() {
	$options = get_option ( EasySocialShareButtons::$plugin_settings_name );
	if (is_array ( $options )) {
		echo "<ul style='margin: 0; padding:0;'>";

		$k = 'translate_love_thanks';
		$value = isset($options[$k]) ? $options[$k] : '';

		echo '<li><p style="margin: .2em 5% .2em 0;">
		<input value="' . $value . '" name="general_options[' . $k . ']" type="text"
		class="input-element" />
		<label for="network_selection_' . $k . '">Thank you for loving this.</label>
		</p></li>';

		$k = 'translate_love_loved';
		$value = isset($options[$k]) ? $options[$k] : '';

		echo '<li><p style="margin: .2em 5% .2em 0;">
		<input value="' . $value . '" name="general_options[' . $k . ']" type="text"
		class="input-element" />
		<label for="network_selection_' . $k . '">You already love this today.</label>
		</p></li>';


		echo "</ul>";
	}
}


function essb_display_exclude_from() {
	$options = get_option ( EasySocialShareButtons::$plugin_settings_name );
	if (is_array ( $options )) {
		$exist = isset ( $options ['display_exclude_from'] ) ? $options ['display_exclude_from'] : '';
		$exist = stripslashes ( $exist );

		echo '<p style="margin: .2em 5% .2em 0;"><input id="display_exclude_from" type="text" name="general_options[display_exclude_from]" value="' . $exist . '" class="input-element"  /><br/><span class="small"> Exclude buttons on posts/pages with these IDs? Comma seperated: "11, 15, 125"</span></p>';

	}

}

function essb_display_for_mobile() {
	$options = get_option ( EasySocialShareButtons::$plugin_settings_name );
	if (is_array ( $options )) {
		$exist = isset ( $options ['display_position_mobile'] ) ? $options ['display_position_mobile'] : '';
		$exist = stripslashes ( $exist );
	
		echo '<p style="margin: .2em 5% .2em 0;"><select id="display_position_mobile" type="text" name="general_options[display_position_mobile]" class="input-element">
		<option value="" ' . ($exist == '' ? ' selected="selected"' : '') . '>No</option>
		<option value="bottom" ' . ($exist == 'bottom' ? ' selected="selected"' : '') . '>Content bottom</option>
		<option value="top" ' . ($exist == 'top' ? ' selected="selected"' : '') . '>Content top</option>
		<option value="both" ' . ($exist == 'both' ? ' selected="selected"' : '') . '>Content top and bottom</option>
		<option value="float" ' . ($exist == 'float' ? ' selected="selected"' : '') . '>Float from content top</option>
		</select>
		</p>';
	}
}

?>

<div class="wrap">
	
	<?php
	
	if ($msg != "") {
		echo '<div class="updated" style="padding: 10px;">' . $msg . '</div>';
	}
	
	?>
	
		<form name="general_form" method="post"
		action="admin.php?page=essb_settings&tab=display"
		enctype="multipart/form-data">
		<input type="hidden" id="cmd" name="cmd" value="update" />

		<div class="essb-options">
			<div class="essb-options-header" id="essb-options-header">
				<div class="essb-options-title">
			Display Settings<br /><span class="label" style="font-weight: 400;"><a href="http://codecanyon.net/item/easy-social-share-buttons-for-wordpress/6394476?ref=appscreo" target="_blank" style="text-decoration: none;">Easy Social Share Buttons for WordPress version <?php echo ESSB_VERSION; ?></a></span>
		</div>		
		<?php echo '<input type="Submit" name="Submit" value="' . __ ( 'Update Settings', ESSB_TEXT_DOMAIN ) . '" class="button-primary" />'; ?>
	</div>
			<div class="essb-options-sidebar">
				<ul class="essb-options-group-menu">
					<li id="essb-menu-1" class="essb-menu-item"><a href="#"
						onclick="essb_option_activate('1'); return false;">Display
							Settings</a></li>
					<li id="essb-menu-2" class="essb-menu-item"><a href="#"
						onclick="essb_option_activate('2'); return false;">Button Position</a></li>
					<li id="essb-menu-3" class="essb-menu-item"><a href="#"
						onclick="essb_option_activate('3'); return false;">Float From Top Settings</a></li>
					<li id="essb-menu-4" class="essb-menu-item"><a href="#"
						onclick="essb_option_activate('4'); return false;">Sidebar Settings</a>
					</li>
					<li id="essb-menu-5" class="essb-menu-item"><a href="#"
						onclick="essb_option_activate('5'); return false;">Popup Window Settings</a></li>
					<li id="essb-menu-6" class="essb-menu-item"><a href="#"
						onclick="essb_option_activate('6'); return false;">Network Names and Counters</a></li>
					<li id="essb-menu-7" class="essb-menu-item"><a href="#"
						onclick="essb_option_activate('7'); return false;">Rename default network texts</a></li>
					<li id="essb-menu-8" class="essb-menu-item"><a href="#"
						onclick="essb_option_activate('8'); return false;">Message above buttons</a></li>
							
												<li id="essb-menu-9" class="essb-menu-item"><a href="#"
						onclick="essb_option_activate('9'); return false;">Localization Settings</a></li>
							</ul>
			</div>
			<div class="essb-options-container" style="min-height: 450px;">
				<div id="essb-container-1" class="essb-data-container">


					<table border="0" cellpadding="5" cellspacing="0" width="100%">
						<col width="25%" />
						<col width="75%" />
						<tr>
							<td colspan="2" class="sub"><?php _e('Display Settings', ESSB_TEXT_DOMAIN); ?></td>
						</tr>

						<tr class="even table-border-bottom">
							<td valign="top" class="bold"><?php _e('Where to display buttons:', ESSB_TEXT_DOMAIN); ?><br />
								<span class="label" style="font-weight: 400;">Choose post types
									where you wish buttons to appear. If you are running <span
									class="bold">WooCommerce</span> store you can choose between
									post type <span class="bold">Products</span> which will display
									share buttons into product description or option to display
									buttons below price.
							</span></td>
							<td class="essb_general_options"><?php essb_select_content_type(); ?></td>
						</tr>
						<tr class="odd table-border-bottom">
							<td valign="top" class="bold"><?php _e('Exclude display on:', ESSB_TEXT_DOMAIN);?></td>
							<td class="essb_general_options"><?php essb_display_exclude_from(); ?>
						</tr>
						
						<tr class="table-border-bottom">
							<td colspan="2" class="sub2">bbPress Display</td>
						</tr>
						<tr class="odd table-border-bottom">
							<td valign="top" class="bold">Display in forums:<br /> <span
								class="label" style="font-weight: 400;">This will alow users to
									share forum page.</span></td>
							<td class="essb_general_options"><?php essb_bbpress_forum(); ?></td>
						</tr>
						<tr class="even table-border-bottom">
							<td valign="top" class="bold">Display in topics:<br /> <span
								class="label" style="font-weight: 400;">This will alow users to
									share topic page.</span></td>
							<td class="essb_general_options"><?php essb_bbpress_topic(); ?></td>
						</tr>
						<tr class="table-border-bottom">
							<td colspan="2" class="sub2">BuddyPress Display</td>
						</tr>
						<tr class="odd table-border-bottom">
							<td valign="top" class="bold">Display in activity:<br /> <span
								class="label" style="font-weight: 400;">This will alow users to
									share activities.</span></td>
							<td class="essb_general_options"><?php essb_buddypress_activity(); ?></td>
						</tr>
						<tr class="even table-border-bottom">
							<td valign="top" class="bold">Display on group page:<br /> <span
								class="label" style="font-weight: 400;">This will alow users to
									share group page.</span></td>
							<td class="essb_general_options"><?php essb_buddypress_group(); ?></td>
						</tr>
					</table>
				</div>
				<div id="essb-container-2" class="essb-data-container">
					<table border="0" cellpadding="5" cellspacing="0" width="100%">
						<col width="25%" />
						<col width="75%" />
						<tr>
							<td colspan="2" class="sub"><?php _e('Button Position', ESSB_TEXT_DOMAIN); ?></td>
						</tr>

						<tr class="odd table-border-bottom">
							<td valign="top" class="bold"><?php _e('Position of buttons:', ESSB_TEXT_DOMAIN); ?><br />
								<span class="label" style="font-weight: 400;">Choose default
									method that will be used to render buttons. You are able to
									provide custom options for each post/page.</span></td>
							<td class="essb_general_options"><?php essb_setting_radio_where(); ?></td>
						</tr>
						<tr class="even table-border-bottom">
							<td valign="top" class="bold"><?php _e("Activate another display method for mobile:", ESSB_TEXT_DOMAIN);?>
							<span class="label" style="font-weight: 400;">This option allow you to set another display method of buttons which will be applied when page/post is viewed from mobile device.</span></td>
							<td class="essb_general_options"><?php essb_display_for_mobile(); ?></td>
						</tr>
						<tr class="odd table-border-bottom">
							<td valign="top" class="bold">Hide buttons for low resolution mobile devices:</td>
							<td><?php essb_force_hide_buttons_on_mobile();?>
						</tr>
						<tr class="odd table-border-bottom">
							<td valign="top" class="bold">Hide buttons for all mobile devices:</td>
							<td><?php ESSB_Settings_Helper::drawCheckboxField('force_hide_buttons_on_all_mobile');?>
						</tr>
						<tr class="even table-border-bottom">
							<td valign="top" class="bold">Activate Sidebar as another display method:<br/><span class="label" style="font-weight:400;">This option will allow you to display buttons as sidebar in combination with default selected display method.</span></td>
							<td><?php essb_another_display_sidebar();?>
						</tr>
						<tr class="odd table-border-bottom">
							<td valign="top" class="bold">Activate Popup as another display method:<br/><span class="label" style="font-weight:400;">This option will allow you to display buttons as popup window in combination with default selected display method.</span></td>
							<td><?php essb_another_display_popup();?>
						</tr>
						<tr class="even table-border-bottom">
							<td valign="top" class="bold">Deactivate another display methods on mobile:<br/><span class="label" style="font-weight:400;">This option will deactivate display as popup or sidebar as another display method when viewed on mobile device.</span></td>
							<td><?php ESSB_Settings_Helper::drawCheckboxField('another_display_deactivate_mobile');?>
						</tr>
						</table>
				</div>
				<div id="essb-container-3" class="essb-data-container">
					<table border="0" cellpadding="5" cellspacing="0" width="100%">
						<col width="25%" />
						<col width="75%" />
						<tr>
							<td colspan="2" class="sub"><?php _e('Float From Top Settings', ESSB_TEXT_DOMAIN); ?></td>
						</tr>
						<tr class="even">
							<td class="bold">Set custom top of float bar:</td>
							<td class="essb_general_options"><?php essb_custom_float_from_top_pos(); ?></td>
						</tr>
						<tr class="even table-border-bottom">
							<td></td>
							<td class="small">If your curent theme has fixed bar or menu you
								may need to provide custom top position of float or it will be
								rendered below this sticked bar. For example you can try with
								value 40 (which is equal to 40px from top).</td>
						</tr>
						<tr class="odd">
							<td class="bold">Background color of float bar:</td>
							<td class="essb_general_options"><?php essb_custom_float_from_top_bgcolor(); ?></td>
						</tr>
						<tr class="odd table-border-bottom">
							<td></td>
							<td class="small">Default color provided for background of float
								bar is <span class="bold">#FFFFFF</span>. If you wish to change
								it you can enter other color in hex or rgba. For example your
								can set <span class="bold">#e1e1e1</span> or <span class="bold">rgba(200,200,200,1)</span>
							</td>
						</tr>
						<tr class="even">
							<td class="bold">Set full width of float bar:</td>
							<td class="essb_general_options"><?php essb_custom_float_from_top_fullwidth(); ?></td>
						</tr>
						<tr class="even table-border-bottom">
							<td></td>
							<td class="small">This option will make float bar to take full
								width of browser window.</td>
						</tr>
						<tr class="odd">
							<td class="bold">Float with javascript:</td>
							<td class="essb_general_options"><?php essb_custom_float_from_top_js(); ?></td>
						</tr>
						<tr class="odd table-border-bottom">
							<td></td>
							<td class="small">Set this option to avoid Google Chrome issue with fixed elements.</td>
						</tr>						
					</table>
				</div>
				<div id="essb-container-4" class="essb-data-container">
					<table border="0" cellpadding="5" cellspacing="0" width="100%">
						<col width="25%" />
						<col width="75%" />
						<tr>
							<td colspan="2" class="sub"><?php _e('Sidebar Settings', ESSB_TEXT_DOMAIN); ?></td>
						</tr>
						<tr class="even table-border-bottom">
							<td class="bold">Sidebar appearance:<br /> <span class="label"
								style="font-weight: 400;">You choose different position for
									sidebar. Available options are Left (default), Right and Bottom</span></td>
							<td class="essb_general_options"><?php essb_custom_sidebar_pos(); ?></td>
						</tr>
						<tr class="odd table-border-bottom">
							<td class="bold">Sticky Sidebar:<br /> <span class="label"
								style="font-weight: 400;">This will make sidebar when displayed
									left or right to avoid entering footer area.</span></td>
							<td class="essb_general_options"><?php essb_sidebar_sticky(); ?></td>
						</tr>
					</table>
				</div>
				<div id="essb-container-5" class="essb-data-container">
					<table border="0" cellpadding="5" cellspacing="0" width="100%">
						<col width="25%" />
						<col width="75%" />
						<tr>
							<td colspan="2" class="sub"><?php _e('Popup Window Settings', ESSB_TEXT_DOMAIN); ?></td>
						</tr>
						<tr class="odd table-border-bottom">
							<td class="bold">Popup window title:</td>
							<td class="essb_general_options"><?php essb_popup_window_title();?></td>
						</tr>
						<tr class="even table-border-bottom">
							<td class="bold">Automatically close popup after (sec):<br /> <span
								class="label" style="font-weight: 400;">You can provide seconds
									and after they expire window will close automatically. User can
									close this window manually by pressing close button.</span></td>
							<td class="essb_general_options"><?php essb_popup_window_close_after(); ?>
				
						
						
						
						</tr>
						<tr class="odd table-border-bottom">
							<td class="bold">Display popup window after (sec):<br /> <span
								class="label" style="font-weight: 400;">If you wish popup window
									to appear after amount of seconds you can provide theme here.
									Leave blank for immediate popup after page load.</span></td>
							<td class="essb_general_options"><?php essb_popup_window_popafter(); ?></td>
						</tr>
					</table>
				</div>
				<div id="essb-container-6" class="essb-data-container">
					<table border="0" cellpadding="5" cellspacing="0" width="100%">
						<col width="25%" />
						<col width="75%" />
						<tr>
							<td colspan="2" class="sub"><?php _e('Network Names and Counters', ESSB_TEXT_DOMAIN); ?></td>
						</tr>

						<tr class="even table-border-bottom">
							<td valign="top" class="bold"><?php _e('Hide Social Network Names:', ESSB_TEXT_DOMAIN); ?><br />
								<span class="label" style="font-weight: 400;"><?php _e('This will display only social network icon.', ESSB_TEXT_DOMAIN); ?></span></td>
							<td class="essb_general_options"><?php essb_radio_hide_social_name(); ?>
					<br /> <img
								src="<?php echo ESSB_PLUGIN_URL; ?>/assets/images/demo-image1.png" /></td>
						</tr>
									<tr class="odd table-border-bottom">
							<td valign="top" class="bold">Always hide network names on mobile:</td>
							<td><?php ESSB_Settings_Helper::drawCheckboxField('always_hide_names_mobile'); ?></td>
						</tr>
						<tr class="even">
							<td valign="top" class="bold">Don't pop network names:</td>
							<td><?php essb_force_hide_name(); ?></td>
						</tr>
						<tr class="even table-border-bottom">
							<td></td>
							<td class="small">This option will disable network name pop out
								when you activate Hide Social Network Names. This option is
								activated by default on mobile devices and can't be turned off.</td>
						</tr>
						<tr class="odd table-border-bottom">
							<td valign="top" class="bold"><?php _e('Display counter of sharing', ESSB_TEXT_DOMAIN); ?><br />
								<span class="label" style="font-weight: 400;"><?php _e('This may slow down page loading.', ESSB_TEXT_DOMAIN); ?></span></td>
							<td class="essb_general_options"><?php essb_setting_radio_counter(); ?><br />
								<img
								src="<?php echo ESSB_PLUGIN_URL; ?>/assets/images/demo-image2.png" /></td>
						</tr>
						<tr class="even table-border-bottom">
							<td class="bold">Position of counters:</td>
							<td class="essb_general_options"><?php essb_custom_counter_pos(); ?></td>
						</tr>
						<tr class="odd table-border-bottom">
							<td class="bold">Position of total counter:</td>
							<td class="essb_general_options"><?php essb_custom_total_counter_pos(); ?></td>
						</tr>
						<tr class="even table-border-bottom">
							<td class="bold">Load counters with admin-ajax.php call:</td>
							<td class="essb_general_options"><?php essb_loadcounters_from_admin(); ?></td>
						</tr>
						<tr class="odd table-border-bottom">
							<td class="bold" valign="top">Hide total counter when display
								counter of sharing is active:</td>
							<td class="essb_general_options"><?php essb_force_hide_total_count(); ?></td>
						</tr>
						<tr class="even table-border-bottom">
							<td valign="top" class="bold"><?php _e('Display counters for native social buttons', ESSB_TEXT_DOMAIN); ?><br />
								<span class="label" style="font-weight: 400;"><?php _e('This option will show counters form +1, Facebook Like, VK Like and Twitter Follow/Tweet.', ESSB_TEXT_DOMAIN); ?></span></td>
							<td class="essb_general_options"><?php essb_native_social_counters(); ?></td>
						</tr>
					</table>
				</div>
				<div id="essb-container-7" class="essb-data-container">
					<table border="0" cellpadding="5" cellspacing="0" width="100%">
						<col width="25%" />
						<col width="75%" />
						<tr>
							<td colspan="2" class="sub">Rename default network texts</td>
						</tr>
						<tr class="odd table-border-bottom">
							<td class="bold" valign="top">Social Networks:</td>
							<td class="essb_general_options"><ul id="networks-sortable"><?php essb_setting_rename_network_selection(); ?></ul></td>
						</tr>
					</table>
				</div>
				<div id="essb-container-8" class="essb-data-container">
					<table border="0" cellpadding="5" cellspacing="0" width="100%">
						<col width="25%" />
						<col width="75%" />
						<tr>
							<td colspan="2" class="sub"><?php _e('Message above buttons', ESSB_TEXT_DOMAIN); ?></td>
						</tr>
						<tr class="even table-border-bottom">
							<td class="bold">Message above share buttons:<br/><span class="label" style="font-weight: nornal">You can use following variables to create personalized message: %%title%% - displays current post title, %%permalink%% - displays current post address.</span></td>
							<td class="essb_general_options"><?php essb_message_above_buttons();?></td>
						</tr>
						<tr class="odd table-border-bottom">
							<td class="bold">Message above like buttons:<br/><span class="label" style="font-weight: nornal">You can use following variables to create personalized message: %%title%% - displays current post title, %%permalink%% - displays current post address.</span></td>
							<td class="essb_general_options"><?php essb_message_above_like_buttons();?></td>
						</tr>
					</table>
				</div>
				<div id="essb-container-9" class="essb-data-container">
					<table border="0" cellpadding="5" cellspacing="0" width="100%">
						<col width="25%" />
						<col width="75%" />
						<tr>
							<td colspan="2" class="sub">Localization Settings</td>
						</tr>
						<tr class="even table-border-bottom">
							<td class="bold">Language code for native social networks:</td>
							<td class="essb_general_options"><?php essb_native_social_language(); ?></td>
						</tr>
						<tr class="odd table-border-bottom">
							<td class="bold" valign="top">Custom message on social network
								hover:</td>
							<td class="essb_general_options"><ul id="networks-sortable"><?php essb_setting_rename_network_messages(); ?></ul></td>
						</tr>
						<tr class="even table-border-bottom">
							<td class="bold">Localize love you button commands:
							<td class="essb_general_options"><?php essb_localize_loveyou_button(); ?></td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</form>
</div>


<script type="text/javascript">
jQuery(document).ready(function(){
    jQuery('#networks-sortable').sortable();
    essb_option_activate('1');
});

</script>