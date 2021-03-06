<?php
/**
 * Empty cart page
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

?>

<div class="cart-empty">
	<div class="cart-empty-message">

	    <p><?php _e( 'Your cart is currently <strong>empty</strong>.', 'yit' ) ?></p>
	    
	    <?php do_action('woocommerce_cart_is_empty'); ?>
	    
	   	<div class="empty-button"><p><a class="red-button" href="<?php echo get_permalink(woocommerce_get_page_id('shop')); ?>"><?php _e( 'Return To Shop', 'yit' ) ?></a></p></div>
	</div>
</div>