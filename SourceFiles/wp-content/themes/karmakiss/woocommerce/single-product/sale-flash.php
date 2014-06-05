<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php global $post, $product; ?>
<?php if ( $product -> is_on_sale() ) : ?>
	<span id="on-sale">On sale</span>
<?php endif; ?>