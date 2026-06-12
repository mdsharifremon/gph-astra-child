<?php
/**
 * Cart & Checkout Shipping Notices
 *
 * @package Astra_Child
 * @version 1.2.0
 */

defined('ABSPATH') || exit;

/**
 * Display shipping notice on Cart page.
 */
add_action('woocommerce_before_cart_totals', 'gph_display_cart_shipping_notice', 10);
function gph_display_cart_shipping_notice() {
    // Only on cart page, not AJAX
    if (!is_cart() || wp_doing_ajax()) {
        return;
    }
    ?>
    <div class="gph-shipping-notice" role="region" aria-label="<?php esc_attr_e('Shipping Information', 'astra-child'); ?>">
        <h2 class="gph-shipping-notice__title">
            <?php esc_html_e('Why is shipping not included in my cost?', 'astra-child'); ?>
        </h2>
        <p class="gph-shipping-notice__description">
            <?php esc_html_e('We are unable to offer free shipping. Since each order is unique, the shipping costs associated with it are as well. All our carriers go by dimensional weight, where your cost depends on the size and weight of the box it ships in. We do our best to select the most affordable option for each order. Thank you for your understanding, and please contact us if you have any questions prior to placing your order.', 'astra-child'); ?>
        </p>
    </div>
    <?php
}

/**
 * Display shipping notice on Checkout page.
 * 
 * Uses woocommerce_checkout_before_customer_details instead of 
 * woocommerce_before_checkout_form for better compatibility.
 */
add_action('woocommerce_checkout_before_customer_details', 'gph_display_checkout_shipping_notice', 5);
function gph_display_checkout_shipping_notice() {
    // Only on checkout page, not AJAX
    if (!is_checkout() || wp_doing_ajax()) {
        return;
    }
    ?>
    <div class="gph-shipping-notice gph-shipping-notice--checkout" role="region" aria-label="<?php esc_attr_e('Shipping Information', 'astra-child'); ?>">
        <h2 class="gph-shipping-notice__title">
            <?php esc_html_e('Why is shipping not included in my cost?', 'astra-child'); ?>
        </h2>
        <p class="gph-shipping-notice__description">
            <?php esc_html_e('We are unable to offer free shipping. Since each order is unique, the shipping costs associated with it are as well. All our carriers go by dimensional weight, where your cost depends on the size and weight of the box it ships in. We do our best to select the most affordable option for each order. Thank you for your understanding, and please contact us if you have any questions prior to placing your order.', 'astra-child'); ?>
        </p>
    </div>
    <?php
}