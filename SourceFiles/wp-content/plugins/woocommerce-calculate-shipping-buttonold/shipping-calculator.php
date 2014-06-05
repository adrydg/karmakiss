<?php
/**
 * Shipping Calculator
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.8
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $woocommerce;
?>

<?php do_action( 'woocommerce_before_shipping_calculator' ); ?>

<form class="shipping_calculator" action="" method="post">

	<section class="shipping-calculator-form" id="shipping-calculator-form">

		<p>
        <?php 
			//setting up default feilds for logged in user.
			$current_cc = $woocommerce->customer->get_shipping_country();
			$current_r  = $woocommerce->customer->get_shipping_state();
			$current_ct = $woocommerce->customer->get_shipping_city();
			$shippost    = $woocommerce->customer->get_shipping_postcode(); 
			if ( is_user_logged_in() ) {
					global $current_user;
					$usmeta = get_user_meta($current_user->ID);
					if(isset($usmeta['shipping_country']) && $usmeta['shipping_country'][0] != '')
					{
						$current_cc = $usmeta['shipping_country'][0];
					}
					if(isset($usmeta['shipping_state']) && $usmeta['shipping_state'][0] != '')
					{
						$current_r = $usmeta['shipping_state'][0];
					}
					if(isset($usmeta['shipping_city']) && $usmeta['shipping_city'][0] != '')
					{
						$current_ct = $usmeta['shipping_city'][0];
					}
					if(isset($usmeta['shipping_postcode']) && $usmeta['shipping_postcode'][0] != '')
					{
						$shippost = $usmeta['shipping_postcode'][0];
					}
			}
			$states     = $woocommerce->countries->get_states( $current_cc );
		?>
			<select name="calc_shipping_country" id="calc_shipping_country" class="country_to_state" rel="calc_shipping_state">
				<option value=""><?php _e( 'Select a country&hellip;', 'woocommerce' ); ?></option>
				<?php
					foreach( $woocommerce->countries->get_allowed_countries() as $key => $value )
						echo '<option value="' . esc_attr( $key ) . '"' . selected( $current_cc, esc_attr( $key ), false ) . '>' . esc_html( $value ) . '</option>';
				?>
			</select>
		</p>

		<p>
			<?php
				// Hidden Input
				if ( is_array( $states ) && empty( $states ) ) {

					?><input type="hidden" name="calc_shipping_state" id="calc_shipping_state" placeholder="<?php _e( 'State / county', 'woocommerce' ); ?>" /><?php

				// Dropdown Input
				} elseif ( is_array( $states ) ) {

					?><span>
						<select name="calc_shipping_state" id="calc_shipping_state" placeholder="<?php _e( 'State / county', 'woocommerce' ); ?>">
							<option value=""><?php _e( 'Select a state&hellip;', 'woocommerce' ); ?></option>
							<?php
								foreach ( $states as $ckey => $cvalue )
									echo '<option value="' . esc_attr( $ckey ) . '" ' . selected( $current_r, $ckey, false ) . '>' . __( esc_html( $cvalue ), 'woocommerce' ) .'</option>';
							?>
						</select>
					</span><?php

				// Standard Input
				} else {

					?><input type="text" class="input-text" value="<?php echo esc_attr( $current_r ); ?>" placeholder="<?php _e( 'State / county', 'woocommerce' ); ?>" name="calc_shipping_state" id="calc_shipping_state" /><?php

				}
			?>
		</p>

		<?php if ( apply_filters( 'woocommerce_shipping_calculator_enable_city', false ) ) : ?>

			<p>
				<input type="text" class="input-text" value="<?php echo esc_attr( $current_ct ); ?>" placeholder="<?php _e( 'City', 'woocommerce' ); ?>" name="calc_shipping_city" id="calc_shipping_city" />
			</p>

		<?php endif; ?>

		<?php if ( apply_filters( 'woocommerce_shipping_calculator_enable_postcode', true ) ) : ?>

			<p>
				<input type="text" class="input-text" value="<?php echo esc_attr($shippost); ?>" placeholder="<?php _e( 'Postcode / Zip', 'woocommerce' ); ?>" name="calc_shipping_postcode" id="calc_shipping_postcode" />
			</p>

		<?php endif; ?>
        	<p>
            
            <?php  $available_methods = $woocommerce->shipping->load_shipping_methods(); ?>
				<select name="calc_shipping_method" id="calc_shipping_method" class="shipping_method">
				<option value=""><?php _e( 'Select a Shipping Method&hellip;', 'woocommerce' ); ?></option>
				<?php
					foreach ( $available_methods as $method )
					{
						if($method->enabled != 'no')
						{
							echo '<option value="' . esc_attr( $method->id ) . '" ' . selected( $method->id, $woocommerce->session->chosen_shipping_method, false ) . '>' . wp_kses_post( $method->method_title ) . '</option>';
						}
					}
				?>
			</select>
			</p>

		<p><button type="submit" name="calc_shipping" value="1" class="button"><?php _e( 'Calculate', 'woocommerce' ); ?></button></p>

		<?php $woocommerce->nonce_field('cart') ?>
	</section>
</form>

<?php 
	if(isset($_POST['calc_shipping']) && $_POST['calc_shipping'] == '1')
	{
		if($_POST['calc_shipping_method'] != '')
		{
			
				$all_methods = $woocommerce->shipping->load_shipping_methods();
				foreach($all_methods as $method)
				{
					if($_POST['calc_shipping_method'] == 'free_shipping')
					{
						if($method->availability == 'all')
						{
							echo "<span style='color:green'>This is Free shipping method</span>";	
							
						}else if($method->availability == 'including')
						{
							$fg = 0;	
							foreach($method->countries as $country)
							{
								if($_POST['calc_shipping_country'] == $country)
								{
									$fg = 1;	
								}
							}
							if($fg == 0)
							{
								echo "<span style='color:red'>".$method->method_title." is not available in selected country.</span>";
							}else
							{
								echo "<span style='color:green'>This is Free shipping method</span>";
							}
							
						}else
						{
							$fg = 0;	
							foreach($method->countries as $country)
							{
								if($_POST['calc_shipping_country'] == $country)
								{
									$fg = 1;	
								}
							}
							if($fg == 1)
							{
								echo "<span style='color:red'>".$method->method_title." is not available in selected country.</span>";
							}else
							{
								echo "<span style='color:green'>This is Free shipping method</span>";
							}
						}
						if($method->type == 'order')
							{
								echo "<br /><span style='color:black'>This shipping charge is for the entire order as a whole.</span>";
							}
							if($method->type == 'item')
							{
								echo "<br /><span style='color:black'>This shipping charge is for each item individually.</span>";
							}
							if($method->type == 'class')
							{
								echo "<br /><span style='color:black'>This shipping charge is for each shipping class in an order.</span>";
							}
						break;
					}else
					{
						if($method->id == $_POST['calc_shipping_method'])	
						{
							if($method->availability == 'all')
							{
									$total_cost = 0;
									if($method->fee != '')
									{
										$total_cost = $method->fee;	
									}
									if($method->cost != '')
									{
										$total_cost = $total_cost + $method->cost;	
									}
									if($method->cost_per_order != '')
									{
										$total_cost = $total_cost + $method->cost_per_order;	
									}
									if($total_cost != 0)
									{
										echo "<span style='color:green'>".$method->method_title." shipping method cost you ".$total_cost." ".get_woocommerce_currency_symbol().".</span>";
									}else
									{
										echo "<span style='color:red'>Cost is not set for ".$method->method_title." shipping method.</span>";
									}
								
							}else if($method->availability == 'including')
							{
								$fg = 0;	
								foreach($method->countries as $country)
								{
									if($_POST['calc_shipping_country'] == $country)
									{
										$fg = 1;	
									}
								}
								if($fg == 0)
								{
									echo "<span style='color:red'>".$method->method_title." is not available in selected country.</span>";
								}else
								{
									$total_cost = 0;
									if($method->fee != '')
									{
										$total_cost = $method->fee;	
									}
									if($method->cost != '')
									{
										$total_cost = $total_cost + $method->cost;	
									}
									if($method->cost_per_order != '')
									{
										$total_cost = $total_cost + $method->cost_per_order;	
									}
									if($total_cost != 0)
									{
										echo "<span style='color:green'>".$method->method_title." shipping method cost you ".$total_cost." ".get_woocommerce_currency_symbol().".</span>";
									}else
									{
										echo "<span style='color:red'>Cost is not set for ".$method->method_title." shipping method.</span>";
									}
								}
								
							}else
							{
								$fg = 0;	
								foreach($method->countries as $country)
								{
									if($_POST['calc_shipping_country'] == $country)
									{
										$fg = 1;	
									}
								}
								if($fg == 1)
								{
									echo "<span style='color:red'>".$method->method_title." is not available in selected country.</span>";
								}else
								{
									$total_cost = 0;
									if($method->fee != '')
									{
										$total_cost = $method->fee;	
									}
									if($method->cost != '')
									{
										$total_cost = $total_cost + $method->cost;	
									}
									if($method->cost_per_order != '')
									{
										$total_cost = $total_cost + $method->cost_per_order;	
									}
									if($total_cost != 0)
									{
										echo "<span style='color:green'>".$method->method_title." shipping method cost you ".$total_cost." ".get_woocommerce_currency_symbol().".</span>";
									}else
									{
										echo "<span style='color:red'>Cost is not set for ".$method->method_title." shipping method.</span>";
									}
								}
							}
							
							if($method->type == 'order')
							{
								echo "<br /><span style='color:black'>This shipping charge is for the entire order as a whole.</span>";
							}
							if($method->type == 'item')
							{
								echo "<br /><span style='color:black'>This shipping charge is for each item individually.</span>";
							}
							if($method->type == 'class')
							{
								echo "<br /><span style='color:black'>This shipping charge is for each shipping class in an order.</span>";
							}


						}
					}
				}
		}else
		{
			echo "<span style='color:red'>Invalid Shipping Method</span>";	
		}			
	}

?>

<?php do_action( 'woocommerce_after_shipping_calculator' ); ?>