<?php
/*
Plugin Name: WooCommerce Empty Cart Redirect
Description: Redirects users to the shop page when the cart is empty and they click on the cart.
Version: 1.1
Author: Joel Gratcyk
Author URI: https://joel.gr
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class WC_Empty_Cart_Redirect {

    public function __construct() {
        // Add settings link on plugin page
        add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'add_settings_link'));

        // Add admin menu
        add_action('admin_menu', array($this, 'add_admin_menu'));

        // Initialize settings
        add_action('admin_init', array($this, 'initialize_settings'));

        // Add redirection logic
        add_filter('woocommerce_add_to_cart_fragments', array($this, 'redirect_if_cart_empty'));
    }

    // Add settings link on plugin page
    public function add_settings_link($links) {
        $settings_link = '<a href="admin.php?page=wc-empty-cart-redirect">Settings</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

    // Add admin menu
    public function add_admin_menu() {
        add_menu_page(
            'WC Empty Cart Redirect',
            'WC Empty Cart Redirect',
            'manage_options',
            'wc-empty-cart-redirect',
            array($this, 'settings_page')
        );
    }

    // Initialize settings
    public function initialize_settings() {
        register_setting('wc_empty_cart_redirect_settings', 'wc_empty_cart_redirect_options');

        add_settings_section(
            'wc_empty_cart_redirect_section',
            'WooCommerce Empty Cart Redirect Settings',
            array($this, 'settings_section_callback'),
            'wc-empty-cart-redirect'
        );

        add_settings_field(
            'redirect_url',
            'Redirect URL',
            array($this, 'redirect_url_callback'),
            'wc-empty-cart-redirect',
            'wc_empty_cart_redirect_section'
        );

        add_settings_field(
            'custom_message',
            'Custom Message',
            array($this, 'custom_message_callback'),
            'wc-empty-cart-redirect',
            'wc_empty_cart_redirect_section'
        );
    }

    // Settings section callback
    public function settings_section_callback() {
        echo '<p>Configure the settings for the WooCommerce Empty Cart Redirect plugin.</p>';
    }

    // Redirect URL callback
    public function redirect_url_callback() {
        $options = get_option('wc_empty_cart_redirect_options');
        echo '<input type="text" id="redirect_url" name="wc_empty_cart_redirect_options[redirect_url]" value="' . esc_attr($options['redirect_url'] ?? '') . '" size="50" />';
    }

    // Custom message callback
    public function custom_message_callback() {
        $options = get_option('wc_empty_cart_redirect_options');
        echo '<input type="text" id="custom_message" name="wc_empty_cart_redirect_options[custom_message]" value="' . esc_attr($options['custom_message'] ?? '') . '" size="50" />';
    }

    // Settings page
    public function settings_page() {
        ?>
        <div class="wrap">
            <h1>WooCommerce Empty Cart Redirect</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('wc_empty_cart_redirect_settings');
                do_settings_sections('wc-empty-cart-redirect');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    // Redirection logic
    public function redirect_if_cart_empty($fragments) {
        $options = get_option('wc_empty_cart_redirect_options');
        $redirect_url = !empty($options['redirect_url']) ? esc_url($options['redirect_url']) : wc_get_page_permalink('shop');
        $custom_message = !empty($options['custom_message']) ? esc_html($options['custom_message']) : __('Cart is empty', 'woocommerce');

        if (WC()->cart->is_empty()) {
            $fragments['a.cart-contents'] = '<a href="' . $redirect_url . '" class="cart-contents" title="' . __('View your shopping cart', 'woocommerce') . '">' . $custom_message . '</a>';
        }
        return $fragments;
    }
}

new WC_Empty_Cart_Redirect();
