<?php
/*
Plugin Name: WooCommerce Calculate Shipping Button
Plugin URI: http://gameraiderr.com/plugins/woocommerce-calculate-shipping-button.zip
Description: Add a calculate shipping button to product pages.
Version: 1.0.0
Author: Ravish Pandey 'The Gameraiderr'
Author URI: http://gameraiderr.com
*/

/*  Copyright 2013 Terry Tsang (email: ravishpandey340@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

/*
 * GSTUDIO_WooCommerce_CalculateShipping_Button
 */

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	if ( ! class_exists( 'GSTUDIO_WooCommerce_CalculateShipping_Button' ) ) {
		class GSTUDIO_WooCommerce_CalculateShipping_Button{
			var $plugin_url;
			var $app_name = 'gstudio_calculateshipping';
			var $options;
			var $key;
					
			function __construct()
			{
				$this->plugin_url = trailingslashit(plugins_url(null,__FILE__));
				$this->key = 'gstudio_calculateshipping';
				$this->button_aligns = array('left' => 'Left', 'right' => 'Right');
				 // called only after woocommerce has finished loading
				add_action( 'woocommerce_init', array( $this, 'woocommerce_loaded' ) );
				//Add product write panel
				add_action( 'woocommerce_product_write_panels', array(&$this, 'gstudio_calculateshipping_main') );
				add_action( 'woocommerce_product_write_panel_tabs', array(&$this,'gstudio_calculateshipping_tab') );
				
				//Add product meta
				add_action( 'woocommerce_process_product_meta', array(&$this, 'gstudio_calculateshipping_meta') );
				
				//Display on product page for the calculate button
				$this->options = $this->get_options();
				$option_show_after_table = $this->options['custom_show_after_title'];
				
				if( $option_show_after_table == 'yes' )
					add_action( 'woocommerce_after_single_product_summary', array(&$this, 'gstudio_calculateshipping_button' ), 8 );
				else
					add_action( 'woocommerce_after_single_product_summary', array(&$this, 'gstudio_calculateshipping_button' ), 100 );
					
				$this->options = $this->get_options();
				
				//Display setting menu under woocommerce
				add_action( 'admin_menu', array( &$this, 'add_menu_items' ) );
				
				//load stylesheet
				add_action( 'wp_enqueue_scripts', array(&$this, 'custom_plugin_stylesheet') );
				
				//Add javascript after <body> tag
				//add_action( 'init', array( &$this, 'add_afterbody_scripts' ) );
				add_action( 'wp_footer', array( &$this, 'add_afterbody_scripts' ) );
				
				//Add image_src link for facebook thumbnail generation
				//add_action( 'wp_head', array( &$this, 'add_head_imagesrc' ) );
				
				add_shortcode( 'calculateshipping', array( $this, 'gstudio_calculateshipping_button') );
				add_filter( 'widget_text', 'do_shortcode' );
			}
			
			/**
			 * Take care of anything that needs woocommerce to be loaded.  
			 * For instance, if you need access to the $woocommerce global
			 */
			public function woocommerce_loaded() {
				// ...
			}
			
			/**
			 * Load stylesheet for the page
			 */
			function custom_plugin_stylesheet() {
				wp_register_style( 'calculateshipping-stylesheet', plugins_url('/css/calculateshipping.css', __FILE__) );
				wp_enqueue_style( 'calculateshipping-stylesheet' );
			}
						
			function gstudio_calculateshipping_main()
			{
				global $post;
				$enabled_option = get_post_meta($post->ID, $this->id, true);
				$label = 'Enable';
				$description = 'Enable Calculate Shipping Button on this product?';
				
				//if the option not set for yes or no, then default is yes
				if( 'yes' != $enabled_option && 'no' != $enabled_option ):
					$enabled_option = 'yes'; 
				endif;
				
				$check_id = $this->id;
				
				?>
				<div id="calculateshipping" class="panel woocommerce_options_panel" style="display: none; ">
					<fieldset>
						<p class="form-field">
							<?php
								woocommerce_wp_checkbox(array(
									'id'		=> $check_id,
									'label'		=> __($label, $this->id_name),
									'description'	=> __($description, $this->id_name),
									'value'		=> $enabled_option
								));
							?>
							<br /><br />
							<span class="alignright" style="font-size:75%; font-weight: bold;">Calculate Shipping Extension by Ravish Pandey (The Gameraiderr) - <a target="_blank" href="http://gameraiderr.com/" title="Calculate Shipping Extension by Ravish Pandey (The Gameraiderr)">View More</a></span>
						</p>
					</fieldset>
				</div>
				<?php
			}
			
			function gstudio_calculateshipping_tab()
			{
				?>
				<li class="gstudio_calculateshipping_tab">	
					<a href="#calculateshipping"><?php _e('Calculate Shipping', $this->app_name );?></a>
				</li>
				<?php
			}
			
			function gstudio_calculateshipping_meta( $post_id )
			{
				$gstudio_calculateshipping_option = isset($_POST[$this->id]) ? 'yes' : 'no';
				update_post_meta($post_id, $this->id, $gstudio_calculateshipping_option);
			}
			
			function gstudio_calculateshipping_button()
			{
				global $post;
				$enabled_option = get_post_meta($post->ID, $this->id, true);
				
				if( $enabled_option != 'yes' && $enabled_option != 'no' ):
					$enabled_option = 'yes'; //default new products or unset value to true
				endif;
				
				$this->options = $this->get_options();
				$option_calculateshipping_enabled 	= $this->options['custom_calculateshipping_enabled'];
				$option_csbtn_width 	= $this->options['custom_csbtn_width'];
				$option_button_align 	= $this->options['custom_button_align'];
				$button_align_default	= 'left';
			
				
				if( $option_button_align == 'right' )
					$button_align_default = 'right';
				
					
				if( $option_calculateshipping_enabled ):
				/*?>
					<div class="calculateshipping-button-container" style="display:block;float:<?php echo $button_align_default; ?>;">
						<div class="calculateshipping-button"><div class="cs-btn" data-href="<?php the_permalink() ?>" data-layout="button_count" data-width="<?php echo $option_csbtn_width; ?>" data-show-faces="false">
							<input type="button" name="" value="<?php _e('Calculate Shipping', $this->app_name );?>" class="shipping-calculator-button" />
						 </div></div>
					</div>
					<?php woocommerce_shipping_calculator(); ?>
				<?php*/
							include('shipping-calculator.php');
				endif;
			}
			
			function add_menu_items() {
				$wc_page = 'woocommerce';
				$comparable_settings_page = add_submenu_page( $wc_page , __( 'CaclucateShpping Setting', 'calculate-shipping' ), __( 'CaclucateShpping Setting', 'calculate-shipping' ), 'manage_options', 'cs-settings', array(
					&$this,
					'options_page'
				));
	
				//$image = $this->plugin_url . '/assets/images/icon.png';
				/*add_menu_page( __( 'FbShareLike', 'facebook-sharelike' ), __( 'FbShareLike', 'facebook-sharelike' ), 'manage_options', 'fbshare_settings', array(
					&$this,
					'options_page'
				), $image);*/
			}
			
			//start to include any script after <body> tag
			function add_afterbody_scripts()
			{
				
			}
			
			function options_page() 
			{ 
				// If form was submitted
				if ( isset( $_POST['submitted'] ) ) 
				{			
					check_admin_referer( 'calculate-shipping' );
					
					$this->options['custom_calculateshipping_enabled'] = ! isset( $_POST['custom_calculateshipping_enabled'] ) ? '' : $_POST['custom_calculateshipping_enabled'];
					$this->options['custom_csbtn_width'] = ! isset( $_POST['custom_csbtn_width'] ) ? '450' : $_POST['custom_csbtn_width'];
					$this->options['custom_button_align'] = ! isset( $_POST['custom_button_align'] ) ? 'left' : $_POST['custom_button_align'];
					
					update_option( $this->key, $this->options );
					
					// Show message
					echo '<div id="message" class="updated fade"><p>' . __( 'Calculate Shipping options saved.', 'calculate-shipping' ) . '</p></div>';
				} 
				
				$custom_calculateshipping_enabled = $this->options['custom_calculateshipping_enabled'];
				$custom_csbtn_width 		= $this->options['custom_csbtn_width'];
				$custom_button_align 		= $this->options['custom_button_align'];
					
				$checked_value2 = '';
				if($custom_calculateshipping_enabled == 'yes')
					$checked_value2 = 'checked="checked"';
					
				if($custom_csbtn_width == '')
					$custom_csbtn_width = '450';
				
				if($custom_button_align == '')
					$custom_button_align = 'left';
				
				global $wp_version;
			
				$imgpath = $this->plugin_url.'/assets/images/';
				$actionurl = $_SERVER['REQUEST_URI'];
				$nonce = wp_create_nonce( 'calculate-shipping' );
				
				$this->options = $this->get_options();
						
				// Configuration Page
						
				?>
				<div id="icon-options-general" class="icon32"></div>
				<h3><?php _e( 'CalculateShipping Options', 'calculate-shipping' ); ?></h3>
				
				
				<table width="90%" cellspacing="2">
				<tr>
					<td width="70%">
						<form action="<?php echo $actionurl; ?>" method="post">
						<table class="widefat fixed" cellspacing="0">
								<thead>
									<th width="30%">Option</th>
									<th>Setting</th>
								</thead>
								<tbody>
									<tr>
										<td>Enabled</td>
										<td><input class="checkbox" name="custom_calculateshipping_enabled" id="custom_calculateshipping_enabled" value="yes" <?php echo $checked_value2; ?> type="checkbox"></td>
									</tr>
									<tr>
										<td>Width</td>
										<td><input id="custom_csbtn_width" name="custom_csbtn_width" value="<?php echo $custom_csbtn_width; ?>" size="20"/></td>
									</tr>
									<tr>
										<td>Button Alignment</td>
										<td>
											<select name="custom_button_align">
											<?php foreach($this->button_aligns as $align_option => $button_align): ?>
												<?php if($align_option == $custom_button_align): ?>
													<option selected="selected" value="<?php echo $align_option; ?>"><?php echo $button_align; ?></option>
												<?php else: ?>
													<option value="<?php echo $align_option; ?>"><?php echo $button_align; ?></option>
												<?php endif; ?>
											<?php endforeach; ?>
											</select>
										</td>
									</tr>
									<tr>
										<td colspan="2">
											<input class="button-primary" type="submit" name="Save" value="<?php _e('Save Options'); ?>" id="submitbutton" />
											<input type="hidden" name="submitted" value="1" /> 
											<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo $nonce; ?>" />
										</td>
									</tr>
								
								</tbody>
						</table>
						</form>
					
					</td>
					
					<td width="30%" style="background:#ececec;padding:10px 5px;" valign="top">
						<p><b>WooCommerce Calculate Shipping Button</b> is a free woocommerce plugin developed by <a href="http://www.gameraiderr.com" target="_blank" title="Ravish Pandey The Gameraiderr - a php,wordpress and hybrid apps developer">Ravish Pandey (The Gameraiderr)</a>. I have spent a lot of time to improve and writing this.</p>
						
						<?php
							$get_pro_image = $this->plugin_url . '/assets/images/get-social-buttons-pro.png';
						?>
						
						<h3>Get More Extensions</h3>
						
						<p>Vist <a href="http://gameraiderr.com" target="_blank" title="Premium &amp; Free Extensions/Plugins for E-Commerce by Ravish Pandey">My Shop</a> to get more free and premium extensions/plugins for your ecommerce platform.</p>
						
						<h3>Spreading the Word</h3>
	
						<h3>Thank you for your support!</h3>
					</td>
					
				</tr>
				</table>
				
				
				<br />
				
				<?php
			}
			
			// Handle our options
			function get_options() {
				$options = array(
					'custom_show_after_title' => '',
				);
				$saved = get_option( $this->key );
				
				if ( ! empty( $saved ) ) {
					foreach ( $saved as $key => $option ) {
						$options[$key] = $option;
					}
				}
					  
				if ( $saved != $options ) {
					update_option( $this->key, $options );
				}
				
				return $options;
			}
		
		}
	}
	
	
	// finally instantiate the plugin class
	$GSTUDIO_WooCommerce_CalculateShipping_Button = &new GSTUDIO_WooCommerce_CalculateShipping_Button();
	
	function calculateshipping() {
		$woo_calculateshipping = new GSTUDIO_WooCommerce_CalculateShipping_Button();
		
		add_action('init', $woo_calculateshipping->gstudio_calculateshipping_button());
	}
}
?>