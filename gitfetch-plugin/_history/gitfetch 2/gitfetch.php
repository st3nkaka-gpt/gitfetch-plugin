<?php
/**
 * Plugin Name: GitFetch
 * Plugin URI:  https://github.com/your-org/gitfetch
 * Description: Uppdaterar och installerar plugins och teman från privata GitHub‑repositorier via WordPress admin. Ger administratörer möjlighet att lägga till repo, jämföra versioner och installera uppdateringar med ett klick.
 * Version: 0.2.0
 * Author: Thomas & Co
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: gitfetch
 * Domain Path: /languages
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Main GitFetch class.
 *
 * This class acts as a bootstrapper for the plugin. It loads dependencies,
 * registers hooks and defines constants. Actual functionality resides in
 * separate classes under the `inc/` directory.
 */
class GitFetch {
    /** @var GitFetch Singleton instance */
    private static $instance;

    /**
     * Retrieve the singleton instance of GitFetch.
     *
     * @return GitFetch
     */
    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * GitFetch constructor.
     *
     * Registers activation/deactivation hooks, loads dependencies and hooks
     * into WordPress.
     */
    private function __construct() {
        // Define plugin constants.
        $this->define_constants();

        // Run activation/deactivation hooks.
        register_activation_hook( __FILE__, array( $this, 'activate' ) );
        register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
        register_uninstall_hook( __FILE__, array( 'GitFetch', 'uninstall' ) );

        // Load dependencies.
        $this->includes();

        // Instantiate admin class on init.
        add_action( 'plugins_loaded', array( $this, 'init' ) );
    }

    /**
     * Define plugin constants used throughout the plugin.
     */
    private function define_constants() {
        define( 'GITFETCH_VERSION', '0.2.0' );
        define( 'GITFETCH_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
        define( 'GITFETCH_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
        define( 'GITFETCH_BASENAME', plugin_basename( __FILE__ ) );
    }

    /**
     * Include required class files.
     */
    private function includes() {
        require_once GITFETCH_PLUGIN_DIR . 'inc/class-github-api.php';
        require_once GITFETCH_PLUGIN_DIR . 'inc/class-admin.php';
        require_once GITFETCH_PLUGIN_DIR . 'inc/class-upgrader.php';
    }

    /**
     * Initialize the plugin after WordPress is loaded.
     */
    public function init() {
        if ( is_admin() ) {
            // Initialize admin pages and settings.
            GitFetch_Admin::instance();
        }
    }

    /**
     * Runs on plugin activation.
     *
     * Creates default options and prepares plugin environment.
     */
    public function activate() {
        // Create default options if they don't exist.
        if ( false === get_option( 'gitfetch_repositories' ) ) {
            update_option( 'gitfetch_repositories', array(), false );
        }
        if ( false === get_option( 'gitfetch_token' ) ) {
            update_option( 'gitfetch_token', '', false );
        }
        if ( false === get_option( 'gitfetch_version' ) ) {
            update_option( 'gitfetch_version', GITFETCH_VERSION, false );
        }
        // TODO: Additional setup tasks can go here.
    }

    /**
     * Runs on plugin deactivation.
     */
    public function deactivate() {
        // No action needed on deactivation for now.
    }

    /**
     * Runs on plugin uninstallation.
     *
     * This method is static because register_uninstall_hook requires a
     * callback that is serializable. Removes options and cleans up roles.
     */
    public static function uninstall() {
        // Remove plugin options.
        delete_option( 'gitfetch_repositories' );
        delete_option( 'gitfetch_token' );
        delete_option( 'gitfetch_version' );
        delete_option( 'gitfetch_versions' );
        // You could also remove custom roles here if you create any in the future.
    }
}

// Initialize the plugin.
GitFetch::instance();