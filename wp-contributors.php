<?php
/**
 * Plugin Name: WP Contributors Plugin
 * Description: Creates a Meta-Box for Contibutors(Authors) on Post Page
 * Author: Kavita Joshi
 * Author URI: https://github.com/kavitajoshi99
 * Version: 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// define plugin version. All update to the plugin will be made using this version.
define ('CURRENT_WPCONTIBUTORS_PLUGIN_VERSION', '1.0.0');

require plugin_dir_path( __FILE__ ) . 'includes/class-wp-contributors.php';

// plugin activation
register_activation_hook( __FILE__, 'wpcontributors_activate' );
function wpcontributors_activate() {
    if(!get_option('WPCONTRIBUTORS_PLUGIN_VERSION')) {
        add_option('WPCONTRIBUTORS_PLUGIN_VERSION', CURRENT_WPCONTIBUTORS_PLUGIN_VERSION);
    } else {
        update_option('WPCONTRIBUTORS_PLUGIN_VERSION', CURRENT_WPCONTIBUTORS_PLUGIN_VERSION);
    }
    $wp_contributors = new Wp_Contributors();
}


// plugin deactivation
register_deactivation_hook( __FILE__, 'wpcontributors_deactivate' );
function wpcontributors_deactivate() {
    delete_option('WPCONTRIBUTORS_PLUGIN_VERSION');
}
