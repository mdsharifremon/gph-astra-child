<?php
/**
 * Astra Child: Constants
 *
 * @package Astra_Child
 */

defined('ABSPATH') || exit;

/**
 * Theme Version (from style.css)
 */
$theme = wp_get_theme();
define('GPH_CHILD_VERSION', $theme->get('Version'));

/**
 * Theme Paths & URLs
 */
define('GPH_CHILD_PATH', trailingslashit(get_stylesheet_directory()));
define('GPH_CHILD_URI', trailingslashit(get_stylesheet_directory_uri()));