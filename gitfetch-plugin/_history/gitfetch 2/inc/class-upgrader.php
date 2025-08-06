<?php
/**
 * GitFetch Upgrader.
 *
 * Provides helper functions to install or upgrade plugins and themes from
 * GitHub archives. Uses WordPress Plugin_Upgrader and Theme_Upgrader
 * classes. This is a skeleton implementation; real download and version
 * handling will be implemented in future versions.
 */
class GitFetch_Upgrader {

    /**
     * Install a package from a given URL.
     *
     * @param string $package_url URL to the zip file.
     * @param string $type        Package type: 'plugin' or 'theme'.
     * @param bool   $overwrite   Whether to overwrite existing installation.
     * @return bool|WP_Error
     */
    public static function install_package( $package_url, $type = 'plugin', $overwrite = true ) {
        if ( 'plugin' === $type ) {
            require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
            $upgrader = new Plugin_Upgrader( new Automatic_Upgrader_Skin() );
        } elseif ( 'theme' === $type ) {
            require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
            $upgrader = new Theme_Upgrader( new Automatic_Upgrader_Skin() );
        } else {
            return new WP_Error( 'invalid_type', __( 'Invalid package type.', 'gitfetch' ) );
        }

        // Set options: clear_destination to remove existing files if overwrite is true.
        $options = array(
            'clear_destination' => $overwrite,
            'overwrite_package' => $overwrite,
        );

        // Download and install the package.
        $result = $upgrader->install( $package_url, $options );
        return $result;
    }
}