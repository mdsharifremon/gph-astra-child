<?php

/**
 * Astra Child: Assets Enqueue
 *
 * @package Astra_Child
 */

defined('ABSPATH') || exit;

/**
 * Enqueue theme styles and scripts.
 */
add_action('wp_enqueue_scripts', 'gph_child_enqueue_assets', 15);
function gph_child_enqueue_assets()
{

    /* ---------- Global Fonts ---------- */
    $fonts_rel = 'assets/css/fonts.css';
    $fonts_abs = GPH_CHILD_PATH . $fonts_rel;
    $fonts_url = GPH_CHILD_URI . $fonts_rel;

    wp_enqueue_style(
        'gph-fonts',
        $fonts_url,
        array(),
        file_exists($fonts_abs) ? filemtime($fonts_abs) : GPH_CHILD_VERSION,
        'all'
    );

    /* ---------- Child Stylesheet ---------- */
    $style_rel = 'style.css';
    $style_abs = GPH_CHILD_PATH . $style_rel;
    $style_url = GPH_CHILD_URI . $style_rel;

    wp_enqueue_style(
        'astra-child-theme-css',
        $style_url,
        array('astra-theme-css', 'gph-fonts'),
        file_exists($style_abs) ? filemtime($style_abs) : GPH_CHILD_VERSION,
        'all'
    );

    /* ---------- Search Page Styles ---------- */
    if (is_search()) {
        $search_rel = 'assets/css/search-page.css';
        $search_abs = GPH_CHILD_PATH . $search_rel;
        $search_url = GPH_CHILD_URI . $search_rel;

        wp_enqueue_style(
            'gph-search-page',
            $search_url,
            array('astra-child-theme-css'),
            file_exists($search_abs) ? filemtime($search_abs) : GPH_CHILD_VERSION,
            'all'
        );
    }

    /* ---------- WooCommerce CSS & JS (Conditional) ---------- */
    if (function_exists('is_woocommerce')) {

        // Catalog/product-facing pages.
        if (gph_is_catalog_context()) {
            $catalog_rel = 'assets/css/woo-template-styles.css';
            $catalog_abs = GPH_CHILD_PATH . $catalog_rel;
            $catalog_url = GPH_CHILD_URI . $catalog_rel;

            wp_enqueue_style(
                'gph-woo-catalog',
                $catalog_url,
                array('astra-child-theme-css'),
                file_exists($catalog_abs) ? filemtime($catalog_abs) : GPH_CHILD_VERSION,
                'all'
            );


            // Enqueue JS for catalog pages (skeleton loader, etc.)
            $js_rel = 'assets/js/gph-woocommerce.js';
            $js_abs = GPH_CHILD_PATH . $js_rel;
            $js_url = GPH_CHILD_URI . $js_rel;

            wp_enqueue_script(
                'gph-woo-catalog-js',
                $js_url,
                array(), // No dependencies
                file_exists($js_abs) ? filemtime($js_abs) : GPH_CHILD_VERSION,
                true // Load in footer
            );
        }

        // Transactional pages.
        if (is_cart() || is_checkout() || is_account_page()) {
            $txn_rel = 'assets/css/woo-transactional.css';
            $txn_abs = GPH_CHILD_PATH . $txn_rel;
            $txn_url = GPH_CHILD_URI . $txn_rel;

            wp_enqueue_style(
                'gph-woo-transactional',
                $txn_url,
                array('astra-child-theme-css'),
                file_exists($txn_abs) ? filemtime($txn_abs) : GPH_CHILD_VERSION,
                'all'
            );
        }
    }

    /* ---------- Global JS ---------- */
    $js_rel = 'assets/js/global-script.js';
    $js_abs = GPH_CHILD_PATH . $js_rel;
    $js_url = GPH_CHILD_URI . $js_rel;

    wp_enqueue_script(
        'gph-global-js',
        $js_url,
        array(),
        file_exists($js_abs) ? filemtime($js_abs) : GPH_CHILD_VERSION,
        true
    );

  /* ---------- Homepage Hero Slider JS (Front Page Only) ---------- */
   if ( is_front_page() ) {
        $hero_js_rel = 'assets/js/gph-hero-slider.js';
        $hero_js_abs = GPH_CHILD_PATH . $hero_js_rel;
        $hero_js_url = GPH_CHILD_URI . $hero_js_rel;

        wp_enqueue_script(
            'gph-hero-slider-js',
            $hero_js_url,
            array(),
            file_exists($hero_js_abs) ? filemtime($hero_js_abs) : GPH_CHILD_VERSION,
            true // footer
        );
  }

}

/**
 * Ensure WooCommerce variable product scripts are loaded.
 * Critical for attribute selection and variation matching.
 */
add_action('wp_enqueue_scripts', 'gph_child_enqueue_woo_product_scripts', 90);
function gph_child_enqueue_woo_product_scripts()
{

    if (!function_exists('is_product')) {
        return;
    }

    if (is_product()) {
        wp_enqueue_script('jquery');
        wp_enqueue_script('wc-add-to-cart-variation');
        wp_enqueue_script('wc-single-product');
    }
}

/**
 * Preload critical fonts for performance.
 */
add_action('wp_head', 'gph_child_preload_fonts', 1);
function gph_child_preload_fonts()
{

    $base_uri = GPH_CHILD_URI . 'assets/fonts';

    // Only fonts needed above-the-fold.
    $fonts_to_preload = array(
        'OpenSans-Regular.woff2',
        'meltix.regular-webfont.woff2',
    );

    foreach ($fonts_to_preload as $file) {
        printf(
            '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>%s',
            esc_url($base_uri . '/' . $file),
            "\n"
        );
    }
}

/**
 * Disable Astra's Google Fonts (we use local fonts only).
 */
add_filter('astra_google_fonts', '__return_empty_array');


