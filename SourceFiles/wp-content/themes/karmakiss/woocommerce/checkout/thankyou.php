<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<?php global $woocommerce; ?> 

<div id="page-header" class="checkout">
	<div class="row">
		<a href="<?php echo home_url( '/cart/' ); ?>" class="large-3 columns passed"><span>Your shopping cart</span></a>
		<a href="<?php echo home_url( '/cart/' ); ?>"class="large-3 columns passed"><span>Delivery address</span></a>
		<a href="<?php echo home_url( '/cart/' ); ?>" class="large-3 columns passed"><span>Payment and shipping</span></a>
		<a href="<?php echo home_url( '/cart/' ); ?>" class="large-3 columns passed"><span>Review and place order</span></a>
	</div>
</div> <!-- end #page-header -->

<div id="main" class="row">
	
	<div id="content" class="large-12 columns" role="main">
		
		<div id="thank-you" class="border-block">
		
		<?php if ($order) : ?>
		
		<?php if (in_array($order->status, array('failed'))) : ?>
		
		<p><?php _e('Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction.', 'yit'); ?></p>
		
		<p><?php
			if (is_user_logged_in()) :
				_e('Please attempt your purchase again or go to your account page.', 'yit');
			else :
				_e('Please attempt your purchase again.', 'yit');
			endif;
		?></p>
		
		<p>
			<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="button pay"><?php _e('Pay', 'yit') ?></a>
			<?php if (is_user_logged_in()) : ?>
			<a href="<?php echo esc_url( get_permalink(wc_get_page_id('myaccount')) ); ?>" class="button pay"><?php _e('My Account', 'yit'); ?></a>
			<?php endif; ?>
		</p>
		
		<?php else : ?>
		
		<h2>Thank you for buying at Karma Kiss</h2>
		<p>Your transaction has been completed successfully.</p>
		<p>Your order Number is <strong><?php echo $order->get_order_number(); ?></strong>. Orders are usually processed and shipped within 24 hours.</p>
		<div class="row">
		<div class="large-6 columns">
			<div class="panel">
				<p>We have sent you a copy of your receipt via e-mail.</p>
				<p>You may also view and print copy of your receipt here</p>
			</div>
		</div>
		
		<div class="large-6 columns">
			<div class="panel">
			<p>As soon as your order ships, we will notify you via e-mail and provide you with a tracking number. You can also track your packages, review your orders, print invoices and more from <a href="<?php echo home_url( '/my-account/' ); ?>">My Account</a> page at any time.</p>
			</div>
		</div>
		</div>
        <?php /*
        <table class="shop_table thankyou">
        	<thead>
            	<tr>
                	<th><?php _e('Order:', 'yit'); ?></th>
                    <th><?php _e('Date:', 'yit'); ?></th>
                    <th><?php _e('Total:', 'yit'); ?></th>
                    <?php if ($order->payment_method_title) : ?>
                    <th><?php _e('Payment method:', 'yit'); ?></th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
            	<tr>
                	<td><?php echo $order->get_order_number(); ?></td>
                	<td><?php echo date_i18n(get_option('date_format'), strtotime($order->order_date)); ?></td>
                	<td><?php echo $order->get_formatted_order_total(); ?></td>
                    <?php if ($order->payment_method_title) : ?>
                	<td><?php echo $order->payment_method_title; ?></td>
                    <?php endif; ?>
                </tr>
            </tbody>
        </table>
		<div class="clear"></div>
        
			<ul class="order_details">
				<li class="order">
					<?php _e('Order:', 'yit'); ?>
					<strong><?php echo $order->get_order_number(); ?></strong>
				</li>
				<li class="date">
					<?php _e('Date:', 'yit'); ?>
					<strong><?php echo date_i18n(get_option('date_format'), strtotime($order->order_date)); ?></strong>
				</li>
				<li class="total">
					<?php _e('Total:', 'yit'); ?>
					<strong><?php echo $order->get_formatted_order_total(); ?></strong>
				</li>
				<?php if ($order->payment_method_title) : ?>
				<li class="method">
					<?php _e('Payment method:', 'yit'); ?>
					<strong><?php
						echo $order->payment_method_title;
					?></strong>
				</li>
				<?php endif; ?>
			</ul>
			<div class="clear"></div>
			*/
		?>
		
		<?php endif; ?>
		
		<?php /*
		<?php do_action( 'woocommerce_thankyou_' . $order->payment_method, $order->id ); ?>
		<?php do_action( 'woocommerce_thankyou', $order->id ); ?> */ ?>
		
		<?php else : ?>
		
		<p><?php _e('Thank you. Your order has been received.', 'yit'); ?></p>
		
		<?php endif; ?>
		
		</div>
		
	</div> <!-- end #content -->

</did> <!-- end #main -->