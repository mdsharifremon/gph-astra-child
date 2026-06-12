<?php
/**
 * Astra Child: Performance Optimizations
 *
 * @package Astra_Child
 */

defined('ABSPATH') || exit;

/**
 * Check whether current page is a static/non-commerce page
 * where WooCommerce cart fragments are not needed.
 */
function gph_child_is_static_non_commerce_page(): bool
{
    if (is_admin()) {
        return false;
    }

    // Never affect WooCommerce-critical pages.
    if (function_exists('is_cart')) {
        if (
            is_cart() ||
            is_checkout() ||
            is_account_page() ||
            is_product() ||
            is_shop() ||
            is_product_category() ||
            is_product_tag()
        ) {
            return false;
        }
    }

    // Product search results should stay WooCommerce-aware.
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    $post_type_param = isset($_GET['post_type']) ? sanitize_text_field(wp_unslash($_GET['post_type'])) : '';

    if (is_search() && (get_query_var('post_type') === 'product' || $post_type_param === 'product')) {
        return false;
    }

    // Static/non-commerce pages.
    if (is_front_page() || is_search()) {
        return true;
    }

    return is_page(array(
        'gas-pump-parts',
        'about-us',
        'contact-us',
        'faq',
        'how-to-videos',
    ));
}

/**
 * Disable WooCommerce cart fragments only on selected static pages.
 */
add_action('wp_enqueue_scripts', function () {
    if (!gph_child_is_static_non_commerce_page()) {
        return;
    }

    wp_dequeue_script('wc-cart-fragments');
    wp_deregister_script('wc-cart-fragments');
}, 9999);

/**
 * Hide Astra/WooCommerce cart count badge only on selected static pages.
 * Cart icon/link can remain visible.
 */
add_action('wp_head', function () {
    if (!gph_child_is_static_non_commerce_page()) {
        return;
    }
    ?>
    <style id="gph-static-page-cart-count-css">
        .ast-site-header-cart .count,
        .ast-site-header-cart .ast-count,
        .ast-menu-cart-count,
        .ast-cart-menu-wrap .count,
        .ast-addon-cart-wrap .count,
        .ast-header-woo-cart .count,
        .ast-header-woo-cart .ast-woo-header-cart-total,
        .ast-woo-header-cart-total {
            display: none !important;
        }
    </style>
    <?php
}, 20);