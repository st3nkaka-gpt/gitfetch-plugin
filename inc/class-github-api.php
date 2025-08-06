<?php
/**
 * GitFetch GitHub API wrapper.
 *
 * Provides basic methods to interact with the GitHub API using WordPress HTTP
 * API. All requests are authenticated using a Personal Access Token stored
 * in the gitfetch_token option. This class is meant as a lightweight
 * abstraction; it does not implement every endpoint, but it provides
 * convenience methods used by other classes.
 */
class GitFetch_GitHub_API {

    /**
     * Base URL for GitHub API.
     *
     * @var string
     */
    private $api_base = 'https://api.github.com';

    /**
     * GitHub personal access token.
     *
     * @var string
     */
    private $token;

    /**
     * Constructor.
     *
     * Retrieves token from database. You can also pass a token directly
     * for testing purposes.
     *
     * @param string|null $token Optional token. If not provided, reads from
     *                           gitfetch_token option.
     */
    public function __construct( $token = null ) {
        if ( $token ) {
            $this->token = $token;
        } else {
            $this->token = get_option( 'gitfetch_token', '' );
        }
    }

    /**
     * Performs a GET request to GitHub.
     *
     * @param string $endpoint API endpoint starting with a slash.
     * @param array  $args     Additional query arguments.
     *
     * @return array|WP_Error Response array on success or WP_Error on failure.
     */
    public function get( $endpoint, $args = array() ) {
        $url = trailingslashit( $this->api_base ) . ltrim( $endpoint, '/' );
        if ( ! empty( $args ) ) {
            $url = add_query_arg( $args, $url );
        }

        $headers = array(
            'Accept'        => 'application/vnd.github+json',
            'Authorization' => 'token ' . $this->token,
            'User-Agent'    => 'GitFetch-Plugin',
        );

        $response = wp_remote_get( $url, array( 'headers' => $headers ) );
        return $this->parse_response( $response );
    }

    /**
     * Performs a GET request to retrieve the repository information.
     *
     * @param string $owner Repo owner.
     * @param string $repo  Repo name.
     *
     * @return array|WP_Error Repo info array or WP_Error on failure.
     */
    public function get_repo( $owner, $repo ) {
        return $this->get( "/repos/$owner/$repo" );
    }

    /**
     * Retrieves the releases (tags) for a repository.
     *
     * @param string $owner Repo owner.
     * @param string $repo  Repo name.
     *
     * @return array|WP_Error List of release objects or WP_Error on failure.
     */
    public function get_releases( $owner, $repo ) {
        return $this->get( "/repos/$owner/$repo/releases" );
    }

    /**
     * Parses a WP HTTP response.
     *
     * @param WP_Error|array $response Response from wp_remote_get().
     *
     * @return array|WP_Error
     */
    private function parse_response( $response ) {
        if ( is_wp_error( $response ) ) {
            return $response;
        }
        $code = wp_remote_retrieve_response_code( $response );
        if ( 200 !== $code ) {
            return new WP_Error( 'github_api_error', sprintf( 'GitHub API returned status %d', $code ) );
        }
        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );
        if ( null === $data ) {
            return new WP_Error( 'github_json_error', 'Invalid JSON returned from GitHub API' );
        }
        return $data;
    }
}