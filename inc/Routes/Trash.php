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

class Trash extends Route implements Router {
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
				'callback'            => [ $this, 'get_rest_response' ],
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
	public function get_rest_response( $request ) {
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
