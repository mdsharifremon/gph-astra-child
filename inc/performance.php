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
 * GPH: Add font-display: swap for Divi / Font Awesome icon fonts.
 *
 * PageSpeed flags these font files across multiple pages, not only homepage.
 * This runs globally on the frontend so Divi icon fonts do not block text/icon rendering.
 */
function gph_add_font_display_swap_overrides()
{
    if (is_admin()) {
        return;
    }
    ?>
    <style id="gph-font-display-swap">
        @font-face {
            font-family: 'ETmodules';
            src: url('/wp-content/plugins/divi-builder/core/admin/fonts/modules/base/modules.woff') format('woff');
            font-weight: normal;
            font-style: normal;
            font-display: swap;
        }

        @font-face {
            font-family: 'ETmodules';
            src: url('/wp-content/plugins/divi-builder/core/admin/fonts/modules/all/modules.woff') format('woff');
            font-weight: normal;
            font-style: normal;
            font-display: swap;
        }

        @font-face {
            font-family: 'FontAwesome';
            src: url('/wp-content/plugins/divi-builder/core/admin/fonts/fontawesome/fa-solid-900.woff2') format('woff2');
            font-weight: 900;
            font-style: normal;
            font-display: swap;
        }

        @font-face {
            font-family: 'FontAwesome';
            src: url('/wp-content/plugins/divi-builder/core/admin/fonts/fontawesome/fa-regular-400.woff2') format('woff2');
            font-weight: 400;
            font-style: normal;
            font-display: swap;
        }

        @font-face {
            font-family: 'FontAwesome';
            src: url('/wp-content/plugins/divi-builder/core/admin/fonts/fontawesome/fa-brands-400.woff2') format('woff2');
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
        $attr['width']  = '420';
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


/**
 * GPH: Critical CLS CSS for Contact page.
 *
 * Mirrors the existing Divi/Gravity Forms layout rules for the Contact page
 * so the browser has the final dimensions before external Divi CSS finishes loading.
 */
add_action('wp_head', function () {
    if (! is_page('contact-us')) {
        return;
    }
    ?>
    <style id="gph-contact-critical-cls-css">
        /* Contact page Divi row baseline */
        .et-db #et-boc .et-l .gph-contact-heading,
        .et-db #et-boc .et-l .gph-contact-main-row {
            width: 80%;
            max-width: 1080px;
            margin-left: auto;
            margin-right: auto;
            position: relative;
            box-sizing: border-box;
        }

        @media (min-width: 981px) {
            .et-db #et-boc .et-l .gph-contact-heading,
            .et-db #et-boc .et-l .gph-contact-main-row {
                padding: 2% 0;
            }
        }

        /* Heading row: Contact Us */
        .et-db #et-boc .et-l .gph-contact-heading .et_pb_heading_0 .et_pb_module_heading {
            font-size: 36px;
            font-weight: 700;
            text-transform: uppercase;
            line-height: 1em;
            text-align: center;
            margin: 0;
            padding-bottom: 10px;
            box-sizing: border-box;
        }

        .et-db #et-boc .et-l .gph-contact-heading .et_pb_heading_0 {
            margin-bottom: 2.75%;
            box-sizing: border-box;
        }

        .et-db #et-boc .et-l .gph-contact-heading .et_pb_divider_0 {
            width: 100%;
            max-width: 300px;
            height: 23px;
            margin: 0 auto 30px auto;
            box-sizing: content-box;
        }

        /* Main contact row: preserve existing Divi equal-column behavior */
        @media (min-width: 981px) {
            .et-db #et-boc .et-l .gph-contact-main-row.et_pb_equal_columns {
                display: flex;
            }

            .et-db #et-boc .et-l .gph-contact-main-row.et_pb_gutters2 > .et_pb_column_1_2 {
                width: 48.5%;
            }

            .et-db #et-boc .et-l .gph-contact-main-row.et_pb_gutters2 > .et_pb_column {
                margin-right: 3%;
            }

            .et-db #et-boc .et-l .gph-contact-main-row.et_pb_gutters2 > .et_pb_column.et-last-child {
                margin-right: 0 !important;
            }
        }

        /* Contact cards: mirror existing Divi module design */
        .et-db #et-boc .et-l .gph-contact-main-row > .et_pb_column {
            background-color: #f7f6f2;
            border-radius: 10px 10px 10px 10px;
            border-width: 1px;
            border-style: solid;
            border-color: #d8d8d8;
            padding: 30px;
            overflow: hidden;
            box-sizing: border-box;
        }

        /* Stabilize headings inside cards */
        .et-db #et-boc .et-l .gph-contact-main-row .et_pb_heading_container h2,
        .et-db #et-boc .et-l .gph-contact-main-row .et_pb_module_heading {
            margin-top: 0;
            line-height: 1.15;
            box-sizing: border-box;
        }

        /* Stabilize left-side icon blurbs */
        .et-db #et-boc .et-l .gph-contact-main-row .et_pb_blurb_content {
            min-height: 64px;
            box-sizing: border-box;
        }

        .et-db #et-boc .et-l .gph-contact-main-row .et_pb_main_blurb_image {
            width: 48px;
            min-width: 48px;
            height: 48px;
            min-height: 48px;
            box-sizing: border-box;
        }

        /* Gravity Forms: prevent hidden honeypot from creating temporary layout space */
        .et-db #et-boc .et-l .gph-contact-main-row .gform_validation_container {
            display: none !important;
            position: absolute !important;
            left: -9999px !important;
            width: 0 !important;
            height: 0 !important;
            min-height: 0 !important;
            overflow: hidden !important;
        }

        /* Gravity Forms: reserve final field dimensions */
        .et-db #et-boc .et-l .gph-contact-main-row .gfield input {
            min-height: 54px;
            box-sizing: border-box;
        }

        .et-db #et-boc .et-l .gph-contact-main-row .gfield textarea {
            height: 150px;
            min-height: 150px;
            box-sizing: border-box;
        }

        .et-db #et-boc .et-l .gph-contact-main-row .gform_footer {
            min-height: 60px;
            box-sizing: border-box;
        }

        /* Mobile: keep Divi stacking natural */
        @media (max-width: 980px) {
            .et-db #et-boc .et-l .gph-contact-heading,
            .et-db #et-boc .et-l .gph-contact-main-row {
                width: 80%;
            }

            .et-db #et-boc .et-l .gph-contact-main-row > .et_pb_column {
                width: 100%;
                margin-right: 0;
            }
        }
    </style>
    <?php
}, 1);

/**
 * GPH: Critical CLS CSS for FAQ page.
 *
 * Purpose:
 * Reserve the FAQ support/header row before delayed Divi CSS/fonts finish loading,
 * so the accordion row below does not get pushed after first paint.
 */
add_action('wp_head', function () {
    if (! is_page('faq')) {
        return;
    }
    ?>
    <style id="gph-faq-critical-cls-css">
        /*
         * FAQ top support/header row.
         * Final measured row height is ~183.875px.
         */
        .et-db #et-boc .et-l .gph-faq-support {
            width: 80% !important;
            max-width: 614px !important;
            margin-left: auto !important;
            margin-right: auto !important;
            position: relative !important;
            box-sizing: border-box !important;
            min-height: 184px !important;
            padding-top: 0 !important;
            padding-bottom: 0 !important;
        }

        /*
         * SUPPORT small heading.
         * Final box: ~614 × 20 with 10px bottom padding.
         */
        .et-db #et-boc .et-l .gph-faq-support .et_pb_heading_0 {
            margin-bottom: 10px !important;
            box-sizing: border-box !important;
        }

        .et-db #et-boc .et-l .gph-faq-support .et_pb_heading_0 .et_pb_module_heading {
            font-size: 24px !important;
            line-height: 1em !important;
            font-weight: 400 !important;
            text-transform: uppercase !important;
            text-align: center !important;
            margin: 0 !important;
            padding-bottom: 10px !important;
            box-sizing: border-box !important;
        }

        /*
         * Main FAQ heading.
         * Final box: ~614 × 36 with 10px bottom padding.
         */
        .et-db #et-boc .et-l .gph-faq-support .et_pb_heading_1 {
            margin-bottom: 10px !important;
            box-sizing: border-box !important;
        }

        .et-db #et-boc .et-l .gph-faq-support .et_pb_heading_1 .et_pb_module_heading {
            font-size: 36px !important;
            line-height: 1em !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            text-align: center !important;
            margin: 0 !important;
            padding-bottom: 10px !important;
            box-sizing: border-box !important;
        }

        /*
         * Intro text.
         * Final box: ~614 × 48.
         */
        .et-db #et-boc .et-l .gph-faq-support .et_pb_text_0 {
            font-size: 16px !important;
            line-height: 1.5em !important;
            text-align: center !important;
            margin-bottom: 0 !important;
            box-sizing: border-box !important;
        }

        .et-db #et-boc .et-l .gph-faq-support .et_pb_text_0 p {
            margin: 0 !important;
            padding-bottom: 1em !important;
            box-sizing: border-box !important;
        }

        /*
         * Divider under intro text.
         */
        .et-db #et-boc .et-l .gph-faq-support .et_pb_divider_0 {
            max-width: 180px !important;
            height: 23px !important;
            margin: 0 auto 20px auto !important;
            box-sizing: content-box !important;
        }

        .et-db #et-boc .et-l .gph-faq-support .et_pb_divider_0::before {
            content: "" !important;
            width: 100% !important;
            height: 1px !important;
            border-top: 2px solid rgba(12, 113, 195, 0.18) !important;
            position: absolute !important;
            left: 0 !important;
            top: 0 !important;
            box-sizing: border-box !important;
        }

        /*
         * FAQ accordion row baseline.
         * Keep row width stable before Divi CSS finishes.
         */
        .et-db #et-boc .et-l .gph-faq-container {
            width: 80% !important;
            max-width: 1080px !important;
            margin-left: auto !important;
            margin-right: auto !important;
            position: relative !important;
            box-sizing: border-box !important;
        }

        @media (min-width: 981px) {
            .et-db #et-boc .et-l .gph-faq-container {
                padding: 2% 0 !important;
            }
        }

        /*
         * Critical accordion state:
         * Closed answers should not take space before Divi CSS loads.
         */
        .et-db #et-boc .et-l .gph-faq-container .et_pb_toggle_close .et_pb_toggle_content {
            display: none !important;
        }

        .et-db #et-boc .et-l .gph-faq-container .et_pb_toggle_open .et_pb_toggle_content {
            display: block !important;
        }

        @media (max-width: 980px) {
            .et-db #et-boc .et-l .gph-faq-support,
            .et-db #et-boc .et-l .gph-faq-container {
                width: 80% !important;
            }

            .et-db #et-boc .et-l .gph-faq-support {
                min-height: 184px !important;
            }
        }
    </style>
    <?php
}, 1);



/**
 * GPH: Preload first visible WooCommerce product image.
 *
 * Uses featured image first. Falls back to first gallery image if needed.
 */
add_action('wp_head', function () {
    if (is_admin()) {
        return;
    }

    if (! function_exists('is_product') || ! is_product()) {
        return;
    }

    $product_id = get_queried_object_id();

    if (! $product_id || ! function_exists('wc_get_product')) {
        return;
    }

    $product = wc_get_product($product_id);

    if (! $product) {
        return;
    }

    $image_id = $product->get_image_id();

    if (! $image_id) {
        $gallery_ids = $product->get_gallery_image_ids();
        $image_id = ! empty($gallery_ids[0]) ? (int) $gallery_ids[0] : 0;
    }

    if (! $image_id) {
        return;
    }

    $image_src = wp_get_attachment_image_url($image_id, 'full');
    $srcset    = wp_get_attachment_image_srcset($image_id, 'full');
    $sizes     = wp_get_attachment_image_sizes($image_id, 'full');

    if (! $image_src) {
        return;
    }

    echo "\n" . '<link rel="preload" as="image" href="' . esc_url($image_src) . '"';

    if ($srcset) {
        echo ' imagesrcset="' . esc_attr($srcset) . '"';
    }

    if ($sizes) {
        echo ' imagesizes="' . esc_attr($sizes) . '"';
    }

    echo ' fetchpriority="high">' . "\n";

}, 5);