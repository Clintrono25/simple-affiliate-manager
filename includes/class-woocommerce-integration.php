<?php
if (!defined('ABSPATH')) exit;

// Hook into WooCommerce before the order is created
add_action('woocommerce_checkout_create_order', function($order, $data) {
    // Example: get affiliate username from a cookie
    if (!empty($_COOKIE['sam_affiliate_ref'])) {
        $affiliate_username = sanitize_text_field($_COOKIE['sam_affiliate_ref']);
        if ($affiliate_username) {
            $order->update_meta_data('sam_affiliate_ref', $affiliate_username);
        }
    }
}, 10, 2);