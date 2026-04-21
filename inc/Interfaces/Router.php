<?php
/**
 * Router Interface
 *
 * Establish base methods for all Concrete Classes
 * used across the plugin
 *
 * @package TrashPostInBlockEditor
 */
namespace TrashPostInBlockEditor\Interfaces;

interface Router {
	/**
	 * Is user permissible?
	 *
	 * Validate that User has Admin capabilities
	 * and Nonce is set correctly.
	 *
	 * @since 1.2.0
	 *
	 * @return boolean
	 */
	public function is_user_permissible( $request );

	/**
	 * Get Rest Reponse
	 *
	 * @since 1.2.0
	 *
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function get_rest_response( $request );

	/**
	 * Setup REST routes.
	 *
	 * @since 1.2.0
	 */
	public function register_route();
}
