<?php
/**
 * Uninstall GitFetch.
 *
 * This file is called when the plugin is deleted via the WordPress admin.
 * It ensures that all plugin-specific options are removed and custom roles
 * cleaned up. WordPress automatically loads this file during the uninstall
 * process when register_uninstall_hook is used in the main plugin file.
 */

// If uninstall.php is not called by WordPress, exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Delete plugin options.
delete_option( 'gitfetch_repositories' );
delete_option( 'gitfetch_token' );
delete_option( 'gitfetch_version' );
delete_option( 'gitfetch_versions' );

// Remove custom roles prefixed with gitfetch_ if any (future use).
global $wp_roles;
if ( ! empty( $wp_roles ) ) {
    foreach ( $wp_roles->roles as $role_slug => $role_info ) {
        if ( 0 === strpos( $role_slug, 'gitfetch_' ) ) {
            remove_role( $role_slug );
        }
    }
}