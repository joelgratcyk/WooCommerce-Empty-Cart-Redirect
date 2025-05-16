<?php
/*
Plugin Name: WooCommerce Empty Cart Redirect
Description: Redirects users to the shop page when the cart is empty and they click on the cart.
Version: 1.0
Author: Joel Gratcyk
Author URI: https://joel.gr 
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

add_filter('woocommerce_add_to_cart_fragments', 'redirect_if_cart_empty');

function redirect_if_cart_empty($fragments) {
    if (WC()->cart->is_empty()) {
        $fragments['a.cart-contents'] = '<a href="' . esc_url(wc_get_page_permalink('shop')) . '" class="cart-contents" title="' . __('View your shopping cart', 'woocommerce') . '">' . __('Cart is empty', 'woocommerce') . '</a>';
    }
    return $fragments;
}
