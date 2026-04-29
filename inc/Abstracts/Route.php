<?php
/**
 * Route Abstraction.
 *
 * This abstraction defines the base logic from which all
 * Route classes are derived.
 *
 * @package TrashPostInBlockEditor
 */
namespace TrashPostInBlockEditor\Abstracts;

use WP_Error;
use TrashPostInBlockEditor\Interfaces\Router;

abstract class Route implements Router {
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
	 * Get Rest Reponse.
	 *
	 * @since 1.2.0
	 *
	 * @return \WP_REST_Response|\WP_Error
	 */
	abstract public function get_rest_response( $request );

	/**
	 * Setup REST routes.
	 *
	 * @since 1.2.0
	 */
	abstract public function register_route();
}
