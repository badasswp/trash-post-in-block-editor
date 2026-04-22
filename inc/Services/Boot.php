<?php
/**
 * Boot Service.
 *
 * Handle all setup login before plugin is
 * fully capable
 *
 * @package TrashPostInBlockEditor
 */

namespace TrashPostInBlockEditor\Services;

use TrashPostInBlockEditor\Abstracts\Service;
use TrashPostInBlockEditor\Interfaces\Kernel;

class Boot extends Service implements Kernel {
	/**
	 * Bind to WP.
	 *
	 * @since 1.2.0
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'enqueue_block_editor_assets', [ $this, 'register_assets' ] );
		add_action( 'init', [ $this, 'register_text_domain' ] );
	}

	/**
	 * Load Scripts.
	 *
	 * @since 1.0.0
	 *
	 * @wp-hook 'enqueue_block_editor_assets'
	 */
	public function register_assets(): void {
		$assets = $this->get_assets( plugin_dir_path( __FILE__ ) . '../../dist/app.asset.php' );
		global $wp_version;

		wp_enqueue_script(
			'trash-post-in-block-editor',
			plugins_url( 'trash-post-in-block-editor/dist/app.js' ),
			$assets['dependencies'],
			$assets['version'],
			false
		);

		wp_set_script_translations(
			'trash-post-in-block-editor',
			'trash-post-in-block-editor',
			plugin_dir_path( __FILE__ ) . '../../languages'
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
			dirname( plugin_basename( __FILE__ ) ) . '../../languages'
		);
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
