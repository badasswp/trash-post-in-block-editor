<?php
/**
 * Plugin Name: Trash Post in Block Editor
 * Plugin URI:  https://github.com/badasswp/trash-post-in-block-editor
 * Description: Delete a Post from within the WP Block Editor.
 * Version:     1.0.0
 * Author:      badasswp
 * Author URI:  https://github.com/badasswp
 * License:     GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: trash-post-in-block-editor
 * Domain Path: /languages
 *
 * @package TrashPostInBlockEditor
 */

namespace badasswp\TrashPostInBlockEditor;

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Load Scripts.
 *
 * @since 1.0.0
 *
 * @wp-hook 'enqueue_block_editor_assets'
 */
add_action( 'enqueue_block_editor_assets', function() {
	wp_enqueue_script(
		'trash-post-in-block-editor',
		trailingslashit( plugin_dir_url( __FILE__ ) ) . 'dist/app.js',
		[
			'wp-i18n',
			'wp-element',
			'wp-blocks',
			'wp-components',
			'wp-editor',
			'wp-hooks',
			'wp-compose',
			'wp-plugins',
			'wp-edit-post',
			'wp-edit-site',
		],
		'1.0.0',
		false,
	);

	wp_set_script_translations(
		'trash-post-in-block-editor',
		'trash-post-in-block-editor',
		plugin_dir_path( __FILE__ ) . 'languages'
	);
} );

/**
 * Add Translation.
 *
 * @since 1.0.0
 *
 * @wp-hook 'init'
 */
add_action( 'init', function() {
	load_plugin_textdomain(
		'trash-post-in-block-editor',
		false,
		dirname( plugin_basename( __FILE__ ) ) . '/languages'
	);
} );

/**
 * Setup REST routes.
 *
 * @since 1.0.0
 *
 * @wp-hook 'rest_api_init'
 */
add_action( 'rest_api_init', function() {
	register_rest_route(
		'tpbe/v1',
		'/trash',
		[
			'methods'             => \WP_REST_Server::CREATABLE,
			'callback'            => __NAMESPACE__ . '\trash_post',
			'permission_callback' => __NAMESPACE__ . '\is_user_permissible',
		],
	);
} );

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
function trash_post( $request ): \WP_REST_Response {
	$args = $request->get_json_params();

	// Get Post ID.
	$post_id = (int) ( $args['id'] ?? '' );

	//Bail out, if it does NOT exists.
	if ( ! get_post( $post_id ) ) {
		return new \WP_Error(
			'tpbe-bad-request',
			sprintf(
				'Fatal Error: Bad Request, Post does not exists for ID: %s',
				$post_id
			),
			[
				'status'  => 400,
				'request' => $args,
			]
		);
	}

	if ( ! wp_delete_post( $post_id ) ) {
		return new \WP_Error(
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
			'ID' => $post_id
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
function is_user_permissible( $request ) {
	$http_error = rest_authorization_required_code();

	if ( ! current_user_can( 'administrator' ) ) {
		return new \WP_Error(
			'tpbe-rest-forbidden',
			sprintf( 'Invalid User. Error: %s', $http_error ),
			[ 'status' => $http_error ]
		);
	}

	if ( ! wp_verify_nonce( $request->get_header( 'X-WP-Nonce' ), 'wp_rest' ) ) {
		return new \WP_Error(
			'tpbe-rest-forbidden',
			sprintf( 'Invalid Nonce. Error: %s', $http_error ),
			[ 'status' => $http_error ]
		);
	}

	return true;
}
