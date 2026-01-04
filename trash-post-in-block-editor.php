<?php
/**
 * Plugin Name: Trash Post in Block Editor
 * Plugin URI:  https://github.com/badasswp/trash-post-in-block-editor
 * Description: Delete a Post from within the WP Block Editor.
 * Version:     1.1.1
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

define( 'TPBE_AUTOLOAD', __DIR__ . '/vendor/autoload.php' );

// Composer Check.
if ( ! file_exists( TPBE_AUTOLOAD ) ) {
	add_action(
		'admin_notices',
		function () {
			vprintf(
				/* translators: Plugin directory path. */
				esc_html__( 'Fatal Error: Composer not setup in %s', 'trash-post-in-block-editor' ),
				[ __DIR__ ]
			);
		}
	);

	return;
}

// Run Plugin.
require_once TPBE_AUTOLOAD;
( \TrashPostInBlockEditor\Plugin::get_instance() )->run();
