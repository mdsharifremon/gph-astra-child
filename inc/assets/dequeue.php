<?php
/**
 * Astra Child: Assets Dequeue
 *
 * @package Astra_Child
 */

defined('ABSPATH') || exit;

/**
 * Dequeue Divi/ET assets on WooCommerce pages.
 *
 * NOTE:
 * - Only targets Divi/ET handles/patterns.
 * - Does NOT touch WooCommerce core handles.
 */
add_action('wp_enqueue_scripts', 'gph_child_dequeue_divi_on_woo', 100);
function gph_child_dequeue_divi_on_woo()
{

    if (!gph_is_woo_context()) {
        return;
    }

    // Known Divi handles (best-effort).
    $styles = array(
        'divi-style',
        'et-builder-modules-style',
        'et-core-unified',
    );

    $scripts = array(
        'jquery-fitvids',
        'waypoints',
        'magnific-popup',
        'salvattore',
        'et-builder-modules-global-functions-script',
        'et-builder-modules-script',
    );

    foreach ($styles as $handle) {
        wp_dequeue_style($handle);
        wp_deregister_style($handle);
    }

    foreach ($scripts as $handle) {
        wp_dequeue_script($handle);
        wp_deregister_script($handle);
    }

    /**
     * Pattern-based cleanup: remove any handle containing Divi/ET patterns.
     * Guardrail: never remove WooCommerce / WP core scripts.
     */
    global $wp_styles, $wp_scripts;

    $never_touch = array(
        'jquery',
        'wc-add-to-cart-variation',
        'wc-single-product',
        'woocommerce',
        'wc-cart-fragments',
        'wc-checkout',
        'wc-add-to-cart',
        'wc-cart',
    );

    if ($wp_styles instanceof WP_Styles) {
        foreach ((array) $wp_styles->queue as $handle) {
            if (in_array($handle, $never_touch, true)) {
                continue;
            }
            if (gph_child_is_divi_handle($handle)) {
                wp_dequeue_style($handle);
                wp_deregister_style($handle);
            }
        }
    }

    if ($wp_scripts instanceof WP_Scripts) {
        foreach ((array) $wp_scripts->queue as $handle) {
            if (in_array($handle, $never_touch, true)) {
                continue;
            }
            if (gph_child_is_divi_handle($handle)) {
                wp_dequeue_script($handle);
                wp_deregister_script($handle);
            }
        }
    }
}

/**
 * Check if a handle is Divi-related.
 *
 * @param string $handle Asset handle.
 * @return bool
 */
function gph_child_is_divi_handle($handle)
{
    $patterns = array('et-', 'divi', 'elegant');

    foreach ($patterns as $pattern) {
        if (stripos($handle, $pattern) !== false) {
            return true;
        }
    }

    return false;
}

/**
 * Prevent Divi Builder framework from loading on WooCommerce pages.
 * Big performance win: reduces runtime overhead.
 */
add_filter('et_builder_should_load_framework', 'gph_child_disable_divi_on_woo');
function gph_child_disable_divi_on_woo($load)
{

    if (gph_is_woo_context()) {
        return false;
    }

    return $load;
}