<?php
/**
 * WooCommerce: Category Card Customization
 */

defined('ABSPATH') || exit;

add_action('init', 'gph_replace_category_title_template');
function gph_replace_category_title_template()
{
    remove_action('woocommerce_shop_loop_subcategory_title', 'woocommerce_template_loop_category_title', 10);
    add_action('woocommerce_shop_loop_subcategory_title', 'gph_template_loop_category_title_custom', 10);
}


function gph_template_loop_category_title_custom($category)
{

    if (!$category instanceof WP_Term) {
        return;
    }

    $count_html = '';
    if ($category->count > 0) {
        $count_html = apply_filters(
            'woocommerce_subcategory_count_html',
            ' <mark class="gph-count">' . sprintf(
                _n('%s product', '%s products', $category->count, 'woocommerce'),
                number_format_i18n($category->count)
            ) . '</mark>',
            $category
        );
    }

    printf(
        '<h2 class="woocommerce-loop-category__title"><span class="gph-cat-title">%s</span>%s<span class="gph-view-series">%s</span></h2>',
        esc_html($category->name),
        wp_kses_post($count_html),
        esc_html__('View series', 'astra-child')
    );
}



/**
 * Featured subcategories on Signs archive (hardcoded 2)
 */
function gph_featured_sign_subcategories_section() {
    if ( ! is_product_category('signs') ) return;

    $slugs = [
        'square-rectangular-and-other-pump-signs',
        '12-inches-round-signs',
    ];

    $terms = [];
    foreach ($slugs as $slug) {
        $t = get_term_by('slug', $slug, 'product_cat');
        if ( $t && ! is_wp_error($t) ) {
            $terms[] = $t;
        }
    }

    if ( empty($terms) ) return;

    echo '<section class="gph-featured-items">';
    echo '<h2 class="gph-featured-title">FEATURED ITEMS</h2>';
    echo '<ul class="products columns-4 gph-featured-grid">';

    foreach ($terms as $category) {
        // This uses the same Woo markup your grid uses
        woocommerce_get_template('content-product_cat.php', [ 'category' => $category ]);
    }

    echo '</ul>';
    echo '</section>';
}

// This is the correct hook point for archive pages (if the template calls it)
add_action('woocommerce_before_shop_loop', 'gph_featured_sign_subcategories_section', 5);



/**
 * Remove featured subcategories from default Woo loop
 */
add_filter('woocommerce_product_subcategories_args', function ($args) {

    if ( ! is_product_category('signs') ) {
        return $args;
    }

    // Exclude featured subcategory slugs
    $featured_slugs = [
        'square-rectangular-and-other-pump-signs',
        '12-inches-round-signs',
    ];

    $exclude_ids = [];

    foreach ($featured_slugs as $slug) {
        $term = get_term_by('slug', $slug, 'product_cat');
        if ($term && ! is_wp_error($term)) {
            $exclude_ids[] = $term->term_id;
        }
    }

    if (!empty($exclude_ids)) {
        $args['exclude'] = $exclude_ids;
    }

    return $args;

});






