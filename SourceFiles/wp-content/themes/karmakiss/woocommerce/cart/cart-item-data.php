<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php foreach ( $item_data as $data ) :	$key = sanitize_text_field( $data['key'] ); ?>
	<p class="variation"><?php echo wp_kses_post( $data['key'] ); ?>: <?php echo wp_kses_post( $data['value'] ); ?></p>
<?php endforeach; ?>
