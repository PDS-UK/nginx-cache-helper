<?php
/**
 * Plugin Name: NGINX Cache Helper
 * Plugin URI:  https://printdatasolutions.co.uk
 * Description: Automatically clears NGINX FastCGI/Proxy Cache when content is updated.
 * Version:     1.0.0
 * Author:      Cameron Stephen
 * Author URI:  https://printdatasolutions.co.uk
 * License:     GPL-2.0+
 */

if (!defined('ABSPATH')) exit; // Exit if accessed directly

define('NGINX_CACHE_PATH', getenv('NGINX_CACHE_PATH') ?: '/var/run/nginx-cache/');

/**
 * Recursively delete files in a directory.
 *
 * @param string $dir Directory path.
 * @param bool   $delete_root_too Delete root directory or not.
 *
 * @return void
 */
function unlink_recursive($dir, $delete_root_too = false) {
    if (!is_dir($dir) || !is_writable($dir)) {
        error_log("NGINX Cache Helper: Cache directory does not exist or is not writable: $dir");
        return;
    }

    $files = array_diff(scandir($dir), ['.', '..']);
    foreach ($files as $file) {
        $path = "$dir/$file";
        is_dir($path) ? unlink_recursive($path, true) : @unlink($path);
    }

    if ($delete_root_too) {
        @rmdir($dir);
    }
}

/**
 * Purge NGINX cache.
 *
 * @return void
 */
function purge_nginx_cache() {
    if (php_sapi_name() === 'cli') {
        return; // Prevent execution in CLI mode
    }

    unlink_recursive(NGINX_CACHE_PATH);
    error_log("NGINX Cache purged successfully.");
    add_action('admin_notices', 'nginx_cache_purge_admin_notice');
}

// Hook into relevant WordPress events to purge cache
$purge_hooks = [
    'transition_post_status', 'wp_insert_comment', 'wp_set_comment_status', 'delete_comment',
    'activated_plugin', 'deactivated_plugin', 'after_switch_theme', 'upgrader_process_complete',
    'save_post_product', 'woocommerce_order_status_changed', 'delete_post', 'save_post',
    'publish_post', 'publish_page'
];
foreach ($purge_hooks as $hook) {
    add_action($hook, 'purge_nginx_cache');
}

// Add admin menu item to purge cache manually
function nginx_cache_purger_menu() {
    add_menu_page(
        'Purge NGINX Cache', 'Purge Cache', 'manage_options',
        'purge-nginx-cache', 'manual_purge_nginx_cache', 'dashicons-update', 99
    );
}
add_action('admin_menu', 'nginx_cache_purger_menu');

// Function to handle manual purge
function manual_purge_nginx_cache() {
    purge_nginx_cache();
    echo '<div class="updated"><p>NGINX Cache Purged Successfully.</p></div>';
}

// Display admin notice after purging cache
function nginx_cache_purge_admin_notice() {
    echo '<div class="updated notice is-dismissible"><p>NGINX Cache Purged Successfully.</p></div>';
}
