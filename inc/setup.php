<?php
/**
 * Astra Child: Theme Setup
 *
 * @package Astra_Child
 */

defined('ABSPATH') || exit;

/**
 * Add custom body classes for specific contexts.
 */
add_filter('body_class', 'gph_child_body_classes');
function gph_child_body_classes($classes)
{

    if (gph_is_howto_context()) {
        $classes[] = 'gph-howto';
    }

    return $classes;
}