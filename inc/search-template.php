<?php

/**
 * Astra Child: Search Template Customization
 * 
 * Handles search result modifications and no-results page.
 * 
 * @package Astra_Child
 * @version 1.1.0
 */

defined('ABSPATH') || exit;

/**
 * Trim search result excerpts to 80 characters.
 *
 * @param string $excerpt The excerpt.
 * @return string Modified excerpt.
 */
add_filter('get_the_excerpt', 'gph_trim_search_excerpt', 20);
function gph_trim_search_excerpt($excerpt)
{
    // Only apply on search pages
    if (!is_search()) {
        return $excerpt;
    }

    $text = wp_strip_all_tags($excerpt);
    $limit = 80;

    if (mb_strlen($text) > $limit) {
        $text = mb_substr($text, 0, $limit) . '…';
    }

    return $text;
}

/**
 * Add "View Details" button to search result cards.
 */
add_action('astra_entry_content_after', 'gph_add_search_card_button', 18);
function gph_add_search_card_button()
{
    // Only apply on search pages
    if (!is_search()) {
        return;
    }

    printf(
        '<div class="gph-card-footer"><a class="button btn gph-card-btn" href="%s">%s</a></div>',
        esc_url(get_permalink()),
        esc_html__('View Details', 'astra-child')
    );
}

/**
 * Display professional no-results message.
 * 
 * Shows helpful tips, search box, and contact options.
 */
add_action('astra_entry_content_404_page', 'gph_search_no_results', 10);
function gph_search_no_results()
{
    // Only on search pages with no results
    if (!is_search() || have_posts()) {
        return;
    }

    $search_query = get_search_query();
?>

    <div class="gph-no-results">

        <!-- Icon -->
        <div class="gph-no-results__icon" aria-hidden="true">
            🔍
        </div>

        <!-- Main Message -->
        <h1 class="gph-no-results__title">
            <?php
            printf(
                esc_html__('We\'re sorry. There are no results for "%s"', 'astra-child'),
                '<strong>' . esc_html($search_query) . '</strong>'
            );
            ?>
        </h1>

        <!-- Helpful Tips -->
        <div class="gph-no-results__tips">
            <p><?php esc_html_e('Try using broader search words, fewer keywords, or double-checking your spelling.', 'astra-child'); ?></p>
            <p>
                <?php esc_html_e('You can also enter the product catalog item number.', 'astra-child'); ?>
                <span class="gph-tooltip">
                    <span class="gph-tooltip__icon" aria-label="<?php esc_attr_e('Help', 'astra-child'); ?>">ⓘ</span>
                    <span class="gph-tooltip__text">
                        <?php esc_html_e('Item numbers are shown on each product page (e.g., B150-PIA)', 'astra-child'); ?>
                    </span>
                </span>
            </p>
        </div>

        <!-- Search Again -->
        <div class="gph-no-results__search">
            <?php get_search_form(); ?>
        </div>

        <!-- Help Section -->
        <div class="gph-no-results__help">
            <h2><?php esc_html_e('Still can\'t find what you need?', 'astra-child'); ?></h2>
            <div class="gph-help-options">

                <a href="<?php echo esc_url(get_permalink(get_page_by_path('contact'))); ?>" class="gph-help-option">
                    <strong><?php esc_html_e('Visit our Contact Page', 'astra-child'); ?></strong>
                    <span><?php esc_html_e('to email or call us', 'astra-child'); ?></span>
                </a>

                <span class="gph-help-separator" aria-hidden="true">|</span>

                <a href="tel:4025921710" class="gph-help-option">
                    <strong><?php esc_html_e('Ask Our Experts', 'astra-child'); ?></strong>
                    <span><?php esc_html_e('Mon – Fri  8a - 11p PT', 'astra-child'); ?></span>
                </a>

            </div>
        </div>

        <?php
        // Optional: Show popular categories
        $show_categories = apply_filters('gph_search_show_categories', true);

        if ($show_categories) {
            gph_render_popular_categories();
        }
        ?>

    </div>

<?php
}

/**
 * Render popular categories section.
 * 
 * Shows top 6 product categories by count.
 */
function gph_render_popular_categories()
{
    $categories = get_terms(array(
        'taxonomy' => 'product_cat',
        'number' => 6,
        'orderby' => 'count',
        'order' => 'DESC',
        'hide_empty' => true,
    ));

    if (empty($categories) || is_wp_error($categories)) {
        return;
    }
?>

    <div class="gph-no-results__categories">
        <h2><?php esc_html_e('Browse Popular Categories', 'astra-child'); ?></h2>
        <div class="gph-category-grid">
            <?php foreach ($categories as $category) :
                $thumbnail_id = get_term_meta($category->term_id, 'thumbnail_id', true);
                $image_url = $thumbnail_id ? wp_get_attachment_url($thumbnail_id) : wc_placeholder_img_src();
            ?>
                <a href="<?php echo esc_url(get_term_link($category)); ?>" class="gph-category-card">
                    <div class="gph-category-card__image">
                        <img src="<?php echo esc_url($image_url); ?>"
                            alt="<?php echo esc_attr($category->name); ?>"
                            loading="lazy">
                    </div>
                    <h3 class="gph-category-card__title">
                        <?php echo esc_html($category->name); ?>
                    </h3>
                    <span class="gph-category-card__count">
                        <?php printf(_n('%s product', '%s products', $category->count, 'astra-child'), number_format_i18n($category->count)); ?>
                    </span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

<?php
}
