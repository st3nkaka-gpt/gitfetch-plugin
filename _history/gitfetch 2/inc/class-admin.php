<?php
/**
 * Admin interface for GitFetch.
 *
 * Handles plugin settings, repository management and package overview.
 */
class GitFetch_Admin {
    /** @var GitFetch_Admin */
    private static $instance;

    /**
     * Get singleton instance.
     *
     * @return GitFetch_Admin
     */
    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor.
     */
    private function __construct() {
        // Only load in admin area.
        add_action( 'admin_menu', array( $this, 'add_menus' ) );
        add_action( 'admin_init', array( $this, 'handle_post' ) );
    }

    /**
     * Registers admin menu pages.
     */
    public function add_menus() {
        // Main menu under Tools or Settings.
        add_menu_page(
            __( 'GitFetch', 'gitfetch' ),
            __( 'GitFetch', 'gitfetch' ),
            'manage_options',
            'gitfetch',
            array( $this, 'render_main_page' ),
            'dashicons-update',
            80
        );
        // Subpages can be added later for details, etc.
    }

    /**
     * Handle form submissions.
     */
    public function handle_post() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        // Check nonce.
        if ( isset( $_POST['gitfetch_action'], $_POST['_gitfetch_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_gitfetch_nonce'] ) ), 'gitfetch_action' ) ) {
            $action = sanitize_text_field( wp_unslash( $_POST['gitfetch_action'] ) );
            if ( 'save_token' === $action ) {
                $token = sanitize_text_field( wp_unslash( $_POST['gitfetch_token'] ?? '' ) );
                update_option( 'gitfetch_token', $token, false );
                add_settings_error( 'gitfetch_messages', 'token_saved', __( 'GitHub token saved.', 'gitfetch' ), 'updated' );
            } elseif ( 'add_repo' === $action ) {
                $repo_owner = sanitize_text_field( wp_unslash( $_POST['repo_owner'] ?? '' ) );
                $repo_name  = sanitize_text_field( wp_unslash( $_POST['repo_name'] ?? '' ) );
                $repo_type  = sanitize_text_field( wp_unslash( $_POST['repo_type'] ?? '' ) );
                if ( $repo_owner && $repo_name ) {
                    $repos = get_option( 'gitfetch_repositories', array() );
                    $repos[] = array(
                        'owner' => $repo_owner,
                        'repo'  => $repo_name,
                        'type'  => $repo_type,
                    );
                    update_option( 'gitfetch_repositories', $repos, false );
                    add_settings_error( 'gitfetch_messages', 'repo_added', __( 'Repository added.', 'gitfetch' ), 'updated' );
                } else {
                    add_settings_error( 'gitfetch_messages', 'repo_error', __( 'Repository owner and name are required.', 'gitfetch' ), 'error' );
                }
            } elseif ( 'delete_repo' === $action ) {
                $index = absint( $_POST['repo_index'] ?? -1 );
                $repos = get_option( 'gitfetch_repositories', array() );
                if ( isset( $repos[ $index ] ) ) {
                    unset( $repos[ $index ] );
                    $repos = array_values( $repos );
                    update_option( 'gitfetch_repositories', $repos, false );
                    add_settings_error( 'gitfetch_messages', 'repo_deleted', __( 'Repository deleted.', 'gitfetch' ), 'updated' );
                }
            }
        }
    }

    /**
     * Renders the main admin page.
     */
    public function render_main_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        // Get stored values.
        $token = get_option( 'gitfetch_token', '' );
        $repos = get_option( 'gitfetch_repositories', array() );

        // Display notices from add_settings_error().
        settings_errors( 'gitfetch_messages' );
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'GitFetch Settings', 'gitfetch' ); ?></h1>
            <form method="post">
                <?php wp_nonce_field( 'gitfetch_action', '_gitfetch_nonce' ); ?>
                <input type="hidden" name="gitfetch_action" value="save_token" />
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="gitfetch_token"><?php esc_html_e( 'GitHub Personal Access Token', 'gitfetch' ); ?></label>
                        </th>
                        <td>
                            <input type="text" name="gitfetch_token" id="gitfetch_token" value="<?php echo esc_attr( $token ); ?>" class="regular-text" />
                            <p class="description"><?php esc_html_e( 'Token används för att autentisera mot GitHub API.', 'gitfetch' ); ?></p>
                        </td>
                    </tr>
                </table>
                <?php submit_button( __( 'Spara token', 'gitfetch' ) ); ?>
            </form>

            <hr />

            <h2><?php esc_html_e( 'Repositories', 'gitfetch' ); ?></h2>
            <form method="post">
                <?php wp_nonce_field( 'gitfetch_action', '_gitfetch_nonce' ); ?>
                <input type="hidden" name="gitfetch_action" value="add_repo" />
                <table class="form-table">
                    <tr>
                        <th><label for="repo_owner"><?php esc_html_e( 'Owner', 'gitfetch' ); ?></label></th>
                        <td><input type="text" name="repo_owner" id="repo_owner" required class="regular-text" /></td>
                    </tr>
                    <tr>
                        <th><label for="repo_name"><?php esc_html_e( 'Repository', 'gitfetch' ); ?></label></th>
                        <td><input type="text" name="repo_name" id="repo_name" required class="regular-text" /></td>
                    </tr>
                    <tr>
                        <th><label for="repo_type"><?php esc_html_e( 'Type', 'gitfetch' ); ?></label></th>
                        <td>
                            <select name="repo_type" id="repo_type">
                                <option value="plugin"><?php esc_html_e( 'Plugin', 'gitfetch' ); ?></option>
                                <option value="theme"><?php esc_html_e( 'Theme', 'gitfetch' ); ?></option>
                            </select>
                        </td>
                    </tr>
                </table>
                <?php submit_button( __( 'Lägg till repo', 'gitfetch' ) ); ?>
            </form>

            <?php if ( ! empty( $repos ) ) : ?>
                <h3><?php esc_html_e( 'Existing repositories', 'gitfetch' ); ?></h3>
                <table class="widefat fixed">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'Repository', 'gitfetch' ); ?></th>
                            <th><?php esc_html_e( 'Type', 'gitfetch' ); ?></th>
                            <th><?php esc_html_e( 'Actions', 'gitfetch' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $repos as $index => $repo ) : ?>
                            <tr>
                                <td><?php echo esc_html( $repo['owner'] . '/' . $repo['repo'] ); ?></td>
                                <td><?php echo esc_html( ucfirst( $repo['type'] ) ); ?></td>
                                <td>
                                    <form method="post" style="display:inline;">
                                        <?php wp_nonce_field( 'gitfetch_action', '_gitfetch_nonce' ); ?>
                                        <input type="hidden" name="gitfetch_action" value="delete_repo" />
                                        <input type="hidden" name="repo_index" value="<?php echo esc_attr( $index ); ?>" />
                                        <?php submit_button( __( 'Delete', 'gitfetch' ), 'delete', 'submit', false ); ?>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <hr />

            <h2><?php esc_html_e( 'Packages', 'gitfetch' ); ?></h2>
            <?php
            // Build packages overview based on defined repositories.
            $packages_info = array();
            if ( ! empty( $repos ) ) {
                // Include plugin and theme functions.
                if ( ! function_exists( 'get_plugins' ) ) {
                    require_once ABSPATH . 'wp-admin/includes/plugin.php';
                }
                foreach ( $repos as $repo ) {
                    $installed         = false;
                    $installed_version = '';
                    $type              = isset( $repo['type'] ) ? $repo['type'] : 'plugin';
                    $slug              = isset( $repo['repo'] ) ? $repo['repo'] : '';
                    if ( 'plugin' === $type ) {
                        $plugins = get_plugins();
                        foreach ( $plugins as $plugin_file => $plugin_data ) {
                            $plugin_dir = dirname( $plugin_file );
                            if ( $plugin_dir === $slug ) {
                                $installed         = true;
                                $installed_version = isset( $plugin_data['Version'] ) ? $plugin_data['Version'] : '';
                                break;
                            }
                        }
                    } elseif ( 'theme' === $type ) {
                        $themes = wp_get_themes();
                        foreach ( $themes as $stylesheet => $theme ) {
                            if ( $stylesheet === $slug ) {
                                $installed         = true;
                                $installed_version = $theme->get( 'Version' );
                                break;
                            }
                        }
                    }
                    $packages_info[] = array(
                        'name'             => $repo['owner'] . '/' . $slug,
                        'type'             => $type,
                        'installed'        => $installed,
                        'installed_version'=> $installed_version,
                        'latest_version'   => __( 'N/A', 'gitfetch' ),
                    );
                }
            }
            if ( ! empty( $packages_info ) ) :
            ?>
                <table class="widefat fixed">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'Package', 'gitfetch' ); ?></th>
                            <th><?php esc_html_e( 'Installed version', 'gitfetch' ); ?></th>
                            <th><?php esc_html_e( 'Latest version', 'gitfetch' ); ?></th>
                            <th><?php esc_html_e( 'Status', 'gitfetch' ); ?></th>
                            <th><?php esc_html_e( 'Actions', 'gitfetch' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $packages_info as $pkg ) : ?>
                            <tr>
                                <td><?php echo esc_html( $pkg['name'] ); ?></td>
                                <td>
                                    <?php
                                    if ( $pkg['installed'] ) {
                                        echo esc_html( $pkg['installed_version'] );
                                    } else {
                                        esc_html_e( 'Not installed', 'gitfetch' );
                                    }
                                    ?>
                                </td>
                                <td><?php echo esc_html( $pkg['latest_version'] ); ?></td>
                                <td>
                                    <?php
                                    if ( $pkg['installed'] ) {
                                        esc_html_e( 'Installed', 'gitfetch' );
                                    } else {
                                        esc_html_e( 'Not installed', 'gitfetch' );
                                    }
                                    ?>
                                </td>
                                <td><?php esc_html_e( 'Coming soon', 'gitfetch' ); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p><?php esc_html_e( 'No packages defined. Add a repository above to get started.', 'gitfetch' ); ?></p>
            <?php endif; ?>
        </div>
        <?php
    }
}