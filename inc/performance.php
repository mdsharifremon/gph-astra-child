<?php

/**
 * Astra Child: Performance Optimizations
 *
 * @package Astra_Child
 */

defined('ABSPATH') || exit;

/**
 * Preload homepage Divi hero slider LCP image.
 *
 * The Divi slider uses background images, so we cannot add fetchpriority
 * directly to an <img> tag. Preloading the first visible slide image helps
 * the browser discover the LCP image earlier.
 */
function gph_preload_homepage_lcp_image()
{
    if (! is_front_page()) {
        return;
    }

    $lcp_image_url = 'https://www.gaspumpheaven.com/wp-content/uploads/2026/06/yqq0q3izn02z2yvk8pmn-1.webp';

    echo '<link rel="preload" as="image" href="' . esc_url($lcp_image_url) . '" type="image/webp" fetchpriority="high">' . "\n";
}
add_action('wp_head', 'gph_preload_homepage_lcp_image', 1);

/**
 * Add font-display for Divi / Font Awesome icon fonts flagged by PageSpeed.
 *
 * Keep this late so it can override original @font-face declarations.
 */
function gph_add_font_display_swap_overrides()
{
    if (! is_front_page()) {
        return;
    }
?>
    <style id="gph-font-display-swap">
        @font-face {
            font-family: 'ETmodules';
            src: url('https://www.gaspumpheaven.com/wp-content/plugins/divi-builder/core/admin/fonts/modules/base/modules.woff') format('woff');
            font-weight: normal;
            font-style: normal;
            font-display: swap;
        }

        @font-face {
            font-family: 'FontAwesome';
            src: url('https://www.gaspumpheaven.com/wp-content/plugins/divi-builder/core/admin/fonts/fontawesome/fa-solid-900.woff2') format('woff2');
            font-weight: 900;
            font-style: normal;
            font-display: swap;
        }

        @font-face {
            font-family: 'FontAwesome';
            src: url('https://www.gaspumpheaven.com/wp-content/plugins/divi-builder/core/admin/fonts/fontawesome/fa-brands-400.woff2') format('woff2');
            font-weight: 400;
            font-style: normal;
            font-display: swap;
        }
    </style>
<?php
}
add_action('wp_head', 'gph_add_font_display_swap_overrides', 999);

/**
 * GPH: Prioritize header logo image.
 *
 * The site logo is above the fold, so it should not be lazy-loaded.
 * This improves logo-related CLS/LCP behavior.
 */
add_filter('wp_get_attachment_image_attributes', function ($attr, $attachment, $size) {

    if (!empty($attr['class']) && strpos($attr['class'], 'custom-logo') !== false) {
        unset($attr['loading']);

        $attr['loading']       = 'eager';
        $attr['fetchpriority'] = 'high';
        $attr['decoding']      = 'sync';

        // Keep intrinsic image dimensions stable.
        $attr['width']  = '420';<?php

/**
 * Astra Child: Performance Optimizations
 *
 * @package Astra_Child
 */

defined('ABSPATH') || exit;

/* =============================================================================
 * TABLE OF CONTENTS
 * =============================================================================
 *
 * 01. Global Frontend Optimizations
 *     01.01 Header Logo Priority
 *
 * 02. Homepage Head Optimizations
 *     02.01 Hero Slider LCP Image Preload
 *     02.02 Divi / Font Awesome Font Display Overrides
 *
 * 03. Shared Detection Helpers
 *     03.01 Static Marketing Page Allowlist
 *     03.02 WooCommerce Block Detection
 *     03.03 WooCommerce Content Detection
 *     03.04 WooCommerce Asset Cleanup Gatekeeper
 *
 * 04. Multi-Page Static Marketing Optimizations
 *     04.01 Disable WooCommerce Cart Fragments
 *     04.02 Hide Header Cart Count Badge
 *     04.03 Dequeue WooCommerce / Commerce Scripts
 *     04.04 Dequeue WooCommerce Styles
 *
 * 05. Homepage-Only Optimizations
 *     05.01 Defer Selected Non-Critical Scripts
 *     05.02 Remove Frontend Admin Toolbar Scripts
 *     05.03 Remove WordPress Block Styles
 *
 * =============================================================================
 */


/* =============================================================================
 * 01. GLOBAL FRONTEND OPTIMIZATIONS
 * ============================================================================= */


/* -----------------------------------------------------------------------------
 * 01.01 Header Logo Priority
 * -------------------------------------------------------------------------- */

/**
 * GPH: Prioritize header logo image.
 *
 * The site logo is above the fold, so it should not be lazy-loaded.
 * This improves logo-related CLS/LCP behavior.
 */
add_filter('wp_get_attachment_image_attributes', function ($attr, $attachment, $size) {

    if (!empty($attr['class']) && strpos($attr['class'], 'custom-logo') !== false) {
        unset($attr['loading']);

        $attr['loading']       = 'eager';
        $attr['fetchpriority'] = 'high';
        $attr['decoding']      = 'sync';

        // Keep intrinsic image dimensions stable.
        $attr['width']  = '420';
        $attr['height'] = '52';
    }

    return $attr;

}, 20, 3);


/* =============================================================================
 * 02. HOMEPAGE HEAD OPTIMIZATIONS
 * ============================================================================= */


/* -----------------------------------------------------------------------------
 * 02.01 Hero Slider LCP Image Preload
 * -------------------------------------------------------------------------- */

/**
 * Preload homepage Divi hero slider LCP image.
 *
 * The Divi slider uses background images, so we cannot add fetchpriority
 * directly to an <img> tag. Preloading the first visible slide image helps
 * the browser discover the LCP image earlier.
 */
function gph_preload_homepage_lcp_image()
{
    if (! is_front_page()) {
        return;
    }

    $lcp_image_url = 'https://www.gaspumpheaven.com/wp-content/uploads/2026/06/yqq0q3izn02z2yvk8pmn-1.webp';

    echo '<link rel="preload" as="image" href="' . esc_url($lcp_image_url) . '" type="image/webp" fetchpriority="high">' . "\n";
}
add_action('wp_head', 'gph_preload_homepage_lcp_image', 1);


/* -----------------------------------------------------------------------------
 * 02.02 Divi / Font Awesome Font Display Overrides
 * -------------------------------------------------------------------------- */

/**
 * Add font-display for Divi / Font Awesome icon fonts flagged by PageSpeed.
 *
 * Keep this late so it can override original @font-face declarations.
 */
function gph_add_font_display_swap_overrides()
{
    if (! is_front_page()) {
        return;
    }
?>
    <style id="gph-font-display-swap">
        @font-face {
            font-family: 'ETmodules';
            src: url('https://www.gaspumpheaven.com/wp-content/plugins/divi-builder/core/admin/fonts/modules/base/modules.woff') format('woff');
            font-weight: normal;
            font-style: normal;
            font-display: swap;
        }

        @font-face {
            font-family: 'FontAwesome';
            src: url('https://www.gaspumpheaven.com/wp-content/plugins/divi-builder/core/admin/fonts/fontawesome/fa-solid-900.woff2') format('woff2');
            font-weight: 900;
            font-style: normal;
            font-display: swap;
        }

        @font-face {
            font-family: 'FontAwesome';
            src: url('https://www.gaspumpheaven.com/wp-content/plugins/divi-builder/core/admin/fonts/fontawesome/fa-brands-400.woff2') format('woff2');
            font-weight: 400;
            font-style: normal;
            font-display: swap;
        }
    </style>
<?php
}
add_action('wp_head', 'gph_add_font_display_swap_overrides', 999);


/* =============================================================================
 * 03. SHARED DETECTION HELPERS
 * ============================================================================= */


/* -----------------------------------------------------------------------------
 * 03.01 Static Marketing Page Allowlist
 * -------------------------------------------------------------------------- */

/**
 * Check whether current page is one of the known static marketing pages
 * where WooCommerce frontend interaction assets are normally not needed.
 *
 * This is intentionally an allowlist.
 * Do not replace this with a broad "non-Woo page" rule.
 */
function gph_is_static_marketing_candidate_page(): bool
{
    if (is_admin()) {
        return false;
    }

    if (is_front_page()) {
        return true;
    }

    return is_page(
        array(
            'about-us',
            'contact-us',
            'faq',
        )
    );
}


/* -----------------------------------------------------------------------------
 * 03.02 WooCommerce Block Detection
 * -------------------------------------------------------------------------- */

/**
 * Recursively detect WooCommerce blocks inside Gutenberg block content.
 */
function gph_blocks_contain_woocommerce_block(array $blocks): bool
{
    foreach ($blocks as $block) {
        $block_name = isset($block['blockName']) ? (string) $block['blockName'] : '';

        if (0 === strpos($block_name, 'woocommerce/')) {
            return true;
        }

        if (! empty($block['innerBlocks']) && is_array($block['innerBlocks'])) {
            if (gph_blocks_contain_woocommerce_block($block['innerBlocks'])) {
                return true;
            }
        }
    }

    return false;
}


/* -----------------------------------------------------------------------------
 * 03.03 WooCommerce Content Detection
 * -------------------------------------------------------------------------- */

/**
 * Detect whether the current page content contains WooCommerce content.
 *
 * Fallback protection:
 * If Home/About/Contact/FAQ later contain WooCommerce products, add-to-cart,
 * cart, checkout, product blocks, or Divi Woo modules, Woo assets will stay loaded.
 */
function gph_current_page_has_woocommerce_content(): bool
{
    if (! is_singular() && ! is_front_page()) {
        return false;
    }

    $post_id = get_queried_object_id();

    if (! $post_id) {
        return false;
    }

    $post = get_post($post_id);

    if (! $post || empty($post->post_content)) {
        return false;
    }

    $content = (string) $post->post_content;

    $woocommerce_shortcodes = array(
        'products',
        'product',
        'product_page',
        'product_category',
        'product_categories',
        'add_to_cart',
        'add_to_cart_url',
        'recent_products',
        'featured_products',
        'sale_products',
        'best_selling_products',
        'top_rated_products',
        'woocommerce_cart',
        'woocommerce_checkout',
        'woocommerce_my_account',
    );

    foreach ($woocommerce_shortcodes as $shortcode) {
        if (has_shortcode($content, $shortcode)) {
            return true;
        }
    }

    // Detect WooCommerce Gutenberg blocks.
    if (function_exists('parse_blocks')) {
        $blocks = parse_blocks($content);

        if (gph_blocks_contain_woocommerce_block($blocks)) {
            return true;
        }
    }

    // Detect Divi WooCommerce module markers and Woo block markers.
    $markers = array(
        'wp:woocommerce/',
        '<!-- wp:woocommerce/',
        'wc-block-',
        '[products',
        '[product ',
        '[product_page',
        '[product_category',
        '[add_to_cart',
        '[woocommerce_cart',
        '[woocommerce_checkout',
        'et_pb_shop',
        'et_pb_wc_',
        'et_pb_woocommerce',
    );

    foreach ($markers as $marker) {
        if (false !== strpos($content, $marker)) {
            return true;
        }
    }

    return false;
}


/* -----------------------------------------------------------------------------
 * 03.04 WooCommerce Asset Cleanup Gatekeeper
 * -------------------------------------------------------------------------- */

/**
 * Final gatekeeper for WooCommerce asset cleanup.
 *
 * Only remove Woo assets on allowlisted marketing pages.
 * If WooCommerce content is detected, keep Woo assets.
 */
function gph_can_remove_woo_assets_on_current_page(): bool
{
    if (! gph_is_static_marketing_candidate_page()) {
        return false;
    }

    if (gph_current_page_has_woocommerce_content()) {
        return false;
    }

    return true;
}


/* =============================================================================
 * 04. MULTI-PAGE STATIC MARKETING OPTIMIZATIONS
 * ============================================================================= */


/* -----------------------------------------------------------------------------
 * 04.01 Disable WooCommerce Cart Fragments
 * -------------------------------------------------------------------------- */

/**
 * Disable WooCommerce cart fragments only on safe static marketing pages.
 */
function gph_disable_cart_fragments_on_static_marketing_pages()
{
    if (! gph_can_remove_woo_assets_on_current_page()) {
        return;
    }

    wp_dequeue_script('wc-cart-fragments');
    wp_deregister_script('wc-cart-fragments');
}
add_action('wp_enqueue_scripts', 'gph_disable_cart_fragments_on_static_marketing_pages', 9999);


/* -----------------------------------------------------------------------------
 * 04.02 Hide Header Cart Count Badge
 * -------------------------------------------------------------------------- */

/**
 * Hide Astra/WooCommerce cart count badge only on safe static marketing pages.
 *
 * Cart icon/link can remain visible.
 */
function gph_hide_cart_count_on_static_marketing_pages()
{
    if (! gph_can_remove_woo_assets_on_current_page()) {
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
        .ast-woo-header-cart-total,
        .ast-site-header-cart .ast-icon-shopping-bag:after,
        .ast-header-woo-cart .ast-icon-shopping-bag:after,
        .ast-cart-menu-wrap .ast-icon-shopping-bag:after,
        .ast-builder-layout-element[data-section="section-header-woo-cart"] .ast-icon-shopping-bag:after {
            display: none !important;
            content: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
        }
    </style>
<?php
}
add_action('wp_head', 'gph_hide_cart_count_on_static_marketing_pages', 20);


/* -----------------------------------------------------------------------------
 * 04.03 Dequeue WooCommerce / Commerce Scripts
 * -------------------------------------------------------------------------- */

/**
 * Remove unused WooCommerce/plugin commerce scripts from safe static marketing pages.
 *
 * Applies only to:
 * - Homepage
 * - About
 * - Contact
 * - FAQ
 *
 * If those pages later contain WooCommerce content, this will not run.
 */
function gph_dequeue_static_marketing_page_woo_scripts()
{
    if (! gph_can_remove_woo_assets_on_current_page()) {
        return;
    }

    $script_handles = array(
        // WooCommerce frontend interaction scripts.
        'wc-add-to-cart',
        'woocommerce',
        'jquery-blockui',
        'js-cookie',
        'wc-cart-fragments',

        // Woo Discount Rules dynamic pricing scripts.
        'awdr-main',
        'awdr-dynamic-price',

        // WooCommerce attribution/session scripts.
        // If the client relies heavily on Woo attribution reports, remove these two from this array.
        'sourcebuster-js',
        'wc-order-attribution',

        // Astra cart drawer/mobile cart behavior.
        // Basic cart icon/link can remain without this script.
        'astra-mobile-cart',
    );

    foreach ($script_handles as $handle) {
        wp_dequeue_script($handle);
        wp_deregister_script($handle);
    }
}
add_action('wp_enqueue_scripts', 'gph_dequeue_static_marketing_page_woo_scripts', 9999);


/* -----------------------------------------------------------------------------
 * 04.04 Dequeue WooCommerce Styles
 * -------------------------------------------------------------------------- */

/**
 * Remove unused WooCommerce styles from safe static marketing pages.
 *
 * If WooCommerce content is detected on the page, these styles stay loaded.
 */
function gph_dequeue_static_marketing_page_woo_styles()
{
    if (! gph_can_remove_woo_assets_on_current_page()) {
        return;
    }

    $style_handles = array(
        // WooCommerce classic styles.
        'woocommerce-general',
        'woocommerce-layout',
        'woocommerce-smallscreen',
        'woocommerce-inline',

        // WooCommerce Blocks styles.
        'wc-blocks-style',
        'wc-blocks-style-css',
        'wc-block-style',
        'wc-blocks-packages-style',
        'wc-blocks-vendors-style',
    );

    foreach ($style_handles as $handle) {
        wp_dequeue_style($handle);
        wp_deregister_style($handle);
    }
}
add_action('wp_enqueue_scripts', 'gph_dequeue_static_marketing_page_woo_styles', 9999);


/* =============================================================================
 * 05. HOMEPAGE-ONLY OPTIMIZATIONS
 * ============================================================================= */


/* -----------------------------------------------------------------------------
 * 05.01 Defer Selected Non-Critical Scripts
 * -------------------------------------------------------------------------- */

/**
 * Defer selected non-critical homepage scripts.
 *
 * Scope is intentionally narrow. Do not defer jQuery, WooCommerce core,
 * checkout/payment scripts, Gravity Forms, Divi slider scripts, or discount scripts.
 */
function gph_defer_selected_homepage_scripts($tag, $handle, $src)
{
    if (is_admin() || ! is_front_page()) {
        return $tag;
    }

    $defer_handles = array(
        'google_gtagjs',
        'googlesitekit-events-provider-woocommerce',
    );

    if (! in_array($handle, $defer_handles, true)) {
        return $tag;
    }

    if (false !== strpos($tag, ' defer') || false !== strpos($tag, ' async')) {
        return $tag;
    }

    return str_replace('<script ', '<script defer ', $tag);
}
add_filter('script_loader_tag', 'gph_defer_selected_homepage_scripts', 10, 3);


/* -----------------------------------------------------------------------------
 * 05.02 Remove Frontend Admin Toolbar Scripts
 * -------------------------------------------------------------------------- */

/**
 * Remove frontend admin/plugin toolbar scripts from homepage output.
 *
 * These are not required for normal public visitors.
 * This does not affect wp-admin screens.
 */
function gph_dequeue_admin_toolbar_scripts_on_homepage()
{
    if (is_admin() || ! is_front_page()) {
        return;
    }

    $handles = array(
        'admin-bar',
        'imagify-admin-bar',
        'wpfc-toolbar',
        'googlesitekit-adminbar',
        'yoast-seo-premium-frontend-inspector',
    );

    foreach ($handles as $handle) {
        wp_dequeue_script($handle);
        wp_deregister_script($handle);
    }
}
add_action('wp_enqueue_scripts', 'gph_dequeue_admin_toolbar_scripts_on_homepage', 9999);


/* -----------------------------------------------------------------------------
 * 05.03 Remove WordPress Block Styles
 * -------------------------------------------------------------------------- */

/**
 * Remove WordPress block-library CSS from the homepage only.
 *
 * The homepage is Divi-built, but it also contains a Gravity Form.
 * Do not remove Gravity Forms assets here.
 */
function gph_dequeue_block_styles_on_homepage_only()
{
    if (is_admin() || ! is_front_page()) {
        return;
    }

    $style_handles = array(
        'wp-block-library',
        'wp-block-library-theme',
        'global-styles',
        'classic-theme-styles',
        'wp-block-image',
        'wp-block-paragraph',
        'wp-block-spacer',
    );

    foreach ($style_handles as $handle) {
        wp_dequeue_style($handle);
        wp_deregister_style($handle);
    }
}
add_action('wp_enqueue_scripts', 'gph_dequeue_block_styles_on_homepage_only', 9999);

        $attr['height'] = '52';
    }

    return $attr;

}, 20, 3);


/**
 * Check whether current page is one of the known static marketing pages
 * where WooCommerce frontend interaction assets are normally not needed.
 *
 * This is intentionally an allowlist.
 * Do not replace this with a broad "non-Woo page" rule.
 */
function gph_is_static_marketing_candidate_page(): bool
{
    if (is_admin()) {
        return false;
    }

    if (is_front_page()) {
        return true;
    }

    return is_page(
        array(
            'about-us',
            'contact-us',
            'faq',
        )
    );
}

/**
 * Recursively detect WooCommerce blocks inside Gutenberg block content.
 */
function gph_blocks_contain_woocommerce_block(array $blocks): bool
{
    foreach ($blocks as $block) {
        $block_name = isset($block['blockName']) ? (string) $block['blockName'] : '';

        if (0 === strpos($block_name, 'woocommerce/')) {
            return true;
        }

        if (! empty($block['innerBlocks']) && is_array($block['innerBlocks'])) {
            if (gph_blocks_contain_woocommerce_block($block['innerBlocks'])) {
                return true;
            }
        }
    }

    return false;
}

/**
 * Detect whether the current page content contains WooCommerce content.
 *
 * Fallback protection:
 * If Home/About/Contact/FAQ later contain WooCommerce products, add-to-cart,
 * cart, checkout, product blocks, or Divi Woo modules, Woo assets will stay loaded.
 */
function gph_current_page_has_woocommerce_content(): bool
{
    if (! is_singular() && ! is_front_page()) {
        return false;
    }

    $post_id = get_queried_object_id();

    if (! $post_id) {
        return false;
    }

    $post = get_post($post_id);

    if (! $post || empty($post->post_content)) {
        return false;
    }

    $content = (string) $post->post_content;

    $woocommerce_shortcodes = array(
        'products',
        'product',
        'product_page',
        'product_category',
        'product_categories',
        'add_to_cart',
        'add_to_cart_url',
        'recent_products',
        'featured_products',
        'sale_products',
        'best_selling_products',
        'top_rated_products',
        'woocommerce_cart',
        'woocommerce_checkout',
        'woocommerce_my_account',
    );

    foreach ($woocommerce_shortcodes as $shortcode) {
        if (has_shortcode($content, $shortcode)) {
            return true;
        }
    }

    // Detect WooCommerce Gutenberg blocks.
    if (function_exists('parse_blocks')) {
        $blocks = parse_blocks($content);

        if (gph_blocks_contain_woocommerce_block($blocks)) {
            return true;
        }
    }

    // Detect Divi WooCommerce module markers and Woo block markers.
    $markers = array(
        'wp:woocommerce/',
        '<!-- wp:woocommerce/',
        'wc-block-',
        '[products',
        '[product ',
        '[product_page',
        '[product_category',
        '[add_to_cart',
        '[woocommerce_cart',
        '[woocommerce_checkout',
        'et_pb_shop',
        'et_pb_wc_',
        'et_pb_woocommerce',
    );

    foreach ($markers as $marker) {
        if (false !== strpos($content, $marker)) {
            return true;
        }
    }

    return false;
}

/**
 * Final gatekeeper for WooCommerce asset cleanup.
 *
 * Only remove Woo assets on allowlisted marketing pages.
 * If WooCommerce content is detected, keep Woo assets.
 */
function gph_can_remove_woo_assets_on_current_page(): bool
{
    if (! gph_is_static_marketing_candidate_page()) {
        return false;
    }

    if (gph_current_page_has_woocommerce_content()) {
        return false;
    }

    return true;
}

/**
 * Disable WooCommerce cart fragments only on safe static marketing pages.
 */
function gph_disable_cart_fragments_on_static_marketing_pages()
{
    if (! gph_can_remove_woo_assets_on_current_page()) {
        return;
    }

    wp_dequeue_script('wc-cart-fragments');
    wp_deregister_script('wc-cart-fragments');
}
add_action('wp_enqueue_scripts', 'gph_disable_cart_fragments_on_static_marketing_pages', 9999);

/**
 * Hide Astra/WooCommerce cart count badge only on safe static marketing pages.
 *
 * Cart icon/link can remain visible.
 */
function gph_hide_cart_count_on_static_marketing_pages()
{
    if (! gph_can_remove_woo_assets_on_current_page()) {
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
        .ast-woo-header-cart-total,
        .ast-site-header-cart .ast-icon-shopping-bag:after,
        .ast-header-woo-cart .ast-icon-shopping-bag:after,
        .ast-cart-menu-wrap .ast-icon-shopping-bag:after,
        .ast-builder-layout-element[data-section="section-header-woo-cart"] .ast-icon-shopping-bag:after {
            display: none !important;
            content: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
        }
    </style>
<?php
}
add_action('wp_head', 'gph_hide_cart_count_on_static_marketing_pages', 20);

/**
 * Remove unused WooCommerce/plugin commerce scripts from safe static marketing pages.
 *
 * Applies only to:
 * - Homepage
 * - About
 * - Contact
 * - FAQ
 *
 * If those pages later contain WooCommerce content, this will not run.
 */
function gph_dequeue_static_marketing_page_woo_scripts()
{
    if (! gph_can_remove_woo_assets_on_current_page()) {
        return;
    }

    $script_handles = array(
        // WooCommerce frontend interaction scripts.
        'wc-add-to-cart',
        'woocommerce',
        'jquery-blockui',
        'js-cookie',
        'wc-cart-fragments',

        // Woo Discount Rules dynamic pricing scripts.
        'awdr-main',
        'awdr-dynamic-price',

        // WooCommerce attribution/session scripts.
        // If the client relies heavily on Woo attribution reports, remove these two from this array.
        'sourcebuster-js',
        'wc-order-attribution',

        // Astra cart drawer/mobile cart behavior.
        // Basic cart icon/link can remain without this script.
        'astra-mobile-cart',
    );

    foreach ($script_handles as $handle) {
        wp_dequeue_script($handle);
        wp_deregister_script($handle);
    }
}
add_action('wp_enqueue_scripts', 'gph_dequeue_static_marketing_page_woo_scripts', 9999);

/**
 * Remove unused WooCommerce styles from safe static marketing pages.
 *
 * If WooCommerce content is detected on the page, these styles stay loaded.
 */
function gph_dequeue_static_marketing_page_woo_styles()
{
    if (! gph_can_remove_woo_assets_on_current_page()) {
        return;
    }

    $style_handles = array(
        // WooCommerce classic styles.
        'woocommerce-general',
        'woocommerce-layout',
        'woocommerce-smallscreen',
        'woocommerce-inline',

        // WooCommerce Blocks styles.
        'wc-blocks-style',
        'wc-blocks-style-css',
        'wc-block-style',
        'wc-blocks-packages-style',
        'wc-blocks-vendors-style',
    );

    foreach ($style_handles as $handle) {
        wp_dequeue_style($handle);
        wp_deregister_style($handle);
    }
}
add_action('wp_enqueue_scripts', 'gph_dequeue_static_marketing_page_woo_styles', 9999);

/**
 * Defer selected non-critical homepage scripts.
 *
 * Scope is intentionally narrow. Do not defer jQuery, WooCommerce core,
 * checkout/payment scripts, Gravity Forms, Divi slider scripts, or discount scripts.
 */
function gph_defer_selected_homepage_scripts($tag, $handle, $src)
{
    if (is_admin() || ! is_front_page()) {
        return $tag;
    }

    $defer_handles = array(
        'google_gtagjs',
        'googlesitekit-events-provider-woocommerce',
    );

    if (! in_array($handle, $defer_handles, true)) {
        return $tag;
    }

    if (false !== strpos($tag, ' defer') || false !== strpos($tag, ' async')) {
        return $tag;
    }

    return str_replace('<script ', '<script defer ', $tag);
}
add_filter('script_loader_tag', 'gph_defer_selected_homepage_scripts', 10, 3);

/**
 * Remove frontend admin/plugin toolbar scripts from homepage output.
 *
 * These are not required for normal public visitors.
 * This does not affect wp-admin screens.
 */
function gph_dequeue_admin_toolbar_scripts_on_homepage()
{
    if (is_admin() || ! is_front_page()) {
        return;
    }

    $handles = array(
        'admin-bar',
        'imagify-admin-bar',
        'wpfc-toolbar',
        'googlesitekit-adminbar',
        'yoast-seo-premium-frontend-inspector',
    );

    foreach ($handles as $handle) {
        wp_dequeue_script($handle);
        wp_deregister_script($handle);
    }
}
add_action('wp_enqueue_scripts', 'gph_dequeue_admin_toolbar_scripts_on_homepage', 9999);

/**
 * Remove WordPress block-library CSS from the homepage only.
 *
 * The homepage is Divi-built, but it also contains a Gravity Form.
 * Do not remove Gravity Forms assets here.
 */
function gph_dequeue_block_styles_on_homepage_only()
{
    if (is_admin() || ! is_front_page()) {
        return;
    }

    $style_handles = array(
        'wp-block-library',
        'wp-block-library-theme',
        'global-styles',
        'classic-theme-styles',
        'wp-block-image',
        'wp-block-paragraph',
        'wp-block-spacer',
    );

    foreach ($style_handles as $handle) {
        wp_dequeue_style($handle);
        wp_deregister_style($handle);
    }
}
add_action('wp_enqueue_scripts', 'gph_dequeue_block_styles_on_homepage_only', 9999);
