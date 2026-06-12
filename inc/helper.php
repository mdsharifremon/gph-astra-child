<?php
/**
 * Astra Child: Helpers
 *
 * @package Astra_Child
 */

defined('ABSPATH') || exit;

/**
 * Determine if current request is within WooCommerce context.
 * (Includes catalog + single + transactional pages.)
 */
function gph_is_woo_context(): bool
{
    if (!function_exists('is_woocommerce')) {
        return false;
    }

    return (
        is_shop() ||
        is_product_taxonomy() ||
        is_product() ||
        is_cart() ||
        is_checkout() ||
        is_account_page()
    );
}

/**
 * Determine if current request is catalog-like (shop/archive/single/search)
 * excluding transactional/account.
 */
function gph_is_catalog_context(): bool
{
    if (!function_exists('is_woocommerce')) {
        return false;
    }

    // Product search results (covers ?s=...&post_type=product).
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    $post_type_param = isset($_GET['post_type']) ? sanitize_text_field(wp_unslash($_GET['post_type'])) : '';

    $is_product_search = is_search() && (
        get_query_var('post_type') === 'product' ||
        $post_type_param === 'product'
    );

    return (
        (is_woocommerce() || is_product_taxonomy() || $is_product_search) &&
        !is_cart() &&
        !is_checkout() &&
        !is_account_page()
    );
}


/**
 * Determine if current request is a How-To page.
 * Matches: /how-to/page-name or /how-to-videos/
 */
function gph_is_howto_context(): bool
{
    // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
    $uri = isset($_SERVER['REQUEST_URI']) ? wp_unslash($_SERVER['REQUEST_URI']) : '';

    return (
        strpos($uri, '/how-to/') !== false ||
        strpos($uri, '/how-to-videos/') !== false
    );
}