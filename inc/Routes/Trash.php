<?php
/**
 * Sets up all Routes
 *
 * @package TrashPostInBlockEditor
 */
namespace TrashPostInBlockEditor\Routes;

use WP_Error;
use WP_REST_Server;
use WP_REST_Response;

use TrashPostInBlockEditor\Abstracts\Route;

class Trash extends Route {
	/**
	 * Bind to WP
	 *
	 * @since 1.2.0
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'rest_api_init', [ $this, 'register_route' ] );
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
}
