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
function gph_preload_homepage_lcp_image() {
	if ( ! is_front_page() ) {
		return;
	}

	$lcp_image_url = 'https://www.gaspumpheaven.com/wp-content/uploads/2026/06/yqq0q3izn02z2yvk8pmn-1.webp';

	echo '<link rel="preload" as="image" href="' . esc_url( $lcp_image_url ) . '" type="image/webp" fetchpriority="high">' . "\n";
}
add_action( 'wp_head', 'gph_preload_homepage_lcp_image', 1 );


/**
 * Add font-display for Divi/FontAwesome icon fonts flagged by PageSpeed.
 *
 * This must print late so it overrides the original @font-face declarations.
 */
function gph_add_font_display_swap_overrides() {
	?>
	<style id="gph-font-display-swap">
		@font-face {
			font-family: 'ETmodules';
			src: url('https://www.gaspumpheaven.com/wp-content/plugins/divi-builder/core/admin/fonts/modules/base/modules.woff') format('woff');
			font-weight: normal;
			font-style: normal;
			font-display: swap;
		}
    
	</style>
	<?php
}
add_action( 'wp_head', 'gph_add_font_display_swap_overrides', 999 );

/**
 * Dequeue Font Awesome completely on the homepage only.
 * Prevents LCP rendering blocks from unused icon assets.
 */
function gph_dequeue_fontawesome_on_homepage() {
    // Safety check: Never run in admin or on any page other than the homepage
    if ( is_admin() || ! is_front_page() ) {
        return;
    }
    
    // Dequeue standard handles used by Divi / Astra configurations
    wp_dequeue_style( 'font-awesome' ); 
    wp_deregister_style( 'font-awesome' );
    
    wp_dequeue_style( 'divi-fonts' ); // Common Divi style handle fallback
}
add_action( 'wp_enqueue_scripts', 'gph_dequeue_fontawesome_on_homepage', 9999 );


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
}, 20);