<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php if ( ! $messages ) return; ?>
<?php foreach ( $messages as $message ) : ?>
	<div class="row"><div class="large-12 columns"><div data-alert class="alert-box warning"><?php echo wp_kses_post( $message ); ?><a href="#" class="close">&times;</a></div></div></div>
<?php endforeach; ?>