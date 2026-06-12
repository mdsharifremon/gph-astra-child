<?php
/**
 * Astra Child Theme
 *
 * @package Astra_Child
 * @since   1.0.0
 */

defined('ABSPATH') || exit;

/**
 * Load order matters:
 * 1. Constants & Helpers first (other files depend on these)
 * 2. Theme setup & assets
 * 3. Feature modules (search, WooCommerce)
 */

function gph_video_products_shortcode() {

    if (!function_exists('get_field')) return '';

    $products = get_field('related_products');

    // Hide section completely if empty
    if (empty($products)) {
        return '';
    }

    ob_start();

    echo '<div class="gph-video-products">';
    echo '<h2 class="gph-video-products-title">Products in This Video</h2>';

    echo '<ul class="products columns-4">';

    foreach ($products as $product_id) {
        $post_object = get_post($product_id);
        setup_postdata($GLOBALS['post'] =& $post_object);

        wc_get_template_part('content', 'product');
    }

    wp_reset_postdata();

    echo '</ul>';
    echo '</div>';

    return ob_get_clean();
}
add_shortcode('video_products', 'gph_video_products_shortcode');



//  Core utilities
require_once get_stylesheet_directory() . '/inc/constants.php';
require_once get_stylesheet_directory() . '/inc/helper.php';

// Theme setup & assets
require_once get_stylesheet_directory() . '/inc/setup.php';
require_once get_stylesheet_directory() . '/inc/assets/enqueue.php';
require_once get_stylesheet_directory() . '/inc/assets/dequeue.php';

// Disable Divi resources everywhere apart from marketing pages
require_once get_stylesheet_directory() . '/inc/disable-divi-everywhere.php';

// Performance optimizations
require_once get_stylesheet_directory() . '/inc/performance.php';

// Search customizations
require_once get_stylesheet_directory() . '/inc/search-template.php';

//  WooCommerce UI (only if WooCommerce is active)
if (class_exists('WooCommerce')) {

	require_once get_stylesheet_directory() . '/inc/woocommerce/category-template.php';
	
}

