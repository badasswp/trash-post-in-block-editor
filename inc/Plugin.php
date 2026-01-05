<?php
/**
 * Plugin Class.
 *
 * Register Plugin actions and filters within this
 * class for plugin use.
 *
 * @package RedirectDuplicatePosts
 */

namespace TrashPostInBlockEditor;

use WP_Error;
use WP_REST_Server;
use WP_REST_Response;

class Plugin {
	/**
	 * Plugin Instance.
	 *
	 * @since 1.0.0
	 *
	 * @var Plugin
	 */
	protected static $instance;

	/**
	 * Set up Instance.
	 *
	 * @since 1.0.0
	 *
	 * @return Plugin
	 */
	public static function get_instance(): Plugin {
		if ( is_null( static::$instance ) ) {
			static::$instance = new self();
		}

		return static::$instance;
	}

	/**
	 * Run Plugin.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function run(): void {
		add_action( 'enqueue_block_editor_assets', [ $this, 'register_assets' ] );
		add_action( 'init', [ $this, 'register_text_domain' ] );
		add_action( 'rest_api_init', [ $this, 'register_route' ] );
	}

	/**
	 * Load Scripts.
	 *
	 * @since 1.0.0
	 *
	 * @wp-hook 'enqueue_block_editor_assets'
	 */
	public function register_assets(): void {
		$assets = $this->get_assets( plugin_dir_path( __FILE__ ) . '../dist/app.asset.php' );
		global $wp_version;

		wp_enqueue_script(
			'trash-post-in-block-editor',
			plugin_dir_url( __FILE__ ) . '../dist/app.js',
			$assets['dependencies'],
			$assets['version'],
			false
		);

		wp_set_script_translations(
			'trash-post-in-block-editor',
			'trash-post-in-block-editor',
			plugin_dir_path( __FILE__ ) . '../languages'
		);

		/**
		 * Filter Redirect URL.
		 *
		 * @since 1.1.0
		 *
		 * @param string $redirect_url Redirect URL.
		 * @return string
		 */
		$redirect_url = apply_filters(
			'tpbe_redirect_url',
			add_query_arg(
				[
					'post_type' => get_post_type(),
				],
				sprintf( '%s/%s', untrailingslashit( get_admin_url() ), 'edit.php' )
			),
		);

		wp_localize_script(
			'trash-post-in-block-editor',
			'tpbe',
			[
				'wpVersion' => $wp_version,
				'url'       => esc_url( $redirect_url ),
			]
		);
	}

	/**
	 * Add Translation.
	 *
	 * @since 1.0.0
	 *
	 * @wp-hook 'init'
	 */
	public function register_text_domain(): void {
		load_plugin_textdomain(
			'trash-post-in-block-editor',
			false,
			dirname( plugin_basename( __FILE__ ) ) . '../languages'
		);
	}

	/**
	 * Setup REST routes.
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Use validate & sanitize callbacks.
	 *
	 * @wp-hook 'rest_api_init'
	 */
	public function register_route(): void {
		register_rest_route(
			'tpbe/v1',
			'/trash',
			[
				'args'                => [
					'id' => [
						'validate_callback' => function ( $param ) {
							return is_numeric( $param );
						},
						'sanitize_callback' => 'absint',
					],
				],
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'trash_post' ],
				'permission_callback' => [ $this, 'is_user_permissible' ],
			]
		);
	}

	/**
	 * Get REST Response.
	 *
	 * This method deletes the Post when the user
	 * clicks on the trash icon.
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_REST_Request $request Request Object.
	 * @return \WP_REST_Response|\WP_Error
	 *
	 * @wp-hook 'rest_api_init'
	 */
	public function trash_post( $request ): WP_REST_Response {
		$args = $request->get_json_params();

		// Get Post ID.
		$post_id = $args['id'];

		if ( ! wp_delete_post( $post_id ) ) {
			return new WP_Error(
				'tpbe-bad-request',
				sprintf(
					'Fatal Error: Bad Request, Unable to delete Post with ID: %s',
					$post_id
				),
				[
					'status'  => 400,
					'request' => $args,
				]
			);
		}

		return rest_ensure_response(
			[
				'ID' => $post_id,
			]
		);
	}

	/**
	 * Is User Permissible?
	 *
	 * Validate that User has Admin capabilities
	 * and Nonce is set correctly.
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_REST_Request $request Request Object.
	 * @return bool|\WP_Error
	 *
	 * @wp-hook 'rest_api_init'
	 */
	public function is_user_permissible( $request ) {
		$http_error = rest_authorization_required_code();

		if ( ! current_user_can( 'edit_posts' ) ) {
			return new WP_Error(
				'tpbe-rest-forbidden',
				sprintf( 'Invalid User. Error: %s', $http_error ),
				[ 'status' => $http_error ]
			);
		}

		if ( ! wp_verify_nonce( $request->get_header( 'X-WP-Nonce' ), 'wp_rest' ) ) {
			return new WP_Error(
				'tpbe-rest-forbidden',
				sprintf( 'Invalid Nonce. Error: %s', $http_error ),
				[ 'status' => $http_error ]
			);
		}

		return true;
	}

	/**
	 * Get Asset dependencies.
	 *
	 * @since 1.0.4
	 *
	 * @param string $path Path to webpack generated PHP asset file.
	 * @return array
	 */
	protected function get_assets( string $path ): array {
		$assets = [
			'version'      => strval( time() ),
			'dependencies' => [],
		];

		if ( ! file_exists( $path ) ) {
			return $assets;
		}

		$assets = require_once $path; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingVariable

		return $assets;
	}
}
