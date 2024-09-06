<?php
/**
 * Plugin Name: Gold Rate Plugin
 * Description: A plugin to manage gold rates.
 * Version: 1.0
 * Author: Sithumini
 */

// Include the necessary files
include(plugin_dir_path(__FILE__) . 'admin-menu.php');
include(plugin_dir_path(__FILE__) . 'api-endpoint.php');
include(plugin_dir_path(__FILE__) . 'shortcode.php');
include(plugin_dir_path(__FILE__) . 'plugin-activation.php');
include(plugin_dir_path(__FILE__) . 'plugin-deactivation.php');
include(plugin_dir_path(__FILE__) . 'wp-table-editor.php');

// Enqueue admin styles
function gold_rate_enqueue_admin_styles() {
    wp_enqueue_style('gold-rate-admin-style', plugins_url('admin-style.css', __FILE__));
}
add_action('admin_enqueue_scripts', 'gold_rate_enqueue_admin_styles');
