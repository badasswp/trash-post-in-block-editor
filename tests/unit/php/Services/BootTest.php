<?php

namespace TrashPostInBlockEditor\Tests\Services;

use WP_Mock;
use Mockery;
use ReflectionClass;
use WP_Mock\Tools\TestCase;
use TrashPostInBlockEditor\Services\Boot;

/**
 * @covers TrashPostInBlockEditor\Services\Boot::register
 * @covers TrashPostInBlockEditor\Services\Boot::register_assets
 * @covers TrashPostInBlockEditor\Services\Boot::register_text_domain
 * @covers TrashPostInBlockEditor\Services\Boot::get_assets
 */
class BootTest extends TestCase {
	public Boot $boot;

	public function setUp(): void {
		WP_Mock::setUp();

		$this->boot = new Boot();
	}

	public function tearDown(): void {
		WP_Mock::tearDown();
	}

	public function test_register() {
		WP_Mock::expectActionAdded( 'enqueue_block_editor_assets', [ $this->boot, 'register_assets' ] );
		WP_Mock::expectActionAdded( 'init', [ $this->boot, 'register_text_domain' ] );

		$this->boot->register();

		$this->assertConditionsMet();
	}

	public function test_register_assets() {
		$boot = new ReflectionClass( Boot::class );

		$mock_boot = Mockery::mock( Boot::class )->makePartial();
		$mock_boot->shouldAllowMockingProtectedMethods();

		$mock_boot->shouldReceive( 'get_assets' )
			->andReturn(
				[
					'dependencies' => [],
					'version'      => 'ec9080196954ae49fb68',
				]
			);

		global $wpVersion;
		$wpVersion = '';

		WP_Mock::userFunction( 'plugins_url' )
			->with( 'trash-post-in-block-editor/dist/app.js' )
			->andReturn( 'https://example.com/wp-content/plugins/trash-post-in-block-editor/dist/app.js' );

		WP_Mock::userFunction( 'plugin_dir_path' )
			->with( $boot->getFileName() )
			->andReturn( '/var/www/html/wp-content/plugins/trash-post-in-block-editor/inc/Services/Boot.php/' );

		WP_Mock::userFunction( 'wp_enqueue_script' )
			->with(
				'trash-post-in-block-editor',
				'https://example.com/wp-content/plugins/trash-post-in-block-editor/dist/app.js',
				[],
				'ec9080196954ae49fb68',
				false,
			);

		WP_Mock::userFunction( 'wp_set_script_translations' )
			->with(
				'trash-post-in-block-editor',
				'trash-post-in-block-editor',
				'/var/www/html/wp-content/plugins/trash-post-in-block-editor/inc/Services/Boot.php/../../languages',
			);
		WP_Mock::userFunction( 'get_post_type' )
			->with()
			->andReturn( 'post' );

		WP_Mock::userFunction( 'untrailingslashit' )
			->with( 'https://example.com/wp-admin/' )
			->andReturn( 'https://example.com/wp-admin' );

		WP_Mock::userFunction( 'get_admin_url' )
			->with()
			->andReturn( 'https://example.com/wp-admin/' );

		WP_Mock::userFunction( 'add_query_arg' )
			->with(
				[
					'post_type' => 'post',
				],
				'https://example.com/wp-admin/edit.php'
			)
			->andReturn( 'https://example.com/wp-admin/edit.php?post_type=post' );

		WP_Mock::expectFilter(
			'tpbe_redirect_url',
			'https://example.com/wp-admin/edit.php?post_type=post',
		);

		WP_Mock::userFunction( 'esc_url' )
			->with( 'https://example.com/wp-admin/edit.php?post_type=post' )
			->andReturn( 'https://example.com/wp-admin/edit.php?post_type=post' );

		WP_Mock::userFunction( 'wp_localize_script' )
			->once()
			->with(
				'trash-post-in-block-editor',
				'tpbe',
				[
					'wpVersion' => $wpVersion,
					'url'       => 'https://example.com/wp-admin/edit.php?post_type=post',
				],
			)
			->andReturn( null );

		$mock_boot->register_assets();

		$this->assertConditionsMet();
	}

	public function test_register_text_domain() {
		$boot = new ReflectionClass( Boot::class );

		WP_Mock::userFunction( 'plugin_basename' )
			->once()
			->with( $boot->getFileName() )
			->andReturn( 'inc/Services/Boot.php' );

		WP_Mock::userFunction( 'load_plugin_textdomain' )
			->once()
			->with(
				'trash-post-in-block-editor',
				false,
				'inc/Services../../languages'
			);

		$this->boot->register_text_domain();

		$this->assertConditionsMet();
	}
}
