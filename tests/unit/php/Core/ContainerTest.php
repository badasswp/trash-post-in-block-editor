<?php

namespace TrashPostInBlockEditor\Tests\Core;

use WP_Mock;
use WP_Mock\Tools\TestCase;

use TrashPostInBlockEditor\Core\Container;
use TrashPostInBlockEditor\Services\Boot;
use TrashPostInBlockEditor\Services\Routes;
use TrashPostInBlockEditor\Abstracts\Service;

/**
 * @covers \TrashPostInBlockEditor\Core\Container::__construct
 * @covers \TrashPostInBlockEditor\Core\Container::register
 * @covers \TrashPostInBlockEditor\Abstracts\Service::get_instance
 * @covers \TrashPostInBlockEditor\Services\Boot::register
 * @covers \TrashPostInBlockEditor\Services\Routes::__construct
 * @covers \TrashPostInBlockEditor\Services\Routes::register
 */
class ContainerTest extends TestCase {
	public Container $container;

	public function setUp(): void {
		WP_Mock::setUp();
	}

	public function tearDown(): void {
		WP_Mock::tearDown();
	}

	public function test_container_contains_required_services() {
		$this->container = new Container();

		$this->assertTrue( in_array( Boot::class, Container::$bindings, true ) );
		$this->assertTrue( in_array( Routes::class, Container::$bindings, true ) );
	}

	public function test_register() {
		$container = new Container();

		/**
		 * Hack around unset Service::$instances.
		 *
		 * We create instances of services so we can
		 * have a populated version of the Service abstraction's instances.
		 */
		foreach ( Container::$bindings as $binding ) {
			$binding::get_instance();
		}

		WP_Mock::expectActionAdded(
			'enqueue_block_editor_assets',
			[
				Service::$services[ Boot::class ],
				'register_assets',
			]
		);

		WP_Mock::expectActionAdded(
			'init',
			[
				Service::$services[ Boot::class ],
				'register_text_domain',
			]
		);

		WP_Mock::expectActionAdded(
			'rest_api_init',
			[
				Service::$services[ Routes::class ],
				'register_rest_routes',
			]
		);

		$container->register();

		$this->assertConditionsMet();
	}
}
